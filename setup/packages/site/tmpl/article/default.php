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

//JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

$app    = Factory::getApplication();

// Create shortcuts to some parameters.
$item       = $this -> item;
$params		= $item->params;
$images     = json_decode($item->images);
$urls       = json_decode($item->urls);
$canEdit	= $item->params->get('access-edit');
$user		= Factory::getApplication()->getIdentity();
$doc        = Factory::getDocument();

?>

<div class="tp-item-page item-page<?php echo $this->pageclass_sfx?>"  itemscope itemtype="http://schema.org/Article">
    <div class="tp-item-page__inner" data-uk-margin>
        <meta itemprop="inLanguage" content="<?php
        echo ($item->language === '*') ? Factory::getConfig()->get('language') : $item->language; ?>" />
        <?php if ($this->params->get('show_page_heading', 1)) : ?>
            <h1 class="uk-article-title tp-heading-title">
            <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        <?php endif; ?>

        <?php
        if($this -> generateLayout && !empty($this -> generateLayout)) {
            echo $this->generateLayout;
        }else{
            echo $this -> loadTemplate('media');
        ?>
            <?php if(($title = $this -> loadTemplate('title')) || ($icons = $this -> loadTemplate('icons'))):?>
                <div class="">
                    <?php echo $this -> loadTemplate('icons');?>
                    <?php echo $title;?>
                </div>
            <?php endif;?>

            <?php echo $item -> event -> beforeDisplayAdditionInfo; ?>

            <?php
                $author_info = $this -> loadTemplate('author');
                $created_date = $this -> loadTemplate('created_date');
                $category = $this -> loadTemplate('category');
                $hits = $this -> loadTemplate('hits');
                $published_date = $this -> loadTemplate('published_date');
                $modified_date = $this -> loadTemplate('modified_date');
            ?>
            <?php if($author_info || $created_date || $category || $hits || $published_date || $modified_date): ?>
            <div class="uk-article-meta">
                <?php if($author_info):?>
                    <?php echo $author_info;?>
                <?php endif;?>
                <?php if($created_date):?>
                    <?php echo $created_date;?>
                <?php endif;?>
                <?php if($category):?>
                    <?php echo $category;?>
                <?php endif;?>
                <?php if($hits):?>
                    <?php echo $hits;?>
                <?php endif;?>
                <?php if($published_date):?>
                    <?php echo $published_date;?>
                <?php endif;?>
                <?php if($modified_date):?>
                    <?php echo $modified_date;?>
                <?php endif;?>
            </div>
            <?php endif;?>

            <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

            <?php if($introtext = $this -> loadTemplate('introtext')):?>
            <div class="tpp-item-introtext uk-text-lead">
                <?php echo $introtext;?>
            </div>
            <?php endif;?>
            <?php if($fulltext = $this -> loadTemplate('fulltext')):?>
            <div class="tpp-item-fulltext">
                <?php echo $fulltext;?>
            </div>
            <?php endif;?>
            <?php if($extrafields = $this -> loadTemplate('extrafields')):?>
                <?php echo $extrafields;?>
            <?php endif;?>
            <?php if($tag = $this -> loadTemplate('tags')):?>
            <div class="tpp-item-tags">
                <?php echo $tag;?>
            </div>
            <?php endif;?>
            <?php
            //Call event onContentAfterDisplayArticleView on plugin
            echo $item->event->contentDisplayArticleView;
            ?>

            <?php if($related = $this -> loadTemplate('related')):?>
                <?php echo $related;?>
            <?php endif;?>

        <?php }?>

        <?php

        //Call event onContentAfterDisplay on plugin
//        echo $item->event->afterDisplayContent;
        ?>
    </div>
</div>
