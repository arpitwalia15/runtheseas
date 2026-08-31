<?php
/**
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/plugins
 * @since             1.0.0
 * @package           Wbcom_Essential
 *
 * @wordpress-plugin
 * Plugin Name:       Wbcom Essential
 * Plugin URI:        https://wbcomdesigns.com/downloads/wbcom-essential/
 * Description:       Premium Elementor widgets and 32 production-grade Gutenberg V2 blocks for BuddyPress, WooCommerce, EDD, and WordPress. Built on a shared infrastructure for responsive, accessible, theme-aware design.
 * Version:           4.5.0
 * Requires at least: 6.0
 * Tested up to:      6.9
 * Requires PHP:      8.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wbcom-essential
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WBCOM_ESSENTIAL_VERSION', '4.5.0' );
define( 'WBCOM_ESSENTIAL_PREVIOUS_STABLE_VERSION', '4.3.0' );

define( 'WBCOM_ESSENTIAL_FILE', __FILE__ );
define( 'WBCOM_ESSENTIAL_PLUGIN_BASE', plugin_basename( WBCOM_ESSENTIAL_FILE ) );
define( 'WBCOM_ESSENTIAL_PATH', plugin_dir_path( WBCOM_ESSENTIAL_FILE ) );
define( 'WBCOM_ESSENTIAL_URL', plugins_url( '/', WBCOM_ESSENTIAL_FILE ) );
define( 'WBCOM_ESSENTIAL_ASSETS_URL', WBCOM_ESSENTIAL_URL . 'assets/' );

// License constants.
define( 'WBCOM_ESSENTIAL_STORE_URL', 'https://wbcomdesigns.com' );
define( 'WBCOM_ESSENTIAL_ITEM_ID', 1545975 );
/**
 * Backward compatibility constants
 *
 * @deprecated Use WBCOM_ESSENTIAL_PATH and WBCOM_ESSENTIAL_PLUGIN_BASE instead
 */
define( 'WBCOM_ESSENTIAL_PLUGIN_DIR', WBCOM_ESSENTIAL_PATH );
define( 'WBCOM_ESSENTIAL_PLUGIN_BASENAME', WBCOM_ESSENTIAL_PLUGIN_BASE );


// Load EDD SL SDK for license and update handling.
$wbcom_essential_sdk = WBCOM_ESSENTIAL_PATH . 'vendor/easy-digital-downloads/edd-sl-sdk/edd-sl-sdk.php';
if ( file_exists( $wbcom_essential_sdk ) ) {
	require_once $wbcom_essential_sdk;
}

add_action(
	'edd_sl_sdk_registry',
	function ( $registry ) {
		$registry->register(
			array(
				'id'          => 'wbcom-essential',
				'url'         => WBCOM_ESSENTIAL_STORE_URL,
				'item_id'     => WBCOM_ESSENTIAL_ITEM_ID,
				'version'     => WBCOM_ESSENTIAL_VERSION,
				'file'        => WBCOM_ESSENTIAL_FILE,
				'option_name' => 'wbcom_essential_license_key',
				'type'        => 'plugin',
			)
		);
	}
);

require_once WBCOM_ESSENTIAL_PATH . 'wbcom-essential.php';
require_once WBCOM_ESSENTIAL_PATH . 'includes/wbcom-essential-function.php';
require_once WBCOM_ESSENTIAL_PATH . 'includes/edd-account-login-security.php';
require_once WBCOM_ESSENTIAL_PATH . 'plugins/elementor/wbcom-essential-elementor.php';
require_once WBCOM_ESSENTIAL_PATH . 'plugins/elementor/Plugins.php';
require_once WBCOM_ESSENTIAL_PATH . 'plugins/elementor/wbcom-essential-woocommerce.php';

/**
 * Returns the Plugin application instance.
 *
 * @return \WBCOM_ESSENTIAL\Plugin
 * @since 3.0.0
 */
function wbcom_essential() {
	return \WBCOM_ESSENTIAL\WBCOMESSENTIAL::get_instance();
}

/**
 * Initializes the Plugin application.
 *
 * @since 3.0.0
 */
wbcom_essential();

/**
 * Fires on plugin activation.
 *
 * Stores the activation timestamp and current version so future upgrades
 * can detect cold installs vs. in-place upgrades. Also exposes a
 * do_action hook for integrations that want to run setup code.
 *
 * @since 4.5.0
 * @return void
 */
function wbcom_essential_activate() {
	$previous_version = get_option( 'wbcom_essential_version', '' );

	update_option( 'wbcom_essential_version', WBCOM_ESSENTIAL_VERSION );

	if ( ! get_option( 'wbcom_essential_activated_at', 0 ) ) {
		update_option( 'wbcom_essential_activated_at', time() );
	}

	/**
	 * Fires after Wbcom Essential finishes its activation routine.
	 *
	 * @since 4.5.0
	 *
	 * @param string $previous_version Version stored before activation, empty on fresh install.
	 */
	do_action( 'wbcom_essential_activated', $previous_version );
}
register_activation_hook( WBCOM_ESSENTIAL_FILE, 'wbcom_essential_activate' );

/**
 * Fires on plugin deactivation.
 *
 * Keeps options intact (removal happens in uninstall.php) but exposes a
 * do_action hook so integrations can clear their own caches / transients.
 *
 * @since 4.5.0
 * @return void
 */
function wbcom_essential_deactivate() {
	/**
	 * Fires after Wbcom Essential is deactivated.
	 *
	 * @since 4.5.0
	 */
	do_action( 'wbcom_essential_deactivated' );
}
register_deactivation_hook( WBCOM_ESSENTIAL_FILE, 'wbcom_essential_deactivate' );
