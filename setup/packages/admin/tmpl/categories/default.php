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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Multilanguage;

$user		= Factory::getUser();
$userId		= $user->get('id');
$extension	= $this->escape($this->state->get('filter.extension'));
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$saveOrder 	= ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tz_portfolio&task=categories.saveOrderAjax&tmpl=component';

    HTMLHelper::_('draggablelist.draggable');
}

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');
?>
<form action="<?php echo Route::_('index.php?option=com_tz_portfolio&view=categories');?>" method="post" name="adminForm" id="adminForm">

<?php echo HTMLHelper::_('tzbootstrap.addrow');?>
    <?php echo HTMLHelper::_('tzbootstrap.startcontainer', '10', false);?>
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

                <table class="table" id="categoryList">
                    <caption class="visually-hidden">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_CATEGORIES'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                    </caption>
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', '', 'a.lft', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                            </th>
                            <th scope="col" class="w-1 text-center">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                            </th>
                            <th>
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                            </th>
                            <th class="center text-center d-none d-md-table-cell">
                                <?php echo Text::_('COM_TZ_PORTFOLIO_INHERITS_PARAMETERS_FROM'); ?>
                            </th>
                            <th class="w-20">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_GROUP', 'groupname', $listDirn, $listOrder);?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
                            </th>
                            <?php if (Multilanguage::isEnabled()){ ?>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
                                </th>
                            <?php } ?>
                            <th class="w-5 nowrap d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                    ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                        <?php
                        $originalOrders = array();
                        foreach ($this->items as $i => $item) :
                            $canEdit    = $user->authorise('core.edit',       $extension . '.category.' . $item->id);
                            $canCheckin = $user->authorise('core.admin',      'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                            $canEditOwn = $user->authorise('core.edit.own',   $extension . '.category.' . $item->id) && $item->created_user_id == $userId;
                            $canChange  = ($user->authorise('core.edit.state', $extension . '.category.' . $item->id) ||
                                    ($user->authorise('core.edit.state.own', $extension . '.category.' . $item->id)
                                        && $item->created_user_id == $userId)) && $canCheckin;

                            // Get the parents of item for sorting
                            if ($item->level > 1)
                            {
                                $parentsStr = "";
                                $_currentParentId = $item->parent_id;
                                $parentsStr = " ".$_currentParentId;
                                for ($j = 0; $j < $item->level; $j++)
                                {
                                    foreach ($this->ordering as $k => $v)
                                    {
                                        $v = implode("-", $v);
                                        $v = "-".$v."-";
                                        if (strpos($v, "-" . $_currentParentId . "-") !== false)
                                        {
                                            $parentsStr .= " ".$k;
                                            $_currentParentId = $k;
                                            break;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $parentsStr = "";
                            }
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php
                            echo $item->parent_id; ?>" data-item-id="<?php echo $item->id
                            ?>" data-parents="<?php echo $parentsStr?>" data-level="<?php echo $item->level?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
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
                                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->lft; ?>" />
                                </td>
                                <td class="center text-center">
                                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'categories.', $canChange);?>
                                </td>
                                <th scope="row">
                                    <?php echo LayoutHelper::render('joomla.html.treeprefix', array('level' => $item->level)); ?>
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit || $canEditOwn) : ?>
                                        <a href="<?php echo Route::_('index.php?option=com_tz_portfolio&task=category.edit&id='.$item->id.'&extension='.$extension);?>">
                                            <?php echo $this->escape($item->title); ?></a>
                                    <?php else : ?>
                                        <?php echo $this->escape($item->title); ?>
                                    <?php endif; ?>
                                    <span class="small" title="<?php echo $this->escape($item->path);?>">
                                        <?php if (empty($item->note)) : ?>
                                            <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                                        <?php else : ?>
                                            <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
                                        <?php endif; ?>
                                    </span>
                                </th>
                                <td class="center text-center"><?php echo $item -> inheritFrom;?></td>
                                <td class="small hidden-phone">
                                    <a href="index.php?option=com_TZ_PORTFOLIO&task=group.edit&id=<?php echo $item -> groupid;?>"><?php echo $item -> groupname;?></a>
                                </td>
                                <td class="small hidden-phone">
                                    <?php echo $this->escape($item->access_level); ?>
                                </td>

                                <?php if (Multilanguage::isEnabled()){ ?>
                                    <td class="small d-none d-md-table-cell">
                                        <?php echo LayoutHelper::render('joomla.content.language', $item); ?>
                                    </td>
                                <?php  } ?>
                                <td class="center text-center">
                                    <span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
                                        <?php echo (int) $item->id; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php // load the pagination. ?>
                <?php echo $this->pagination->getListFooter(); ?>

<!--                --><?php ////Load the batch processing form. ?>
<!--                --><?php //echo HTMLHelper::_(
//                    'bootstrap.renderModal',
//                    'collapseModal',
//                    array(
//                        'title'  => Text::_('COM_TZ_PORTFOLIO_CATEGORIES_BATCH_OPTIONS'),
////                        'footer' => $this->loadTemplate('batch_footer'),
//                    ),
//                    $this->loadTemplate('batch_body')
//                ); ?>
                <?php // Load the batch processing form. ?>
                <?php
                if (
                    $user->authorise('core.create', $extension)
                    && $user->authorise('core.edit', $extension)
                    && $user->authorise('core.edit.state', $extension)
                ) : ?>
                    <template id="joomla-dialog-batch"><?php echo $this->loadTemplate('batch_body'); ?></template>
                <?php endif; ?>

            <?php }?>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="original_order_values" value="<?php echo is_array($originalOrders)?implode(',', $originalOrders):''; ?>" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </div>
    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
<?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</form>
