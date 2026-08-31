/* Add presentation-only hints without changing BuddyNext's authentication fields. */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var settings = window.rtsCaptainsSuiteAuth || {};
        var username = document.getElementById('bn-login-user');
        var password = document.getElementById('bn-login-password');
        var logo = document.querySelector('.bn-auth-formlogo img');

        if (logo && settings.loginLogoUrl) {
            logo.src = settings.loginLogoUrl;
        }

        if (username && !username.getAttribute('placeholder')) {
            username.setAttribute('placeholder', 'Enter your login');
        }

        if (password && !password.getAttribute('placeholder')) {
            password.setAttribute('placeholder', 'Enter your passcode');
        }
    });
}());


