<?php
/**
 * Default login-form protection for the EDD Account Dashboard block.
 *
 * Two defenses that run against ANY POST to wp-login.php (so they still
 * work when our block form and core's form coexist):
 *
 * 1. Honeypot — if the hidden `wbcom_edd_hp_url` field has any value, the
 *    attempt is treated as bot traffic and rejected silently with the
 *    generic "incorrect username or password" message.
 *
 * 2. Rate limit — track failed attempts per IP in a 15-minute window. After
 *    WBCOM_ESSENTIAL_LOGIN_MAX_ATTEMPTS failures the IP is locked out for
 *    WBCOM_ESSENTIAL_LOGIN_LOCKOUT_MINUTES minutes. Works even if no
 *    dedicated security plugin is installed; defers to any plugin that
 *    blocks earlier in the `authenticate` filter chain.
 *
 * Both protections can be disabled via the
 * `wbcom_essential_login_security_enabled` filter so site owners who
 * install a heavier plugin (Wordfence, LLAR, Solid Security) can turn
 * ours off without touching code.
 *
 * @package Wbcom_Essential
 * @since   4.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maximum failed login attempts allowed per IP per window before lockout.
 */
if ( ! defined( 'WBCOM_ESSENTIAL_LOGIN_MAX_ATTEMPTS' ) ) {
	define( 'WBCOM_ESSENTIAL_LOGIN_MAX_ATTEMPTS', 5 );
}

/**
 * Lockout duration in minutes after max attempts are exceeded.
 */
if ( ! defined( 'WBCOM_ESSENTIAL_LOGIN_LOCKOUT_MINUTES' ) ) {
	define( 'WBCOM_ESSENTIAL_LOGIN_LOCKOUT_MINUTES', 15 );
}

/**
 * Whether the built-in login protection is active.
 *
 * @return bool
 */
function wbcom_essential_login_security_enabled() {
	return (bool) apply_filters( 'wbcom_essential_login_security_enabled', true );
}

/**
 * Best-effort client IP — respects common proxy headers when the site sits
 * behind Cloudflare / a load balancer. Never trust without that trusted
 * proxy context, so the filter below lets admins override the resolution.
 *
 * @return string
 */
function wbcom_essential_login_security_get_ip() {
	$candidates = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' );
	$ip         = '';

	foreach ( $candidates as $key ) {
		if ( empty( $_SERVER[ $key ] ) ) {
			continue;
		}
		$raw   = is_string( $_SERVER[ $key ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) : '';
		$first = trim( strtok( $raw, ',' ) );
		$valid = filter_var( $first, FILTER_VALIDATE_IP );
		if ( $valid ) {
			$ip = $valid;
			break;
		}
	}

	/**
	 * Allow sites to plug in their own IP-resolution logic (CDN quirks,
	 * VPN scenarios, etc.).
	 *
	 * @param string $ip Best-effort IP we derived.
	 */
	return (string) apply_filters( 'wbcom_essential_login_security_client_ip', $ip );
}

/**
 * Transient key for the per-IP attempt counter.
 *
 * @param string $ip Client IP.
 * @return string
 */
function wbcom_essential_login_security_key( $ip ) {
	return 'wbcom_ee_login_' . md5( $ip );
}

/**
 * Read the current attempt state for an IP.
 *
 * @param string $ip Client IP.
 * @return array{count:int,locked_until:int}
 */
function wbcom_essential_login_security_state( $ip ) {
	$raw = get_transient( wbcom_essential_login_security_key( $ip ) );
	if ( ! is_array( $raw ) ) {
		return array(
			'count'        => 0,
			'locked_until' => 0,
		);
	}

	return array(
		'count'        => isset( $raw['count'] ) ? (int) $raw['count'] : 0,
		'locked_until' => isset( $raw['locked_until'] ) ? (int) $raw['locked_until'] : 0,
	);
}

/**
 * Short-circuit authentication when the honeypot is filled or the IP is
 * currently locked out. Runs before wp_authenticate_username_password so a
 * blocked attempt never reaches the hashed-password comparison.
 *
 * @param null|WP_User|WP_Error $user      Authenticated user or error.
 * @param string                $username  Submitted username.
 * @return null|WP_User|WP_Error
 */
function wbcom_essential_login_security_gate( $user, $username ) {
	if ( ! wbcom_essential_login_security_enabled() ) {
		return $user;
	}

	// Honeypot check — posted alongside a real submit, empty for humans.
	if ( ! empty( $_POST['wbcom_edd_hp_url'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Honeypot read only; wp-login.php owns the CSRF model.
		return new WP_Error( 'wbcom_essential_honeypot', __( 'Login attempt rejected.', 'wbcom-essential' ) );
	}

	// Don't gate empty submissions — core will produce its "empty field" error instead.
	if ( empty( $username ) ) {
		return $user;
	}

	$ip = wbcom_essential_login_security_get_ip();
	if ( '' === $ip ) {
		return $user;
	}

	$state = wbcom_essential_login_security_state( $ip );
	if ( $state['locked_until'] > time() ) {
		$minutes = max( 1, (int) ceil( ( $state['locked_until'] - time() ) / MINUTE_IN_SECONDS ) );
		return new WP_Error(
			'wbcom_essential_rate_limited',
			sprintf(
				/* translators: %d: minutes remaining in the lockout. */
				_n(
					'Too many login attempts. Try again in %d minute.',
					'Too many login attempts. Try again in %d minutes.',
					$minutes,
					'wbcom-essential'
				),
				$minutes
			)
		);
	}

	return $user;
}
add_filter( 'authenticate', 'wbcom_essential_login_security_gate', 20, 2 );

/**
 * Record a failed attempt and apply a lockout once the threshold is hit.
 *
 * @param string                  $username Submitted username.
 * @param WP_Error|\stdClass|null $error    Core error object, if present.
 * @return void
 */
function wbcom_essential_login_security_record_failure( $username, $error = null ) {
	unset( $error ); // Unused; signature matches wp_login_failed.

	if ( ! wbcom_essential_login_security_enabled() ) {
		return;
	}

	if ( empty( $username ) ) {
		return;
	}

	$ip = wbcom_essential_login_security_get_ip();
	if ( '' === $ip ) {
		return;
	}

	$state = wbcom_essential_login_security_state( $ip );
	++$state['count'];

	$window_seconds = WBCOM_ESSENTIAL_LOGIN_LOCKOUT_MINUTES * MINUTE_IN_SECONDS;

	if ( $state['count'] >= WBCOM_ESSENTIAL_LOGIN_MAX_ATTEMPTS ) {
		$state['locked_until'] = time() + $window_seconds;
	}

	set_transient( wbcom_essential_login_security_key( $ip ), $state, $window_seconds );
}
add_action( 'wp_login_failed', 'wbcom_essential_login_security_record_failure', 10, 2 );

/**
 * Clear the per-IP counter on a successful login.
 *
 * @param string $user_login Sanitized username that just signed in.
 * @return void
 */
function wbcom_essential_login_security_on_success( $user_login ) {
	unset( $user_login );

	if ( ! wbcom_essential_login_security_enabled() ) {
		return;
	}

	$ip = wbcom_essential_login_security_get_ip();
	if ( '' === $ip ) {
		return;
	}

	delete_transient( wbcom_essential_login_security_key( $ip ) );
}
add_action( 'wp_login', 'wbcom_essential_login_security_on_success', 10, 1 );
