<?php
/**
 * Template Search Item.
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
<label for="elementor-template-library-filter-text" class="elementor-screen-only"><?php echo esc_html__( 'Search Templates:', 'wbcom-essential' ); ?></label>
<input id="elementor-template-library-filter-text" placeholder="<?php echo esc_attr__( 'Search', 'wbcom-essential' ); ?>">
<i class="eicon-search"></i>
