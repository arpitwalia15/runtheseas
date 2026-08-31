<?php
/**
 * Template: Magazine
 *
 * Full-width hero with featured image background, gradient overlay,
 * title/meta overlay. No sidebar, 740px content width.
 * Inline share section below content for social sharing.
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

<div class="wbcom-sp wbcom-sp--magazine">
	<?php
	while ( have_posts() ) :
		the_post();

		$wbcom_thumb_url  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		$wbcom_word_count = str_word_count( wp_strip_all_tags( get_the_content() ) );
		$wbcom_read_time  = max( 1, (int) ceil( $wbcom_word_count / 250 ) );
		?>

		<?php if ( $wbcom_thumb_url ) : ?>
			<div class="wbcom-sp__hero" style="background-image: url('<?php echo esc_url( $wbcom_thumb_url ); ?>');">
				<div class="wbcom-sp__hero-overlay">
					<div class="wbcom-sp__hero-content">
						<?php
						$wbcom_categories_list = get_the_category_list( ', ' );
						if ( $wbcom_categories_list ) :
							?>
							<div class="wbcom-sp__hero-cats"><?php echo wp_kses_post( $wbcom_categories_list ); ?></div>
						<?php endif; ?>

						<?php the_title( '<h1 class="wbcom-sp__title">', '</h1>' ); ?>

						<div class="wbcom-sp__meta">
							<span class="wbcom-sp__meta-author">
								<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
								<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
									<?php the_author(); ?>
								</a>
							</span>
							<time class="wbcom-sp__meta-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</time>
							<span class="wbcom-sp__meta-reading">
								<?php
								printf(
									/* translators: %d: number of minutes */
									esc_html( _n( '%d min read', '%d min read', $wbcom_read_time, 'wbcom-essential' ) ),
									(int) $wbcom_read_time
								);
								?>
							</span>
						</div>
					</div>
				</div>
			</div>
		<?php else : ?>
			<header class="wbcom-sp__header wbcom-sp__header--no-hero">
				<div class="wbcom-sp__container wbcom-sp__container--narrow">
					<?php
					$wbcom_categories_list = get_the_category_list( ', ' );
					if ( $wbcom_categories_list ) :
						?>
						<div class="wbcom-sp__hero-cats"><?php echo wp_kses_post( $wbcom_categories_list ); ?></div>
					<?php endif; ?>

					<?php the_title( '<h1 class="wbcom-sp__title">', '</h1>' ); ?>

					<div class="wbcom-sp__meta">
						<span class="wbcom-sp__meta-author">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
								<?php the_author(); ?>
							</a>
						</span>
						<time class="wbcom-sp__meta-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>
						<span class="wbcom-sp__meta-reading">
							<?php
							printf(
								/* translators: %d: number of minutes */
								esc_html( _n( '%d min read', '%d min read', $wbcom_read_time, 'wbcom-essential' ) ),
								(int) $wbcom_read_time
							);
							?>
						</span>
					</div>
				</div>
			</header>
		<?php endif; ?>

		<div class="wbcom-sp__container wbcom-sp__container--narrow">
			<main class="wbcom-sp__main" id="wbcom-sp-content">
				<article <?php post_class( 'wbcom-sp__article' ); ?>>
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
								<span class="wbcom-sp__tags-label"><?php esc_html_e( 'Tags:', 'wbcom-essential' ); ?></span>
								<?php echo wp_kses_post( $wbcom_tags_list ); ?>
							</div>
						<?php endif; ?>
					</footer>
				</article>

				<!-- Inline share section -->
				<div class="wbcom-sp__share-inline">
					<span class="wbcom-sp__share-inline-label"><?php esc_html_e( 'Share this article', 'wbcom-essential' ); ?></span>
					<?php include $wbcom_partials . 'share-buttons.php'; ?>
				</div>

				<?php include $wbcom_partials . 'author-bio.php'; ?>
				<?php include $wbcom_partials . 'related-posts.php'; ?>
				<?php include $wbcom_partials . 'post-navigation.php'; ?>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</main>
		</div>
	<?php endwhile; ?>
</div>

<?php
get_footer();
