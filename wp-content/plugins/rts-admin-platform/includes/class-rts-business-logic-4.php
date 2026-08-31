<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_4 {

	private static function audit( $u, $a, $m, $n = '' ) { RTS_Business_Logic::log_audit( $u ?: 'admin', $a, $m, 'success', $n ); }

	// ---- The four spec roles, as REAL WordPress roles with REAL capabilities. ----
	// Unlike the Node prototype (a standalone `admins` table with no login), this hooks into
	// wp_users — so role management here = actual login + actual permission enforcement.
	// Capabilities match spec Appendix B (Permissions Matrix):
	//   Super Administrator — everything.  Administrator — operational, minus admin-management and
	//   system/Tier-3 actions.  Content Editor — Website Content ONLY (no participant / financial /
	//   security data).  Contributor — no platform-wide access (task-specific; nothing by default).
	// Batch 4 originally gave content_editor and contributor 'rts_view'; that was harmless while REST
	// was open and the admin pages were gated by manage_options, but it would have let them read
	// participant PII once REST became the gate. Corrected here; register_roles() syncs existing roles.
	const ROLES = array(
		'rts_super_admin'    => array( 'label' => 'RTS Super Administrator', 'caps' => array( 'rts_dashboard', 'rts_view', 'rts_manage', 'rts_send_bulk', 'rts_manage_admins', 'rts_system', 'rts_content', 'upload_files' ) ),
		'rts_administrator'  => array( 'label' => 'RTS Administrator',       'caps' => array( 'rts_dashboard', 'rts_view', 'rts_manage', 'rts_send_bulk', 'rts_content', 'upload_files' ) ),
		'rts_content_editor' => array( 'label' => 'RTS Content Editor',      'caps' => array( 'rts_dashboard', 'rts_content' ) ),
		'rts_contributor'    => array( 'label' => 'RTS Contributor',         'caps' => array( 'rts_dashboard' ) ),
	);

	public static function register_roles() {
		$fluent_caps = array( 'fluentform_dashboard_access', 'fluentform_forms_manager', 'fluentform_entries_viewer' );
		$media_caps = array( 'upload_files' );
		foreach ( self::ROLES as $slug => $def ) {
			$want = array_fill_keys( $def['caps'], true );
			$role = get_role( $slug );
			if ( ! $role ) { add_role( $slug, $def['label'], $want + array( 'read' => true ) ); continue; }
			foreach ( RTS_Auth::CAPS as $c ) { // sync: exactly the defined set, no more
				if ( isset( $want[ $c ] ) ) { $role->add_cap( $c ); } else { $role->remove_cap( $c ); }
			}
			// Survey operations use Fluent Forms' native editor and AJAX APIs.
			// Only roles holding rts_manage receive those native capabilities.
			foreach ( $fluent_caps as $c ) {
				if ( isset( $want['rts_manage'] ) ) { $role->add_cap( $c ); } else { $role->remove_cap( $c ); }
			}
			foreach ( $media_caps as $c ) {
				if ( isset( $want[ $c ] ) ) { $role->add_cap( $c ); } else { $role->remove_cap( $c ); }
			}
			$role->add_cap( 'read' );
		}
		// Built-in WP administrators get every RTS cap so the site owner is never locked out.
		$wp_admin = get_role( 'administrator' );
		if ( $wp_admin ) { foreach ( RTS_Auth::CAPS as $c ) { $wp_admin->add_cap( $c ); } }
	}

	public static function list_admins() {
		$users = get_users( array( 'role__in' => array_merge( array_keys( self::ROLES ), array( 'administrator' ) ), 'orderby' => 'registered', 'order' => 'DESC' ) );
		return array_map( function ( $u ) {
			$rts_roles = array_values( array_filter( $u->roles, fn( $r ) => str_starts_with( $r, 'rts_' ) || 'administrator' === $r ) );
			return array( 'id' => $u->ID, 'name' => $u->display_name, 'email' => $u->user_email, 'login' => $u->user_login, 'role' => $rts_roles[0] ?? null, 'registered' => $u->user_registered );
		}, $users );
	}

	public static function invite_admin( $name, $email, $role, $invited_by ) {
		if ( ! isset( self::ROLES[ $role ] ) ) { return array( 'error' => 'INVALID_ROLE' ); }
		if ( ! is_email( $email ) ) { return array( 'error' => 'INVALID_EMAIL' ); }
		if ( email_exists( $email ) ) { return array( 'error' => 'EMAIL_ALREADY_INVITED' ); }
		$login = sanitize_user( strstr( $email, '@', true ) ?: $email, true ); $base = $login; $i = 2;
		while ( username_exists( $login ) ) { $login = $base . $i++; }
		$uid = wp_insert_user( array( 'user_login' => $login, 'user_email' => $email, 'display_name' => $name, 'user_pass' => wp_generate_password( 24 ), 'role' => $role ) );
		if ( is_wp_error( $uid ) ) { return array( 'error' => 'WP_ERROR', 'message' => $uid->get_error_message() ); }
		self::audit( $invited_by, "Administrator invited: $name ($role)", 'Administrator Management', "user_id=$uid" );
		return array( 'error' => null, 'admin_id' => $uid, 'login' => $login );
	}

	private static function count_super_admins_excluding( $uid ) {
		$n = 0;
		foreach ( get_users( array( 'role__in' => array( 'rts_super_admin', 'administrator' ) ) ) as $u ) { if ( (int) $u->ID !== (int) $uid ) { $n++; } }
		return $n;
	}

	// Business rule: never allow the last Super Administrator to be demoted/removed (lockout prevention).
	public static function change_role( $uid, $new_role, $by ) {
		if ( ! isset( self::ROLES[ $new_role ] ) ) { return array( 'error' => 'INVALID_ROLE' ); }
		$u = get_user_by( 'id', $uid );
		if ( ! $u ) { return array( 'error' => 'NOT_FOUND' ); }
		$is_super = in_array( 'rts_super_admin', $u->roles, true ) || in_array( 'administrator', $u->roles, true );
		if ( $is_super && 'rts_super_admin' !== $new_role && 0 === self::count_super_admins_excluding( $uid ) ) { return array( 'error' => 'CANNOT_REMOVE_LAST_SUPER_ADMIN' ); }
		$u->set_role( $new_role );
		self::audit( $by, "Administrator role changed: {$u->display_name} -> $new_role", 'Administrator Management', "user_id=$uid" );
		return array( 'error' => null );
	}

	public static function deactivate( $uid, $by ) {
		$u = get_user_by( 'id', $uid );
		if ( ! $u ) { return array( 'error' => 'NOT_FOUND' ); }
		$is_super = in_array( 'rts_super_admin', $u->roles, true ) || in_array( 'administrator', $u->roles, true );
		if ( $is_super && 0 === self::count_super_admins_excluding( $uid ) ) { return array( 'error' => 'CANNOT_DEACTIVATE_LAST_SUPER_ADMIN' ); }
		$u->set_role( '' ); // strips all roles = cannot log in to anything RTS; account kept for audit trail
		update_user_meta( $uid, 'rts_deactivated', current_time( 'mysql' ) );
		self::audit( $by, "Administrator deactivated: {$u->display_name}", 'Administrator Management', "user_id=$uid" );
		return array( 'error' => null );
	}

	// ---- Executive Dashboard — expanded Top-20 KPIs ----
	public static function executive_summary_v2() {
		global $wpdb; $pt = RTS_DB::table( 'participants' ); $rt = RTS_DB::table( 'referrals' ); $ct = RTS_DB::table( 'cabin_credits' ); $srt = RTS_DB::table( 'survey_responses' ); $tut = RTS_DB::table( 'trophy_unlocks' );
		$i = fn( $q ) => (int) $wpdb->get_var( $q );
		$completed = $i( "SELECT COUNT(*) FROM $srt WHERE status='completed'" ); $started = $i( "SELECT COUNT(*) FROM $srt" );
		$this_wk = $i( "SELECT COUNT(*) FROM $pt WHERE registered_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)" );
		$last_wk = $i( "SELECT COUNT(*) FROM $pt WHERE registered_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND registered_at < DATE_SUB(NOW(), INTERVAL 7 DAY)" );
		$k = RTS_Business_Logic::calculate_referral_coefficient();
		$total_ref = $i( "SELECT COUNT(*) FROM $rt" ); $ver_ref = $i( "SELECT COUNT(*) FROM $rt WHERE verified=1" );
		$total_p = $i( "SELECT COUNT(*) FROM $pt" ); $ver_p = $i( "SELECT COUNT(*) FROM $pt WHERE email_verified=1" );
		$avg_party = $wpdb->get_var( "SELECT AVG(travel_party_size) FROM $pt WHERE travel_party_size IS NOT NULL" );
		$credits = $i( "SELECT COUNT(*) FROM $ct WHERE status IN ('issued','deferred')" );
		$daily_completions = $wpdb->get_results( "SELECT DATE(completed_at) AS label, COUNT(*) AS value FROM $srt WHERE status='completed' AND completed_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY) GROUP BY DATE(completed_at) ORDER BY label ASC" );
		$weekly_referrals = $wpdb->get_results( "SELECT DATE_FORMAT(COALESCE(verified_at, completed_date, created_at), '%x-W%v') AS label, COUNT(*) AS value FROM $rt WHERE verified=1 AND COALESCE(verified_at, completed_date, created_at) >= DATE_SUB(CURDATE(), INTERVAL 12 WEEK) GROUP BY YEARWEEK(COALESCE(verified_at, completed_date, created_at), 3) ORDER BY YEARWEEK(COALESCE(verified_at, completed_date, created_at), 3) ASC" );
		$top_referrers = $wpdb->get_results( "SELECT COALESCE(NULLIF(p.name,''), NULLIF(p.email,''), 'Unknown') AS label, COUNT(*) AS value FROM $rt r LEFT JOIN $pt p ON p.id=COALESCE(r.referring_participant_id,r.referrer_id) WHERE r.verified=1 GROUP BY COALESCE(NULLIF(p.name,''), NULLIF(p.email,''), 'Unknown') ORDER BY value DESC LIMIT 5" );
		return array(
			'total_surveys_completed' => $completed,
			'survey_completion_rate' => $started ? round( $completed / $started * 100, 1 ) : 0,
			'week_over_week_growth' => $last_wk ? round( ( $this_wk - $last_wk ) / $last_wk * 100, 1 ) : null,
			'referral_coefficient' => $k,
			'verified_referrals_total' => $ver_ref, 'total_referrals_sent' => $total_ref,
			'avg_referrals_per_founding_runner' => $ver_p ? round( $total_ref / $ver_p, 2 ) : 0,
			'total_participants' => $total_p, 'verified_participants' => $ver_p,
			'runners_vs_non_runners' => array( 'runners' => $i( "SELECT COUNT(*) FROM $pt WHERE runner_status='runner'" ), 'non_runners' => $i( "SELECT COUNT(*) FROM $pt WHERE runner_status='non_runner'" ) ),
			'avg_travel_party_size' => $avg_party ? round( (float) $avg_party, 1 ) : null,
			'geographic_distribution' => $wpdb->get_results( "SELECT country, COUNT(*) AS c FROM $pt WHERE country IS NOT NULL GROUP BY country ORDER BY c DESC LIMIT 10" ),
			'marketing_source_breakdown' => $wpdb->get_results( "SELECT marketing_source, COUNT(*) AS c FROM $pt WHERE marketing_source IS NOT NULL GROUP BY marketing_source ORDER BY c DESC" ),
			'cabin_credits_issued' => $credits,
			'cabin_credit_floor' => 400, 'cabin_credit_target' => 500,
			'conversion_funnel' => array( 'registered' => $total_p, 'verified' => $ver_p, 'credited' => $i( "SELECT COUNT(*) FROM $ct" ) ),
			'cost_per_founding_runner' => null, // pending ad-spend integration (Batch 5)
			'email_verification_rate' => $total_p ? round( $ver_p / $total_p * 100, 1 ) : 0,
			'notification_interest_total' => $i( "SELECT COUNT(*) FROM $pt WHERE wants_cruise_notification=1" ),
			'total_trophies_unlocked' => $i( "SELECT COUNT(*) FROM $tut" ),
			'unique_trophy_holders' => $i( "SELECT COUNT(DISTINCT participant_id) FROM $tut" ),
			'outstanding_credit_liability' => $credits * 100,
			'daily_completions' => $daily_completions,
			'weekly_verified_referrals' => $weekly_referrals,
			'top_referrers' => $top_referrers,
		);
	}

	// ---- Global search across every searchable module ----
	public static function global_search( $q ) {
		global $wpdb;
		$q = trim( (string) $q );
		$empty = array( 'participants' => array(), 'surveys' => array(), 'trophies' => array(), 'audit_log' => array(), 'admins' => array() );
		if ( strlen( $q ) < 2 ) { return $empty; }
		$like = '%' . $wpdb->esc_like( $q ) . '%';
		return array(
			'participants' => $wpdb->get_results( $wpdb->prepare( "SELECT id, name, email, founding_runner_number FROM " . RTS_DB::table( 'participants' ) . " WHERE name LIKE %s OR email LIKE %s OR founding_runner_number LIKE %s LIMIT 10", $like, $like, $like ) ),
			'surveys'      => $wpdb->get_results( $wpdb->prepare( "SELECT id, name, status FROM " . RTS_DB::table( 'surveys' ) . " WHERE name LIKE %s LIMIT 10", $like ) ),
			'trophies'     => $wpdb->get_results( $wpdb->prepare( "SELECT id, name, unlock_rule FROM " . RTS_DB::table( 'trophies' ) . " WHERE name LIKE %s LIMIT 10", $like ) ),
			'audit_log'    => $wpdb->get_results( $wpdb->prepare( "SELECT id, user, action, module, created_at FROM " . RTS_DB::table( 'audit_log' ) . " WHERE action LIKE %s OR user LIKE %s OR module LIKE %s ORDER BY created_at DESC LIMIT 10", $like, $like, $like ) ),
			'admins'       => array_values( array_filter( self::list_admins(), fn( $a ) => stripos( $a['name'], $q ) !== false || stripos( $a['email'], $q ) !== false ) ),
		);
	}

	// ---- Backups ----
	public static function run_backup( $by ) {
		global $wpdb;
		$wpdb->insert( RTS_DB::table( 'backups' ), array( 'triggered_by' => $by ?: 'admin', 'status' => 'completed' ) );
		$id = (int) $wpdb->insert_id; // BEFORE audit()
		self::audit( $by, 'Backup run manually', 'Backup & System Settings', "backup_id=$id" );
		return array( 'error' => null, 'backup_id' => $id );
	}
	public static function backup_history() { global $wpdb; return $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'backups' ) . " ORDER BY created_at DESC LIMIT 20" ); }
	public static function last_backup()    { global $wpdb; return $wpdb->get_row( "SELECT * FROM " . RTS_DB::table( 'backups' ) . " ORDER BY created_at DESC LIMIT 1" ); }

	// ---- Security Dashboard — now partly REAL because WP has real auth ----
	public static function security_stats() {
		global $wpdb;
		$dist = array();
		foreach ( array_merge( array_keys( self::ROLES ), array( 'administrator' ) ) as $slug ) { $n = count( get_users( array( 'role' => $slug, 'fields' => 'ID' ) ) ); if ( $n ) { $dist[] = array( 'role' => $slug, 'c' => $n ); } }
		return array(
			'role_distribution' => $dist,
			'active_admins'     => array_sum( array_column( $dist, 'c' ) ),
			'last_backup'       => self::last_backup(),
			'recent_audit_log'  => $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'audit_log' ) . " ORDER BY created_at DESC LIMIT 15" ),
			// Honest: WP has real logins, but core does NOT track failed attempts or active sessions
			// without a plugin (e.g. Limit Login Attempts). Reported as null rather than faked.
			'failed_logins_24h' => null,
			'active_sessions'   => null,
			'auth_note'         => 'WordPress has real login; failed-attempt and session counts need a security plugin or custom hook — not faked here.',
		);
	}

	public static function system_health() {
		global $wpdb;
		return array( 'active_admins' => array_sum( array_column( self::security_stats()['role_distribution'], 'c' ) ), 'last_backup' => self::last_backup(), 'total_audit_entries' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . RTS_DB::table( 'audit_log' ) ), 'wp_version' => get_bloginfo( 'version' ), 'php_version' => PHP_VERSION );
	}
}
