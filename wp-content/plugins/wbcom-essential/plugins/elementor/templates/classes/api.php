<?php
/**
 * WBcom_Essential_elementor_Templates API.
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
} // No access of directly access.

if ( ! class_exists( 'WBcom_Essential_elementor_Templates_API' ) ) {

	/**
	 *  WBcom_Essential_elementor_Templates API.
	 *
	 * It's responsible for getting API data.
	 *
	 * @since 1.4.7
	 */
	class WBcom_Essential_elementor_Templates_API {

		/**
		 * API URL which is used to get the response from.
		 *
		 * @since  1.4.7
		 * @var array
		 */
		private $config = array();

		/**
		 * API enabled.
		 *
		 * @since  1.4.7
		 * @var string
		 */
		private $enabled = null;

		/**
		 * WBcom_Essential_elementor_Templates_API constructor.
		 *
		 * Get all API data.
		 *
		 * @since  1.4.7
		 * @access public
		 */
		public function __construct() {

			$this->config = Templates\wbcom_essential_elementor_templates()->config->get( 'api' );

		}

		/**
		 * Is Enabled.
		 *
		 * Check if remote API is enabled.
		 *
		 * @since  1.4.7
		 * @access public
		 * @return boolean
		 */
		public function is_enabled() {

			if ( null !== $this->enabled ) {
				return $this->enabled;
			}

			if ( empty( $this->config['enabled'] ) || true !== $this->config['enabled'] ) {
				$this->enabled = false;

				return $this->enabled;
			}

			if ( empty( $this->config['base'] ) || empty( $this->config['path'] ) || empty( $this->config['endpoints'] ) ) {
				$this->enabled = false;

				return $this->enabled;
			}

			$this->enabled = true;

			return $this->enabled;
		}

		/**
		 * API URL.
		 *
		 * Get API for template library area data.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @param string $flag Flag.
		 *
		 * @return bool|string
		 */
		public function api_url( $flag ) {

			if ( ! $this->is_enabled() ) {
				return false;
			}

			if ( empty( $this->config['endpoints'][ $flag ] ) ) {
				return false;
			}

			return $this->config['base'] . $this->config['path'] . $this->config['endpoints'][ $flag ];
		}

		/**
		 * Request Args.
		 *
		 * Get request arguments for the remote request.
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @return array
		 */
		public function request_args() {
			return array(
				'timeout'   => 60,
				'sslverify' => false,
			);
		}

	}

}
