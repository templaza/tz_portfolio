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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_tz_portfolio_plus')) {
    throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$input			= JFactory::getApplication() -> input;
$option         = $input -> getCmd('option','com_tz_portfolio_plus');
$view           = $input -> getCmd('view','dashboard');
$task           = $input -> getCmd('task',null);

JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

// Register helper class
JLoader::register('TZ_Portfolio_PlusHelper', dirname(__FILE__) . '/helpers/tz_portfolio_plus.php');


$controller	= JControllerLegacy::getInstance('TZ_Portfolio_Plus');

$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
