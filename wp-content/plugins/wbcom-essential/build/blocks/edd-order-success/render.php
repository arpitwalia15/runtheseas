<?php
/**
 * EDD Order Success Block - Server-Side Render (v2).
 *
 * @package wbcom-essential
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Bail early if EDD is not active.
if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
	echo '<p>' . esc_html__( 'Easy Digital Downloads is required for this block.', 'wbcom-essential' ) . '</p>';
	return;
}

// Shared infrastructure: unique ID + CSS output + visibility classes.
$unique_id   = ! empty( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
$vis_classes = \WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::get_visibility_classes( $attributes );
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::add( $unique_id, $attributes );

// Extract block-specific attributes with defaults.
$show_success_header = isset( $attributes['showSuccessHeader'] ) ? (bool) $attributes['showSuccessHeader'] : true;
$success_message     = ! empty( $attributes['successMessage'] ) ? $attributes['successMessage'] : __( 'Thank you for your purchase!', 'wbcom-essential' );
$show_next_steps     = isset( $attributes['showNextSteps'] ) ? (bool) $attributes['showNextSteps'] : true;
$account_page_url    = ! empty( $attributes['accountPageUrl'] ) ? $attributes['accountPageUrl'] : '';

// Resolve account page URL: attribute > page with account dashboard block > EDD settings > home.
if ( empty( $account_page_url ) ) {
	// First, find a page containing our EDD Account Dashboard block.
	$account_pages = get_posts(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			's'           => 'wbcom-essential/edd-account-dashboard',
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( ! empty( $account_pages ) ) {
		$account_page_url = get_permalink( $account_pages[0] );
	}

	// Fall back to EDD's purchase history page.
	if ( empty( $account_page_url ) ) {
		$purchase_history_page = edd_get_option( 'purchase_history_page', 0 );
		if ( $purchase_history_page ) {
			$account_page_url = get_permalink( absint( $purchase_history_page ) );
		}
	}

	if ( empty( $account_page_url ) ) {
		$account_page_url = home_url( '/' );
	}
}

$account_page_url = esc_url( $account_page_url );

// Unique ID for animation scoping.
$unique_block_id = ! empty( $unique_id ) ? 'wbcom-edd-success-' . $unique_id : wp_unique_id( 'wbcom-edd-success-' );

// Build wrapper attributes.
$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => trim( 'wbe-block-' . esc_attr( $unique_id ) . ' wbcom-edd-success ' . $vis_classes ),
		'id'    => esc_attr( $unique_block_id ),
	)
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes() ?>>

	<div class="wbcom-edd-success__topbar">
		<a class="wbcom-edd-success__back-link" href="<?php echo esc_url( $account_page_url ); ?>">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
			<span><?php esc_html_e( 'Back to Account', 'wbcom-essential' ); ?></span>
		</a>
	</div>

	<?php if ( $show_success_header ) : ?>
		<div class="wbcom-edd-success__header">
			<div class="wbcom-edd-success__checkmark" aria-hidden="true">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 52 52"
					width="48"
					height="48"
					fill="none"
					stroke="#16a34a"
					stroke-width="3"
					stroke-linecap="round"
					stroke-linejoin="round"
					role="img"
					aria-label="<?php esc_attr_e( 'Order confirmed', 'wbcom-essential' ); ?>"
				>
					<polyline points="14 27 22 35 38 17" />
				</svg>
			</div>

			<h2 class="wbcom-edd-success__title">
				<?php echo esc_html( $success_message ); ?>
			</h2>

			<p class="wbcom-edd-success__subtitle">
				<?php esc_html_e( 'Your order has been confirmed and is being processed.', 'wbcom-essential' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<div class="wbcom-edd-success__receipt">
		<?php echo do_shortcode( '[edd_receipt]' ); ?>
	</div>

	<?php if ( $show_next_steps ) : ?>
		<div class="wbcom-edd-success__next-steps">
			<h3 class="wbcom-edd-success__next-steps-title">
				<?php esc_html_e( "What's Next?", 'wbcom-essential' ); ?>
			</h3>

			<div class="wbcom-edd-success__cards">

				<?php /* Downloads card - always shown */ ?>
				<div class="wbcom-edd-success__card">
					<div class="wbcom-edd-success__card-icon" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
							<polyline points="7 10 12 15 17 10"/>
							<line x1="12" y1="15" x2="12" y2="3"/>
						</svg>
					</div>
					<h4 class="wbcom-edd-success__card-title">
						<?php esc_html_e( 'Download Your Files', 'wbcom-essential' ); ?>
					</h4>
					<p class="wbcom-edd-success__card-description">
						<?php esc_html_e( 'Access and download the files included in your purchase.', 'wbcom-essential' ); ?>
					</p>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'downloads', $account_page_url ) ); ?>" class="wbcom-edd-success__card-link">
						<?php esc_html_e( 'View Downloads', 'wbcom-essential' ); ?>
					</a>
				</div>

				<?php /* License Keys card - only when EDD Software Licensing is active */ ?>
				<?php if ( class_exists( 'EDD_Software_Licensing' ) ) : ?>
					<div class="wbcom-edd-success__card">
						<div class="wbcom-edd-success__card-icon" aria-hidden="true">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
							</svg>
						</div>
						<h4 class="wbcom-edd-success__card-title">
							<?php esc_html_e( 'View License Keys', 'wbcom-essential' ); ?>
						</h4>
						<p class="wbcom-edd-success__card-description">
							<?php esc_html_e( 'Find and manage the license keys for your purchased products.', 'wbcom-essential' ); ?>
						</p>
						<a href="<?php echo esc_url( add_query_arg( 'tab', 'licenses', $account_page_url ) ); ?>" class="wbcom-edd-success__card-link">
							<?php esc_html_e( 'View Licenses', 'wbcom-essential' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<?php /* Manage Account card - always shown */ ?>
				<div class="wbcom-edd-success__card">
					<div class="wbcom-edd-success__card-icon" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
							<circle cx="12" cy="7" r="4"/>
						</svg>
					</div>
					<h4 class="wbcom-edd-success__card-title">
						<?php esc_html_e( 'Manage Your Account', 'wbcom-essential' ); ?>
					</h4>
					<p class="wbcom-edd-success__card-description">
						<?php esc_html_e( 'View your purchase history, update details, and manage your profile.', 'wbcom-essential' ); ?>
					</p>
					<a href="<?php echo esc_url( $account_page_url ); ?>" class="wbcom-edd-success__card-link">
						<?php esc_html_e( 'Go to Account', 'wbcom-essential' ); ?>
					</a>
				</div>

			</div>
		</div>
	<?php endif; ?>

</div>
