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
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Multilanguage;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper as TZ_PortfolioHelperRoute;

$app = Factory::getApplication();
//HTMLHelper::_('formbehavior.chosen', 'select');

if ($app-> isClient('site'))
{
	Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));
}

$extension	= $this->escape($this->state->get('filter.extension'));
$function  	= $app->input->getCmd('function', 'jSelectCategory');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_tz_portfolio&view=categories&layout=modal&tmpl=component&function='
    . $function . '&' . Session::getFormToken() . '=1'); ?>" method="post" name="adminForm" id="adminForm" class="tpContainer">

    <?php
    // Search tools bar
    echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>

    <?php if (empty($this->items)){ ?>
        <div class="alert alert-no-items">
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
                <th scope="col" class="w-1 text-center">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                </th>
                <th scope="col">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th scope="col" class="w-10 d-none d-md-table-cell">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
                </th>
                <th scope="col" class="w-15 d-none d-md-table-cell">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
                </th>
                <th scope="col" class="w-1 d-none d-md-table-cell">
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
		<tbody>
        <?php
        $iconStates = array(
            -2 => 'icon-trash',
            0  => 'icon-unpublish',
            1  => 'icon-publish',
            2  => 'icon-archive',
        );
        ?>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php if ($item->language && Multilanguage::isEnabled())
				{
					$tag = strlen($item->language);
					if ($tag == 5)
					{
						$lang = substr($item->language, 0, 2);
					}
					elseif ($tag == 6)
					{
						$lang = substr($item->language, 0, 3);
					}
					else
					{
						$lang = "";
					}
				}
				elseif (!Multilanguage::isEnabled())
				{
					$lang = "";
				}
				?>
				<tr class="row<?php echo $i % 2; ?>">
                    <td class="center text-center">
                        <span class="<?php echo $iconStates[$this->escape($item->published)]; ?>" aria-hidden="true"></span>
                    </td>
					<td>
						<?php echo str_repeat('<span class="gi">&mdash;</span>', $item->level - 1) ?>
						<a href="javascript:void(0);"
						   onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>',
							   '<?php echo $this->escape(addslashes($item->title)); ?>', null,
							   '<?php echo $this->escape(TZ_PortfolioHelperRoute::getCategoryRoute($item->id, $item->language)); ?>',
							   '<?php echo $this->escape($lang); ?>', null);">
							<?php echo $this->escape($item->title); ?>
						</a>
					</td>
					<td class="small hidden-phone">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="center text-center nowrap">
						<?php if ($item->language == '*'): ?>
							<?php echo Text::alt('JALL', 'language'); ?>
						<?php else: ?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : Text::_('JUNDEFINED'); ?>
						<?php endif; ?>
					</td>
					<td class="center text-center hidden-phone">
						<?php echo (int) $item->id; ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
    <?php } ?>

	<input type="hidden" name="extension" value="<?php echo $extension; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
