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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;

$user		= Factory::getApplication() -> getIdentity();

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');
?>
<form action="index.php?option=com_tz_portfolio&view=<?php echo $this -> getName();
?>" method="post" name="adminForm" id="adminForm">

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

                <table class="table" id="templatesList">
                    <caption class="visually-hidden">
                        <?php echo Text::_('COM_TZ_PORTFOLIO_TEMPLATE_STYLES'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                    </caption>
                    <thead>
                    <tr>
                        <td class="w-1"></td>
                        <th class="title">
                            <?php echo HTMLHelper::_('searchtools.sort','COM_TEMPLATES_HEADING_STYLE','name',$listDirn,$listOrder);?>
                        </th>
                        <th class="w-1 nowrap center text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_TEMPLATES_HEADING_DEFAULT', 'home', $listDirn, $listOrder); ?>
                        </th>
                        <th class="w-1">
                            <?php echo Text::_('COM_TZ_PORTFOLIO_HEADING_ASSIGNED'); ?>
                        </th>
                        <th nowrap="nowrap" class="center text-center w-15">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_TZ_PORTFOLIO_TEMPLATE', 'template', $listDirn, $listOrder); ?>
                        </th>
                        <th nowrap="nowrap" class="w-1">
                            <?php echo HTMLHelper::_('searchtools.sort','JGRID_HEADING_ID','id',$listDirn,$listOrder);?>
                        </th>
                    </tr>
                    </thead>

                    <?php if($this -> items):?>
                        <tbody>
                        <?php foreach($this -> items as $i => $item):

                            $canCreate = $user->authorise('core.create',     'com_tz_portfolio.style');
                            $canEdit   = $user->authorise('core.edit',       'com_tz_portfolio.style');
                            $canChange = $user->authorise('core.edit.state', 'com_tz_portfolio.style');

                        ?>
                            <tr class="<?php echo ($i%2==0)?'row0':'row1';?>">
                                <td class="center text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="nowrap has-context">
                                    <div class="pull-left float-left">
                                        <?php if($canEdit){ ?>
                                        <a href="index.php?option=com_tz_portfolio&task=layout.edit&id=<?php echo $item -> id;?>">
                                            <?php echo $this -> escape($item -> title);?>
                                        </a>
                                        <?php }else{ ?>
                                            <?php echo $this -> escape($item -> title);?>
                                        <?php } ?>
                                    </div>
                                </td>

                                <td class="center text-center">
                                    <?php if ($item->home == '0' || $item->home == '1'):?>
                                        <?php echo HTMLHelper::_('jgrid.isdefault', $item->home != '0', $i,
                                            $this -> getName().'.', $canChange && $item->home != '1');?>
                                    <?php elseif ($canChange):?>
                                        <a href="<?php echo Route::_('index.php?option=com_tz_portfolio&task='
                                            .$this -> getName().'.unsetDefault&cid[]='
                                            .$item->id.'&'.Session::getFormToken().'=1');?>">
                                            <?php echo HTMLHelper::_('image', 'mod_languages/'.$item->image.'.gif',
                                                $item->language_title,
                                                array('title' => Text::sprintf('COM_TEMPLATES_GRID_UNSET_LANGUAGE',
                                                    $item->language_title)), true);?>
                                        </a>
                                    <?php else:?>
                                        <?php echo HTMLHelper::_('image', 'mod_languages/'.$item->image.'.gif',
                                            $item->language_title, array('title' => $item->language_title), true);?>
                                    <?php endif;?>
                                </td>

                                <td class="center text-center">
                                    <?php if((isset($item -> category_assigned) AND $item -> category_assigned > 0)
                                        OR (isset($item -> content_assigned) AND $item -> content_assigned > 0)
                                        OR (isset($item -> menu_assigned) AND $item -> menu_assigned > 0)):?>
                                    <i class="icon-ok tip hasTooltip" title="<?php
                                    echo Text::plural('COM_TZ_PORTFOLIO_ASSIGNED_MORE',$item -> menu_assigned,
                                        $item->category_assigned,$item -> content_assigned); ?>"></i>
                                    <?php endif;?>
                                </td>
                                <td class="center text-center"><?php echo $item -> template;?></td>
                                <td class="center text-center"><?php echo $item -> id;?></td>
                            </tr>
                        <?php endforeach;?>
                        </tbody>
                    <?php endif;?>

                </table>

                <?php // load the pagination. ?>
                <?php echo $this->pagination->getListFooter(); ?>

            <?php } ?>
        </div>
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token');?>
    <?php echo HTMLHelper::_('tzbootstrap.endcontainer');?>
<?php echo HTMLHelper::_('tzbootstrap.endrow');?>
</form>