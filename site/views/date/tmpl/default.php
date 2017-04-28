<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>

<div class="tzpp_bootstrap3 TzBlog blog<?php echo $this->pageclass_sfx;?>" itemscope itemtype="http://schema.org/Blog">
    <div class="TzBlogInner">
        <div class="row-fluid">
            <?php if ($this->params->get('show_page_heading', 1)) : ?>
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
            <?php endif; ?>

            <?php if ($this->params->get('page_subheading')) : ?>
            <h2 class="TzCategoryTitle">
                <?php echo $this->escape($this->params->get('page_subheading')); ?>
            </h2>
            <?php endif; ?>

            <?php if($this->params -> get('use_filter_first_letter',0)):?>
            <div class="TzLetters">
                <div class="breadcrumb">
                    <?php echo $this -> loadTemplate('letters');?>
                </div>
            </div>
            <?php endif;?>

            <?php $date = null;?>
            <?php if (!empty($this->items)) :
                ?>


            <div class="TzItemsRow row">
                <?php
                $col        = $this->params -> get('article_columns', 1);
                $cols       = TZ_Portfolio_PlusContentHelper::getBootstrapColumns($col);
                $colCounter = 0;

                foreach ($this->items as $key => &$item) : ?>

                    <?php if(isset($item -> date_group) AND !empty($item -> date_group)
                        AND $date != strtotime(date(JText::_('COM_TZ_PORTFOLIO_PLUS_DATE_FORMAT_LC3'),strtotime($item -> date_group))) ):?>
                    <div class="date-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="clearfix"></div>
                        <h2 class="text-info date"><?php echo JHtml::_('date',$item -> date_group,JText::_('COM_TZ_PORTFOLIO_PLUS_DATE_FORMAT_LC3'));?></h2>
                    </div>
                    <?php endif;?>
                        <div class="<?php echo ($cols && isset($cols[$colCounter]))?'col-md-'.$cols[$colCounter]
                            .(($i != 0 && $i % $col == 0)?' clr':''):'col-md-12'; ?>">
                            <div class="TzItem <?php echo $item->state == 0 ? ' system-unpublished' : null; ?>"
                                 itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                                <?php
                                    $this->item = &$item;
                                    echo $this->loadTemplate('item');
                                ?>
                            <div class="clr"></div>
                            </div>
                        </div>

                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($this->link_items)) : ?>
                <?php echo $this->loadTemplate('links'); ?>
            <?php endif; ?>
            <div class="clearfix"></div>

            <?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination', 1) == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
                <div class="pagination">
                    <?php echo $this->pagination->getPagesLinks(); ?>

                    <?php  if ($this->params->def('show_pagination_results', 1)) : ?>
                            <p class="TzCounter">
                                    <?php echo $this->pagination->getPagesCounter(); ?>
                            </p>
                    <?php endif; ?>
                </div>
            <?php  endif; ?>
            <div class="clearfix"></div>

        </div>
    </div>
</div>
