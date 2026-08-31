<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_REST_API_5 {
	public static function init() { add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) ); }
	private static function body( $r ) { return json_decode( $r->get_body(), true ) ?: array(); }
	private static function out( $res, $code = 400 ) { return new WP_REST_Response( $res, ( is_array( $res ) && ! empty( $res['error'] ) ) ? $code : 200 ); }

	public static function register_routes() {
		$ns = 'rts/v1';
		$r = function ( $route, $m, $cb, $cap ) use ( $ns ) { RTS_Auth::route( $ns, $route, $m, array( __CLASS__, $cb ), $cap ); };
		$PUB = RTS_Auth::PUBLIC_ROUTE;
		$r( '/email-campaigns',                              'GET',  'list_campaigns', 'rts_view' );
		$r( '/email-campaigns',                              'POST', 'create_campaign', 'rts_send_bulk' );
		$r( '/email-campaigns/(?P<id>\d+)/status',           'POST', 'campaign_status', 'rts_send_bulk' );
		$r( '/email-campaigns/(?P<id>\d+)/run-trigger-check','POST', 'trigger', 'rts_send_bulk' );
		$r( '/email-reporting/stats',                        'GET',  'reporting', 'rts_view' );
		$r( '/ad-campaigns',                                 'POST', 'create_ad', 'rts_manage' );
		$r( '/ad-campaigns/stats',                           'GET',  'ad_stats', 'rts_view' );
		$r( '/participants/(?P<id>\d+)/notification-preference', 'POST', 'notif_pref', 'rts_manage' );
		$r( '/participants/(?P<id>\d+)/declined-contact',        'POST', 'declined', 'rts_manage' );
		$r( '/notification-list-v2',                         'GET',  'notif_list', 'rts_view' );
		$r( '/declined-contact-list-v2',                     'GET',  'declined_list', 'rts_view' );
		$r( '/fraud/duplicate-scan',                         'GET',  'dup_scan', 'rts_manage' );
		$r( '/fraud/duplicate-review',                       'POST', 'dup_review', 'rts_manage' );
		$r( '/fraud/queue',                                  'GET',  'fraud_queue', 'rts_view' );
		$r( '/fraud/referrals/(?P<id>\d+)/reject',           'POST', 'reject_ref', 'rts_manage' );
	}
	public static function list_campaigns()     { return rest_ensure_response( RTS_Business_Logic_5::list_campaigns() ); }
	public static function create_campaign( $q ){ $b = self::body( $q ); if ( empty( $b['name'] ) ) { return self::out( array( 'error' => 'NAME_REQUIRED' ) ); } return self::out( RTS_Business_Logic_5::create_campaign( $b ) ); }
	public static function campaign_status( $q ){ $b = self::body( $q ); return self::out( RTS_Business_Logic_5::set_campaign_status( (int) $q['id'], $b['status'] ?? '', $b['updated_by'] ?? null ) ); }
	public static function trigger( $q )        { $b = self::body( $q ); return self::out( RTS_Business_Logic_5::run_trigger_check( (int) $q['id'], $b['run_by'] ?? null ) ); }
	public static function reporting()          { return rest_ensure_response( RTS_Business_Logic_5::reporting_stats() ); }
	public static function create_ad( $q )      { $b = self::body( $q ); if ( empty( $b['name'] ) || empty( $b['utm_campaign_code'] ) ) { return self::out( array( 'error' => 'NAME_AND_UTM_REQUIRED' ) ); } return self::out( RTS_Business_Logic_5::create_ad_campaign( $b ) ); }
	public static function ad_stats()           { return rest_ensure_response( RTS_Business_Logic_5::ad_campaign_stats() ); }
	public static function notif_pref( $q )     { $b = self::body( $q ); return self::out( RTS_Business_Logic_5::set_notification_pref( (int) $q['id'], ! empty( $b['wants_notification'] ) ) ); }
	public static function declined( $q )       { $b = self::body( $q ); return self::out( RTS_Business_Logic_5::set_declined_contact( (int) $q['id'], ! empty( $b['declined'] ), $b['reason'] ?? '' ) ); }
	public static function notif_list()         { return rest_ensure_response( RTS_Business_Logic_5::notification_list() ); }
	public static function declined_list()      { return rest_ensure_response( RTS_Business_Logic_5::declined_list() ); }
	public static function dup_scan()           { return rest_ensure_response( RTS_Business_Logic_5::duplicate_scan() ); }
	public static function dup_review( $q )     { $b = self::body( $q ); return self::out( RTS_Business_Logic_5::review_duplicate( (int) ( $b['id_a'] ?? 0 ), (int) ( $b['id_b'] ?? 0 ), $b['decision'] ?? '', $b['reviewed_by'] ?? null ) ); }
	public static function fraud_queue()        { return rest_ensure_response( RTS_Business_Logic_5::fraud_queue() ); }
	public static function reject_ref( $q )     { $b = self::body( $q ); return self::out( RTS_Business_Logic_5::reject_referral( (int) $q['id'], $b['admin'] ?? null, $b['reason'] ?? '' ) ); }
}
