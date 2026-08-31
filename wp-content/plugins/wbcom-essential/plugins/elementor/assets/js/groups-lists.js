( function( $ ) {
	"use strict";

	var wbcom_groupslists = function() {

		$( '.wbcom-essential-groups .wbcom-essential-groups__tab' ).on(
			'click',
			function (e) {
				e.preventDefault();
				var $tabItem     = $( this );
				var $mType       = $tabItem.data( 'type' );
				var $bbContainer = $tabItem.closest( '.wbcom-essential-groups' )
				$( '.wbcom-essential-groups .wbcom-essential-groups__tab' ).removeClass( 'selected' );
				$tabItem.toggleClass( 'selected' );

				$bbContainer.find( '.wbcom-essential-groups-list' ).removeClass( 'active' );
				$bbContainer.find( '.wbcom-essential-groups-list--' + $mType + '' ).addClass( 'active' );
			}
		);
	}

	jQuery( window ).on(
		'elementor/frontend/init',
		() => {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-groups-lists.default', wbcom_groupslists );
		}
	);

})( jQuery );
