<?php
/**
 * Post Carousel Block - Server-Side Render
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

// ── Extract attributes ────────────────────────────────────────────────────────
$unique_id     = sanitize_html_class( $attributes['uniqueId'] ?? '' );
$post_type     = sanitize_key( $attributes['postType'] ?? 'post' );
$posts_per     = absint( $attributes['postsPerPage'] ?? 6 );
$cat_ids       = array_map( 'absint', (array) ( $attributes['categories'] ?? array() ) );
$order_by      = sanitize_key( $attributes['orderBy'] ?? 'date' );
$order         = in_array( strtoupper( $attributes['order'] ?? 'DESC' ), array( 'ASC', 'DESC' ), true )
	? strtoupper( $attributes['order'] )
	: 'DESC';
$display_mode  = sanitize_key( $attributes['displayMode'] ?? 'carousel' );
$slides        = absint( $attributes['slidesPerView'] ?? 3 );
$autoplay      = ! empty( $attributes['autoplay'] );
$autoplay_delay = absint( $attributes['autoplayDelay'] ?? 5000 );
$show_image    = ! empty( $attributes['showImage'] );
$show_excerpt  = ! empty( $attributes['showExcerpt'] );
$show_date     = ! empty( $attributes['showDate'] );
$show_category = ! empty( $attributes['showCategory'] );
$show_author   = ! empty( $attributes['showAuthor'] );
$excerpt_len   = absint( $attributes['excerptLength'] ?? 20 );
$image_ratio   = sanitize_text_field( $attributes['imageRatio'] ?? '16/9' );
$card_bg       = sanitize_hex_color( $attributes['cardBg'] ?? '#ffffff' ) ?: '#ffffff';
$title_color   = sanitize_hex_color( $attributes['titleColor'] ?? '#1e1e2e' ) ?: '#1e1e2e';
$excerpt_color = sanitize_hex_color( $attributes['excerptColor'] ?? '#6c757d' ) ?: '#6c757d';
$meta_color    = sanitize_hex_color( $attributes['metaColor'] ?? '#999999' ) ?: '#999999';
$accent_color  = sanitize_hex_color( $attributes['accentColor'] ?? '#667eea' ) ?: '#667eea';

// Visibility classes.
$visibility_classes = '';
if ( class_exists( 'WBCOM_ESSENTIAL\Gutenberg\WBE_CSS' ) ) {
	$visibility_classes = WBE_CSS::get_visibility_classes( $attributes );
}

// ── Scoped spacing / shadow / radius CSS ─────────────────────────────────────
if ( ! empty( $unique_id ) && class_exists( 'WBCOM_ESSENTIAL\Gutenberg\WBE_CSS' ) ) {
	WBE_CSS::add( $unique_id, $attributes );
	WBE_CSS::init();
}

// ── Build token CSS ──────────────────────────────────────────────────────────
$border_radius = $attributes['borderRadius'] ?? array( 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8 );
$radius_unit   = sanitize_text_field( $attributes['borderRadiusUnit'] ?? 'px' );
$radius_val    = sprintf(
	'%s%s %s%s %s%s %s%s',
	absint( $border_radius['top'] ?? 8 ), $radius_unit,
	absint( $border_radius['right'] ?? 8 ), $radius_unit,
	absint( $border_radius['bottom'] ?? 8 ), $radius_unit,
	absint( $border_radius['left'] ?? 8 ), $radius_unit
);

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

// Ratio for CSS aspect-ratio.
$ratio_css = esc_attr( str_replace( '/', ' / ', $image_ratio ) );

// ── WP_Query ─────────────────────────────────────────────────────────────────
$query_args = array(
	'post_type'      => $post_type,
	'posts_per_page' => $posts_per,
	'orderby'        => $order_by,
	'order'          => $order,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
);

if ( ! empty( $cat_ids ) && 'post' === $post_type ) {
	$query_args['category__in'] = $cat_ids;
}

$query = new WP_Query( $query_args );

if ( ! $query->have_posts() ) {
	echo '<div class="wbe-post-carousel wbe-post-carousel--empty"><p>' . esc_html__( 'No posts found.', 'wbcom-essential' ) . '</p></div>';
	return;
}

// ── Wrapper attributes ───────────────────────────────────────────────────────
$wrapper_class = trim( implode( ' ', array_filter( array(
	'wbe-post-carousel',
	'wbe-post-carousel--' . $display_mode,
	$unique_id ? 'wbe-block-' . $unique_id : '',
	$visibility_classes,
) ) ) );

$wrapper_attrs = get_block_wrapper_attributes( array(
	'class' => $wrapper_class,
) );

// ── Token CSS ────────────────────────────────────────────────────────────────
$token_css = '';
if ( $unique_id ) {
	$token_css = sprintf(
		'.wbe-block-%1$s { --wbe-pc-card-bg: %2$s; --wbe-pc-title-color: %3$s; --wbe-pc-excerpt-color: %4$s; --wbe-pc-meta-color: %5$s; --wbe-pc-accent: %6$s; --wbe-pc-card-radius: %7$s; --wbe-pc-card-shadow: %8$s; --wbe-pc-image-ratio: %9$s; }',
		esc_attr( $unique_id ),
		esc_attr( $card_bg ),
		esc_attr( $title_color ),
		esc_attr( $excerpt_color ),
		esc_attr( $meta_color ),
		esc_attr( $accent_color ),
		esc_attr( $radius_val ),
		esc_attr( $box_shadow_val ),
		esc_attr( $ratio_css )
	);
}

// ── Helper: build a post card ─────────────────────────────────────────────────
if ( ! function_exists( 'wbe_post_carousel_card' ) ) :
function wbe_post_carousel_card( $post, $show_image, $show_category, $show_excerpt, $excerpt_len, $show_date, $show_author ) {
	$post_id    = $post->ID;
	$permalink  = get_permalink( $post_id );
	$title      = get_the_title( $post_id );
	$thumb_id   = get_post_thumbnail_id( $post_id );
	$thumb_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
	$categories = get_the_category( $post_id );
	$date_str   = get_the_date( '', $post_id );
	$date_iso   = get_the_date( 'c', $post_id );
	$author     = get_the_author_meta( 'display_name', $post->post_author );

	// Excerpt.
	$raw_excerpt = $post->post_excerpt ?: $post->post_content;
	$words       = explode( ' ', wp_strip_all_tags( $raw_excerpt ) );
	$excerpt     = implode( ' ', array_slice( $words, 0, $excerpt_len ) );
	if ( count( $words ) > $excerpt_len ) {
		$excerpt .= '&hellip;';
	}

	ob_start();
	?>
	<article class="wbe-post-carousel__card" aria-label="<?php echo esc_attr( $title ); ?>">
		<?php if ( $show_image ) : ?>
		<div class="wbe-post-carousel__image-wrap">
			<a href="<?php echo esc_url( $permalink ); ?>" tabindex="-1">
				<?php if ( $thumb_url ) : ?>
				<img
					src="<?php echo esc_url( $thumb_url ); ?>"
					alt=""
					class="wbe-post-carousel__image"
					loading="lazy"
					decoding="async"
				/>
				<?php else : ?>
				<div class="wbe-post-carousel__image wbe-post-carousel__image--placeholder" aria-hidden="true"></div>
				<?php endif; ?>
			</a>
		</div>
		<?php endif; ?>

		<div class="wbe-post-carousel__body">
			<?php if ( $show_category && ! empty( $categories ) ) : ?>
			<div class="wbe-post-carousel__cats">
				<?php foreach ( array_slice( $categories, 0, 2 ) as $cat ) : ?>
				<a
					href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
					class="wbe-post-carousel__cat-badge"
				>
					<?php echo esc_html( $cat->name ); ?>
				</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<h3 class="wbe-post-carousel__title">
				<a href="<?php echo esc_url( $permalink ); ?>">
					<?php echo esc_html( $title ); ?>
				</a>
			</h3>

			<?php if ( $show_excerpt && $excerpt ) : ?>
			<p class="wbe-post-carousel__excerpt"><?php echo wp_kses_post( $excerpt ); ?></p>
			<?php endif; ?>

			<?php if ( $show_date || $show_author ) : ?>
			<div class="wbe-post-carousel__meta">
				<?php if ( $show_date ) : ?>
				<time class="wbe-post-carousel__date" datetime="<?php echo esc_attr( $date_iso ); ?>">
					<?php echo esc_html( $date_str ); ?>
				</time>
				<?php endif; ?>
				<?php if ( $show_author ) : ?>
				<span class="wbe-post-carousel__author"><?php echo esc_html( $author ); ?></span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</article>
	<?php
	return ob_get_clean();
}
endif;

// ── Output ───────────────────────────────────────────────────────────────────
?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $token_css ) : ?>
	<style><?php echo esc_html( $token_css ); ?></style>
	<?php endif; ?>

	<?php if ( in_array( $display_mode, array( 'carousel', 'slider' ), true ) ) : ?>
		<?php
		$swiper_slides = ( 'slider' === $display_mode ) ? 1 : $slides;
		?>
		<div
			class="swiper"
			data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
			data-delay="<?php echo esc_attr( $autoplay_delay ); ?>"
			data-loop="true"
			data-slides="<?php echo esc_attr( $swiper_slides ); ?>"
			data-mode="<?php echo esc_attr( $display_mode ); ?>"
		>
			<div class="swiper-wrapper">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<div class="swiper-slide">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wbe_post_carousel_card( get_post(), $show_image, $show_category, $show_excerpt, $excerpt_len, $show_date, $show_author );
					?>
				</div>
				<?php endwhile; ?>
			</div>

			<div class="swiper-pagination" aria-label="<?php esc_attr_e( 'Post carousel navigation', 'wbcom-essential' ); ?>"></div>
			<button class="swiper-button-prev" aria-label="<?php esc_attr_e( 'Previous post', 'wbcom-essential' ); ?>"></button>
			<button class="swiper-button-next" aria-label="<?php esc_attr_e( 'Next post', 'wbcom-essential' ); ?>"></button>
		</div>

	<?php else : ?>
		<?php // Grid mode — pure CSS, no JS. ?>
		<div class="wbe-post-carousel__grid">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wbe_post_carousel_card( get_post(), $show_image, $show_category, $show_excerpt, $excerpt_len, $show_date, $show_author );
			?>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
<?php
wp_reset_postdata();
