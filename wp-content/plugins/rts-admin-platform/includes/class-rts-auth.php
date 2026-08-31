<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * RTS_Auth — the single place REST routes are registered.
 *
 * Every route MUST name a capability. Passing anything other than a known rts_* capability or
 * the explicit RTS_Auth::PUBLIC_ROUTE sentinel throws, so a route can't be left open by omission.
 *
 * PUBLIC_ROUTE is reserved for the participant-facing flows that cannot carry a login:
 * the survey itself, registration, email-link verification, and token-based subscription
 * management. Everything else requires a logged-in user holding the named capability.
 *
 * WordPress itself turns a false permission_callback into 401 (not logged in) or 403 (logged in
 * but lacking the capability) via rest_authorization_required_code().
 */
class RTS_Auth {

	const PUBLIC_ROUTE = 'public';
	const CAPS = array( 'rts_dashboard', 'rts_view', 'rts_manage', 'rts_send_bulk', 'rts_manage_admins', 'rts_system', 'rts_content' );

	private static $registry = array();

	public static function route( $ns, $route, $methods, $callback, $cap ) {
		if ( self::PUBLIC_ROUTE !== $cap && ! in_array( $cap, self::CAPS, true ) ) {
			throw new InvalidArgumentException( "RTS_Auth::route(): route '$route' has no valid capability ('$cap'). Refusing to register an unguarded route." );
		}
		self::$registry[] = array( 'route' => $route, 'methods' => $methods, 'cap' => $cap );
		register_rest_route( $ns, $route, array(
			'methods'             => $methods,
			'callback'            => $callback,
			'permission_callback' => self::PUBLIC_ROUTE === $cap ? '__return_true' : function () use ( $cap ) { return current_user_can( $cap ); },
		) );
	}

	/** admin_post action -> capability. MUST mirror the REST cap for the same mutation, so the UI and API agree. */
	const ACTION_CAPS = array(
		'clone_survey' => 'rts_manage',
		'survey_status' => 'rts_manage',
		'participant_status' => 'rts_manage',
		'participant_edit' => 'rts_manage',
		'participant_note' => 'rts_manage',
		'participant_email' => 'rts_manage',
		'suspend' => 'rts_manage',
		'reinstate' => 'rts_manage',
		'merge' => 'rts_manage',
		'manual_verify' => 'rts_manage',
		'reset_passcode' => 'rts_manage',
		'create_template' => 'rts_manage',
		'update_template' => 'rts_manage',
		'assign_template' => 'rts_manage',
		'rollback_template' => 'rts_manage',
		'defer_credit' => 'rts_manage',
		'void_credit' => 'rts_manage',
		'create_trophy' => 'rts_manage',
		'retro_unlock' => 'rts_manage',
		'run_draw' => 'rts_system',
		'bc_create' => 'rts_send_bulk',
		'bc_test_self' => 'rts_send_bulk',
		'bc_test_group' => 'rts_send_bulk',
		'bc_send_bulk' => 'rts_send_bulk',
		'invite_admin' => 'rts_manage_admins',
		'change_role' => 'rts_manage_admins',
		'deactivate_admin' => 'rts_manage_admins',
		'run_backup' => 'rts_system',
		'ec_create' => 'rts_send_bulk',
		'ec_status' => 'rts_send_bulk',
		'ec_trigger' => 'rts_send_bulk',
		'ad_create' => 'rts_manage',
		'dup_review' => 'rts_manage',
		'reject_ref' => 'rts_manage',
		'q_create' => 'rts_manage',
		'q_draft' => 'rts_manage',
		'q_send' => 'rts_manage',
		'q_ai' => 'rts_manage',
		'cms_save' => 'rts_content',
		'export_csv' => 'rts_view',
		'rb_save' => 'rts_manage',
		'rb_run' => 'rts_view',
		'seg_save' => 'rts_manage',
		'ai_resolve' => 'rts_manage',
	);
	public static function action_cap( $action ) {
		if ( ! isset( self::ACTION_CAPS[ $action ] ) ) { return 'do_not_allow'; } // unknown action => nobody
		return self::ACTION_CAPS[ $action ];
	}

	/** Admin page registry (slug => cap) — populated by RTS_Auth::page() so the UI gate is auditable like the REST gate. */
	private static $pages = array();
	public static function page( $parent, $title, $menu, $cap, $slug, $cb, $pos = null ) {
		if ( ! in_array( $cap, self::CAPS, true ) ) { throw new InvalidArgumentException( "RTS_Auth::page(): '$slug' must be gated by an rts_* capability, got '$cap'." ); }
		self::$pages[ $slug ] = $cap;
		return add_submenu_page( $parent, $title, $menu, $cap, $slug, $cb, $pos );
	}
	public static function pages() { return self::$pages; }

	/** For the audit in wp_test_flow.py: list of every registered RTS route with its capability. */
	public static function registry() { return self::$registry; }
}
