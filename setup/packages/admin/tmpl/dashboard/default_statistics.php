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
use Joomla\CMS\HTML\HTMLHelper;

$adoTotal       = 0;
$stlTotal       = 0;
$stlInstTotal   = 0;
$adoInstTotal   = 0;
$adosUpdateTotal= 0;
$stlsUpdateTotal= 0;

$wa = $this -> document -> getWebAssetManager();
$wa -> addInlineScript('(function($){
        "use strict";
        $(document).ready(function(){
           $.ajax({
               "url": "index.php?option=com_tz_portfolio",
               "type": "POST",
               "dataType": "json",
               "data": {
                   "task": "dashboard.statistics"
               },
               success: function(result){
                   if(result && result.success && result.data) {
                       var statistcs = result.data,
                           tpStatistic  = $(".tp-statistic");

                       tpStatistic.find("[data-addon-total]").html(statistcs.addons.total);
                       tpStatistic.find("[data-addon-update]").html(statistcs.addons.update);
                       tpStatistic.find("[data-addon-installed]").html(statistcs.addons.installed);

                       tpStatistic.find("[data-style-total]").html(statistcs.styles.total);
                       tpStatistic.find("[data-style-update]").html(statistcs.styles.update);
                       tpStatistic.find("[data-style-installed]").html(statistcs.styles.installed);

                       tpStatistic.find("[data-statistic-checking]").hide();
                   }
               }
           });
        });
    })(jQuery);');
?>
<div class="tp-statistic">
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
        <div class="col-md-6">
            <div class="tp-widget">
                <h4 class="title text-uppercase"><?php echo Text::_('COM_TZ_PORTFOLIO_ADDON_STATISTICS'); ?>
                    <small class="small" data-statistic-checking>
                        <i class="tps tp-circle-notch tp-spin"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_CHECKING');?>...</small></h4>
                <ul class="inside">
                    <li>
                        <span class="name"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_STATISTIC_TOTAL',
                                Text::_('COM_TZ_PORTFOLIO_ADDONS'))?>:</span>
                        <span class="value badge badge-info bg-info rounded" data-addon-total>0</span>
                    </li>
                    <li>
                        <span class="name"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_STATISTIC_TOTAL_INSTALLED',
                                Text::_('COM_TZ_PORTFOLIO_ADDONS'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio&view=addons" class="value badge badge-success bg-success rounded" data-addon-installed>0</a>
                    </li>
                    <li>
                        <span class="name"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_STATISTIC_TOTAL_NEED_UPDATE',
                                Text::_('COM_TZ_PORTFOLIO_ADDONS'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio&view=addon&layout=upload" class="value badge badge-important bg-danger rounded" data-addon-update>0</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="tp-widget">
                <h4 class="title text-uppercase"><?php echo Text::_('COM_TZ_PORTFOLIO_STYLE_STATISTICS'); ?>
                    <small class="small" data-statistic-checking>
                        <i class="tps tp-circle-notch tp-spin"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_CHECKING');?>...</small>
                </h4>
                <ul class="inside">
                    <li>
                        <span class="name"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_STATISTIC_TOTAL',
                                Text::_('COM_TZ_PORTFOLIO_TEMPLATES'))?>:</span>
                        <span class="value badge badge-info bg-info rounded" data-style-total>0</span>
                    </li>
                    <li>
                        <span class="name"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_STATISTIC_TOTAL_INSTALLED',
                                Text::_('COM_TZ_PORTFOLIO_TEMPLATES'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio&view=templates" class="value badge badge-success bg-success rounded" data-style-installed>0</a>
                    </li>
                    <li>
                        <span class="name"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_STATISTIC_TOTAL_NEED_UPDATE',
                                Text::_('COM_TZ_PORTFOLIO_TEMPLATES'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio&view=template&layout=upload" class="value badge badge-important badge-danger bg-danger rounded" data-style-update>0</a>
                    </li>
                </ul>
            </div>
        </div>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</div>


