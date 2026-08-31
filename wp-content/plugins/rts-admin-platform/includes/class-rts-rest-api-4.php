<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_REST_API_4 {
	public static function init() { add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) ); }
	private static function body( $r ) { return json_decode( $r->get_body(), true ) ?: array(); }
	private static function out( $res, $code = 400 ) { return new WP_REST_Response( $res, ( is_array( $res ) && ! empty( $res['error'] ) ) ? $code : 200 ); }

	public static function register_routes() {
		$ns = 'rts/v1';
		$r = function ( $route, $m, $cb, $cap ) use ( $ns ) { RTS_Auth::route( $ns, $route, $m, array( __CLASS__, $cb ), $cap ); };
		$PUB = RTS_Auth::PUBLIC_ROUTE;
		$r( '/reports/executive-summary-v2',       'GET',  'exec_v2', 'rts_view' );
		$r( '/search',                             'GET',  'search', 'rts_view' );
		$r( '/system/health',                      'GET',  'health', 'rts_view' );
		$r( '/admins',                             'GET',  'list_admins', 'rts_manage_admins' );
		$r( '/admins',                             'POST', 'invite', 'rts_manage_admins' );
		$r( '/admins/(?P<id>\d+)/role',            'POST', 'role', 'rts_manage_admins' );
		$r( '/admins/(?P<id>\d+)/deactivate',      'POST', 'deactivate', 'rts_manage_admins' );
		$r( '/backups/run',                        'POST', 'backup_run', 'rts_system' );
		$r( '/backups/history',                    'GET',  'backup_history', 'rts_view' );
		$r( '/security/stats',                     'GET',  'security', 'rts_view' );
	}
	public static function exec_v2()          { return rest_ensure_response( RTS_Business_Logic_4::executive_summary_v2() ); }
	public static function search( $q )       { return rest_ensure_response( RTS_Business_Logic_4::global_search( $q->get_param( 'q' ) ) ); }
	public static function health()           { return rest_ensure_response( RTS_Business_Logic_4::system_health() ); }
	public static function list_admins()      { return rest_ensure_response( RTS_Business_Logic_4::list_admins() ); }
	public static function invite( $q )       { $b = self::body( $q ); if ( empty( $b['name'] ) || empty( $b['email'] ) || empty( $b['role'] ) ) { return self::out( array( 'error' => 'NAME_EMAIL_ROLE_REQUIRED' ) ); } return self::out( RTS_Business_Logic_4::invite_admin( $b['name'], $b['email'], $b['role'], $b['invited_by'] ?? null ) ); }
	public static function role( $q )         { $b = self::body( $q ); return self::out( RTS_Business_Logic_4::change_role( (int) $q['id'], $b['role'] ?? '', $b['updated_by'] ?? null ) ); }
	public static function deactivate( $q )   { $b = self::body( $q ); return self::out( RTS_Business_Logic_4::deactivate( (int) $q['id'], $b['deactivated_by'] ?? null ) ); }
	public static function backup_run( $q )   { $b = self::body( $q ); return self::out( RTS_Business_Logic_4::run_backup( $b['triggered_by'] ?? null ) ); }
	public static function backup_history()   { return rest_ensure_response( RTS_Business_Logic_4::backup_history() ); }
	public static function security()         { return rest_ensure_response( RTS_Business_Logic_4::security_stats() ); }
}
