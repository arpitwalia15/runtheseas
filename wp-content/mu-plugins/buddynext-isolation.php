<?php
/**
 * Plugin Name: BuddyNext Isolation
 * Description: Strips non-essential plugins on BuddyNext front-end routes to save 20-40 MB per request.
 * Version:     1.1.5
 * Author:      Wbcom Designs
 *
 * @package BuddyNext
 */

defined( 'ABSPATH' ) || exit;

// No-op on admin pages and WP-CLI runs — isolation is for front-end only.
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

/**
 * Determine whether the current HTTP request targets a BuddyNext front-end route.
 *
 * Reads the autoloaded buddynext_slug_* options via the options API, so the
 * lookup is served from the single alloptions cache WordPress loads each request
 * (no extra query) and from the object cache on Redis/Memcached sites. A static
 * guard ensures the work runs at most once per request.
 *
 * @return bool
 */
function buddynext_mu_is_bn_request() {
	static $result = null;

	if ( null !== $result ) {
		return $result;
	}

	// Parse the bare path from REQUEST_URI and strip the leading slash.
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- raw comparison only, never output.
	$path        = ltrim( strtok( $request_uri, '?' ), '/' );

	if ( '' === $path ) {
		$result = false;
		return false;
	}

	// Read the six hub slugs via the options API. They are autoloaded, so this
	// is served from the single alloptions cache WordPress already loads each
	// request (no extra query) and from the object cache on Redis/Memcached
	// sites. The option_active_plugins filter below is registered only AFTER
	// this function returns, so reading options here cannot recurse into it.
	$slug_defaults = array(
		'buddynext_slug_activity'      => 'activity',
		'buddynext_slug_people'        => 'members',
		'buddynext_slug_spaces'        => 'spaces',
		'buddynext_slug_messages'      => 'messages',
		'buddynext_slug_notifications' => 'notifications',
		'buddynext_slug_auth'          => 'login',
	);

	foreach ( $slug_defaults as $option_name => $default_slug ) {
		$slug = trim( (string) get_option( $option_name, $default_slug ) );
		if ( '' === $slug ) {
			$slug = $default_slug;
		}

		// Match the first path segment exactly — not a bare prefix — so a page
		// like /membership/ is not isolated by the 'members' slug.
		if ( $path === $slug || 0 === strpos( $path, $slug . '/' ) ) {
			$result = true;
			return true;
		}
	}

	$result = false;
	return false;
}

if ( buddynext_mu_is_bn_request() ) {
	/**
	 * Strip all non-whitelisted plugins before WordPress loads them.
	 *
	 * The whitelist is filterable so site owners can add cache or security
	 * plugins that must remain active on every request.
	 *
	 * @param string[]|mixed $plugins List of active plugin paths from wp_options.
	 * @return string[] Filtered list.
	 */
	add_filter(
		'option_active_plugins',
		static function ( $plugins ) {
			if ( ! is_array( $plugins ) ) {
				return $plugins;
			}

			// Self-guard: if BuddyNext itself is not active, do nothing. A
			// mu-plugin left behind after BuddyNext is deactivated must never
			// strip plugins on /members/, /spaces/ etc. (it matches those paths
			// by slug and cannot tell BuddyNext is gone) — that would break the
			// site. With BuddyNext inactive the mu-plugin is inert.
			if ( ! in_array( 'buddynext/buddynext.php', $plugins, true ) ) {
				return $plugins;
			}

			// Essentials that must ALWAYS survive — BuddyNext + Pro, operational
			// plugins, AND the full in-house integration family. The family is
			// hard-coded here (not left to the option below) so Portfolio tabs / nav
			// appear even on the very first request, or when a tester's mu-plugin
			// file is stale, or before PluginIsolation has populated the option.
			// Keep in sync with PluginIsolation::CORE_INTEGRATIONS + the Pro
			// buddynext_isolation_plugins filter.
			// Rendered from PluginIsolation::essentials() when this file is written
			// - see the placeholder substitution in the generator. This used to be a
			// second hand-written copy of that list, kept in step by a comment
			// reading "keep in sync"; a comment is not a sync mechanism. The literal
			// array is still needed because a mu-plugin runs before plugins load and
			// cannot call into the class - but it is now derived, not maintained.
			$essentials = array (
  0 => 'buddynext/buddynext.php',
  1 => 'buddynext-pro/buddynext-pro.php',
  2 => 'wpmediaverse/wpmediaverse.php',
  3 => 'wpmediaverse-pro/wpmediaverse-pro.php',
  4 => 'jetonomy/jetonomy.php',
  5 => 'jetonomy-pro/jetonomy-pro.php',
  6 => 'wb-gamification/wb-gamification.php',
  7 => 'buddypress-member-blog/buddypress-member-blog.php',
  8 => 'buddypress-member-blog-pro/class-buddypress-member-blog-pro.php',
  9 => 'wp-career-board/wp-career-board.php',
  10 => 'wp-career-board-pro/wp-career-board-pro.php',
  11 => 'wb-listora/wb-listora.php',
  12 => 'wb-listora-pro/wb-listora-pro.php',
  13 => 'learnomy/learnomy.php',
  14 => 'learnomy-pro/learnomy-pro.php',
  15 => 'eventonomy/eventonomy.php',
  16 => 'eventonomy-pro/eventonomy-pro.php',
  17 => 'redis-cache/redis-cache.php',
  18 => 'query-monitor/query-monitor.php',
  19 => 'loco-translate/loco.php',
  20 => 'polylang/polylang.php',
  21 => 'polylang-pro/polylang.php',
  22 => 'sitepress-multilingual-cms/sitepress.php',
  23 => 'wpml-string-translation/plugin.php',
  24 => 'translatepress-multilingual/index.php',
  25 => 'say-what/say-what.php',
  26 => 'wpconsent-cookies-banner-privacy-suite/wpconsent.php',
  27 => 'complianz-gdpr/complianz-gpdr.php',
  28 => 'complianz-gdpr-premium/complianz-gpdr-premium.php',
  29 => 'cookie-law-info/cookie-law-info.php',
  30 => 'webtoffee-gdpr-cookie-consent/webtoffee-gdpr-cookie-consent.php',
  31 => 'gdpr-cookie-consent/gdpr-cookie-consent.php',
  32 => 'borlabs-cookie/borlabs-cookie.php',
  33 => 'real-cookie-banner/index.php',
  34 => 'cookiebot/cookiebot.php',
  35 => 'uk-cookie-consent/uk-cookie-consent.php',
  36 => 'iubenda-cookie-law-solution/iubenda_cookie_solution.php',
);

			// Plus any dynamic / 3rd-party additions BuddyNext mirrors into the
			// `buddynext_isolation_plugins` option. Read via the options API so it
			// rides the object cache (Redis/Memcached) instead of a raw query. The
			// hard-coded family above is the floor; this merge only adds extras a
			// filter contributed at runtime.
			$stored       = get_option( 'buddynext_isolation_plugins', '' );
			$integrations = is_string( $stored ) ? json_decode( $stored, true ) : $stored;
			if ( ! is_array( $integrations ) ) {
				$integrations = array();
			}

			$whitelist = apply_filters(
				'buddynext_isolation_whitelist',
				array_values( array_unique( array_merge( $essentials, $integrations ) ) )
			);

			return array_values( array_intersect( $plugins, $whitelist ) );
		}
	);
}