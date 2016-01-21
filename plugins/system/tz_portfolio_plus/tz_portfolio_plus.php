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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
JLoader::register('TZ_Portfolio_PlusPlugin',JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'
                    .DIRECTORY_SEPARATOR.'com_tz_portfolio_plus'.DIRECTORY_SEPARATOR.'libraries'
                    .DIRECTORY_SEPARATOR.'plugin'.DIRECTORY_SEPARATOR.'plugin.php');

class PlgSystemTZ_Portfolio_Plus extends JPlugin {

    public function __construct(&$subject, $config = array())
    {
        JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

        JLoader::import('com_tz_portfolio_plus.libraries.plugin.helper', JPATH_ADMINISTRATOR.'/components');

        parent::__construct($subject,$config);
    }

    public function onAfterRoute(){
        if(class_exists('TZ_Portfolio_PlusPluginHelper')) {
            TZ_Portfolio_PlusPluginHelper::importPlugin('mediatype');
            TZ_Portfolio_PlusPluginHelper::importPlugin('content');
            TZ_Portfolio_PlusPluginHelper::importPlugin('user');
        }

    }
}
?>