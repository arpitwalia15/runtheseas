<?php
/**
 * Magazine Homepage Pattern.
 *
 * Full news site homepage: ticker + hero slider + featured grid + category grid.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_block_pattern(
	'wbcom-essential/magazine-homepage',
	array(
		'title'       => __( 'Magazine Homepage', 'wbcom-essential' ),
		'description' => __( 'A full news site homepage with breaking news ticker, hero post slider, featured posts grid, and category grid.', 'wbcom-essential' ),
		'categories'  => array( 'wbcom-essential-magazine' ),
		'keywords'    => array( 'magazine', 'homepage', 'news', 'blog' ),
		'content'     => '<!-- wp:wbcom-essential/posts-ticker {"useThemeColors":true} /-->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/post-carousel {"useThemeColors":true,"postsPerPage":4} /-->

<!-- wp:spacer {"height":"48px"} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"left"} --><h2 class="wp-block-heading has-text-align-left">Latest Stories</h2><!-- /wp:heading -->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/post-carousel {"displayType":"posts_type6","postsPerPage":6,"columns":3,"useThemeColors":true,"sectionLabel":"Latest Stories \u2014 Select Categories to Filter"} /-->

<!-- wp:spacer {"height":"48px"} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"left"} --><h2 class="wp-block-heading has-text-align-left">Browse by Category</h2><!-- /wp:heading -->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/category-grid {"columns":4,"maxCategories":8,"showPostCount":true,"showImage":true,"useThemeColors":true} /-->',
	)
);
