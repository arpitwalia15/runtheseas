( function( $ ) {
	"use strict";

	var wbcom_memberslists = function() {

		$( '.wbcom-essential-members .wbcom-essential-members__tab' ).on(
			'click',
			function (e) {
				e.preventDefault();
				var $tabItem     = $( this );
				var $mType       = $tabItem.data( 'type' );
				var $bbContainer = $tabItem.closest( '.wbcom-essential-members' )
				$( '.wbcom-essential-members .wbcom-essential-members__tab' ).removeClass( 'selected' );
				$tabItem.toggleClass( 'selected' );

				$bbContainer.find( '.wbcom-essential-members-list' ).removeClass( 'active' );
				$bbContainer.find( '.wbcom-essential-members-list--' + $mType + '' ).addClass( 'active' );
			}
		);
	}

	jQuery( window ).on(
		'elementor/frontend/init',
		() => {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-members-lists.default', wbcom_memberslists );
		}
	);

})( jQuery );
