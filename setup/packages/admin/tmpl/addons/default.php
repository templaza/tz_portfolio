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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\TZ_PortfolioHelper;

$user		= Factory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_tz_portfolio.addons');
$saveOrder	= $listOrder == 'ordering';

//$j4Compare  = COM_TZ_PORTFOLIO_JVERSION_4_COMPARE;
if ($saveOrder && !empty($this->items))
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio&task=addons.saveOrderAjax&tmpl=component'
        . Session::getFormToken() . '=1';

    HTMLHelper::_('draggablelist.draggable');
}

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this -> document -> getWebAssetManager();

$wa->useScript('table.columns')
    ->useScript('multiselect')
    -> useStyle('com_tz_portfolio.introjs');

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
        if($("#addonList .js-tpp-title").length){
            addonSteps.push({
                /* Step 2: Config options of addon */
                element: $("#addonList .js-tpp-title")[0],
                title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_CONFIG_ADDON')). '",            
                intro: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_CONFIG_ADDON_DESC')) . '",
                position: "top"
            });
        }
        if($("#addonList .js-tpp-data-manage").length){
            addonSteps.push({
                /* Step 3: Addon data management */
                element: $("#addonList .js-tpp-data-manage")[0],
                title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_DATA_MANAGEMENT')). '",            
                intro: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_DATA_MANAGEMENT_DESC')) . '",
                position: "right"
            });
//            addonSteps[2]   = {
//                /* Step 2: Config options of addon */
//                element: $("#addonList .js-tpp-data-manage")[0],
//                title: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_DATA_MANAGEMENT')). '",            
//                intro: "' . $this->escape(Text::_('COM_TZ_PORTFOLIO_INTRO_GUIDE_DATA_MANAGEMENT_DESC')) . '",
//                position: "right"
//            }
        }
    
        tppIntroGuide("'.$this -> getName().'",addonSteps , '
            .(TZ_PortfolioHelper::introGuideSkipped($this -> getName())?1:0).', "'.Session::getFormToken().'");
    });
})(jQuery);', ['position' => 'after'], [], ['com_tz_portfolio.introguide']);
?>
<form action="index.php?option=com_tz_portfolio&view=addons" method="post" name="adminForm"
      class="tz_portfolio-addons"
      id="adminForm">
    <?php echo HTMLHelper::_('tzbootstrap.addrow');?>
    <?php if(!empty($this -> sidebar)){?>
        <div id="j-sidebar-container" class="col-md-2">
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
            <div class="alert alert-warning alert-no-items">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php }else{ ?>
            <table class="table"  id="addonList">
                <caption class="visually-hidden">
                    <?php echo Text::_('COM_TZ_PORTFOLIO_ADDONS'); ?>,
                    <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                    <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                </caption>
                <thead>
                <tr>
                    <th class="w-1 nowrap center text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th class="w-1 hidden-phone">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </th>
                    <th class="w-1 nowrap center text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                    </th>
                    <th class="title">
                        <?php echo HTMLHelper::_('searchtools.sort','JGLOBAL_TITLE','name',$listDirn,$listOrder);?>
                    </th>
                    <th class="w-7 nowrap center text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_TYPE', 'folder', $listDirn, $listOrder); ?>
                    </th>
                    <th class="w-10 nowrap center text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_ELEMENT', 'element', $listDirn, $listOrder); ?>
                    </th>
                    <th class="w-5 nowrap hidden-phone">
                        <?php echo HTMLHelper::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'f.access', $listDirn, $listOrder); ?>
                    </th>
                    <th class="w-6 nowrap center text-center">
                        <?php echo Text::_('JVERSION'); ?>
                    </th>
                    <th class="w-10 nowrap center text-center">
                        <?php echo Text::_('JDATE'); ?>
                    </th>
                    <th class="w-10 nowrap">
                        <?php echo Text::_('JAUTHOR'); ?>
                    </th>
                    <th class="w-1 nowrap">
                        <?php echo HTMLHelper::_('searchtools.sort','JGRID_HEADING_ID','id',$listDirn,$listOrder);?>
                    </th>
                </tr>
                </thead>

                <?php if($this -> items):?>
                    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                    ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                    <?php foreach($this -> items as $i => $item):

                        $canCreate = $user->authorise('core.create',     'com_tz_portfolio.addon');
                        $canEdit   = ($user->authorise('core.edit', 'com_tz_portfolio.addon.'.$item -> id)
                            || $user->authorise('core.admin', 'com_tz_portfolio.addon.'.$item -> id)
                            || $user->authorise('core.options', 'com_tz_portfolio.addon.'.$item -> id));
                        $canCheckin = $user->authorise('core.manage',     'com_checkin')
                            || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                        $canChange = $user->authorise('core.edit.state', 'com_tz_portfolio.addon') && $canCheckin;

                        ?>
                        <tr class="<?php echo ($i%2==0)?'row0':'row1';?>" sortable-group-id="<?php echo $item->folder
                        ?>" data-draggable-group="<?php echo $item->folder?>">
                            <td class="order nowrap center text-center hidden-phone">
                                <?php
                                $iconClass = '';
                                if (!$canChange)
                                {
                                    $iconClass = ' inactive';
                                }
                                elseif (!$saveOrder)
                                {
                                    $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
                                }
                                ?>
                                <span class="sortable-handler<?php echo $iconClass ?>">
                                <span class="icon-menu"></span>
                            </span>
                                <?php if ($canChange && $saveOrder) : ?>
                                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                                <?php endif; ?>
                            </td>
                            <td class="center text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center text-center">
                                <?php
                                $states	= array(
                                    2 => array(
                                        '',
                                        'COM_TZ_PORTFOLIO_ADDON_PROTECTED',
                                        '',
                                        'COM_TZ_PORTFOLIO_ADDON_PROTECTED',
                                        true,
                                        'protected',
                                        'protected',
                                    ),
                                    1 => array(
                                        'unpublish',
                                        'COM_TZ_PORTFOLIO_ADDON_ENABLED',
                                        'COM_TZ_PORTFOLIO_ADDON_DISABLE',
                                        'COM_TZ_PORTFOLIO_ADDON_ENABLED',
                                        true,
                                        'publish',
                                        'publish',
                                    ),
                                    0 => array(
                                        'publish',
                                        'COM_TZ_PORTFOLIO_ADDON_DISABLED',
                                        'COM_TZ_PORTFOLIO_ADDON_ENABLE',
                                        'COM_TZ_PORTFOLIO_ADDON_DISABLED',
                                        true,
                                        'unpublish',
                                        'unpublish',
                                    ),
                                );

                                if($item ->protected) {
                                    echo HTMLHelper::_('jgrid.state', $states, 2, $i, 'addon.', false, true, 'cb');
                                }else{
                                    echo HTMLHelper::_('jgrid.state', $states, $item->published, $i, 'addons.', $canChange, true, 'cb');
                                }
                                ?>
                            </td>
                            <td class="nowrap has-context">
                                <div class="pull-left float-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'addons.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if($canEdit){?>
                                        <a href="index.php?option=com_tz_portfolio&task=addon.edit&id=<?php
                                        echo $item -> id;?>" class="js-tpp-title"><?php
                                            echo $item->name;
                                            ?></a>
                                    <?php }else{
                                        echo $item -> name;
                                    } ?>

                                    <?php
                                    if(isset($item -> data_manager) && !empty($item -> data_manager)){
                                        ?>
                                        <a href="<?php echo Route::_(TZ_Portfolio_PlusHelperAddon_Datas::getRootURL($item -> id));?>"
                                           class="btn btn-secondary btn-small btn-sm hasTooltip js-tpp-data-manage"
                                           title="<?php echo Text::_('COM_TZ_PORTFOLIO_ADDON_DATA_MANAGER')?>">
                                            <span class="icon-book me-1"></span><span><?php echo Text::_('COM_TZ_PORTFOLIO_ADDON_DATA_MANAGER')?></span>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="center text-center">
                                <?php echo $item -> folder;?>
                            </td>
                            <td class="center text-center">
                                <?php echo $item -> element;?>
                            </td>
                            <td class="nowrap small hidden-phone">
                                <?php echo $this->escape($item->access_level); ?>
                            </td>
                            <td class="nowrap center text-center hidden-phone">
                                <?php echo @$item -> version != '' ? $item -> version : '&#160;';?>
                            </td>
                            <td class="nowrap center text-center hidden-phone">

                                <?php echo @$item-> creationDate != '' ? $item-> creationDate : '&#160;'; ?>
                            </td>
                            <td class="nowrap hidden-phone">
                                <span class="editlinktip hasTooltip" title="<?php echo HTMLHelper::tooltipText(Text::_('COM_TZ_PORTFOLIO_AUTHOR_INFORMATION'), $item -> author_info, 0); ?>">
                                    <?php echo @$item->author != '' ? $item->author : '&#160;'; ?>
                                </span>
                            </td>

                            <td align="center text-center hidden-phone"><?php echo $item -> id;?></td>
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
        <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
        <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</form>