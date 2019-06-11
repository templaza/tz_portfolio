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

$path = dirname(dirname(__FILE__));

if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_PATH')) {
    define ('COM_TZ_PORTFOLIO_PLUS_SETUP_PATH', $path);
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_VIEW_PATH')) {
    define ('COM_TZ_PORTFOLIO_PLUS_SETUP_VIEW_PATH', $path.DIRECTORY_SEPARATOR
        .'view');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_URL')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_URL', JURI::base() . 'components/com_tz_portfolio_plus/setup');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_CONTROLLERS')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_CONTROLLERS', $path . '/controllers');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_PACKAGES')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_PACKAGES', $path . '/packages');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_CONFIG')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_CONFIG', $path . '/config');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_LICENCE_PATH', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/includes');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_TMP')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_TMP', $path . '/tmp');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_VERIFY')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_VERIFY', 'https://www.tzportfolio.com/download.html?task=license.verify');
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_ACTIVE')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_ACTIVE', 'https://www.tzportfolio.com/download.html?task=license.active');
}


// Get the current version
$contents = JFile::read(JPATH_ROOT. '/administrator/components/com_tz_portfolio_plus/tz_portfolio_plus.xml');
$parser = simplexml_load_string($contents);

$version = $parser->xpath('version');
$version = (string) $version[0];

if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_HASH')) {
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_HASH', md5($version));
}
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_PACKAGE')) {
    $version    = '2.2.7';
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_PACKAGE', 'com_tz_portfolio_plus_v'.$version.'_component.zip');
}

$configXMLFile  = JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/config.xml';
//if(JFile::exists($configXMLFile)){
if(!defined('COM_TZ_PORTFOLIO_PLUS_SETUP_TOKEN_KEY')) {
    $params = JComponentHelper::getParams('com_tz_portfolio_plus');
    define('COM_TZ_PORTFOLIO_PLUS_SETUP_TOKEN_KEY', $input -> get('token_key')?$input -> get('token_key'):$params->get('token_key', ''));
}
//}