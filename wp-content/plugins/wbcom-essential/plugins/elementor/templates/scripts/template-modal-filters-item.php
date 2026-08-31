<?php
/**
 * Templates Library Filter Item.
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
<label class="wbcomessentialelementor-template-filter-label">
	<input type="radio" value="{{ slug }}" <# if ( '' === slug ) { #> checked<# } #> name="wbcomessentialelementor-template-filter">
	<span>{{ title.replace('&amp;', '&') }}</span>
</label>
