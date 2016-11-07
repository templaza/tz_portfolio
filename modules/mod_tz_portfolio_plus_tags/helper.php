<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    TemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

JLoader::import('route', JPATH_SITE . '/components/com_tz_portfolio_plus/helpers');

class modTzPortfolioTagsHelper
{
    public static function getList(&$params)
    {
        $db     = JFactory::getDbo();
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
            foreach ($items as $tag) {
                $size = $min_size + (($tag->total - $min_qty) * $step);
                $size = ceil($size);
                $tag->size = $size;
                $tag->link = TZ_Portfolio_PlusHelperRoute::getTagRoute($tag->tagslug, 0, 'auto');
            }
            return $items;
        }
        return false;
    }
}

?>
