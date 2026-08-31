<?php
/**
 * Portfolio Grid Block - Server-Side Render
 *
 * @package WBCOM_Essential
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WBCOM_ESSENTIAL\Gutenberg\WBE_CSS;

// ── Shared infrastructure ─────────────────────────────────────────────────────
$unique_id   = ! empty( $attributes['uniqueId'] ) ? sanitize_html_class( $attributes['uniqueId'] ) : '';
$vis_classes = class_exists( 'WBCOM_ESSENTIAL\Gutenberg\WBE_CSS' ) ? WBE_CSS::get_visibility_classes( $attributes ) : '';

if ( ! empty( $unique_id ) && class_exists( 'WBCOM_ESSENTIAL\Gutenberg\WBE_CSS' ) ) {
	WBE_CSS::add( $unique_id, $attributes );
	WBE_CSS::init();
}

// ── Extract attributes ────────────────────────────────────────────────────────
$columns           = max( 1, absint( $attributes['columns'] ?? 3 ) );
$columns_tablet    = max( 1, absint( $attributes['columnsTablet'] ?? 2 ) );
$columns_mobile    = max( 1, absint( $attributes['columnsMobile'] ?? 1 ) );
$posts_per         = max( 1, absint( $attributes['postsPerPage'] ?? 9 ) );
$post_type         = sanitize_key( $attributes['postType'] ?? 'post' );
$cat_ids           = array_map( 'absint', (array) ( $attributes['categories'] ?? array() ) );
$show_filter       = ! empty( $attributes['showFilter'] );
$show_excerpt      = ! empty( $attributes['showExcerpt'] );
$show_category     = ! empty( $attributes['showCategory'] );
$image_ratio       = sanitize_text_field( $attributes['imageRatio'] ?? '1/1' );
$gap               = absint( $attributes['gap'] ?? 16 );
$hover_effect      = sanitize_key( $attributes['hoverEffect'] ?? 'overlay' );
$overlay_color     = sanitize_text_field( $attributes['overlayColor'] ?? 'rgba(102, 126, 234, 0.85)' );
$title_color       = sanitize_hex_color( $attributes['titleColor'] ?? '#ffffff' ) ?: '#ffffff';
$filter_active_clr = sanitize_hex_color( $attributes['filterActiveColor'] ?? '#667eea' ) ?: '#667eea';
$card_radius       = absint( $attributes['cardBorderRadius'] ?? 8 );

// ── Box shadow ────────────────────────────────────────────────────────────────
$box_shadow_val = 'none';
if ( ! empty( $attributes['boxShadow'] ) ) {
	$box_shadow_val = sprintf(
		'%dpx %dpx %dpx %dpx %s',
		intval( $attributes['shadowHorizontal'] ?? 0 ),
		intval( $attributes['shadowVertical'] ?? 8 ),
		absint( $attributes['shadowBlur'] ?? 24 ),
		intval( $attributes['shadowSpread'] ?? 0 ),
		sanitize_text_field( $attributes['shadowColor'] ?? 'rgba(0,0,0,0.08)' )
	);
}

// CSS aspect-ratio value.
$ratio_css = esc_attr( str_replace( '/', ' / ', $image_ratio ) );

// ── WP_Query ─────────────────────────────────────────────────────────────────
$query_args = array(
	'post_type'      => $post_type,
	'posts_per_page' => $posts_per,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_status'    => 'publish',
	'no_found_rows'  => true,
);

if ( ! empty( $cat_ids ) && 'post' === $post_type ) {
	$query_args['category__in'] = $cat_ids;
}

$query = new WP_Query( $query_args );

if ( ! $query->have_posts() ) {
	echo '<div class="wbe-portfolio-grid wbe-portfolio-grid--empty"><p>' . esc_html__( 'No portfolio items found.', 'wbcom-essential' ) . '</p></div>';
	return;
}

// ── Collect categories from result set for filter bar ─────────────────────────
$all_filter_cats = array( 0 => __( 'All', 'wbcom-essential' ) );
while ( $query->have_posts() ) {
	$query->the_post();
	$post_cats = get_the_category( get_the_ID() );
	foreach ( $post_cats as $cat ) {
		if ( ! isset( $all_filter_cats[ $cat->term_id ] ) ) {
			$all_filter_cats[ $cat->term_id ] = $cat->name;
		}
	}
}
$query->rewind_posts();

// ── Scoped token CSS ──────────────────────────────────────────────────────────
$token_css = '';
if ( $unique_id ) {
	$token_css = sprintf(
		'.wbe-block-%1$s {
			--wbe-pg-columns: %2$d;
			--wbe-pg-columns-tablet: %3$d;
			--wbe-pg-columns-mobile: %4$d;
			--wbe-pg-gap: %5$dpx;
			--wbe-pg-ratio: %6$s;
			--wbe-pg-card-radius: %7$dpx;
			--wbe-pg-overlay: %8$s;
			--wbe-pg-title-color: %9$s;
			--wbe-pg-filter-active: %10$s;
			--wbe-pg-card-shadow: %11$s;
		}',
		esc_attr( $unique_id ),
		$columns,
		$columns_tablet,
		$columns_mobile,
		$gap,
		esc_attr( $ratio_css ),
		$card_radius,
		esc_attr( $overlay_color ),
		esc_attr( $title_color ),
		esc_attr( $filter_active_clr ),
		esc_attr( $box_shadow_val )
	);

	// Responsive columns via media queries in the scoped token CSS.
	$token_css .= sprintf(
		'@media (max-width: 1024px) { .wbe-block-%1$s { --wbe-pg-columns: %2$d; } }',
		esc_attr( $unique_id ),
		$columns_tablet
	);
	$token_css .= sprintf(
		'@media (max-width: 640px) { .wbe-block-%1$s { --wbe-pg-columns: %2$d; } }',
		esc_attr( $unique_id ),
		$columns_mobile
	);
}

// ── Wrapper ───────────────────────────────────────────────────────────────────
$wrapper_class = trim( implode( ' ', array_filter( array(
	'wbe-portfolio-grid',
	'wbe-portfolio-grid--hover-' . $hover_effect,
	$unique_id ? 'wbe-block-' . $unique_id : '',
	$vis_classes,
) ) ) );

$wrapper_attrs = get_block_wrapper_attributes( array( 'class' => $wrapper_class ) );
?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $token_css ) : ?>
	<style><?php echo esc_html( $token_css ); ?></style>
	<?php endif; ?>

	<?php if ( $show_filter && count( $all_filter_cats ) > 1 ) : ?>
	<div class="wbe-portfolio-grid__filter" role="tablist" aria-label="<?php esc_attr_e( 'Portfolio filter', 'wbcom-essential' ); ?>">
		<?php foreach ( $all_filter_cats as $term_id => $label ) : ?>
		<button
			class="wbe-portfolio-grid__filter-btn<?php echo 0 === $term_id ? ' is-active' : ''; ?>"
			data-filter="<?php echo 0 === $term_id ? 'all' : esc_attr( 'cat-' . $term_id ); ?>"
			role="tab"
			aria-selected="<?php echo 0 === $term_id ? 'true' : 'false'; ?>"
		>
			<?php echo esc_html( $label ); ?>
		</button>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="wbe-portfolio-grid__grid">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();

			$post_id    = get_the_ID();
			$permalink  = get_permalink( $post_id );
			$title      = get_the_title( $post_id );
			$thumb_id   = get_post_thumbnail_id( $post_id );
			$thumb_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
			$post_cats  = get_the_category( $post_id );
			$cat_slugs  = array_map( function( $c ) { return 'cat-' . $c->term_id; }, $post_cats );
			$data_cats  = implode( ' ', $cat_slugs );

			// Excerpt.
			$excerpt = '';
			if ( $show_excerpt ) {
				$raw     = get_the_excerpt( $post_id );
				$excerpt = wp_trim_words( $raw ?: get_the_content( null, false, $post_id ), 20, '&hellip;' );
			}
			?>
		<article
			class="wbe-portfolio-grid__item"
			data-category="<?php echo esc_attr( $data_cats ?: 'uncategorized' ); ?>"
			aria-label="<?php echo esc_attr( $title ); ?>"
		>
			<div class="wbe-portfolio-grid__image-wrap">
				<?php if ( $thumb_url ) : ?>
				<img
					src="<?php echo esc_url( $thumb_url ); ?>"
					alt=""
					class="wbe-portfolio-grid__image"
					loading="lazy"
					decoding="async"
				/>
				<?php else : ?>
				<div class="wbe-portfolio-grid__image wbe-portfolio-grid__image--placeholder" aria-hidden="true"></div>
				<?php endif; ?>

				<div class="wbe-portfolio-grid__overlay" aria-hidden="true">
					<div class="wbe-portfolio-grid__overlay-inner">
						<h3 class="wbe-portfolio-grid__title">
							<?php echo esc_html( $title ); ?>
						</h3>
						<?php if ( $show_category && ! empty( $post_cats ) ) : ?>
						<span class="wbe-portfolio-grid__cat-label">
							<?php echo esc_html( $post_cats[0]->name ); ?>
						</span>
						<?php endif; ?>
						<?php if ( $show_excerpt && $excerpt ) : ?>
						<p class="wbe-portfolio-grid__excerpt"><?php echo esc_html( $excerpt ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<a
					href="<?php echo esc_url( $permalink ); ?>"
					class="wbe-portfolio-grid__link"
					aria-label="<?php
						/* translators: %s: post title */
						echo esc_attr( sprintf( __( 'View %s', 'wbcom-essential' ), $title ) );
					?>"
				></a>
			</div>
		</article>
		<?php endwhile; ?>
	</div>
</div>
<?php
wp_reset_postdata();
