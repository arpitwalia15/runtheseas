<?php
/**
 * Blog Index Template Loader.
 *
 * Hooks into home_template / archive_template to serve custom blog layouts.
 * Uses the global setting from Wbcom_Blog_Index_Settings.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/includes/blog-index
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Blog_Index_Templates
 */
class Wbcom_Blog_Index_Templates {

	/**
	 * Valid layout slugs.
	 *
	 * @var array
	 */
	private $valid_layouts = array( 'grid', 'list', 'magazine', 'newspaper' );

	/**
	 * Initialize hooks.
	 */
	public function __construct() {
		add_filter( 'home_template', array( $this, 'override_blog_template' ), 99 );
		add_filter( 'archive_template', array( $this, 'override_blog_template' ), 99 );
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'pre_get_posts', array( $this, 'modify_posts_per_page' ) );
	}

	/**
	 * Get the resolved layout slug.
	 *
	 * @return string Layout slug or empty string.
	 */
	public function get_resolved_layout() {
		$layout = get_option( 'wbcom_essential_blog_index_layout', '' );

		if ( $layout && in_array( $layout, $this->valid_layouts, true ) ) {
			return $layout;
		}

		return '';
	}

	/**
	 * Override the blog/archive template.
	 *
	 * @param string $template Default template path.
	 * @return string Modified template path.
	 */
	public function override_blog_template( $template ) {
		if ( is_admin() || ! $this->is_blog_page() ) {
			return $template;
		}

		$slug = $this->get_resolved_layout();

		if ( ! $slug ) {
			return $template;
		}

		$custom_template = WBCOM_ESSENTIAL_PATH . 'templates/blog-index/template-' . $slug . '.php';

		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}

		return $template;
	}

	/**
	 * Check if current page is a blog listing page.
	 *
	 * @return bool
	 */
	private function is_blog_page() {
		return is_home() || is_category() || is_tag() || is_author() || is_date();
	}

	/**
	 * Add body classes when a custom layout is active.
	 *
	 * @param array $classes Existing body classes.
	 * @return array Modified body classes.
	 */
	public function add_body_classes( $classes ) {
		if ( ! $this->is_blog_page() ) {
			return $classes;
		}

		$slug = $this->get_resolved_layout();

		if ( $slug ) {
			$classes[] = 'wbcom-blog-active';
			$classes[] = 'wbcom-blog--' . $slug;
		}

		return $classes;
	}

	/**
	 * Enqueue CSS assets for active layouts.
	 */
	public function enqueue_assets() {
		if ( ! $this->is_blog_page() ) {
			return;
		}

		$slug = $this->get_resolved_layout();

		if ( ! $slug ) {
			return;
		}

		$base_url = WBCOM_ESSENTIAL_URL . 'assets/blog-index/css/';
		$version  = WBCOM_ESSENTIAL_VERSION;

		wp_enqueue_style(
			'wbcom-blog-base',
			$base_url . 'blog-index-base.css',
			array(),
			$version
		);

		$layout_css_file = WBCOM_ESSENTIAL_PATH . 'assets/blog-index/css/layout-' . $slug . '.css';
		if ( file_exists( $layout_css_file ) ) {
			wp_enqueue_style(
				'wbcom-blog-' . $slug,
				$base_url . 'layout-' . $slug . '.css',
				array( 'wbcom-blog-base' ),
				$version
			);
		}
	}

	/**
	 * Modify posts per page based on plugin setting.
	 * Only for simple layouts (grid/list). Magazine/Newspaper manage their own queries.
	 *
	 * @param \WP_Query $query Main query.
	 */
	public function modify_posts_per_page( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( ! $this->is_blog_page() ) {
			return;
		}

		$layout = $this->get_resolved_layout();

		// Only simple layouts use posts_per_page override.
		if ( ! in_array( $layout, array( 'grid', 'list' ), true ) ) {
			return;
		}

		$per_page = (int) get_option( 'wbcom_essential_blog_posts_per_page', 0 );

		if ( $per_page > 0 ) {
			$query->set( 'posts_per_page', $per_page );
		}
	}

	/**
	 * Render a block programmatically using render_block().
	 *
	 * @param string $block_name Block name (e.g., 'wbcom-essential/posts-revolution').
	 * @param array  $attrs      Block attributes.
	 * @return string Rendered HTML.
	 */
	public static function render_block( $block_name, $attrs = array() ) {
		return render_block(
			array(
				'blockName'    => $block_name,
				'attrs'        => $attrs,
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);
	}

	/**
	 * Get resolved sections for composite layouts.
	 *
	 * @param string $layout Layout slug.
	 * @return array Resolved sections.
	 */
	public static function get_sections( $layout ) {
		$sections = get_option( 'wbcom_essential_blog_sections', array() );

		// Fall back to defaults if nothing saved.
		if ( empty( $sections ) ) {
			$sections = Wbcom_Blog_Index_Settings::get_defaults( $layout );
		}

		// Still empty? Build minimal defaults.
		if ( empty( $sections ) ) {
			$sections = array(
				array(
					'title'        => '',
					'category'     => '',
					'display_type' => 'posts_type3',
					'posts_count'  => 9,
				),
			);
		}

		return Wbcom_Blog_Index_Settings::resolve_section_categories( $sections );
	}
}
