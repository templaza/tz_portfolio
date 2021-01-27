//Sort

(function($, window, TZ_Portfolio_Plus){
    'use strict';

    // ajaxCompletes array with value is the function
    TZ_Portfolio_Plus.infiniteScroll  = {
        "addAjaxComplete": function(func){
            this.ajaxCompletes.push(func);
        }
        ,"ajaxCompletes": []
    }

    function tzSortFilter(srcObj, desObj, order) {
        if ((!order || order == 'auto')
            && (srcObj.last().attr('data-order') && srcObj.last().data('order').toString().length)) {
            order = 'filter_asc';
        }
        srcObj.sort(function (a, b) {
            var compA = jQuery(a).data('order') ? parseInt(jQuery(a).data('order'), 10) : jQuery(a).text().trim();
            var compB = jQuery(b).data('order') ? parseInt(jQuery(b).data('order'), 10) : jQuery(b).text().trim();
            if (jQuery(a).attr('data-option-value') != '*' &&
                jQuery(b).attr('data-option-value') != '*') {
                if (order.substr(order.length - 3, order.length).toLowerCase() == 'asc') {
                    return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
                }
                if (order.substr(order.length - 4, order.length).toLowerCase() == 'desc') {
                    return (compA > compB) ? -1 : (compA < compB) ? 1 : 0;
                }
            }
        });
        srcObj.each(function (idx, itm) {
            desObj.append(itm).append('\n');
        });
        return true;
    }

    var $tppUtility =   {};

    $tppUtility.lastClickAvailabled  =   false;

    $tppUtility.createCookie = function (name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    };

    $tppUtility.readCookie = function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    };

    $tppUtility.eraseCookie = function (name) {
        createCookie(name,"",-1);
    };

    $tppUtility.goToByScroll = function(id) {
        // Remove "link" from the ID
        // id = id.replace("link", "");
        // Scroll
        $('html,body').animate({
            scrollTop: $("#" + id).offset().top
        }, 'slow');
    };

    $.tzPortfolioPlusIsotope  = function(el,options){

        var $tzppIsotope   = $(el),
            $defOptions = $.extend(true,{},$.tzPortfolioPlusIsotope.defaults),
            $var    = $.extend(true,$defOptions,options);

        if(!$var.params.orderby_sec.length){
            $var.params.orderby_sec = 'rdate';
        }

        var $params = $var.params,
            $isotope_options    = $var.isotope_options;

        switch($params.orderby_sec){
            default:
                $isotope_options.core.sortBy        = 'original-order';
                $isotope_options.core.sortAscending = true;
                break;
            case 'order':
            case 'rorder':
                $isotope_options.core.sortBy        = 'original-order';
                $isotope_options.core.sortAscending = true;
                break;
            case 'date':
                $isotope_options.core.sortBy        = 'date';
                $isotope_options.core.sortAscending = true;
                if($var.timeline){
                    $isotope_options.core.sortAscending = false;
                }
                break;
            case 'rdate':
                $isotope_options.core.sortBy        = 'date';
                $isotope_options.core.sortAscending = false;
                break;
            case 'alpha':
                $isotope_options.core.sortBy        = 'name';
                $isotope_options.core.sortAscending = true;
                break;
            case 'ralpha':
                $isotope_options.core.sortBy        = 'name';
                $isotope_options.core.sortAscending = false;
                break;
            case 'author':
                $isotope_options.core.sortBy = 'date';
                $isotope_options.core.sortAscending = false;
                break;
            case 'rauthor':
                $isotope_options.core.sortBy = 'date';
                $isotope_options.core.sortAscending = false;
                break;
            case 'hits':
                $isotope_options.core.sortBy        = 'hits';
                $isotope_options.core.sortAscending = false;
                break;
            case 'rhits':
                $isotope_options.core.sortBy        = 'hits';
                $isotope_options.core.sortAscending = true;
                break;
        }

        if(!$isotope_options.core.layoutMode.length){
            if($params.layout_type.length){
                $isotope_options.core.layoutMode  = $params.layout_type[0];
            }else {
                $isotope_options.core.layoutMode = 'masonry';
            }
        }

        // This is the function to calculate column width for isotope
        $tzppIsotope.tz_init = function(bool){
            var contentWidth    = $($var.mainElementSelector).width();
            var columnWidth     = $params.tz_column_width;
            var curColCount     = 0;

            var maxColCount     = 0;
            var newColCount     = 0;
            var newColWidth     = 0;
            var featureColWidth = 0;

            if(contentWidth < $tzppIsotope.width()){
                contentWidth    = $tzppIsotope.width();
            }

            if($var.columnWidth){
                columnWidth    = $var.columnWidth;
            }

            if($var.timeline) {
                $.extend(true, $.Isotope.prototype, {
                    _sort: function () {
                        var sortBy = this.options.sortBy,
                            getSorter = this._getSorter,
                            sortDir = this.options.sortAscending ? 1 : -1,
                            sortFn = function (alpha, beta) {
                                var a = getSorter(alpha, sortBy),
                                    b = getSorter(beta, sortBy);

                                // fall back to original order if data matches
                                if (a === b && sortBy !== 'original-order') {
                                    a = getSorter(alpha, 'original-order');
                                    b = getSorter(beta, 'original-order');
                                }

                                if(sortBy == 'name' || sortBy == 'hits') {
                                    if ($(alpha).attr('data-category') > $(beta).attr('data-category')) {
                                        return 0;
                                    } else {
                                        if ($(alpha).attr('data-category') == $(beta).attr('data-category')) {
                                            if($(alpha).hasClass('TzDate')){
                                                return 0;
                                            }
                                            if (a > b) {
                                                return 1 * sortDir;
                                            } else {
                                                return 0;
                                            }
                                        }
                                    }
                                }

                                return ( ( a > b ) ? 1 : ( a < b ) ? -1 : 0 ) * sortDir;
                            };
                        this.$filteredAtoms.sort(sortFn);
                    }
                });
            }


            curColCount = Math.floor(contentWidth / columnWidth);
            $var.beforeCalculateColumn(curColCount);

            maxColCount = curColCount + 1;
            if((maxColCount - (contentWidth / columnWidth)) > ((contentWidth / columnWidth) - curColCount)){
                newColCount     = curColCount;
            }
            else{
                newColCount = maxColCount;
            }

            newColWidth = contentWidth;
            featureColWidth = contentWidth;

            if(newColCount > 1){
                newColWidth = Math.floor(contentWidth / newColCount);
                featureColWidth = newColWidth * 2;
            }
            $var.afterCalculateColumn(newColCount,newColWidth);

            $tzppIsotope.find(".element").width(newColWidth);

            $tzppIsotope.find($var.elementFeatureSelector).width(featureColWidth);
            $tzppIsotope.find(".element").find('.element.TzDate').width(contentWidth);

            $var.afterColumnWidth(newColCount,newColWidth);

            function loadVisible($els, trigger) {
                if(typeof $els === "undefined"){
                    return;
                }
                $els.filter(function () {
                    var rect = this.getBoundingClientRect();
                    return rect.top >= 0 && rect.top <= window.innerHeight;
                }).trigger(trigger);
            }
            if(parseInt($params.enable_lazyload,10) && (typeof $.fn.lazyload !== "undefined" || typeof window.lazyload !== "undefined")) {
                // var $lazyloading = "<span class=\"tz-icon-spinner\"></span>";
                var $imgs = $tzppIsotope.find("img:not(.lazyloaded)").addClass("lazyload");
                $imgs = $tzppIsotope.find("img.lazyload");

                $(window).on('scroll', function () {
                    loadVisible($imgs, 'lazylazy');
                });

                $imgs.css({
                    "padding-top": function () {
                        return this.height;
                    },
                    "padding-left": function () {
                        return this.width;
                    }
                })
                    .attr("data-src", function () {
                        var src = $(this).attr("src");
                        return src;
                    });
                $imgs.lazyload({
                    // effect: "fadeIn",
                    failure_limit: Math.max($imgs.length - 1, 0),
                    event: 'lazylazy',
                    placeholder: "",
                    data_attribute: "src",
                    appear: function (elements_left, settings) {
                        if (!this.loaded) {
                            // $(this).addClass("lazyloading");
                            $(this).removeClass("lazyload").addClass("lazyloading");
                            // $(this).removeClass("lazyload").addClass("lazyloading").before($lazyloading);
                        }
                    },
                    load: function (elements_left, settings) {
                        if (this.loaded) {
                            $(this).removeClass("lazyloading").css({
                                "padding-top": "",
                                "padding-left": ""
                            }).addClass("lazyloaded");
                        }
                    }
                });
            }

            if(bool) {
                $tzppIsotope.find('.element').css({opacity: 0});
            }
            $tzppIsotope.imagesLoaded(function(){

                if(bool) {
                    $tzppIsotope.find('.element').css({opacity: 1});
                }

                // if(parseInt($params.enable_lazyload,10) && (typeof $.fn.lazyload !== "undefined" || typeof window.lazyload !== "undefined")) {
                //     // var $lazyloading = "<span class=\"tz-icon-spinner\"></span>";
                //     var $imgs = $tzppIsotope.find("img:not(.lazyloaded)").addClass("lazyload");
                //     $imgs = $tzppIsotope.find("img.lazyload");
                //
                //     $(window).on('scroll', function () {
                //         loadVisible($imgs, 'lazylazy');
                //     });
                //
                //     $imgs.css({
                //         "padding-top": function () {
                //             return this.height;
                //         },
                //         "padding-left": function () {
                //             return this.width;
                //         }
                //     })
                //         .attr("data-src", function () {
                //             var src = $(this).attr("src");
                //             return src;
                //         });
                //     $imgs.lazyload({
                //         // effect: "fadeIn",
                //         failure_limit: Math.max($imgs.length - 1, 0),
                //         event: 'lazylazy',
                //         placeholder: "",
                //         data_attribute: "src",
                //         appear: function (elements_left, settings) {
                //             if (!this.loaded) {
                //                 // $(this).addClass("lazyloading");
                //                 $(this).removeClass("lazyload").addClass("lazyloading");
                //                 // $(this).removeClass("lazyload").addClass("lazyloading").before($lazyloading);
                //             }
                //         },
                //         load: function (elements_left, settings) {
                //             if (this.loaded) {
                //                 $(this).removeClass("lazyloading").css({
                //                     "padding-top": "",
                //                     "padding-left": ""
                //                 }).addClass("lazyloaded");
                //             }
                //         }
                //     });
                // }


                $var.afterImagesLoaded(newColCount,newColWidth);

                var dir = $("html").attr("dir"),
                    _transformsEnabled = true;

                if($var.rtl || (dir !== undefined && dir.toLowerCase() === 'rtl')){
                    _transformsEnabled  = false;

                    // modify Isotope's absolute position method
                    $.Isotope.prototype._positionAbs = function( x, y ) {
                        return { right: x, top: y };
                    };
                }

                $tzppIsotope.isotope({
                    resizable: false, // disable normal resizing
                    itemSelector : $isotope_options.core.itemSelector,
                    layoutMode: $isotope_options.core.layoutMode,
                    sortBy: $isotope_options.core.sortBy,
                    sortAscending: $isotope_options.core.sortAscending,
                    filter: $isotope_options.core.filter,
                    transformsEnabled: _transformsEnabled,
                    masonry:{
                        columnWidth: newColWidth
                    },
                    getSortData: $isotope_options.core.getSortData,
                    onLayout: function(){
                        if(parseInt($params.enable_lazyload,10) && typeof loadVisible !== "undefined") {
                            loadVisible($imgs, 'lazylazy');
                        }
                    }
                },function(){
                    if(parseInt($params.tz_show_filter,10) && $params.filter_tags_categories_order) {
                        //Sort tags or categories filter
                        if(typeof tzSortFilter != 'undefined' && !parseInt($params.show_all_filter,10)) {
                            tzSortFilter($($isotope_options.sortParentTag).find($isotope_options.sortChildTag),
                                $($isotope_options.sortParentTag), $params.filter_tags_categories_order);
                        }
                    }
                    $isotope_options.complete();
                    $tzppIsotope.afterLoadPortfolio();
                });
            });
        };

        var _filterItems = $($isotope_options.filterSelector + " [data-option-value]"),
            _btnActiveClass = _filterItems.hasClass("active")?"active":"selected";

        $tzppIsotope.loadPortfolio  = function(){

            var $optionSets = $($isotope_options.filterSelector),
                $optionLinks = $optionSets.find($isotope_options.tagFilter);
            var $r_options    = null,
                $container_options=$isotope_options.core;

            $optionLinks.click(function(event){
                event.preventDefault();
                var $isotope_this = $(this);
                // _btnActiveClass = "selected";

                // don't proceed if already selected
                if ( $isotope_this.hasClass('selected') || $isotope_this.hasClass('active') ) {
                    if(!$isotope_this.closest("[data-option-key=filter]")
                        .find("[data-sub-category-of=\""+ $isotope_this.data("term") +"\"]").length) {
                        return false;
                    }
                }

                var $optionSet = $isotope_this.closest('.option-set');

                // make option object dynamically, i.e. { filter: '.my-filter-class' }
                var options = $container_options,
                    key = $optionSet.attr('data-option-key'),
                    value = $isotope_this.attr('data-option-value');

                /* Since v2.2.5 */

                if(key === "filter"){
                    if($.data(el, "tppinfscr") === "loading"){
                        return;
                    }
                }

                $optionSet.find("[data-option-value]").removeClass(_btnActiveClass);
                $isotope_this.addClass(_btnActiveClass);


                if(key === "filter"){
                    /**** Parent or subcategory click */
                    var term    = $isotope_this.data("term")||((value!=="*") && value.substr(value.lastIndexOf("_") + 1, value.length)),
                        subcat  = $($isotope_options.filterSelector).find("[data-sub-category-of=\""+ term +"\"]");

                    if(term && subcat.length ){
                        $isotope_this.closest($isotope_options.filterSelector).addClass("subcategory-active");
                        $($isotope_options.filterSelector).find("[data-sub-category-of]").removeClass("is-active");
                        subcat.addClass("is-active");
                    }
                    if($isotope_this.hasClass("js-subcategory-back-href")){
                        var dataTerm    = $isotope_this.closest("[data-sub-category-of]").data("sub-category-of"),
                            elTerm  = $isotope_this.closest($isotope_options.filterSelector).find("[data-term=" + dataTerm + "]").not($isotope_this);
                        elTerm.trigger("click");
                        if(elTerm.closest("[data-sub-category-of]").length){
                            elTerm.closest("[data-sub-category-of]").addClass("is-active");
                        }else{
                            $isotope_this.closest($isotope_options.filterSelector).removeClass("subcategory-active");
                        }
                        $isotope_this.closest("[data-sub-category-of]").removeClass("is-active");
                    }
                    var $itemLoad   = !$tzppIsotope.find($isotope_options.core.itemSelector
                        + $($isotope_options.filterSelector + "[data-option-key=filter] " +
                            $isotope_options.tagFilter + "." + _btnActiveClass).data("option-value")).length || ($(window).height() >= $(document).height()),
                        _filterSelected = $($isotope_options.filterSelector + "[data-option-key=filter] " +
                            $isotope_options.tagFilter +  "." + _btnActiveClass),
                        $loadDone = _filterSelected.length?$.data(_filterSelected[0], "infinitescroll"):undefined;

                    if((typeof $loadDone === typeof undefined)){
                        if($params.tz_portfolio_plus_layout == 'ajaxInfiScroll'
                            && TZ_Portfolio_Plus.infiniteScroll.displayNoMorePageLoad
                            && $tzppIsotope.find($isotope_options.core.itemSelector).length
                            < TZ_Portfolio_Plus.infiniteScroll.countItems){
                            $("#tz_append > a:first").remove();
                        }

                        if(!$params.tz_portfolio_plus_layout || $params.tz_portfolio_plus_layout === 'ajaxButton'){
                            $itemLoad   = !$tzppIsotope.find($isotope_options.core.itemSelector
                                + $($isotope_options.filterSelector + "[data-option-key=filter] " +
                                    $isotope_options.tagFilter +  "." + _btnActiveClass).data("option-value")).length;
                            if(TZ_Portfolio_Plus.infiniteScroll.displayNoMorePageLoad){
                                $("#tz_append").show();
                            }
                        }

                        if($params.tz_portfolio_plus_layout == 'ajaxButton'){
                            $("#tz_append").show();
                            var addItemMore = $.data(el, "tppinfscr_additemmore");
                            if(typeof addItemMore !== typeof undefined && addItemMore.length){
                                addItemMore.clone(true).insertAfter($("#tz_append > a:first"));
                                $("#tz_append > a:first").remove();
                            }
                        }

                        var _infscroll  = $.data(el, "tzPortfolioPlusInfiniteScroll");
                        if(_infscroll &&  $tzppIsotope.find($isotope_options.core.itemSelector).length
                            < TZ_Portfolio_Plus.infiniteScroll.countItems){
                            _infscroll.infinitescroll("update",{
                                state:{
                                    isDone: false,
                                    isDuringAjax: false,
                                    isBeyondMaxPage: false
                                }
                            });
                        }

                        if($itemLoad && $tzppIsotope.find($isotope_options.core.itemSelector).length
                            < TZ_Portfolio_Plus.infiniteScroll.countItems){
                            // var _infscroll  = $.data(el, "tzPortfolioPlusInfiniteScroll");
                            if(_infscroll){
                                // _infscroll.infinitescroll("update",{
                                //     state:{
                                //         isDone: false,
                                //         isDuringAjax: false,
                                //         isBeyondMaxPage: false
                                //     }
                                // });

                                if($params.tz_portfolio_plus_layout == 'ajaxButton'){
                                    $("#infscr-loading").hide();
                                    $("#tz_append").show()
                                        .find("a:first").trigger("click");
                                }

                                if($params.tz_portfolio_plus_layout == 'ajaxInfiScroll') {
                                    $("#infscr-loading").show();
                                    $("#tz_append").show();
                                    $("#tz_append > a:first").remove();
                                    if(!$isotope_this.hasClass("js-subcategory-back-href")) {
                                        _infscroll.infinitescroll('retrieve');
                                    }
                                }
                            }
                        }
                    }else{
                        if(TZ_Portfolio_Plus.infiniteScroll.displayNoMorePageLoad){
                            $("#tz_append").html('<a class="tzNomore">' + TZ_Portfolio_Plus.infiniteScroll.noMorePageLoadText + '</a>').show();
                        }else {
                            $("#tz_append").hide();
                        }
                    }
                }
                /* End since v2.2.5 */

                // parse 'false' as false boolean
                value = value === 'false' ? false : value;
                options[ key ] = value;

                if ( key === 'layoutMode' && typeof window.changeLayoutMode === 'function' ) {
                    // changes in layout modes need extra logic
                    window.changeLayoutMode( $isotope_this, options );
                } else {
                    // otherwise, apply new options
                    if(value == 'name'){
                        if($params.orderby_sec == 'alpha' || ($params.orderby_sec != 'alpha'
                            && $params.orderby_sec != 'ralpha')){
                            options.sortAscending    = true;
                        }else{
                            if($params.orderby_sec == 'ralpha'){
                                options.sortAscending    = false;
                            }
                        }
                    }
                    if(value == 'date'){
                        if($params.orderby_sec == 'rdate'
                            || ($params.orderby_sec != 'date'
                                && $params.orderby_sec != 'rdate')){
                            options.sortAscending    = false;
                        }else{
                            if($params.orderby_sec == 'date'){
                                options.sortAscending    = true;
                            }
                        }
                    }
                    if(value == 'hits'){
                        if($params.orderby_sec == 'hits'
                            || ($params.orderby_sec != 'hits'
                                && $params.orderby_sec != 'rhits')){
                            options.sortAscending    = false;
                        }else{
                            if($params.orderby_sec == 'rhits'){
                                options.sortAscending    = true;
                            }
                        }
                    }

                    options = $.extend($r_options,options);
                    $tzppIsotope.isotope(options);
                    $r_options  = options;
                }
                return false;
            });
        };

        $tzppIsotope.triggerOnClickItem = function () {
            $($isotope_options.core.itemSelector).find('a').click(function (event) {
                // event.preventDefault();
                $tppUtility.createCookie('tppLatestItem', $(this).closest($isotope_options.core.itemSelector).attr('id'), 0.5);
            });
        };

        $tzppIsotope.afterLoadPortfolio = function () {
            if($var.params.remember_recent_article !== "undefined" && $var.params.remember_recent_article == 1) {
                var index   =   $tppUtility.readCookie('tppLatestItem');
                if (index != null) {
                    if(!$tppUtility.lastClickAvailabled && $('#'+ index).length){
                        $tppUtility.goToByScroll(index);
                        $tppUtility.lastClickAvailabled  =   true;
                    }
                }
            }
        };

        // Call Function isotope ind document ready function
        $tzppIsotope.tz_init(true);
        $($isotope_options.filterSelector+'[data-option-key=sortBy]').children().removeClass('selected active')
            .end().find('[data-option-value='+$isotope_options.core.sortBy + ']').addClass(_btnActiveClass);

        $tzppIsotope.loadPortfolio();
        $tzppIsotope.triggerOnClickItem();
        $tzppIsotope.vars   = $var;

        $.data(el,"tzPortfolioPlusIsotope", $tzppIsotope);

        // Call Function tz_init in window load and resize function
        /*** Smart Resize is the function of isotope v1 ***/
        /*** debouncedresize is the function of isotope v2 ***/
        if(typeof $(window).smartresize !== "undefined") {
            $(window).smartresize(function () {
                $tzppIsotope.tz_init();
            });
        }else{
            $(window).on('debouncedresize', function(){
                $tzppIsotope.tz_init();
            });
        }

        return this;
    };
    // Create options object
    $.tzPortfolioPlusIsotope.defaults  = { // This is default options
        "columnWidth"               : "",
        "mainElementSelector"       : "#TzContent",
        "containerElementSelector"  : "#portfolio",
        "elementFeatureSelector"    : ".tz_feature_item",
        "JSON"                      : {},
        "timeline"                  : false,
        "rtl"                       : false,
        'params'                    : {
            "orderby_sec"                   : "rdate",
            "tz_show_filter"                : 1,
            "filter_tags_categories_order"  : "auto",
            "tz_portfolio_plus_layout"      : "ajaxButton",
            "comment_function_type"         : "default",
            "tz_filter_type"                : "categories",
            "show_all_filter"               : 0,
            "tz_comment_type"               : "disqus",
            "tz_show_count_comment"         : 1,
            "tz_column_width"               : 233,
            "layout_type"                   : ["masonry"],
            "enable_lazyload"               : 0
        },
        "isotope_options"                   : {
            "core"  : {
                "itemSelector": ".element",
                "layoutMode": "",
                "sortBy": "date",
                "sortAscending": false,
                "filter": "*",
                "getSortData": {
                    date: function ($_el) {
                        var $el = (typeof $_el.hasClass === "undefined" )?$($_el):$_el,
                            number = ($el.hasClass("element") && $el.attr("data-date").length) ?
                                $el.attr("data-date") : $el.find(".create").text();
                        return parseInt(number,10);
                    },
                    hits: function ($_el) {
                        var $el = (typeof $_el.hasClass === "undefined" )?$($_el):$_el,
                            number = ($el.hasClass("element") && $el.attr("data-hits").length) ?
                                $el.attr("data-hits") : $el.find(".hits,.tpp-item-hit").text();
                        return parseInt(number,10);
                    },
                    name: function ($_el) {
                        var $el = (typeof $_el.hasClass === "undefined" )?$($_el):$_el,
                            name = ($el.hasClass("element") && $el.find(".TzPortfolioTitle.name,.tpp-item-title.name")
                                .length)?$el.find(".TzPortfolioTitle.name, .tpp-item-title.name").text().trim():
                                (($el.attr("data-title").length)?$el.attr("data-title"):""),
                            itemText = name.length ? name : $el.text().trim();
                        return itemText;
                    }
                }
            },
            "filterSelector"    : "#tz_options .option-set",
            "tagFilter"         : "a",
            "sortParentTag"     : "#filter",
            "sortChildTag"      : "a",
            "complete"  : function(){}
        },
        // Call back function
        beforeCalculateColumn   : function(){},
        afterCalculateColumn    : function(){},
        afterColumnWidth        : function(){},
        afterImagesLoaded       : function(){}
    };
    $.fn.tzPortfolioPlusIsotope = function(options){
        if (options === undefined) options = {};
        if (typeof options === "object") {
            // Call function
            return this.each(function() {
                // Call function
                if ($(this).data("tzPortfolioPlusIsotope") === undefined) {
                    new $.tzPortfolioPlusIsotope(this, options);
                }else{
                    $(this).data('tzPortfolioPlusIsotope');
                }
            });
        }
    };


    // Variable tzPortfolioPlusInfiniteScroll plugin
    $.tzPortfolioPlusInfiniteScroll  = function(el,options){
        var $tzppScroll   = $(el),
            $var    = $.extend(true,$.tzPortfolioPlusInfiniteScroll.defaults,options),
            $params = $var.params;

        var tzpage    = 1;

        var $scroll = true,
            $ajaxData   = "",
            LastDate = $('div.TzDate:last').attr('data-category');


        $ajaxData = function () {
            var _ajaxData = {},
                ids = $tzppScroll.find($var.itemSelector).map(function () {
                    var attr = $(this).attr("data-portfolio-item-id");
                    if (typeof attr !== typeof undefined && attr !== false) {
                        return $(this).data("portfolio-item-id");
                    }
                    return $(this).attr("id").replace(/tzelement/i, "");
                }).get();

            _ajaxData["shownIds[]"] = ids;

            var $tzppIsotope    = $tzppScroll.data("tzPortfolioPlusIsotope");

            if($tzppIsotope) {
                var $isovars = $tzppIsotope.vars;
                var $optionSet = $($isovars.isotope_options.filterSelector + "[data-option-key=filter]"),
                    $optionLink = $optionSet.find($isovars.isotope_options.tagFilter + ".selected:not([data-option-value=\"*\"]),"
                        +$isovars.isotope_options.tagFilter + ".active:not([data-option-value=\"*\"])");
                var optvalue    = $optionLink.data("option-value");

                if($params.tz_filter_type === "tags"){
                    if(typeof optvalue !== typeof undefined) {
                        _ajaxData["tagAlias"] = optvalue.replace(/\./, "");
                    }
                }else{
                    if($optionLink.data("term")){
                        _ajaxData["id"] = $optionLink.data("term");
                    }else{
                        if(typeof optvalue !== typeof undefined) {
                            _ajaxData["id"] = optvalue.substr(optvalue.lastIndexOf("_") + 1, optvalue.length);
                        }
                    }
                }
            }

            /* Get fields filter */
            window.location.search.replace(/\?/i, "").split("&").map(function(value, index, arr){
                var a = value.split('=');
                if(a.length) {
                    var paramName   = a[0];
                    if(a[0].match(/^fields/i)) {
                        _ajaxData[a[0]] = a[1];
                    }
                }
            });

            $("#tz_append > a.tzNomore").remove();
            $.data(el, "tppinfscr", "loading");

            return _ajaxData;
        };

        $tzppScroll.infinitescroll({
                navSelector  : $var.navSelector,    // selector for the paged navigation
                nextSelector : $var.nextSelector,  // selector for the NEXT link (to page 2)
                itemSelector : $var.itemSelector,     // selector for all items you'll retrieve
                dataType     : $var.dataType,
                ajaxMethod   : $var.ajaxMethod,
                ajaxData     : $ajaxData,
                path: function(curpage){
                    // //         // var $url = 'index.php?option=com_tz_portfolio_plus&view=portfolio&task=portfolio.ajax&layout=default:item';
                    // //
                    // //     var $url    = $($var.nextSelector).attr('href') + "&tags="+getTags();
                    // //         // alert(getTags());
                    // //         alert($($var.nextSelector).attr('href'));
                    // //     // alert(encodeURI($url));
                    // //         return $url;
                    return $($var.nextSelector).attr('href').replace(/\&page=[0-9+]/i,"");
                },
                template: function(data){
                    if(data) {
                        if(data.filter){
                            var $newFilter = $(data.filter);
                            var $newFilter = $newFilter.map(function(){
                                var checkFilter = $($var.sortParentTag).find($var.sortChildTag+"[data-option-value=\""+$(this).data("option-value")+"\"]").index();

                                if(checkFilter == -1){
                                    return this;
                                }
                            });
                            $($var.sortParentTag).append($newFilter);
                            var $tzppIsotope    = $tzppScroll.data("tzPortfolioPlusIsotope");
                            $tzppIsotope.loadPortfolio();
                            if(typeof tzSortFilter != 'undefined') {
                                tzSortFilter($($var.sortParentTag).find($var.sortChildTag), $($var.sortParentTag), $params.filter_tags_categories_order);
                            }
                        }
                        return data.articles;
                    }
                },
                errorCallback: function() {
                    if(!$var.errorCallback) {
                        var $tzppIsotope    = $.data(el,"tzPortfolioPlusIsotope");
                        $var.loadedText =   TZ_Portfolio_Plus.infiniteScroll.noMorePageLoadText;
                        // if (!$params.tz_portfolio_plus_layout || $params.tz_portfolio_plus_layout == 'ajaxButton') {
                        //     $('#tz_append a').unbind('click').html($var.loadedText).show();
                        // }

                        $('#infscr-loading').hide();

                        if (!$params.tz_portfolio_plus_layout || $params.tz_portfolio_plus_layout == 'ajaxButton') {
                            if($tzppIsotope.find($tzppIsotope.vars.isotope_options.core.itemSelector).length >= TZ_Portfolio_Plus.infiniteScroll.countItems){
                                $('#tz_append a').html($var.loadedText).unbind("click");
                            }
                            $('#tz_append a').show();
                        }

                        if (TZ_Portfolio_Plus.infiniteScroll.displayNoMorePageLoad) {
                            // if ($params.tz_portfolio_plus_layout == 'ajaxInfiScroll') {
                            $('#tz_append').removeAttr('style').html('<a class="tzNomore">' + $var.loadedText + '</a>');
                            // }
                            // if (!$params.tz_portfolio_plus_layout || $params.tz_portfolio_plus_layout == 'ajaxButton') {
                            //     $('#tz_append a').html($var.loadedText);
                            // }
                            $('#tz_append a').addClass('tzNomore');
                        } else {
                            $('#tz_append').hide();
                        }

                        var _fSelectedClass = $tzppIsotope.vars.isotope_options.filterSelector + "[data-option-key=filter] " +
                            $tzppIsotope.vars.isotope_options.tagFilter,
                            _filterSelected = $(_fSelectedClass + ".selected," + _fSelectedClass + ".active");
                        if(_filterSelected.length) {
                            $.data(_filterSelected[0], "infinitescroll", true);
                        }

                        if($tzppIsotope.find($tzppIsotope.vars.isotope_options.core.itemSelector).length < TZ_Portfolio_Plus.infiniteScroll.countItems){
                            $scroll = true;
                        }else{
                            $('#infscr-loading').remove();
                        }

                        $.data(el, "tppinfscr", "loaded");
                    }
                },
                loading: {
                    msg: $('<div id="infscr-loading">' + $var.msgText + '</div>'),
                    msgText: $var.msgText,
                    finishedMsg: '',
                    img: '',
                    selector: '#tz_append'
                }
            },
            // call Isotope as a callback
            function( data, options, url ) {
                var newElements = data;
                if(data) {
                    // Append element if dataType is json
                    if (data.articles && !options.appendCallback) {
                        var box = $("<div/>");
                        box.append(data.articles);

                        newElements =  box.children().get();
                        $tzppScroll.append(newElements);
                    }
                    $('#infscr-loading').css('display','none');

                    var $newElems =   $( newElements ).css({ opacity: 0 });

                    // ensure that images load before adding to masonry layout
                    $newElems.imagesLoaded(function() {

                        // show elems now they're ready
                        $newElems.animate({opacity: 1});

                        var $tzppIsotope    = $tzppScroll.data("tzPortfolioPlusIsotope");
                        $tzppIsotope.tz_init();
                        if($var.timeline){
                            // trigger scroll again
                            $tzppIsotope.isotope( 'insert', $newElems);
                            // Delete date haved
                            $newElems.each(function(){
                                var tzClass = $(this).attr('class');
                                if(tzClass.match(/.*?TzDate.*?/i)){
                                    var LastDate2 = $(this).attr('data-category');
                                    if(LastDate == LastDate2){

                                        $(this).remove();
                                        $tzppIsotope.isotope('reloadItems');
                                    }
                                    else
                                        LastDate    = LastDate2;
                                }
                            });
                        }else {

                            // Append new elements.
                            $tzppIsotope.isotope('appended', $newElems);

                            // Append tags filter
                            if (data.filter && !options.appendCallback) {
                                var $newFilter = $(data.filter);
                                var $newFilter = $newFilter.map(function(){
                                    var checkFilter = $($var.sortParentTag).find($var.sortChildTag+"[data-option-value=\""+$(this).data("option-value")+"\"]").index();

                                    if(checkFilter == -1){
                                        return this;
                                    }
                                });
                                $($var.sortParentTag).append($newFilter);
                                $tzppIsotope.loadPortfolio();
                            }
                            $tzppIsotope.afterLoadPortfolio();
                            $tzppIsotope.triggerOnClickItem();

                        }

                        tzpage++;

                        $scroll = true;

                        //if there still more item
                        if($newElems.length){

                            //move item-more to the end
                            $('div#tz_append').find('a:first').show();
                        }

                        // Call functions ajaxComplete added
                        if(TZ_Portfolio_Plus.infiniteScroll.ajaxCompletes.length){
                            $.each(TZ_Portfolio_Plus.infiniteScroll.ajaxCompletes, function(index, func){
                                if(typeof func === 'function') {
                                    func($newElems, $tzppIsotope);
                                }
                            });
                        }
                        // Call load page when system can't find latest item
                        if($params.tz_portfolio_plus_layout == 'ajaxInfiScroll' || $params.tz_portfolio_plus_layout == 'ajaxButton'){
                            var index   =   $tppUtility.readCookie('tppLatestItem');
                            if (index != null) {
                                if(!$('#'+ index).length){
                                    $tzppScroll.infinitescroll('retrieve');
                                }
                            }
                        }

                        $.data(el, "tppinfscr", "loaded");
                    });
                }
            }
        );

        if($params.tz_portfolio_plus_layout == 'ajaxInfiScroll'){
            $(window).scroll(function(){
                $(window).unbind('.infscr');
                if(($(window).scrollTop() + $(window).height()) >= ($tzppScroll.offset().top + $tzppScroll.height())){

                    var $tzppIsotope    = $.data(el,"tzPortfolioPlusIsotope"),
                        _fSelectedClass = $tzppIsotope.vars.isotope_options.filterSelector + "[data-option-key=filter] " +
                            $tzppIsotope.vars.isotope_options.tagFilter,
                        _filterSelected = $(_fSelectedClass + ".selected," + _fSelectedClass + ".active"),
                        $loadDone   = _filterSelected.length?$.data(_filterSelected[0], "infinitescroll"):undefined,
                        $elAll  = $tzppIsotope.find($tzppIsotope.vars.isotope_options.itemSelector);

                    if($.data(el, "tppinfscr") === "loading" || ($elAll.length >= TZ_Portfolio_Plus.infiniteScroll.countItems ||
                        ($elAll.length < TZ_Portfolio_Plus.infiniteScroll.countItems &&
                            typeof $loadDone !== typeof undefined && $loadDone === true))){
                        return;
                    }else{
                        $("#tz_append").show();
                        $("#infscr-loading").show();
                        $tzppScroll.infinitescroll("update",{
                            state:{
                                isDone: false,
                                isDuringAjax: false,
                                isBeyondMaxPage: false
                            }
                        });
                    }
                    if($scroll){
                        $scroll	= false;
                        $tzppScroll.infinitescroll('retrieve');
                    }
                }
            });

            $(window).bind("load resize",function(){
                if($(document).height() <= $(window).height()){
                    $tzppScroll.infinitescroll('retrieve');
                }
            });

        }

        if(!$params.tz_portfolio_plus_layout || $params.tz_portfolio_plus_layout == 'ajaxButton'){
            $(window).unbind('.infscr');

            $('div#tz_append a').click(function(){
                $(this).stop();
                $('div#tz_append').find('a:first').hide();
                $tzppScroll.infinitescroll('retrieve');
            });

            if($("div#tz_append > a:first").length) {
                $.data(el, "tppinfscr_additemmore", $("div#tz_append > a:first").clone(true));
            }
        }

        // Call load page when system can't find latest item
        // if($params.tz_portfolio_plus_layout == 'ajaxInfiScroll' || $params.tz_portfolio_plus_layout == 'ajaxButton'){
        //     var index   =   $tppUtility.readCookie('tppLatestItem');
        //     if (index != null) {
        //         if(!$('#'+ index).length){
        //             $tzppScroll.infinitescroll('retrieve');
        //         }
        //     }
        // }

        $.data(el,"tzPortfolioPlusInfiniteScroll", $tzppScroll);

        return this;
    };

    // Create options object
    $.tzPortfolioPlusInfiniteScroll.defaults  = {
        rootPath        : '',
        msgText         : '<i class="tz-icon-spinner tz-spin"></i><em>Loading the next set of posts...</em>',
        loadedText      : 'No more items to load',
        navSelector     : '#loadaj a',    // selector for the paged navigation
        nextSelector    : '#loadaj a:first',  // selector for the NEXT link (to page 2)
        itemSelector    : '.element',     // selector for all items you'll retrieve
        dataType        : 'json',
        ajaxMethod      : "POST",
        ajaxData        : undefined,
        Itemid          : null,
        commentText     : 'Comment count:',
        timeline        : false,
        sortParentTag   : '#filter',
        sortChildTag    : 'a',
        lang            : '',
        errorCallback   : null,
        ajaxComplete    : function(){}

    };
    $.fn.tzPortfolioPlusInfiniteScroll   = function(options){
        if(options === undefined) options   = {};
        if(typeof options === 'object'){
            // Call function
            return this.each(function() {
                // Call function
                if ($(this).data("tzPortfolioPlusInfiniteScroll") === undefined) {
                    new $.tzPortfolioPlusInfiniteScroll(this, options);
                }else{
                    $(this).data('tzPortfolioPlusInfiniteScroll');
                }
            });
        }
    };
})(jQuery,window, window.TZ_Portfolio_Plus);