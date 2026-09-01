<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * RTS_Production — the pieces that turn the prototype into a deployable plugin:
 *   - Emergency Take-Offline (spec's non-negotiable control) — real holding page, any admin, <10s
 *   - Email delivery via wp_mail() with a 'log' mode (outbox table) and a 'send' mode (real wp_mail)
 *   - WP-Cron schedules: campaign triggers, scheduled reports, action-item generation, FR sync
 *   - AI (LLM) integration point for Q&R drafts and broadcast drafts — wp_remote_post, key-gated
 *   - External Founding Runner import (CSV / REST) + email-match sync
 *   - Forms-plugin adapter: one function + one action hook any forms plugin can call
 *   - Rate limiting on the public participant routes (per-IP, transient-based)
 *   - Settings (options) page under Run The Seas -> Settings (rts_system)
 *
 * Nothing here fakes an external service. Where a real service is required (SMTP provider, LLM key,
 * the live main-site export) the code is complete and the connection point is a single setting.
 */
class RTS_Production {

	// =========================================================================================
	//  SETTINGS
	// =========================================================================================
	const OPT = 'rts_settings';
	public static function defaults() {
		return array(
			'email_mode'          => 'log',    // log | send
			'email_from_name'     => 'Run The Seas',
			'email_from_address'  => 'info@runtheseas.com',
			'ai_provider'         => 'anthropic',
			'ai_api_key'          => '',
			'ai_model'            => 'claude-sonnet-4-6',
			'rate_limit_register' => 100,      // per IP per hour
			'rate_limit_verify'   => 60,       // per IP per hour
			'offline_message'     => "We'll be right back. Run The Seas is temporarily offline for maintenance.",
			'admin_notify_email'  => '',       // cron failure / take-offline alerts; blank = site admin_email
		);
	}
	public static function get( $k ) { $o = get_option( self::OPT, array() ); return array_key_exists( $k, $o ) ? $o[ $k ] : ( self::defaults()[ $k ] ?? null ); }
	public static function set( $k, $v ) { $o = get_option( self::OPT, array() ); $o[ $k ] = $v; update_option( self::OPT, $o ); }
	private static function audit( $u, $a, $m, $n = '' ) { RTS_Business_Logic::log_audit( $u ?: 'system', $a, $m, 'success', $n ); }
	private static function who() { $u = wp_get_current_user(); return ( $u && $u->ID ) ? $u->user_login : 'system'; }

	public static function init() {
		// Take-offline
		add_action( 'template_redirect', array( __CLASS__, 'maybe_serve_offline' ), 1 );
		add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar_toggle' ), 100 );
		add_action( 'admin_notices', array( __CLASS__, 'offline_admin_notice' ) );
		add_action( 'admin_post_rts_offline', array( __CLASS__, 'handle_offline' ) );
		add_action( 'admin_post_rts_settings', array( __CLASS__, 'handle_settings' ) );
		add_action( 'admin_post_rts_fr_import', array( __CLASS__, 'handle_fr_import' ) );
		add_action( 'admin_post_rts_fr_sync', array( __CLASS__, 'handle_fr_sync' ) );
		add_action( 'admin_menu', array( __CLASS__, 'menu' ), 80 );
		// Cron
		add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
		add_action( 'rts_cron_campaign_triggers', array( __CLASS__, 'cron_campaign_triggers' ) );
		add_action( 'rts_run_scheduled_campaign', array( __CLASS__, 'run_scheduled_campaign' ) );
		add_action( 'rts_cron_scheduled_reports', array( __CLASS__, 'cron_scheduled_reports' ) );
		add_action( 'rts_cron_action_items', array( __CLASS__, 'cron_action_items' ) );
		add_action( 'rts_cron_fr_sync', array( __CLASS__, 'cron_fr_sync' ) );
		// Email from-header
		add_filter( 'wp_mail_from', fn( $f ) => is_email( self::get( 'email_from_address' ) ) ? self::get( 'email_from_address' ) : $f );
		add_filter( 'wp_mail_from_name', fn( $n ) => self::get( 'email_from_name' ) ?: $n );
	}

	public static function schedule_cron() {
		// The custom 15-minute recurrence must be registered BEFORE scheduling, or wp_schedule_event()
		// rejects it as an unknown schedule (this is called from the activation hook, before init()).
		add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
		foreach ( array( 'rts_cron_campaign_triggers' => 'hourly', 'rts_cron_scheduled_reports' => 'daily', 'rts_cron_action_items' => 'daily', 'rts_cron_fr_sync' => 'rts_every_15_minutes' ) as $hook => $rec ) {
			if ( ! wp_next_scheduled( $hook ) ) { wp_schedule_event( time() + 60, $rec, $hook ); }
		}
	}
	public static function unschedule_cron() { foreach ( array( 'rts_cron_campaign_triggers', 'rts_run_scheduled_campaign', 'rts_cron_scheduled_reports', 'rts_cron_action_items', 'rts_cron_fr_sync' ) as $h ) { wp_clear_scheduled_hook( $h ); } }
	public static function cron_schedules( $s ) { $s['rts_every_15_minutes'] = array( 'interval' => 900, 'display' => 'Every 15 minutes (RTS)' ); return $s; }

	// =========================================================================================
	//  EMERGENCY TAKE-OFFLINE  (spec: any admin, from any screen, under 10 seconds, no hosting panel)
	// =========================================================================================
	public static function is_offline() { return (bool) get_option( 'rts_site_offline', false ); }

	public static function take_offline( $by, $message = null ) {
		update_option( 'rts_site_offline', 1 );
		update_option( 'rts_site_offline_at', current_time( 'mysql' ) );
		update_option( 'rts_site_offline_by', $by ?: self::who() );
		if ( null !== $message ) { self::set( 'offline_message', wp_kses_post( $message ) ); }
		self::audit( $by, '⛔ EMERGENCY TAKE-OFFLINE activated', 'System', 'public site now serving holding page' );
		self::notify_admins( '⛔ Run The Seas site taken OFFLINE', "The public site was taken offline by " . ( $by ?: self::who() ) . " at " . current_time( 'mysql' ) . ".\nRestore from wp-admin → Run The Seas → Backup & System, or POST /rts/v1/system/restore." );
		return array( 'error' => null, 'online' => false );
	}
	public static function restore( $by ) {
		delete_option( 'rts_site_offline' );
		self::audit( $by, 'Site RESTORED online', 'System' );
		self::notify_admins( '✅ Run The Seas site restored', 'The public site is back online (restored by ' . ( $by ?: self::who() ) . ').' );
		return array( 'error' => null, 'online' => true );
	}
	public static function status() {
		return array( 'online' => ! self::is_offline(), 'offline_since' => get_option( 'rts_site_offline_at' ), 'offline_by' => get_option( 'rts_site_offline_by' ), 'message' => self::get( 'offline_message' ) );
	}
	/** Public requests get a 503 holding page while offline. Logged-in users with rts_view still see the site (so admins can verify). wp-admin, login, REST and cron are never blocked. */
	public static function maybe_serve_offline() {
		if ( ! self::is_offline() || is_admin() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) { return; }
		if ( in_array( $GLOBALS['pagenow'] ?? '', array( 'wp-login.php', 'wp-cron.php' ), true ) ) { return; }
		if ( is_user_logged_in() && current_user_can( 'rts_view' ) ) { return; }
		status_header( 503 ); header( 'Retry-After: 3600' ); nocache_headers();
		echo '<!doctype html><html><head><meta charset="utf-8"><title>Run The Seas — temporarily offline</title><meta name="robots" content="noindex"><style>body{font-family:-apple-system,Segoe UI,sans-serif;background:#0B1420;color:#E4C77A;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}div{max-width:560px;padding:40px;text-align:center}h1{font-weight:600;letter-spacing:.5px}p{color:#c9d3e0;font-size:16px;line-height:1.5}</style></head><body><div><h1>⚓ RUN THE SEAS</h1><p>' . wp_kses_post( self::get( 'offline_message' ) ) . '</p></div></body></html>';
		exit;
	}
	public static function admin_bar_toggle( $bar ) {
		if ( ! current_user_can( 'rts_system' ) ) { return; }
		$off = self::is_offline();
		$bar->add_node( array( 'id' => 'rts-offline', 'title' => $off ? '🔴 SITE OFFLINE — click to restore' : '⛔ Take Site Offline', 'href' => admin_url( 'admin.php?page=rts-backup#offline' ), 'meta' => array( 'style' => $off ? 'background:#B23B3B;color:#fff' : '' ) ) );
	}
	public static function offline_admin_notice() { if ( self::is_offline() && current_user_can( 'rts_view' ) ) { echo '<div class="notice notice-error"><p><b>🔴 The public site is OFFLINE</b> (since ' . esc_html( get_option( 'rts_site_offline_at' ) ) . ', by ' . esc_html( get_option( 'rts_site_offline_by' ) ) . '). <a href="' . esc_url( admin_url( 'admin.php?page=rts-backup#offline' ) ) . '">Restore</a></p></div>'; } }
	public static function offline_panel_html() {
		$s = self::status(); $nonce = wp_nonce_field( 'rts_offline', '_rts_nonce', true, false ); $act = esc_url( admin_url( 'admin-post.php' ) );
		$h = '<h3 id="offline">Emergency Take-Offline</h3><p style="max-width:820px;color:#666;font-size:12px">Any Super Administrator, from any screen, can pull the public site behind a holding page in seconds — no developer, no hosting panel (spec requirement, built from a real past incident). wp-admin, login, REST and cron keep working so the team can investigate. Logged-in RTS staff still see the real site.</p>';
		if ( $s['online'] ) {
			$h .= '<form method="post" action="' . $act . '" onsubmit="return (document.getElementById(\'rts_off_confirm\').value===\'OFFLINE\') || (alert(\'Type OFFLINE to confirm.\'),false);"><input type="hidden" name="action" value="rts_offline"><input type="hidden" name="mode" value="offline">' . $nonce
			   . '<p><textarea name="message" style="width:520px;height:60px" placeholder="Holding-page message">' . esc_textarea( $s['message'] ) . '</textarea></p>'
			   . '<p>Type <b>OFFLINE</b> to confirm: <input type="text" id="rts_off_confirm" autocomplete="off" style="width:120px"> <button class="button button-primary" style="background:#B23B3B;border-color:#8a2a2a">⛔ Take site offline now</button></p></form>';
		} else {
			$h .= '<div class="notice notice-error inline" style="padding:10px"><b>🔴 OFFLINE</b> since ' . esc_html( $s['offline_since'] ) . ' by ' . esc_html( $s['offline_by'] ) . '</div><form method="post" action="' . $act . '"><input type="hidden" name="action" value="rts_offline"><input type="hidden" name="mode" value="restore">' . $nonce . '<p><button class="button button-primary">✅ Restore site</button></p></form>';
		}
		return $h;
	}
	public static function handle_offline() {
		if ( ! current_user_can( 'rts_system' ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_offline' ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); }
		if ( 'offline' === ( $_POST['mode'] ?? '' ) ) { self::take_offline( self::who(), sanitize_textarea_field( $_POST['message'] ?? '' ) ); $m = 'Site is now OFFLINE.'; } else { self::restore( self::who() ); $m = 'Site restored.'; }
		wp_safe_redirect( RTSAP_Frontend_Dashboard::screen_url( 'rts-backup', array( 'rts_msg' => rawurlencode( $m ) ) ) . '#offline' ); exit;
	}

	// =========================================================================================
	//  EMAIL — every message goes through send(); mode decides log vs real wp_mail()
	// =========================================================================================
	public static function page_url( $slug ) { $p = get_page_by_path( $slug ); return $p ? get_permalink( $p ) : home_url( '/' ); }
	public static function verify_url( $token )      { return add_query_arg( 'rts_verify', $token, self::page_url( 'survey' ) ); }
	public static function unsubscribe_url( $token ) { return add_query_arg( 'token', $token, self::page_url( 'unsubscribe' ) ); }
	public static function referral_url( $code )     { return add_query_arg( 'ref', $code, self::page_url( 'survey' ) ); }

	/** Merge fields available in every template/body: {name} {first_name} {email} {founding_runner_number} {referral_url} {unsubscribe_url} {verify_url} */
	public static function merge( $text, $p, $extra = array() ) {
		$map = array(
			'{name}' => $p->name ?? '', '{first_name}' => trim( strtok( (string) ( $p->name ?? '' ), ' ' ) ), '{email}' => $p->email ?? '',
			'{founding_runner_number}' => $p->founding_runner_number ?? '', '{referral_url}' => isset( $p->referral_code ) ? self::referral_url( $p->referral_code ) : '',
			'{unsubscribe_url}' => isset( $p->unsubscribe_token ) ? self::unsubscribe_url( $p->unsubscribe_token ) : '', '{verify_url}' => isset( $p->verification_token ) ? self::verify_url( $p->verification_token ) : '',
		) + $extra;
		return strtr( (string) $text, $map );
	}
	/** Marketing email MUST carry an unsubscribe link (CASL / CAN-SPAM); transactional (verification) must not. */
	public static function send( $to, $subject, $body, $kind = 'marketing', $meta = array() ) {
		global $wpdb;
		$mode = self::get( 'email_mode' ); $html = wpautop( $body );
		if ( 'marketing' === $kind && ! empty( $meta['unsubscribe_url'] ) && false === strpos( $body, $meta['unsubscribe_url'] ) ) {
			$html .= '<p style="font-size:12px;color:#777">You are receiving this because you joined Run The Seas. <a href="' . esc_url( $meta['unsubscribe_url'] ) . '">Manage or unsubscribe</a>.</p>';
		}
		$ok = null; $err = null;
		if ( 'send' === $mode ) {
			$ok = wp_mail( $to, $subject, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
			if ( ! $ok ) { $err = 'wp_mail returned false (no mail transport configured, or the provider rejected the message)'; }
		}
		$wpdb->insert( RTS_DB::table( 'email_outbox' ), array( 'to_email' => $to, 'subject' => $subject, 'body_html' => $html, 'kind' => $kind, 'mode' => $mode, 'delivered' => 'send' === $mode ? ( $ok ? 1 : 0 ) : null, 'error' => $err, 'meta' => wp_json_encode( $meta ) ) );
		return array( 'mode' => $mode, 'delivered' => $ok, 'error' => $err, 'outbox_id' => (int) $wpdb->insert_id );
	}
	public static function send_verification( $participant_id ) {
		global $wpdb;
		$p = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'participants' ) . " WHERE id = %d", $participant_id ) );
		if ( ! $p ) { return array( 'error' => 'NOT_FOUND' ); }
		$body = self::merge( "Hi {first_name},\n\nThanks for joining Run The Seas. Please confirm your email to claim your \$100 Founding Runner Cabin Credit:\n\n{verify_url}\n\nIf you didn't sign up, you can ignore this message.", $p );
		return self::send( $p->email, 'Confirm your email — Run The Seas', $body, 'transactional', array( 'participant_id' => $p->id, 'verify_url' => self::verify_url( $p->verification_token ) ) );
	}
	/** Send a subject/body to a list of participant rows. Used by Broadcast (send_bulk), campaigns, and test sends. */
	public static function send_to_participants( $recipients, $subject, $body, $meta = array() ) {
		$n = 0; $d = 0;
		foreach ( $recipients as $r ) {
			$res = self::send( $r->email, self::merge( $subject, $r ), self::merge( $body, $r ), 'marketing', $meta + array( 'participant_id' => $r->id, 'unsubscribe_url' => self::unsubscribe_url( $r->unsubscribe_token ?? '' ) ) );
			$n++; if ( $res['delivered'] ) { $d++; }
		}
		return array( 'attempted' => $n, 'delivered' => $d, 'mode' => self::get( 'email_mode' ) );
	}
	public static function outbox( $limit = 50 ) { global $wpdb; return $wpdb->get_results( $wpdb->prepare( "SELECT id, to_email, subject, kind, mode, delivered, error, created_at FROM " . RTS_DB::table( 'email_outbox' ) . " ORDER BY id DESC LIMIT %d", $limit ) ); }
	private static function notify_admins( $subject, $body ) { $to = self::get( 'admin_notify_email' ) ?: get_option( 'admin_email' ); if ( is_email( $to ) ) { self::send( $to, $subject, $body, 'transactional', array( 'internal' => 1 ) ); } }

	// =========================================================================================
	//  CRON JOBS (WP-Cron; see handoff for the production system-cron line)
	// =========================================================================================
	public static function cron_campaign_triggers() {
		global $wpdb; $n = 0;
		foreach ( $wpdb->get_col( "SELECT id FROM " . RTS_DB::table( 'email_campaigns' ) . " WHERE status = 'active'" ) as $id ) { RTS_Business_Logic_5::run_trigger_check( (int) $id, 'cron' ); $n++; }
		update_option( 'rts_cron_last_campaign_triggers', current_time( 'mysql' ) ); return $n;
	}
	public static function run_scheduled_campaign( $campaign_id ) { return RTS_Business_Logic_5::run_trigger_check( absint( $campaign_id ), 'cron' ); }
	public static function cron_scheduled_reports() {
		global $wpdb; $n = 0;
		foreach ( $wpdb->get_results( "SELECT r.*, (SELECT MAX(run_at) FROM " . RTS_DB::table( 'report_runs' ) . " x WHERE x.report_id = r.id) AS last_run FROM " . RTS_DB::table( 'report_definitions' ) . " r WHERE r.schedule_frequency IN ('daily','weekly','monthly')" ) as $r ) {
			$due = array( 'daily' => 86400, 'weekly' => 7 * 86400, 'monthly' => 30 * 86400 )[ $r->schedule_frequency ];
			if ( ! $r->last_run || ( time() - strtotime( $r->last_run ) ) >= $due ) { RTS_Business_Logic_7::run_report( (int) $r->id, 'cron' ); $n++; }
		}
		update_option( 'rts_cron_last_scheduled_reports', current_time( 'mysql' ) ); return $n;
	}
	public static function cron_action_items() { $r = RTS_Business_Logic_7::generate_action_items(); update_option( 'rts_cron_last_action_items', current_time( 'mysql' ) ); return $r; }
	public static function cron_fr_sync() { $r = self::fr_sync( 'cron' ); update_option( 'rts_cron_last_fr_sync', current_time( 'mysql' ) ); return $r; }

	// =========================================================================================
	//  AI INTEGRATION POINT (Q&R drafts, broadcast drafts). Real HTTP to the provider when a key
	//  is configured; otherwise returns a clear NOT_CONFIGURED — never a fake "AI" answer.
	// =========================================================================================
	public static function ai_configured() { return '' !== trim( (string) self::get( 'ai_api_key' ) ); }
	public static function ai_draft( $task, $context ) {
		if ( ! self::ai_configured() ) { return array( 'error' => 'AI_NOT_CONFIGURED', 'message' => 'Add an API key under Run The Seas → Settings to enable AI drafting.' ); }
		$system = "You are the customer-communications assistant for Run The Seas, a luxury cruise-based running festival (1K, 5K, Half Marathon; \$100 Founding Runner Cabin Credit for verified sign-ups). Write warm, concise, accurate replies. Never invent pricing, dates or policies not given in the context.";
		$prompt = 'question_reply' === $task
			? "A customer asked: \"{$context['question']}\"\n\nDraft a reply (3-6 sentences). Known facts: {$context['facts']}"
			: "Write a short marketing email for Run The Seas participants. Brief from the admin: \"{$context['brief']}\"\n\nReturn the email body only (no subject line). Use {first_name} as a merge field.";
		$resp = wp_remote_post( 'https://api.anthropic.com/v1/messages', array(
			'timeout' => 30,
			'headers' => array( 'content-type' => 'application/json', 'x-api-key' => self::get( 'ai_api_key' ), 'anthropic-version' => '2023-06-01' ),
			'body'    => wp_json_encode( array( 'model' => self::get( 'ai_model' ), 'max_tokens' => 600, 'system' => $system, 'messages' => array( array( 'role' => 'user', 'content' => $prompt ) ) ) ),
		) );
		if ( is_wp_error( $resp ) ) { return array( 'error' => 'AI_HTTP_ERROR', 'message' => $resp->get_error_message() ); }
		$code = wp_remote_retrieve_response_code( $resp ); $j = json_decode( wp_remote_retrieve_body( $resp ), true );
		if ( 200 !== $code || empty( $j['content'][0]['text'] ) ) { return array( 'error' => 'AI_BAD_RESPONSE', 'http' => $code, 'message' => $j['error']['message'] ?? 'unexpected response' ); }
		self::audit( self::who(), "AI draft generated ($task)", 'AI', 'model=' . self::get( 'ai_model' ) );
		return array( 'error' => null, 'draft' => trim( $j['content'][0]['text'] ), 'model' => self::get( 'ai_model' ) );
	}

	// =========================================================================================
	//  EXTERNAL FOUNDING RUNNERS (main site) — import + email-match sync
	// =========================================================================================
	public static function fr_import_rows( $rows, $source = 'main_site', $by = null ) {
		global $wpdb; $t = RTS_DB::table( 'external_founding_runners' ); $ins = 0; $skip = 0;
		foreach ( $rows as $r ) {
			$email = sanitize_email( $r['email'] ?? '' ); if ( ! is_email( $email ) ) { $skip++; continue; }
			$ok = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $t (name, email, source) VALUES (%s, %s, %s)", sanitize_text_field( $r['name'] ?? '' ), $email, sanitize_key( $source ) ) );
			if ( $ok ) { $ins++; } else { $skip++; }
		}
		self::audit( $by, "External Founding Runners imported", 'Founding Runner Outreach', "inserted=$ins; skipped=$skip; source=$source" );
		$sync = self::fr_sync( $by );
		return array( 'error' => null, 'inserted' => $ins, 'skipped' => $skip, 'matched_now' => $sync['newly_matched'] );
	}
	/** Link external records to local participants by email. Idempotent. Also fires for newly-registered participants via register hook below. */
	public static function fr_sync( $by = null ) {
		global $wpdb; $t = RTS_DB::table( 'external_founding_runners' ); $p = RTS_DB::table( 'participants' );
		$n = (int) $wpdb->query( "UPDATE $t e JOIN $p pp ON LOWER(pp.email) = LOWER(e.email) SET e.matched_participant_id = pp.id WHERE e.matched_participant_id IS NULL" );
		if ( $n ) { self::audit( $by, 'External Founding Runners matched to participants by email', 'Founding Runner Outreach', "newly_matched=$n" ); }
		return array( 'newly_matched' => $n, 'unmatched' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM $t WHERE matched_participant_id IS NULL" ) );
	}

	// =========================================================================================
	//  FORMS-PLUGIN ADAPTER — one call any forms plugin (Gravity Forms / WPForms / CF7 / Elementor)
	//  can make from its submission hook. Returns the same result as the REST register route.
	// =========================================================================================
	public static function register_from_form( $fields, $source = 'wp_form' ) {
		$data = array(
			'name' => sanitize_text_field( $fields['name'] ?? trim( ( $fields['first_name'] ?? '' ) . ' ' . ( $fields['last_name'] ?? '' ) ) ),
			'email' => sanitize_email( $fields['email'] ?? '' ), 'country' => sanitize_text_field( $fields['country'] ?? '' ),
			'runner_status' => in_array( $fields['runner_status'] ?? '', array( 'runner', 'non_runner' ), true ) ? $fields['runner_status'] : null,
			'marketing_source' => sanitize_key( $source ), 'utm_campaign' => sanitize_text_field( $fields['utm_campaign'] ?? '' ), 'referred_by_code' => sanitize_text_field( $fields['ref'] ?? $fields['referred_by_code'] ?? '' ),
		);
		if ( ! is_email( $data['email'] ) ) { return array( 'error' => 'INVALID_EMAIL' ); }
		$r = RTS_Business_Logic::register_participant( $data );
		if ( empty( $r['error'] ) ) { self::send_verification( $r['participant_id'] ); do_action( 'rts_participant_registered', $r['participant_id'], $data, $source ); }
		return $r;
	}

	// =========================================================================================
	//  RATE LIMITING (public routes) — transient per IP per hour; returns WP_Error 429 when exceeded
	// =========================================================================================
	public static function rate_limit( $bucket ) {
		$limit = (int) self::get( 'rate_limit_' . $bucket ); if ( $limit <= 0 ) { return true; }
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'; $ip = sanitize_text_field( explode( ',', $ip )[0] );
		$key = 'rts_rl_' . $bucket . '_' . md5( $ip ); $n = (int) get_transient( $key );
		if ( $n >= $limit ) { return new WP_Error( 'rts_rate_limited', 'Too many requests. Please try again later.', array( 'status' => 429 ) ); }
		set_transient( $key, $n + 1, HOUR_IN_SECONDS ); return true;
	}

	// =========================================================================================
	//  ADMIN: Settings page + FR import panel
	// =========================================================================================
	public static function menu() {
		RTS_Auth::page( 'rts-admin', 'Settings & Integrations', 'Settings', 'rts_system', 'rts-settings', array( __CLASS__, 'render_settings' ) );
	}
	public static function render_settings() {
		$msg = ! empty( $_GET['rts_msg'] ) ? '<div class="notice notice-success is-dismissible"><p>' . esc_html( rawurldecode( $_GET['rts_msg'] ) ) . '</p></div>' : '';
		$g = fn( $k ) => esc_attr( self::get( $k ) ); $act = esc_url( admin_url( 'admin-post.php' ) );
		$cron = array(); foreach ( array( 'rts_cron_campaign_triggers', 'rts_cron_scheduled_reports', 'rts_cron_action_items', 'rts_cron_fr_sync' ) as $h ) { $ts = wp_next_scheduled( $h ); $cron[] = '<tr><td><code>' . $h . '</code></td><td>' . ( $ts ? esc_html( date( 'Y-m-d H:i', $ts ) ) : '<b style="color:#B23B3B">not scheduled</b>' ) . '</td><td>' . esc_html( get_option( str_replace( 'rts_cron_', 'rts_cron_last_', $h ) ) ?: 'never' ) . '</td></tr>'; }
		$ob = ''; foreach ( self::outbox( 15 ) as $o ) { $ob .= '<tr><td>' . esc_html( $o->created_at ) . '</td><td>' . esc_html( $o->to_email ) . '</td><td>' . esc_html( $o->subject ) . '</td><td>' . esc_html( $o->kind ) . '</td><td>' . esc_html( $o->mode ) . ( 'send' === $o->mode ? ( $o->delivered ? ' ✅' : ' ❌ ' . esc_html( $o->error ) ) : '' ) . '</td></tr>'; }
		echo '<div class="wrap"><h1>Settings &amp; Integrations</h1>' . $msg
		. '<form method="post" action="' . $act . '"><input type="hidden" name="action" value="rts_settings">' . wp_nonce_field( 'rts_settings', '_rts_nonce', true, false )
		. '<h3>Email delivery</h3><table class="form-table">'
		. '<tr><th>Mode</th><td><select name="email_mode"><option value="log"' . selected( self::get( 'email_mode' ), 'log', false ) . '>log only (no mail leaves the server — outbox below)</option><option value="send"' . selected( self::get( 'email_mode' ), 'send', false ) . '>send via wp_mail() (needs an SMTP/API mail plugin)</option></select><p class="description">wp_mail() is the standard WordPress hand-off: install WP Mail SMTP / Brevo / SendGrid etc. and every RTS email flows through it.</p></td></tr>'
		. '<tr><th>From name</th><td><input name="email_from_name" value="' . $g( 'email_from_name' ) . '" class="regular-text"></td></tr>'
		. '<tr><th>From address</th><td><input name="email_from_address" value="' . $g( 'email_from_address' ) . '" class="regular-text"><p class="description">Must be a sender your mail provider has verified.</p></td></tr>'
		. '<tr><th>Admin alert email</th><td><input name="admin_notify_email" value="' . $g( 'admin_notify_email' ) . '" class="regular-text" placeholder="' . esc_attr( get_option( 'admin_email' ) ) . '"><p class="description">Take-offline / restore alerts. Blank = site admin email.</p></td></tr>'
		. '</table><h3>AI drafting (Q&amp;R Queue, Broadcast)</h3><table class="form-table">'
		. '<tr><th>Provider</th><td><code>anthropic</code> (Claude Messages API)</td></tr>'
		. '<tr><th>API key</th><td><input name="ai_api_key" type="password" value="' . $g( 'ai_api_key' ) . '" class="regular-text" autocomplete="new-password"><p class="description">Stored in wp_options; treat like a password. Leave blank to disable AI drafting (buttons show "not configured").</p></td></tr>'
		. '<tr><th>Model</th><td><input name="ai_model" value="' . $g( 'ai_model' ) . '" class="regular-text"></td></tr>'
		. '</table><h3>Public-route rate limits (per IP, per hour)</h3><table class="form-table">'
		. '<tr><th>Registrations</th><td><input name="rate_limit_register" type="number" min="0" value="' . $g( 'rate_limit_register' ) . '"> <span class="description">0 = unlimited</span></td></tr>'
		. '<tr><th>Verification attempts</th><td><input name="rate_limit_verify" type="number" min="0" value="' . $g( 'rate_limit_verify' ) . '"></td></tr>'
		. '</table><h3>Holding page</h3><table class="form-table"><tr><th>Offline message</th><td><textarea name="offline_message" class="large-text" rows="2">' . esc_textarea( self::get( 'offline_message' ) ) . '</textarea></td></tr></table>'
		. '<p><button class="button button-primary">Save settings</button></p></form>'
		. '<h3>Scheduled jobs (WP-Cron)</h3><table class="widefat striped" style="max-width:760px"><thead><tr><th>Hook</th><th>Next run</th><th>Last run</th></tr></thead><tbody>' . implode( '', $cron ) . '</tbody></table><p class="description">WP-Cron fires on page loads. For reliable timing on the live site, disable the pseudo-cron and hit wp-cron.php from a real system cron (see handoff report).</p>'
		. '<h3>Email outbox (latest 15)</h3><table class="widefat striped"><thead><tr><th>When</th><th>To</th><th>Subject</th><th>Kind</th><th>Mode / result</th></tr></thead><tbody>' . ( $ob ?: '<tr><td colspan="5" style="color:#777">No email generated yet</td></tr>' ) . '</tbody></table>'
		. '<h3>External Founding Runners (main-site import)</h3><p class="description">Upload a CSV with columns <code>name,email</code> exported from the main site. Rows are matched to participants by email immediately and again every 15 minutes by cron.</p>'
		. '<form method="post" action="' . $act . '" enctype="multipart/form-data"><input type="hidden" name="action" value="rts_fr_import">' . wp_nonce_field( 'rts_fr_import', '_rts_nonce', true, false ) . '<input type="file" name="csv" accept=".csv" required> <button class="button">Import CSV</button></form> '
		. '<form method="post" action="' . $act . '" style="display:inline"><input type="hidden" name="action" value="rts_fr_sync">' . wp_nonce_field( 'rts_fr_sync', '_rts_nonce', true, false ) . '<button class="button">Run email-match sync now</button></form>'
		. '</div>';
	}
	public static function handle_settings() {
		if ( ! current_user_can( 'rts_system' ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_settings' ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); }
		$o = get_option( self::OPT, array() );
		$o['email_mode'] = in_array( $_POST['email_mode'] ?? '', array( 'log', 'send' ), true ) ? $_POST['email_mode'] : 'log';
		$o['email_from_name'] = sanitize_text_field( $_POST['email_from_name'] ?? '' ); $o['email_from_address'] = sanitize_email( $_POST['email_from_address'] ?? '' ); $o['admin_notify_email'] = sanitize_email( $_POST['admin_notify_email'] ?? '' );
		$o['ai_api_key'] = trim( sanitize_text_field( $_POST['ai_api_key'] ?? '' ) ); $o['ai_model'] = sanitize_text_field( $_POST['ai_model'] ?? 'claude-sonnet-4-6' );
		$o['rate_limit_register'] = max( 0, (int) ( $_POST['rate_limit_register'] ?? 100 ) ); $o['rate_limit_verify'] = max( 0, (int) ( $_POST['rate_limit_verify'] ?? 60 ) );
		$o['offline_message'] = sanitize_textarea_field( $_POST['offline_message'] ?? '' );
		update_option( self::OPT, $o ); self::audit( self::who(), 'Settings updated', 'Settings', 'email_mode=' . $o['email_mode'] . '; ai=' . ( $o['ai_api_key'] ? 'configured' : 'off' ) );
		wp_safe_redirect( RTSAP_Frontend_Dashboard::screen_url( 'rts-settings', array( 'rts_msg' => rawurlencode( 'Settings saved.' ) ) ) ); exit;
	}
	public static function handle_fr_import() {
		if ( ! current_user_can( 'rts_system' ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_fr_import' ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); }
		$rows = array();
		if ( ! empty( $_FILES['csv']['tmp_name'] ) && ( $fh = fopen( $_FILES['csv']['tmp_name'], 'r' ) ) ) { $head = array_map( 'strtolower', array_map( 'trim', (array) fgetcsv( $fh ) ) ); while ( ( $line = fgetcsv( $fh ) ) !== false ) { $rows[] = array_combine( $head, array_pad( $line, count( $head ), '' ) ); } fclose( $fh ); }
		$r = self::fr_import_rows( $rows, 'main_site_csv', self::who() );
		wp_safe_redirect( RTSAP_Frontend_Dashboard::screen_url( 'rts-settings', array( 'rts_msg' => rawurlencode( "Imported {$r['inserted']} (skipped {$r['skipped']}); {$r['matched_now']} matched to participants." ) ) ) ); exit;
	}
	public static function handle_fr_sync() {
		if ( ! current_user_can( 'rts_system' ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_fr_sync' ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); }
		$r = self::fr_sync( self::who() );
		wp_safe_redirect( RTSAP_Frontend_Dashboard::screen_url( 'rts-settings', array( 'rts_msg' => rawurlencode( "Sync: {$r['newly_matched']} newly matched, {$r['unmatched']} still unmatched." ) ) ) ); exit;
	}
}
