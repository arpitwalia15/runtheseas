(function($) {
	"use strict";

	var wbcom_teamCarousel = function($scope) {
		var $container = $scope.find('.wbcom-team-carousel');

		$container.each(function() {
			var $this = $(this);
			var elementSettings = $this.data('settings');

			if (!elementSettings) {
				return;
			}

			var slidesToShow = +elementSettings.slides_to_show || 3,
				isSingleSlide = (slidesToShow === 1),
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

			// Single slide: enable fade effect
			if (isSingleSlide) {
				swiperOptions.effect = 'fade';
				swiperOptions.fadeEffect = { crossFade: true };
				$this.addClass('noanim');
			}

			// Navigation Arrows
			if (elementSettings.navigation === 'arrows' || elementSettings.navigation === 'both') {
				swiperOptions.navigation = {
					prevEl: $this.find('.elementor-swiper-button-prev').get(0),
					nextEl: $this.find('.elementor-swiper-button-next').get(0)
				};
			}

			// Dots Pagination
			if (elementSettings.navigation === 'dots' || elementSettings.navigation === 'both') {
				swiperOptions.pagination = {
					el: $this.find('.swiper-pagination').get(0),
					type: 'bullets',
					clickable: true
				};
			}

			var swiperInstance = new Swiper($this.get(0), swiperOptions);

			// Pause on Hover
			if (elementSettings.autoplay === 'yes') {
				$this.on('mouseenter', function() {
					if (swiperInstance.autoplay) {
						swiperInstance.autoplay.stop();
					}
				}).on('mouseleave', function() {
					if (swiperInstance.autoplay) {
						swiperInstance.autoplay.start();
					}
				});
			}

			// Featherlight gallery init
			$this.find('.wbcom-team-member > a.has-lightbox').featherlightGallery({
				gallery: {
					fadeIn: 300,
					fadeOut: 300
				},
				previousIcon: '<i class="fas fa-arrow-left"></i>',
				nextIcon: '<i class="fas fa-arrow-right"></i>',
				closeIcon: '<i class="fas fa-times"></i>',
				openSpeed: 300,
				closeSpeed: 300
			});

			// Hover animations for team overlay
			$this.find('.wbcom-team-overlay').hover(
				function() {
					var $overlay = $(this);
					var $title = $overlay.find('.wbcom-team-title.animated');
					var $subtitle = $overlay.find('.wbcom-team-subtitle.animated');

					$title.removeClass('wbcom-hide');
					$subtitle.removeClass('wbcom-hide');

					$title.removeClass($title.data('exit')).addClass($title.data('animation'));
					$subtitle.removeClass($subtitle.data('exit')).addClass($subtitle.data('animation'));
				},
				function() {
					var $overlay = $(this);
					var $title = $overlay.find('.wbcom-team-title.animated');
					var $subtitle = $overlay.find('.wbcom-team-subtitle.animated');

					$title.removeClass($title.data('animation')).addClass($title.data('exit'));
					$subtitle.removeClass($subtitle.data('animation')).addClass($subtitle.data('exit'));
				}
			);

			// Show carousel after init
			setTimeout(function() {
				$this.css('opacity', '1');
			}, 500);
		});
	};

	$(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction('frontend/element_ready/wbcom-team-carousel.default', wbcom_teamCarousel);
	});

})(jQuery);
