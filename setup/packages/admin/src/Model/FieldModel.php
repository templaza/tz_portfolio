<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Model;

// no direct access
defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Form\Form;
use Joomla\CMS\UCM\UCMType;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\Filesystem\File;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ExtraFieldsHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;

class FieldModel extends AdminModel
{
    public function getForm($data = array(), $loadData = true){
        if (empty($data))
        {
            $item   = $this -> getItem();
            $type   = $item -> type;
        }
        else
        {
            $type  = ArrayHelper::getValue($data, 'type');
        }

        // This is needed that the plugins can determine the type
        $this->setState('field.type', $type);

        $form = $this->loadForm('com_tz_portfolio.field', 'field', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_tz_portfolio.edit.field.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            if(!$data -> id){
                // Set the type if available from the request
                $data->type = $app->input->getWord('type', $this->state->get('field.type', $data->type));
            }
        }

        $this->preprocessData('com_tz_portfolio.field', $data);

        return $data;
    }

    public function getItem($pk = null){
        if($item = parent::getItem($pk)){
            $item -> field      = null;

            $item -> groupid    = ExtraFieldsHelper::getFieldGroups((int) $item -> id);
            return $item;
        }
        return false;
    }

    public function save($data){
        $groupid    = $data['groupid'];
        unset($data['groupid']);

        $table = $this->getTable();
        $_data = $data;

        if($data && isset($data['value']) && !$data['value']){
            $data['value']  = '';
        }


        if(parent::save($data)){

            $pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');

            // Insert field's groups
            ExtraFieldsHelper::insertFieldGroups($pk, $groupid);

            if ($pk && $table->load($pk))
            {
                $_data['id']    = $pk;
                if($fieldClass = ExtraFieldsFrontHelper::getExtraField($pk)) {
                    $_data = $fieldClass->onSave($_data);
                    if($_data && isset($_data['value']) && !is_string($_data['value'])){
                        $_data['value'] = json_encode($_data['value']);
                    }
                    $db     = $this -> _db;
                    $query  = $db -> getQuery(true);
                    $query -> update($table -> getTableName());
                    $query -> set('value='.$db -> quote($_data['value']));
                    $query -> where('id='.$pk);
                    $db -> setQuery($query);
                    $db -> execute();
                }
            }

            return true;
        }
        return false;
    }

    public function prepareTable($table){
        if(isset($table -> params) && is_array($table -> params)){
            $registry   = new Registry;
            $registry -> loadArray($table -> params);
            $table -> params    = $registry -> toString();
        }
        if(isset($table -> value) && is_array($table -> value)){
            $table -> value    = json_encode($table -> value);
        }
    }

    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        $type       = $this->getState('field.type');

        if($data){
            if(is_array($data)){
                if(isset($data['type']) && $data['type']){
                    $type   = $data['type'];
                }
                if(isset($data['id']) && $data['id']){
                    $form -> setFieldAttribute('type', 'readonly',  true);
                }
            }elseif(is_object($data)){
                if(isset($data -> type) && $data -> type){
                    $type   = $data -> type;
                }
                if(isset($data -> id) && $data -> id){
                    $form -> setFieldAttribute('type', 'readonly',  true);
                }
            }
        }

        if($type ){

            $core_path  = COM_TZ_PORTFOLIO_ADDON_PATH.'/extrafields/'.$type.'/admin';

            $core_f_xml_path    = Path::clean($core_path.'/forms/field.xml');
            if(!file_exists($core_f_xml_path)) {
                $core_f_xml_path = Path::clean($core_path . '/models/forms/field.xml');
            }
            if(file_exists($core_f_xml_path)){
                $form -> loadFile($core_f_xml_path, false, '/form/fields[@name="params"]');
            }

            // Insert parameter from extrafield
            ExtraFieldsHelper::prepareForm($form, $data);

            parent::preprocessForm($form, $data, $group);
        }
    }

    public function updateState(&$pks,$value=1, $task = null){
        if($table  = $this -> getTable()){
            $user   = Factory::getApplication()->getIdentity();
            switch($task){
                default:
                    break;
                case 'listview':
                case 'unlistview':
                    $table -> setColumnAlias('updatestate', 'list_view');
                    break;
                case 'detailview':
                case 'undetailview':
                    $table -> setColumnAlias('updatestate', 'detail_view');
                    break;
                case 'advsearch':
                case 'unadvsearch':
                    $table -> setColumnAlias('updatestate', 'advanced_search');
                    break;
            }

            // Access checks.
            foreach ($pks as $i => $pk)
            {
                $table->reset();

                if ($table->load($pk))
                {
                    if (!$this->canEditState($table))
                    {
                        // Prune items that you can't change.
                        unset($pks[$i]);

                        Log::add(\JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), Log::WARNING, 'jerror');

                        return false;
                    }

                    // If the table is checked out by another user, drop it and report to the user trying to change its state.
                    if (property_exists($table, 'checked_out') && $table->checked_out && ($table->checked_out != $user->id))
                    {
                        Log::add(\JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), Log::WARNING, 'jerror');

                        // Prune items that you can't change.
                        unset($pks[$i]);

                        return false;
                    }
                }
            }

            if(!$table -> updateState($pks,$value)){
                $this -> setError($table -> getError());
                return false;
            }
        }
        return true;
    }

    public function saveOrderAjax($pks = array(), $order = null, $group = null)
    {
        if(!$group){
            return parent::saveorder($pks, $order);
        }

        $table          = $this -> getTable('FieldGroupMap');
        $tableClassName = get_class($table);
        $contentType    = new UCMType();
        $type           = $contentType->getTypeByTable($tableClassName);
        $conditions     = array();

        if (empty($pks))
        {
            Factory::getApplication()->enqueueMessage(Text::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'), 'error');
            return false;
        }

        // Update ordering values
        foreach ($pks as $i => $pk)
        {
            $table->load(array('fieldsid' => (int) $pk, 'groupid' => $group));

            // Access checks.
            if (!$this->canEditState($table))
            {
                // Prune items that you can't change.
                unset($pks[$i]);
                Log::add(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), Log::WARNING, 'jerror');
            }
            elseif ($table->ordering != $order[$i])
            {
                $table->ordering = $order[$i];

                if (!$table->store())
                {
                    $this->setError($table->getError());

                    return false;
                }

                // Remember to reorder within position and client_id
                $condition = $this->getReorderConditions($table);
                $found = false;

                foreach ($conditions as $cond)
                {
                    if ($cond[1] == $condition)
                    {
                        $found = true;
                        break;
                    }
                }

                if (!$found)
                {
                    $key = $table->getKeyName();
                    $conditions[] = array($table->$key, $condition);
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond)
        {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }

    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = Factory::getApplication()->getIdentity();

            $state  = $user->authorise('core.delete', $this->option.'.field.' . (int) $record->id)
                || ($user->authorise('core.delete.own', $this->option.'.field.' . (int) $record->id)
                    && $record -> created_by == $user -> id);
            return $state;
        }

        return false;
    }

    protected function canEditState($record)
    {
        $user = Factory::getApplication()->getIdentity();

        // Check for existing category.
        if (!empty($record->id))
        {
            if(isset($record -> asset_id) && !empty($record -> asset_id)) {
                $state = $user->authorise('core.edit.state', $this->option . '.field.' . (int)$record->id)
                    || ($user->authorise('core.edit.state.own', $this->option . '.field.' . (int)$record->id)
                        && $record->created_by == $user->id);
            }else{
                $state  = parent::canEditState($record) || ($user->authorise('core.edit.state.own',$this -> option)
                        && $record -> created_by == $user -> id);
            }
            return $state;
        }
        // Default to component settings if neither category nor parent known.
        return parent::canEditState($record);
    }
}