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
use Joomla\Utilities\ArrayHelper;

class GroupsHelper{

    protected static $cache	= array();
    
    public static function getGroups($option = null){
        $storeId    = __METHOD__;
        if($option){
            if(is_array($option)) {
                $storeId .= '::'.implode(',', $option);
            }else{
                $storeId    .= '::'.$option;
            }
        }

        if(!isset(self::$cache[$storeId])){

            $user       = Factory::getUser();
            $viewlevels = ArrayHelper::toInteger($user->getAuthorisedViewLevels());
            $db         = Factory::getDbo();
            $query      = $db -> getQuery(true);

            $query -> select('*');
            $query -> from('#__tz_portfolio_plus_fieldgroups');
            $query -> where('access IN (' . implode(',', $viewlevels) . ')');
            if($option) {
                if(isset($option['filter.published'])) {
                    if($option['filter.published']) {
                        $query->where('published = 1');
                    }else{
                        $query->where('published = 0');
                    }
                }
            }
            $db -> setQuery($query);
            if($data = $db -> loadObjectList()){
                self::$cache[$storeId]  = $data;
                return $data;
            }
            self::$cache[$storeId]  = false;
        }
        return self::$cache[$storeId];
    }
}