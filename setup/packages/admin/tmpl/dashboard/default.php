<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    Sonny

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

$user   = Factory::getUser();
$date   = Factory::getDate();
$lang   = Factory::getApplication() -> getLanguage();
$xml    = $this -> info;

$tppIntroGuide  = '[{
                    /* Step 1: Video tutorial */
                    element: $("#toolbar-youtube")[0],
                    title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_VIDEO_TUTORIALS')). '",
                    intro: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_VIDEO_TUTORIALS_DESC')) . '",
                    position: "left"
                },
                {
                    /* Step 2: Document */
                    element: $("#toolbar-help")[0],
                    title: "' . $this->escape(Text::_('JHELP')). '",
                    intro: "' .$this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_HELP_DESC')) . '",
                    position: "left"
                },
                '.(($user->authorise('core.admin', 'com_tz_portfolio')
        || $user->authorise('core.options', 'com_tz_portfolio') )?'
                {
                    /* Step 3: Options */
                    element: $("#toolbar-options")[0],
                    title: "' . $this->escape(Text::_('JOPTIONS')). '",
                    intro: "'. $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_GLOBAL_CONFIGURATION_DESC')) . '",
                    position: "left"
                },':'').'
                {
                    /* Step 4: Sidebar */
                    element: $(".main-nav-container .collapse-level-1 > '
                .'.item.parent.item-level-2 > a[href=\\"index.php?option=com_tz_portfolio\\"]").parent()[0],
                    title: "' . $this->escape(Text::_('JTOGGLE_SIDEBAR_LABEL')). '",
                    intro: "'. $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_SIDEBAR_DESC')) . '",
                    position: "right"
                },
                {
                    /* Step 5: Quick link */
                    element: $(".tpQuicklink")[0],
                    title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_QUICK_LINKS')). '",
                    intro: "'. $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_QUICK_LINK_DESC')) . '",
                    position: "top"
                }
                '.($user->authorise('core.manage', 'com_tz_portfolio')?'
                ,{
                    /* Step 6: Information */
                    element: $(".tpInfo")[0],
                    title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INFORMATION')). '",
                    intro: "'. $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_INFORMATION_DESC')) . '",
                    position: "left"
                }':'').']';

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this -> document -> getWebAssetManager();

$wa -> useStyle('com_tz_portfolio.introjs');

if($lang -> isRtl()){
    $wa -> useStyle('com_tz_portfolio.intro-rtl');
}

$wa -> addInlineScript('
(function($){
    "use strict";
    $(document).ready(function(){

         tppIntroGuide("' . $this->getName() . '",'.$tppIntroGuide.', '
        . (TZ_PortfolioHelper::introGuideSkipped($this->getName()) ? 1 : 0) . ', "' . Session::getFormToken() . '");
        '.($user->authorise('core.manage', 'com_tz_portfolio')?'var compareVersion = function (curVer, onVer) {
            for (var i=0; i< curVer.length || i< onVer.length; i++){
                if (curVer[i] < onVer[i]) {
                    return true;
                }
            }
            return false;
        };':'').'
        $.ajax({
            url: "index.php?option=com_tz_portfolio",
            type: "POST",
            dataType: "json",
            data: {
                task: "dashboard.checkupdate"
            },
            success: function(result){
                if(result && result.success == true && result.data){
                    var latestVersion = result.data;
                    var currentVersion = $(".local-version span").attr("data-local-version");
                    $(".latest-version span").attr("data-online-version",latestVersion).html(latestVersion);
                    $(".checking").css("display", "none");
                    if (compareVersion(currentVersion, latestVersion)) {
                        $(".requires-updating").css("display","block");
                        $(".local-version span").addClass("oldversion");
                    } else {
                        $(".latest").css("display","block");
                    }
                }
            },
            beforeSend: function() {
                $(".checking").css("display", "block");
            }
        });
        $.ajax({
            url: "index.php?option=com_tz_portfolio",
            type: "POST",
            dataType: "json",
            data: {
                format: "ajax",
                view:   "dashboard",
                layout: "feed:default",
            },
            success: function(result){
                if(result.data){
                    $(".tpDashboard .tpInfo").after(result.data);
                }
            }
        });
    });
})(jQuery);
', ['position' => 'after'], [], ['com_tz_portfolio.introguide']);

?>

<?php echo HTMLHelper::_('tzbootstrap.addrow');?>
<?php if(!empty($this -> sidebar)){?>
    <div id="j-sidebar-container" class="col-md-2">
        <?php echo $this -> sidebar; ?>
    </div>
<?php } ?>

<?php echo HTMLHelper::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar),
    array('containerclass' => false));?>

    <div class="tpDashboard">
        <?php if($user -> authorise('core.manage', 'com_installer')) {?>
            <div class="tpHeadline">
                <h2 class="reset-heading"><?php echo Text::_('COM_TZ_PORTFOLIO_DASHBOARD'); ?></h2>
                <p><?php echo Text::_('COM_TZ_PORTFOLIO_DASHBOARD_DESC'); ?></p>
            </div>
        <?php } ?>
        <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
        <?php
        $col    = $user->authorise('core.manage', 'com_tz_portfolio')?7:12;
        ?>
        <div class="col-md-<?php echo $col; ?>">

            <?php if($user -> authorise('core.manage', 'com_installer')) {?>
                <div class="tp-widget free-license">
                    <span><?php echo Text::_('COM_TZ_PORTFOLIO_GET_FREE_PERSONAL_LICENSE'); ?></span>
                    <a href="<?php echo $xml -> freelicenseUrl; ?>" class="btn btn-danger" target="_blank"><?php
                        echo Text::_('COM_TZ_PORTFOLIO_GET_NOW'); ?></a>
                </div>
            <?php } ?>
            <div class="tpQuicklink">
                <?php
                if($user -> authorise('core.manage.article', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=articles', 'icon-64-articles.png', 'COM_TZ_PORTFOLIO_ARTICLES');
                }
                if($user -> authorise('core.manage.category', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=categories', 'icon-64-categories.png', 'COM_TZ_PORTFOLIO_CATEGORIES');
                }
                if($user -> authorise('core.manage.article', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=featured', 'icon-64-featured.png', 'COM_TZ_PORTFOLIO_FEATURED');
                }
                if($user -> authorise('core.manage.field', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=fields', 'icon-64-fields.png', 'COM_TZ_PORTFOLIO_FIELDS');
                }
                if($user -> authorise('core.manage.group', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=groups', 'icon-64-groups.png', 'COM_TZ_PORTFOLIO_FIELD_GROUPS');
                }
                if($user -> authorise('core.manage.tag', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=tags', 'icon-64-tags.png', 'COM_TZ_PORTFOLIO_TAGS');
                }
                if($user -> authorise('core.manage.addon', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=addons', 'icon-64-addons.png', 'COM_TZ_PORTFOLIO_ADDONS');
                }
                if($user -> authorise('core.manage.style', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=layouts', 'icon-64-styles.png', 'COM_TZ_PORTFOLIO_TEMPLATE_STYLES');
                }
                if($user -> authorise('core.manage.template', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=styles', 'icon-64-templates.png', 'COM_TZ_PORTFOLIO_TEMPLATES');
                }
                if($user -> authorise('core.manage.extension', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=extension&layout=upload', 'icon-64-modules.png', 'COM_TZ_PORTFOLIO_EXTENSIONS');
                }
                if($user -> authorise('core.manage.acl', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_tz_portfolio&view=acls', 'icon-64-security.png', 'COM_TZ_PORTFOLIO_ACL');
                }
                if($user -> authorise('core.manage.admin', 'com_tz_portfolio')
                    || $user -> authorise('core.manage.options', 'com_tz_portfolio')) {
                    $this->_quickIcon('index.php?option=com_config&view=component&component=com_tz_portfolio&return='
                        . urlencode(base64_encode(Uri::getInstance())), 'icon-64-configure.png', 'COM_TZ_PORTFOLIO_CONFIGURE');
                }
                ?>
            </div>
            <?php echo $this -> loadTemplate('statistics'); ?>
            <?php
            if($user -> authorise('core.manage', 'com_installer')) {
                echo $this->loadTemplate('license');
            }?>
        </div>
        <?php if($user -> authorise('core.manage', 'com_installer')) {?>
            <div class="col-md-5">
                <div class="tpInfo">
                    <div class="tpDesc">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_DESCRIPTION_2'); ?>
                    </div>
                    <div class="tpVersion">
                        <b class="checking">
                            <i class="tps tp-circle-notch tp-spin"></i> <?php echo Text::_('COM_TZ_PORTFOLIO_CHECKING_FOR_UPDATES'); ?></b>
                        <b class="latest"><?php echo Text::_('COM_TZ_PORTFOLIO_SOFTWARE_IS_UP_TO_DATE'); ?></b>
                        <b class="requires-updating">
                            <?php echo Text::_('COM_TZ_PORTFOLIO_REQUIRES_UPDATING'); ?>
                            <a href="http://www.tzportfolio.com/" class="btn btn-default btn-sm btn-secondary"><?php echo Text::_('COM_TZ_PORTFOLIO_UPDATE_NOW'); ?></a>
                        </b>
                        <div class="versions-meta">
                            <div class="text-muted local-version"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_INSTALLED_VERSION', '');?> <span data-local-version="<?php echo $xml->version; ?>"><?php echo $xml->version; ?></span></div>
                            <div class="text-muted latest-version"><?php echo Text::sprintf('COM_TZ_PORTFOLIO_LATEST_VERSION', '');?> <span data-online-version="">N/A</span></div>
                        </div>
                    </div>
                    <div class="tpDetail">
                        <ul>
                            <li><span><?php echo Text::_('COM_TZ_PORTFOLIO_AUTHOR'); ?>:</span> <a href="<?php echo $xml -> authorUrl;?>" target="_blank"><?php echo $xml->author; ?></a></li>
                            <li><span><?php echo Text::_('COM_TZ_PORTFOLIO_COPYRIGHT'); ?>:</span> <?php echo Text::sprintf('COM_TZ_PORTFOLIO_COPYRIGHT_FOOTER', $date ->year); ?></li>
                            <li><span><?php echo Text::_('COM_TZ_PORTFOLIO_SUPPORT'); ?>:</span> <a href="<?php echo $xml->forumUrl; ?>" title="<?php echo Text::_('COM_TZ_PORTFOLIO_SUPPORT'); ?>" target="_blank"><?php echo Text::_('COM_TZ_PORTFOLIO_SUPPORT_DESC'); ?></a></li>
                            <li><span><?php echo Text::_('COM_TZ_PORTFOLIO_GROUP'); ?>:</span> <a href="<?php echo $xml->facebookGroupUrl; ?>" target="_blank"><?php echo $xml->facebookGroupUrl; ?></a></li>
                            <li><span><?php echo Text::_('COM_TZ_PORTFOLIO_FANPAGE'); ?>:</span> <a href="<?php echo $xml->facebookUrl; ?>" target="_blank"><?php echo $xml->facebookUrl; ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
    </div>
<?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
<?php echo HTMLHelper::_('tzbootstrap.endrow');?>