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

namespace TemPlaza\Component\TZ_Portfolio\Module\ArticlesArchive\Site\Helper;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Application\SiteApplication;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

class ArticlesArchiveHelper
{
    public static $cache    = array();

    public function getList(&$params, SiteApplication $app){

        $storeId    = __METHOD__;
        $storeId   .= ':'.serialize($params);
        $storeId    = md5($storeId);

        if(isset(self::$cache[$storeId])){
            return self::$cache[$storeId];
        }
        
        //get database
        $db         = Factory::getDbo();
        $query      = $db->getQuery(true);
        $subQuery   = $db->getQuery(true);

        $query->select('MONTH(created) AS created_month, created, id, title, YEAR(created) AS created_year');
        $query->from('#__tz_portfolio_plus_content');
        $query->where('checked_out = 0');
        $query->where('state = 1');
        $query->group('created_year DESC, created_month DESC');


        $subQuery->select('COUNT(*)');
        $subQuery->from('#__tz_portfolio_plus_content');
        $subQuery->where('checked_out = 0');
        $subQuery->where('MONTH(created) = created_month AND YEAR(created) = created_year');
        $subQuery->where('state = 1');
        $query->select('(' . $subQuery->__toString() . ') AS total');

        // Filter by language
        if (Factory::getApplication()->getLanguageFilter()) {
            $query->where('language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
        }

        $db->setQuery($query, 0, $params->get('count'));
        $rows = (array)$db->loadObjectList();

        $i = 0;
        $lists = array();
        if ($rows) {
            foreach ($rows as $row) {
                $date = Factory::getDate($row->created);

                $created_month = $date->format('n');
                $created_year = $date->format('Y');

                $created_year_cal = HTMLHelper::_('date', $row->created, 'Y');
                $month_name_cal = HTMLHelper::_('date', $row->created, 'F');

                $lists[$i] = new \stdClass;

                $lists[$i] -> link  = RouteHelper::getDateRoute($created_year, $created_month, 0, $params -> get('tzmenuitem'));
                $lists[$i]->text = Text::sprintf('MOD_TZ_PORTFOLIO_ARTICLES_ARCHIVE_DATE', $month_name_cal, $created_year_cal);

                $lists[$i]->total = 0;
                if (isset($row->total)) {
                    $lists[$i]->total = $row->total;
                }
                $i++;
            }
        }

        if(!empty($lists)){
            return static::$cache[$storeId] = $lists;
        }

        return $lists;
    }

}
