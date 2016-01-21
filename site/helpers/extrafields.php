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

class TZ_Portfolio_PlusFrontHelperExtraFields{

    protected static $cache     = array();

    public static function getExtraField($field, $article = null, $resetArticleCache = false)
    {
        if (!$field)
        {
            return null;
        }

        if (is_object($field))
        {
            $fieldId = $field->id;
        }
        else
        {
            $fieldId = $field;
        }

        $storeId = md5("TZ_Portfolio_PlusCTField::" . $fieldId);
        if (!isset(self::$cache['fields'][$storeId]))
        {

            if (!is_object($field))
            {
                $field = self::getExtraFieldById($field);
            }

            if (!$field)
            {
                return false;
            }
            tzportfolioplusimport('fields.extrafield');

            if (!self::checkExtraField($field-> type))
            {
                $fieldClassName = 'TZ_Portfolio_PlusExtraField';
            }
            else
            {
                $fieldClassName = 'TZ_Portfolio_PlusExtraField' . $field->type;
            }

            self::loadExtraFieldFile($field -> type);

            $_fieldObj = clone $field;

            $fieldClass = null;
            if (class_exists($fieldClassName))
            {
                $fieldClass = new $fieldClassName($_fieldObj);
            }

            self::$cache['fields'][$storeId] = $fieldClass;
        }


        $fieldClass = self::$cache['fields'][$storeId];
        if ($fieldClass)
        {
            $fieldClassWithDoc = clone $fieldClass;
            $fieldClassWithDoc->loadArticle($article, $resetArticleCache);

            return $fieldClassWithDoc;
        }
        else
        {
            return $fieldClass;
        }
    }

    public static function getExtraFields($article, $params = null, $group = false){
        $fields     = null;
        if($group){
            $groupobj   = self::getFieldGroupsByArticleId($article -> id);
            $groupid    = JArrayHelper::getColumn($groupobj, 'id');
            $fields     = self::getExtraFieldsByFieldGroupId($groupid);
        }else{
            $fields = self::getExtraFieldsByArticle($article, $params);
        }
        if($fields){
            if(count($fields)){
                $fieldsObject   = array();
                foreach($fields as $field){
                    if($field -> published == 1) {
                        $fieldsObject[] = self::getExtraField($field, $article);
                    }
                }
                return $fieldsObject;
            }
        }
        return false;
    }

    public static function getFieldGroupsById($fieldGroupId)
    {
        $storeId = md5(__METHOD__ . "::" . (int) $fieldGroupId);
        if (!isset(self::$cache[$storeId]))
        {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__tz_portfolio_plus_fieldgroups');
            if(is_array($fieldGroupId)){
                $query -> where('id IN('.implode(',', $fieldGroupId).')');
            }else {
                $query->where('id = ' . $fieldGroupId);
            }
            $query -> where('published = 1');
            $db->setQuery($query);
            self::$cache[$storeId] = $db->loadObjectList();
        }

        return self::$cache[$storeId];
    }

    public static function getFieldGroupsByArticleId($articleId){
        $storeId = md5(__METHOD__ . "::" . (int) $articleId);
        if (!isset(self::$cache[$storeId]))
        {
            if($articleId) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $subquery = $db->getQuery(true);

                $subquery->select('CASE WHEN c.groupid = 0 OR c.groupid IS NULL THEN cc.groupid ELSE c.groupid END AS groupid');
                $subquery->from('#__tz_portfolio_plus_content AS c');
                $subquery->join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');
                $subquery->join('LEFT', '#__tz_portfolio_plus_categories AS cc ON cc.id = m.catid');

                if (is_array($articleId)) {
                    $subquery->where('c.id IN(' . implode(',', $articleId) . ')');
                } else {
                    $subquery->where('c.id = ' . $articleId);
                }
                $subquery->group('groupid');

                $query->select('*');
                $query->from('#__tz_portfolio_plus_fieldgroups');
                $query->where('id IN(' . $subquery . ')');

                $db->setQuery($query);
                self::$cache[$storeId] = $db->loadObjectList();
            }else{
                self::$cache[$storeId]  = false;
            }
        }

        return self::$cache[$storeId];
    }

    public static function getFieldGroupsByCatId($catId)
    {
        $storeId = md5(__METHOD__ . "::" . (int) $catId);
        if (!isset(self::$cache[$storeId]))
        {
            if($catId) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('g.*, c.id AS catid, c.title AS category_title');
                $query->from('#__tz_portfolio_plus_fieldgroups AS g');
                $query->join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.groupid = g.id');
                if (is_array($catId)) {
                    $query->where('c.id IN(' . implode(',', $catId) . ')');
                } else {
                    $query->where('c.id = ' . $catId);
                }
                $query->where('g.published = 1');
                $query->where('c.published = 1');
                $db->setQuery($query);
                self::$cache[$storeId] = $db->loadObjectList();
                return self::$cache[$storeId];
            }else{
                self::$cache[$storeId]  = false;
            }
        }

        return self::$cache[$storeId];
    }

    public static function loadExtraFieldFile($name){
        $storeId = md5(__METHOD__ . "::$name");
        if(!isset(self::$cache[$storeId])){
            if(self::checkExtraField($name)){
                require_once(COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields'
                    .DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.'.php');
            }else {
                tzportfolioplusimport('fields.extrafield');
            }
            self::$cache[$storeId]  = true;
        }
        return self::$cache[$storeId];
    }

    protected static function checkExtraField($name){

        $storeId = md5(__METHOD__ . "::$name");
        if(!isset(self::$cache[$storeId])){
            $core_path  = COM_TZ_PORTFOLIO_PLUS_ADDON_PATH.DIRECTORY_SEPARATOR.'extrafields';
            if($core_folders = JFolder::folders($core_path)){
                $core_f_xml_path    = $core_path.DIRECTORY_SEPARATOR.$name
                    .DIRECTORY_SEPARATOR.$name.'.xml';
                if(JFile::exists($core_f_xml_path)){
                    self::$cache[$storeId]  = true;
                    return self::$cache[$storeId];
                }
            }

            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }

    public static function getExtraFieldById($fieldId, $fieldObj = null)
    {
        if (!$fieldId)
        {
            return null;
        }

        $storeId = md5(__METHOD__ . "::$fieldId");

        if (!isset(self::$cache[$storeId]))
        {
            if (!is_object($fieldObj))
            {
                $db    = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('field.*')
                    ->from('#__tz_portfolio_plus_fields AS field');

                $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = field.type')
                    -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                    -> where('e.folder = '.$db -> quote('extrafields'))
                    -> where('e.published = 1');

                if($fieldId) {
                    $query->where('field.id = ' . $fieldId);
                }

                $db->setQuery($query);

                $fieldObj = $db->loadObject();
            }

            self::$cache[$storeId] = $fieldObj;
        }

        return self::$cache[$storeId];
    }

    public static function getExtraFieldsByFieldGroupId($groupid){
        if($groupid) {
            if (is_array($groupid)) {
                $storeId = md5(__METHOD__ . '::' . implode(',', $groupid));
            } else {
                $storeId = md5(__METHOD__ . '::' . $groupid);
            }
            if (!isset(self::$cache[$storeId])) {
                if($groupid){
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);

                    $query->select('f.*');
                    $query->from('#__tz_portfolio_plus_fields AS f');
                    $query->join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS m ON m.fieldsid = f.id');
                    $query->join('INNER', '#__tz_portfolio_plus_fieldgroups AS g ON g.id = m.groupid');

                    $query->join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                        ->where('e.type = ' . $db->quote('tz_portfolio_plus-plugin'))
                        ->where('e.folder = ' . $db->quote('extrafields'))
                        ->where('e.published = 1');

                    if (is_array($groupid)) {
                        $query->where('g.id IN(' . implode(',', $groupid) . ')');
                    } else {
                        $query->where('g.id = ' . $groupid);
                    }
                    $db->setQuery($query);
                    if ($fields = $db->loadObjectList()) {
                        self::$cache[$storeId] = $fields;
                        return $fields;
                    }
                }
                self::$cache[$storeId] = false;
            }
            return self::$cache[$storeId];
        }
        return false;
    }

    public static function getExtraFieldsByArticle($article, $params = null){
        if (is_numeric($article))
        {
            $article = TZ_Portfolio_PlusContentHelper::getArticleById($article);
        }

        $groupid    = self::getFieldGroupsByArticleId($article -> id);
        $groupid    = JArrayHelper::getColumn($groupid, 'id');

        $storeId    = md5(__METHOD__.'::'.implode(',',$groupid).'::'.$article -> id);
        if(!isset(self::$cache[$storeId])){
            $db         = JFactory::getDbo();
            $query      = $db -> getQuery(true);

            $query -> select('f.*');
            $query -> from('#__tz_portfolio_plus_fields AS f');
            $query -> join('INNER', '#__tz_portfolio_plus_field_content_map AS m ON m.fieldsid = f.id');
            $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = m.contentid');
            $query -> join('INNER', '#__tz_portfolio_plus_field_fieldgroup_map AS fm ON fm.fieldsid = f.id');

            $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e ON e.element = f.type')
                -> where('e.type = '.$db -> quote('tz_portfolio_plus-plugin'))
                -> where('e.folder = '.$db -> quote('extrafields'))
                -> where('e.published = 1');

            if(count($groupid)) {
                $query->where('fm.groupid IN('.implode(',', $groupid).')');
            }
            $query -> where('c.id = '.$article -> id);
            $query -> where('f.published = 1');
            $db    -> setQuery($query);

            if($fields = $db -> loadObjectList()){
                self::$cache[$storeId]  = $fields;
                return $fields;
            }
            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }
}