<?php
/**
 * Category Archive Pattern.
 *
 * Category page layout: heading + post grid with sidebar.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_block_pattern(
	'wbcom-essential/category-archive',
	array(
		'title'       => __( 'Category Archive', 'wbcom-essential' ),
		'description' => __( 'A category archive page with a post grid and sidebar featuring latest posts.', 'wbcom-essential' ),
		'categories'  => array( 'wbcom-essential-magazine' ),
		'keywords'    => array( 'category', 'archive', 'sidebar', 'blog' ),
		'content'     => '<!-- wp:heading {"textAlign":"left"} --><h2 class="wp-block-heading has-text-align-left">Category</h2><!-- /wp:heading -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"32px"}}}} -->
<div class="wp-block-columns">

<!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%">
<!-- wp:wbcom-essential/post-carousel {"displayType":"posts_type3","postsPerPage":6,"columns":2,"enablePagination":true,"useThemeColors":true} /-->
</div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%">

<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Recent Posts</h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"12px"} -->
<div style="height:12px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:latest-posts {"postsToShow":5,"displayPostDate":true} /-->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Categories</h3>
<!-- /wp:heading -->

<!-- wp:spacer {"height":"12px"} -->
<div style="height:12px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:categories {"showPostCounts":true} /-->

</div>
<!-- /wp:column -->

</div>
<!-- /wp:columns -->',
	)
);
