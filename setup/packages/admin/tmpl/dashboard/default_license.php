<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    Sonny

# copyright Copyright (C) 2024 tzportfolio.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Family Website: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/


// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

$doc    = Factory::getApplication() -> getDocument();
$params = ComponentHelper::getParams('com_tz_portfolio');
$wa     = $doc -> getWebAssetManager();

$wa -> addInlineScript('
    (function($, Joomla){
       $(document).ready(function(){
           $(".tp-license [data-tp-license-delete]").on("click", function(){
               if(confirm("'.htmlspecialchars(Text::_('COM_TZ_PORTFOLIO_DELETE_LICENSE_CONFIRM')).'")) {
                   $.ajax({
                       type: "POST",
                       url: "index.php?option=com_tz_portfolio",
                       data: {
                           "task": "license.deletelicense",
                           "license": $("[data-source-license]").val()
                       }
                   }).done(function (result) {
                       $("[data-tp-license-loading]").addClass("hide");

                       if (result.state == 400) {
                           Joomla.renderMessages({"error": [result.message]});
                           return false;
                       }

                       if (result.state == 200) {
                           window.location = "index.php?option=com_tz_portfolio";
                       }
                   });
               }
           });
       });
    })(jQuery, Joomla);');
?>
<?php if($license = $this -> license){ ?>
<div class="tp-widget tp-license<?php echo $this -> license?' tp-pro':''; ?>">
    <h4 class="title text-uppercase"><?php echo Text::_('COM_TZ_PORTFOLIO_LICENSE_INFO'); ?></h4>
    <ul class="inside">
        <li class="text-success"><b><?php echo Text::sprintf('COM_TZ_PORTFOLIO_IS_VERSION', Text::_('COM_TZ_PORTFOLIO_PRO')); ?></b></li>
        <li>
            <div class="name"><?php echo Text::_('JGLOBAL_TITLE'); ?>:</div>
            <div class="value"><?php echo $license -> title; ?></div>
        </li>
        <li>
            <div class="name"><?php echo Text::_('COM_TZ_PORTFOLIO_LICENSE'); ?>:</div>
            <div class="value"><?php echo $license -> reference; ?></div></li>
        <li>
            <div class="name"><?php echo Text::_('COM_TZ_PORTFOLIO_DATE_EXPIRY'); ?>:</div>
            <div class="value"><?php echo $license -> expire; ?><?php
                if($license -> isExpired){
                    ?><span class="expired text-danger"><i class="icon-warning"></i><?php
                    echo Text::_('COM_TZ_PORTFOLIO_EXPIRED'); ?></span><?php
                } ?>
            </div>
        </li>
        <li>
            <div class="name"><?php echo Text::_('COM_TZ_PORTFOLIO_SUPPORT_VALID'); ?>:</div>
            <div class="value"><?php echo $license -> support_expire; ?><?php
                if($license -> isSupportExpired){
                ?><span class="expired text-danger"><i class="icon-warning"></i><?php
                    echo Text::_('COM_TZ_PORTFOLIO_EXPIRED'); ?></span><?php
                } ?>
            </div>
        </li>
        <li class="actions">
            <a href="javascript:" class="btn btn-danger btn-large" data-tp-license-delete><i class="tps tp-times"></i> <?php echo Text::_('JACTION_DELETE'); ?></a>
        </li>
    </ul>
</div>
<?php } ?>