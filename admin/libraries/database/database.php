<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TZ_Portfolio_Plus\Database;

// no direct access
defined('_JEXEC') or die;


class TZ_Portfolio_PlusDatabase{

    protected static $cache    = array();

    /* Get database class from application
    *  @var $fullGroup: Disable full group by of Joomla 4.
    */
    public static function getDbo($fullGroup = false){

        $storeId    = md5(__METHOD__.':'.$fullGroup);
        $query      = false;

        if(isset(self::$cache[$storeId])){
            $query = self::$cache[$storeId];
        }

        $db    = \JFactory::getDbo();
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE && !$fullGroup && !$query){
            $db->setQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            self::$cache[$storeId]  = $db->execute();
        }

        return $db;
    }
}