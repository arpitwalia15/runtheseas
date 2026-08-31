<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_REST_API_2 {

	public static function init() { add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) ); }

	private static function body( $req ) { return json_decode( $req->get_body(), true ) ?: array(); }
	private static function out( $result, $err_code = 400 ) {
		return new WP_REST_Response( $result, ( is_array( $result ) && ! empty( $result['error'] ) ) ? $err_code : 200 );
	}

	public static function register_routes() {
		$ns = 'rts/v1';
		$r = function ( $route, $m, $cb, $cap ) use ( $ns ) { RTS_Auth::route( $ns, $route, $m, array( __CLASS__, $cb ), $cap ); };
		$PUB = RTS_Auth::PUBLIC_ROUTE;
		// Survey Administration
		$r( '/surveys/list',                    'GET',  'list_surveys', 'rts_view' );
		$r( '/surveys/(?P<id>\d+)/clone',        'POST', 'clone_survey', 'rts_manage' );
		$r( '/surveys/(?P<id>\d+)/status',       'POST', 'survey_status', 'rts_manage' );
		// Participant actions
		$r( '/participants/(?P<id>\d+)/update',    'POST', 'update_participant', 'rts_manage' );
		$r( '/participants/(?P<id>\d+)/suspend',   'POST', 'suspend', 'rts_manage' );
		$r( '/participants/(?P<id>\d+)/reinstate', 'POST', 'reinstate', 'rts_manage' );
		$r( '/participants/merge-preview',        'POST', 'merge_preview', 'rts_manage' );
		$r( '/participants/merge-commit',         'POST', 'merge_commit', 'rts_manage' );
		// Verification queue
		$r( '/verification-queue',                        'GET',  'verification_queue', 'rts_view' );
		$r( '/participants/(?P<id>\d+)/manual-verify',    'POST', 'manual_verify', 'rts_manage' );
		// Email templates
		$r( '/email-templates',                         'GET',  'list_templates', 'rts_view' );
		$r( '/email-templates',                         'POST', 'create_template', 'rts_manage' );
		$r( '/email-templates/(?P<id>\d+)',             'GET',  'get_template', 'rts_view' );
		$r( '/email-templates/(?P<id>\d+)/update',      'POST', 'update_template', 'rts_manage' );
		$r( '/email-templates/(?P<id>\d+)/assign',      'POST', 'assign_template', 'rts_manage' );
		$r( '/email-templates/(?P<id>\d+)/versions',    'GET',  'template_versions', 'rts_view' );
		$r( '/email-templates/(?P<id>\d+)/rollback',    'POST', 'rollback_template', 'rts_manage' );
	}

	public static function list_surveys( $req )  { return rest_ensure_response( RTS_Business_Logic_2::list_surveys() ); }
	public static function clone_survey( $req )  { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::clone_survey( $req['id'], $b['new_name'] ?? null, $b['created_by'] ?? null ) ); }
	public static function survey_status( $req ) { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::set_survey_status( $req['id'], $b['status'] ?? '', $b['updated_by'] ?? null ) ); }

	public static function update_participant( $req ) { return self::out( RTS_Business_Logic_2::update_participant( $req['id'], self::body( $req ) ) ); }
	public static function suspend( $req )   { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::set_account_status( $req['id'], 'suspended', $b['admin'] ?? null, $b['reason'] ?? '' ) ); }
	public static function reinstate( $req ) { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::set_account_status( $req['id'], 'active', $b['admin'] ?? null ) ); }
	public static function merge_preview( $req ) { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::merge_duplicates( $b['keep_id'] ?? 0, $b['merge_id'] ?? 0, false ) ); }
	public static function merge_commit( $req )  { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::merge_duplicates( $b['keep_id'] ?? 0, $b['merge_id'] ?? 0, true ) ); }

	public static function verification_queue( $req ) { return rest_ensure_response( RTS_Business_Logic_2::get_verification_queue() ); }
	public static function manual_verify( $req ) { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::manually_verify( $req['id'], $b['admin'] ?? null, $b['reason'] ?? '' ) ); }

	public static function list_templates( $req ) { global $wpdb; return rest_ensure_response( $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'email_templates' ) . " ORDER BY updated_at DESC" ) ); }
	public static function get_template( $req ) {
		global $wpdb;
		$t = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'email_templates' ) . " WHERE id = %d", $req['id'] ) );
		return $t ? rest_ensure_response( $t ) : new WP_REST_Response( array( 'error' => 'NOT_FOUND' ), 404 );
	}
	public static function create_template( $req ) {
		$b = self::body( $req );
		if ( empty( $b['name'] ) || empty( $b['subject'] ) ) { return new WP_REST_Response( array( 'error' => 'NAME_AND_SUBJECT_REQUIRED' ), 400 ); }
		return self::out( RTS_Business_Logic_2::create_template( $b ) );
	}
	public static function update_template( $req )    { return self::out( RTS_Business_Logic_2::update_template( $req['id'], self::body( $req ) ) ); }
	public static function assign_template( $req )    { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::assign_template_action( $req['id'], $b['action_key'] ?? '', $b['admin'] ?? null ) ); }
	public static function template_versions( $req )  { return rest_ensure_response( RTS_Business_Logic_2::template_versions( $req['id'] ) ); }
	public static function rollback_template( $req )  { $b = self::body( $req ); return self::out( RTS_Business_Logic_2::rollback_template( $req['id'], (int) ( $b['to_version'] ?? 0 ), $b['admin'] ?? null ) ); }
}
