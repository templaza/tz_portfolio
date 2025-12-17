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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

///* @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
//$wa = $this -> document -> getWebAssetManager();
//
//$wa -> useStyle('com_tz_portfolio.style.system.style');

$doc    = Factory::getDocument();
$doc -> addScriptDeclaration('(function($){
    $(document).ready(function(){
        $(".tp-categories-grid-page .cat-child-btn").off("click").on("click", function(e){
            e.preventDefault();
            var btn = $(this),
                main = btn.closest(".tp-categories-grid-page"),
                parent = btn.closest(".cat-grid"),
                filter = btn.attr("href").replace(/^\#/gi, ""),
                items = parent.parent().find(".cat-grid.cat-faded"),
                items_filter = parent.siblings(":not([data-cat-filter=\""+filter+"\"])");

            if(typeof main.data("cat-on-click") !== typeof undefined && main.data("cat-on-click") !== parent.index()){
                items.removeClass("cat-faded");
                parent.parent().find(".cat-child-btn.cat-active").removeClass("cat-active");
            }
            
            items_filter.toggleClass("cat-faded");
//            items_filter.toggleClass("cat-faded", 500);
            btn.toggleClass("cat-active");

            main.data("cat-on-click", parent.index());
        });
    });
})(jQuery);');

?>
<div class="tp-categories-grid-page <?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>
<?php if ($this->params->get('show_base_description')) : ?>
	<?php 	//If there is a description in the menu parameters use that; ?>
		<?php if($this->params->get('categories_description')) : ?>
			<?php echo  HTMLHelper::_('content.prepare', $this->params->get('categories_description'), '', 'com_tz_portfolio.categories'); ?>
		<?php  else: ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($this->parent->description) : ?>
				<div class="category-desc">
					<?php  echo HTMLHelper::_('content.prepare', $this->parent->description, '', 'com_tz_portfolio.categories'); ?>
				</div>
			<?php  endif; ?>
		<?php  endif; ?>
	<?php endif; ?>

    <div class="cat-items cat-grids uk-child-width-1-3" data-uk-grid>
        <?php
        echo $this->loadTemplate('items');
        ?>
    </div>

</div>

