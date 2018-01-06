<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access');

//JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'g.ordering';

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=groups.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'groups', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>

<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_tz_portfolio_plus&view=groups">
    <?php if(!empty($this -> sidebar) AND COM_TZ_PORTFOLIO_PLUS_JVERSION_COMPARE):?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this -> sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else:?>
        <div id="j-main-container">
    <?php endif;?>
        <div class="tpContainer">

            <?php
            // Search tools bar
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>

            <?php if (empty($this->items)){ ?>
                <div class="alert alert-no-items">
                    <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php }else{ ?>
            <table class="table table-striped" id="groups">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', '', 'g.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="1%" style="min-width:55px" class="nowrap center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'g.published', $listDirn, $listOrder); ?>
                    </th>
                    <th class="title">
                        <?php echo JHTML::_('searchtools.sort','JGLOBAL_TITLE','g.name',$listDirn,$listOrder);?>
                    </th>
                    <th class="title">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ASSIGNMENT');?>
                    </th>
                    <th class="title">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TOTAL_FIELDS');?>
                    </th>
                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'g.access', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" nowrap="nowrap">
                        <?php echo JHTML::_('searchtools.sort','JGRID_HEADING_ID','g.id',$listDirn,$listOrder);?>
                    </th>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $this -> pagination -> getListFooter();?>
                    </td>
                </tr>
                </tfoot>

                <tbody>
                <?php
                    foreach ($this->items as $i => $item) {

                        $canEdit = $user->authorise('core.edit', 'com_tz_portfolio_plus.group.' . $item->id);
                        $canEditOwn = $user->authorise('core.edit.own', 'com_tz_portfolio_plus.group.' . $item->id)
                            && $item->created_by == $userId;
                        $canCheckin = $user->authorise('core.manage',     'com_checkin') ||
                            $item->checked_out == $userId || $item->checked_out == 0;
                        $canChange = ($user->authorise('core.edit.state', 'com_tz_portfolio_plus.group.' . $item->id) ||
                            ($user->authorise('core.edit.state.own', 'com_tz_portfolio_plus.group.' . $item->id)
                                && $item->created_by == $userId)) && $canCheckin;
                        ?>
                    <tr class="row<?php echo ($i % 2 == 1) ? '1' : $i; ?>">
                        <td class="order nowrap center hidden-phone">
                            <?php
                            if ($canChange) {
                                $disableClassName = '';
                                $disabledLabel = '';
                                if (!$saveOrder) {
                                    $disabledLabel = JText::_('JORDERINGDISABLED');
                                    $disableClassName = 'inactive tip-top';
                                }
                                ?>
                                <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
                                      title="<?php echo $disabledLabel ?>">
                            <i class="icon-menu"></i>
                        </span>
                            <?php } else {
                                ?>
                                <span class="sortable-handler inactive">
                                <i class="icon-menu"></i>
                            </span>
                            <?php } ?>
                            <input type="text" style="display:none" name="order[]" size="5"
                                   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
                        </td>
                        <td>
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center">
                            <div class="btn-group">
                                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'groups.', $canChange, 'cb'); ?>
                            </div>
                        </td>
                        <td class="nowrap has-context">
                            <?php if ($item -> checked_out){ ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item -> editor, $item -> checked_out_time, 'groups.', $canCheckin); ?>
                            <?php } ?>
                            <?php if ($canEdit || $canEditOwn) { ?>
                                <a href="index.php?option=com_tz_portfolio_plus&task=group.edit&id=<?php echo $item->id; ?>">
                                    <?php echo $this->escape($item->name); ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $this->escape($item->name); ?>
                            <?php } ?>
                        </td>
                        <td><?php
                            if (isset($item->categories) && $item->categories) {
                                foreach ($item->categories as $j => $cat) {
                                    ?>
                                    <a href="index.php?option=com_tz_portfolio_plus&task=category.edit&id=<?php echo $cat->id; ?>">
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
                        <td><?php echo $item->total; ?></td>
                        <td class="small hidden-phone">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td align="center"><?php echo $item->id; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php }?>

            <input type="hidden" value="" name="task">
            <input type="hidden" value="0" name="boxchecked">
            <input type="hidden" value="<?php echo $listOrder;?>" name="filter_order">
            <input type="hidden" value="<?php echo $listDirn;?>" name="filter_order_Dir">
            <input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance() -> toString())?>">
            <?php echo JHTML::_('form.token');?>
        </div>
    </div>
</form>
