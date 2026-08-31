<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_REST_API_6 {
	public static function init() { add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) ); }
	private static function body( $r ) { return json_decode( $r->get_body(), true ) ?: array(); }
	private static function out( $res, $code = 400 ) { return new WP_REST_Response( $res, ( is_array( $res ) && ! empty( $res['error'] ) ) ? $code : 200 ); }

	public static function register_routes() {
		$ns = 'rts/v1';
		$r = function ( $route, $m, $cb, $cap ) use ( $ns ) { RTS_Auth::route( $ns, $route, $m, array( __CLASS__, $cb ), $cap ); };
		$PUB = RTS_Auth::PUBLIC_ROUTE;
		$r( '/feedback/breakdown/(?P<id>\d+)',  'GET',  'breakdown', 'rts_view' );
		$r( '/feedback/comments',               'GET',  'comments', 'rts_view' );
		$r( '/feedback/themes',                 'GET',  'themes', 'rts_view' );
		$r( '/feedback/comment-summary',        'GET',  'summary', 'rts_view' );
		$r( '/questions/open',                  'GET',  'open_q', 'rts_view' );
		$r( '/questions/response-log',          'GET',  'resp_log', 'rts_view' );  // static BEFORE /questions/(?P<id>) — avoid the Batch-3-Node collision
		$r( '/questions',                       'POST', 'create_q', 'rts_manage' );
		$r( '/questions/(?P<id>\d+)/drafts',    'GET',  'drafts', 'rts_view' );
		$r( '/questions/(?P<id>\d+)/drafts',    'POST', 'add_draft', 'rts_manage' );
		$r( '/questions/(?P<id>\d+)/send',      'POST', 'send_q', 'rts_manage' );
		$r( '/customer-profile',                'GET',  'profile', 'rts_view' );
		$r( '/content-blocks',                  'GET',  'blocks', 'rts_content' );
		$r( '/content-blocks/(?P<key>[a-z0-9_\-]+)', 'GET',  'get_block', $PUB );
		$r( '/content-blocks/(?P<key>[a-z0-9_\-]+)', 'POST', 'set_block', 'rts_content' );
		$r( '/export-center/history',           'GET',  'exp_hist', 'rts_view' );
		$r( '/export-center/download',          'GET',  'download', 'rts_view' );
	}
	public static function breakdown( $q ) { return self::out( RTS_Business_Logic_6::response_breakdown( (int) $q['id'] ), 404 ); }
	public static function comments( $q )  { return rest_ensure_response( RTS_Business_Logic_6::all_comments( $q->get_param( 'question_id' ), $q->get_param( 'gender' ) ) ); }
	public static function themes()        { return rest_ensure_response( RTS_Business_Logic_6::top_themes() ); }
	public static function summary()       { return rest_ensure_response( RTS_Business_Logic_6::comment_summary() ); }
	public static function open_q()        { return rest_ensure_response( RTS_Business_Logic_6::open_questions() ); }
	public static function resp_log()      { return rest_ensure_response( RTS_Business_Logic_6::response_log() ); }
	public static function create_q( $q )  { $b = self::body( $q ); if ( empty( $b['question_text'] ) ) { return self::out( array( 'error' => 'QUESTION_TEXT_REQUIRED' ) ); } return self::out( RTS_Business_Logic_6::create_question( $b['question_text'], $b['participant_id'] ?? null, $b['source'] ?? 'manual' ) ); }
	public static function drafts( $q )    { return rest_ensure_response( RTS_Business_Logic_6::draft_history( (int) $q['id'] ) ); }
	public static function add_draft( $q ) { $b = self::body( $q ); if ( empty( $b['draft_text'] ) ) { return self::out( array( 'error' => 'DRAFT_TEXT_REQUIRED' ) ); } return self::out( RTS_Business_Logic_6::add_draft( (int) $q['id'], $b['draft_text'], $b['feedback'] ?? null, $b['created_by'] ?? null ) ); }
	public static function send_q( $q )    { $b = self::body( $q ); return self::out( RTS_Business_Logic_6::approve_and_send( (int) $q['id'], $b['approved_by'] ?? null ) ); }
	public static function profile()       { return rest_ensure_response( RTS_Business_Logic_6::customer_profile() ); }
	public static function blocks()        { return rest_ensure_response( RTS_Business_Logic_6::all_blocks() ); }
	public static function get_block( $q ) { $b = RTS_Business_Logic_6::get_block( $q['key'] ); return $b ? rest_ensure_response( $b ) : new WP_REST_Response( array( 'error' => 'NOT_FOUND' ), 404 ); }
	public static function set_block( $q ) { $b = self::body( $q ); return self::out( RTS_Business_Logic_6::set_block( $q['key'], $b['value'] ?? '', $b['updated_by'] ?? null ) ); }
	public static function exp_hist()      { return rest_ensure_response( RTS_Business_Logic_6::export_history() ); }
	public static function download( $q ) {
		$r = RTS_Business_Logic_6::export( $q->get_param( 'dataset' ), $q->get_param( 'requested_by' ) ?: 'admin' );
		if ( $r['error'] ) { return self::out( $r ); }
		// Stream raw CSV — rest_ensure_response would JSON-encode it. Must echo + exit like core's download handlers.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $q->get_param( 'dataset' ) ) . '.csv"' );
		echo $r['csv']; exit;
	}
}
