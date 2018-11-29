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

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

JLoader::import('com_tz_portfolio_plus.libraries.helper.modulehelper', JPATH_ADMINISTRATOR.'/components');

JHtml::_('jquery.framework');

$doc    = JFactory::getDocument();
$doc -> addScript(TZ_Portfolio_PlusUri::root(true).'/js/core.min.js');

$list = modTZ_Portfolio_PlusPortfolioHelper::getList($params, $module);
$categories = modTZ_Portfolio_PlusPortfolioHelper::getCategoriesByArticle($params);
$tags = modTZ_Portfolio_PlusPortfolioHelper::getTagsByArticle($params);
$show_filter = $params->get('show_filter',1);
if($show_filter) {
    $filter_tag = modTZ_Portfolio_PlusPortfolioHelper::getTagsFilterByArticle($params);
    $filter_cat = modTZ_Portfolio_PlusPortfolioHelper::getCategoriesFilterByArticle($params);
}
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require TZ_Portfolio_PlusModuleHelper::getTZLayoutPath($module, $params->get('layout', 'default'));
