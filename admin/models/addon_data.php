<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class TZ_Portfolio_PlusModelAddon_Data extends JModelAdmin{

    protected $addon_element   = null;

    public function __construct($config = array())
    {
        // Guess the option from the class name (Option)Model(View).
        if (empty($this->option))
        {
            $r = null;

            if (!preg_match('/(.*)Model/i', get_class($this), $r))
            {
                throw new Exception(JText::_('JLIB_APPLICATION_ERROR_MODEL_GET_NAME'), 500);
            }

            $this->option = 'com_tz_portfolio_plus';
        }

        parent::__construct($config);
    }

    protected function populateState(){

        $addon_id   = JFactory::getApplication()->input->getInt('addon_id');
        $this -> setState($this -> getName().'.addon_id',$addon_id);

        // List state information.
        parent::populateState();
    }

    function getForm($data = array(), $loadData = true){

        // Load addon's form
        if($addonId = JFactory::getApplication()->input->getInt('addon_id')){
            // Get a row instance.
            $table = $this->getTable('Extensions','TZ_Portfolio_PlusTable');

            // Attempt to load the row.
            $return = $table->load($addonId);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return $return;
            }

            $path   = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.$table -> folder
                .DIRECTORY_SEPARATOR.$table -> element;

            // Add plugin form's path
            JForm::addFormPath($path.DIRECTORY_SEPARATOR.'admin/models/form');
            JForm::addFormPath($path.DIRECTORY_SEPARATOR.'admin/models/forms');
        }

        $form = $this->loadForm('com_tz_portfolio_plus.'.$this -> getName()
            , $this -> getName(), array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    public function getTable($type = 'Addon_Data', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app    = JFactory::getApplication();
        $data   = $app->getUserState('com_tz_portfolio_plus.edit.'.$this -> getName().'.data', array());

        if (empty($data)) {
            $data           = $this->getItem();
            if($data && isset($data -> value) && is_string($data -> value)){
                $data -> value  = json_decode($data -> value);
            }
        }

        return $data;
    }

    protected function prepareTable($table){
        if(!isset($table -> extension_id)
            || (isset($table -> extension_id) && !$table -> extension_id)){
            $input  = JFactory::getApplication() -> input;
            $table -> extension_id   = $input -> getInt('addon_id');

            if(!isset($table -> element)){
                $table -> element   = $this -> addon_element;
            }
            if(!isset($table -> published)){
                $table -> published    = -1;
            }
        }
    }

    public function save($data)
    {
        $dispatcher = JEventDispatcher::getInstance();
        $table      = $this->getTable();
        $context    = $this->option . '.' . $this->name;

        if ((!empty($data['tags']) && $data['tags'][0] != ''))
        {
            $table->newTags = $data['tags'];
        }

        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
        $isNew = true;

        // Include the plugins for the save events.
        JPluginHelper::importPlugin($this->events_map['save']);

        // Allow an exception to be thrown.
        try
        {
            // Load the row if saving an existing record.
            if ($pk > 0)
            {
                $table->load($pk);
                $isNew = false;
            }

            // Bind the data.
            if (!$table->bind($data))
            {
                $this->setError($table->getError());

                return false;
            }

            // Prepare the row for saving
            $this->prepareTable($table);

            // Check the data.
            if (!$table->check())
            {
                $this->setError($table->getError());

                return false;
            }

            // Trigger the before save event.
            $result = $dispatcher->trigger($this->event_before_save, array($context, $table, $isNew));

            if (in_array(false, $result, true))
            {
                $this->setError($table->getError());

                return false;
            }

            // Store the data.
            if (!$table->store())
            {
                $this->setError($table->getError());

                return false;
            }

            // Clean the cache.
            $this->cleanCache();

            // Trigger the after save event.
            $dispatcher->trigger($this->event_after_save, array($context, $table, $isNew));
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        if (isset($table->$key))
        {
            $this->setState($this->getName() . '.id', $table->$key);
        }

        $this->setState($this->getName() . '.new', $isNew);

        if ($this->associationsContext && JLanguageAssociations::isEnabled())
        {
            $associations = $data['associations'];

            // Unset any invalid associations
            $associations = Joomla\Utilities\ArrayHelper::toInteger($associations);

            // Unset any invalid associations
            foreach ($associations as $tag => $id)
            {
                if (!$id)
                {
                    unset($associations[$tag]);
                }
            }

            // Show a notice if the item isn't assigned to a language but we have associations.
            if ($associations && ($table->language == '*'))
            {
                JFactory::getApplication()->enqueueMessage(
                    JText::_(strtoupper($this->option) . '_ERROR_ALL_LANGUAGE_ASSOCIATED'),
                    'notice'
                );
            }

            // Adding self to the association
            $associations[$table->language] = (int) $table->$key;

            // Deleting old association for these items
            $db    = $this->getDbo();
            $query = $db->getQuery(true)
                ->delete($db->qn('#__associations'))
                ->where($db->qn('context') . ' = ' . $db->quote($this->associationsContext))
                ->where($db->qn('id') . ' IN (' . implode(',', $associations) . ')');
            $db->setQuery($query);
            $db->execute();

            if ((count($associations) > 1) && ($table->language != '*'))
            {
                // Adding new association for these items
                $key   = md5(json_encode($associations));
                $query = $db->getQuery(true)
                    ->insert('#__associations');

                foreach ($associations as $id)
                {
                    $query->values(((int) $id) . ',' . $db->quote($this->associationsContext) . ',' . $db->quote($key));
                }

                $db->setQuery($query);
                $db->execute();
            }
        }

        return true;
    }
}