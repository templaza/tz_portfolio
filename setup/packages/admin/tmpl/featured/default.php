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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Button\FeaturedButton;
use Joomla\CMS\Language\Associations;
use TemPlaza\Component\TZ_Portfolio\Administrator\Helper\ACLHelper;

$user		    = Factory::getUser();
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
	$saveOrderingUrl = 'index.php?option=com_tz_portfolio&task=articles.saveOrderAjax&tmpl=component';
        HTMLHelper::_('draggablelist.draggable');
}

$assoc		= Associations::isEnabled();

?>

<form action="<?php echo Route::_('index.php?option=com_tz_portfolio&view=articles');?>" method="post" name="adminForm" id="adminForm">

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
                <table class="table " id="articleList">
                    <caption class="visually-hidden">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_ARTICLES'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                    </caption>
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                            </th>
                            <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-1 text-center">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                            </th>
                            <th>
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                            </th>
                            <th class="w-6">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_TYPE_OF_MEDIA', 'groupname', $listDirn, $listOrder); ?>
                            </th>
                            <th class="w-10 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_GROUP', 'groupname', $listDirn, $listOrder); ?>
                            </th>
                            <th class="w-6 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                            </th>

                            <?php if ($assoc) : ?>
                            <th class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
                            </th>
                            <?php endif;?>

                            <th class="w-10 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JAUTHOR', 'a.created_by', $listDirn, $listOrder); ?>
                            </th>
                            <th class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
                            </th>
                            <th class="w-8 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
                            </th>
                            <th class="w-5 text-center d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
                            </th>
                            <th class="w-1 text-center d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_PRIORITY', 'a.priority', $listDirn, $listOrder); ?>
                                <?php
                                if($savePriority) {
                                    echo HTMLHelper::_('grid.order', $this->items, 'filesave.png', 'articles.savepriority');
                                }
                                ?>
                            </th>
                            <th class="w-1 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl;
                    ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
                    <?php
                    if($this -> items):
                        foreach ($this->items as $i => $item) :
                            $item->max_ordering = 0; //??
                            $ordering	    = ($listOrder == 'a.ordering');
                            $canCreate	    = $user->authorise('core.create',	  'com_tz_portfolio.category.'.$item->catid);
                            $canEdit	    = $user->authorise('core.edit',		  'com_tz_portfolio.article.'.$item->id);
                            $canCheckin	    = $user->authorise('core.manage',	  'com_checkin')
                                                || $item->checked_out == $userId || $item->checked_out == 0;
                            $canEditOwn	    = $user->authorise('core.edit.own', 'com_tz_portfolio.article.'.$item->id)
                                                && $item->created_by == $userId;
                            $canChange	    = ($user->authorise('core.edit.state', 'com_tz_portfolio.article.'.$item->id)
                                                ||($user->authorise('core.edit.state.own', 'com_tz_portfolio.article.'
                                                .$item->id)
                                                && $item->created_by == $userId)) && $canCheckin;
                            $canApprove     = ACLHelper::allowApprove($item);
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->catid; ?>">
                                <th class="center text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </th>
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
                                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
                                </td>
                                <td class="center text-center">
                                    <?php
                                    $workflow_featured = false;
                                    $options = [
                                        'task_prefix' => 'articles.',
                                        'disabled' => $workflow_featured || !$canChange,
                                        'id' => 'featured-' . $item->id
                                    ];

                                    echo (new FeaturedButton)
                                        ->render((int) $item->featured, $i, $options);
                                    ?>
                                </td>
                                <td class="center text-center">
                                    <?php
                                    $filterPublished    = $this -> state -> get('filter.published');
                                    ?>
                                    <?php
                                    if($canApprove && ($item -> state == 3 || $item -> state == 4) ){
                                        echo HTMLHelper::_('tpgrid.approve', $i, $this->getName() . '.', $canChange, 'cb');
                                        echo HTMLHelper::_('tpgrid.reject', $i, $this->getName() . '.', $canChange, 'cb');
                                    }elseif($item -> state != 4){
                                        if($item -> state == -3 || $item -> state == 3){
                                            echo HTMLHelper::_('jgrid.action', $i, 'trash',
                                                $this -> getName().'.', 'JTOOLBAR_TRASH', 'JTOOLBAR_TRASH', '', true, 'trash', $canChange);
                                        }else{
                                            echo HTMLHelper::_('tpgrid.status', $item->state, $i, $item -> state,
                                                $this -> getName().'.', $canChange, 'cb', $item->publish_up, $item->publish_down);
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="has-context">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php
                                    if(($canApprove && ($canEdit || $canEditOwn || $item -> state == 3 || $item -> state == 4)) ||
                                        (!$canApprove && ($canEditOwn || $item -> state == 3 || $item -> state == -3) && $item -> state != 4)){
                                        ?>
                                        <a href="<?php echo Route::_('index.php?option=com_tz_portfolio&task=article.edit&return='
                                            .$this -> getName().'&id='.$item->id);?>">
                                            <?php echo $this->escape($item->title); ?></a>
                                    <?php }else{ ?>
                                        <?php echo $this->escape($item->title); ?>
                                    <?php } ?>
                                    <?php if(isset($item -> rejected_id) && $item -> rejected_id && in_array($item -> state, array(-3,3,4))){ ?>
                                        <span class="label label-danger label-important"><?php echo Text::_('COM_TZ_PORTFOLIO_REJECTED'); ?></span>
                                    <?php } ?>
                                    <?php
                                    if($filterPublished === '*'){?>
                                        <?php if($item -> state == -3){ ?>
                                            <span class="label"><?php echo Text::_('COM_TZ_PORTFOLIO_DRAFT'); ?></span>
                                        <?php } ?>
                                        <?php if($item -> state == 3){ ?>
                                            <span class="label label-warning"><?php echo Text::_('COM_TZ_PORTFOLIO_PENDING'); ?>...</span>
                                        <?php } ?>
                                        <?php if($item -> state == 4){ ?>
                                            <span class="label label-info"><?php echo Text::_('COM_TZ_PORTFOLIO_UNDER_REVIEW'); ?></span>
                                        <?php } ?>
                                    <?php } ?>
                                    <div class="small">
                                        <div class="clearfix">
                                            <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                        </div>
                                        <div class="clearfix">
                                            <?php echo Text::_('COM_TZ_PORTFOLIO_MAIN_CATEGORY') . ": " ?>
                                            <a href="index.php?option=com_tz_portfolio&task=category.edit&id=<?php echo $item -> catid;?>"><?php echo $this->escape($item->category_title); ?></a>
                                        </div>
                                        <?php if(isset($item -> categories) && $item -> categories && count($item -> categories)):?>
                                        <div class="clearfix">
                                            <?php echo Text::_('COM_TZ_PORTFOLIO_SECONDARY_CATEGORY') . ": " ?>
                                            <?php foreach($item -> categories as $i => $category):?>
                                                <a href="index.php?option=com_tz_portfolio&task=category.edit&id=<?php echo $category -> id;?>"><?php echo $this->escape($category->title); ?></a>
                                                <?php
                                                if($i < count($item -> categories) - 1){
                                                    echo ',';
                                                }
                                                ?>
                                            <?php endforeach;?>
                                        </div>
                                        <?php endif;?>
                                    </div>
                                    <?php if(isset($item -> rejected_id) && $item -> rejected_id){ ?>
                                        <div class="tpp-reject__message">
                                            <strong><u><?php echo Text::_('COM_TZ_PORTFOLIO_REASON'); ?></u></strong>: <?php echo $item -> rejected_message; ?>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $item -> type;?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <a href="index.php?option=com_tz_portfolio&task=group.edit&id=<?php echo $item -> groupid?>">
                                        <?php echo $item -> groupname;?>
                                    </a>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $this->escape($item->access_level); ?>
                                </td>
                                <?php if ($assoc) : ?>
                                <td class="d-none d-md-table-cell">
                                    <?php if ($item->association) : ?>
                                        <?php echo HTMLHelper::_('tzcontentadmin.association', $item->id); ?>
                                    <?php endif; ?>
                                </td>
                                <?php endif;?>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $this->escape($item->author_name); ?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php if ($item->language=='*'):?>
                                        <?php echo Text::alt('JALL', 'language'); ?>
                                    <?php else:?>
                                        <?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED'); ?>
                                    <?php endif;?>
                                </td>
                                <td class="small nowrap d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                                </td>
                                <td class="center text-center d-none d-md-table-cell">
                                    <?php echo (int) $item->hits; ?>
                                </td>
                                <td class="nowrap d-none d-md-table-cell order" style="text-align: right;">
                                    <?php if ($savePriority){ ?>
                                        <div class="btn-group">
                                            <?php echo $this -> pagination -> orderUpIcon($i, true, 'articles.priorityup', 'Move Up');?>
                                            <?php if($orderDown = $this -> pagination -> orderDownIcon($i, $this -> pagination -> pagesTotal, true, 'articles.prioritydown')){
                                                echo $orderDown;
                                            }?>
                                        </div>
                                    <?php }
                                    ?>
                                    <input type="text" name="priority[]" class="width-auto text-center" min="0"<?php
                                    echo $savePriority ?  '' : ' disabled="disabled"';
                                    ?> style="margin-bottom: 0;" size="1" step="1" value="<?php
                                    echo (int) $item -> priority; ?>"/>
                                </td>
                                <td class="center text-center">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                            <?php endforeach;
                        endif;
                        ?>
                        </tbody>
                </table>

                <?php // load the pagination. ?>
                <?php echo $this->pagination->getListFooter(); ?>

            <?php } ?>

<!--            --><?php ////Load the batch processing form. ?>
<!--            --><?php //echo $this->loadTemplate('batch'); ?>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </div>
    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
<?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</form>
