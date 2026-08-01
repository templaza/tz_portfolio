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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Associations;

$params     = $this -> params;

$user		    = Factory::getApplication()->getIdentity();
$userId		    = $user->get('id');
$listOrder	    = $this->escape($this->state->get('list.ordering'));
$listDirn	    = $this->escape($this->state->get('list.direction'));
$canOrder	    = $user->authorise('core.edit.state', 'com_tz_portfolio.article');
$archived	    = $this->state->get('filter.published') == 2 ? true : false;
$trashed	    = $this->state->get('filter.published') == -2 ? true : false;
$saveOrder	    = $listOrder == 'a.ordering';
$savePriority   = $listOrder == 'a.priority';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tz_portfolio&task='.$this -> getName().'.saveOrderAjax&tmpl=component';
    HTMLHelper::_('draggablelist.draggable');
}

$assoc		= Associations::isEnabled();

/* @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this -> document -> getWebAssetManager();

$wa -> useScript('com_tz_portfolio.core');

$wa -> addInlineScript('
//console.log(window.TZ_Portfolio);
(function($, TZ_Portfolio){
        "use strict";
        $(document).ready(function(){
            window.TZ_Portfolio.dialogAjax(["'.$this -> getName().'.approve", "'.$this -> getName().'.reject"]);
        });
    })(jQuery, window.TZ_Portfolio);');

$menu   = Factory::getApplication() -> getMenu();
$active = $menu -> getActive();
$url    = 'index.php?option=com_tz_portfolio&view='.$this -> getName()
    .((isset($active -> id) && $active -> id)?'&Itemid='.$active -> id:'');
?>

<div class="tp-myarticles-page <?php echo $this->pageclass_sfx;?>">
    <?php if ($params->get('show_page_heading', 1)) : ?>
        <h1 class="page-heading">
            <?php echo $this->escape($params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <form action="<?php echo Route::_($url);
    ?>" method="post" name="adminForm" id="adminForm">

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
                    <div class="uk-alert-warning alert-no-items" data-uk-alert>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php }else{ ?>
                <table class="uk-table uk-table-hover uk-table-divider" id="myArticleList">
                    <thead>
                        <tr>
                            <th width="1%" class="uk-visible@s">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </th>
                            <th>
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                            </th>
                            <th width="6%" class="uk-text-nowrap">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_TYPE_OF_MEDIA', 'groupname', $listDirn, $listOrder); ?>
                            </th>
                            <th width="10%" class="uk-text-nowrap uk-visible@s">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_GROUP', 'groupname', $listDirn, $listOrder); ?>
                            </th>

                            <?php if ($assoc) : ?>
                            <th width="5%" class="uk-text-nowrap uk-visible@s">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
                            </th>
                            <?php endif;?>

                            <th width="10%" class="uk-text-nowrap uk-visible@s">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                            </th>
                            <th width="8%" class="uk-text-nowrap uk-visible@s">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
                            </th>
                            <th width="5%" class="uk-text-center uk-text-nowrap uk-visible@s">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
                            </th>
                            <th width="1%" class="uk-text-center uk-text-nowrap uk-visible@s">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PRIORITY', 'a.priority', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td>
                    </tr>
                    </tfoot>
                    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                    ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                    <?php
                    if($this -> items):
                        foreach ($this->items as $i => $item) :

                            $item->max_ordering = 0; //??

                            $ordering	    = ($listOrder == 'a.ordering');

                            $filterPublished= $this -> state -> get('filter.published');

                            $canCreate	    = $user->authorise('core.create',	  'com_tz_portfolio.category.'.$item->catid);
                            $canEdit	    = $user->authorise('core.edit',		  'com_tz_portfolio.article.'.$item->id);
                            $canCheckin	    = $user->authorise('core.manage',	  'com_checkin')
                                                || $item->checked_out == $userId || $item->checked_out == 0;
                            $canEditOwn	    = $user->authorise('core.edit.own', 'com_tz_portfolio.article.'.$item->id)
                                                && $item->created_by == $userId;
                            $canDelete      = $filterPublished == -2 && ($user->authorise('core.delete', 'com_tz_portfolio.article.'.$item->id)
                                    ||($user->authorise('core.delete.own', 'com_tz_portfolio.article.'
                                            .$item->id)
                                        && $item->created_by == $userId)) && $canCheckin;
                            $canChange	    = ($user->authorise('core.edit.state', 'com_tz_portfolio.article.'.$item->id)
                                                ||($user->authorise('core.edit.state.own', 'com_tz_portfolio.article.'
                                                .$item->id)
                                                && $item->created_by == $userId)) && $canCheckin;
                            $canApprove     = \TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ACLHelper::allowApprove($item);
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item -> catid;
                            ?>" data-dragable-group="<?php echo $item->catid; ?>">
                                <td class="uk-text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="uk-transition-toggle">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('tpjgrid.checkedout', $i,
                                            $item->editor, $item->checked_out_time, 'myarticles.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php
                                    if(($canApprove && ($canEdit || $canEditOwn || $item -> state == 3 || $item -> state == 4)) ||
                                        (!$canApprove && ($canEditOwn || $item -> state == 3 || $item -> state == -3) && $item -> state != 4)){
                                        ?>
                                        <?php
                                        $editIcon   = '';
                                        ?>
                                        <a class="title" href="<?php
                                        echo Route::_('index.php?option=com_tz_portfolio&task=article.edit&a_id='
                                            .$item->id.((isset($active -> id) && $active -> id)?'&Itemid='.$active -> id:'')
                                            .'&return='.base64_encode(Route::_($url)));?>">
                                            <?php echo $editIcon.$this->escape($item->title); ?></a>
                                    <?php }else{ ?>
                                        <?php echo $this->escape($item->title); ?>
                                    <?php } ?>
                                    <?php if(isset($item -> rejected_id) && $item -> rejected_id && in_array($item -> state, array(-3,3,4))){ ?>
                                        <span class="uk-label uk-label-danger label-important"><?php echo Text::_('COM_TZ_PORTFOLIO_REJECTED'); ?></span>
                                    <?php } ?>
                                    <?php
                                    if($filterPublished === '*'){?>
                                        <?php if($item -> state == 3){ ?>
                                            <span class="uk-label uk-label-warning"><?php echo Text::_('COM_TZ_PORTFOLIO_PENDING'); ?>...</span>
                                        <?php } ?>
                                    <?php } ?>
                                    <div class="uk-text-small uk-text-meta">
                                        <div class="clearfix">
                                            <?php echo Text::_('COM_TZ_PORTFOLIO_MAIN_CATEGORY') . ": " ?>
                                            <a href="<?php echo Route::_(
                                                'index.php?option=com_tz_portfolio&view=myarticles&catid='
                                                .$item -> catid);?>"><?php echo $this->escape($item->category_title); ?></a>
                                        </div>
                                        <?php if(isset($item -> categories) && $item -> categories && count($item -> categories)):?>
                                        <div class="clearfix">
                                            <?php echo Text::_('COM_TZ_PORTFOLIO_SECONDARY_CATEGORY') . ": " ?>
                                            <?php foreach($item -> categories as $i => $category):?>
                                                <a href="<?php echo Route::_(
                                                        'index.php?option=com_tz_portfolio&view=myarticles&catid='
                                                        .$category -> id);?>"><?php echo $this->escape($category->title); ?></a>
                                                <?php
                                                if($i < count($item -> categories) - 1){
                                                    echo ',';
                                                }
                                                ?>
                                            <?php endforeach;?>
                                        </div>
                                        <?php endif;?>
                                        <div class="clearfix">
                                            <?php echo Text::_('JAUTHOR').': '.$this->escape($item->author_name); ?>
                                        </div>
                                    </div>
                                    <?php if(isset($item -> rejected_id) && $item -> rejected_id){ ?>
                                        <div class="tp-reject__message">
                                            <strong><u><?php echo Text::_('COM_TZ_PORTFOLIO_REASON'); ?></u></strong>: <?php echo $item -> rejected_message; ?>
                                        </div>
                                    <?php } ?>
                                    <ul class="tp-myarticles__actions uk-list uk-list-collapse uk-transition-fade uk-grid-small" data-tpp-actions data-tpp-id="<?php
                                    echo $item -> id;?>" data-uk-grid>
                                        <?php
                                        if($canApprove && ($item -> state == 3 || $item -> state == 4) ){ ?>
                                            <li>
                                                <?php echo HTMLHelper::_('tpjgrid.approveLink', $item -> state, $i,
                                                    $this -> getName());?>
                                            </li>
                                            <li>
                                                <?php echo HTMLHelper::_('tpjgrid.rejectLink', $i,
                                                    $this -> getName());?>
                                            </li>
                                        <?php
                                        }elseif($item -> state != 4){
                                            if($canChange) {
                                                ?>

                                                <li>
                                                    <?php echo HTMLHelper::_('tpjgrid.taskLink', $item->state, $i,
                                                        $this->getName()); ?>
                                                </li>

                                                <?php if(!in_array($item -> state, array(-3, -2, 3))){ ?>
                                                <li>
                                                    <?php echo HTMLHelper::_('tpjgrid.trashLink', $i,
                                                        $this->getName()); ?>
                                                </li>
                                                <?php } ?>

                                                <?php
                                            }
                                                if($canDelete) { ?>
                                                <li>
                                                    <?php echo HTMLHelper::_('tpjgrid.deleteLink', $i,
                                                        $this -> getName());?>
                                                </li>
                                            <?php
                                            }
                                            if($canApprove && $filterPublished != -2){ ?>
                                                <li><?php echo HTMLHelper::_('tpjgrid.featuredLink',$item -> featured, $i,
                                                        $this -> getName(), $canChange) ?></li>
                                            <?php } ?>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                </td>
                                <td class="uk-text-small uk-visible@s">
                                    <?php echo $item -> type;?>
                                </td>
                                <td class="uk-text-small uk-visible@s">
                                    <a href="index.php?option=com_tz_portfolio&task=group.edit&id=<?php echo $item -> groupid?>">
                                        <?php echo $item -> groupname;?>
                                    </a>
                                </td>
                                <?php if ($assoc) : ?>
                                <td class="uk-visible@s">
                                    <?php if ($item->association) : ?>
                                        <?php echo HTMLHelper::_('contentadministrator.association', $item->id); ?>
                                    <?php endif; ?>
                                </td>
                                <?php endif;?>
                                <td class="uk-text-small uk-visible@s">
                                    <?php
                                    if($item -> state == 0){ ?>
                                        <span class="text-error text-danger"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                                    <?php }elseif($item -> state == 1){ ?>
                                        <span class="text-success"><?php echo Text::_('JPUBLISHED'); ?></span>
                                    <?php }
                                    elseif($item -> state == -2){?>
                                        <span><?php echo Text::_('JTRASHED'); ?></span>
                                    <?php }
                                    elseif($item -> state == -3){?>
                                        <span><?php echo Text::_('COM_TZ_PORTFOLIO_DRAFT'); ?></span>
                                    <?php }
                                    elseif($item -> state == 4){?>
                                        <span><?php echo Text::_('COM_TZ_PORTFOLIO_UNDER_REVIEW'); ?></span>
                                    <?php } ?>
                                </td>
                                <td class="uk-text-small uk-text-nowrap uk-visible@s">
                                    <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                                </td>
                                <td class="uk-text-center uk-visible@s">
                                    <?php echo (int) $item->hits; ?>
                                </td>
                                <td class="uk-text-nowrap uk-visible@s order" style="text-align: right;">
                                    <?php echo $item -> priority; ?>
                                </td>
                            </tr>
                            <?php endforeach;
                        endif;
                        ?>
                        </tbody>
                </table>
                <?php } ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
    </form>
</div>