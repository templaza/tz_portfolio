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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\AdminModel;

class AddOnModel extends AdminModel
{
    protected $data         = null;
    protected $plugin_type  = null;
    protected $addon_name = null;

    public function getTable($type = 'AddonData', $prefix = 'Administrator', $config = array())
    {
        $mvc    = Factory::getApplication() -> bootComponent('tz_portfolio') -> getMVCFactory();
        return $mvc -> createTable($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $name   = $this -> getName();

        $path  = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> plugin_type.'/'.$this -> addon_name.'/admin/forms';

        Form::addFormPath(Path::clean($path));

        $xml_file   = Path::clean($path.'/'.$name.'.xml');
        if(file_exists($xml_file)) {
            $form = $this->loadForm('plg_' . $this->plugin_type . '_' . $this -> addon_name . '.' . $name, $name,
                array('control' => 'jform', 'load_data' => $loadData));
        }

        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_tz_portfolio.edit.article.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        if (empty($data)) {
            $data = new \stdClass();
        }
        $this->preprocessData('com_tz_portfolio.article', $data);

        return $data;
    }

    public function getActionLink() {
        $addon_view = Factory::getApplication()->input->getString('addon_view', '');
        $addon_id   = Factory::getApplication()->input->getInt('addon_id');
        $return = Factory::getApplication()->input->getString('return', '');
        return 'index.php?option=com_tz_portfolio&view=addon_datas&addon_id='.$addon_id.'&addon_view='.$addon_view.'&id='.$this->getState($this->getName() . '.id').'&addon_return='.$return;
    }

    public function getItem($pk = null)
    {
        $pk    = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table = $this->getTable();
        if ($pk > 0) {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false) {
                // If there was no underlying error, then the false means there simply was not a row in the db for this $pk.
                if (!$table->getError()) {
                    $this->setError(Text::_('JLIB_APPLICATION_ERROR_NOT_EXIST'));
                } else {
                    $this->setError($table->getError());
                }

                return false;
            }
        }

        // Convert to \stdClass before adding other data
        $properties = get_object_vars($table);
        $item       = ArrayHelper::toObject($properties);
        $addon_data = !empty($item -> value) ? \json_decode($item -> value) : new \stdClass();
        $addon_data -> id = $item -> id;
        return $addon_data;
    }

    public function save($data){
        $table      = $this->getTable();
        $isNew = true;
        if (is_object($data)) {
            $data = ArrayHelper::fromObject($data);
        }
        if (!empty($data['id'])) {
            if ($table->load($data['id'])) {
                $isNew = false;
            }
        } elseif (!empty($data['extension_id']) && !empty($data['content_id'])) {
            if ($table->load(array('extension_id' => $data['extension_id'], 'content_id' => $data['content_id']))) {
                $isNew = false;
            }
        }

        if (!$table->bind($data)) {
            $this->setError($table->getError());
            return false;
        }
        // Prepare the row for saving
        $this->prepareTable($table);
        // Check the data.
        if (!$table->check()) {
            $this->setError($table->getError());

            return false;
        }
        if (!$table->store()) {
            $this->setError($table->getError());

            return false;
        }
        return $table;
    }

    public function delete(&$article)
    {
        if($article) {
            if (is_object($article)) {
                if (!empty($article->addon)) {
                    $table      = $this->getTable();
                    if (isset($article->addon->id) && !empty($article->addon->id)
                        && isset($article->id) && !empty($article->id)) {
                        if ($table->load(array('extension_id' => $article->addon->id, 'content_id' => $article->id))) {
                            if (!$table->delete($table->id)) {
                                $this->setError($table->getError());
                                return false;
                            }
                        }
                    }
                }
            }
        }
    }

    public function remove($cid)
    {
        foreach($cid as $id){
            $table = $this->getTable();
            $table->delete($id);
        }
    }

    protected function prepareImageSize($image_size){
        if($image_size && !is_array($image_size) && preg_match_all('/(\{.*?\})/',$image_size,$match)) {
            $image_size = $match[1];
        }
        return $image_size;
    }

    protected function __save($data,$image_data){
        if($image_data && count($image_data)){
            $registry = new Registry();
            if($data && !empty($data) && isset($data -> media) && !is_object($data -> media)){
                // Process data
                $registry->loadString($data -> media);

                if($registry -> get($this -> getName())) {
                    $old_data   = ArrayHelper::fromObject($registry->get($this -> getName()));
                    $image_data = array_merge($old_data, $image_data);
                }
            }

            // Store data to database
            $registry -> set($this -> getName(),$image_data);
            $data -> media  = $registry -> toString();
            $data -> store();
        }
    }
}