<?php
/**
 * Gutenberg Blocks Loader for WBcom Essential.
 *
 * Registers the block category and bootstraps BlockRegistrar.
 *
 * @package Wbcom_Essential
 * @since   4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// BlockRegistrar lives next to this file.
require_once __DIR__ . '/BlockRegistrar.php';

// Shared PHP infrastructure for v2 blocks.
require_once __DIR__ . '/includes/class-wbe-css.php';
require_once __DIR__ . '/includes/class-wbe-schema.php';
require_once __DIR__ . '/includes/class-wbe-fonts.php';

// Initialize shared infrastructure.
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::init();
\WBCOM_ESSENTIAL\Gutenberg\WBE_Schema::init();
\WBCOM_ESSENTIAL\Gutenberg\WBE_Fonts::init();

/**
 * Register shared Swiper library for carousel blocks.
 * block.json viewScript arrays reference the "swiper" handle.
 */
add_action(
	'init',
	function () {
		wp_register_style(
			'wbe-swiper',
			WBCOM_ESSENTIAL_URL . 'plugins/elementor/assets/css/swiper-base.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);
		wp_register_script(
			'swiper',
			WBCOM_ESSENTIAL_URL . 'plugins/elementor/assets/js/swiper.min.js',
			array(),
			WBCOM_ESSENTIAL_VERSION,
			true
		);
	}
);

/**
 * Enqueue Swiper base CSS when any carousel block is on the page.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		global $post;
		if ( ! $post ) {
			return;
		}
		if ( false !== strpos( $post->post_content, 'members-carousel' )
			|| false !== strpos( $post->post_content, 'group-carousel' )
			|| false !== strpos( $post->post_content, 'post-carousel' )
			|| false !== strpos( $post->post_content, 'product-carousel' )
			|| false !== strpos( $post->post_content, 'testimonial-carousel' )
			|| false !== strpos( $post->post_content, 'customer-reviews' )
		) {
			wp_enqueue_style( 'wbe-swiper' );
		}
	},
	5
);

/**
 * Enqueue shared design tokens + base styles on frontend and editor.
 * These provide --wbe-* CSS custom properties used by all blocks.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		global $post;
		if ( ! $post || false === strpos( $post->post_content, '<!-- wp:wbcom-essential/' ) ) {
			return;
		}
		wp_enqueue_style(
			'wbe-design-tokens',
			WBCOM_ESSENTIAL_URL . 'plugins/gutenberg/assets/shared/design-tokens.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);
		wp_enqueue_style(
			'wbe-base',
			WBCOM_ESSENTIAL_URL . 'plugins/gutenberg/assets/shared/base.css',
			array( 'wbe-design-tokens' ),
			WBCOM_ESSENTIAL_VERSION
		);
		wp_enqueue_style(
			'wbe-theme-isolation',
			WBCOM_ESSENTIAL_URL . 'plugins/gutenberg/assets/shared/theme-isolation.css',
			array( 'wbe-base' ),
			WBCOM_ESSENTIAL_VERSION
		);
	}
);
add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style(
			'wbe-design-tokens',
			WBCOM_ESSENTIAL_URL . 'plugins/gutenberg/assets/shared/design-tokens.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);
		wp_enqueue_style(
			'wbe-base',
			WBCOM_ESSENTIAL_URL . 'plugins/gutenberg/assets/shared/base.css',
			array( 'wbe-design-tokens' ),
			WBCOM_ESSENTIAL_VERSION
		);
		wp_enqueue_style(
			'wbe-theme-isolation',
			WBCOM_ESSENTIAL_URL . 'plugins/gutenberg/assets/shared/theme-isolation.css',
			array( 'wbe-base' ),
			WBCOM_ESSENTIAL_VERSION
		);
	}
);

/**
 * Register block categories for all WBcom Essential blocks.
 */
add_filter(
	'block_categories_all',
	function ( $categories ) {
		$wbe_categories = array(
			array(
				'slug'  => 'essential-header',
				'title' => __( 'Essential - Header', 'wbcom-essential' ),
				'icon'  => 'screenoptions',
			),
			array(
				'slug'  => 'essential-design',
				'title' => __( 'Essential - Design', 'wbcom-essential' ),
				'icon'  => 'art',
			),
			array(
				'slug'  => 'essential-content',
				'title' => __( 'Essential - Content', 'wbcom-essential' ),
				'icon'  => 'edit',
			),
			array(
				'slug'  => 'essential-blog',
				'title' => __( 'Essential - Blog', 'wbcom-essential' ),
				'icon'  => 'admin-post',
			),
			array(
				'slug'  => 'essential-marketing',
				'title' => __( 'Essential - Marketing', 'wbcom-essential' ),
				'icon'  => 'megaphone',
			),
			array(
				'slug'  => 'essential-buddypress',
				'title' => __( 'Essential - BuddyPress', 'wbcom-essential' ),
				'icon'  => 'groups',
			),
			array(
				'slug'  => 'essential-woocommerce',
				'title' => __( 'Essential - WooCommerce', 'wbcom-essential' ),
				'icon'  => 'cart',
			),
			array(
				'slug'  => 'essential-edd',
				'title' => __( 'Essential - EDD', 'wbcom-essential' ),
				'icon'  => 'download',
			),
			array(
				'slug'  => 'wbcom-essential',
				'title' => __( 'WBcom Essential', 'wbcom-essential' ),
				'icon'  => 'screenoptions',
			),
		);

		// Avoid duplicate registration.
		$existing_slugs = array_column( $categories, 'slug' );
		foreach ( $wbe_categories as $cat ) {
			if ( ! in_array( $cat['slug'], $existing_slugs, true ) ) {
				array_unshift( $categories, $cat );
			}
		}

		return $categories;
	}
);

/**
 * Bootstrap block registration.
 *
 * Points to the build/blocks/ directory where compiled block.json + assets live.
 */
$wbcom_block_registrar = new \WBCOM_ESSENTIAL\Gutenberg\BlockRegistrar(
	WBCOM_ESSENTIAL_PATH . 'build/blocks/'
);
$wbcom_block_registrar->init();

/**
 * Register REST API routes for block editor.
 */
add_action(
	'rest_api_init',
	function () {
		// BuddyPress xProfile groups endpoint for profile-completion and members blocks.
		register_rest_route(
			'wbcom-essential/v1',
			'/xprofile-groups',
			array(
				'methods'             => 'GET',
				'callback'            => 'wbe_get_xprofile_groups',
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
);

/**
 * Get BuddyPress xProfile groups for the block editor.
 *
 * @return WP_REST_Response
 */
function wbe_get_xprofile_groups() {
	$groups = array();

	if ( ! function_exists( 'bp_xprofile_get_groups' ) ) {
		return rest_ensure_response( $groups );
	}

	$profile_groups = bp_xprofile_get_groups();

	if ( ! empty( $profile_groups ) ) {
		foreach ( $profile_groups as $group ) {
			$groups[] = array(
				'id'   => $group->id,
				'name' => $group->name,
			);
		}
	}

	$photo_options = array();

	if ( function_exists( 'bp_disable_avatar_uploads' ) && ! bp_disable_avatar_uploads() ) {
		$photo_options[] = array(
			'id'   => 'profile_photo',
			'name' => __( 'Profile Photo', 'wbcom-essential' ),
		);
	}

	if ( function_exists( 'bp_disable_cover_image_uploads' ) && ! bp_disable_cover_image_uploads() ) {
		$photo_options[] = array(
			'id'   => 'cover_photo',
			'name' => __( 'Cover Photo', 'wbcom-essential' ),
		);
	}

	return rest_ensure_response(
		array(
			'fieldGroups'  => $groups,
			'photoOptions' => $photo_options,
		)
	);
}
