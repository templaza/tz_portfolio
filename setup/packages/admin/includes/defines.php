<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use \TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

if(!defined('COM_TZ_PORTFOLIO_JVERSION_4_COMPARE')) {
    define('COM_TZ_PORTFOLIO_JVERSION_4_COMPARE', version_compare(JVERSION, '4.0', 'ge'));
}
if(!DIRECTORY_SEPARATOR){
    define('DIRECTORY_SEPARATOR',DS);
}
if(!defined('COM_TZ_PORTFOLIO')) {
    define('COM_TZ_PORTFOLIO', 'com_tz_portfolio');
}
if(!defined('COM_TZ_PORTFOLIO_PATH_SITE')) {
    define('COM_TZ_PORTFOLIO_PATH_SITE', JPATH_SITE . '/components/com_tz_portfolio');
}
if(!defined('COM_TZ_PORTFOLIO_ADMIN_PATH')) {
    define ('COM_TZ_PORTFOLIO_ADMIN_PATH', JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR
        .'components'.DIRECTORY_SEPARATOR.COM_TZ_PORTFOLIO);
}
if(!defined('COM_TZ_PORTFOLIO_ADMIN_HELPERS_PATH')) {
    define ('COM_TZ_PORTFOLIO_ADMIN_HELPERS_PATH', COM_TZ_PORTFOLIO_ADMIN_PATH.DIRECTORY_SEPARATOR.'helpers');
}
if(!defined('COM_TZ_PORTFOLIO_SITE_HELPERS_PATH')) {
    define ('COM_TZ_PORTFOLIO_SITE_HELPERS_PATH', COM_TZ_PORTFOLIO_PATH_SITE.DIRECTORY_SEPARATOR.'helpers');
}
if(!defined('COM_TZ_PORTFOLIO_ADMIN_LAYOUTS')) {
    define ('COM_TZ_PORTFOLIO_ADMIN_LAYOUTS', COM_TZ_PORTFOLIO_ADMIN_PATH.DIRECTORY_SEPARATOR.'layouts');
}
if(!defined('COM_TZ_PORTFOLIO_LIBRARIES')) {
    define ('COM_TZ_PORTFOLIO_LIBRARIES', COM_TZ_PORTFOLIO_ADMIN_PATH.DIRECTORY_SEPARATOR.'libraries');
}
if(!defined('COM_TZ_PORTFOLIO_MEDIA_BASE')) {
    define ('COM_TZ_PORTFOLIO_MEDIA_BASE', 'media/com_tz_portfolio');
}
if(!defined('COM_TZ_PORTFOLIO_MEDIA_PATH')) {
    define ('COM_TZ_PORTFOLIO_MEDIA_PATH', JPATH_ROOT . '/media/com_tz_portfolio');
}
if(!defined('COM_TZ_PORTFOLIO_IMAGES_BASE')) {
    define ('COM_TZ_PORTFOLIO_IMAGES_BASE', 'images/tz_portfolio');
}
if(!defined('COM_TZ_PORTFOLIO_IMAGES_PATH')) {
    define ('COM_TZ_PORTFOLIO_IMAGES_PATH', JPATH_ROOT .'/'. COM_TZ_PORTFOLIO_IMAGES_BASE);
}
if(!defined('COM_TZ_PORTFOLIO_STYLE_PATH')) {
    define ('COM_TZ_PORTFOLIO_STYLE_PATH',COM_TZ_PORTFOLIO_PATH_SITE.DIRECTORY_SEPARATOR.'styles');
}
if(!defined('COM_TZ_PORTFOLIO_ADDON_PATH')) {
    define('COM_TZ_PORTFOLIO_ADDON_PATH', COM_TZ_PORTFOLIO_PATH_SITE.DIRECTORY_SEPARATOR.'add-ons');
}
if(!defined('COM_TZ_PORTFOLIO_ACL_SECTIONS')) {
    define('COM_TZ_PORTFOLIO_ACL_SECTIONS', json_encode(array('category', 'group', 'tag', 'addon', 'template', 'style', 'extension')));
}
if(!defined('COM_TZ_PORTFOLIO_EDITION')) {
    $license    = TZ_PortfolioHelper::getLicense();
    if(file_exists(COM_TZ_PORTFOLIO_ADMIN_PATH.'/includes/license.php')
        && $license && isset($license -> reference) && $license -> reference) {
        define('COM_TZ_PORTFOLIO_EDITION', 'commercial');
    }else{
        define('COM_TZ_PORTFOLIO_EDITION', 'free');
    }
}
/* since v2.2.7 */
if(!defined('COM_TZ_PORTFOLIO_VERIFY_LICENSE')) {
    define('COM_TZ_PORTFOLIO_VERIFY_LICENSE', 'https://www.tzportfolio.com/download.html?task=license.verify');
}
if(!defined('COM_TZ_PORTFOLIO_ACTIVE_LICENSE')) {
    define('COM_TZ_PORTFOLIO_ACTIVE_LICENSE', 'https://www.tzportfolio.com/download.html?task=license.active');
}