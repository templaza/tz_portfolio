<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    TuanNATemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
$doc->addScript(JUri::root() . '/components/com_tz_portfolio_plus/js/tz_portfolio_plus.min.js');
$doc->addScript(JUri::root() . '/components/com_tz_portfolio_plus/js/jquery.isotope.min.js');
$doc->addStyleSheet(JUri::base(true) . '/components/com_tz_portfolio_plus/css/isotope.min.css');
$doc->addStyleSheet(JUri::base(true) . '/modules/mod_tz_portfolio_plus_articles/css/style.css');

if($params -> get('load_style', 0)) {
    $doc->addStyleSheet(JUri::base(true) . '/modules/mod_tz_portfolio_plus_articles/css/basic.css');
}

if ($params->get('height_element')) {
    $doc->addStyleDeclaration('
        #portfolio' . $module->id . ' .TzInner{
            height:' . $params->get('height_element') . 'px;
        }
    ');
}
if($params -> get('enable_resize_image', 0)){
    $doc -> addScript(JUri::base(true) . '/modules/mod_tz_portfolio_plus_articles/js/resize.js');
    if ($params->get('height_element')) {
        $doc->addStyleDeclaration('
        #portfolio' . $module->id . ' .tzpp_media img{
            max-width: none;
        }
        #portfolio' . $module->id . ' .tzpp_media{
            height:' . $params->get('height_element') . 'px;
        }
    ');
    }
}
$doc->addScriptDeclaration('
jQuery(function($){
    $(document).ready(function(){
        $("#portfolio' . $module->id . '").tzPortfolioPlusIsotope({
            "mainElementSelector"       : "#TzContent' . $module->id . '",
            "containerElementSelector"  : "#portfolio' . $module->id . '",
            "sortParentTag"             : "filter'.$module->id.'",
            "isotope_options"                   : {
                "core"  : {
                   "getSortData": null
                }
            },
            "params"                    : {
                "tz_column_width"               : ' . $params->get('width_element') . ',
                "tz_filter_type"        : "tags"
            },
            "afterColumnWidth" : function(newColCount,newColWidth){
                '.($params -> get('enable_resize_image', 0)?'TzPortfolioPlusArticlesResizeImage($("#portfolio' . $module->id . ' > .element .tzpp_media"));':'').'
            }
        });
    });
    $(window).load(function(){
        var $tzppisotope    = $("#portfolio' . $module->id . '").data("tzPortfolioPlusIsotope");
        if(typeof $tzppisotope === "object"){
            $tzppisotope.imagesLoaded(function(){
                $tzppisotope.tz_init();
            });
        }
    });
});
');

if ($list):
    ?>
<div id="TzContent<?php echo $module->id; ?>" class="tz_portfolio_plus_articles<?php echo $moduleclass_sfx;?> TzContent">
    <?php if($show_filter && isset($filter_tag)):?>
    <div id="tz_options" class="clearfix">
        <div class="option-combo">
            <div class="filter-title TzFilter"><?php echo JText::_('MOD_TZ_PORTFOLIO_PLUS_ARTICLES_FILTER');?></div>
            <div id="filter<?php echo $module->id;?>" class="option-set clearfix" data-option-key="filter">
                <a href="#show-all" data-option-value="*" class="btn btn-default btn-small selected"><?php echo JText::_('MOD_TZ_PORTFOLIO_PLUS_ARTICLES_SHOW_ALL');?></a>
                <?php if($filter_tag):?>
                    <?php foreach($filter_tag as $i => $itag): //var_dump($itag->title); die;?>
                        <a href="#<?php echo $itag -> alias; ?>"
                           class="btn btn-default btn-small"
                           data-option-value=".<?php echo $itag -> alias; ?>">
                            <?php echo $itag -> title;?>
                        </a>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>
    </div>
    <?php endif?>
    <div id="portfolio<?php echo $module->id; ?>" class="masonry row ">
        <?php foreach ($list as $i => $item) : ?>
            <?php
            $item_tags_alias    = array();
            if (isset($tags[$item->content_id]) && !empty($tags[$item->content_id])) {
                $item_tags_alias = JArrayHelper::getColumn($tags[$item->content_id], 'alias');
            }
            ?>
        <div class="element <?php echo implode(' ', $item_tags_alias)?>">
            <div class="TzInner">
                <?php
                if(isset($item->event->onContentDisplayMediaType)){
                ?>
                <div class="tzpp_media">
                  <?php echo $item->event->onContentDisplayMediaType;?>
                </div>
                <?php
                }

                if(!isset($item -> mediatypes) || (isset($item -> mediatypes) && !in_array($item -> type,$item -> mediatypes))){
                ?>
                <div class="information">
                    <?php
                    if ($params -> get('show_title', 1)) {
                        echo '<h3 class="title"><a href="' . $item->link . '">' . $item->title . '</a></h3>';
                    }

                    //Call event onContentBeforeDisplay on plugin
                    if(isset($item -> event -> beforeDisplayContent)) {
                        echo $item->event->beforeDisplayContent;
                    }

                    if ($params->get('show_introtext', 1)) {
                    ?>
                        <div class="description"><?php echo $item->introtext;?></div>
                    <?php }
                    if($params -> get('show_author', 1) or $params->get('show_created_date', 1)
                        or $params->get('show_hit', 1) or $params->get('show_tag', 1)
                        or $params->get('show_category', 1)
                        or !empty($item -> event -> beforeDisplayAdditionInfo)
                        or !empty($item -> event -> afterDisplayAdditionInfo)) {
                    ?>
                    <div class="muted item-meta">
                        <?php
                        if (isset($item->event->beforeDisplayAdditionInfo)) {
                            echo $item->event->beforeDisplayAdditionInfo;
                        }

                        if ($params->get('show_author', 1)) {
                            echo '<div class="tz_created_by"><span class="text">' . JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_CREATED_BY')
                                . '</span><a href="' . $item->author_link . '">' . $item->user_name . '</a></div>';
                        }
                        if ($params->get('show_created_date', 1)) {
                            echo '<div class="tz_date"><span class="text">' . JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_DATE')
                                . '</span>' . JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC1')) . '</div>';
                        }
                        if ($params->get('show_hit', 1)) {
                            echo '<div class="tz_hit"><span class="text">' . JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_HIT') . '</span>' . $item->hits . '</div>';
                        }
                        if ($params->get('show_tag', 1)) {
                            if (isset($tags[$item->content_id])) {
                                echo '<div class="tz_tag"><span class="text">' . JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_TAGS') . '</span>';
                                foreach ($tags[$item->content_id] as $t => $tag) {
                                    echo '<a href="' . $tag->link . '">' . $tag->title . '</a>';
                                    if ($t != count($tags[$item->content_id]) - 1) {
                                        echo ', ';
                                    }
                                }
                                echo '</div>';
                            }
                        }
                        if ($params->get('show_category', 1)) {
                            if (isset($categories[$item->content_id]) && $categories[$item->content_id]) {
                                if (count($categories[$item->content_id]))
                                    echo '<div class="tz_categories"><span class="text">' . JText::_('MOT_TZ_PORTFOLIO_PLUS_ARTICLE_TZ_CATEGORIES') . '</span>';
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
                        <?php
                    }

                    if(isset($item -> event -> contentDisplayListView)) {
                        echo $item->event->contentDisplayListView;
                    }
                    if($params -> get('show_readmore',1)){
                    ?>
                    <a href="<?php echo $item->link?>"
                       class="btn btn-primary readmore"><?php echo $params -> get('readmore_text','Read More');?></a>
                    <?php }?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>