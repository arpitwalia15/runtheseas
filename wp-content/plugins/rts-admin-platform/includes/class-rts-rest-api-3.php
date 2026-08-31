<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_REST_API_3 {

	public static function init() { add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) ); }
	private static function body( $r ) { return json_decode( $r->get_body(), true ) ?: array(); }
	private static function out( $res, $code = 400 ) { return new WP_REST_Response( $res, ( is_array( $res ) && ! empty( $res['error'] ) ) ? $code : 200 ); }

	public static function register_routes() {
		$ns = 'rts/v1';
		$r = function ( $route, $m, $cb, $cap ) use ( $ns ) { RTS_Auth::route( $ns, $route, $m, array( __CLASS__, $cb ), $cap ); };
		$PUB = RTS_Auth::PUBLIC_ROUTE;
		// Cabin credits
		$r( '/cabin-credits/ledger',               'GET',  'ledger', 'rts_view' );
		$r( '/cabin-credits/summary-v2',           'GET',  'summary_v2', 'rts_view' );
		$r( '/cabin-credits/(?P<id>\d+)/defer',    'POST', 'defer', 'rts_manage' );
		$r( '/cabin-credits/(?P<id>\d+)/void',     'POST', 'void_one', 'rts_manage' );
		// Trophies
		$r( '/trophies/stats',                                   'GET',  'trophy_stats', 'rts_view' );
		$r( '/trophies',                                         'POST', 'create_trophy', 'rts_manage' );
		$r( '/trophies/(?P<id>\d+)/eligible-not-unlocked',       'GET',  'eligible', 'rts_view' );
		$r( '/trophies/(?P<id>\d+)/retroactive-unlock',          'POST', 'retro', 'rts_manage' );
		// Referrals / draws
		$r( '/referrals/leaderboard-v2',           'GET',  'leaderboard', 'rts_view' );
		$r( '/referrals/draw/(?P<type>[AB])',      'POST', 'draw', 'rts_system' );
		$r( '/referrals/draw-history',             'GET',  'draw_history', 'rts_view' );
		// Subscriptions (admin) — distinct path from /subscriptions/(?P<token>) to avoid the Batch-3-Node collision
		$r( '/subscription-stats',                                                   'GET',  'sub_stats', 'rts_view' );
		$r( '/subscriptions/(?P<token>[A-Za-z0-9]+)/unsubscribe-with-reason',        'POST', 'unsub_reason', $PUB );
		// Broadcast / send-gate
		$r( '/broadcast/preview',                        'GET',  'preview', 'rts_view' );
		$r( '/bulk-email/drafts',                        'POST', 'create_draft', 'rts_send_bulk' );
		$r( '/bulk-email/drafts/(?P<id>\d+)',            'GET',  'gate', 'rts_view' );
		$r( '/bulk-email/drafts/(?P<id>\d+)/test-self',  'POST', 'test_self', 'rts_send_bulk' );
		$r( '/bulk-email/drafts/(?P<id>\d+)/test-group', 'POST', 'test_group', 'rts_send_bulk' );
		$r( '/bulk-email/drafts/(?P<id>\d+)/send-bulk',  'POST', 'send_bulk', 'rts_send_bulk' );
	}

	public static function ledger()           { return rest_ensure_response( RTS_Business_Logic_3::credit_ledger() ); }
	public static function summary_v2()       { return rest_ensure_response( RTS_Business_Logic_3::credit_summary() ); }
	public static function defer( $q )        { $b = self::body( $q ); return self::out( RTS_Business_Logic_3::defer_credit( $q['id'], $b['admin'] ?? null, $b['reason'] ?? '' ) ); }
	public static function void_one( $q )     { $b = self::body( $q ); return self::out( RTS_Business_Logic_3::void_credit( $q['id'], $b['admin'] ?? null, $b['reason'] ?? '' ) ); }

	public static function trophy_stats()     { return rest_ensure_response( RTS_Business_Logic_3::trophy_stats() ); }
	public static function create_trophy( $q ){ $b = self::body( $q ); if ( empty( $b['name'] ) || empty( $b['unlock_rule'] ) ) { return self::out( array( 'error' => 'NAME_AND_UNLOCK_RULE_REQUIRED' ) ); } return self::out( RTS_Business_Logic_3::create_trophy( $b ) ); }
	public static function eligible( $q )     { return rest_ensure_response( RTS_Business_Logic_3::eligible_not_unlocked( $q['id'] ) ); }
	public static function retro( $q )        { $b = self::body( $q ); if ( empty( $b['participant_ids'] ) ) { return self::out( array( 'error' => 'PARTICIPANT_IDS_REQUIRED' ) ); } return self::out( RTS_Business_Logic_3::retroactive_unlock( $q['id'], $b['participant_ids'], $b['admin'] ?? null ) ); }

	public static function leaderboard()      { return rest_ensure_response( RTS_Business_Logic_3::leaderboard() ); }
	public static function draw( $q )         { $b = self::body( $q ); return self::out( RTS_Business_Logic_3::run_draw( $q['type'], $b['admin'] ?? null ) ); }
	public static function draw_history()     { return rest_ensure_response( RTS_Business_Logic_3::draw_history() ); }

	public static function sub_stats()        { return rest_ensure_response( RTS_Business_Logic_3::subscription_stats() ); }
	public static function unsub_reason( $q ) { $b = self::body( $q ); return self::out( RTS_Business_Logic_3::unsubscribe_with_reason( $q['token'], $b['category'] ?? 'all', $b['reason'] ?? '' ) ); }

	public static function preview( $q )      { return rest_ensure_response( RTS_Business_Logic_3::audience_preview( $q->get_param( 'category' ) ?: 'general', $q->get_param( 'audience_filter' ) ?: 'all' ) ); }
	public static function create_draft( $q ) { $b = self::body( $q ); if ( empty( $b['subject'] ) ) { return self::out( array( 'error' => 'SUBJECT_REQUIRED' ) ); } return self::out( RTS_Business_Logic_3::create_draft( $b ) ); }
	public static function gate( $q )         { return self::out( RTS_Business_Logic_3::gate_status( $q['id'] ), 404 ); }
	public static function test_self( $q )    { $b = self::body( $q ); return self::out( RTS_Business_Logic_3::test_self( $q['id'], $b['admin_email'] ?? '' ) ); }
	public static function test_group( $q )   { $b = self::body( $q ); return self::out( RTS_Business_Logic_3::test_group( $q['id'], $b['test_emails'] ?? '', $b['sent_by'] ?? null ) ); }
	public static function send_bulk( $q )    { $b = self::body( $q ); return self::out( RTS_Business_Logic_3::send_bulk( $q['id'], $b['sent_by'] ?? null, ! empty( $b['force'] ), $b['force_reason'] ?? '' ), 409 ); }
}
