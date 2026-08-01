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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Helper;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ExtraFieldsFrontHelper;
use Joomla\Database\DatabaseInterface;

class ExtraFieldsHelper{

    public static function getExtraFields($groupid=null,$catid=null){
        if($groupid || $catid) {
            $db     = Factory::getContainer()->get(DatabaseInterface::class);
            $query  = $db->getQuery(true);

            $query -> select('f.*');
            $query -> from($db -> quoteName('#__tz_portfolio_plus_fields').' AS f');
            $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map')
                            .' AS m ON m.fieldsid = f.id');
            $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_categories')
                            .' AS c ON c.groupid = m.groupid');

            $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                -> where('(e.type = '.$db -> quote('tz_portfolio-addon').' OR e.type = '.
                    $db -> quote('tz_portfolio_plus-plugin').')')
                -> where('e.folder = '.$db -> quote('extrafields'))
                -> where('e.published = 1');

            $query -> where('f.published = 1');

            // if inherit category
            if ($groupid == 0) {
                if($catid) {
                    $query->where('c.id = ' . $catid);
                }
            } else {
                $query -> where('m.groupid = '. $groupid);
            }
            $query -> order('f.ordering ASC');

            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $db -> setQuery($query);

            if($rows   = $db -> loadObjectList()){
                return $rows;
            }
        }
        return null;
    }

    public static function getAllExtraFields(){
        $db     = Factory::getContainer()->get(DatabaseInterface::class);
        $query  = $db->getQuery(true);

        $query -> select('f.*');
        $query -> from($db -> quoteName('#__tz_portfolio_plus_fields').' AS f');
        $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map')
            .' AS m ON m.fieldsid = f.id');

        $query -> select('g.id AS groupid, g.name AS group_title');
        $query -> join('INNER', $db -> quoteName('#__tz_portfolio_plus_fieldgroups').' AS g ON g.id = m.groupid');

        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
            -> where('(e.type = '.$db -> quote('tz_portfolio-addon')
                .' OR e.type = '.$db -> quote('tz_portfolio_plus-plugin').')')
            -> where('e.folder = '.$db -> quote('extrafields'))
            -> where('e.published = 1');

        $query -> where('f.published = 1');
        $query -> group('f.id');

        $query -> order('g.id ASC');

        $db -> setQuery($query);

        if($rows   = $db -> loadObjectList()){
            return $rows;
        }
        return null;
    }

    public static function getFieldGroups($fieldid){
        if($fieldid){
            $db     = Factory::getContainer()->get(DatabaseInterface::class);
            $query  = $db -> getQuery(true);

            $query -> select('groupid');
            $query -> from($db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
            $query -> where('fieldsid='.$fieldid);
            $db -> setQuery($query);
            if($items = $db -> loadColumn()){
                return $items;
            }
        }
        return false;
    }

    public static function removeFieldGroups($fieldid, $groupid = null){
        if($fieldid){
            $db     = Factory::getContainer()->get(DatabaseInterface::class);
            $query  = $db -> getQuery(true);

            $query -> delete($db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
            if(is_array($fieldid)) {
                $query->where('fieldsid IN(' . implode(',', $fieldid) . ')');
            }else{
                $query->where('fieldsid =' .$fieldid);
            }
            if($groupid){
                if(is_numeric($groupid)){
                    $query -> where('groupid <> '.$groupid);
                }elseif(is_array($groupid)){
                    $query -> where('groupid NOT IN('. implode(',', $groupid) .')');
                }
            }
            $db -> setQuery($query);
            if($bool = $db -> execute()) {
                return true;
            }
        }
        return false;
    }

    public static function insertFieldGroups($fieldid, $groupid){
        if($fieldid && $groupid){
            $db         = Factory::getContainer()->get(DatabaseInterface::class);
            $_groupid   = $groupid;
            $_dbGroupid = null;

            if($dbGroupid = self::getFieldGroups($fieldid)){
                $_groupid   = array_diff($groupid, $dbGroupid);
            }

            // Remove old field's group
            self::removeFieldGroups($fieldid, $groupid);

            if(count($_groupid)) {
                $query = $db->getQuery(true);
                $query->insert($db->quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
                $query->columns('fieldsid,groupid');
                if (is_array($_groupid)) {
                    foreach ($_groupid as $gid) {
                        $query->values($fieldid . ',' . $gid);
                    }
                } else {
                    $query->values($fieldid . ',' . $_groupid);
                }
                $db->setQuery($query);
                if ($db->execute()) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function prepareForm(&$form, $data){
        if($addons = AddonsHelper::getAddons(array('published' => 1, 'folder' => 'extrafields'))){
            foreach($addons as $addon){
                $field          = new \stdClass();
                $field -> id    = $addon -> id;
                $field -> params    = new Registry();
                if(isset($addon -> params) && !is_object($addon -> params)){
                    $field -> params    = new Registry($addon -> params);
                }else{
                    $field -> params    = $addon -> params;
                }
                $field -> type      = $addon -> element;
                $fieldObj   = ExtraFieldsFrontHelper::getExtraField($field);
                if(!empty($fieldObj) && method_exists($fieldObj, 'prepareForm')) {
                    call_user_func_array(array($fieldObj, 'prepareForm'), array(&$form, $data));
                }
            }
        }
    }
}