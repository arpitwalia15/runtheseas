<?php
/**
 * Define WBcom_Essential_elementor_Structure_Page class.
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
} // Exit if accessed directly

if ( ! class_exists( 'WBcom_Essential_elementor_Structure_Page' ) ) {

	/**
	 * Define WBcom_Essential_elementor_Structure_Page class
	 */
	class WBcom_Essential_elementor_Structure_Page extends WBcom_Essential_elementor_Structure_Base {

		/**
		 * Get Section id.
		 *
		 * @since  1.4.7
		 * @access public
		 * @return string
		 */
		public function get_id() {
			return 'wbcom_essential_elementor_pages';
		}

		/**
		 * Get Section Label.
		 *
		 * @since  1.4.7
		 * @access public
		 * @return string
		 */
		public function get_single_label() {
			return __( 'Page', 'wbcom-essential' );
		}

		/**
		 * Get Section Plural Label.
		 *
		 * @since  1.4.7
		 * @access public
		 * @return string
		 */
		public function get_plural_label() {
			return __( 'Pages', 'wbcom-essential' );
		}

		/**
		 * Get Sources.
		 *
		 * @since  1.4.7
		 * @access public
		 * @return array
		 */
		public function get_sources() {
			return array( 'wbcom-essential-elementor-pages-api' );
		}

		/**
		 * Get Document Types.
		 *
		 * @since  1.4.7
		 * @access public
		 * @return array
		 */
		public function get_document_type() {
			return array(
				'class' => 'WBcom_Essential_elementor_Pages_Document',
				'file'  => require ELEMENTOR_WBCOMESSENTIAL__DIR__ . '/templates/documents/page.php',
			);
		}

		/**
		 * Library settings for current structure.
		 *
		 * @since  1.4.7
		 * @access public
		 * @return array
		 */
		public function library_settings() {

			return array(
				'show_title' => false,
			);

		}

	}

}
