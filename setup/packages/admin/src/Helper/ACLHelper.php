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

class ACLHelper
{
	protected static $cache         = array();

	public static function allowApprove($article = null, $option = 'com_tz_portfolio'){

        $user       = Factory::getUser();

        if($user->authorise('core.approve', $option)){
            if($article && $article -> id && $article -> created_by != $user -> id){
                return true;
            }elseif($article && $article -> id && $article -> created_by == $user -> id
                && $article -> state != 4){
                return true;
            }elseif (!$article || ($article && !$article -> id)){
                return true;
            }
        }

        return false;
    }
}
