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

class TZ_Portfolio_PlusHelperAddon_Datas{
    public static function getRootURL($addon_id,$root_view = 'addon_datas'){
        if($addon_id){
            return 'index.php?option=com_tz_portfolio_plus&view='.$root_view.'&addon_id='.$addon_id;
        }
        return false;
    }
}