<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

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
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\AdminModel;

class AddOnAdminModel extends AdminModel
{
    protected $data         = null;
    protected $plugin_type  = null;

//    public function getTable($type = 'Article', $prefix = 'Table', $config = array())
//    {
//        return Table::getInstance($type, $prefix, $config);
//    }

    public function getTable($type = 'Article', $prefix = 'Administrator', $config = array())
    {
        $mvc    = Factory::getApplication() -> bootComponent('tz_portfolio') -> getMVCFactory();
        return $mvc -> createTable($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $name   = $this -> getName();

        $path   = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> plugin_type.'/'.$name.'/admin/form';
        $path2  = COM_TZ_PORTFOLIO_ADDON_PATH.'/'.$this -> plugin_type.'/'.$name.'/admin/forms';

        Form::addFormPath(Path::clean($path));
        Form::addFormPath(Path::clean($path2));

        $xml_file   = Path::clean($path.'/'.$name.'.xml');
        $xml_file   = file_exists($xml_file)?$xml_file:Path::clean($path2.'/'.$name.'.xml');

        if(file_exists($xml_file)) {
            $form = $this->loadForm('plg_' . $this->plugin_type . $name . '.' . $name, $name,
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

        $this->preprocessData('com_tz_portfolio.article', $data);

        return $data;
    }

    public function getItem($pk = null)
    {
        return $this->data;
    }

    protected function __save($data,$dataInsert){
        if($dataInsert && count($dataInsert)){
            $registry = new Registry();
            if($data && !empty($data) && isset($data -> media) && !is_object($data -> media)){
                // Process data
                $registry->loadString($data -> media);

                if($registry -> get($this -> getName())) {
                    $old_data   = ArrayHelper::fromObject($registry->get($this -> getName()));
                    $dataInsert = array_merge($old_data, $dataInsert);
                }
            }

            // Store data to database
            $registry -> set($this -> getName(),$dataInsert);
            $data -> media  = $registry -> toString();
            $data -> store();
        }
    }

    protected function prepareImageSize($image_size){
        if($image_size && !is_array($image_size) && preg_match_all('/(\{.*?\})/',$image_size,$match)) {
            $image_size = $match[1];
        }
        return $image_size;
    }

}