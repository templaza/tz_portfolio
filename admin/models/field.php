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

use Joomla\Registry\Registry;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.modeladmin');
JLoader::import('extrafields',COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH);
JLoader::register('TZ_Portfolio_PlusFrontHelperExtraFields',COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'extrafields.php');
tzportfolioplusimport('fields.extrafield');

class TZ_Portfolio_PlusModelField extends JModelAdmin
{
    public function __construct($config = array()){
        parent::__construct($config);
    }

    public function populateState(){
        parent::populateState();
    }

    public function getTable($type = 'Fields', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true){
        $form = $this->loadForm('com_tz_portfolio_plus.field', 'field', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getItem($pk = null){

        if($item = parent::getItem($pk)){
            $item -> field      = null;

            $item -> groupid    = TZ_Portfolio_PlusHelperExtraFields::getFieldGroups((int) $item -> id);
            return $item;
        }
        return false;
    }

    public function getGroups($pk = null){
        // Initialise variables.
        $pk     = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $db     = $this -> getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('g.*,f.id AS fieldsid');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map').' AS x ON x.groupid=g.id');
        $query -> join('LEFT',$db -> quoteName('#__tz_portfolio_plus_fields').' AS f ON x.fieldsid = f.id');
        $query -> where('f.id='.$pk);
        $db -> setQuery($query);

        if($items = $db -> loadObjectList()){
            $list   = array();
            foreach($items as $item){
                $list[$item -> id]  = $item;
            }
            return $list;
        }

        return array();
    }
    
    public function save($data){
        $groupid    = $data['groupid'];
        unset($data['groupid']);


        $table = $this->getTable();

        if ($data['id'] && $table->load($data['id']))
        {
            $fieldClass     = TZ_Portfolio_PlusFrontHelperExtraFields::getExtraField($data['id']);
            $data           = $fieldClass->onSave($data);
        }

        if(parent::save($data)){

            $pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');

            // Insert field's groups
            TZ_Portfolio_PlusHelperExtraFields::insertFieldGroups($pk, $groupid);

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

    protected function preprocessForm(JForm $form, $data, $group = 'content')
    {
        if($data && $data -> id){
            $core_path  = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields';
            $core_f_xml_path    = $core_path.DIRECTORY_SEPARATOR.$data -> type.DIRECTORY_SEPARATOR
                .'admin'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.'field.xml';
            if(JFile::exists($core_f_xml_path)){
                $form -> loadFile($core_f_xml_path, false, '/form/fields[@name="params"]');
            }
        }
    }

    public function publish(&$pks,$value=1){
        $table  = $this -> getTable();
        if(!$table -> publish($pks,$value)){
            $this -> setError($table -> getError());
            return false;
        }
        return true;
    }
}