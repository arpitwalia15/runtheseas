<?php
/**
 * Product Grid Block - Server-Side Render
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
$columns        = absint( $attributes['columns'] ?? 4 );
$cols_tablet    = absint( $attributes['columnsTablet'] ?? 2 );
$cols_mobile    = absint( $attributes['columnsMobile'] ?? 1 );
$posts_per      = absint( $attributes['postsPerPage'] ?? 8 );
$cat_ids        = array_map( 'absint', (array) ( $attributes['categories'] ?? array() ) );
$order_by       = sanitize_key( $attributes['orderBy'] ?? 'date' );
$order          = in_array( strtoupper( $attributes['order'] ?? 'DESC' ), array( 'ASC', 'DESC' ), true )
	? strtoupper( $attributes['order'] )
	: 'DESC';
$show_image     = ! empty( $attributes['showImage'] );
$show_price     = ! empty( $attributes['showPrice'] );
$show_rating    = ! empty( $attributes['showRating'] );
$show_atc       = ! empty( $attributes['showAddToCart'] );
$show_sale      = ! empty( $attributes['showSaleBadge'] );
$image_ratio    = sanitize_text_field( $attributes['imageRatio'] ?? '1/1' );
$gap            = absint( $attributes['gap'] ?? 24 );
$card_bg        = sanitize_hex_color( $attributes['cardBg'] ?? '#ffffff' ) ?: '#ffffff';
$title_color    = sanitize_hex_color( $attributes['titleColor'] ?? '#1e1e2e' ) ?: '#1e1e2e';
$price_color    = sanitize_hex_color( $attributes['priceColor'] ?? '#667eea' ) ?: '#667eea';
$sale_color     = sanitize_hex_color( $attributes['saleBadgeColor'] ?? '#e74c3c' ) ?: '#e74c3c';

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

// ── Border radius & box-shadow ────────────────────────────────────────────────
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

$ratio_css = esc_attr( str_replace( '/', ' / ', $image_ratio ) );

// ── Map orderby to WP_Query args ──────────────────────────────────────────────
$meta_key     = '';
$orderby_val  = $order_by;
if ( 'price' === $order_by ) {
	$orderby_val = 'meta_value_num';
	$meta_key    = '_price';
} elseif ( 'popularity' === $order_by ) {
	$orderby_val = 'meta_value_num';
	$meta_key    = 'total_sales';
} elseif ( 'rating' === $order_by ) {
	$orderby_val = 'meta_value_num';
	$meta_key    = '_wc_average_rating';
}

// ── Query ─────────────────────────────────────────────────────────────────────
$query_args = array(
	'post_type'      => 'product',
	'posts_per_page' => $posts_per,
	'orderby'        => $orderby_val,
	'order'          => $order,
	'post_status'    => 'publish',
	'no_found_rows'  => true,
);
if ( $meta_key ) {
	$query_args['meta_key'] = $meta_key; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
}
if ( ! empty( $cat_ids ) ) {
	$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $cat_ids,
		),
	);
}

$query = new WP_Query( $query_args );

if ( ! $query->have_posts() ) {
	echo '<div class="wbe-product-grid wbe-product-grid--empty"><p>' . esc_html__( 'No products found.', 'wbcom-essential' ) . '</p></div>';
	return;
}

// ── Wrapper ───────────────────────────────────────────────────────────────────
$wrapper_class = trim( implode( ' ', array_filter( array(
	'wbe-product-grid',
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
		'.wbe-block-%1$s { --wbe-pg-cols: %2$d; --wbe-pg-cols-tablet: %3$d; --wbe-pg-cols-mobile: %4$d; --wbe-pg-gap: %5$dpx; --wbe-pg-card-bg: %6$s; --wbe-pg-title-color: %7$s; --wbe-pg-price-color: %8$s; --wbe-pg-sale-color: %9$s; --wbe-pg-card-radius: %10$s; --wbe-pg-card-shadow: %11$s; --wbe-pg-image-ratio: %12$s; }',
		esc_attr( $unique_id ),
		$columns,
		$cols_tablet,
		$cols_mobile,
		$gap,
		esc_attr( $card_bg ),
		esc_attr( $title_color ),
		esc_attr( $price_color ),
		esc_attr( $sale_color ),
		esc_attr( $radius_val ),
		esc_attr( $box_shadow_val ),
		esc_attr( $ratio_css )
	);

	// Responsive column overrides scoped to this block instance.
	$token_css .= sprintf(
		'@media (max-width:1024px){ .wbe-block-%1$s { --wbe-pg-cols: %2$d; } }',
		esc_attr( $unique_id ),
		$cols_tablet
	);
	$token_css .= sprintf(
		'@media (max-width:640px){ .wbe-block-%1$s { --wbe-pg-cols: %2$d; } }',
		esc_attr( $unique_id ),
		$cols_mobile
	);
}

?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $token_css ) : ?>
	<style><?php echo esc_html( $token_css ); ?></style>
	<?php endif; ?>

	<div class="wbe-product-grid__inner">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			$product = wc_get_product( get_the_ID() );
			if ( ! $product ) {
				continue;
			}

			$product_id   = $product->get_id();
			$permalink    = get_permalink( $product_id );
			$title        = $product->get_name();
			$is_on_sale   = $product->is_on_sale();
			$rating       = (float) $product->get_average_rating();
			$rating_count = $product->get_rating_count();
			$thumb_id     = $product->get_image_id();
			$thumb_url    = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
			$price_html   = $product->get_price_html();
			?>
			<article class="wbe-product-grid__card" aria-label="<?php echo esc_attr( $title ); ?>">
				<?php if ( $show_image ) : ?>
				<div class="wbe-product-grid__image-wrap">
					<a href="<?php echo esc_url( $permalink ); ?>" tabindex="-1">
						<img
							src="<?php echo esc_url( $thumb_url ); ?>"
							alt=""
							class="wbe-product-grid__image"
							loading="lazy"
							decoding="async"
						/>
					</a>
					<?php if ( $show_sale && $is_on_sale ) : ?>
					<span class="wbe-product-grid__sale-badge" aria-label="<?php esc_attr_e( 'Sale', 'wbcom-essential' ); ?>">
						<?php esc_html_e( 'Sale', 'wbcom-essential' ); ?>
					</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<div class="wbe-product-grid__body">
					<h3 class="wbe-product-grid__title">
						<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
					</h3>

					<?php if ( $show_rating && $rating_count > 0 ) : ?>
					<div class="wbe-product-grid__rating" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %s out of 5', 'wbcom-essential' ), number_format( $rating, 1 ) ) ); ?>">
						<?php
						for ( $i = 1; $i <= 5; $i++ ) :
							if ( $rating >= $i ) {
								$star_class = 'wbe-product-grid__star wbe-product-grid__star--full';
							} elseif ( $rating >= $i - 0.5 ) {
								$star_class = 'wbe-product-grid__star wbe-product-grid__star--half';
							} else {
								$star_class = 'wbe-product-grid__star wbe-product-grid__star--empty';
							}
							?>
							<span class="<?php echo esc_attr( $star_class ); ?>" aria-hidden="true"></span>
						<?php endfor; ?>
					</div>
					<?php endif; ?>

					<?php if ( $show_price && $price_html ) : ?>
					<div class="wbe-product-grid__price">
						<?php echo wp_kses_post( $price_html ); ?>
					</div>
					<?php endif; ?>

					<?php if ( $show_atc ) : ?>
					<div class="wbe-product-grid__atc">
						<?php
						woocommerce_template_loop_add_to_cart(
							array(
								'quantity' => 1,
							)
						);
						?>
					</div>
					<?php endif; ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</div>
<?php
wp_reset_postdata();
