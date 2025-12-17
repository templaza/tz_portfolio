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

if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0){
?>
<?php foreach($this->items[$this->parent->id] as $id => $item){
	if ($this->params->get('show_empty_categories_cat', 0) || $item->numitems || count($item->getChildren())) {
        if (!isset($this->items[$this->parent->id][$id + 1]))
        {
            $class = ' class="last"';
        }
?>
    <div class="cat-grid"<?php echo $item -> level > 1?' data-cat-filter="category-'.$this->parent->id.'"':''; ?>>
        <div class="uk-card uk-card-default uk-card-body">
            <?php if ($this->params->get('show_description_image',0) && $item->images) { ?>
                <div class="img">
                    <img src="<?php echo $item->images; ?>" alt="<?php echo htmlspecialchars($item->title); ?>" />
                </div>
            <?php } ?>

            <div class="cat-item-content">
                <?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) {
                    ?>
                    <a href="#category-<?php echo $item->id;?>" class="cat-child-btn uk-icon-button uk-float-right">
                        <i class="fas fa-chevron-left cat-caret-left"></i><i class="fas fa-chevron-down cat-caret-down"></i>
                    </a>
                <?php }?>
                <h2 class="title uk-card-title"><a href="<?php echo Route::_(RouteHelper::getCategoryRoute($item->id));
                ?>"><?php echo $this->escape($item->title); ?></a>
                    <?php if ($this->params->get('show_cat_num_articles_cat', 1) == 1){?>
                        <span class="uk-badge cat-badge" title="<?php echo Text::_('COM_TZ_PORTFOLIO_NUM_ITEMS'); ?>">
                    <?php echo $item->numitems; ?>
                    </span>
                    <?php } ?>
                </h2>

                <?php if ($this->params->get('show_subcat_desc_cat', 1) && $item->description) {?>
                    <div class="category-desc">
                        <?php echo HTMLHelper::_('content.prepare', $item->description, '', 'com_tz_portfolio_plus.categories'); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>


    <?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1){ ?>
        <?php
        $this->items[$item->id] = $item->getChildren();
        $this->parent = $item;
        $this->maxLevelcat--;
        echo $this->loadTemplate('items');
        $this->parent = $item->getParent();
        $this->maxLevelcat++;
        ?>
    <?php }
	} ?>
<?php }
} ?>
