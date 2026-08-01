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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

$user		= Factory::getApplication() -> getIdentity();
$lang   = Factory::getApplication()->getLanguage();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));


/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect')
    ->useStyle('com_tz_portfolio.introjs');

if($lang -> isRtl()){
    $wa -> useStyle('com_tz_portfolio.intro-rtl');
}

$wa -> addInlineStyle('
    .tz_portfolio-styles .thumbnail > img{
        max-width: 80px;
    }');

$wa -> addInlineScript('
(function($){
    "use strict";
    
    $(document).ready(function(){
        var styleSteps  = [
        {
        /* Step 1: Install */
            element: $("#toolbar-new > button")[0],
            title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INSTALL_UPDATE')). '",
            intro: "' . $this->escape(Text::sprintf('COM_TZ_PORTFOLIO_INTRO_GUIDE_INSTALL_MANUAL_ONLINE_DESC',
                Text::_('COM_TZ_PORTFOLIO_ADDON'))) . '",
            position: "right"
        }];
        
        tppIntroGuide("'.$this -> getName().'",styleSteps , '.(TZ_PortfolioHelper::introGuideSkipped($this -> getName())?1:0)
    .', "'.Session::getFormToken().'");
    });
})(jQuery);
', ['position' => 'after'], [], ['com_tz_portfolio.introguide']);
?>
<form action="index.php?option=com_tz_portfolio&view=<?php echo $this -> getName();?>" method="post" name="adminForm"
      class="tz_portfolio-styles"
      id="adminForm">
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
    <?php if(!empty($this -> sidebar)){?>
        <div id="j-sidebar-container" class="span2 col-md-2">
            <?php echo $this -> sidebar; ?>
        </div>
    <?php } ?>

    <?php echo HTMLHelper::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar));?>

    <div class="tpContainer">
        <?php
        // Search tools bar
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>

        <?php if (empty($this->items)){ ?>
            <div class="alert alert-no-items">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php }else{ ?>

            <table class="table" id="stylesList">
                <thead>
                <tr>
                    <th class="w-1 nowrap"></th>
                    <th class="w-7 nowrap col1template hidden-phone">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_THUMBNAIL');?>
                    </th>
                    <th class="title">
                        <?php echo HTMLHelper::_('searchtools.sort','COM_TZ_PORTFOLIO_TEMPLATE_LABEL','name', $listDirn, $listOrder);?>
                    </th>
                    <th class="w-5 nowrap center text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                    </th>
                    <th class="w-7 nowrap center text-center">
                        <?php echo Text::_('JVERSION'); ?>
                    </th>
                    <th class="w-12 nowrap center text-center">
                        <?php echo Text::_('JDATE'); ?>
                    </th>
                    <th class="w-18 nowrap">
                        <?php echo Text::_('JAUTHOR'); ?>
                    </th>
                    <th class="w-1 nowrap">
                        <?php echo HTMLHelper::_('searchtools.sort','JGRID_HEADING_ID','id', $listDirn, $listOrder);?>
                    </th>
                </tr>
                </thead>

                <?php if($this -> items):?>
                    <tbody>
                    <?php foreach($this -> items as $i => $item):

                        $canCreate = $user->authorise('core.create',     'com_tz_portfolio.style');
                        $canCheckin = $user->authorise('core.manage',     'com_tz_portfolio')
                            || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                        $canChange = $user->authorise('core.edit.state', 'com_tz_portfolio.style') && $canCheckin;

                        ?>
                        <tr>
                            <td class="center text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center text-center hidden-phone">
                                <?php
                                echo HTMLHelper::_('tztemplates.thumb', $item->name);
                                ?>
                            </td>
                            <td class="nowrap has-context">
                                <?php echo $item->name; ?>
                            </td>

                            <td class="center text-center">
                                <?php
                                $states	= array(
                                    2 => array(
                                        '',
                                        'COM_INSTALLER_EXTENSION_PROTECTED',
                                        '',
                                        'COM_INSTALLER_EXTENSION_PROTECTED',
                                        true,
                                        'protected',
                                        'protected',
                                    ),
                                    1 => array(
                                        'unpublish',
                                        'COM_INSTALLER_EXTENSION_ENABLED',
                                        'COM_INSTALLER_EXTENSION_DISABLE',
                                        'COM_INSTALLER_EXTENSION_ENABLED',
                                        true,
                                        'publish',
                                        'publish',
                                    ),
                                    0 => array(
                                        'publish',
                                        'COM_INSTALLER_EXTENSION_DISABLED',
                                        'COM_INSTALLER_EXTENSION_ENABLE',
                                        'COM_INSTALLER_EXTENSION_DISABLED',
                                        true,
                                        'unpublish',
                                        'unpublish',
                                    ),
                                );

                                if($item ->protected) {
                                    echo HTMLHelper::_('jgrid.state', $states, 2, $i, 'style.', false, true, 'cb');
                                }else{
                                    echo HTMLHelper::_('jgrid.state', $states, $item->published, $i, $this -> getName().'.', $canChange, true, 'cb');
                                }
                                ?>
                            </td>

                            <td class="center text-center hidden-phone">
                                <?php echo @$item -> version != '' ? $item -> version : '&#160;';?>
                            </td>
                            <td class="center text-center hidden-phone">

                                <?php echo @$item-> creationDate != '' ? $item-> creationDate : '&#160;'; ?>
                            </td>
                            <td class="hidden-phone">
                                <?php if ($author = $item-> author) : ?>
                                    <p><?php echo $this->escape($author); ?></p>
                                <?php else : ?>
                                    &mdash;
                                <?php endif; ?>
                                <?php if ($email = $item->authorEmail) : ?>
                                    <p><?php echo $this->escape($email); ?></p>
                                <?php endif; ?>
                                <?php if ($url = $item->authorUrl) : ?>
                                    <p><a href="<?php echo $this->escape($url); ?>">
                                            <?php echo $this->escape($url); ?></a></p>
                                <?php endif; ?>
                            </td>

                            <td class="center text-center hidden-phone"><?php echo $item -> id;?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                <?php endif;?>

            </table>

            <?php // load the pagination. ?>
            <?php echo $this->pagination->getListFooter(); ?>

        <?php } ?>

        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token');?>

    </div>
    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</form>