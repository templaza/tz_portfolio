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

$list = modTZ_Portfolio_PlusArticlesHelper::getList($params);
$categories = modTZ_Portfolio_PlusArticlesHelper::getCategoriesByArticle($params);
$tags = modTZ_Portfolio_PlusArticlesHelper::getTagsByArticle($params);
$show_filter = $params->get('show_filter',1);
if($show_filter) {
    $filter_tag = modTZ_Portfolio_PlusArticlesHelper::getTagsByCategory($params);
}
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_tz_portfolio_plus_articles', $params->get('layout', 'default'));
