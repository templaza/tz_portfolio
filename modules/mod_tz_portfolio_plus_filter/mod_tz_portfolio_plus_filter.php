<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2016 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Family Website: http://www.templaza.com

# Technical Support:  Forum - http://tzportfolio.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$upper_limit        = $lang->getUpperLimitSearchWord();
$button             = $params->get('button', 0);
$imagebutton        = $params->get('imagebutton', 0);
$button_pos         = $params->get('button_pos', 'left');
$button_text        = htmlspecialchars($params->get('button_text', JText::_('MOD_TZ_PORTFOLIO_PLUS_FILTER_SEARCHBUTTON_TEXT')), ENT_COMPAT, 'UTF-8');
$width              = (int) $params->get('width');
$maxlength          = $upper_limit;
$text               = htmlspecialchars($params->get('text', JText::_('MOD_TZ_PORTFOLIO_PLUS_FILTER_SEARCHBOX_TEXT')), ENT_COMPAT, 'UTF-8');
$label              = htmlspecialchars($params->get('label', JText::_('MOD_TZ_PORTFOLIO_PLUS_FILTER_LABEL_TEXT')), ENT_COMPAT, 'UTF-8');
$set_Itemid         = (int) $params->get('set_itemid', 0);
$moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

$mitemid            = $set_Itemid > 0 ? $set_Itemid : $app->input->get('Itemid');
$advfilter          = modTZ_Portfolio_PlusFilterHelper::getAdvFilterFields($params);
$categoryOptions    = modTZ_Portfolio_PlusFilterHelper::getCategoriesOptions($params);

require JModuleHelper::getLayoutPath('mod_tz_portfolio_plus_filter', $params->get('layout', 'default'));