<?php
/**
 * Template: Grid
 *
 * Simple card grid using posts-revolution type3 (columns) with pagination.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/blog-index
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$wbcom_per_page = (int) get_option( 'wbcom_essential_blog_posts_per_page', 0 );
if ( $wbcom_per_page < 1 ) {
	$wbcom_per_page = (int) get_option( 'posts_per_page', 10 );
}
?>

<div class="wbcom-blog wbcom-blog--grid">
	<div class="wbcom-blog__container">
		<?php
		echo Wbcom_Blog_Index_Templates::render_block(
			'wbcom-essential/posts-revolution',
			array(
				'displayType'      => 'posts_type3',
				'columns'          => 3,
				'postsPerPage'     => $wbcom_per_page,
				'showExcerpt'      => true,
				'excerptLength'    => 120,
				'enablePagination' => true,
				'paginationType'   => 'numeric',
				'useThemeColors'   => true,
			)
		);
		?>
	</div>
</div>

<?php
get_footer();
