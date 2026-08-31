<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Removes every option and post meta key the plugin writes to the
 * database. Runs only when the user clicks "Delete" on the plugin
 * screen — not on a simple deactivate.
 *
 * @package Wbcom_Essential
 * @since   4.5.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Options owned by the plugin (register_setting + explicit update_option).
 */
$wbcom_essential_options = array(
	'wbcom_essential_blog_index_layout',
	'wbcom_essential_blog_posts_per_page',
	'wbcom_essential_blog_show_ticker',
	'wbcom_essential_blog_show_category_nav',
	'wbcom_essential_blog_show_slider',
	'wbcom_essential_blog_sections',
	'wbcom_essential_single_post_template',
	'wbcom_essential_elementor_notice_dismissed',
	'wbcom_essential_license_key',
	'wbcom_essential_version',
	'wbcom_essential_activated_at',
	// Elementor widget defaults written through wbcom_get_option().
	'wbcom_style_tabs',
	'wbcom_others_tabs',
);

/**
 * Post meta written by the per-post single-post template control.
 */
$wbcom_essential_post_meta_keys = array(
	'_wbcom_single_post_template',
);

if ( is_multisite() ) {
	$wbcom_essential_sites = get_sites( array( 'number' => 0 ) );

	foreach ( $wbcom_essential_sites as $wbcom_essential_site ) {
		switch_to_blog( (int) $wbcom_essential_site->blog_id );

		foreach ( $wbcom_essential_options as $wbcom_essential_option ) {
			delete_option( $wbcom_essential_option );
		}

		foreach ( $wbcom_essential_post_meta_keys as $wbcom_essential_meta_key ) {
			delete_post_meta_by_key( $wbcom_essential_meta_key );
		}

		restore_current_blog();
	}
} else {
	foreach ( $wbcom_essential_options as $wbcom_essential_option ) {
		delete_option( $wbcom_essential_option );
	}

	foreach ( $wbcom_essential_post_meta_keys as $wbcom_essential_meta_key ) {
		delete_post_meta_by_key( $wbcom_essential_meta_key );
	}
}
