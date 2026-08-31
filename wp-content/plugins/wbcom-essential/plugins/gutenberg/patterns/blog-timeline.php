<?php
/**
 * Blog Timeline Pattern.
 *
 * A chronological two-column timeline layout for blog posts.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_block_pattern(
	'wbcom-essential/blog-timeline',
	array(
		'title'       => __( 'Blog Timeline', 'wbcom-essential' ),
		'description' => __( 'A chronological two-column timeline layout for blog posts.', 'wbcom-essential' ),
		'categories'  => array( 'wbcom-essential-magazine' ),
		'keywords'    => array( 'blog', 'timeline', 'chronological', 'history' ),
		'content'     => '<!-- wp:heading {"textAlign":"center"} --><h2 class="wp-block-heading has-text-align-center">Our Story</h2><!-- /wp:heading -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/timeline {"postsPerPage":10,"showExcerpt":true,"excerptLength":120,"useThemeColors":true} /-->',
	)
);
