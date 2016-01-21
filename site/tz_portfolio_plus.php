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
defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

//require_once JPATH_COMPONENT.'/helpers/route.php';
//require_once JPATH_COMPONENT.'/helpers/query.php';

JLoader::import('framework',JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/includes');

JLoader::import('template', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/libraries');
JLoader::import('controller', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/libraries');
JLoader::import('view', JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/libraries');
tzportfolioplusimport('plugin.helper');
JLoader::import('route', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);
JLoader::import('query', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);

// Include helpers file
JLoader::import('categories', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);
JLoader::import('tags', COM_TZ_PORTFOLIO_PLUS_SITE_HELPERS_PATH);

$controller = JControllerLegacy::getInstance('TZ_Portfolio_Plus');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
