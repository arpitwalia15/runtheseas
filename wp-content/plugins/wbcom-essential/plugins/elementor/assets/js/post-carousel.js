(function($) {
	"use strict";

	var wbcom_postCarousel = function() {
		$('.wbcom-post-carousel').each(function() {
			var $container = $(this);
			var elementSettings = $container.data('settings');

			if (!elementSettings) {
				return;
			}

			var slidesToShow = +elementSettings.slides_to_show || 3,
				elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints;

			var swiperOptions = {
				slidesPerView: slidesToShow,
				slidesPerGroup: 1,
				loop: elementSettings.infinite === 'yes',
				speed: elementSettings.speed || 300,
				autoplay: elementSettings.autoplay === 'yes' ? {
					delay: elementSettings.autoplay_speed || 5000,
					disableOnInteraction: false
				} : false,
				spaceBetween: 20,
				breakpoints: {
					0: {
						slidesPerView: 1,
						slidesPerGroup: 1
					},
					[elementorBreakpoints.mobile.value]: {
						slidesPerView: Math.min(slidesToShow, 2),
						slidesPerGroup: 1
					},
					[elementorBreakpoints.tablet.value]: {
						slidesPerView: slidesToShow,
						slidesPerGroup: 1
					}
				}
			};

			// Navigation Arrows
			if (elementSettings.navigation === 'arrows' || elementSettings.navigation === 'both') {
				swiperOptions.navigation = {
					prevEl: $container.find('.elementor-swiper-button-prev').get(0),
					nextEl: $container.find('.elementor-swiper-button-next').get(0)
				};
			}

			// Dots Pagination
			if (elementSettings.navigation === 'dots' || elementSettings.navigation === 'both') {
				swiperOptions.pagination = {
					el: $container.find('.swiper-pagination').get(0),
					type: 'bullets',
					clickable: true
				};
			}

			var swiperInstance = new Swiper($container.get(0), swiperOptions);

			// Pause on Hover
			if (elementSettings.autoplay === 'yes') {
				$container.on('mouseenter', function() {
					if (swiperInstance.autoplay) {
						swiperInstance.autoplay.stop();
					}
				}).on('mouseleave', function() {
					if (swiperInstance.autoplay) {
						swiperInstance.autoplay.start();
					}
				});
			}
		});
	};

	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/wbcom-post-carousel.default', wbcom_postCarousel);
	});

})(jQuery);
