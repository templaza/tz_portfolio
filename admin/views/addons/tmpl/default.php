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

JHtml::_('formbehavior.chosen', 'select');

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$user		= JFactory::getUser();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_tz_portfolio_plus.addons');
$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=addons.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'addonList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != "' . $listOrder . '")
		{
			dirn = "asc";
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, "");
	};
');
?>
<form action="index.php?option=com_tz_portfolio_plus&view=addons" method="post" name="adminForm"
      class="tz_portfolio_plus-addons"
      id="adminForm">
    <?php if(!empty($this -> sidebar)):?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this -> sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php else:?>
        <div id="j-main-container">
            <?php endif;?>

            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label for="search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL')?></label>
                    <input type="text" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TAGS_SEARCH_DESC');?>"
                           value="<?php echo $this -> escape($this -> state -> get('filter.search'));?>"
                           placeholder="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SEARCH_IN_NAME'); ?>"
                           id="filter_search"
                           name="filter_search"/>
                </div>
                <div class="btn-group pull-left">
                    <button type="submit" class="btn hasTooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button onclick="document.getElementById('filter_search').value='';this.form.submit();"
                            type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" >
                        <i class="icon-remove"></i>
                    </button>
                </div>

                <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                    <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                        <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                        <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                    </select>
                </div>
                <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                        <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                    </select>
                </div>
            </div>

            <table class="table table-striped"  id="addonList">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', '<span class="icon-menu-2"></span>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle"
                               title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                    </th>
                    <th class="title">
                        <?php echo JHtml::_('grid.sort','JGLOBAL_TITLE','name',$listDirn,$listOrder);?>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_TZ_PORTFOLIO_PLUS_TYPE', 'folder', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%" class="nowrap center">
                        <?php echo JHtml::_('grid.sort', 'COM_TZ_PORTFOLIO_PLUS_ELEMENT', 'element', $listDirn, $listOrder); ?>
                    </th>
                    <th class="nowrap center" width="10%">
                        <?php echo JText::_('JVERSION'); ?>
                    </th>
                    <th class="nowrap center" width="15%">
                        <?php echo JText::_('JDATE'); ?>
                    </th>
                    <th class="nowrap" width="25%">
                        <?php echo JText::_('JAUTHOR'); ?>
                    </th>
                    <th class="nowrap" width="1%">
                        <?php echo JHtml::_('grid.sort','JGRID_HEADING_ID','id',$listDirn,$listOrder);?>
                    </th>
                </tr>
                </thead>

                <?php if($this -> items):?>
                    <tbody>
                    <?php foreach($this -> items as $i => $item):

                        $canCreate = $user->authorise('core.create',     'com_tz_portfolio_plus');
                        $canEdit   = $user->authorise('core.edit',       'com_tz_portfolio_plus');
                        $canCheckin = $user->authorise('core.manage',     'com_tz_portfolio_plus')
                            || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                        $canChange = $user->authorise('core.edit.state', 'com_tz_portfolio_plus');

                        ?>
                        <tr class="<?php echo ($i%2==0)?'row0':'row1';?>" sortable-group-id="<?php echo $item->folder?>">
                            <td class="order nowrap center hidden-phone">
                                <?php
                                $iconClass = '';
                                if (!$canChange)
                                {
                                    $iconClass = ' inactive';
                                }
                                elseif (!$saveOrder)
                                {
                                    $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                                }
                                ?>
                                <span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
                                <?php if ($canChange && $saveOrder) : ?>
                                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                                <?php endif; ?>
                            </td>
                            <td class="center">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="center">
                                <div class="btn-group">
                                <?php
                                $states	= array(
                                    2 => array(
                                        '',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_PROTECTED',
                                        '',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_PROTECTED',
                                        true,
                                        'protected',
                                        'protected',
                                    ),
                                    1 => array(
                                        'unpublish',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_ENABLED',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_DISABLE',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_ENABLED',
                                        true,
                                        'publish',
                                        'publish',
                                    ),
                                    0 => array(
                                        'publish',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_DISABLED',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_ENABLE',
                                        'COM_TZ_PORTFOLIO_PLUS_ADDON_DISABLED',
                                        true,
                                        'unpublish',
                                        'unpublish',
                                    ),
                                );

                                echo JHtml::_('jgrid.state', $states, $item->published, $i, 'addons.', true, true, 'cb');
                                if($item ->protected) {
                                    echo JHtml::_('jgrid.state', $states, 2, $i, 'addon.', false, true, 'cb');
                                }
                                ?>
                                </div>
                            </td>
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'addons.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <a href="index.php?option=com_tz_portfolio_plus&task=addon.edit&id=<?php echo $item -> id;?>">
                                    <?php echo $item->name; ?>
                                    </a>

                                    <?php
                                    if($item -> data_manager){
                                    ?>
                                        <a href="<?php echo JRoute::_(TZ_Portfolio_PlusHelperAddon_Datas::getRootURL($item -> id));?>"
                                           class="btn btn-small hasTooltip"
                                           title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_DATA_MANAGER')?>">
                                            <span class="icon-book"></span><span><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_DATA_MANAGER')?></span>
                                        </a>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </td>
                            <td class="center">
                                <?php echo $item -> folder;?>
                            </td>
                            <td class="center">
                                <?php echo $item -> element;?>
                            </td>
                            <td class="center hidden-phone">
                                <?php echo @$item -> version != '' ? $item -> version : '&#160;';?>
                            </td>
                            <td class="center hidden-phone">

                                <?php echo @$item-> creationDate != '' ? $item-> creationDate : '&#160;'; ?>
                            </td>
                            <td class="hidden-phone">
                                <span class="editlinktip hasTooltip" title="<?php echo JHtml::tooltipText(JText::_('COM_TZ_PORTFOLIO_PLUS_AUTHOR_INFORMATION'), $item -> author_info, 0); ?>">
                                    <?php echo @$item->author != '' ? $item->author : '&#160;'; ?>
                                </span>
                            </td>

                            <td align="center hidden-phone"><?php echo $item -> id;?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                <?php endif;?>

                <tfoot>
                <tr>
                    <td colspan="11">
                        <?php echo $this -> pagination -> getListFooter();?>
                    </td>
                </tr>
                </tfoot>

            </table>

            <input type="hidden" name="task" value="">
            <input type="hidden" name="boxchecked" value="0">
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
            <?php echo JHtml::_('form.token');?>

        </div>
</form>