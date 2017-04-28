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

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'ordering';

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_tz_portfolio_plus&task=fields.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'extraFieldList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = array('f.ordering' => JText::_('JGRID_HEADING_ORDERING'),
    'a.state' => JText::_('JSTATUS'),
    'f.title' => JText::_('COM_TZ_PORTFOLIO_PLUS_HEADING_TITLE'),
    'groupname' => JText::_('COM_TZ_PORTFOLIO_PLUS_GROUP'),
    'f.type' => JText::_('COM_TZ_PORTFOLIO_PLUS_HEADING_TYPE'),
    'f.published' => JText::_('JSTATUS'),
    'f.id' => JText::_('JGRID_HEADING_ID'));
?>
<script type="text/javascript">
    Joomla.orderTable = function() {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form id="adminForm" name="adminForm" method="post" action="index.php?option=com_tz_portfolio_plus&view=fields">
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
            <input type="text" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FIELDS_SEARCH_DESC');?>"
                   value="<?php echo $this -> state -> filter_search;?>"
                   placeholder="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SEARCH_IN_TITLE'); ?>"
                   id="filter_search"
                   name="filter_search"/>
        </div>
        <div class="btn-group pull-left">
            <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
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

    <div class="clearfix"> </div>


    <table class="table table-striped" id="extraFieldList">
        <thead>
        <tr>
            <th width="1%" class="nowrap center hidden-phone">
                <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', ($this -> state -> get('filter.group')?'ordering':'ordering'),
                    $listDirn, $listOrder, null,
                    'asc', 'JGRID_HEADING_ORDERING');?>
            </th>
            <th width="1%">
                <input type="checkbox" name="checkall-toggle"
                       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
            </th>
            <th width="1%" style="min-width:55px" class="nowrap center">
                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'f.published', $listDirn, $listOrder); ?>
            </th>
            <th><?php echo JHTML::_('grid.sort','COM_TZ_PORTFOLIO_PLUS_HEADING_TITLE','f.title'
                    ,$listDirn,$listOrder);?></th>
            <th width="20%"><?php echo JHTML::_('grid.sort','COM_TZ_PORTFOLIO_PLUS_GROUP','groupname'
                    ,$listDirn,$listOrder);?></th>
            <th width="10%"><?php echo JHTML::_('grid.sort','COM_TZ_PORTFOLIO_PLUS_TYPE','f.type'
                    ,$listDirn,$listOrder);?></th>
            <th width="5%"><?php echo JHTML::_('grid.sort','COM_TZ_PORTFOLIO_PLUS_LIST_VIEW_LABEL','f.list_view'
                    ,$listDirn,$listOrder);?></th>
            <th width="5%"><?php echo JHTML::_('grid.sort','COM_TZ_PORTFOLIO_PLUS_DETAILS_VIEW_LABEL','f.detail_view'
                    ,$listDirn,$listOrder);?></th>
            <th width="5%"><?php echo JHTML::_('grid.sort','COM_TZ_PORTFOLIO_PLUS_ADVANCED_SEARCH_LABEL','f.advanced_search'
                    ,$listDirn,$listOrder);?></th>
            <th nowrap="nowrap" width="1%"><?php echo JHTML::_('grid.sort','JGRID_HEADING_ID','f.id'
                    ,$listDirn,$listOrder);?></th>
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
        if($this -> items):
            foreach($this -> items as $i => $item):
                ?>
            <tr class="row<?php echo ($i%2==1)?'1':$i;?>"<?php echo ($group = $this -> state -> get('filter.group'))?'sortable-group-id="'.$group.'"':''?>>
                <td class="order nowrap center hidden-phone">
                    <?php
                    $disableClassName = '';
                    $disabledLabel	  = '';

                    if (!$saveOrder) :
                        $disabledLabel    = JText::_('JORDERINGDISABLED');
                        $disableClassName = 'inactive tip-top';
                    endif;
                    ?>
                    <span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
                        <i class="icon-menu"></i>
                    </span>
                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                </td>
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td class="center">
                    <div class="btn-group">
                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'fields.', true, 'cb'); ?>
                    </div>
                </td>
                <td class="nowrap has-context">
                    <div class="pull-left">
                        <a href="index.php?option=com_tz_portfolio_plus&task=field.edit&id=<?php echo $item -> id;?>">
                            <?php echo $item -> title;?>
                        </a>
                    </div>
                    <div class="pull-left">
                        <?php
                        // Create dropdown items
                        JHtml::_('dropdown.edit', $item->id, 'field.');
                        JHtml::_('dropdown.divider');
                        if ($item->published) :
                            JHtml::_('dropdown.unpublish', 'cb' . $i, 'fields.');
                        else :
                            JHtml::_('dropdown.publish', 'cb' . $i, 'fields.');
                        endif;
                        // render dropdown list
                        echo JHtml::_('dropdown.render');
                        ?>
                    </div>
                </td>
                <td class="small hidden-phone">
                    <?php echo $item -> groupname;?>
                </td>

                <td class="small hidden-phone"><?php echo $item -> type;?></td>
                <td class="center"><?php
                    $active_class   = ($item -> list_view)?'publish':'unpublish';
                    echo JHtml::_('fields.action', $i, ($item -> list_view == 1?'unlistview':'listview'), 'fields.', true, '', '', false, $active_class); ?></td>
                <td class="center"><?php
                    $dactive_class   = ($item -> detail_view)?'publish':'unpublish';
                    echo JHtml::_('fields.action', $i, ($item -> detail_view == 1?'undetailview':'detailview'), 'fields.', true, '', '', false, $dactive_class); ?></td>
                <td class="center"><?php
                    $advactive_class   = ($item -> advanced_search)?'publish':'unpublish';
                    echo JHtml::_('fields.action', $i, ($item -> advanced_search == 1?'unadvsearch':'advsearch'), 'fields.', true, '', '', false, $advactive_class); ?></td>
                <td class="center"><?php echo $item -> id;?></td>
            </tr>
        <?php
                endforeach;
        endif;
        ?>

        </tbody>

    </table>
    <input type="hidden" value="" name="task">
    <input type="hidden" value="0" name="boxchecked">
    <input type="hidden" value="<?php echo $listOrder;?>" name="filter_order">
    <input type="hidden" value="<?php echo $listDirn;?>" name="filter_order_Dir">
    <input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance() -> toString())?>">
    <?php echo JHTML::_('form.token');?>
</div>
</form>