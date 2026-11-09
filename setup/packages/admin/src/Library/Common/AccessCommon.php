<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library\Common;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Access\Access;

class AccessCommon extends Access{
    public static function getAddOnActions($addon, $group, $section = 'component')
    {
        $actions = self::getActionsFromFile(
            COM_TZ_PORTFOLIO_ADDON_PATH . '/'.$group.'/' . $addon . '/access.xml',
            "/access/section[@name='" . $section . "']/"
        );

        if (empty($actions))
        {
            return array();
        }
        else
        {
            return $actions;
        }
    }
}