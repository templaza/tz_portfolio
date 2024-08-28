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

// no direct access
defined('_JEXEC') or die;

use Joomla\Filesystem\Folder;
use Joomla\CMS\MVC\Controller\BaseController;

class TZ_PortfolioSetupController extends BaseController {

    protected $default_view = 'dashboard';

    public function completed(){
        if(is_dir(COM_TZ_PORTFOLIO_SETUP_PATH)){
            Folder::delete(COM_TZ_PORTFOLIO_SETUP_PATH);
            return true;
        }
    }
}