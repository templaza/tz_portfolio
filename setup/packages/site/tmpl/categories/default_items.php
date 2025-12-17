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

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

$class = ' class="first"';

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
?>
<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
	<?php
	if ($this->params->get('show_empty_categories_cat', 0) || $item->numitems || count($item->getChildren())) :
	if (!isset($this->items[$this->parent->id][$id + 1]))
	{
		$class = ' class="last"';
	}
	?>
	<div<?php echo $class; ?>>
	<?php $class = ''; ?>
		<h3 class="uk-heading-small uk-heading-divider uk-article-title"><a href="<?php
            echo Route::_(RouteHelper::getCategoryRoute($item->id));?>">
			<?php echo $this->escape($item->title); ?></a>
			<?php if ($this->params->get('show_cat_num_articles_cat', 1) == 1) :?>
				<span class="uk-badge" data-uk-tooltip="<?php echo Text::_('COM_TZ_PORTFOLIO_NUM_ITEMS'); ?>">
					<?php echo Text::_('COM_TZ_PORTFOLIO_NUM_ITEMS'); ?><?php echo $item->numitems; ?>
				</span>
			<?php endif; ?>
			<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) : ?>
				<a href="#category-<?php echo $item->id;?>" data-toggle="collapse" data-toggle="button" class="btn btn-sm btn-default float-right"><i class="tps tp-plus-circle"></i></a>
			<?php endif;?>
		</h3>
        <?php if ($this->params->get('show_description_image',0) && $item->images) : ?>
            <img src="<?php echo $item->images; ?>" alt="<?php echo htmlspecialchars($item->title); ?>" />
        <?php endif; ?>
		<?php if ($this->params->get('show_subcat_desc_cat', 1)) :?>
		<?php if ($item->description) : ?>
			<div class="category-desc">
				<?php echo HTMLHelper::_('content.prepare', $item->description, '', 'com_tz_portfolio.categories'); ?>
			</div>
		<?php endif; ?>
        <?php endif; ?>

		<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) :?>
			<div class="collapse" id="category-<?php echo $item->id;?>">
			<?php
			$this->items[$item->id] = $item->getChildren();
			$this->parent = $item;
			$this->maxLevelcat--;
			echo $this->loadTemplate('items');
			$this->parent = $item->getParent();
			$this->maxLevelcat++;
			?>
			</div>
		<?php
		endif; ?>

	</div>
	<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
