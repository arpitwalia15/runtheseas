<?php
/**
 * Server-side render for EDD Account Dashboard block (v2).
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

// Guard: EDD must be active.
if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
	?>
	<div class="wbcom-edd-account-notice">
		<p><?php esc_html_e( 'Easy Digital Downloads is required to display this block.', 'wbcom-essential' ); ?></p>
	</div>
	<?php
	return;
}

// Shared infrastructure: unique ID + CSS output + visibility classes.
$unique_id   = ! empty( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
$vis_classes = \WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::get_visibility_classes( $attributes );
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::add( $unique_id, $attributes );

// Extract existing block attributes.
$show_support  = isset( $attributes['showSupport'] ) ? (bool) $attributes['showSupport'] : true;
$support_url   = isset( $attributes['supportUrl'] ) ? esc_url( $attributes['supportUrl'] ) : '';
$support_label = isset( $attributes['supportLabel'] ) ? $attributes['supportLabel'] : __( 'My Tickets', 'wbcom-essential' );
$default_tab   = isset( $attributes['defaultTab'] ) ? $attributes['defaultTab'] : 'dashboard';

$valid_tabs = array( 'dashboard', 'subscriptions', 'downloads', 'licenses', 'purchases', 'profile' );
if ( ! in_array( $default_tab, $valid_tabs, true ) ) {
	$default_tab = 'dashboard';
}

// Guest: show login form with redirect back to this page.
if ( ! is_user_logged_in() ) {
	$redirect_url       = esc_url( get_permalink() );
	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class' => trim( 'wbe-block-' . esc_attr( $unique_id ) . ' wbcom-edd-account wbcom-edd-account--guest ' . $vis_classes ),
		)
	);
	?>
	<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes() ?>>
		<div class="wbcom-edd-account__login-card">
			<div class="wbcom-edd-account__login-icon">
				<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
					<circle cx="12" cy="7" r="4"/>
				</svg>
			</div>
			<h2 class="wbcom-edd-account__login-title"><?php esc_html_e( 'Sign In to Your Account', 'wbcom-essential' ); ?></h2>
			<p class="wbcom-edd-account__login-description"><?php esc_html_e( 'Please log in to access your purchases, downloads, and account settings.', 'wbcom-essential' ); ?></p>
			<?php
			// Use the filtered login-post URL so WPS Hide Login (and any plugin
			// hooking site_url for the 'login_post' context) routes submits to
			// the renamed endpoint instead of the now-404 /wp-login.php.
			$login_post_url = site_url( 'wp-login.php', 'login_post' );
			?>
			<form name="loginform" id="loginform" class="wbcom-edd-login-form" action="<?php echo esc_url( $login_post_url ); ?>" method="post" autocomplete="on">
				<?php
				/**
				 * Third-party hook: Limit Login Attempts, Wordfence, reCAPTCHA,
				 * hCaptcha, Turnstile etc. inject their hidden fields or widgets
				 * here just like they do on /wp-login.php.
				 */
				do_action( 'login_form' );
				?>
				<p class="login-username">
					<label for="user_login"><?php esc_html_e( 'Username or Email Address', 'wbcom-essential' ); ?></label>
					<input type="text" name="log" id="user_login" class="input" value="" size="20" autocomplete="username" required />
				</p>
				<p class="login-password">
					<label for="user_pass"><?php esc_html_e( 'Password', 'wbcom-essential' ); ?></label>
					<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" autocomplete="current-password" required />
				</p>
				<?php
				// Honeypot: off-screen, tabindex=-1, autocomplete off. Humans never
				// see/fill it; most bots fill every field. Server-side check in
				// includes/edd-account-login-security.php rejects any non-empty value.
				?>
				<p class="wbcom-edd-login-form__hp" aria-hidden="true">
					<label for="wbcom_edd_hp_url"><?php esc_html_e( 'Website', 'wbcom-essential' ); ?></label>
					<input type="text" name="wbcom_edd_hp_url" id="wbcom_edd_hp_url" tabindex="-1" autocomplete="off" value="" />
				</p>
				<p class="login-remember">
					<label>
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" />
						<?php esc_html_e( 'Remember Me', 'wbcom-essential' ); ?>
					</label>
				</p>
				<p class="login-submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="<?php esc_attr_e( 'Log In', 'wbcom-essential' ); ?>" />
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>" />
				</p>
			</form>
		</div>
	</div>
	<?php
	return;
}

// Determine active tab from query param, fallback to block attribute.
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $default_tab;
if ( ! in_array( $active_tab, $valid_tabs, true ) ) {
	$active_tab = $default_tab;
}

// Unique identifier for CSS scoping (use block's uniqueId, fallback to wp_unique_id).
$block_id = ! empty( $unique_id ) ? 'wbcom-edd-account-' . $unique_id : wp_unique_id( 'wbcom-edd-account-' );

// REST URL and nonce for frontend JS tab switching.
$rest_url = rest_url( 'wbcom/v1/edd-account/' );
$nonce    = wp_create_nonce( 'wp_rest' );

// Build tabs list, conditionally including add-on tabs.
$tabs = array();

$tabs['dashboard'] = array(
	'label' => __( 'Dashboard', 'wbcom-essential' ),
	'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>',
);

if ( class_exists( 'EDD_Recurring' ) ) {
	$tabs['subscriptions'] = array(
		'label' => __( 'Subscriptions', 'wbcom-essential' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
	);
}

$tabs['downloads'] = array(
	'label' => __( 'Downloads', 'wbcom-essential' ),
	'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
);

if ( function_exists( 'edd_software_licensing' ) ) {
	$tabs['licenses'] = array(
		'label' => __( 'Licenses', 'wbcom-essential' ),
		'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
	);
}

$tabs['purchases'] = array(
	'label' => __( 'Order History', 'wbcom-essential' ),
	'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
);

$tabs['profile'] = array(
	'label' => __( 'Edit Profile', 'wbcom-essential' ),
	'icon'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
);

// Wrapper attributes with data- attributes for JS.
$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'           => trim( 'wbe-block-' . esc_attr( $unique_id ) . ' wbcom-edd-account ' . $vis_classes ),
		'id'              => esc_attr( $block_id ),
		'data-rest-url'   => esc_attr( $rest_url ),
		'data-nonce'      => esc_attr( $nonce ),
		'data-active-tab' => esc_attr( $active_tab ),
	)
);

// Fetch the EDD customer once and pass to every tab render — otherwise
// the initial server-side render shows the empty state because the tab
// functions default $customer to false. The REST AJAX callback already
// does this; the first page load must match.
$current_user = wp_get_current_user();
$customer     = ( $current_user && $current_user->ID ) ? edd_get_customer_by( 'user_id', $current_user->ID ) : false;

// Render active tab content server-side.
ob_start();
switch ( $active_tab ) {
	case 'dashboard':
		wbcom_essential_edd_render_dashboard_tab( $customer );
		break;
	case 'subscriptions':
		wbcom_essential_edd_render_subscriptions_tab( $customer );
		break;
	case 'downloads':
		wbcom_essential_edd_render_downloads_tab( $customer );
		break;
	case 'licenses':
		wbcom_essential_edd_render_licenses_tab( $customer );
		break;
	case 'purchases':
		wbcom_essential_edd_render_purchases_tab( $customer );
		break;
	case 'profile':
		wbcom_essential_edd_render_profile_tab();
		break;
	default:
		wbcom_essential_edd_render_dashboard_tab( $customer );
		break;
}
$initial_content = ob_get_clean();
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes() ?>>

	<nav class="wbcom-edd-account__sidebar" aria-label="<?php esc_attr_e( 'Account navigation', 'wbcom-essential' ); ?>">
		<ul class="wbcom-edd-account__nav" role="list">
			<?php foreach ( $tabs as $tab_key => $tab_data ) : ?>
			<li class="wbcom-edd-account__nav-item">
				<a
					href="<?php echo esc_url( add_query_arg( 'tab', $tab_key ) ); ?>"
					class="wbcom-edd-account__nav-link<?php echo $active_tab === $tab_key ? ' is-active' : ''; ?>"
					data-tab="<?php echo esc_attr( $tab_key ); ?>"
					aria-current="<?php echo $active_tab === $tab_key ? 'page' : 'false'; ?>"
				>
					<span class="wbcom-edd-account__nav-icon"><?php echo $tab_data['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG is hardcoded, not user input ?></span>
					<span class="wbcom-edd-account__nav-label"><?php echo esc_html( $tab_data['label'] ); ?></span>
				</a>
			</li>
			<?php endforeach; ?>

			<?php if ( $show_support && $support_url ) : ?>
			<li class="wbcom-edd-account__nav-item">
				<a
					href="<?php echo esc_url( $support_url ); ?>"
					class="wbcom-edd-account__nav-link"
					target="_blank"
					rel="noopener noreferrer"
				>
					<span class="wbcom-edd-account__nav-icon">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
					</span>
					<span class="wbcom-edd-account__nav-label"><?php echo esc_html( $support_label ); ?></span>
					<span class="wbcom-edd-account__nav-external" aria-hidden="true">
						<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
					</span>
				</a>
			</li>
			<?php endif; ?>

			<li class="wbcom-edd-account__nav-item wbcom-edd-account__nav-item--logout">
				<a
					href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>"
					class="wbcom-edd-account__nav-link wbcom-edd-account__nav-link--logout"
				>
					<span class="wbcom-edd-account__nav-icon">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
					</span>
					<span class="wbcom-edd-account__nav-label"><?php esc_html_e( 'Sign Out', 'wbcom-essential' ); ?></span>
				</a>
			</li>
		</ul>
	</nav>

	<main class="wbcom-edd-account__content" aria-label="<?php esc_attr_e( 'Account content', 'wbcom-essential' ); ?>">
		<div class="wbcom-edd-account__tab-content" data-tab-content="<?php echo esc_attr( $active_tab ); ?>" aria-live="polite" aria-atomic="false">
			<div class="wbcom-edd-account__loading" aria-hidden="true" role="status" hidden>
				<span class="wbcom-edd-account__spinner"></span>
			</div>
			<div class="wbcom-edd-account__inner">
				<?php echo wbcom_essential_kses_form( $initial_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized by wbcom_essential_kses_form() ?>
			</div>
		</div>
	</main>

</div>
