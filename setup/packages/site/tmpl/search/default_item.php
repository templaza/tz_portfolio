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

defined('_JEXEC') or die();

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

if($item = $this -> item):
    $params         = $this -> item -> params;
?>

    <?php
    if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){
    // Start Description and some info
    ?>
        <?php
        // Display media from plugin of group tz_portfolio_mediatype
        echo $this -> loadTemplate('item_media');
        ?>

<!--        <div class="uk-card-header">-->
            <?php if ($params -> get('access-edit')){ ?>
                <div class="uk-float-right"><?php echo HTMLHelper::_('icon.edit', $item, $params); ?></div>
            <?php } ?>

            <?php if($params -> get('show_search_title',1)){ ?>
            <h3 class="tp-article-title uk-article-title" itemprop="name">
                <?php if($params->get('cat_link_titles',1)) { ?>
                    <a href="<?php echo $item ->link; ?>"  itemprop="url" class="uk-link-reset">
                        <?php echo $this->escape($item -> title); ?>
                    </a>
                <?php }else { ?>
                    <?php echo $this->escape($item -> title); ?>
                <?php } ?>
            </h3>
            <?php }?>

            <?php
            //-- Start display some information --//
            if ($params->get('show_search_author',0) or $params->get('show_search_category',0)
                or $params->get('show_search_create_date',0) or $params->get('show_search_modify_date',0)
                or $params->get('show_search_publish_date',0) or $params->get('show_search_parent_category',0)
                or $params->get('show_search_hits',0) or $params->get('show_search_tags',0)
                or !empty($item -> event -> beforeDisplayAdditionInfo)
                or !empty($item -> event -> afterDisplayAdditionInfo)) :
                ?>
                <div class="tp-article-info uk-article-meta uk-margin-top uk-margin-bottom uk-grid-small" data-uk-grid>

                    <?php echo $item -> event -> beforeDisplayAdditionInfo;?>

                    <?php if ($params->get('show_search_category',0)){ ?>
                        <div class="tp-article-category">
                            <?php $title = $this->escape($item->category_title);
                            $url = '<a href="' . $item -> category_link
                                . '" itemprop="genre">' . $title . '</a>';
                            $lang_text  = 'COM_TZ_PORTFOLIO_CATEGORY';
                            ?>

                            <?php if(isset($item -> second_categories) && $item -> second_categories
                                && count($item -> second_categories)){
                                $lang_text  = 'COM_TZ_PORTFOLIO_CATEGORIES';
                                foreach($item -> second_categories as $j => $scategory){
                                    if($j <= count($item -> second_categories)) {
                                        $title  .= ', ';
                                        $url    .= ', ';
                                    }
                                    $url    .= '<a href="' . $scategory -> link
                                        . '" itemprop="genre">' . $scategory -> title . '</a>';
                                    $title  .= $this->escape($scategory -> title);
                                }
                            }?>

                            <?php if ($params->get('cat_link_category',1)){ ?>
                                <?php echo Text::sprintf($lang_text, $url); ?>
                            <?php }else{ ?>
                                <?php echo Text::sprintf($lang_text, '<span itemprop="genre">' . $title . '</span>'); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_parent_category', 0) && $item->parent_id != 1){ ?>
                        <div class="tp-article-parent-category">
                            <?php $title = $this->escape($item->parent_title);
                            $url = '<a href="' . Route::_(RouteHelper::getCategoryRoute($item->parent_id))
                                . '" itemprop="genre">' . $title . '</a>'; ?>
                            <?php if ($params->get('cat_link_parent_category', 1)){ ?>
                                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PARENT', $url); ?>
                            <?php }else{ ?>
                                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PARENT', '<span itemprop="genre">'
                                    . $title . '</span>'); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php
                    if ($params->get('show_search_tags', 0)) {
                        echo $this->loadTemplate('item_tags');
                    }
                    ?>

                    <?php if ($params->get('show_search_create_date',0)){ ?>
                        <div class="tp-article-created-date" itemprop="dateCreated">
                            <?php echo Text::sprintf('COM_TZ_PORTFOLIO_CREATED_DATE_ON', HTMLHelper::_('date',
                                $item->created, Text::_('DATE_FORMAT_LC2'))); ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_modify_date', 0)) { ?>
                        <div class="tp-modified-date" itemprop="dateModified">
                            <?php echo Text::sprintf('COM_TZ_PORTFOLIO_LAST_UPDATED', HTMLHelper::_('date',
                                $item->modified, Text::_('DATE_FORMAT_LC2'))); ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_publish_date',0)){ ?>
                        <div class="tp-article-published-date" itemprop="datePublished">
                            <?php echo Text::sprintf('COM_TZ_PORTFOLIO_PUBLISHED_DATE_ON', HTMLHelper::_('date',
                                $item->publish_up, Text::_('DATE_FORMAT_LC2'))); ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_author', 0) && !empty($item->author )){ ?>
                        <div class="tp-article-created-by" itemprop="author" itemscope itemtype="http://schema.org/Person">
                            <?php $author =  $item->author; ?>
                            <?php $author = ($item->created_by_alias ? $item->created_by_alias : $author);?>
                            <?php $author = '<span itemprop="name">' . $author . '</span>'; ?>

                            <?php if ($params->get('cat_link_author', 1)){?>
                                <?php 	echo Text::sprintf('COM_TZ_PORTFOLIO_WRITTEN_BY' ,
                                    HTMLHelper::_('link', $item -> author_link, $author, array('itemprop' => 'url'))); ?>
                            <?php }else{?>
                                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_WRITTEN_BY', $author); ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('show_search_hits', 0)){ ?>
                        <div class="tp-article-hits">
                            <?php echo Text::sprintf('COM_TZ_PORTFOLIO_ARTICLE_HITS', $item->hits); ?>
                            <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $item->hits; ?>" />
                        </div>
                    <?php } ?>

                    <?php echo $item -> event -> afterDisplayAdditionInfo; ?>

                </div>
            <?php
            endif;
            //-- End display some information --//
            ?>
<!--        </div>-->
<!--        <div class="uk-card-body">-->
            <?php
            if(!$params -> get('show_search_intro',1)) {
                //Call event onContentAfterTitle on plugin
                echo $item->event->afterDisplayTitle;
            }
            ?>

            <?php
//            // Display media from plugin of group tz_portfolio_mediatype
//            echo $this -> loadTemplate('item_media');
            ?>

            <?php
            //Show vote
            echo $item -> event -> contentDisplayVote;
            ?>

            <?php
             //Call event onContentBeforeDisplay on plugin
            echo $item -> event -> beforeDisplayContent;
            ?>

            <?php  if ($params->get('show_search_intro',1) AND !empty($item -> introtext)){?>
            <div class="tp-introtext" itemprop="description">
                <?php echo $item -> introtext;?>
            </div>
            <?php } ?>

            <?php echo $item -> event -> contentDisplayListView; ?>

            <?php echo $this -> loadTemplate('item_extrafields');?>

            <?php if($params -> get('show_search_readmore',1)){?>
            <a class="uk-button uk-button-text tp-article-readmore uk-margin-small-top" href="<?php
            echo $item ->link; ?>">
                <?php echo Text::sprintf('COM_TZ_PORTFOLIO_READ_MORE'); ?>
            </a>
            <?php }?>

            <?php
            //Call event onContentAfterDisplay on plugin
            echo $item->event->afterDisplayContent;
            ?>
<!--        </div>-->
    <?php
    // End Description and some info
    }?>
<?php endif;?>
