<?php
/**
 * BB Elementor Sections Templates Manager.
 *
 * Templates manager class handles all templates library insertion.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates/classes
 */

namespace WBcomEssentialelementor\Templates\Classes;

use WBcomEssentialelementor\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WBcom_Essential_elementor_Templates_Manager' ) ) {

	/**
	 * BB Elementor Sections Templates Manager.
	 *
	 * Templates manager class handles all templates library insertion.
	 *
	 * @since 1.4.7
	 */
	class WBcom_Essential_elementor_Templates_Manager {

		/**
		 * Instance of the class.
		 *
		 * @access private
		 * @since  1.4.7
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Sources.
		 *
		 * @access private
		 * @since  1.4.7
		 * @var array
		 */
		private $sources = array();

		/**
		 * WBcom_Essential_elementor_Templates_Manager constructor.
		 *
		 * Initialize required hooks for templates.
		 *
		 * @since  1.4.7
		 * @access public
		 */
		public function __construct() {

			// Register AJAX hooks.
			add_action( 'wp_ajax_wbcom_essential_elementor_sections_get_templates', array( $this, 'get_templates' ) );
			add_action( 'wp_ajax_wbcom_essential_elementor_sections_inner_template', array( $this, 'insert_inner_template' ) );

			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.2.8', '>' ) ) {
				add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ), 20 );
			} else {
				add_action( 'wp_ajax_elementor_get_template_data', array( $this, 'get_template_data' ), - 1 );
			}

			$this->register_sources();

			add_filter(
				'wbcom-essential-elementor-sections-templates-core/assets/editor/localize',
				array(
					$this,
					'localize_tabs',
				)
			);
		}

		/**
		 * Localize tabs.
		 *
		 * Add tabs data to localize object
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @param array $data Tabs Data.
		 *
		 * @return array $data
		 */
		public function localize_tabs( $data ) {

			$tabs    = $this->get_template_tabs();
			$ids     = array_keys( $tabs );
			$default = $ids[0];

			$data['tabs']       = $this->get_template_tabs();
			$data['defaultTab'] = $default;

			return $data;
		}

		/**
		 * Register sources.
		 *
		 * Register templates sources.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @return void
		 */
		public function register_sources() {

			require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/sources/base.php';

			$namespace = str_replace( 'Classes', 'Sources', __NAMESPACE__ );

			$sources = array(
				'wbcom-essential-elementor-sections-api' => $namespace . '\WBcom_Essential_elementor_Templates_Source_Api',
				'wbcom-essential-elementor-pages-api'    => $namespace . '\WBcom_Essential_elementor_Templates_Page_Api',
			);

			foreach ( $sources as $key => $class ) {

				require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/sources/' . $key . '.php';

				$this->add_source( $key, $class );
			}
		}

		/**
		 * Get template tabs.
		 *
		 * Get tabs for the library.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @return array $tabs
		 */
		public function get_template_tabs() {

			return Templates\wbcom_essential_elementor_templates()->types->get_types_for_popup();
		}

		/**
		 * Get template tabs.
		 *
		 * Get tabs for the library.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @param string $key  Source key.
		 * @param string $class Source class.
		 */
		public function add_source( $key, $class ) {
			$this->sources[ $key ] = new $class();
		}

		/**
		 * Returns needed source instance.
		 *
		 * @param string $slug Source slug.
		 *
		 * @return object|bool
		 */
		public function get_source( $slug = null ) {
			return isset( $this->sources[ $slug ] ) ? $this->sources[ $slug ] : false;
		}


		/**
		 * Get template.
		 *
		 * Get templates grid data.
		 *
		 * @since  1.4.7
		 * @access public
		 */
		public function get_templates() {

			if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'template-nonce' ) ) {
				return false;
			}

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Tab is validated against available tabs below via array key check.
			$tab     = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			$tabs    = $this->get_template_tabs();
			$sources = isset( $tabs[ $tab ] ) ? $tabs[ $tab ]['sources'] : array();

			$result = array(
				'templates'  => array(),
				'categories' => array(),
			);

			foreach ( $sources as $source_slug ) {

				$source = isset( $this->sources[ $source_slug ] ) ? $this->sources[ $source_slug ] : false;

				if ( $source ) {
					$result['templates']  = array_merge( $result['templates'], $source->get_items( $tab ) );
					$result['categories'] = array_merge( $result['categories'], $source->get_categories( $tab ) );
				}
			}

			$all_cats = array(
				array(
					'slug'  => '',
					'title' => __( 'All', 'wbcom-essential' ),
				),
			);

			if ( ! empty( $result['categories'] ) ) {
				$result['categories'] = array_merge( $all_cats, $result['categories'] );
			}

			wp_send_json_success( $result );
		}

		/**
		 * Insert inner template.
		 *
		 * Insert an inner template before insert the parent one.
		 *
		 * @since  1.4.7
		 * @access public
		 */
		public function insert_inner_template() {

			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'template-nonce' ) ) {
				return false;
			}

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			$template = ( isset( $_REQUEST['template'] ) && is_array( $_REQUEST['template'] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_REQUEST['template'] ) ) : false;

			if ( ! $template ) {
				wp_send_json_error();
			}

			$template_id = ( isset( $template['template_id'] ) && is_string( $template['template_id'] ) ) ? sanitize_text_field( $template['template_id'] ) : false;
			$source_name = ( isset( $template['source'] ) && is_string( $template['source'] ) ) ? sanitize_text_field( $template['source'] ) : false;
			$source      = ( $source_name && isset( $this->sources[ $source_name ] ) ) ? $this->sources[ $source_name ] : false;

			if ( ! $source || ! $template_id ) {
				wp_send_json_error();
			}

			$template_data = $source->get_item( $template_id );

			$content = '';
			if ( ! empty( $template_data['content'] ) && $source_name && 'wbcom-essential-elementor-sections-api' === $source_name ) {
				$content = array( $template_data['content'] );
			} elseif ( ! empty( $template_data['content'] ) && $source_name && 'wbcom-essential-elementor-pages-api' === $source_name ) {
				$content = $template_data['content'];
			}

			if ( ! empty( $template_data['content'] ) ) {
				wp_insert_post(
					array(
						'post_type'   => 'elementor_library',
						'post_title'  => $template['title'],
						'post_status' => 'publish',
						'meta_input'  => array(
							'_elementor_data'          => $content,
							'_elementor_edit_mode'     => 'builder',
							'_elementor_template_type' => 'section',
						),
					)
				);
			}

			wp_send_json_success();
		}

		/**
		 * Register AJAX actions.
		 *
		 * Add new actions to handle data after an AJAX requests returned.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @param object $ajax_manager Ajax actions.
		 */
		public function register_ajax_actions( $ajax_manager ) {
			// phpcs:disable WordPress.Security.NonceVerification -- Nonce verified by Elementor's Ajax handler before this hook fires.
			if ( ! isset( $_POST['actions'] ) ) {
				return;
			}

			// Verify user capabilities.
			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- JSON decoded and validated below.
			$actions = json_decode( wp_unslash( $_REQUEST['actions'] ), true );

			if ( ! is_array( $actions ) ) {
				return;
			}

			$data = false;

			foreach ( $actions as $id => $action_data ) {
				if ( ! is_array( $action_data ) ) {
					continue;
				}
				if ( ! isset( $action_data['get_template_data'] ) ) {
					$data = $action_data;
				}
			}

			if ( ! $data ) {
				return;
			}

			if ( ! isset( $data['data'] ) || ! is_array( $data['data'] ) ) {
				return;
			}

			if ( ! isset( $data['data']['source'] ) || ! is_string( $data['data']['source'] ) ) {
				return;
			}

			$source = $data['data']['source'];

			if ( ! is_string( $source ) || '' === $source ) {
				return;
			}

			if ( ! isset( $this->sources[ $source ] ) ) {
				return;
			}

			$ajax_manager->register_ajax_action(
				'get_template_data',
				function ( $data ) {
					return $this->get_template_data_array( $data );
				}
			);
			// phpcs:enable WordPress.Security.NonceVerification
		}

		/**
		 * Get template data array.
		 *
		 * Triggered to get an array for a single template data.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @param array $data Template Data.
		 *
		 * @return bool|array
		 */
		public function get_template_data_array( $data ) {

			if ( ! current_user_can( 'edit_posts' ) ) {
				return false;
			}

			if ( empty( $data['template_id'] ) ) {
				return false;
			}

			$source_name = ( isset( $data['source'] ) && is_string( $data['source'] ) ) ? sanitize_text_field( $data['source'] ) : '';

			if ( ! $source_name ) {
				return false;
			}

			$source = isset( $this->sources[ $source_name ] ) ? $this->sources[ $source_name ] : false;

			if ( ! $source ) {
				return false;
			}

			if ( empty( $data['tab'] ) ) {
				return false;
			}

			return $source->get_item( $data['template_id'], $data['tab'] );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.4.7
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

}
