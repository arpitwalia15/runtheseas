(function($) {
	"use strict";

	var wbcom_slider = function($scope) {
		$scope.find('.wbcom-slider').each(function() {
			var $slider = $(this);
			var elementSettings = $slider.data('settings');

			if (!elementSettings) {
				return;
			}

			var useFade = elementSettings.effect === 'fade';
			var useThumbnails = elementSettings.nav_thumbnails === 'yes';
			var thumbSwiperInstance = null;

			// Build Swiper options.
			var swiperOptions = {
				slidesPerView: 1,
				slidesPerGroup: 1,
				loop: true,
				speed: elementSettings.speed || 300,
				autoplay: elementSettings.autoplay === 'yes' ? {
					delay: elementSettings.autoplay_speed || 5000,
					disableOnInteraction: false
				} : false,
				on: {
					init: function() {
						var $active = $slider.find('.swiper-slide-active');
						$active.find('.wbcom-slider-text-box').removeClass('noanim');
						$active.find('.wbcom-slider-inner').removeClass('none');

						// Fade out loader after init.
						setTimeout(function() {
							$scope.find('.wbcom-slider-loader').fadeOut(200);
						}, 300);
					},
					slideChangeTransitionStart: function() {
						$slider.find('.swiper-slide .wbcom-slider-text-box').addClass('noanim');
						$slider.find('.swiper-slide .wbcom-slider-inner').addClass('none');
					},
					slideChangeTransitionEnd: function() {
						var $active = $slider.find('.swiper-slide-active');
						$active.find('.wbcom-slider-text-box').removeClass('noanim');
						$active.find('.wbcom-slider-inner').removeClass('none');
					}
				}
			};

			// Fade effect.
			if (useFade) {
				swiperOptions.effect = 'fade';
				swiperOptions.fadeEffect = { crossFade: true };
			}

			// Navigation Arrows.
			if (elementSettings.navigation === 'yes') {
				swiperOptions.navigation = {
					prevEl: $slider.find('.elementor-swiper-button-prev').get(0),
					nextEl: $slider.find('.elementor-swiper-button-next').get(0)
				};
			}

			// Dots Pagination (only if not using thumbnails).
			if (elementSettings.dots === 'yes' && !useThumbnails) {
				swiperOptions.pagination = {
					el: $slider.find('.swiper-pagination').get(0),
					type: 'bullets',
					clickable: true
				};
			}

			// Thumbnail Navigation.
			if (useThumbnails) {
				var $thumbContainer = $scope.find('.wbcom-slider-thumbnails');
				if ($thumbContainer.length) {
					thumbSwiperInstance = new Swiper($thumbContainer.get(0), {
						slidesPerView: 'auto',
						spaceBetween: 0,
						watchSlidesVisibility: true,
						watchSlidesProgress: true
					});
					swiperOptions.thumbs = {
						swiper: thumbSwiperInstance
					};
				}
			}

			// Initialize main Swiper.
			var swiperInstance = new Swiper($slider.get(0), swiperOptions);

			// Pause on Hover.
			if (elementSettings.autoplay === 'yes') {
				$slider.on('mouseenter', function() {
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
		elementorFrontend.hooks.addAction('frontend/element_ready/wbcom-slider.default', wbcom_slider);
	});

})(jQuery);
