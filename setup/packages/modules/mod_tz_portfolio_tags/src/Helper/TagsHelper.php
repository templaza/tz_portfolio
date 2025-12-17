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

namespace TemPlaza\Component\TZ_Portfolio\Module\Tags\Site\Helper;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Application\SiteApplication;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

class TagsHelper
{
    public static $cache    = array();

    public function getList(&$params, SiteApplication $app){

        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($params);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = Factory::getDbo();
        $query = $db->getQuery(true);
        $catid = $params -> get('catid', array());

        $query -> select(' count(t.id) as total, t.*');
        $query -> select('CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(":", t.id, t.alias) ELSE t.id END as tagslug');

        $query -> from('#__tz_portfolio_plus_tags AS t');
        $query -> where('t.published = 1');

        $query -> join('LEFT', '#__tz_portfolio_plus_tag_content_map AS tm ON tm.tagsid = t.id');

        $query -> join('LEFT', '#__tz_portfolio_plus_content AS c ON (tm.contentid = c.id)');
        $query -> where('c.state = 1');

        $query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS cm ON (cm.contentid = c.id)');
        $query -> join('LEFT', '#__tz_portfolio_plus_categories AS cc ON cc.id = cm.catid');
        $query -> where('cc.published = 1');

        $subQuery   = $db -> getQuery(true);
        $subQuery -> select('COUNT(DISTINCT c2.id)');
        $subQuery -> from('#__tz_portfolio_plus_content AS c2');
        $subQuery -> join('INNER', '#__tz_portfolio_plus_tag_content_map AS tm2 ON tm2.contentid = c2.id');
        $subQuery -> join('INNER', '#__tz_portfolio_plus_tags AS t2 ON tm2.tagsid = t2.id');
        $subQuery -> where('t2.id = t.id');
        $query -> select('('.(string) $subQuery.') AS article_count');

        if(is_array($catid)){
            $catid  = array_filter($catid);
            if(count($catid)) {
                $query->where('cm.catid IN (' . implode(',', $catid) . ')');
            }
        }else{
            $query -> where('cm.catid = '.$catid);
        }
        $query -> group('t.alias');

        $db -> setQuery($query, 0, $params->get('tag_limit'));

        if ($items = $db->loadObjectList()) {
            foreach ($items as $item) {
                $cloud[] = $item->total;
            }
            $max_size = $params->get('maxfont', 300);
            $min_size = $params->get('minfont', 75);
            $max_qty = max(array_values($cloud));
            $min_qty = min(array_values($cloud));
            $spread = $max_qty - $min_qty;
            if (0 == $spread) {
                $spread = 1;
            }
            $step = ($max_size - $min_size) / ($spread);
            foreach ($items as &$tag) {
                $size = $min_size + (($tag->total - $min_qty) * $step);
                $size = ceil($size);
                $tag->size = $size;
                $tag->link = RouteHelper::getTagRoute($tag->tagslug, 0, $params -> get('menu_active', 'auto'));
                $tag -> link    = Route::_($tag -> link);
            }
            self::$cache[$storeId]  = $items;
            return $items;
        }
        return false;
    }

    public function getArticleTotal(){
        $storeId    = __METHOD__;
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }

        $db     = Factory::getDbo();
        $query = $db->getQuery(true);
        $query -> select('COUNT(DISTINCT c.id)');

        $query -> from('#__tz_portfolio_plus_content AS c');
        $query -> join('INNER', '#__tz_portfolio_plus_tag_content_map AS tm ON tm.contentid = c.id');
        $query -> join('INNER', '#__tz_portfolio_plus_tags AS t ON tm.tagsid = t.id AND c.state = 1');
        $query -> join('LEFT', '#__tz_portfolio_plus_content_category_map AS cm ON cm.contentid = c.id');
        $query -> join('INNER', '#__tz_portfolio_plus_categories AS cc ON cc.id = cm.catid AND cc.published = 1');

        $query -> where('t.published = 1');

        $db -> setQuery($query);

        if($total = $db -> loadResult()) {
            self::$cache[$storeId] = $total;
            return $total;
        }

        return 0;
    }

}
