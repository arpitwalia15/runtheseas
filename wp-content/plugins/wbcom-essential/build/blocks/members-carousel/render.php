<?php
/**
 * Server-side render for Members Carousel block.
 *
 * Two modes:
 *  - Editor (REST_REQUEST): Full SSR via BP template tags for live preview.
 *  - Frontend: Outputs a Swiper container hydrated by view.js via BP REST API.
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

if ( ! function_exists( 'buddypress' ) ) {
	echo '<p class="wbe-block-notice">' . esc_html__( 'BuddyPress is required for this block.', 'wbcom-essential' ) . '</p>';
	return;
}

$unique_id          = ! empty( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
$vis_classes        = \WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::get_visibility_classes( $attributes );
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::add( $unique_id, $attributes );

$total              = absint( $attributes['totalMembers'] ?? 12 );
$sort               = sanitize_text_field( $attributes['sortType'] ?? 'newest' );
$slides_desktop     = absint( $attributes['slidesPerView'] ?? 4 );
$slides_tablet      = absint( $attributes['slidesPerViewTablet'] ?? 2 );
$slides_mobile      = absint( $attributes['slidesPerViewMobile'] ?? 1 );
$do_autoplay        = ! empty( $attributes['autoplay'] );
$autoplay_delay     = absint( $attributes['autoplayDelay'] ?? 3000 );
$do_loop            = ! empty( $attributes['loop'] );
$show_dots          = ! empty( $attributes['showDots'] );
$show_arrows        = ! empty( $attributes['showArrows'] );
$space_between      = absint( $attributes['spaceBetween'] ?? 24 );
$avatar_size        = absint( $attributes['avatarSize'] ?? 80 );
$card_bg            = sanitize_hex_color( $attributes['cardBg'] ?? '#ffffff' );
$name_color         = sanitize_hex_color( $attributes['nameColor'] ?? '#1e1e2e' );
$meta_color         = sanitize_hex_color( $attributes['metaColor'] ?? '#6c757d' );
$card_radius        = absint( $attributes['cardBorderRadius'] ?? 12 );

// ── Swiper options ────────────────────────────────────────────────────────────
$swiper_options = array(
	'slidesPerView' => $slides_desktop,
	'spaceBetween'  => $space_between,
	'loop'          => $do_loop,
	'grabCursor'    => true,
	'breakpoints'   => array(
		0    => array( 'slidesPerView' => $slides_mobile ),
		768  => array( 'slidesPerView' => $slides_tablet ),
		1025 => array( 'slidesPerView' => $slides_desktop ),
	),
);

if ( $do_autoplay ) {
	$swiper_options['autoplay'] = array(
		'delay'                => $autoplay_delay,
		'disableOnInteraction' => false,
		'pauseOnMouseEnter'    => true,
	);
}

if ( $show_arrows ) {
	$swiper_options['navigation'] = array(
		'nextEl' => '.swiper-button-next',
		'prevEl' => '.swiper-button-prev',
	);
}

if ( $show_dots ) {
	$swiper_options['pagination'] = array(
		'el'        => '.swiper-pagination',
		'clickable' => true,
	);
}

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => trim( 'wbe-block-' . esc_attr( $unique_id ) . ' wbe-members-carousel ' . $vis_classes ),
	)
);

$card_style = '';
if ( $card_bg ) {
	$card_style .= '--wbe-mc-card-bg: ' . esc_attr( $card_bg ) . '; ';
}
if ( $name_color ) {
	$card_style .= '--wbe-mc-name-color: ' . esc_attr( $name_color ) . '; ';
}
if ( $card_radius ) {
	$card_style .= '--wbe-mc-card-radius: ' . absint( $card_radius ) . 'px; ';
}

// ── Detect context ────────────────────────────────────────────────────────────
$is_editor = defined( 'REST_REQUEST' ) && REST_REQUEST;
?>

<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes() ?>>

	<?php if ( $is_editor ) : ?>
		<?php
		// ── Editor: static SSR preview using BP template tags ────────────────────
		$members_args = array(
			'user_id'         => 0,
			'type'            => $sort,
			'per_page'        => $total,
			'max'             => $total,
			'populate_extras' => true,
			'search_terms'    => false,
		);

		$avatar_args = array(
			'type'   => 'full',
			'width'  => $avatar_size,
			'height' => $avatar_size,
			'class'  => 'wbe-members-carousel__avatar-img',
		);
		?>
		<?php if ( bp_has_members( $members_args ) ) : ?>
			<div class="swiper wbe-members-carousel__swiper"
				data-swiper-options="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>"
				style="<?php echo esc_attr( $card_style ); ?>">

				<div class="swiper-wrapper">
					<?php
					while ( bp_members() ) :
						bp_the_member();
						?>
						<div class="swiper-slide">
							<div class="wbe-members-carousel__card">
								<div class="wbe-members-carousel__avatar">
									<a href="<?php bp_member_permalink(); ?>">
										<?php bp_member_avatar( $avatar_args ); ?>
									</a>
								</div>
								<div class="wbe-members-carousel__info">
									<h3 class="wbe-members-carousel__name">
										<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
									</h3>
									<?php if ( ! empty( $attributes['showLastActive'] ) ) : ?>
										<span class="wbe-members-carousel__meta">
											<?php bp_member_last_active(); ?>
										</span>
									<?php endif; ?>
									<?php if ( ! empty( $attributes['showFriendButton'] ) && is_user_logged_in() && bp_is_active( 'friends' ) ) : ?>
										<div class="wbe-members-carousel__action">
											<?php bp_add_friend_button( bp_get_member_user_id() ); ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				</div>

				<?php if ( $show_dots ) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>

				<?php if ( $show_arrows ) : ?>
					<button class="swiper-button-prev" aria-label="<?php esc_attr_e( 'Previous', 'wbcom-essential' ); ?>">&#8249;</button>
					<button class="swiper-button-next" aria-label="<?php esc_attr_e( 'Next', 'wbcom-essential' ); ?>">&#8250;</button>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<p class="wbe-members-carousel__empty"><?php esc_html_e( 'No members found.', 'wbcom-essential' ); ?></p>
		<?php endif; ?>

	<?php else : ?>
		<?php
		// ── Frontend: JS-hydrated Swiper container ────────────────────────────────
		$config = array(
			'restUrl'          => rest_url( 'buddypress/v1/members' ),
			'restNonce'        => wp_create_nonce( 'wp_rest' ),
			'perPage'          => $total,
			'sortType'         => $sort,
			'showLastActive'   => ! empty( $attributes['showLastActive'] ),
			'showFriendButton' => ! empty( $attributes['showFriendButton'] ) && bp_is_active( 'friends' ),
			'avatarSize'       => $avatar_size,
			'showDots'         => $show_dots,
			'showArrows'       => $show_arrows,
			'swiperOptions'    => $swiper_options,
			'loggedIn'         => is_user_logged_in(),
			'currentUserId'    => get_current_user_id(),
			'colors'           => array(
				'cardBg'    => $card_bg,
				'nameColor' => $name_color,
				'metaColor' => $meta_color,
			),
			'cardRadius'       => $card_radius,
			'i18n'             => array(
				'loading'   => __( 'Loading members...', 'wbcom-essential' ),
				'empty'     => __( 'No members found.', 'wbcom-essential' ),
				'addFriend'   => __( 'Add Friend', 'wbcom-essential' ),
				'friends'     => __( 'Friends', 'wbcom-essential' ),
				'pending'     => __( 'Pending', 'wbcom-essential' ),
				'viewProfile' => __( 'View Profile', 'wbcom-essential' ),
				'previous'    => __( 'Previous', 'wbcom-essential' ),
				'next'        => __( 'Next', 'wbcom-essential' ),
			),
		);
		?>
		<div class="swiper wbe-members-carousel__swiper"
			data-wbe-mc-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
			style="<?php echo esc_attr( $card_style ); ?>">
			<div class="swiper-wrapper">
				<div class="swiper-slide wbe-members-carousel__loading">
					<p><?php esc_html_e( 'Loading members...', 'wbcom-essential' ); ?></p>
				</div>
			</div>
			<?php if ( $show_dots ) : ?>
				<div class="swiper-pagination"></div>
			<?php endif; ?>
			<?php if ( $show_arrows ) : ?>
				<button class="swiper-button-prev" aria-label="<?php esc_attr_e( 'Previous', 'wbcom-essential' ); ?>">&#8249;</button>
				<button class="swiper-button-next" aria-label="<?php esc_attr_e( 'Next', 'wbcom-essential' ); ?>">&#8250;</button>
			<?php endif; ?>
		</div>

	<?php endif; ?>
</div>
