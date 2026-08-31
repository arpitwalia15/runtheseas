( function( $ ) {
	"use strict";

	var wbcom_profilecompletion = function() {

		var readyStateProfile = true;

		$( '.profile_bit' ).click(
			function(event) {
				event.stopPropagation();

				if ( ! $( this ).find( '.profile_bit__details' ).is( ':visible' ) && readyStateProfile ) {
					$( this ).find( '.profile_bit__details' ).slideDown();
					$( this ).addClass( 'active' );
					setTimeout(
						function(){
							readyStateProfile = false;
						},
						300
					);
				} else if ( $( this ).find( '.profile_bit__details' ).is( ':visible' ) && ! readyStateProfile ) {
					$( this ).find( '.profile_bit__details' ).slideUp();
					$( this ).removeClass( 'active' );
					setTimeout(
						function(){
							readyStateProfile = true;
						},
						300
					);
				}
			}
		);

		$( '.profile_bit' ).hover(
			function(){
				if ( ! $( this ).find( '.profile_bit__details' ).is( ':visible' ) && readyStateProfile ) {
					$( this ).find( '.profile_bit__details' ).slideDown();
					$( this ).addClass( 'active' );
					setTimeout(
						function(){
							readyStateProfile = false;
						},
						300
					);
				}
			},
			function(){
				if ($( this ).find( '.profile_bit__details' ).is( ':visible' ) && ! readyStateProfile ) {
					$( this ).find( '.profile_bit__details' ).slideUp();
					$( this ).removeClass( 'active' );
					setTimeout(
						function(){
							readyStateProfile = true;
						},
						300
					);
				}
			}
		);
	}

	jQuery( window ).on(
		'elementor/frontend/init',
		() => {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/wbcom-profile-completion.default', wbcom_profilecompletion );
		}
	);

})( jQuery );
