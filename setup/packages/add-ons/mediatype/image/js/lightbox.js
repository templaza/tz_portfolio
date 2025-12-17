(function($, window){
    $.image_addon_lightbox = function(options) {
        var lightboxopen = false;
        $('.tz_portfolio_plus_image .image-title, .tp-image .image-title').on('click', function (event) {
            event.preventDefault();
            // var $pic = $('.tz_portfolio_plus_image');
            var $clickid = $(this).attr('data-id'),
                $index = 0,
                $buttons = (typeof options.buttons !== 'undefined')?options.buttons:[];

            if ($clickid && lightboxopen === false) {
                var items   = {
                    src: $(this).attr("href"),
                    opts:{
                        caption   : $(this).data("caption"),
                        thumb   : $(this).data("thumb")
                    }
                };
                if ($(window).width() < 768) {
                    var instance = $.fancybox.open(items, {
                        loop: true,
                        thumbs: {
                            autoStart: false
                        },
                        buttons: $buttons,
                        beforeShow: function (instance, slide) {
                            lightboxopen = true;
                        },
                        afterClose: function (instance, slide) {
                            lightboxopen = false;
                        }
                    }, $index);
                } else {
                    var instance = $.fancybox.open(items, {
                        loop: true,
                        thumbs: {
                            autoStart: true
                        },
                        buttons: $buttons,
                        beforeShow: function (instance, slide) {
                            lightboxopen = true;
                        },
                        afterClose: function (instance, slide) {
                            lightboxopen = false;
                        }
                    }, $index);
                }
            }
        });
    };
})(jQuery, window);