<?php
/**
 * Blog Masonry Pattern.
 *
 * A 3-column masonry grid layout with category navigation.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_block_pattern(
	'wbcom-essential/blog-masonry',
	array(
		'title'       => __( 'Blog Masonry', 'wbcom-essential' ),
		'description' => __( 'A masonry-style 3-column blog grid with category navigation.', 'wbcom-essential' ),
		'categories'  => array( 'wbcom-essential-magazine' ),
		'keywords'    => array( 'blog', 'masonry', 'grid', 'categories' ),
		'content'     => '<!-- wp:wbcom-essential/category-grid {"columns":6,"maxCategories":6,"showPostCount":true,"showImage":false,"useThemeColors":true} /-->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/post-carousel {"displayType":"posts_type3","postsPerPage":9,"columns":3,"showExcerpt":true,"excerptLength":80,"enablePagination":true,"paginationType":"numeric","useThemeColors":true} /-->',
	)
);
