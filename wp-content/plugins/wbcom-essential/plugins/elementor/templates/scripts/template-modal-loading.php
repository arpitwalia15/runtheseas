<?php
/**
 * Templates Loader View.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/templates/scripts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="elementor-loader-wrapper">
	<div class="elementor-loader">
		<div class="elementor-loader-box"></div>
		<div class="elementor-loader-box"></div>
		<div class="elementor-loader-box"></div>
		<div class="elementor-loader-box"></div>
	</div>
	<div class="elementor-loading-title"><?php echo esc_html__( 'Loading', 'wbcom-essential' ); ?></div>
</div>
