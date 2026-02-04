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

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Library;

// No direct access
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class TZ_PortfolioUri extends Uri {
    protected static $jsc_path = 'components/com_tz_portfolio';

    public static function base($pathonly = false,$admin=false)
    {
        $base       = parent::base($pathonly);
        if($pathonly){
            $base   .= '/'.self::$jsc_path;
        }else{
            $base   .= self::$jsc_path;
        }
        return $base;
    }

    public static function root($pathonly = false, $path = null,$admin=false)
    {
        $_path  = $path;
        if(!is_null($_path)){
            $_path   = trim($path);
            if(empty($_path)){
                $_path   = null;
            }
        }
        $root   = parent::root($pathonly,$_path);
        $jsc_path   = self::$jsc_path;
        if($admin){
            $jsc_path   = 'administrator/'.self::$jsc_path;
        }
        if($pathonly){
            $root   .= '/'.$jsc_path;
        }else{
            $root   .= $jsc_path;
        }
        return $root;
    }
}