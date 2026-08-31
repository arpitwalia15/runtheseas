<?php
/**
 * WBCom Essential Block Patterns.
 *
 * Registers pattern category and includes all pattern files.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the WBCom Essential Magazine pattern category and patterns.
 *
 * @return void
 */
function wbcom_essential_register_block_patterns() {
	register_block_pattern_category(
		'wbcom-essential-magazine',
		array(
			'label' => __( 'WBcom Essential - Magazine', 'wbcom-essential' ),
		)
	);

	register_block_pattern_category(
		'wbcom-essential-single-post',
		array(
			'label' => __( 'WBcom Essential - Single Post', 'wbcom-essential' ),
		)
	);

	$patterns_dir  = __DIR__;
	$pattern_files = array(
		// Magazine patterns.
		'magazine-homepage',
		'category-archive',
		'blog-listing',
		'news-dashboard',
		// Blog listing variations.
		'blog-masonry',
		'blog-editorial',
		'blog-timeline',
		'blog-featured-list',
		// Single post patterns.
		'single-post-author-bio',
		'single-post-related-posts',
		'single-post-newsletter-cta',
		'single-post-share-section',
	);

	foreach ( $pattern_files as $pattern_file ) {
		$file_path = $patterns_dir . '/' . $pattern_file . '.php';
		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
}
add_action( 'init', 'wbcom_essential_register_block_patterns' );
