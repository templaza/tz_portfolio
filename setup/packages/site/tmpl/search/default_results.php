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

//no direct access
defined('_JEXEC') or die();

use TemPlaza\Component\TZ_Portfolio\Site\Helper\ArticleHelper;

if($this -> items):
    $params     = &$this -> params;
    $col        = $params -> get('article_columns', 1);
?>
    <div class="tp-search-results uk-padding-small uk-padding-remove-horizontal"<?php
    echo $col > 1?' data-uk-grid':'';?> itemscope itemtype="http://schema.org/Blog" >
        <?php
        
        foreach($this -> items as $i => $item) {
            $this->item = $item;
        ?>
            <div class="uk-article" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
            <?php echo $this->loadTemplate('item'); ?>
            </div>
        <?php
        }
        ?>
    </div>

    <?php if (($params->def('show_pagination', 1) == 1
        || ($params->get('show_pagination', 1) == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="tp-pagination">
            <?php  if ($params->def('show_pagination_results', 1)) : ?>
                <p class="tp-counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
            <?php endif; ?>

            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    <?php endif;?>
<?php
endif;