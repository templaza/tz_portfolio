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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

$displayData = [
    'textPrefix' => 'COM_TZ_PORTFOLIO',
    'formURL'    => 'index.php?option=com_tz_portfolio&view=addons',
    'createURL'  => 'index.php?option=com_tz_portfolio&task=addon.upload',
    'helpURL'    => 'https://www.tzportfolio.com/document/add-ons/28-installation.html',
    'icon'       => 'icon-copy article',
];

$user = $this->getCurrentUser();

if ($user->authorise('core.create', 'com_content') || count($user->getAuthorisedCategories('com_tz_portfolio', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_tz_portfolio&task=addon.add';
}

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this -> document -> getWebAssetManager();

$wa -> useStyle('com_tz_portfolio.introjs');

if($lang -> isRtl()){
    $wa -> useStyle('com_tz_portfolio.intro-rtl');
}

$wa -> addInlineScript('(function($){
    "use strict";
    $(document).ready(function(){
    
        var addonSteps  = [
        {
            /* Step 1: Install */
            element: $("#toolbar-new > button")[0],
            title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INSTALL_UPDATE')). '",            
            intro: "' . $this->escape(Text::sprintf('COM_TZ_PORTFOLIO_INTRO_GUIDE_INSTALL_MANUAL_ONLINE_DESC',
        Text::_('COM_TZ_PORTFOLIO_ADDON'))) . '",
            position: "right"
        }];
    
        tppIntroGuide("'.$this -> getName().'",addonSteps , '
    .(TZ_PortfolioHelper::introGuideSkipped($this -> getName())?1:0).', "'.Session::getFormToken().'");
    });
})(jQuery);', ['position' => 'after'], [], ['com_tz_portfolio.introguide']);

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
