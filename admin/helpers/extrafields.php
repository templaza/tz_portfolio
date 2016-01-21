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

class TZ_Portfolio_PlusHelperExtraFields{

    public static function getExtraFields($groupid=null,$catid=null){
        if($groupid || $catid) {
            $db     = JFactory::getDbo();
            $query  = $db->getQuery(true);

            $query -> select('f.*');
            $query -> from($db -> quoteName('#__tz_portfolio_plus_fields').' AS f');
            $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map')
                            .' AS m ON m.fieldsid = f.id');
            $query -> join('LEFT', $db -> quoteName('#__tz_portfolio_plus_categories')
                            .' AS c ON c.groupid = m.groupid');

            $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
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

            $db = JFactory::getDbo();
            $db -> setQuery($query);

            if($rows   = $db -> loadObjectList()){
                return $rows;
            }
        }
        return null;
    }

    public static function getFieldGroups($fieldid){
        if($fieldid){
            $db     = JFactory::getDbo();
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

    public static function removeFieldGroups($fieldid){
        if($fieldid){
            $db     = JFactory::getDbo();
            $query  = $db -> getQuery(true);
            $query -> delete($db -> quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
            if(is_array($fieldid)) {
                $query->where('fieldsid IN(' . implode(',', $fieldid) . ')');
            }else{
                $query->where('fieldsid =' .$fieldid);
            }
            $db -> setQuery($query);
            if($db -> execute()) {
                return true;
            }
        }
        return false;
    }

    public static function insertFieldGroups($fieldid, $groupid){
        if($fieldid && $groupid){
            $db     = JFactory::getDbo();

            // Remove old field's group
            self::removeFieldGroups($fieldid);

//            var_dump($groupid); die();

            $query  = $db -> getQuery(true);
            $query->insert($db->quoteName('#__tz_portfolio_plus_field_fieldgroup_map'));
            $query->columns('fieldsid,groupid');
            if(is_array($groupid)) {
//                $query->where('groupid IN(' . implode(',', $groupid) . ')');
                foreach($groupid as $gid){
                    $query -> values($fieldid . ',' . $gid);
                }
            }else{
                $query -> values($fieldid . ',' . $groupid);
            }
            $db -> setQuery($query);
            if($db -> execute()) {
                return true;
            }
        }
        return false;
    }
}