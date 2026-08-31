<?php
/**
 * Elementor documents base.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates/documents
 */

namespace WBcomEssentialelementor\Templates\Documents;

use Elementor\Core\Base\Document as Document;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Elementor documents base.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates/documents
 */
class WBcom_Essential_elementor_Document_Base extends Document {

	/**
	 * Get name.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_name() {
		return '';
	}

	/**
	 * Get title.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public static function get_title() {
		return '';
	}

}
