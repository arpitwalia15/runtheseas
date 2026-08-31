<?php
/**
 * Template Insert Button.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/scripts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<button class="elementor-template-library-template-action wbcom-essential-template-insert elementor-button elementor-button-success">
	<i class="eicon-file-download"></i><span class="elementor-button-title">
	<?php
		echo esc_html__( 'Insert', 'wbcom-essential' );
	?>
		</span>
</button>
