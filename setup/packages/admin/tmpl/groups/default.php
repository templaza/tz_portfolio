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
use Joomla\CMS\Layout\LayoutHelper;

$user		= Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'g.ordering';

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio&task=groups.saveOrderAjax&tmpl=component';
    HTMLHelper::_('draggablelist.draggable');
}

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');
?>

<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_tz_portfolio&view=groups">

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
                <table class="table" id="groups">
                    <caption class="visually-hidden">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_FIELD_GROUPS'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                    </caption>
                    <thead>
                    <tr>
                        <td class="w-1 text-center">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </td>
                        <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', '', 'g.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
                        <th scope="col" class="w-1 text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'g.published', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col">
                            <?php echo HTMLHelper::_('searchtools.sort','JGLOBAL_TITLE','g.name',$listDirn,$listOrder);?>
                        </th>
                        <th scope="col" class="w-25">
                            <?php echo Text::_('COM_TZ_PORTFOLIO_CATEGORIES_ASSIGNMENT');?>
                        </th>
                        <th scope="col" class="w-1 text-center">
                            <?php echo Text::_('COM_TZ_PORTFOLIO_TOTAL_FIELDS');?>
                        </th>
                        <th scope="col" class="w-5 d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'g.access', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" class="w-1">
                            <?php echo HTMLHelper::_('searchtools.sort','JGRID_HEADING_ID','g.id',$listDirn,$listOrder);?>
                        </th>
                    </tr>
                    </thead>
                    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                    ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                    <?php
                        foreach ($this->items as $i => $item) {

                            $canEdit = $user->authorise('core.edit', 'com_tz_portfolio.group.' . $item->id);
                            $canEditOwn = $user->authorise('core.edit.own', 'com_tz_portfolio.group.' . $item->id)
                                && $item->created_by == $userId;
                            $canCheckin = $user->authorise('core.manage',     'com_checkin') ||
                                $item->checked_out == $userId || $item->checked_out == 0;
                            $canChange = ($user->authorise('core.edit.state', 'com_tz_portfolio.group.' . $item->id) ||
                                ($user->authorise('core.edit.state.own', 'com_tz_portfolio.group.' . $item->id)
                                    && $item->created_by == $userId)) && $canCheckin;
                            ?>
                        <tr class="row<?php echo ($i % 2 == 1) ? '1' : $i; ?>" data-draggable-group="0">
                            <td class="text-center">
                                <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->name); ?>
<!--                                --><?php //echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="order text-center d-none d-md-table-cell">
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
                                <input type="text" style="display:none" name="order[]" size="5"
                                       value="<?php echo $item->ordering; ?>" class="w-20 text-area-order "/>
                            </td>
                            <td class="text-center">
                                <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'groups.', $canChange, 'cb'); ?>
                            </td>
                            <th scope="row">
                                <?php if ($item -> checked_out){ ?>
                                    <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item -> editor, $item -> checked_out_time, 'groups.', $canCheckin); ?>
                                <?php } ?>
                                <?php if ($canEdit || $canEditOwn) { ?>
                                    <a href="index.php?option=com_tz_portfolio&task=group.edit&id=<?php echo $item->id; ?>">
                                        <?php echo $this->escape($item->name); ?>
                                    </a>
                                <?php } else { ?>
                                    <?php echo $this->escape($item->name); ?>
                                <?php } ?>
                            </th>
                            <td><?php
                                if (isset($item->categories) && $item->categories) {
                                    foreach ($item->categories as $j => $cat) {
                                        ?>
                                        <a href="index.php?option=com_tz_portfolio&task=group.edit&id=<?php echo $cat->id; ?>">
                                            <?php echo $cat->title; ?>
                                        </a>
                                        <?php if ($j < count($item->categories) - 1) { ?>
                                            <span>,</span>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </td>
                            <td class="center text-center">
                                <span class="badge badge-info"><?php echo $item->total; ?></span>
                            </td>
                            <td class="small d-none d-md-table-cell">
                                <?php echo $this->escape($item->access_level); ?>
                            </td>
                            <td class="text-center"><?php echo $item->id; ?></td>
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
