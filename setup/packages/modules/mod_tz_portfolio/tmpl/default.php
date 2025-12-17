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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

if (!$list) {
    return;
}

//$bootstrap4 = ($params -> get('enable_bootstrap',0) && $params -> get('bootstrapversion', 3) == 4);

$doc = Factory::getDocument();

$wa = $doc -> getWebAssetManager();

$wa -> usePreset('com_tz_portfolio.uikit');

$show_filter    = $params -> get('show_filter', 1);

//if ($params->get('height_element')) {
//    $wa -> addInlineStyle('
//        #portfolio' . $module->id . ' .TzInner{
//            height:' . $params->get('height_element') . 'px;
//        }
//    ');
//}
//if($params -> get('enable_resize_image', 0)){
////    $doc -> addScript(JUri::base(true) . '/modules/'.$module -> module.'/js/resize.js', array('version' => 'auto'));
//
//    $wa -> addInlineScript('(function(){
//        $(document).ready(function(){
//            tpPortfolioArticlesResizeImage($("#js-mod-tp-portfolio' . $module->id . ' > .element .tzpp_media"));
//        });
//    })(jQuery);');
//    if ($params->get('height_element')) {
//        $doc->addStyleDeclaration('
//        #portfolio' . $module->id . ' .tzpp_media img{
//            max-width: none;
//        }
//        #portfolio' . $module->id . ' .tzpp_media{
//            height:' . $params->get('height_element') . 'px;
//        }
//    ');
//    }
//}

?>
<div id="TzContent<?php echo $module->id; ?>" class="tz_portfolio<?php
echo $moduleclass_sfx;?>">
    <div data-uk-filter="target: #js-mod-tp-portfolio<?php echo $module->id?>" data-uk-margin>
        <?php if($show_filter && (isset($filter_tag) || isset($filter_cat))):?>
            <div class="js-tp-filter">
                <div class="filter-title"><?php echo Text::_('MOD_TZ_PORTFOLIO_FILTER');?></div>
                <div class="uk-grid-small uk-grid-divider uk-child-width-auto" data-uk-grid>
                    <div>
                        <ul class="uk-subnav uk-subnav-pill js-tp-filter-list" data-uk-margin>
                            <li class="uk-active" data-uk-filter-control><a href="#"><?php echo Text::_('JALL');?></a></li>
                            <?php if($params->get('tz_filter_type','categories') == 'tags' && $filter_tag):?>
                                <?php foreach($filter_tag as $i => $itag):?>
                                <li data-uk-filter-control="[data-filter-tag*='<?php echo $itag -> id;
                                ?>']">
                                    <a href="#<?php echo $itag -> alias; ?>" data-tp-filter-tag-id="<?php echo $itag -> id; ?>">
                                        <?php echo $itag -> title;?>
                                    </a>
                                </li>
                                <?php endforeach;?>
                            <?php endif;?>
                            <?php if($params->get('tz_filter_type','categories') == 'categories' && $filter_cat): ?>
                                <?php foreach($filter_cat as $i => $icat):?>
                                    <li data-uk-filter-control="[data-filter-category*='<?php echo $icat -> id;
                                    ?>']">
                                        <a href="#<?php echo $icat -> alias; ?>" data-tp-filter-tag-id="<?php echo $icat -> id; ?>">
                                            <?php echo $icat -> title;?>
                                        </a>
                                    </li>
                                <?php endforeach;?>
                            <?php endif;?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif?>

        <div id="js-mod-tp-portfolio<?php echo $module->id?>" class="uk-child-width-1-2 uk-child-width-1-3@m uk-margin-medium-top" data-uk-grid="<?php
        echo $params -> get('enable_masonry', 1)?' masonry: '.$params -> get('masonry_layout', 'pack'):'';?>">
            <?php foreach ($list as $i => $item) : ?>
                <?php
                $cat_filter    = array();
                $tag_filter    = array();
                if ($item -> params->get('tz_filter_type','') == 'tags'
                    && isset($tags[$item->content_id]) && !empty($tags[$item->content_id])) {
                    $tag_filter = ArrayHelper::getColumn($tags[$item->content_id], 'id');
                }

                if ($item -> params->get('tz_filter_type','') == 'categories'
                    && isset($categories[$item->content_id]) && !empty($categories[$item->content_id])) {
                    if(isset($categories[$item->content_id])){
                        $cat_filter    = ArrayHelper::getColumn($categories[$item->content_id], 'id');
                    }
                }
                ?>

            <div data-filter-category="<?php echo implode(' ',$cat_filter); ?>" data-name="<?php
            echo $item -> title;?>" data-date="<?php echo strtotime($item -> created);
            ?>" data-hits="<?php echo (int) $item -> hits; ?>"<?php
            echo !empty($tag_filter)?' data-filter-tag="'.implode(' ',$tag_filter).'"':''?> data-tp-id="<?php echo $item -> id; ?>">
                <div class="uk-card uk-card-default">
                    <?php
                    if(isset($item->event->onContentDisplayMediaType)){
                        if($item->event->onContentDisplayMediaType && !empty($item->event->onContentDisplayMediaType)){
                            ?>
                        <div class="tpp-portfolio__media uk-card-media-top">
                            <?php echo $item->event->onContentDisplayMediaType;?>
                        </div>
                        <?php }
                    }?>

                    <?php if(!isset($item -> mediatypes) ||
                        (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){?>
                    <div class="uk-card-body">
                        <?php
                        if ($item -> params -> get('show_title', 1)) {
                            echo '<h3 class="title"><a href="' . $item->link . '">' . $item->title . '</a></h3>';
                        }

                        //Call event onContentBeforeDisplay on plugin
                        if(isset($item -> event -> beforeDisplayContent)) {
                            echo $item->event->beforeDisplayContent;
                        }
                        ?>
                        <?php if ($item -> params->get('show_introtext', 1)) { ?>
                        <div class="description"><?php echo $item->introtext;?></div>
                        <?php } ?>
                        <?php
                        if($item -> params -> get('show_author', 1) or $item -> params->get('show_created_date', 1)
                            or $item -> params->get('show_hit', 1) or $item -> params->get('show_tag', 1)
                            or $item -> params->get('show_category', 1)
                            or !empty($item -> event -> beforeDisplayAdditionInfo)
                            or !empty($item -> event -> afterDisplayAdditionInfo)) {
                            ?>
                        <div class="tpp-portfolio__info uk-article-meta uk-margin-bottom">
                            <?php
                            if (isset($item->event->beforeDisplayAdditionInfo)) {
                                echo $item->event->beforeDisplayAdditionInfo;
                            }

                            if ($item -> params->get('show_author', 1)) {
                                echo '<div class="tz_created_by"><span class="text">' . Text::_('MOD_TZ_PORTFOLIO_TZ_CREATED_BY')
                                    . '</span><a href="' . $item->author_link . '">' . $item->user_name . '</a></div>';
                            }
                            if ($item -> params->get('show_created_date', 1)) {
                                echo '<div class="tz_date"><span class="text">' . Text::_('MOD_TZ_PORTFOLIO_TZ_DATE')
                                    . '</span>' . HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC1')) . '</div>';
                            }
                            if ($item -> params->get('show_hit', 1)) {
                                echo '<div class="tz_hit"><span class="text">' . Text::_('MOD_TZ_PORTFOLIO_TZ_HIT') . '</span>' . $item->hits . '</div>';
                            }
                            if ($item -> params->get('show_tag', 1)) {
                                if (isset($tags[$item->content_id])) {
                                    echo '<div class="tz_tag"><span class="text">' . Text::_('MOD_TZ_PORTFOLIO_TZ_TAGS') . '</span>';
                                    foreach ($tags[$item->content_id] as $t => $tag) {
                                        echo '<a href="' . $tag->link . '">' . $tag->title . '</a>';
                                        if ($t != count($tags[$item->content_id]) - 1) {
                                            echo ', ';
                                        }
                                    }
                                    echo '</div>';
                                }
                            }
                            if ($item -> params->get('show_category', 1)) {
                                if (isset($categories[$item->content_id]) && $categories[$item->content_id]) {
                                    if (count($categories[$item->content_id]))
                                        echo '<div class="tz_categories"><span class="text">'
                                            . Text::_('MOD_TZ_PORTFOLIO_TZ_CATEGORIES') . '</span>';
                                    foreach ($categories[$item->content_id] as $c => $category) {
                                        echo '<a href="' . $category->link . '">' . $category->title . '</a>';
                                        if ($c != count($categories[$item->content_id]) - 1) {
                                            echo ', ';
                                        }
                                    }
                                    echo '</div>';
                                }
                            }
                            if(isset($item -> event -> afterDisplayAdditionInfo)){
                                echo $item -> event -> afterDisplayAdditionInfo;
                            }
                            ?>
                        </div>
                        <?php } ?>
                        <?php
                        if(isset($item -> event -> contentDisplayListView)) {
                            echo $item->event->contentDisplayListView;
                        }
                        if($item -> params -> get('show_readmore',1)){
                            ?>
                            <a href="<?php echo $item->link?>"
                               class="uk-button uk-button-primary tp-item-readmore"><?php
                                echo $item -> params -> get('readmore_text','Read More');?></a>
                        <?php }?>
                    </div>
                <?php } ?>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>


    <?php if($params -> get('show_view_all', 0)){?>
    <div class="tp-portfolio__action text-center mb-3">
        <a href="<?php echo $params -> get('view_all_link');?>"<?php
        echo ($target = $params -> get('view_all_target'))?' target="'
            .$target.'"':'';?> class="uk-button uk-button-primary btn-view-all"><?php
            echo $params -> get('view_all_text', 'View All Portfolios');?></a>
    </div>
    <?php } ?>
</div>