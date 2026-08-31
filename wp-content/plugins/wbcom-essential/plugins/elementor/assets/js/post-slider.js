(function($) {
	"use strict";

	var wbcom_postSlider = function($scope) {
		var $container = $scope.find('.wbcom-slider');

		if (!$container.length) {
			return;
		}

		var elementSettings = $container.data('settings');

		if (!elementSettings) {
			return;
		}

		var isFade = elementSettings.slide_anim === 'true',
			speed = elementSettings.speed || 300,
			autoplay = elementSettings.autoplay,
			autoplayDuration = elementSettings.autoplay_duration || 5000,
			showNav = elementSettings.nav_arrows,
			showDots = elementSettings.nav_dots,
			useThumbnails = elementSettings.nav_thumbnails === 'yes',
			isRtl = elementSettings.rtl;

		// Text entrance animation handlers
		function onSlideChangeStart(swiper) {
			$(swiper.el).find('.swiper-slide .wbcom-slider-text-box').addClass('noanim');
			$(swiper.el).find('.swiper-slide .wbcom-slider-inner').addClass('none');
		}

		function onSlideChangeEnd(swiper) {
			var $active = $(swiper.slides[swiper.activeIndex]);
			$active.find('.wbcom-slider-text-box').removeClass('noanim');
			$active.find('.wbcom-slider-inner').removeClass('none');
		}

		function onInit(swiper) {
			var $active = $(swiper.slides[swiper.activeIndex]);
			$active.find('.wbcom-slider-text-box').removeClass('noanim');
			$active.find('.wbcom-slider-inner').removeClass('none');
		}

		// Build Swiper options
		var swiperOptions = {
			slidesPerView: 1,
			slidesPerGroup: 1,
			loop: false,
			speed: speed,
			autoplay: autoplay ? {
				delay: autoplayDuration,
				disableOnInteraction: false
			} : false,
			on: {
				init: onInit,
				slideChangeTransitionStart: onSlideChangeStart,
				slideChangeTransitionEnd: onSlideChangeEnd
			}
		};

		// Fade effect
		if (isFade) {
			swiperOptions.effect = 'fade';
			swiperOptions.fadeEffect = { crossFade: true };
		}

		// Navigation arrows
		if (showNav) {
			swiperOptions.navigation = {
				prevEl: $container.find('.elementor-swiper-button-prev').get(0),
				nextEl: $container.find('.elementor-swiper-button-next').get(0)
			};
		}

		// Dots (non-thumbnail mode)
		if (showDots && !useThumbnails) {
			swiperOptions.pagination = {
				el: $container.find('.swiper-pagination').get(0),
				type: 'bullets',
				clickable: true
			};
		}

		// Thumbnail navigation
		var thumbsSwiper = null;
		if (showDots && useThumbnails) {
			var $thumbContainer = $scope.find('.wbcom-slider-thumbnails');
			if ($thumbContainer.length) {
				thumbsSwiper = new Swiper($thumbContainer.get(0), {
					slidesPerView: 'auto',
					spaceBetween: 10,
					watchSlidesProgress: true,
					freeMode: true
				});
				swiperOptions.thumbs = {
					swiper: thumbsSwiper
				};
			}
		}

		var swiperInstance = new Swiper($container.get(0), swiperOptions);

		// Pause on hover
		if (autoplay) {
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

		// Fade out loader
		setTimeout(function() {
			$scope.find('.wbcom-slider-loader').fadeOut(200);
		}, 500);
	};

	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/wbcom-post-slider.default', wbcom_postSlider);
	});

})(jQuery);
