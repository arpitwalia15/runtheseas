<?php
/**
 * Single Post Author Bio Pattern.
 *
 * A styled author bio section with divider and testimonial block.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_block_pattern(
	'wbcom-essential/single-post-author-bio',
	array(
		'title'       => __( 'Author Bio Section', 'wbcom-essential' ),
		'description' => __( 'A polished author bio section with divider and testimonial-style layout.', 'wbcom-essential' ),
		'categories'  => array( 'wbcom-essential-single-post' ),
		'keywords'    => array( 'author', 'bio', 'profile', 'about' ),
		'content'     => '<!-- wp:separator {"className":"is-style-wide"} --><hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/><!-- /wp:separator -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"left","level":3} --><h3 class="wp-block-heading has-text-align-left">About the Author</h3><!-- /wp:heading -->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/testimonial-carousel {"showRating":false,"layout":"row","useThemeColors":true} /-->',
	)
);
