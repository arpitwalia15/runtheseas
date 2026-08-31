<?php
/**
 * Elementor skin classic.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/helper/skins
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Helper\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor skin classic.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/helper/skins
 */
class Skin_Classic extends Skin_Base {

	/**
	 * Get ID.
	 */
	public function get_id() {
		return 'wbcom-classic';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return __( 'Classic', 'wbcom-essential' );
	}

	/**
	 * Render AMP..
	 */
	public function render_amp() {

	}
}
