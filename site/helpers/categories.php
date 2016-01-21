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

class TZ_Portfolio_PlusFrontHelperCategories{
    protected static $cache = array();

    public static function getCategoriesByArticleId($articleId, $options =
                            array('main' => null, 'condition' => null, 'reverse_contentid' => true)){
        if($articleId) {
            $_options   = '';
            $config     = array('main' => null, 'condition' => null, 'reverse_contentid' => true);
            if(count($options)) {
                $config     = array_merge($config,$options);
                $_options   = implode(',',$config);
            }
            if(is_array($articleId)) {
                $storeId = md5(__METHOD__ . '::'.implode(',', $articleId).'::'.$_options);
            }else{
                $storeId = md5(__METHOD__ . '::'.$articleId.'::'.$_options);
            }

            if(!isset(self::$cache[$storeId])){
                $db     =  JFactory::getDbo();
                $query  =  $db -> getQuery(true);

                $query  -> select('c.*, m.contentid AS contentid');
                $query  -> from('#__tz_portfolio_plus_categories AS c');
                $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
                $query  -> join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');

                if(is_array($articleId)) {
                    $query -> where('cc.id IN('.implode(',', $articleId) .')');
                }else{
                    $query -> where('cc.id = '.$articleId);
                }

                if(count($config)){
                    if ($config['main'] === true) {
                        $query->where('m.main = 1');
                    } elseif ($config['main'] === false) {
                        $query->where('m.main = 0');
                    }
                    if(isset($config['condition']) && $config['condition']){
                        $query -> where($config['condition']);
                    }
                    if(isset($config['orderby']) && isset($config['orderby'])){
                        $query->order($config['orderby']);
                    }
                    if(isset($config['groupby']) && isset($config['groupby'])){
                        $query->group($config['groupby']);
                    }
                }

                $db -> setQuery($query);
                if($data = $db -> loadObjectList()){
                    $categories     = array();
                    $categoryIds    = array();
                    foreach($data as $i => &$item){
                        $item -> order	= $i;
                        $item -> link	= JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($item -> id));

                        // Create article's id is array's key with value are tags
                        if(count($config) && isset($config['reverse_contentid']) && $config['reverse_contentid']){
                            if(!isset($categories[$item -> contentid])){
                                $categories[$item -> contentid]    = array();
                            }
                            if(!isset($categoryIds[$item -> contentid])){
                                $categoryIds[$item -> contentid]    = array();
                            }
                            if(!in_array($item -> id, $categoryIds[$item -> contentid])) {
                                if(count($config) && $config['main'] === true) {
                                    $categories[$item->contentid]     = $item;
                                }else {
                                    $categories[$item->contentid][] = $item;
                                }
                                $categoryIds[$item -> contentid][]  = $item -> id;
                            }
                        }
                    }

                    if(!count($categories)){
                        $categories   = $data;
                        if(count($config) && $config['main'] === true) {
                            $categories = array_shift($data);
                        }
                    }

                    self::$cache[$storeId]  = $categories;
                    return $categories;
                }

                self::$cache[$storeId]  = false;
            }

            return self::$cache[$storeId];
        }
        return false;
    }

    public static function getCategoriesById($id, $options = array('second_by_article' => false, 'orderby' => null)){
        if($id){
            if(is_array($id)){
                $storeId    = md5(__METHOD__ . '::' . implode(',',$id));
            }else {
                $storeId    = md5(__METHOD__ . '::' . $id);
            }
            if(!isset(self::$cache[$storeId])){
                $db         = JFactory::getDbo();
                $query      = $db -> getQuery(true);
                $subquery   = $db -> getQuery(true);

                $query -> select('c.*');
                $query -> from('#__tz_portfolio_plus_categories AS c');

                $query -> where('c.published = 1');

                if(count($options) && isset($options['second_by_article']) && $options['second_by_article']) {
                    $query->join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
                    $query->join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');

                    $subquery->select('DISTINCT c2.id');
                    $subquery->from('#__tz_portfolio_plus_content AS c2');
                    $subquery->join('INNER', '#__tz_portfolio_plus_content_category_map AS m2 ON m2.contentid = c2.id');
                    $subquery->join('INNER', '#__tz_portfolio_plus_categories AS cc2 ON cc2.id = m2.catid');

                    if (is_array($id)) {
                        $subquery->where('cc2.id IN(' . implode(',', $id) . ')');
                    } else {
                        $subquery->where('cc2.id = ' . $id);
                    }

                    $query->where('cc.id IN(' . $subquery . ')');

                    $query->group('c.id');
                }else{
                    if (is_array($id)) {
                        $query -> where('c.id IN(' . implode(',', $id) . ')');
                    } else {
                        $query -> where('c.id = ' . $id);
                    }
                }

                if(count($options) && isset($options['orderby']) && $options['orderby']){
                    $query -> order($options['orderby']);
                }

                $query->group('c.id');

                $db -> setQuery($query);

                $categories = null;

                if(is_array($id)){
                    $categories = $db -> loadObjectList();
                    foreach($categories as &$category){
                        $category -> link   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($category -> id));
                    }
                }else{
                    $categories = $db -> loadObject();
                    $categories -> link   = JRoute::_(TZ_Portfolio_PlusHelperRoute::getCategoryRoute($categories -> id));
                }
                if($categories){
                    self::$cache[$storeId]  = $categories;
                    return $categories;
                }
                self::$cache[$storeId]  = false;
            }
            return self::$cache[$storeId];

        }
        return false;
    }

    public static function getAllCategories($options = array('second_by_article' => false, 'orderby' => null)){
        $storeId    = md5(__METHOD__);
        if(!isset(self::$cache[$storeId])){
            $db     =  JFactory::getDbo();
            $query  =  $db -> getQuery(true);
            $query -> select('c.*');
            $query -> from('#__tz_portfolio_plus_categories AS c');
            $query -> where('c.published = 1');
            if(count($options)){
                if(isset($options['second_by_article']) && $options['second_by_article']){
                    $query -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
                    $query -> join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');
                }
                if(isset($options['orderby']) && $options['orderby']){
                    $query -> order($options['orderby']);
                }

                $query -> group('id');
            }
            $db -> setQuery($query);
            if($categories = $db -> loadObjectList()){
                self::$cache[$storeId]  = $categories;
                return $categories;
            }
            return self::$cache[$storeId];
        }
        return false;
    }

//    public static function getMainCategoriesByArticleId($articleId, $options = array()){
//        if($articleId) {
//            $_options   = '';
//            $config     = array('condition' => null, 'reverse_contentid' => true);
//            if(count($options)) {
//                $registry = new Registry();
//                $registry -> loadArray($config);
//                $registry -> merge($options);
//                $config     = $registry -> toArray();
//                $_options   = $registry -> toString('ini');
//            }
//            if(is_array($articleId)) {
//                $storeId    = md5(__METHOD__ . '::'.implode(',', $articleId).'::'.$_options);
//            }else{
//                $storeId    = md5(__METHOD__ . '::'.$articleId.'::'.$_options);
//            }
//
//            if(!isset(self::$cache[$storeId])){
//                $db     =  JFactory::getDbo();
//                $query  =  $db -> getQuery(true);
//
//                $query  -> select('c.*, m.contentid AS contentid');
//                $query  -> from('#__tz_portfolio_plus_categories AS c');
//                $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.catid = c.id');
//                $query  -> join('INNER', '#__tz_portfolio_plus_content AS cc ON cc.id = m.contentid');
//
//                if(is_array($articleId)) {
//                    $query -> where('cc.id IN('.implode(',', $articleId) .')');
//                }else{
//                    $query -> where('cc.id = '.$articleId);
//                }
//
//                $query -> where('m.main = 1');
//
//                if(count($config)){
//                    if(isset($config['condition']) && $config['condition']){
//                        $query -> where($config['condition']);
//                    }
//                    if(isset($config['orderby']) && isset($config['orderby'])){
//                        $query->order($config['orderby']);
//                    }
//                }
//
//                $db -> setQuery($query);
//
//                if($category = $db -> loadObject()){
//
//                    $category -> link	= TZ_Portfolio_PlusHelperRoute::getCategoryRoute($category -> id);
//
//                    self::$cache[$storeId]  = $category;
//                    return $category;
//                }
//
//                self::$cache[$storeId]  = false;
//            }
//            return self::$cache[$storeId];
//        }
//        return false;
//    }
}