(function ($) {
	'use strict';

    $('.wbcom-notification-area .header-notifications-dropdown-toggle .dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        
        var current = $(this).closest('.wbcom-notification-area .header-notifications-dropdown-toggle');
        
        // Remove 'selected' class from all dropdowns except the one being clicked
        $('.wbcom-notification-area .header-notifications-dropdown-toggle').not(current).removeClass('selected');
        
    });

})(jQuery);
