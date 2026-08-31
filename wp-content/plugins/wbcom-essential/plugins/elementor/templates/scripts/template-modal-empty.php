<?php
/**
 * Templates Loader empty.
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
<div class="elementor-template-library-blank-icon">
	<img src="<?php echo esc_url( ELEMENTOR_ASSETS_URL . 'images/no-search-results.svg' ); ?>" class="elementor-template-library-no-results" />
</div>
<div class="elementor-template-library-blank-title"><?php esc_html__( 'No Templates Found', 'wbcom-essential' ); ?></div>
