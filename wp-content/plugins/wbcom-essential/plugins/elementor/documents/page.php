<?php
/**
 * Elementor document page document.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/document
 */

namespace WBcomEssentialelementor\Templates\Documents;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.
/**
 * Elementor document page document.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/document
 */
class WBcom_Essential_elementor_Pages_Document extends WBcom_Essential_elementor_Document_Base {

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
		return __( 'Page', 'wbcom-essential' );
	}

}
