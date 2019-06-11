<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusSetupController extends JControllerLegacy{

    protected $default_view = 'dashboard';

    public function completed(){
        if(JFolder::exists(COM_TZ_PORTFOLIO_PLUS_SETUP_PATH)){
            JFolder::delete(COM_TZ_PORTFOLIO_PLUS_SETUP_PATH);
            return true;
        }
    }
}