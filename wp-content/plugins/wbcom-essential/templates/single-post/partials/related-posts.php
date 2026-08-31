<?php
/**
 * Partial: Related Posts
 *
 * Displays 3 posts from the same categories as a card grid.
 * Only shows posts that have a featured image.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/single-post/partials
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wbcom_categories = wp_get_post_categories( get_the_ID() );

if ( empty( $wbcom_categories ) ) {
	return;
}

$wbcom_related = new WP_Query(
	array(
		'category__in'        => $wbcom_categories,
		'post__not_in'        => array( get_the_ID() ),
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			),
		),
	)
);

if ( ! $wbcom_related->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<div class="wbcom-sp-related">
	<h3 class="wbcom-sp-related__title"><?php esc_html_e( 'Related Posts', 'wbcom-essential' ); ?></h3>
	<div class="wbcom-sp-related__grid">
		<?php
		while ( $wbcom_related->have_posts() ) :
			$wbcom_related->the_post();
			?>
			<article class="wbcom-sp-related__card">
				<a href="<?php the_permalink(); ?>" class="wbcom-sp-related__image">
					<?php the_post_thumbnail( 'medium' ); ?>
				</a>
				<div class="wbcom-sp-related__card-content">
					<h4 class="wbcom-sp-related__card-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h4>
					<time class="wbcom-sp-related__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_date() ); ?>
					</time>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</div>
<?php
wp_reset_postdata();
