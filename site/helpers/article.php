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
                $db     = JFactory::getDbo();
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