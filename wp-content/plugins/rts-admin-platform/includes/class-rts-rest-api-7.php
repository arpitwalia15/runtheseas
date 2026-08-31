<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_REST_API_7 {
	public static function init() { add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) ); }
	private static function body( $r ) { return json_decode( $r->get_body(), true ) ?: array(); }
	private static function out( $res, $code = 400 ) { return new WP_REST_Response( $res, ( is_array( $res ) && ! empty( $res['error'] ) ) ? $code : 200 ); }

	public static function register_routes() {
		$ns = 'rts/v1';
		$r = function ( $route, $m, $cb, $cap ) use ( $ns ) { RTS_Auth::route( $ns, $route, $m, array( __CLASS__, $cb ), $cap ); };
		$PUB = RTS_Auth::PUBLIC_ROUTE;
		$r( '/reports/preview',                  'POST', 'preview', 'rts_view' );
		$r( '/reports/save',                     'POST', 'save', 'rts_manage' );
		$r( '/reports/saved',                    'GET',  'saved', 'rts_view' );
		$r( '/reports/(?P<id>\d+)/run',          'POST', 'run', 'rts_view' );
		$r( '/segments/preview',                 'POST', 'seg_preview', 'rts_view' );
		$r( '/segments/save',                    'POST', 'seg_save', 'rts_manage' );
		$r( '/segments/saved',                   'GET',  'seg_list', 'rts_view' );
		$r( '/quick-reports',                    'GET',  'quick', 'rts_view' );
		$r( '/action-items/generate',            'POST', 'ai_gen', 'rts_manage' );
		$r( '/action-items',                     'GET',  'ai_list', 'rts_view' );
		$r( '/action-items/(?P<id>\d+)/resolve', 'POST', 'ai_resolve', 'rts_manage' );
		$r( '/cabin-sales-forecast/segments',    'GET',  'forecast', 'rts_view' );
		$r( '/founding-runners/totals',          'GET',  'fr_totals', 'rts_view' );
		$r( '/surveys/(?P<id>\d+)/logic-map',    'GET',  'logic_map', 'rts_view' );
	}
	public static function preview( $q )     { $b = self::body( $q ); return self::out( RTS_Business_Logic_7::preview_report( $b['data_source'] ?? '', $b['fields'] ?? null, $b['filters'] ?? array() ) ); }
	public static function save( $q )        { $b = self::body( $q ); if ( empty( $b['name'] ) || empty( $b['data_source'] ) ) { return self::out( array( 'error' => 'NAME_AND_DATA_SOURCE_REQUIRED' ) ); } return self::out( RTS_Business_Logic_7::save_report( $b ) ); }
	public static function saved()           { return rest_ensure_response( RTS_Business_Logic_7::list_reports() ); }
	public static function run( $q )         { $b = self::body( $q ); return self::out( RTS_Business_Logic_7::run_report( (int) $q['id'], $b['run_by'] ?? null ), 404 ); }
	public static function seg_preview( $q ) { $b = self::body( $q ); return rest_ensure_response( RTS_Business_Logic_7::preview_segment( $b['filters'] ?? array() ) ); }
	public static function seg_save( $q )    { $b = self::body( $q ); if ( empty( $b['name'] ) ) { return self::out( array( 'error' => 'NAME_REQUIRED' ) ); } return self::out( RTS_Business_Logic_7::save_segment( $b['name'], $b['filters'] ?? array(), $b['created_by'] ?? null ) ); }
	public static function seg_list()        { return rest_ensure_response( RTS_Business_Logic_7::list_segments() ); }
	public static function quick()           { return rest_ensure_response( RTS_Business_Logic_7::quick_reports() ); }
	public static function ai_gen()          { return rest_ensure_response( RTS_Business_Logic_7::generate_action_items() ); }
	public static function ai_list( $q )     { return rest_ensure_response( RTS_Business_Logic_7::list_action_items( $q->get_param( 'status' ) ) ); }
	public static function ai_resolve( $q )  { $b = self::body( $q ); return self::out( RTS_Business_Logic_7::resolve_action_item( (int) $q['id'], $b['status'] ?? '', $b['outcome_note'] ?? '', $b['resolved_by'] ?? null ) ); }
	public static function forecast()        { return rest_ensure_response( RTS_Business_Logic_7::forecast_segments() ); }
	public static function fr_totals()       { return rest_ensure_response( RTS_Business_Logic_7::fr_totals() ); }
	public static function logic_map( $q )   { return rest_ensure_response( RTS_Business_Logic_7::logic_map( (int) $q['id'] ) ); }
}
