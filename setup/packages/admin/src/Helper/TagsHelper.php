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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\Database\DatabaseInterface;

class TagsHelper{

    protected static $cache     = array();
    protected static $error     =  null;

    /**
     * Get all tags by article's id or ids
     * @param int|array $articleId
     * @return bool|array
     * */
    public static function getTagsByArticleId($articleId){
        $storeId    = __METHOD__;
        $storeId   .= '::'.serialize($articleId);
        $storeId    = md5($storeId);
        
        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }
        
        if(!$articleId){
            return false;
        }

        $db     = Factory::getContainer()->get(DatabaseInterface::class);
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
        
        return false;
    }

    public static function getTagsByTitle($title){

        $storeId    = __METHOD__;
        $storeId   .= '::'.serialize($title);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if(!$title){
            return false;
        }

        $db     = Factory::getContainer()->get(DatabaseInterface::class);
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
            return self::$cache[$storeId] = $tags;
        }

        return false;
    }

    public static function getTagsByAlias($alias){

        $storeId    = __METHOD__;
        $storeId   .= '::'.serialize($alias);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if(!$alias){
            return false;
        }

        $db     = Factory::getContainer()->get(DatabaseInterface::class);

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
            return self::$cache[$storeId] = $tags;
        }

        return false;
    }

    public static function insertTagsByArticleId($articleId, $tagTitles){

        if($articleId) {
            // Delete old article's tag
            $db     = Factory::getContainer()->get(DatabaseInterface::class);
            $query  = $db -> getQuery(true);

            if(!$tagTitles || ($tagTitles && !count($tagTitles))) {
                $query->delete('#__tz_portfolio_plus_tag_content_map');
                if (is_array($articleId)) {
                    $query->where('contentid IN(' . implode(',', $articleId) . ')');
                } else {
                    $query->where('contentid = ' . (int)$articleId);
                }
                $db->setQuery($query);
                $db->execute();
                return true;
            }

            if($tagTitles){
                $tagsIds        = array();
                $tagTitleCreate = array();
                $newTagTitles   = array();

                foreach($tagTitles as $key => &$tag){
                    if(strpos($tag, '#new#') !== false){
                        $tagText = str_replace('#new#', '', $tag);
                        $newTagTitles[] = $tagText;
                    }else{
                        $tagsIds[]  = $tag;
                    }
                }

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
                    $table       = Table::getInstance('TagContentMapTable','TemPlaza\Component\TZ_Portfolio\Administrator\Table\\');

                    // Execute sql assign new tags for article
                    foreach ($tagsIds as $id) {
                        $table -> set('id', 0);
                        if(!$table -> load(array('tagsid' => ((int) $id), 'contentid' => $articleId))){
                            $table -> bind(array('tagsid' => ((int) $id), 'contentid' => $articleId));
                            $table -> store();
                        }
                    }

                    $query -> clear();
                    $query->delete('#__tz_portfolio_plus_tag_content_map');
                    if (is_array($articleId)) {
                        $query->where('contentid IN(' . implode(',', $articleId) . ')');
                    } else {
                        $query->where('contentid = ' . (int) $articleId);
                    }

                    $query -> where('tagsid NOT IN('.implode(',',$tagsIds).')');
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        }
        return true;
    }

    public static function getTagTitlesByArticleId($articleId){
        if($tags = self::getTagsByArticleId($articleId)){
            $tags   = ArrayHelper::getColumn($tags, 'title');
            return array_unique($tags);
        }
        return false;
    }

    public static function getTagByKey($keys = array(), $not = null){

        $storeId    = __METHOD__;
        $storeId   .= '::'.serialize($keys);
        $storeId   .= '::'.serialize($not);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        if($keys && count($keys)){
            $db     = Factory::getContainer()->get(DatabaseInterface::class);
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
                return self::$cache[$storeId] = $tags;
            }
        }
        return false;
    }


    public static function searchTags($filters = array())
    {
        $storeId    = __METHOD__;
        $storeId   .= '::'.serialize($filters);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select('id AS value')
            ->select('title AS text')
            ->select('alias AS path')
            ->from('#__tz_portfolio_plus_tags');

        // Search in title or path
        if (!empty($filters['like']))
        {
            $query->where(
                '(' . $db->quoteName('title') . ' LIKE ' . $db->quote('%' . $filters['like'] . '%')
                . ' OR ' . $db->quoteName('alias') . ' LIKE ' . $db->quote('%' . $filters['like'] . '%') . ')'
            );
        }

        // Filter title
        if (!empty($filters['title']))
        {
            $query->where($db->quoteName('title') . ' = ' . $db->quote($filters['title']));
        }

        // Filter on the published state
        if (isset($filters['published']) && is_numeric($filters['published']))
        {
            $query->where('published = ' . (int) $filters['published']);
        }

        $query->group('id, title, alias');

        // Get the options.
        $db->setQuery($query);

        if($results = $db->loadObjectList()) {
            return self::$cache[$storeId] = $results;
        }

        return array();
    }

    protected static function _insertTagsByTitle($titles){
        if($titles && is_array($titles) && count($titles)>0){
            try {
                $db = Factory::getContainer()->get(DatabaseInterface::class);
                $query = $db->getQuery(true);

                $query->insert('#__tz_portfolio_plus_tags');
                $query->columns('title, alias, published, params, description');

                foreach ($titles as $title) {
                    if (Factory::getConfig()->get('unicodeslugs') == 1) {
                        $alias = OutputFilter::stringURLUnicodeSlug($title);
                    } else {
                        $alias = OutputFilter::stringURLSafe($title);
                    }
                    $query->values($db->quote($title) . ',' . $db->quote($alias) . ', 1, '.$db -> quote('')
                        .', '.$db -> quote(''));
                }
                $db->setQuery($query);

                $db->execute();

                return $db->insertid();
            }catch (\InvalidArgumentException $e)
            {
                self::_setError($e->getMessage());
                return false;
            }
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