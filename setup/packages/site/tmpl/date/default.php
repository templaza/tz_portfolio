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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$col        = $this->params -> get('article_columns', 1);
?>

<div class="tp-date-article<?php
echo $this->pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
    <div class="TzBlogInner">
            <?php if ($this->params->get('show_page_heading', 1)) : ?>
            <h1 class="page-heading">
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
            <?php endif; ?>

            <?php if ($this->params->get('page_subheading')) : ?>
            <h2 class="tp-category-title">
                <?php echo $this->escape($this->params->get('page_subheading')); ?>
            </h2>
            <?php endif; ?>

            <?php if($this->params -> get('use_filter_first_letter',0)):?>
            <div class="tp-letters">
                <?php echo $this -> loadTemplate('letters');?>
            </div>
            <?php endif;?>

            <?php $date = null;?>
            <?php if (!empty($this->items)) :
                ?>

            <div class="tp-date-items uk-padding-small uk-padding-remove-horizontal<?php
            echo $col > 1?' uk-child-with-1-'.$col:'';?>"<?php
            echo $col > 1?' data-uk-grid':'';?> itemscope itemtype="http://schema.org/Blog" >
                <?php
                $col        = $this->params -> get('article_columns', 1);

                foreach ($this->items as $i => &$item) :
                    ?>

                    <?php if(isset($item -> date_group) AND !empty($item -> date_group)
                        AND $date != strtotime(date(Text::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC3'),strtotime($item -> date_group))) ):?>
                    <div class="tp-date-group uk-width-1-1">
                        <h2 class="tp-date-heading uk-heading-divider uk-display-inline-block uk-text-success uk-margin-top uk-margin-bottom"><?php echo HTMLHelper::_('date',
                                $item -> date_group,Text::_('COM_TZ_PORTFOLIO_DATE_FORMAT_LC3'));?></h2>
                    </div>
                    <?php endif;?>

                    <div class="tp-item uk-article <?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
                         itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                        <?php
                            $this->item = &$item;
                            echo $this->loadTemplate('item');
                        ?>
                    </div>

                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($this->link_items)) : ?>
                <?php echo $this->loadTemplate('links'); ?>
            <?php endif; ?>

            <?php if (($this->params->def('show_pagination', 1) == 1  ||
                    ($this->params->get('show_pagination', 1) == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
                <div class="tp-pagination">
                    <?php echo $this->pagination->getPagesLinks(); ?>

                    <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
                        <p class="tp-counter">
                            <?php echo $this->pagination->getPagesCounter(); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php  endif; ?>

    </div>
</div>
