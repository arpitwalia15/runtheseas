(function($) {
    "use strict";

    var wbcom_memberCarousel = function() {
        $('.member-carousel-container').each(function() {
            var elementSettings = $(this).data('settings');
            
            if (!elementSettings) {
                return;
            }

            var slidesToShow = +elementSettings.slides_to_show || 3,
                isSingleSlide = (slidesToShow === 1),
                elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;

            var swiperOptions = {
                slidesPerView: slidesToShow,
                loop: elementSettings.infinite === 'yes',
                speed: elementSettings.speed || 300,
                autoplay: elementSettings.autoplay === 'yes' ? {
                    delay: elementSettings.autoplay_speed || 5000,
                    disableOnInteraction: elementSettings.pause_on_interaction === 'yes'
                } : false,
                breakpoints: {
                    0: {
                        slidesPerView: +elementSettings.slides_to_show_mobile || 1,
                        slidesPerGroup: +elementSettings.slides_to_scroll_mobile || 1
                    },
                    [elementorBreakpoints.mobile.value]: {
                        slidesPerView: +elementSettings.slides_to_show_tablet || 1,
                        slidesPerGroup: +elementSettings.slides_to_scroll_tablet || 1
                    },
                    [elementorBreakpoints.tablet.value]: {
                        slidesPerView: slidesToShow,
                        slidesPerGroup: +elementSettings.slides_to_scroll || 1
                    }
                }
            };

            // Navigation Arrows
            if (elementSettings.navigation === 'arrows' || elementSettings.navigation === 'both') {
                swiperOptions.navigation = {
                    prevEl: $(this).find('.elementor-swiper-button-prev').get(0),
                    nextEl: $(this).find('.elementor-swiper-button-next').get(0)
                };
            }

            // Dots Pagination
            if (elementSettings.navigation === 'dots' || elementSettings.navigation === 'both') {
                swiperOptions.pagination = {
                    el: $(this).find('.swiper-pagination').get(0),
                    type: 'bullets',
                    clickable: true
                };
            }

            // Space Between Slides
            if (elementSettings.image_spacing_custom) {
                swiperOptions.spaceBetween = elementSettings.image_spacing_custom.size;
            }

            // Initialize Swiper (Fix)
            var swiperInstance = new Swiper($(this).get(0), swiperOptions);

            // Pause on Hover (Fix)
            if (elementSettings.pause_on_hover === 'yes' && swiperInstance.autoplay) {
                $(this).on('mouseenter', function() {
                    swiperInstance.autoplay.stop();
                }).on('mouseleave', function() {
                    swiperInstance.autoplay.start();
                });
            }
        });
    };

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/wbcom-members-carousel.default', wbcom_memberCarousel);
    });

})(jQuery);
