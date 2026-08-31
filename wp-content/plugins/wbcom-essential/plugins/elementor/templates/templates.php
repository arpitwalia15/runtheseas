<?php
/**
 * Sets up and initializes the plugin.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates
 */

namespace WBcomEssentialelementor\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If class `WBcom_Essential_elementor_Templates` not created.
if ( ! class_exists( 'WBcom_Essential_elementor_Templates' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class WBcom_Essential_elementor_Templates {

		/**
		 * Instance of the class
		 *
		 * @access private
		 * @since  1.4.7
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Holds API data
		 *
		 * @access public
		 * @since  1.4.7
		 * @var $api
		 */
		public $api;

		/**
		 * Holds templates configuration data
		 *
		 * @access public
		 * @since  1.4.7
		 * @var $config
		 */
		public $config;

		/**
		 * Holds templates assets
		 *
		 * @access public
		 * @since  1.4.7
		 * @var $assets
		 */
		public $assets;

		/**
		 * Templates Manager
		 *
		 * @access public
		 * @since  1.4.7
		 * @var $temp_manager
		 */
		public $temp_manager;

		/**
		 * Holds templates types
		 *
		 * @access public
		 * @since  1.4.7
		 * @var $types
		 */
		public $types;

		/**
		 * Construct
		 *
		 * Class Constructor
		 *
		 * @since  1.4.7
		 * @access public
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'init' ) );

		}

		/**
		 * Init BB Elementor Sections Templates
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @return void
		 */
		public function init() {

			$this->load_files();

			$this->set_config();

			$this->set_assets();

			$this->set_api();

			$this->set_types();

			$this->set_templates_manager();

		}

		/**
		 * Load required files for BB Elementor Sections templates
		 *
		 * @since  1.4.7
		 * @access private
		 *
		 * @return void
		 */
		private function load_files() {
			require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/classes/config.php';

			require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/classes/assets.php';

			require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/classes/manager.php';

			require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/types/manager.php';

			require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/classes/api.php';

		}

		/**
		 * Init `WBcom_Essential_elementor_Templates_Core_Config`
		 *
		 * @since  1.4.7
		 * @access private
		 *
		 * @return void
		 */
		private function set_config() {

			$this->config = new Classes\WBcom_Essential_elementor_Templates_Core_Config();

		}

		/**
		 * Init `WBcom_Essential_elementor_Templates_Assets`
		 *
		 * @since  1.4.7
		 * @access private
		 *
		 * @return void
		 */
		private function set_assets() {

			$this->assets = new Classes\WBcom_Essential_elementor_Templates_Assets();

		}

		/**
		 * Init `WBcom_Essential_elementor_Templates_API`
		 *
		 * @since  1.4.7
		 * @access private
		 *
		 * @return void
		 */
		private function set_api() {

			$this->api = new Classes\WBcom_Essential_elementor_Templates_API();

		}

		/**
		 * Init `WBcom_Essential_elementor_Templates_Types`
		 *
		 * @since  1.4.7
		 * @access private
		 *
		 * @return void
		 */
		private function set_types() {

			$this->types = new Types\WBcom_Essential_elementor_Templates_Types();

		}

		/**
		 * Init `WBcom_Essential_elementor_Templates_Manager`
		 *
		 * @since  1.4.7
		 * @access private
		 *
		 * @return void
		 */
		private function set_templates_manager() {

			$this->temp_manager = new Classes\WBcom_Essential_elementor_Templates_Manager();

		}

		/**
		 * Get instance.
		 *
		 * Creates and returns an instance of the class.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @return object
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

	}

}

if ( ! function_exists( 'wbcom_essential_elementor_templates' ) ) {

	/**
	 * Triggers `get_instance` method
	 *
	 * @since  1.4.7
	 * @access public
	 * @return object
	 */
	function wbcom_essential_elementor_templates() {

		return WBcom_Essential_elementor_Templates::get_instance();

	}
}
wbcom_essential_elementor_templates();
