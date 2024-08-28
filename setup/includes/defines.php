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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;

$input  = Factory::getApplication() -> input;
$path = dirname(dirname(__FILE__));

if(!defined('COM_TZ_PORTFOLIO_SETUP_PATH')) {
    define ('COM_TZ_PORTFOLIO_SETUP_PATH', $path);
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_VIEW_PATH')) {
    define ('COM_TZ_PORTFOLIO_SETUP_VIEW_PATH', $path.DIRECTORY_SEPARATOR
        .'view');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_URL')) {
    define('COM_TZ_PORTFOLIO_SETUP_URL', Uri::base() . 'components/com_tz_portfolio/setup');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_CONTROLLERS')) {
    define('COM_TZ_PORTFOLIO_SETUP_CONTROLLERS', $path . '/controllers');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_PACKAGES')) {
    define('COM_TZ_PORTFOLIO_SETUP_PACKAGES', $path . '/packages');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_CONFIG')) {
    define('COM_TZ_PORTFOLIO_SETUP_CONFIG', $path . '/config');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH')) {
    define('COM_TZ_PORTFOLIO_SETUP_LICENCE_PATH', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/includes');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_TMP')) {
    define('COM_TZ_PORTFOLIO_SETUP_TMP', $path . '/tmp');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_VERIFY')) {
    define('COM_TZ_PORTFOLIO_SETUP_VERIFY', 'https://www.tzportfolio.com/download.html?task=license.verify');
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_ACTIVE')) {
    define('COM_TZ_PORTFOLIO_SETUP_ACTIVE', 'https://www.tzportfolio.com/download.html?task=license.active');
}


// Get the current version
$contents = file_get_contents(JPATH_ROOT. '/administrator/components/com_tz_portfolio/tz_portfolio.xml');
$parser = simplexml_load_string($contents);

$version = $parser->xpath('version');
$version = (string) $version[0];

if(!defined('COM_TZ_PORTFOLIO_SETUP_HASH')) {
    define('COM_TZ_PORTFOLIO_SETUP_HASH', md5($version));
}
if(!defined('COM_TZ_PORTFOLIO_SETUP_PACKAGE')) {
    define('COM_TZ_PORTFOLIO_SETUP_PACKAGE', 'com_tz_portfolio_v'.$version.'.zip');
}

$configXMLFile  = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio/config.xml';

if(!defined('COM_TZ_PORTFOLIO_SETUP_TOKEN_KEY')) {
    $params = ComponentHelper::getParams('com_tz_portfolio');
    define('COM_TZ_PORTFOLIO_SETUP_TOKEN_KEY', $input -> get('token_key')?$input -> get('token_key'):$params->get('token_key', ''));
}