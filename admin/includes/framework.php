<?php
/*------------------------------------------------------------------------

# JVisualContent Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

// Require defines.php file
JLoader::import('com_tz_portfolio_plus.includes.defines',JPATH_ADMINISTRATOR.'/components');

// Require tzportfolioplus file with some functions php
JLoader::import('com_tz_portfolio_plus.libraries.tzportfolioplus',JPATH_ADMINISTRATOR.'/components');

// Require uri files
if(!class_exists('TZ_Portfolio_PlusUri')){
    JLoader::import('com_tz_portfolio_plus.libraries.uri',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');
}