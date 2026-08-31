<?php
/**
 * Single Post Related Posts Pattern.
 *
 * A related posts carousel section for use after article content.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_block_pattern(
	'wbcom-essential/single-post-related-posts',
	array(
		'title'       => __( 'Related Posts Carousel', 'wbcom-essential' ),
		'description' => __( 'A related posts carousel section with divider and heading.', 'wbcom-essential' ),
		'categories'  => array( 'wbcom-essential-single-post' ),
		'keywords'    => array( 'related', 'posts', 'carousel', 'recommended' ),
		'content'     => '<!-- wp:separator {"className":"is-style-wide"} --><hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/><!-- /wp:separator -->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"left","level":3} --><h3 class="wp-block-heading has-text-align-left">You May Also Like</h3><!-- /wp:heading -->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/post-carousel {"slidesToShow":3,"showExcerpt":false,"showMeta":true,"useThemeColors":true} /-->',
	)
);
