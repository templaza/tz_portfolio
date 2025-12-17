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

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

if($items = $this -> items){
    $pparams    = $this -> params;
//    $tpParams   = TZ_Portfolio_PlusTemplate::getTemplate(true) -> params;
//    $tpParams -> get('date_format', 'l, d F Y H:i')
    $date_format    = 'l, d F Y H:i';
    $enable_masonry = $pparams -> get('enable_masonry', 1);
?>
<div class="js-tp-portfolio uk-child-width-1-2 uk-child-width-1-3@m uk-margin-medium-top" data-uk-grid="<?php
echo $enable_masonry?' masonry: '.$pparams -> get('masonry_layout', 'pack'):'';?>">
    <?php
    foreach($this -> items as $i => $item){
        $this -> item   = $item;
        $params         = $item -> params;

        $tag_filter     = '';
        $cat_filter     = $item -> catid;
        if(isset($item -> second_categories) && $item -> second_categories &&  count($item -> second_categories)) {
            $cat_filter .= ' '.implode(' ', ArrayHelper::getColumn($item -> second_categories, 'id'));
        }
        if($params -> get('tz_filter_type','tags') == 'tags'){
            if($item -> tags && count($item -> tags)){
                $tag_filter  = implode(' ', ArrayHelper::getColumn($item -> tags, 'id'));
            }
        }
    ?>
    <div data-filter-category="<?php echo $cat_filter; ?>" data-name="<?php
    echo $item -> title;?>" data-date="<?php echo strtotime($item -> created);
    ?>" data-hits="<?php echo (int) $item -> hits; ?>"<?php
    echo !empty($tag_filter)?' data-filter-tag="'.$tag_filter.'"':''?> data-tp-id="<?php echo $item -> id; ?>">
        <div class="uk-card uk-card-default">
            <?php
            // Display media from plugin of group tz_portfolio_plus_mediatype
            echo $this -> loadTemplate('media');
            ?>
            <div class="uk-card-body">
                <?php if ($params -> get('access-edit')){ ?>
                     <div class="uk-float-right"><?php echo HTMLHelper::_('tpicon.edit', $item, $params); ?></div>
                <?php } ?>

                <?php if($params -> get('show_cat_title',1)){ ?>
                    <h2 class="tp-item-title uk-card-title" itemprop="name">
                        <?php if($params->get('cat_link_titles',1)){ ?>
                            <a href="<?php echo $item ->link; ?>"  itemprop="url" class="uk-link-heading">
                                <?php echo $this->escape($item -> title); ?>
                            </a>
                        <?php }else{ ?>
                            <?php echo $this->escape($item -> title); ?>
                        <?php } ?>
                    </h2>
                <?php } ?>


                <?php
                //-- Start display some information --//
                if ($params->get('show_cat_author',0) or $params->get('show_cat_category',0)
                    or $params->get('show_cat_create_date',0) or $params->get('show_cat_modify_date',0)
                    or $params->get('show_cat_publish_date',0) or $params->get('show_cat_parent_category',0)
                    or $params->get('show_cat_hits',0) or $params->get('show_cat_tags',0)
                    or !empty($item -> event -> beforeDisplayAdditionInfo)
                    or !empty($item -> event -> afterDisplayAdditionInfo)){
                    ?>
                    <div class="tp-portfolio__info uk-article-meta uk-margin-bottom">

                        <?php echo $item -> event -> beforeDisplayAdditionInfo;?>

                        <?php if ($params->get('show_cat_category',0)){ ?>
                            <?php $title = $this->escape($item->category_title);
                            $url = '<a href="' . $item -> category_link
                                . '" itemprop="genre" class="uk-link-text">' . $title . '</a>';
                            $lang_text  = 'COM_TZ_PORTFOLIO_CATEGORY';

                            if(isset($item -> second_categories) && $item -> second_categories
                                && count($item -> second_categories)){
                                $lang_text  = 'COM_TZ_PORTFOLIO_CATEGORIES';
                                foreach($item -> second_categories as $j => $scategory){
                                    if($j <= count($item -> second_categories)) {
                                        $title  .= ', ';
                                        $url    .= ', ';
                                    }
                                    $url    .= '<a href="' . $scategory -> link
                                        . '" itemprop="genre" class="uk-link-text">' . $scategory -> title . '</a>';
                                    $title  .= $this->escape($scategory -> title);
                                }
                            }?>
                            <div class="tp-item-category" itemprop="genre">
                                <span data-uk-icon="icon: folder; ratio: 0.75"></span>
                                <?php if ($params->get('cat_link_category',1)){?>
                                    <?php echo Text::sprintf($lang_text, $url); ?>
                                <?php }else{ ?>
                                    <?php echo Text::sprintf($lang_text, '<span itemprop="genre">' . $title . '</span>'); ?>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php if ($params->get('show_cat_parent_category', 0) && $item->parent_id != 1) : ?>
                            <div class="tp-item-parent-category">
                                <?php $title = $this->escape($item->parent_title);
                                $url = '<a href="' . Route::_(RouteHelper::getCategoryRoute($item->parent_id))
                                    . '" itemprop="genre" class="uk-link-text">' . $title . '</a>'; ?>
                                <?php if ($params->get('cat_link_parent_category', 1)) : ?>
                                    <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PARENT', $url); ?>
                                <?php else : ?>
                                    <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PARENT', '<span itemprop="genre">' . $title . '</span>'); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_create_date',0)) : ?>
                            <div class="tp-item-created" itemprop="dateCreated">
                                <span data-uk-icon="icon: calendar; ratio: 0.75"></span>
                                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_CREATED_DATE_ON', HTMLHelper::_('date', $item->created, $date_format)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_modify_date', 0)) : ?>
                            <div class="tp-item-modified" itemprop="dateModified">
                                <span data-uk-icon="icon: clock; ratio: 0.75"></span>
                                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_LAST_UPDATED', HTMLHelper::_('date', $item->modified, $date_format)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_publish_date',0)) : ?>
                            <div class="tp-item-published" itemprop="datePublished">
                                <span data-uk-icon="icon: future; ratio: 0.75"></span>
                                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PUBLISHED_DATE_ON', HTMLHelper::_('date', $item->publish_up, $date_format)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_author', 0) && !empty($item->author )) : ?>
                            <div class="tp-item-created-by" itemprop="author" itemscope itemtype="http://schema.org/Person">
                                <?php $author =  $item->author; ?>
                                <?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>
                                <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>

                                <span data-uk-icon="icon: user; ratio: 0.75"></span>
                                <?php if ($params->get('cat_link_author', 1)):?>
                                    <?php 	echo Text::sprintf('COM_TZ_PORTFOLIO_WRITTEN_BY' ,
                                        HTMLHelper::_('link', $item -> author_link, $author, array(
                                                'itemprop' => 'url', 'class' => 'uk-link-text'))); ?>
                                <?php else :?>
                                    <?php echo Text::sprintf('COM_TZ_PORTFOLIO_WRITTEN_BY', $author); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($params->get('show_cat_hits', 0)) : ?>
                            <div class="tp-item-hit">
                                <span data-uk-icon="icon: eye; ratio: 0.75"></span>
                                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_ARTICLE_HITS', $item->hits); ?>
                                <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $item->hits; ?>" />
                            </div>
                        <?php endif; ?>

                        <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

                    </div>
                <?php
                }
                //-- End display some information --//
                ?>

                <?php
                if(!$params -> get('show_cat_intro',1)) {
                    //Call event onContentAfterTitle on plugin
                    echo $item->event->afterDisplayTitle;
                }
                ?>

                <?php
                //Show vote
                echo $item -> event -> contentDisplayVote;
                ?>

                <?php
                //Call event onContentBeforeDisplay on plugin
                echo $item -> event -> beforeDisplayContent;
                ?>

                <?php  if ($params->get('show_cat_intro',1) AND !empty($item -> introtext)){?>
                    <div class="tp-item-introtext uk-margin-bottom" itemprop="description">
                        <?php echo $item -> introtext;?>
                    </div>
                <?php } ?>

                <?php echo $item -> event -> contentDisplayListView; ?>

                <?php echo $this -> loadTemplate('extrafields');?>

                <?php
                if ($params->get('show_cat_tags', 0)){
                    echo $this -> loadTemplate('tags');
                }
                ?>

                <?php if($params -> get('show_cat_readmore',1)){?>
                    <a class="uk-button uk-button-primary tp-item-readmore" href="<?php echo $item ->link; ?>">
                        <?php echo Text::sprintf('COM_TZ_PORTFOLIO_READ_MORE'); ?>
                    </a>
                <?php } ?>

                <?php
                //Call event onContentAfterDisplay on plugin
                echo $item->event->afterDisplayContent;
                ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php } ?>
