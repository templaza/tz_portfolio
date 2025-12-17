
// Only define the TZ_Portfolio namespace if not defined.
window.TZ_Portfolio = window.TZ_Portfolio || {};

(function($, document, Joomla, TZ_Portfolio){
    "use strict";

    // $(document).ready(function(){
    //     alert("423432");
    // });

    $.tzPortfolioInfiniteScroll  = function(el,options){
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
    }
    $.fn.tzPortfolioInfiniteScroll   = function(options){
        if(options === undefined) options   = {};
        if(typeof options === 'object'){
            // Call function
            return this.each(function() {
                // Call function
                if ($(this).data("tzPortfolioInfiniteScroll") === undefined) {
                    new $.tzPortfolioInfiniteScroll(this, options);
                }else{
                    $(this).data('tzPortfolioInfiniteScroll');
                }
            });
        }
    };

    // Generic ajax dialog
    TZ_Portfolio.dialogAjax = function (tasks) {

        // Get submitform function of Joomla
        var tppOrigSubmitForm = Joomla.submitform;

        Joomla.submitform = function (task, form, validate) {

            if (!form) {
                form = document.getElementById('adminForm');
            }

            var $form = $(form),
                tppmatch = "", layout = task;

            if(task && task.match(".")){
                tppmatch    = task.split(".");
            }

            if(tppmatch.length){
                layout  = tppmatch[tppmatch.length - 1];
            }

            if (tasks.indexOf(task) !== -1) {
                var checkboxes = $form.find("[type=checkbox][id^=cb]:checked"),
                    cids = [],
                    ajaxParams = {
                        "view": "dialog",
                        "layout": layout,
                        "format": "ajax",
                        "tmpl": "component"
                    };
                ajaxParams[Joomla.getOptions("csrf.token")] = 1;
                if (checkboxes.length) {
                    cids = checkboxes.map(function () {
                        return this.value;
                    }).get();
                    ajaxParams["cid[]"] = cids;
                }
                $.ajax({
                    url: "index.php?option=com_tz_portfolio",
                    method: "POST",
                    dataType: "json",
                    data: ajaxParams
                }).done(function (result) {
                    var dataObj = $(result.data);

                    dataObj.attr("data-tp-dialog-modal", "");
                    dataObj.find("form").append("<input type=\"hidden\" name=\"" + Joomla.getOptions("csrf.token") + "\" value=\"1\"/>");
                    // dataObj.modal("show");
                    dataObj.appendTo("body");
                    dataObj.find("[data-submit-button]").on("click", function () {
                        if(dataObj.find("form").length) {
                            dataObj.find("form").submit();
                        }else{
                            tppOrigSubmitForm(task, form);
                        }
                    });

                    UIkit.modal(dataObj).show();

                    // $('body').on('hidden.bs.modal', '[data-tp-dialog-modal]', function () {
                    //     $(this).removeData('bs.modal').remove();
                    // });
                });
                return;
            } else {
                tppOrigSubmitForm(task, form);
            }
        };
    };
})(jQuery, document, window.Joomla, window.TZ_Portfolio);