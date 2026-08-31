<?php
/**
 * Single Post Template Loader.
 *
 * Hooks into single_template to serve custom single post layouts.
 * Supports per-post meta override and global default option.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/includes/single-post
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Single_Post_Templates
 */
class Wbcom_Single_Post_Templates {

	/**
	 * Valid template slugs.
	 *
	 * @var array
	 */
	private $valid_templates = array( 'classic', 'magazine', 'minimal', 'modern' );

	/**
	 * Current active template slug.
	 *
	 * @var string
	 */
	private $active_template = '';

	/**
	 * Initialize hooks.
	 */
	public function __construct() {
		add_filter( 'single_template', array( $this, 'override_single_template' ), 99 );
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Get the resolved template slug for the current post.
	 *
	 * @param int|null $post_id Post ID. Defaults to current post.
	 * @return string Template slug or empty string.
	 */
	public function get_resolved_template( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return '';
		}

		$per_post = get_post_meta( $post_id, '_wbcom_single_post_template', true );

		if ( 'none' === $per_post ) {
			return '';
		}

		if ( $per_post && in_array( $per_post, $this->valid_templates, true ) ) {
			return $per_post;
		}

		if ( ! $per_post || 'global' === $per_post ) {
			$global = get_option( 'wbcom_essential_single_post_template', '' );
			if ( $global && in_array( $global, $this->valid_templates, true ) ) {
				return $global;
			}
		}

		return '';
	}

	/**
	 * Override the single post template.
	 *
	 * @param string $template Default template path.
	 * @return string Modified template path.
	 */
	public function override_single_template( $template ) {
		if ( ! is_singular( 'post' ) ) {
			return $template;
		}

		$slug = $this->get_resolved_template();

		if ( ! $slug ) {
			return $template;
		}

		$custom_template = WBCOM_ESSENTIAL_PATH . 'templates/single-post/template-' . $slug . '.php';

		if ( file_exists( $custom_template ) ) {
			$this->active_template = $slug;
			return $custom_template;
		}

		return $template;
	}

	/**
	 * Add body classes when a custom template is active.
	 *
	 * @param array $classes Existing body classes.
	 * @return array Modified body classes.
	 */
	public function add_body_classes( $classes ) {
		if ( ! is_singular( 'post' ) ) {
			return $classes;
		}

		$slug = $this->get_resolved_template();

		if ( $slug ) {
			$classes[] = 'wbcom-sp-active';
			$classes[] = 'wbcom-sp--' . $slug;
		}

		return $classes;
	}

	/**
	 * Enqueue CSS and JS assets for active templates.
	 */
	public function enqueue_assets() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$slug = $this->get_resolved_template();

		if ( ! $slug ) {
			return;
		}

		$base_url = WBCOM_ESSENTIAL_URL . 'assets/single-post/';
		$version  = WBCOM_ESSENTIAL_VERSION;

		wp_enqueue_style(
			'wbcom-sp-base',
			$base_url . 'css/single-post-base.css',
			array(),
			$version
		);

		$template_css = $base_url . 'css/template-' . $slug . '.css';
		wp_enqueue_style(
			'wbcom-sp-' . $slug,
			$template_css,
			array( 'wbcom-sp-base' ),
			$version
		);

		if ( 'modern' === $slug ) {
			wp_enqueue_script(
				'wbcom-sp-reading-progress',
				$base_url . 'js/reading-progress.js',
				array(),
				$version,
				true
			);
		}
	}
}
