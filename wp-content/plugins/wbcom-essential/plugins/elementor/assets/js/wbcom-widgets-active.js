;(function($){
    "use strict";

    /*
    * Product Slider
    */
    var WidgetProductSliderHandler = function ($scope, $) {

        var slider_elem = $scope.find('.product-slider').eq(0);

        if (slider_elem.length > 0) {

            slider_elem[0].style.display='block';

            var settings = slider_elem.data('settings');
            var arrows = settings['arrows'];
            var dots = settings['dots'];
            var autoplay = settings['autoplay'];
            var autoplay_speed = parseInt(settings['autoplay_speed']) || 3000;
            var animation_speed = parseInt(settings['animation_speed']) || 300;
            var pause_on_hover = settings['pause_on_hover'];
            var display_columns = parseInt(settings['product_items']) || 4;
            var scroll_columns = parseInt(settings['scroll_columns']) || 4;
            var tablet_width = parseInt(settings['tablet_width']) || 800;
            var tablet_display_columns = parseInt(settings['tablet_display_columns']) || 2;
            var tablet_scroll_columns = parseInt(settings['tablet_scroll_columns']) || 2;
            var mobile_width = parseInt(settings['mobile_width']) || 480;
            var mobile_display_columns = parseInt(settings['mobile_display_columns']) || 1;
            var mobile_scroll_columns = parseInt(settings['mobile_scroll_columns']) || 1;

            var swiperOptions = {
                slidesPerView: display_columns,
                slidesPerGroup: scroll_columns,
                loop: true,
                speed: animation_speed,
                autoplay: autoplay ? { delay: autoplay_speed, disableOnInteraction: false, pauseOnMouseEnter: pause_on_hover } : false,
                breakpoints: {
                    0: { slidesPerView: mobile_display_columns, slidesPerGroup: mobile_scroll_columns },
                    [mobile_width]: { slidesPerView: tablet_display_columns, slidesPerGroup: tablet_scroll_columns },
                    [tablet_width]: { slidesPerView: display_columns, slidesPerGroup: scroll_columns }
                }
            };

            if (arrows) {
                swiperOptions.navigation = {
                    prevEl: slider_elem.find('.elementor-swiper-button-prev').get(0),
                    nextEl: slider_elem.find('.elementor-swiper-button-next').get(0)
                };
            }

            if (dots) {
                swiperOptions.pagination = {
                    el: slider_elem.find('.swiper-pagination').get(0),
                    type: 'bullets',
                    clickable: true
                };
            }

            new Swiper(slider_elem.get(0), swiperOptions);
        };
    };

    /*
    * Custom Tab
    */
    function wbcom_tabs( $tabmenus, $tabpane ){
        $tabmenus.on('click', 'a', function(e){
            e.preventDefault();
            var $this = $(this),
                $target = $this.attr('href');
            $this.addClass('htactive').parent().siblings().children('a').removeClass('htactive');
            $( $tabpane + $target ).addClass('htactive').siblings().removeClass('htactive');

            // Swiper update
            var swiperContainers = $($target).find('.swiper-container');
            swiperContainers.each(function() {
                if (this.swiper) { this.swiper.update(); }
            });

        });
    }

    /*
    * Universal product
    */
    function productImageThumbnailsSlider( $slider ){
        $slider.each(function() {
            var $this = $(this);
            new Swiper($this.get(0), {
                slidesPerView: 1,
                pagination: {
                    el: $this.find('.swiper-pagination').get(0),
                    type: 'bullets',
                    clickable: true
                },
                navigation: {
                    prevEl: $this.find('.elementor-swiper-button-prev').get(0),
                    nextEl: $this.find('.elementor-swiper-button-next').get(0)
                }
            });
        });
    }
    if( $(".wb-product-image-slider").length > 0 ) {
        productImageThumbnailsSlider( $(".wb-product-image-slider") );
    }

    var WidgetThumbnaisImagesHandler = function thumbnailsimagescontroller(){
        wbcom_tabs( $(".wb-product-cus-tab-links"), '.wb-product-cus-tab-pane' );
        wbcom_tabs( $(".wb-tab-menus"), '.wb-tab-pane' );
    }

    /*
    * Tool Tip
    */
    function wbcom_tool_tips(element, content) {
        if ( content == 'html' ) {
            var tipText = element.html();
        } else {
            var tipText = element.attr('title');
        }
        element.on('mouseover', function() {
            if ( $('.wbcom-tip').length == 0 ) {
                element.before('<span class="wbcom-tip">' + tipText + '</span>');
                $('.wbcom-tip').css('transition', 'all 0.5s ease 0s');
                $('.wbcom-tip').css('margin-left', 0);
            }
        });
        element.on('mouseleave', function() {
            $('.wbcom-tip').remove();
        });
    }

    /*
    * Tooltip Render
    */
    var WidgetWoolentorTooltipHandler = function wbcom_tool_tip(){
        $('a.wbcom-compare').each(function() {
            wbcom_tool_tips( $(this), 'title' );
        });
        $('.wbcom-cart a.add_to_cart_button,.wbcom-cart a.added_to_cart,.wbcom-cart a.button').each(function() {
            wbcom_tool_tips( $(this), 'html');
        });
    }

    /*
    * Product Tab
    */
    var  WidgetProducttabsHandler = wbcom_tabs( $(".wb-tab-menus"),'.wb-tab-pane' );

    /*
    * Run this code under Elementor.
    */
    $(window).on('elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-product-tab.default', WidgetProductSliderHandler);
        elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-product-tab.default', WidgetProducttabsHandler);

        elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-universal-product.default', WidgetProductSliderHandler);
        elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-universal-product.default', WidgetWoolentorTooltipHandler);
        elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-universal-product.default', WidgetThumbnaisImagesHandler);

        elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-wc-testimonial.default', WidgetProductSliderHandler );

    });

})(jQuery);
