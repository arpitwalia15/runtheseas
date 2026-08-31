<?php
/**
 * Template: Newspaper
 *
 * Breaking news style layout combining:
 * - Posts ticker (breaking news bar)
 * - Post slider (hero)
 * - Configurable content sections in various arrangements
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

$wbcom_sections    = Wbcom_Blog_Index_Templates::get_sections( 'newspaper' );
$wbcom_show_ticker = get_option( 'wbcom_essential_blog_show_ticker', true );
$wbcom_show_slider = get_option( 'wbcom_essential_blog_show_slider', true );
$wbcom_total       = count( $wbcom_sections );
?>

<div class="wbcom-blog wbcom-blog--newspaper">
	<div class="wbcom-blog__container">

		<?php if ( $wbcom_show_ticker ) : ?>
			<div class="wbcom-blog__ticker">
				<?php
				echo Wbcom_Blog_Index_Templates::render_block(
					'wbcom-essential/posts-ticker',
					array(
						'postsToShow'   => 10,
						'tickerLabel'   => __( 'Breaking News', 'wbcom-essential' ),
						'showLabel'     => true,
						'useThemeColors' => true,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php if ( $wbcom_show_slider ) : ?>
			<div class="wbcom-blog__hero">
				<?php
				echo Wbcom_Blog_Index_Templates::render_block(
					'wbcom-essential/post-slider',
					array(
						'postsPerPage'   => 4,
						'showExcerpt'    => true,
						'showDate'       => true,
						'useThemeColors' => true,
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php
		// Render first two sections side by side.
		if ( count( $wbcom_sections ) >= 2 ) :
			$wbcom_sec1     = $wbcom_sections[0];
			$wbcom_sec2     = $wbcom_sections[1];
			$wbcom_cat_arr1 = ! empty( $wbcom_sec1['category'] ) ? array( $wbcom_sec1['category'] ) : array();
			$wbcom_cat_arr2 = ! empty( $wbcom_sec2['category'] ) ? array( $wbcom_sec2['category'] ) : array();

			$wbcom_attrs1 = array(
				'displayType'    => $wbcom_sec1['display_type'],
				'postsPerPage'   => $wbcom_sec1['posts_count'],
				'showExcerpt'    => true,
				'excerptLength'  => 80,
				'useThemeColors' => true,
				'sectionLabel'   => $wbcom_sec1['title'],
			);
			if ( ! empty( $wbcom_cat_arr1 ) ) {
				$wbcom_attrs1['categories'] = $wbcom_cat_arr1;
			}
			if ( 'posts_type3' === $wbcom_sec1['display_type'] ) {
				$wbcom_attrs1['columns'] = 2;
			}

			$wbcom_attrs2 = array(
				'displayType'    => $wbcom_sec2['display_type'],
				'postsPerPage'   => $wbcom_sec2['posts_count'],
				'showExcerpt'    => false,
				'useThemeColors' => true,
				'sectionLabel'   => $wbcom_sec2['title'],
			);
			if ( ! empty( $wbcom_cat_arr2 ) ) {
				$wbcom_attrs2['categories'] = $wbcom_cat_arr2;
			}
			?>
			<div class="wbcom-blog__dual-section">
				<div class="wbcom-blog__dual-main">
					<?php echo Wbcom_Blog_Index_Templates::render_block( 'wbcom-essential/posts-revolution', $wbcom_attrs1 ); ?>
				</div>
				<div class="wbcom-blog__dual-sidebar">
					<?php echo Wbcom_Blog_Index_Templates::render_block( 'wbcom-essential/posts-revolution', $wbcom_attrs2 ); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php
		// Render remaining sections full-width.
		for ( $wbcom_i = 2; $wbcom_i < $wbcom_total; $wbcom_i++ ) :
			$wbcom_section  = $wbcom_sections[ $wbcom_i ];
			$wbcom_is_last  = ( $wbcom_i === $wbcom_total - 1 );
			$wbcom_cat_arr  = ! empty( $wbcom_section['category'] ) ? array( $wbcom_section['category'] ) : array();

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

			if ( 'posts_type3' === $wbcom_section['display_type'] ) {
				$wbcom_attrs['columns'] = 3;
			}
			?>
			<div class="wbcom-blog__section">
				<?php echo Wbcom_Blog_Index_Templates::render_block( 'wbcom-essential/posts-revolution', $wbcom_attrs ); ?>
			</div>
		<?php endfor; ?>

	</div>
</div>

<?php
get_footer();
