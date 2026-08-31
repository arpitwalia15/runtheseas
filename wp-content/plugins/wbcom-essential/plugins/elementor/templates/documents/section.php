<?php
/**
 * Elementor documents section.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates/documents
 */

namespace WBcomEssentialelementor\Templates\Documents;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
/**
 * Elementor documents section.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates/documents
 */
class WBcom_Essential_elementor_Sections_Document extends WBcom_Essential_elementor_Document_Base {

	/**
	 * Get Elementor Section name.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_name() {
		return 'wbcom_essential_elementor_sections_page';
	}

	/**
	 * Get Elementor Section title.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public static function get_title() {
		return __( 'Section', 'wbcom-essential' );
	}

}
