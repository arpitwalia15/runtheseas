<?php
/**
 * Customer Reviews Block - Server-Side Render
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

if ( ! class_exists( 'WooCommerce' ) ) {
	echo '<p class="wbe-block-notice">' . esc_html__( 'WooCommerce plugin is required for this block.', 'wbcom-essential' ) . '</p>';
	return;
}

use WBCOM_ESSENTIAL\Gutenberg\WBE_CSS;

// ── Extract attributes ────────────────────────────────────────────────────────
$unique_id      = sanitize_html_class( $attributes['uniqueId'] ?? '' );
$review_count   = absint( $attributes['reviewCount'] ?? 6 );
$slides         = absint( $attributes['slidesPerView'] ?? 2 );
$autoplay       = ! empty( $attributes['autoplay'] );
$autoplay_delay = absint( $attributes['autoplayDelay'] ?? 4000 );
$loop           = ! empty( $attributes['loop'] );
$show_dots      = ! empty( $attributes['showDots'] );
$show_arrows    = ! empty( $attributes['showArrows'] );
$show_rating    = ! empty( $attributes['showRating'] );
$show_product   = ! empty( $attributes['showProduct'] );
$show_avatar    = ! empty( $attributes['showAvatar'] );
$quote_bg       = sanitize_hex_color( $attributes['quoteBg'] ?? '#ffffff' ) ?: '#ffffff';
$quote_color    = sanitize_hex_color( $attributes['quoteColor'] ?? '#1e1e2e' ) ?: '#1e1e2e';
$name_color     = sanitize_hex_color( $attributes['nameColor'] ?? '#1e1e2e' ) ?: '#1e1e2e';
$rating_color   = sanitize_hex_color( $attributes['ratingColor'] ?? '#f5a623' ) ?: '#f5a623';

// Visibility classes.
$visibility_classes = '';
if ( class_exists( 'WBCOM_ESSENTIAL\Gutenberg\WBE_CSS' ) ) {
	$visibility_classes = WBE_CSS::get_visibility_classes( $attributes );
}

// ── Scoped CSS ────────────────────────────────────────────────────────────────
if ( ! empty( $unique_id ) && class_exists( 'WBCOM_ESSENTIAL\Gutenberg\WBE_CSS' ) ) {
	WBE_CSS::add( $unique_id, $attributes );
	WBE_CSS::init();
}

// ── Border radius & shadow ────────────────────────────────────────────────────
$border_radius = $attributes['borderRadius'] ?? array( 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12 );
$radius_unit   = sanitize_text_field( $attributes['borderRadiusUnit'] ?? 'px' );
$radius_val    = sprintf(
	'%s%s %s%s %s%s %s%s',
	absint( $border_radius['top'] ?? 12 ), $radius_unit,
	absint( $border_radius['right'] ?? 12 ), $radius_unit,
	absint( $border_radius['bottom'] ?? 12 ), $radius_unit,
	absint( $border_radius['left'] ?? 12 ), $radius_unit
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

// ── Query reviews ─────────────────────────────────────────────────────────────
$reviews = get_comments( array(
	'type'    => 'review',
	'status'  => 'approve',
	'number'  => $review_count,
	'orderby' => 'comment_date',
	'order'   => 'DESC',
) );

if ( empty( $reviews ) ) {
	echo '<div class="wbe-customer-reviews wbe-customer-reviews--empty"><p>' . esc_html__( 'No reviews found.', 'wbcom-essential' ) . '</p></div>';
	return;
}

// ── Wrapper ───────────────────────────────────────────────────────────────────
$wrapper_class = trim( implode( ' ', array_filter( array(
	'wbe-customer-reviews',
	$unique_id ? 'wbe-block-' . $unique_id : '',
	$visibility_classes,
) ) ) );

$wrapper_attrs = get_block_wrapper_attributes( array(
	'class' => $wrapper_class,
) );

// ── Token CSS ─────────────────────────────────────────────────────────────────
$token_css = '';
if ( $unique_id ) {
	$token_css = sprintf(
		'.wbe-block-%1$s { --wbe-cr-quote-bg: %2$s; --wbe-cr-quote-color: %3$s; --wbe-cr-name-color: %4$s; --wbe-cr-rating-color: %5$s; --wbe-cr-card-radius: %6$s; --wbe-cr-card-shadow: %7$s; }',
		esc_attr( $unique_id ),
		esc_attr( $quote_bg ),
		esc_attr( $quote_color ),
		esc_attr( $name_color ),
		esc_attr( $rating_color ),
		esc_attr( $radius_val ),
		esc_attr( $box_shadow_val )
	);
}

// ── Swiper options ────────────────────────────────────────────────────────────
$swiper_options = array(
	'autoplay'      => $autoplay,
	'delay'         => $autoplay_delay,
	'loop'          => $loop,
	'slidesDesktop' => $slides,
	'slidesTablet'  => min( $slides, 2 ),
	'slidesMobile'  => 1,
	'showDots'      => $show_dots,
	'showArrows'    => $show_arrows,
);

?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $token_css ) : ?>
	<style><?php echo esc_html( $token_css ); ?></style>
	<?php endif; ?>

	<div
		class="swiper wbe-customer-reviews__swiper"
		data-swiper-options="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>"
	>
		<div class="swiper-wrapper">
			<?php foreach ( $reviews as $review ) :
				$review_id    = $review->comment_ID;
				$post_id      = $review->comment_post_ID;
				$author_name  = $review->comment_author;
				$review_text  = $review->comment_content;
				$rating       = (int) get_comment_meta( $review_id, 'rating', true );
				$product      = wc_get_product( $post_id );
				$product_link = $product ? get_permalink( $post_id ) : '';
				$product_name = $product ? $product->get_name() : '';
				$avatar_html  = $show_avatar
					? get_avatar( $review->comment_author_email, 48, '', esc_attr( $author_name ) )
					: '';
				?>
				<div class="swiper-slide">
					<div class="wbe-customer-reviews__card" role="article" aria-label="<?php echo esc_attr( sprintf( __( 'Review by %s', 'wbcom-essential' ), $author_name ) ); ?>">
						<div class="wbe-customer-reviews__quote-mark" aria-hidden="true">&ldquo;</div>

						<?php if ( $show_rating && $rating > 0 ) : ?>
						<div class="wbe-customer-reviews__rating" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %d out of 5', 'wbcom-essential' ), $rating ) ); ?>">
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
								<span class="wbe-customer-reviews__star<?php echo ( $i <= $rating ) ? ' wbe-customer-reviews__star--filled' : ' wbe-customer-reviews__star--empty'; ?>" aria-hidden="true"></span>
							<?php endfor; ?>
						</div>
						<?php endif; ?>

						<blockquote class="wbe-customer-reviews__text">
							<?php echo wp_kses_post( $review_text ); ?>
						</blockquote>

						<footer class="wbe-customer-reviews__footer">
							<?php if ( $show_avatar && $avatar_html ) : ?>
							<div class="wbe-customer-reviews__avatar" aria-hidden="true">
								<?php echo $avatar_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<?php endif; ?>

							<div class="wbe-customer-reviews__meta">
								<span class="wbe-customer-reviews__name"><?php echo esc_html( $author_name ); ?></span>

								<?php if ( $show_product && $product_name ) : ?>
								<a
									class="wbe-customer-reviews__product"
									href="<?php echo esc_url( $product_link ); ?>"
								>
									<?php echo esc_html( $product_name ); ?>
								</a>
								<?php endif; ?>
							</div>
						</footer>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if ( $show_dots ) : ?>
		<div class="swiper-pagination" aria-label="<?php esc_attr_e( 'Reviews carousel navigation', 'wbcom-essential' ); ?>"></div>
		<?php endif; ?>

		<?php if ( $show_arrows ) : ?>
		<button class="swiper-button-prev" aria-label="<?php esc_attr_e( 'Previous review', 'wbcom-essential' ); ?>"></button>
		<button class="swiper-button-next" aria-label="<?php esc_attr_e( 'Next review', 'wbcom-essential' ); ?>"></button>
		<?php endif; ?>
	</div>
</div>
