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

use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusContentHelper{
    protected static $cache = array();

    public static function getArticleById($article_id, $resetCache = false, $articleObject = null)
    {
        if (!$article_id) {
            return null;
        }

        $storeId = md5(__METHOD__ . "::" . $article_id);
        if (!isset(self::$cache[$storeId]) || $resetCache) {

            if (!is_object($articleObject)) {
                $db     = TZ_Portfolio_PlusDatabase::getDbo();
                $query  = $db->getQuery(true);
                $query  -> select('article.*, m.catid');
                $query  -> from('#__tz_portfolio_plus_content AS article');
                $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = article.id');
                $query  -> join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
                $query  -> where('article.id = ' . $article_id);
                $query  -> where('m.main = 1');
                $db     -> setQuery($query);

                $articleObject = $db->loadObject();
            }

            if ($articleObject && $articleObject->catid > 0) {
                self::$cache[$storeId] = $articleObject;
            } else {
                return $articleObject;
            }
        }

        return self::$cache[$storeId];
    }

    public static function getLetters($filters = array()){

        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($filters);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = JFactory::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('DISTINCT ASCII(SUBSTR(LOWER(c.title),1,1)) AS letterKey');
        $query -> from('#__tz_portfolio_plus_content AS c');
        $query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = c.id');
        $query -> join('LEFT', '#__tz_portfolio_plus_categories AS cc ON cc.id=m.catid');
        $query -> join('LEFT', '#__tz_portfolio_plus_tag_content_map AS x ON x.contentid=c.id');
        $query -> join('LEFT', '#__tz_portfolio_plus_tags AS t ON t.id=x.tagsid');
        $query -> join('LEFT', '#__users AS u ON c.created_by=u.id');

        $query -> where('c.state=1');

        if(isset($filters['catid']) && ($catids = $filters['catid'])){

            if(is_array($catids)){
                $catids = array_filter($catids);
                if(count($catids)) {
                    $query->where('cc.id IN(' . implode(',', $catids) . ')');
                }
            }else{
                $query -> where('cc.id ='.(int) $catids);
            }
        }

        if(isset($filters['featured']) && ($featured = $filters['featured'])){
            if(is_array($featured)){
                $query -> where('c.featured IN('.implode(',',$featured).')');
            }else{
                $query -> where('c.featured ='.(int) $featured);
            }
        }

        if(isset($filters['tagId']) && ($tagId = $filters['tagId'])){
            if(is_array($tagId)){
                $query -> where('t.id IN('.implode(',',$tagId).')');
            }else{
                $query -> where('t.id ='.(int) $tagId);
            }
        }

        if(isset($filters['userId']) && ($userId = $filters['userId'])){
            if(is_array($userId)){
                $query -> where('c.created_by IN('.implode(',',$userId).')');
            }else{
                $query -> where('c.created_by ='.(int) $userId);
            }
        }
        if(isset($filters['year']) && ($year = $filters['year'])){
            $query -> where('YEAR(c.created) ='.$year);
        }
        if(isset($filters['month']) && ($month = $filters['month'])){
            $query -> where('MONTH(c.created) ='.$month);
        }

        $db -> setQuery($query);

        if($result = $db -> loadColumn()){
            self::$cache[$storeId]   = $result;
            return $result;
        }
        return false;
    }


    public static function getArticleCountsByAuthorId($authorId, $options = array())
    {
        if (!$authorId) {
            return null;
        }

        $storeId = __METHOD__ . ':' . $authorId;
        $storeId .= ':'.serialize($options);
        $storeId = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db->getQuery(true);
        $query  -> select('COUNT(article.id)');
        $query  -> from('#__tz_portfolio_plus_content AS article');
        $query  -> join('INNER', '#__tz_portfolio_plus_content_category_map AS m ON m.contentid = article.id');
        $query  -> join('LEFT', '#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
        $query  -> join('INNER', '#__users AS ua ON ua.id = article.created_by');

        $query -> where('article.created_by ='.$authorId);

        if(isset($options['filter.published'])) {
            if(is_array($options['filter.published'])){
                $query->where('article.state IN('.implode(',', $options['filter.published']).')');
            }else{
                $query->where('article.state = ' . (int) $options['filter.published']);
            }
        }
        $db     -> setQuery($query);

        if($count = $db->loadResult()){
            self::$cache[$storeId]  = (int) $count;
            return (int) $count;
        }

        return false;
    }

    public static function getBootstrapColumns($numOfColumns)
    {
        switch ($numOfColumns)
        {
            case 1:
                return array(12);
                break;
            case 2:
                return array(6, 6);
                break;
            case 3:
                return array(4, 4, 4);
                break;
            case 4:
                return array(3, 3, 3, 3);
                break;
            case 5:
                return array(3, 3, 2, 2, 2);
                break;
            case 6:
                return array(2, 2, 2, 2, 2, 2);
                break;
            case 7:
                return array(2, 2, 2, 2, 2, 1, 1);
                break;
            case 8:
                return array(2, 2, 2, 2, 1, 1, 1, 1);
                break;
            case 9:
                return array(2, 2, 2, 1, 1, 1, 1, 1, 1);
                break;
            case 10:
                return array(2, 2, 1, 1, 1, 1, 1, 1, 1, 1);
                break;
            case 11:
                return array(2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
                break;
            case 12:
                return array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
                break;
            default:
                return array(12);
                break;
        }
    }
}