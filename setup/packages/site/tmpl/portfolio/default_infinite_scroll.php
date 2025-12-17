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

use Joomla\CMS\Language\Text;

$params     = $this -> params;
//$lang       = JFactory::getApplication() -> input -> getCmd('lang');
//$language   = JLanguageHelper::getLanguages('lang_code');

/* @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this -> document -> getWebAssetManager();
$wa -> addInlineStyle('.page-load-status{
    display: none;
}');
$loadOnScroll    = ($params -> get('tz_portfolio_layout', 'ajaxButton') == 'ajaxInfiScroll')?'true':'false';
$scrollThreshold    = ($params -> get('tz_portfolio_layout', 'ajaxButton') == 'ajaxInfiScroll');
$wa -> addInlineScript('
    jQuery(function($){
        $(document).ready(function(){
            var tpParamUrl   = function(infScroll){
                var __params    = {"shownIds": []},
                    __is_filter_activated = false;
                
                // Get portfolio id
                if($("#portfolio .js-tp-portfolio [data-tp-id]").length){
                    __params["shownIds"]    = $("#portfolio .js-tp-portfolio [data-tp-id]").map(function () {
                        var attr = $(this).data("tp-id");
                        if (typeof attr !== typeof undefined && attr !== false) {
                            return $(this).data("tp-id");
                        }
                    }).get();
                }
                
                // Get category filter id
                if($("#portfolio .js-tp-filter [data-tp-filter-category-id]").length){
                    __params["igCatId"]    = $("#portfolio .js-tp-filter [data-tp-filter-category-id]").map(function () {
                        var attr = $(this).data("tp-filter-category-id");
                        if (typeof attr !== typeof undefined && attr !== false) {
                            return $(this).data("tp-filter-category-id");
                        }
                    }).get();
                }
                
                // Get tag filter id
                if($("#portfolio .js-tp-filter [data-tp-filter-tag-id]").length){
                    __params["igTagId"]    = $("#portfolio .js-tp-filter [data-tp-filter-tag-id]").map(function () {
                        var attr = $(this).data("tp-filter-tag-id");
                        if (typeof attr !== typeof undefined && attr !== false) {
                            return $(this).data("tp-filter-tag-id");
                        }
                    }).get();
                }
                
                // Get current tag activated
                if($("#portfolio .js-tp-filter-list .uk-active [data-tp-filter-tag-id]").length){
                    __params["tid"] = $("#portfolio .js-tp-filter-list .uk-active [data-tp-filter-tag-id]")
                    .data("tp-filter-tag-id");
                    __is_filter_activated   = true;
                }
                
                if(typeof infScroll !== "undefined" && !__is_filter_activated){
                    __params["page"]    = infScroll.pageIndex + 1;
                }
                
                return __params;
            };
            var $container = $("#portfolio .js-tp-portfolio").infiniteScroll({
                append: false,
                button: ".js-view-more",
                status: ".page-load-status",
                loadOnScroll: '.($loadOnScroll).',
                checkLastPage: false,
                history: false,
                responseBody: "json",
                path: function(){
                    var __params    = tpParamUrl(this);
                    var __path  = "'.htmlspecialchars_decode($this -> ajaxLink).'";
                                        
                    return __path + "&" + $.param(__params);
                },
            });
            $container.on( "load.infiniteScroll", function( event, body ) {
                var $infScroll = $container.data("infiniteScroll");
                
                // append item elements
                var $newElems = $($(body.data.articles).children() );
                
                $newElems.addClass("uk-animation-scale-up");
                // ensure that images load before adding to masonry layout
                $newElems.imagesLoaded(function(){
                    $container.infiniteScroll("appendItems", $newElems);
                    setTimeout(function(){
                        $newElems.removeClass("uk-animation-scale-up");
                    }, 200);
                });
                
                // Append new filter
                if(typeof body.data.filter !== "undefined"){
                    $("#portfolio .js-tp-filter .js-tp-filter-list").append(body.data.filter);
                }
                
                if(body.data.articles == "undefined" || !body.data.articles.length){
                    $infScroll.pageIndex--;
                    $infScroll.showStatus("error");
                    $($infScroll.button.element).hide();
                    $infScroll.updateGetAbsolutePath();
                }
            });
            
            $("#portfolio [data-uk-filter]").on("afterFilter", function(){
            
                var __scroll = $("#portfolio .js-tp-portfolio").data("infiniteScroll");
                
                if(!$(this).find(".js-tp-portfolio > *:visible").length){
                    var __path  = "'.htmlspecialchars_decode($this -> ajaxLink).'";
                    var __org_path  = __scroll.options.path;
                    var __org_page_index    = __scroll.pageIndex;
                    var __param = tpParamUrl(__scroll);
                    
                    __path  += "&" + $.param(__param);
                    __scroll.options.path   = function(){ return __path.replace(/\&page=[0-9+]/i,""); };
                    __scroll.updateGetPath();
                    __scroll.updateGetAbsolutePath();
                    
                    __scroll.loadNextPage();
                    
                    __scroll.pageIndex--;
                    
                    // Set again path
                    __scroll.options.path   = __org_path;
                    __scroll.updateGetPath();
                    __scroll.updateGetAbsolutePath();
                }
                
                if(!$(this).find(".js-tp-filter-list .uk-active").data("uk-filter-control")){
                    __scroll.hideAllStatus();
                    __scroll.button.canLoad = true;
                    $(__scroll.button.element).show();
                }
            });
        });
    });
');

//$doc    = \Joomla\CMS\Factory::getDocument();
//$doc -> addScriptDeclaration('
//jQuery(document).ready(function(){
//    jQuery("#portfolio").tzPortfolioPlusInfiniteScroll({
//        "params"    : '.$this -> params .',
//        rootPath    : "'.JUri::root().'",
//        Itemid      : '.$this -> Itemid.',
//         msgText    : "<i class=\"tz-icon-spinner tz-spin\"><\/i>'.JText::_('COM_TZ_PORTFOLIO_LOADING_TEXT').'",
//        loadedText  : "'.JText::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES').'"
//        '.(isset($this -> commentText)?(',commentText : "'.$this -> commentText.'"'):'').',
//        lang        : "'.$this -> lang_sef.'"
//    });
//});');
?>
<!--<div id="tz_append" class="text-center">-->
<div class="page-load uk-margin-medium-top">
    <div class="page-load-status uk-text-center uk-margin-bottom">
        <div class="infinite-scroll-request">
            <div data-uk-spinner="ratio: 1"></div>
        </div>
        <p class="infinite-scroll-last">End of content</p>
        <p class="infinite-scroll-error"><?php echo Text::_('COM_TZ_PORTFOLIO_NO_MORE_PAGES');?></p>
    </div>
    <?php if($params -> get('tz_portfolio_layout', 'ajaxButton') == 'ajaxButton'):?>
        <button class="uk-button uk-button-default uk-width-1-1 js-view-more"><?php
            echo Text::_('COM_TZ_PORTFOLIO_ADD_ITEM_MORE');?></button>
    <?php endif;?>
</div>
<!--</div>-->

<!--<div id="loadaj" style="display: none;">-->
<!--    <a href="--><?php //echo $this -> ajaxLink; ?><!--"></a>-->
<!--</div>-->
