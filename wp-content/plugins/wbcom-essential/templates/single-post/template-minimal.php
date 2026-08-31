<?php
/**
 * Template: Minimal
 *
 * Narrow centered layout (740px), no sidebar, clean typography.
 * Featured image with caption, minimal meta with reading time.
 * Includes author bio and related posts for engagement.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/single-post
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$wbcom_partials = WBCOM_ESSENTIAL_PATH . 'templates/single-post/partials/';
?>

<div class="wbcom-sp wbcom-sp--minimal">
	<div class="wbcom-sp__container wbcom-sp__container--narrow">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<main class="wbcom-sp__main" id="wbcom-sp-content">
				<article <?php post_class( 'wbcom-sp__article' ); ?>>
					<header class="wbcom-sp__header">
						<?php the_title( '<h1 class="wbcom-sp__title">', '</h1>' ); ?>
						<div class="wbcom-sp__meta">
							<span class="wbcom-sp__meta-author">
								<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
									<?php the_author(); ?>
								</a>
							</span>
							<time class="wbcom-sp__meta-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</time>
							<span class="wbcom-sp__meta-reading">
								<?php
								$wbcom_word_count = str_word_count( wp_strip_all_tags( get_the_content() ) );
								$wbcom_read_time  = max( 1, (int) ceil( $wbcom_word_count / 250 ) );
								printf(
									/* translators: %d: number of minutes */
									esc_html( _n( '%d min read', '%d min read', $wbcom_read_time, 'wbcom-essential' ) ),
									(int) $wbcom_read_time
								);
								?>
							</span>
						</div>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="wbcom-sp__featured-image">
							<?php the_post_thumbnail( 'large' ); ?>
							<?php
							$wbcom_caption = get_the_post_thumbnail_caption();
							if ( $wbcom_caption ) :
								?>
								<figcaption class="wbcom-sp__featured-caption"><?php echo esc_html( $wbcom_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<div class="wbcom-sp__content entry-content">
						<?php the_content(); ?>
						<?php
						wp_link_pages(
							array(
								'before' => '<div class="wbcom-sp__page-links">',
								'after'  => '</div>',
							)
						);
						?>
					</div>

					<footer class="wbcom-sp__footer">
						<?php
						$wbcom_tags_list = get_the_tag_list( '', ', ' );
						if ( $wbcom_tags_list ) :
							?>
							<div class="wbcom-sp__tags">
								<?php echo wp_kses_post( $wbcom_tags_list ); ?>
							</div>
						<?php endif; ?>
					</footer>
				</article>

				<?php include $wbcom_partials . 'author-bio.php'; ?>
				<?php include $wbcom_partials . 'related-posts.php'; ?>
				<?php include $wbcom_partials . 'post-navigation.php'; ?>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</main>
		<?php endwhile; ?>
	</div>
</div>

<?php
get_footer();
