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

$user		= Factory::getApplication() -> getIdentity();
$userId		= $user->get('id');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');
?>

<form action="index.php?option=com_tz_portfolio&view=tags" method="post" name="adminForm" id="adminForm">

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
            <table class="table" id="tagsList">
                <caption class="visually-hidden">
                    <?php echo Text::_('COM_TZ_PORTFOLIO_TAGS'); ?>,
                    <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                    <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                </caption>
                <thead>
                <tr>
                    <td class="w-1 text-center">
                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                    </td>
                    <th class="w-1 text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                        </th>
                    <th class="title">
                        <?php echo HTMLHelper::_('searchtools.sort','JGLOBAL_TITLE','title', $listDirn, $listOrder);?>
                    </th>
                    <th scope="col" class="w-10 d-none d-md-table-cell text-center">
                        <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_COUNT_TAGGED_ITEMS', 'countTaggedItems', $listDirn, $listOrder); ?>
                    </th>
                    <th class="w-1">
                        <?php echo HTMLHelper::_('searchtools.sort','JGRID_HEADING_ID','id', $listDirn, $listOrder);?>
                    </th>
                </tr>
                </thead>

                <?php if($this -> items):?>
                <tbody>
                <?php
                $canEdit    = $user->authorise('core.edit',       'com_tz_portfolio.tag');
                $canChange  = $user->authorise('core.edit.state', 'com_tz_portfolio.tag');
                foreach($this -> items as $i => $item):?>
                    <tr class="<?php echo ($i%2==0)?'row0':'row1';?>">
                        <td class="center text-center">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
<!--                        <td>-->
<!--                            --><?php //echo $i+1;?>
<!--                            <input type="hidden" name="order[]">-->
<!--                        </td>-->
                        <td class="center text-center">
                            <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'tags.', $canChange, 'cb'); ?>
                        </td>
                        <th class="nowrap has-context">
                            <div class="pull-left float-left">
                                <?php if($canEdit){ ?>
                                <a href="index.php?option=com_tz_portfolio&task=tag.edit&id=<?php echo $item -> id;?>">
                                    <?php echo $this -> escape($item -> title);?>
                                </a>
                                <?php }else{ ?>
                                    <?php echo $this -> escape($item -> title);?>
                                <?php } ?>
                                <span class="small">
                                    <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                                </span>
                            </div>
                        </th>
                        <td class="small d-none d-md-table-cell text-center">
                            <span class="badge bg-info">
                                <?php echo $item->countTaggedItems; ?>
                            </span>
                        </td>
                        <td class="text-center"><?php echo $item -> id;?></td>
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
        <input type="hidden" name="return" value="<?php echo base64_encode(Uri::getInstance() -> toString())?>">
        <?php echo HTMLHelper::_('form.token');?>
        </div>
    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
<?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</form>