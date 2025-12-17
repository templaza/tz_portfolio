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
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

if (Factory::getApplication()-> isClient('site')) {
	Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
}

$function	= Factory::getApplication() -> input -> getCmd('function', 'jSelectTag');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_tz_portfolio_plus&view=tags&layout=modal&tmpl=component&function='
    .$function.'&'.Session::getFormToken().'=1');?>"
      method="post" name="adminForm" id="adminForm" class="form-inline tpContainer">

    <?php
    // Search tools bar
    echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>

    <?php if (empty($this->items)){ ?>
        <div class="alert alert-no-items">
            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php }else{ ?>

	<table class="table table-sm">
        <caption class="visually-hidden">
            <?php echo Text::_('COM_TZ_PORTFOLIO_TAGS'); ?>,
            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
        </caption>
		<thead>
			<tr>
                <th class="w-10">#</th>
                <th class="title">
                    <?php echo HTMLHelper::_('searchtools.sort','JGLOBAL_TITLE','name', $listDirn, $listOrder);?>
                </th>
                <th class="w-1">
                    <?php echo HTMLHelper::_('searchtools.sort','JGRID_HEADING_ID','id', $listDirn, $listOrder);?>
                </th>
            </tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<?php if($this -> items):?>
        <tbody>
            <?php $i=0;?>
            <?php foreach($this -> items as $item):?>
                <tr class="row<?php echo $i%2;?>">
                    <td><?php echo $i+1;?></td>
                    <td>
                        <a style="cursor: pointer;" class="pointer hasTooltip"
                           data-placement="bottom"
                           data-original-title="<?php echo $item -> title;?>"
                           onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '', null, 'index.php?option=com_tz_portfolio_plus&view=tags&id=<?php echo $item -> id;?>');"
                        >
                            <?php echo $item -> title;?>
                        </a>
                            <span class="small">
                                <?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
                            </span>
                    </td>
                    <td class="text-center"><?php echo $item -> id;?></td>
                </tr>
            <?php $i++;?>
            <?php endforeach;?>
        </tbody>
        <?php endif;?>
	</table>
    <?php }?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>