<?php
/**
 * Server-side render for Groups Grid block.
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

if ( ! bp_is_active( 'groups' ) ) {
	return;
}

$unique_id   = ! empty( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
$vis_classes = \WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::get_visibility_classes( $attributes );
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::add( $unique_id, $attributes );

$total       = absint( $attributes['totalGroups'] ?? 12 );
$sort        = sanitize_text_field( $attributes['sortType'] ?? 'active' );
$cols        = absint( $attributes['columns'] ?? 3 );
$cols_tab    = absint( $attributes['columnsTablet'] ?? 2 );
$cols_mob    = absint( $attributes['columnsMobile'] ?? 1 );
$gap         = absint( $attributes['gap'] ?? 24 );
$avatar_size = absint( $attributes['avatarSize'] ?? 80 );
$card_bg     = sanitize_hex_color( $attributes['cardBg'] ?? '#ffffff' );
$name_color  = sanitize_hex_color( $attributes['nameColor'] ?? '#1e1e2e' );
$desc_color  = sanitize_hex_color( $attributes['descColor'] ?? '#6c757d' );
$card_radius = absint( $attributes['cardBorderRadius'] ?? 12 );

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => trim( 'wbe-block-' . esc_attr( $unique_id ) . ' wbe-groups-grid ' . $vis_classes ),
		'style' => sprintf(
			'--wbe-gg-cols: %d; --wbe-gg-cols-tab: %d; --wbe-gg-cols-mob: %d; --wbe-gg-gap: %dpx; --wbe-gg-card-radius: %dpx;',
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
		$groups_args = array(
			'user_id'         => 0,
			'type'            => $sort,
			'per_page'        => $total,
			'max'             => $total,
			'populate_extras' => true,
		);

		$avatar_args = array(
			'type'   => 'full',
			'width'  => $avatar_size,
			'height' => $avatar_size,
			'class'  => 'wbe-groups-grid__avatar-img',
		);

		// Inline card-level CSS vars.
		$card_style = '';
		if ( $card_bg ) {
			$card_style .= '--wbe-gg-card-bg: ' . esc_attr( $card_bg ) . '; ';
		}
		if ( $name_color ) {
			$card_style .= '--wbe-gg-name-color: ' . esc_attr( $name_color ) . '; ';
		}
		if ( $desc_color ) {
			$card_style .= '--wbe-gg-desc-color: ' . esc_attr( $desc_color ) . '; ';
		}
		?>
		<?php if ( bp_has_groups( $groups_args ) ) : ?>
			<div class="wbe-groups-grid__list">
				<?php
				while ( bp_groups() ) :
					bp_the_group();

					// Prefer bp_get_group_url() (BP 12+), fall back to bp_get_group_permalink().
					$group_url = function_exists( 'bp_get_group_url' )
						? bp_get_group_url()
						: bp_get_group_permalink();
					?>
					<div class="wbe-groups-grid__card" style="<?php echo esc_attr( $card_style ); ?>">
						<div class="wbe-groups-grid__avatar">
							<a href="<?php echo esc_url( $group_url ); ?>">
								<?php bp_group_avatar( $avatar_args ); ?>
							</a>
						</div>
						<div class="wbe-groups-grid__info">
							<h3 class="wbe-groups-grid__name">
								<a href="<?php echo esc_url( $group_url ); ?>"><?php bp_group_name(); ?></a>
							</h3>

							<?php if ( ! empty( $attributes['showDescription'] ) ) : ?>
								<p class="wbe-groups-grid__desc">
									<?php echo wp_kses_post( wp_trim_words( bp_get_group_description_excerpt(), 15 ) ); ?>
								</p>
							<?php endif; ?>

							<div class="wbe-groups-grid__meta">
								<?php if ( ! empty( $attributes['showMemberCount'] ) ) : ?>
									<span class="wbe-groups-grid__count">
										<?php bp_group_member_count(); ?>
									</span>
								<?php endif; ?>
								<?php if ( ! empty( $attributes['showLastActive'] ) ) : ?>
									<span class="wbe-groups-grid__active">
										<?php
										printf(
											/* translators: %s: Group last active time */
											esc_html__( 'Active %s', 'wbcom-essential' ),
											esc_html( bp_get_group_last_active() )
										);
										?>
									</span>
								<?php endif; ?>
							</div>

							<?php if ( ! empty( $attributes['showJoinButton'] ) && is_user_logged_in() ) : ?>
								<div class="wbe-groups-grid__action">
									<?php bp_group_join_button(); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p class="wbe-groups-grid__empty"><?php esc_html_e( 'No groups found.', 'wbcom-essential' ); ?></p>
		<?php endif; ?>

	<?php else : ?>
		<?php
		// ── Frontend: JS-hydrated container ──────────────────────────────────────
		$config = array(
			'restUrl'          => rest_url( 'buddypress/v1/groups' ),
			'restNonce'        => wp_create_nonce( 'wp_rest' ),
			'perPage'          => $total,
			'sortType'         => $sort,
			'showDescription'  => ! empty( $attributes['showDescription'] ),
			'showMemberCount'  => ! empty( $attributes['showMemberCount'] ),
			'showLastActive'   => ! empty( $attributes['showLastActive'] ),
			'showJoinButton'   => ! empty( $attributes['showJoinButton'] ),
			'avatarSize'       => $avatar_size,
			'columns'          => $cols,
			'columnsTablet'    => $cols_tab,
			'columnsMobile'    => $cols_mob,
			'gap'              => $gap,
			'loggedIn'         => is_user_logged_in(),
			'currentUserId'    => get_current_user_id(),
			'colors'           => array(
				'cardBg'    => $card_bg,
				'nameColor' => $name_color,
				'descColor' => $desc_color,
			),
			'i18n'             => array(
				'loading'     => __( 'Loading groups...', 'wbcom-essential' ),
				'empty'       => __( 'No groups found.', 'wbcom-essential' ),
				'members'     => __( 'Members', 'wbcom-essential' ),
				'activeAgo'   => __( 'Active %s', 'wbcom-essential' ),
				'joinGroup'   => __( 'Join Group', 'wbcom-essential' ),
				'joined'      => __( 'Joined', 'wbcom-essential' ),
				'pending'     => __( 'Pending', 'wbcom-essential' ),
			),
		);
		?>
		<div class="wbe-groups-grid__list"
			data-wbe-gg-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
			aria-label="<?php esc_attr_e( 'Groups Grid', 'wbcom-essential' ); ?>">
			<p class="wbe-groups-grid__loading"><?php esc_html_e( 'Loading groups...', 'wbcom-essential' ); ?></p>
		</div>

	<?php endif; ?>
</div>
