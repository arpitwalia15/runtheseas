<?php
/**
 * Template: Modern
 *
 * Full-width hero with title overlay, reading progress bar,
 * sticky ToC sidebar (collapses when no headings), floating share bar on desktop,
 * inline share for smaller screens.
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

// Add anchor IDs to headings for ToC linking.
add_filter(
	'the_content',
	function ( $content ) {
		$content = preg_replace_callback(
			'/<h([23])([^>]*)>(.*?)<\/h([23])>/i',
			function ( $matches ) {
				$text = wp_strip_all_tags( $matches[3] );
				$id   = sanitize_title( $text );
				return '<h' . $matches[1] . $matches[2] . ' id="' . esc_attr( $id ) . '">' . $matches[3] . '</h' . $matches[4] . '>';
			},
			$content
		);
		return $content;
	},
	5
);

// Check if content has H2/H3 headings for ToC.
$wbcom_raw_content = get_the_content();
$wbcom_raw_content = apply_filters( 'the_content', $wbcom_raw_content );
preg_match_all( '/<h([23])[^>]*>(.*?)<\/h[23]>/i', $wbcom_raw_content, $wbcom_heading_check );
$wbcom_has_toc = ! empty( $wbcom_heading_check[0] );
?>

<div class="wbcom-sp wbcom-sp--modern<?php echo ! $wbcom_has_toc ? ' wbcom-sp--no-toc' : ''; ?>">
	<!-- Reading progress bar -->
	<div class="wbcom-sp__progress" id="wbcom-sp-progress">
		<div class="wbcom-sp__progress-bar" id="wbcom-sp-progress-bar"></div>
	</div>

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
				<div class="wbcom-sp__container">
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

		<div class="wbcom-sp__container">
			<div class="wbcom-sp__grid">
				<?php if ( $wbcom_has_toc ) : ?>
					<!-- Sticky ToC sidebar -->
					<aside class="wbcom-sp__toc-sidebar">
						<?php include $wbcom_partials . 'toc.php'; ?>
					</aside>
				<?php endif; ?>

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

					<!-- Inline share for all screen sizes -->
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
		</div>

		<!-- Floating share bar (wide desktop only, supplements inline share) -->
		<div class="wbcom-sp__share-float">
			<?php include $wbcom_partials . 'share-buttons.php'; ?>
		</div>
	<?php endwhile; ?>
</div>

<?php
get_footer();
