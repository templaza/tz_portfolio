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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUser;

$user		= TZ_PortfolioUser::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$saveOrder	= ($listOrder == 'f.ordering' || $listOrder == 'ordering');

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio&task=fields.saveOrderAjax&tmpl=component';
    HTMLHelper::_('draggablelist.draggable');
}
?>

<form id="adminForm" name="adminForm" method="post" action="index.php?option=com_tz_portfolio&view=fields">

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
            <table class="table" id="extraFieldList">
                <caption class="visually-hidden">
                    <?php echo Text::_('COM_TZ_PORTFOLIO_FIELDS'); ?>,
                    <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                    <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                </caption>
                <thead>
                <tr>
                    <td class="w-1 text-center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </td>
                    <th class="w-1 text-center d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th scope="col" class="w-1 text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'f.published', $listDirn, $listOrder); ?>
                    </th>
                    <th><?php echo HTMLHelper::_('searchtools.sort','JGLOBAL_TITLE','f.title'
                            ,$listDirn,$listOrder);?></th>
                    <th class="w-18"><?php echo HTMLHelper::_('searchtools.sort','COM_TZ_PORTFOLIO_GROUP','groupname'
                            ,$listDirn,$listOrder);?></th>
                    <th class="w-7"><?php echo HTMLHelper::_('searchtools.sort','COM_TZ_PORTFOLIO_TYPE','f.type'
                            ,$listDirn,$listOrder);?></th>
                    <th class="w-5 d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'f.access', $listDirn, $listOrder); ?>
                    </th>
                    <th class="w-5 text-center"><?php echo HTMLHelper::_('searchtools.sort','COM_TZ_PORTFOLIO_LIST_VIEW_LABEL','f.list_view'
                            ,$listDirn,$listOrder);?></th>
                    <th class="w-5 text-center"><?php echo HTMLHelper::_('searchtools.sort','COM_TZ_PORTFOLIO_DETAILS_VIEW_LABEL','f.detail_view'
                            ,$listDirn,$listOrder);?></th>
                    <th class="w-5 text-center"><?php echo HTMLHelper::_('searchtools.sort','COM_TZ_PORTFOLIO_ADVANCED_SEARCH_LABEL','f.advanced_search'
                            ,$listDirn,$listOrder);?></th>
                    <th scope="col" class="w-1">
                        <?php echo HTMLHelper::_('searchtools.sort','JGRID_HEADING_ID','f.id',$listDirn,$listOrder);?>
                    </th>
                </tr>
                </thead>
                <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                <?php
                foreach($this -> items as $i => $item){
                    $canEdit    = $user->authorise('core.edit',		  'com_tz_portfolio.field.'.$item->id)
                        && (count($user -> getAuthorisedFieldGroups('core.edit', $item -> groupid)) > 0);
                    $canEditOwn = $user->authorise('core.edit.own', 'com_tz_portfolio.field.'.$item->id)
                        && $item->created_by == $userId && (count($user -> getAuthorisedFieldGroups('core.edit.own', $item -> groupid)) > 0);
                    $canCheckin = $user->authorise('core.manage',     'com_checkin')
                        || $item->checked_out == $userId || $item->checked_out == 0;
                    $canChange  = ($user->authorise('core.edit.state', 'com_tz_portfolio.field.'.$item->id)
                            ||($user->authorise('core.edit.state.own', 'com_tz_portfolio.field.'
                                    .$item->id)
                                && $item->created_by == $userId)) && $canCheckin;
                    ?>
                    <tr class="row<?php echo ($i%2==1)?'1':$i;?>"<?php
                    echo ($group = $this -> state -> get('filter.group'))?'sortable-group-id="'.$group
                        .'"':'';?> data-draggable-group="<?php echo $group?$group:0; ?>">
                        <td class="text-center">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="order nowrap center text-center d-none d-md-table-cell">
                            <?php
                            $iconClass = '';
                            if (!$canChange)
                            {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrder)
                            {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::_('tooltipText', 'JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
                                    <span class="icon-menu" aria-hidden="true"></span>
                                </span>
                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                        </td>
                        <td class="center text-center">
                            <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'fields.', $canChange, 'cb'); ?>
                        </td>
                        <th class="nowrap has-context">
                            <?php if ($item -> checked_out){ ?>
                                <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item -> editor, $item -> checked_out_time, 'fields.', $canCheckin); ?>
                            <?php } ?>
                            <?php if($canEdit || $canEditOwn){?>
                                <a href="index.php?option=com_tz_portfolio&task=field.edit&id=<?php echo $item -> id;?>">
                                    <?php echo $this -> escape($item -> title);?>
                                </a>
                            <?php }else{ ?>
                                <?php echo $this -> escape($item -> title);?>
                            <?php } ?>
                        </th>
                        <td class="small d-none d-md-table-cell">
                            <?php echo $item -> groupname;?>
                        </td>

                        <td class="small d-none d-md-table-cell"><?php echo $item -> type;?></td>
                        <td class="small d-none d-md-table-cell">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="center text-center"><?php
                            $active_class   = ($item -> list_view)?'publish':'unpublish';
                            echo HTMLHelper::_('jgrid.action', $i, ($item -> list_view == 1?'unlistview':'listview'),
                                'fields.', '', '', false, $active_class,$active_class, $canChange); ?></td>
                        <td class="center text-center"><?php
                            $dactive_class   = ($item -> detail_view)?'publish':'unpublish';
                            echo HTMLHelper::_('jgrid.action', $i, ($item -> detail_view == 1?'undetailview':'detailview'),
                                'fields.', '', '', false, $dactive_class,$dactive_class, $canChange); ?></td>
                        <td class="center text-center"><?php
                            $advactive_class   = ($item -> advanced_search)?'publish':'unpublish';
                            echo HTMLHelper::_('jgrid.action', $i, ($item -> advanced_search == 1?'unadvsearch':'advsearch'),
                                'fields.', '', '', false, $advactive_class,$advactive_class, $canChange); ?></td>
                        <td class="center text-center"><?php echo $item -> id;?></td>
                    </tr>
                <?php } ?>

                </tbody>

            </table>

            <?php // load the pagination. ?>
            <?php echo $this->pagination->getListFooter(); ?>

        <?php }?>
        <input type="hidden" value="" name="task">
        <input type="hidden" value="0" name="boxchecked">
        <input type="hidden" name="return" value="<?php echo base64_encode(Uri::getInstance() -> toString())?>">
        <?php echo HTMLHelper::_('form.token');?>
    </div>
    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
    <?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</form>