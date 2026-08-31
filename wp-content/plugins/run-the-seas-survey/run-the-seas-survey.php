<?php

/**
 * Plugin Name: Run The Seas - Survey
 * Plugin URI: https://runtheseas.com/
 * Description: Advanced survey management with gamification, referral system, and Captain's Suite
 * Version: 1.2.80
 * License: GPL v2 or later
 * Text Domain: run-the-seas
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('RTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RTS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RTS_VERSION', '1.2.80');
define('RTS_MANAGE_CAPABILITY', 'rts_manage_surveys');


// Load the core class and its concern-specific implementations.
require_once RTS_PLUGIN_PATH . 'includes/trait-rts-database-schema.php';
require_once RTS_PLUGIN_PATH . 'includes/trait-rts-frontend-assets.php';
require_once RTS_PLUGIN_PATH . 'includes/trait-rts-survey-ajax.php';
require_once RTS_PLUGIN_PATH . 'includes/trait-rts-registration-ajax.php';
require_once RTS_PLUGIN_PATH . 'includes/trait-rts-analytics-ajax.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-email-template-integration.php';
require_once RTS_PLUGIN_PATH . 'includes/class-run-the-seas-plugin.php';

// Initialize the plugin
function rts_init()
{
    return RunTheSeasPlugin::get_instance();
}
add_action('plugins_loaded', 'rts_init');

/** Ensure verification tracking fields exist when upgrading an active install. */
function rts_maybe_upgrade_verification_schema()
{
    $schema_version = '1.0';
    if (get_option('rts_verification_schema_version') === $schema_version) {
        return;
    }

    $plugin = RunTheSeasPlugin::get_instance();
    if ($plugin->ensure_participant_verification_columns()) {
        update_option('rts_verification_schema_version', $schema_version, false);
    }
}
add_action('plugins_loaded', 'rts_maybe_upgrade_verification_schema', 11);

// Register activation hook
function rts_activate_plugin()
{
    rts_register_admin_role();

    $plugin = RunTheSeasPlugin::get_instance();
    $plugin->create_tables();
    $plugin->create_registration_tables();
    $plugin->create_race_tables();

    if (!get_option('rts_qr_terms_version')) {
        update_option('rts_qr_terms_version', '1.0');
    }
}
register_activation_hook(__FILE__, 'rts_activate_plugin');

// Load feature modules. Each module owns its functions and hook registrations.
require_once RTS_PLUGIN_PATH . 'includes/rts-admin-dashboard.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-member-shortcodes.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-leaderboard-shortcodes.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-dashboard-widgets.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-survey-shortcodes.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-marathon-challenge.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-captains-suite.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-journey-shortcode.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-user-verification.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-referrals-trophies.php';
require_once RTS_PLUGIN_PATH . 'includes/rts-auth-registration.php';

add_filter('http_request_timeout', function ($timeout, $url) {
    if (
        strpos($url, 'graph.microsoft.com') !== false ||
        strpos($url, 'login.microsoftonline.com') !== false
    ) {
        return 20;
    }

    return $timeout;
}, 10, 2);
