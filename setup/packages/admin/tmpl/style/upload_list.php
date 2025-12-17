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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

$dataServer = $this -> state -> get('list.dataserver');
$listOrder	    = $this->escape($this->state->get('list.ordering'));

$layoutData = array(
    'params'   => array(
        'url'        => '',
        'width'      => '400px',
        'height'     => '800px',
    )
);

$iframeHtml = LayoutHelper::render('joomla.modal.iframe', $layoutData);

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this -> document -> getWebAssetManager();

$wa -> addInlineScript('(function($){
    "use strict";
    $(document).ready(function(){
        $("#adminForm").tppServerList({
            "iframeHtml": "' . str_replace('"', '\\"',trim($iframeHtml)) . '",
            "formToken" : "'.Session::getFormToken().'",
            "ajax"              : {
                "data"          : {
                    "limitstart": '.$this -> state -> get('list.start', 0).'
                }
            },
            "installNow":{
                "loadingHtml"   : "<span class=\\"loading\\"><span class=\\"fas fa-sync-alt text-update fa-spin\\"></span> '
    .Text::_('COM_TZ_PORTFOLIO_INSTALLING').'</span>",
                "installedHtml" : "<span class=\\"installed\\"><span class=\\"fas fa-check\\"></span> '
    .Text::_('COM_TZ_PORTFOLIO_INSTALLED').'</span>"
            }
        });
    });
})(jQuery);', ['position' => 'after'], [], ['com_tz_portfolio.server-list']);

$xml    = TZ_PortfolioHelper::getXMLManifest();
?>
<div class="tpContainer tpp-container__bar">
    <button type="button" data-toggle="collapse" data-target="#tpp-addon__upload" data-bs-toggle="collapse" data-bs-target="#tpp-addon__upload"
            class="btn btn-success hasTooltip float-start tpp-container__button-upload" title="<?php echo Text::_('JTOOLBAR_UPLOAD');
    ?>"><span class="icon-upload"></span> <?php echo Text::_('JTOOLBAR_UPLOAD'); ?></button>
    <?php
    // Search tools bar
    echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
</div>

<div class="tpp-extension__upload-form collapse bg-white" id="tpp-addon__upload">
    <fieldset>
        <legend class="h2"><?php echo Text::_('COM_TZ_PORTFOLIO_UPLOAD_AND_INSTALL_ADDON');?></legend>
        <div class="form-horizontal">
            <div class="control-group">
                <div class="control-label"><?php echo $this -> form -> getLabel('install_package');?></div>
                <div class="controls"><?php echo $this -> form -> getInput('install_package');?></div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <button class="btn btn-primary btn-small" type="button" onclick="Joomla.submitbutton('style.install')">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_UPLOAD_AND_INSTALL');?></button>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<div class="tpp-extension__list">
    <div class="alert alert-warning alert-no-items" style="display: none;" data-tpp-error>
        <?php echo Text::sprintf('COM_TZ_PORTFOLIO_ERROR_LOADING_FROM_SERVER', $xml -> authorUrl, $xml->forumUrl); ?>
    </div>
    <div class="tpp-extension__list-inner">
        <div class="loading" data-tpp-loading>
            <span class="tps tp-circle-notch tp-spin"></span><span><?php echo Text::_('COM_TZ_PORTFOLIO_SETUP_LOADING');?>...</span>
        </div>
        <div class="tpp-extension__flexbox" data-tpp-extension-list></div>
    </div>
    <div data-tpp-pagination></div>
</div>