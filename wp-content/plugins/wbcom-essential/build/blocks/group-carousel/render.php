<?php
/**
 * Group Carousel Block — Server-Side Render
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

// ── Extract attributes ────────────────────────────────────────────────────────
$unique_id       = ! empty( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
$total_groups    = absint( $attributes['totalGroups'] ?? 12 );
$sort_type       = sanitize_text_field( $attributes['sortType'] ?? 'active' );
$slides_desktop  = absint( $attributes['slidesPerView'] ?? 3 );
$slides_tablet   = absint( $attributes['slidesPerViewTablet'] ?? 2 );
$slides_mobile   = absint( $attributes['slidesPerViewMobile'] ?? 1 );
$do_autoplay     = ! empty( $attributes['autoplay'] );
$autoplay_delay  = absint( $attributes['autoplayDelay'] ?? 3000 );
$do_loop         = ! empty( $attributes['loop'] );
$show_dots       = ! empty( $attributes['showDots'] );
$show_arrows     = ! empty( $attributes['showArrows'] );
$show_desc       = ! empty( $attributes['showDescription'] );
$show_count      = ! empty( $attributes['showMemberCount'] );
$show_join       = ! empty( $attributes['showJoinButton'] ) && bp_is_active( 'groups' );
$space_between   = absint( $attributes['spaceBetween'] ?? 24 );
$avatar_size     = absint( $attributes['avatarSize'] ?? 80 );
$card_bg         = sanitize_hex_color( $attributes['cardBg'] ?? '#ffffff' ) ?: '#ffffff';
$name_color      = sanitize_hex_color( $attributes['nameColor'] ?? '#1e1e2e' ) ?: '#1e1e2e';

// ── Visibility + CSS ──────────────────────────────────────────────────────────
$vis_classes = \WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::get_visibility_classes( $attributes );
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::add( $unique_id, $attributes );

// ── Swiper options (serialized to data attr) ──────────────────────────────────
$swiper_options = array(
	'slidesPerView' => $slides_desktop,
	'spaceBetween'  => $space_between,
	'loop'          => $do_loop,
	'grabCursor'    => true,
	'breakpoints'   => array(
		0    => array( 'slidesPerView' => $slides_mobile, 'spaceBetween' => 16 ),
		768  => array( 'slidesPerView' => $slides_tablet, 'spaceBetween' => $space_between ),
		1025 => array( 'slidesPerView' => $slides_desktop, 'spaceBetween' => $space_between ),
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

// ── Wrapper ───────────────────────────────────────────────────────────────────
$wrapper = get_block_wrapper_attributes(
	array(
		'class' => trim( 'wbe-group-carousel' . ( $unique_id ? ' wbe-block-' . esc_attr( $unique_id ) : '' ) . ( $vis_classes ? ' ' . $vis_classes : '' ) ),
	)
);

// ── Inline token CSS ──────────────────────────────────────────────────────────
$card_style = '--wbe-gc-card-bg: ' . esc_attr( $card_bg ) . '; --wbe-gc-name-color: ' . esc_attr( $name_color ) . ';';

// ── Detect context ────────────────────────────────────────────────────────────
$is_editor = defined( 'REST_REQUEST' ) && REST_REQUEST;
?>

<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes() ?>>

	<?php if ( $is_editor ) : ?>
		<?php
		// ── Editor: static SSR preview using BP template tags ────────────────────
		$groups_args = array(
			'type'            => $sort_type,
			'per_page'        => $total_groups,
			'max'             => $total_groups,
			'populate_extras' => true,
		);

		$avatar_args = array(
			'type'   => 'full',
			'width'  => $avatar_size,
			'height' => $avatar_size,
			'class'  => 'wbe-group-carousel__avatar-img',
			'object' => 'group',
		);
		?>
		<?php if ( bp_has_groups( $groups_args ) ) : ?>
			<div class="swiper wbe-group-carousel__swiper"
				data-swiper-options="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>"
				style="<?php echo esc_attr( $card_style ); ?>">

				<div class="swiper-wrapper">
					<?php
					while ( bp_groups() ) :
						bp_the_group();

						// Group URL — use bp_get_group_url() with fallback.
						if ( function_exists( 'bp_get_group_url' ) ) {
							$group_url = bp_get_group_url( groups_get_current_group() );
						} else {
							$group_url = bp_get_group_permalink( groups_get_current_group() );
						}
						$group_url = esc_url( $group_url );
						?>
						<div class="swiper-slide">
							<div class="wbe-group-carousel__card">

								<div class="wbe-group-carousel__avatar">
									<a href="<?php echo $group_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already esc_url'd above ?>" aria-label="<?php echo esc_attr( bp_get_group_name() ); ?>">
										<?php bp_group_avatar( $avatar_args ); ?>
									</a>
								</div>

								<div class="wbe-group-carousel__info">
									<h3 class="wbe-group-carousel__name">
										<a href="<?php echo $group_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo esc_html( bp_get_group_name() ); ?></a>
									</h3>

									<?php if ( $show_count ) : ?>
										<span class="wbe-group-carousel__count">
											<?php
											$member_count = bp_get_group_member_count();
											echo esc_html( $member_count );
											?>
										</span>
									<?php endif; ?>

									<?php if ( $show_desc ) : ?>
										<p class="wbe-group-carousel__desc">
											<?php echo wp_kses_post( bp_get_group_description_excerpt( array( 'length' => 80 ) ) ); ?>
										</p>
									<?php endif; ?>

									<?php if ( $show_join && is_user_logged_in() ) : ?>
										<div class="wbe-group-carousel__action">
											<?php bp_group_join_button( groups_get_current_group() ); ?>
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
			<p class="wbe-group-carousel__empty"><?php esc_html_e( 'No groups found.', 'wbcom-essential' ); ?></p>
		<?php endif; ?>

	<?php else : ?>
		<?php
		// ── Frontend: JS-hydrated Swiper container ────────────────────────────────
		$config = array(
			'restUrl'         => rest_url( 'buddypress/v1/groups' ),
			'restNonce'       => wp_create_nonce( 'wp_rest' ),
			'perPage'         => $total_groups,
			'sortType'        => $sort_type,
			'showDescription' => $show_desc,
			'showMemberCount' => $show_count,
			'showJoinButton'  => $show_join,
			'avatarSize'      => $avatar_size,
			'showDots'        => $show_dots,
			'showArrows'      => $show_arrows,
			'swiperOptions'   => $swiper_options,
			'loggedIn'        => is_user_logged_in(),
			'currentUserId'   => get_current_user_id(),
			'colors'          => array(
				'cardBg'    => $card_bg,
				'nameColor' => $name_color,
			),
			'i18n'            => array(
				'loading'     => __( 'Loading groups...', 'wbcom-essential' ),
				'empty'       => __( 'No groups found.', 'wbcom-essential' ),
				'members'     => __( 'Members', 'wbcom-essential' ),
				'previous'    => __( 'Previous', 'wbcom-essential' ),
				'next'        => __( 'Next', 'wbcom-essential' ),
				'joinGroup'   => __( 'Join Group', 'wbcom-essential' ),
				'leaveGroup'  => __( 'Leave', 'wbcom-essential' ),
				'joined'      => __( 'Joined', 'wbcom-essential' ),
				'pending'     => __( 'Pending', 'wbcom-essential' ),
			),
		);
		?>
		<div class="swiper wbe-group-carousel__swiper"
			data-wbe-gc-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
			style="<?php echo esc_attr( $card_style ); ?>">
			<div class="swiper-wrapper">
				<div class="swiper-slide wbe-group-carousel__loading">
					<p><?php esc_html_e( 'Loading groups...', 'wbcom-essential' ); ?></p>
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
