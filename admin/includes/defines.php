<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;


define ('COM_TZ_PORTFOLIO_PLUS_JVERSION_COMPARE', version_compare(JVERSION,'3.0','ge'));

if(!DIRECTORY_SEPARATOR){
    define('DIRECTORY_SEPARATOR',DS);
}

define ('COM_TZ_PORTFOLIO_PLUS','com_tz_portfolio_plus');
define ('COM_TZ_PORTFOLIO_PLUS_PATH_SITE',JPATH_SITE.'/components/com_tz_portfolio_plus');
define ('COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR
        .'components'.DIRECTORY_SEPARATOR.COM_TZ_PORTFOLIO_PLUS);
define ('COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'helpers');
define ('COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH', COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');
define ('COM_TZ_PORTFOLIO_PLUS_LIBRARIES', COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'libraries');
define ('COM_TZ_PORTFOLIO_PLUS_MEDIA_BASE', JPATH_ROOT . '/media/tz_portfolio_plus');
define ('COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE', 'media/tz_portfolio_plus/article/cache');
define ('COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_ROOT', JPATH_ROOT . DIRECTORY_SEPARATOR.COM_TZ_PORTFOLIO_PLUS_MEDIA_ARTICLE_BASE);
define ('COM_TZ_PORTFOLIO_PLUS_MEDIA_BASEURL', JURI::root() . 'media/tz_portfolio_plus/article/cache');
define ('COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH',COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'templates');
define('COM_TZ_PORTFOLIO_PLUS_ADDON_PATH', COM_TZ_PORTFOLIO_PLUS_PATH_SITE.DIRECTORY_SEPARATOR.'addons');

if(file_exists(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/tz_portfolio_plus.xml')){
    define('COM_TZ_PORTFOLIO_PLUS_VERSION',JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/tz_portfolio_plus.xml')->version);
}elseif(file_exists(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/manifest.xml')){
    define('COM_TZ_PORTFOLIO_PLUS_VERSION',JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/manifest.xml')->version);
}