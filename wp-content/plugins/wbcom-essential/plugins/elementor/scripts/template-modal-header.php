<?php
/**
 * Template Library Header.
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
<div id="wbcom-essential-template-modal-header-title">
	<span class="wbcom-essential-template-modal-header-title__logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/inc/plugins/elementor/assets/editor/templates/img/wbcom-icon.jpg' ); ?>" alt="" /></span>
	<?php echo esc_html__( 'WBCom Essential Templates', 'wbcom-essential' ); ?>
</div>
<div id="wbcom-essential-template-modal-header-tabs"></div>
<div id="wbcom-essential-template-modal-header-actions">
	<div id="wbcom-essential-template-modal-header-close-modal" class="elementor-template-library-header-item"
		title="<?php echo esc_html__( 'Close', 'wbcom-essential' ); ?>">
		<i class="eicon-close" title="Close"></i>
	</div>
</div>
