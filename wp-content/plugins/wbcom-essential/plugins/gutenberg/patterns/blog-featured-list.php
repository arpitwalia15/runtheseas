<?php
/**
 * Blog Featured List Pattern.
 *
 * Hero + sidebar featured layout with side-by-side post list below.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/gutenberg/patterns
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_block_pattern(
	'wbcom-essential/blog-featured-list',
	array(
		'title'       => __( 'Blog Featured List', 'wbcom-essential' ),
		'description' => __( 'A featured hero post with sidebar grid, followed by a side-by-side post list.', 'wbcom-essential' ),
		'categories'  => array( 'wbcom-essential-magazine' ),
		'keywords'    => array( 'blog', 'featured', 'list', 'hero' ),
		'content'     => '<!-- wp:wbcom-essential/post-carousel {"displayType":"posts_type1","postsPerPage":5,"columns":2,"showExcerpt":true,"excerptLength":120,"useThemeColors":true,"sectionLabel":"Featured Posts"} /-->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:separator {"className":"is-style-wide"} --><hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/><!-- /wp:separator -->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"left"} --><h2 class="wp-block-heading has-text-align-left">Latest Articles</h2><!-- /wp:heading -->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wbcom-essential/post-carousel {"displayType":"posts_type4","postsPerPage":6,"columns":2,"showExcerpt":true,"excerptLength":100,"enablePagination":true,"paginationType":"numeric","useThemeColors":true,"sectionLabel":"Latest Articles \u2014 Select Categories to Filter"} /-->',
	)
);
