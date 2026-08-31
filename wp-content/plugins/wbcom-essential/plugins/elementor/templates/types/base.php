<?php
/**
 * Define WBcom_Essential_elementor_Structure_Base class.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates/types
 */

namespace WBcomEssentialelementor\Templates\Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // No access of directly access.

if ( ! class_exists( 'WBcom_Essential_elementor_Structure_Base' ) ) {

	/**
	 * Define WBcom_Essential_elementor_Structure_Base class
	 */
	abstract class WBcom_Essential_elementor_Structure_Base {

		/**
		 * Get io.
		 *
		 * @abstract
		 * @since  1.4.7
		 * @access public
		 */
		abstract public function get_id();

		/**
		 * Get single label.
		 *
		 * @abstract
		 * @since  1.4.7
		 * @access public
		 */
		abstract public function get_single_label();

		/**
		 * Get plural label.
		 *
		 * @abstract
		 * @since  1.4.7
		 * @access public
		 */
		abstract public function get_plural_label();

		/**
		 * Get Sources.
		 *
		 * @abstract
		 * @since  1.4.7
		 * @access public
		 */
		abstract public function get_sources();

		/**
		 * Get Document.
		 *
		 * @abstract
		 * @since  1.4.7
		 * @access public
		 */
		abstract public function get_document_type();

		/**
		 * Is current structure could be outputed as location
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @return boolean
		 */
		public function is_location() {
			return false;
		}

		/**
		 * Location name
		 *
		 * @since  1.4.7
		 * @access public
		 *
		 * @return boolean
		 */
		public function location_name() {
			return '';
		}

		/**
		 * Library settings for current structure
		 *
		 * @since 1.4.7
		 * @return array
		 */
		public function library_settings() {

			return array(
				'show_title' => true,
			);

		}

	}

}
