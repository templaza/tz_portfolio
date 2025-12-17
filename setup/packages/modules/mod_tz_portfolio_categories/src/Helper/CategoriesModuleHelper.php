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

namespace TemPlaza\Component\TZ_Portfolio\Module\Categories\Site\Helper;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\Application\SiteApplication;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

class CategoriesModuleHelper
{
    public static $cache    = array();

    public function getList(&$params, SiteApplication $app){

        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($params);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }
        $db = Factory::getDbo();
        $categoryName = null;
        $total = null;
        $catIds = null;
        $catIds = $params->get('catid');

        $query = $db->getQuery(true);
        $query->select('a.*');
        $query->select('l.title AS language_title,ag.title AS access_level');
        $query->select('ua.name AS author_name');

        if ($params->get('show_total', 1)) {
            $subQuery   = $db -> getQuery(true);
            $subQuery -> select('COUNT(DISTINCT mc.contentid)');
            $subQuery -> from('#__tz_portfolio_plus_content_category_map AS mc');
            $subQuery -> join('INNER', '#__tz_portfolio_plus_content AS c ON c.id = mc.contentid AND c.state = 1');
            $subQuery -> where('mc.catid = a.id');
            $query -> select('('.(string) $subQuery.') AS total');
        }

        $query->from($db->quoteName('#__tz_portfolio_plus_categories') . ' AS a');
        $query->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');
        $query->join('LEFT', $db->quoteName('#__users') . ' AS uc ON uc.id=a.checked_out');
        $query->join('LEFT', $db->quoteName('#__viewlevels') . ' AS ag ON ag.id = a.access');
        $query->join('LEFT', $db->quoteName('#__users') . ' AS ua ON ua.id = a.created_user_id');
        $query->where('a.published = 1');

        if(!empty($catIds)){
            if(is_array($catIds)) {
                $catIds = array_filter($catIds);
                if(count($catIds)){
                    $query -> where('a.id IN('.implode(',', $catIds).')');
                }
            }else{
                $query -> where('a.id = '.$catIds);
            }
        }

        $query->where('(a.extension = '.$db->quote('com_tz_portfolio').' OR a.extension ='
            .$db -> quote('com_tz_portfolio_plus').')');

        $query->group('a.id');
        $query->order('a.lft ASC');

        $db->setQuery($query);
        if ($items = $db->loadObjectList()) {
            foreach ($items as $item) {
                $item->link = Route::_(RouteHelper::getCategoryRoute($item->id));
                $item -> params = new Registry($item -> params);
            }

            return $items;
        }
        return false;
    }

}
