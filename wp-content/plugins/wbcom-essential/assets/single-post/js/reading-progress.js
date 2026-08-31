/**
 * Reading Progress Bar
 *
 * Calculates scroll progress based on the article content element
 * and updates the progress bar width. Modern template only, no deps.
 *
 * @package wbcom-essential
 * @since 4.3.0
 */
( function () {
	'use strict';

	var bar = document.getElementById( 'wbcom-sp-progress-bar' );
	var content = document.getElementById( 'wbcom-sp-content' );

	if ( ! bar || ! content ) {
		return;
	}

	var ticking = false;

	function updateProgress() {
		var rect = content.getBoundingClientRect();
		var contentTop = rect.top + window.scrollY;
		var contentHeight = content.offsetHeight;
		var windowHeight = window.innerHeight;
		var scrolled = window.scrollY - contentTop;
		var total = contentHeight - windowHeight;

		if ( total <= 0 ) {
			bar.style.width = '100%';
			return;
		}

		var progress = Math.min( Math.max( scrolled / total, 0 ), 1 );
		bar.style.width = ( progress * 100 ) + '%';
		ticking = false;
	}

	window.addEventListener( 'scroll', function () {
		if ( ! ticking ) {
			window.requestAnimationFrame( updateProgress );
			ticking = true;
		}
	}, { passive: true } );

	updateProgress();
} )();
