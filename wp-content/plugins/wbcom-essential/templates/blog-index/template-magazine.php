<?php
/**
 * Template: Magazine
 *
 * Multi-section magazine layout combining:
 * - Category grid navigation bar
 * - Configurable content sections (posts-revolution with different display types)
 * - Pagination on the last section
 *
 * Categories are auto-assigned from categories with posts when set to "Auto".
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/blog-index
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$wbcom_sections     = Wbcom_Blog_Index_Templates::get_sections( 'magazine' );
$wbcom_show_cat_nav = get_option( 'wbcom_essential_blog_show_category_nav', true );
$wbcom_total        = count( $wbcom_sections );
?>

<div class="wbcom-blog wbcom-blog--magazine">
	<div class="wbcom-blog__container">

		<?php if ( $wbcom_show_cat_nav ) : ?>
			<div class="wbcom-blog__category-nav">
				<?php
				echo Wbcom_Blog_Index_Templates::render_block(
					'wbcom-essential/category-grid',
					array(
						'columns'       => 6,
						'maxCategories' => 8,
						'showPostCount' => true,
						'showImage'     => false,
						'useThemeColors' => true,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php
		foreach ( $wbcom_sections as $wbcom_index => $wbcom_section ) :
			$wbcom_is_last  = ( $wbcom_index === $wbcom_total - 1 );
			$wbcom_cat_arr  = array();

			if ( ! empty( $wbcom_section['category'] ) ) {
				$wbcom_cat_arr = array( $wbcom_section['category'] );
			}

			$wbcom_attrs = array(
				'displayType'      => $wbcom_section['display_type'],
				'postsPerPage'     => $wbcom_section['posts_count'],
				'showExcerpt'      => true,
				'excerptLength'    => 100,
				'enablePagination' => $wbcom_is_last,
				'paginationType'   => 'numeric',
				'useThemeColors'   => true,
				'sectionLabel'     => $wbcom_section['title'],
			);

			if ( ! empty( $wbcom_cat_arr ) ) {
				$wbcom_attrs['categories'] = $wbcom_cat_arr;
			}

			// Grid type needs columns.
			if ( 'posts_type3' === $wbcom_section['display_type'] ) {
				$wbcom_attrs['columns'] = 3;
			}
			?>
			<div class="wbcom-blog__section">
				<?php echo Wbcom_Blog_Index_Templates::render_block( 'wbcom-essential/posts-revolution', $wbcom_attrs ); ?>
			</div>
		<?php endforeach; ?>

	</div>
</div>

<?php
get_footer();
