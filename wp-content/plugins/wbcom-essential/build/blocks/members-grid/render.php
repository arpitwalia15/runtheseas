<?php
/**
 * Server-side render for Members Grid block.
 *
 * Two modes:
 *  - Editor (REST_REQUEST): Full SSR via BP template tags for live preview.
 *  - Frontend: Outputs a container div hydrated by view.js via BP REST API.
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

$unique_id   = ! empty( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
$vis_classes = \WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::get_visibility_classes( $attributes );
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::add( $unique_id, $attributes );

$total       = absint( $attributes['totalMembers'] ?? 12 );
$sort        = sanitize_text_field( $attributes['sortType'] ?? 'newest' );
$cols        = absint( $attributes['columns'] ?? 4 );
$cols_tab    = absint( $attributes['columnsTablet'] ?? 2 );
$cols_mob    = absint( $attributes['columnsMobile'] ?? 1 );
$gap         = absint( $attributes['gap'] ?? 24 );
$avatar_size = absint( $attributes['avatarSize'] ?? 80 );
$card_bg     = sanitize_hex_color( $attributes['cardBg'] ?? '#ffffff' );
$name_color  = sanitize_hex_color( $attributes['nameColor'] ?? '#1e1e2e' );
$meta_color  = sanitize_hex_color( $attributes['metaColor'] ?? '#6c757d' );
$card_radius = absint( $attributes['cardBorderRadius'] ?? 12 );

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => trim( 'wbe-block-' . esc_attr( $unique_id ) . ' wbe-members-grid ' . $vis_classes ),
		'style' => sprintf(
			'--wbe-mg-cols: %d; --wbe-mg-cols-tab: %d; --wbe-mg-cols-mob: %d; --wbe-mg-gap: %dpx; --wbe-mg-card-radius: %dpx;',
			$cols,
			$cols_tab,
			$cols_mob,
			$gap,
			$card_radius
		),
	)
);

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
			'class'  => 'wbe-members-grid__avatar-img',
		);
		?>
		<?php if ( bp_has_members( $members_args ) ) : ?>
			<div class="wbe-members-grid__list">
				<?php
				while ( bp_members() ) :
					bp_the_member();

					$card_styles = '';
					if ( $card_bg ) {
						$card_styles .= '--wbe-mg-card-bg: ' . esc_attr( $card_bg ) . '; ';
					}
					if ( $name_color ) {
						$card_styles .= '--wbe-mg-name-color: ' . esc_attr( $name_color ) . '; ';
					}
					if ( $meta_color ) {
						$card_styles .= '--wbe-mg-meta-color: ' . esc_attr( $meta_color ) . '; ';
					}
					?>
					<div class="wbe-members-grid__card" style="<?php echo esc_attr( $card_styles ); ?>">
						<div class="wbe-members-grid__avatar">
							<a href="<?php bp_member_permalink(); ?>">
								<?php bp_member_avatar( $avatar_args ); ?>
							</a>
						</div>
						<div class="wbe-members-grid__info">
							<h3 class="wbe-members-grid__name">
								<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
							</h3>
							<?php if ( ! empty( $attributes['showLastActive'] ) ) : ?>
								<span class="wbe-members-grid__meta">
									<?php bp_member_last_active(); ?>
								</span>
							<?php endif; ?>
							<?php if ( ! empty( $attributes['showFriendButton'] ) && is_user_logged_in() && bp_is_active( 'friends' ) ) : ?>
								<div class="wbe-members-grid__action">
									<?php bp_add_friend_button( bp_get_member_user_id() ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="wbe-members-grid__empty"><?php esc_html_e( 'No members found.', 'wbcom-essential' ); ?></p>
		<?php endif; ?>

	<?php else : ?>
		<?php
		// ── Frontend: JS-hydrated container ──────────────────────────────────────
		$config = array(
			'restUrl'         => rest_url( 'buddypress/v1/members' ),
			'restNonce'       => wp_create_nonce( 'wp_rest' ),
			'perPage'         => $total,
			'sortType'        => $sort,
			'showLastActive'  => ! empty( $attributes['showLastActive'] ),
			'showFriendButton'=> ! empty( $attributes['showFriendButton'] ) && bp_is_active( 'friends' ),
			'avatarSize'      => $avatar_size,
			'columns'         => $cols,
			'columnsTablet'   => $cols_tab,
			'columnsMobile'   => $cols_mob,
			'gap'             => $gap,
			'loggedIn'        => is_user_logged_in(),
			'currentUserId'   => get_current_user_id(),
			'colors'          => array(
				'cardBg'    => $card_bg,
				'nameColor' => $name_color,
				'metaColor' => $meta_color,
			),
			'i18n'            => array(
				'loading'    => __( 'Loading members...', 'wbcom-essential' ),
				'empty'      => __( 'No members found.', 'wbcom-essential' ),
				'addFriend'    => __( 'Add Friend', 'wbcom-essential' ),
				'friends'      => __( 'Friends', 'wbcom-essential' ),
				'pending'      => __( 'Pending', 'wbcom-essential' ),
				'viewProfile'  => __( 'View Profile', 'wbcom-essential' ),
				'activeAgo'  => __( 'Active %s', 'wbcom-essential' ),
			),
		);
		?>
		<div class="wbe-members-grid__list"
			data-wbe-mg-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
			aria-label="<?php esc_attr_e( 'Members Grid', 'wbcom-essential' ); ?>">
			<p class="wbe-members-grid__loading"><?php esc_html_e( 'Loading members...', 'wbcom-essential' ); ?></p>
		</div>

	<?php endif; ?>
</div>
