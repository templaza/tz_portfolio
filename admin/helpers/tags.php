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

class TZ_Portfolio_PlusHelperTags{
    protected static $cache     = array();
    protected static $error     =  null;

    // Get all tags by article's id or ids
    public static function getTagsByArticleId($articleId){
        if($articleId){
            if(is_array($articleId)) {
                $storeId    = implode('_', $articleId);
            }else{
                $storeId    = $articleId;
            }

            if(!isset(self::$cache[$storeId])){
                $db     = JFactory::getDbo();
                $query  = $db -> getQuery(true);
                $query -> select('t.*');
                $query -> from('#__tz_portfolio_plus_tags AS t');
                $query -> join('LEFT', '#__tz_portfolio_plus_tag_content_map AS m ON m.tagsid = t.id');
                $query -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = m.contentid');
                if(is_array($articleId)) {
                    $query->where('m.contentid IN('. implode(',', $articleId) .')');
                }else{
                    $query->where('m.contentid = '. $articleId);
                }
                $db -> setQuery($query);
                if($tags = $db -> loadObjectList()){
                    self::$cache[$storeId]    = $tags;
                    return $tags;
                }
                self::$cache[$storeId]    = false;
            }
            return self::$cache[$storeId];
        }
        return false;
    }

    public static function getTagsByTitle($title){

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__tz_portfolio_plus_tags');
        if(is_array($title) && count($title)) {
            foreach($title as $a){
                $query -> where('title = '.$db -> quote($a), 'OR');
            }
        }else{
            $query -> where('title = '.$db -> quote($title));
        }

        $db -> setQuery($query);
        if($tags = $db -> loadObjectList()){
            return $tags;
        }

        return false;
    }

    public static function getTagsByAlias($alias){

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('*');
        $query -> from('#__tz_portfolio_plus_tags');
        if(is_array($alias) && count($alias)) {
            $where  = array();
            foreach($alias as $a){
                $where[]    = 'alias = '.$db -> quote($a);
            }
            if(count($where)) {
                $query->where(implode(' OR ', $where));
            }
        }else{
            $query -> where('alias = '.$db -> quote($alias));
        }

        $db -> setQuery($query);
        if($tags = $db -> loadObjectList()){
            return $tags;
        }

        return false;
    }

    public static function insertTagsByArticleId($articleId, $tagTitles){

        if($articleId) {
            // Delete old article's tag
            $db     = JFactory::getDbo();
            $query  = $db -> getQuery(true);

            $query -> delete('#__tz_portfolio_plus_tag_content_map');
            if(is_array($articleId)){
                $query->where('contentid IN(' . implode(',', $articleId).')');
            }else {
                $query->where('contentid = ' . (int)$articleId);
            }
            $db -> setQuery($query);
            $db -> execute();

            if($tagTitles){
                $tagsIds        = array();
                $tagTitleCreate = array();
                $newTagTitles   = array();

                // Get all tag's information
                if($tagsCreated = self::getTagsByTitle($tagTitles)){
                    $tagTitleCreate = JArrayHelper::getColumn($tagsCreated, 'title');
                    $tagsIds        = JArrayHelper::getColumn($tagsCreated, 'id');
                }

                // Get new tag title data
                $newTagTitles = array_diff($tagTitles, $tagTitleCreate);
                // Remove tag title duplicate
                $newTagTitles = array_unique($newTagTitles);
                // Remove tag title is null
                $newTagTitles = array_filter($newTagTitles);
                $newTagTitles = array_reverse($newTagTitles);

                // Insert new tags by tag's titles
                if (count($newTagTitles)) {
                    if ($newTagId = self::_insertTagsByTitle($newTagTitles)) { // Get last tag id new is added
                        foreach($newTagTitles as $key => $value){
                           array_push($tagsIds, $newTagId + $key);
                        }
                    }
                }

                // Assign new tags for article
                if (count($tagsIds) > 0) {
                    // Execute sql assign new tags for article
                    $query -> clear();
                    $query -> insert('#__tz_portfolio_plus_tag_content_map');
                    $query -> columns('contentid, tagsid');
                    foreach ($tagsIds as $id) {
                        $query -> values($articleId . ',' . $id);
                    }
                    $db -> setQuery($query);
                    if (!$db -> execute()) {
                        self::_setError($db -> getErrorMsg());
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public static function getTagsSuggestToArticle(){
        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('title');
        $query -> from('#__tz_portfolio_plus_tags');
        $db -> setQuery($query);

        if($rows = $db -> loadColumn()){
            return json_encode($rows);
        }
        return null;
    }

    public static function getTagTitlesByArticleId($articleId){
        if($tags = self::getTagsByArticleId($articleId)){
            $tags   = JArrayHelper::getColumn($tags, 'title');
            return array_unique($tags);
        }
        return false;
    }

    public static function getTagByKey($keys = array(), $not = null){
        if($keys && count($keys)){
            $db     = JFactory::getDbo();
            $query  = $db -> getQuery(true);
            foreach($keys as $key => $value) {
                $query -> select($key);
                if($not){
                    if(is_array($not) && count($not)){
                        if(isset($not[$key]) && $not[$key]){
                            $query->where($key . '<>' . (is_numeric($value) ? $value : $db->quote($value)));
                        }else{
                            $query -> where($key . '=' . (is_numeric($value) ? $value : $db->quote($value)));
                        }
                    }else{
                        $query -> where($key.'<>'.(is_numeric($value)?$value:$db -> quote($value)));
                    }
                }else {
                    $query->where($key . '=' . (is_numeric($value) ? $value : $db->quote($value)));
                }
            }

            $query -> from('#__tz_portfolio_plus_tags');
            $db -> setQuery($query);
            if($tags = $db -> loadAssoc()){
                return $tags;
            }
        }
        return false;
    }

    protected static function _insertTagsByTitle($titles){
        if($titles && is_array($titles) && count($titles)>0){
            $db     = JFactory::getDbo();
            $query  = $db -> getQuery(true);
            $query -> insert('#__tz_portfolio_plus_tags');
            $query -> columns('title, alias, published');

            foreach($titles as $title){
                if (JFactory::getConfig()->get('unicodeslugs') == 1)
                {
                    $alias  = JFilterOutput::stringURLUnicodeSlug($title);
                }
                else
                {
                    $alias  = JFilterOutput::stringURLSafe($title);
                }
                $query -> values($db -> quote($title) .',' . $db -> quote($alias).', 1');
            }
            $db -> setQuery($query);

            if(!$db -> execute()){
                self::_setError($db -> getErrorMsg());
                return false;
            }
            return $db -> insertid();
        }
        return false;
    }

    protected static function _setError($error){
        self::$error    = $error;
    }

    public static function getError(){
        return self::$error;
    }

    protected static function clearCache(){
        if(count(self::$cache)){
            self::$cache    = array();
        }
        return true;
    }
}