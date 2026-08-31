<?php
/**
 * Templates Library Header.
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
<div id="wbcomessentialelementor-template-modal-header-title">
	<span class="wbcomessentialelementor-template-modal-header-title__logo"><img src="<?php echo esc_url( WBCOM_ESSENTIAL_URL . '/plugins/elementor/assets/editor/templates/img/wbcom-icon.jpg' ); ?>" alt="" /></span>
	<?php echo esc_html__( 'WBCom Essential Templates', 'wbcom-essential' ); ?>
</div>
<div id="wbcomessentialelementor-template-modal-header-tabs"></div>
<div id="wbcomessentialelementor-template-modal-header-actions">
	<div id="wbcomessentialelementor-template-modal-header-close-modal" class="elementor-template-library-header-item"
		title="<?php echo esc_html__( 'Close', 'wbcom-essential' ); ?>">
		<i class="eicon-close" title="Close"></i>
	</div>
</div>
