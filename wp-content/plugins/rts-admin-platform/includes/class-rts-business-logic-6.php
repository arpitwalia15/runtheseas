<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_6 {

	private static function audit( $u, $a, $m, $n = '' ) { RTS_Business_Logic::log_audit( $u ?: 'admin', $a, $m, 'success', $n ); }

	// ---- Customer Feedback ----
	public static function response_breakdown( $qid ) {
		global $wpdb; $qt = RTS_DB::table( 'survey_questions' ); $at = RTS_DB::table( 'survey_answers' );
		$q = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $qt WHERE id = %d", $qid ) );
		if ( ! $q ) { return array( 'error' => 'NOT_FOUND' ); }
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT answer_value, COUNT(*) AS c FROM $at WHERE platform_question_id = %d AND answer_value IS NOT NULL AND answer_value != '' GROUP BY answer_value ORDER BY c DESC", $qid ) );
		$total = array_sum( array_map( fn( $r ) => (int) $r->c, $rows ) );
		return array( 'error' => null, 'question' => $q->prompt, 'breakdown' => array_map( fn( $r ) => array( 'answer' => $r->answer_value, 'count' => (int) $r->c, 'pct' => $total ? round( $r->c / $total * 100, 1 ) : 0 ), $rows ) );
	}

	public static function all_comments( $qid = null, $gender = null ) {
		global $wpdb;
		$sql = "SELECT sa.comment_text, sq.question_number, sq.prompt, p.gender, sa.answered_at FROM " . RTS_DB::table( 'survey_answers' ) . " sa JOIN " . RTS_DB::table( 'survey_questions' ) . " sq ON sq.id = sa.platform_question_id JOIN " . RTS_DB::table( 'survey_responses' ) . " sr ON sr.id = sa.response_id LEFT JOIN " . RTS_DB::table( 'participants' ) . " p ON p.id = sr.participant_id WHERE sa.comment_text IS NOT NULL AND sa.comment_text != ''";
		$params = array();
		if ( $qid )    { $sql .= ' AND sq.id = %d';    $params[] = (int) $qid; }
		if ( $gender ) { $sql .= ' AND p.gender = %s'; $params[] = $gender; }
		$sql .= ' ORDER BY sa.answered_at DESC';
		return $params ? $wpdb->get_results( $wpdb->prepare( $sql, ...$params ) ) : $wpdb->get_results( $sql );
	}

	// Keyword frequency — explicitly NOT NLP/LLM theme clustering. An honest stand-in.
	const STOPWORDS = array( 'the','a','an','and','or','but','is','are','was','were','to','of','in','on','for','with','this','that','it','be','have','has','i','you','we','they','my','your','our','their','if','will','would','can','could','not','no','do','does','did','so','as','at','by','from','about','also','just','really','very','would','love' );
	public static function top_themes( $limit = 10 ) {
		global $wpdb;
		$freq = array(); $example = array();
		foreach ( $wpdb->get_col( "SELECT comment_text FROM " . RTS_DB::table( 'survey_answers' ) . " WHERE comment_text IS NOT NULL AND comment_text != ''" ) as $c ) {
			$words = array_unique( array_filter( preg_split( '/\s+/', preg_replace( '/[^a-z0-9\s]/', '', strtolower( $c ) ) ), fn( $w ) => strlen( $w ) > 3 && ! in_array( $w, self::STOPWORDS, true ) ) );
			foreach ( $words as $w ) { $freq[ $w ] = ( $freq[ $w ] ?? 0 ) + 1; $example[ $w ] ??= $c; }
		}
		arsort( $freq );
		$out = array(); foreach ( array_slice( $freq, 0, $limit, true ) as $w => $n ) { $out[] = array( 'keyword' => $w, 'mentions' => $n, 'example' => $example[ $w ] ); }
		return $out;
	}

	public static function comment_summary() {
		global $wpdb;
		return $wpdb->get_results( "SELECT sq.id, sq.question_number, sq.prompt, COUNT(sa.id) AS comment_count FROM " . RTS_DB::table( 'survey_questions' ) . " sq JOIN " . RTS_DB::table( 'survey_answers' ) . " sa ON sa.platform_question_id = sq.id AND sa.comment_text IS NOT NULL AND sa.comment_text != '' GROUP BY sq.id ORDER BY comment_count DESC" );
	}

	// ---- Question & Response Queue ----
	public static function open_questions() { global $wpdb; return $wpdb->get_results( "SELECT cq.*, p.name AS participant_name, p.email AS participant_email FROM " . RTS_DB::table( 'customer_questions' ) . " cq LEFT JOIN " . RTS_DB::table( 'participants' ) . " p ON p.id = cq.participant_id WHERE cq.status = 'open' ORDER BY cq.created_at ASC" ); }

	public static function create_question( $text, $pid = null, $source = 'manual' ) {
		global $wpdb;
		$wpdb->insert( RTS_DB::table( 'customer_questions' ), array( 'participant_id' => $pid ?: null, 'question_text' => $text, 'source' => $source ) );
		return array( 'error' => null, 'question_id' => (int) $wpdb->insert_id );
	}

	public static function add_draft( $qid, $text, $feedback, $by ) {
		global $wpdb; $dt = RTS_DB::table( 'question_response_drafts' );
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM " . RTS_DB::table( 'customer_questions' ) . " WHERE id = %d", $qid ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$v = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(MAX(version),0) FROM $dt WHERE customer_question_id = %d", $qid ) ) + 1;
		$wpdb->insert( $dt, array( 'customer_question_id' => $qid, 'version' => $v, 'draft_text' => $text, 'feedback_that_prompted_this' => $feedback ?: null, 'created_by' => $by ?: 'admin' ) );
		return array( 'error' => null, 'version' => $v );
	}

	public static function draft_history( $qid ) { global $wpdb; return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'question_response_drafts' ) . " WHERE customer_question_id = %d ORDER BY version ASC", $qid ) ); }

	public static function approve_and_send( $qid, $by ) {
		global $wpdb; $qt = RTS_DB::table( 'customer_questions' );
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $qt WHERE id = %d", $qid ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$drafts = self::draft_history( $qid );
		if ( ! $drafts ) { return array( 'error' => 'NO_DRAFT_TO_SEND' ); }
		$latest = end( $drafts );
		$wpdb->insert( RTS_DB::table( 'question_response_log' ), array( 'customer_question_id' => $qid, 'final_response' => $latest->draft_text, 'version_count' => count( $drafts ), 'approved_by' => $by ?: 'admin' ) );
		$wpdb->update( $qt, array( 'status' => 'answered' ), array( 'id' => $qid ) );
		self::audit( $by, "Response sent to customer question #$qid", 'Question & Response Queue', 'versions=' . count( $drafts ) );
		return array( 'error' => null, 'sent_text' => $latest->draft_text, 'version_count' => count( $drafts ) );
	}

	public static function response_log() { global $wpdb; return $wpdb->get_results( "SELECT l.*, cq.question_text, p.name AS participant_name FROM " . RTS_DB::table( 'question_response_log' ) . " l JOIN " . RTS_DB::table( 'customer_questions' ) . " cq ON cq.id = l.customer_question_id LEFT JOIN " . RTS_DB::table( 'participants' ) . " p ON p.id = cq.participant_id ORDER BY l.sent_at DESC" ); }

	// ---- Who Is The Customer: "customer" = verified + received Cabin Credit (spec definition) ----
	public static function customer_profile() {
		global $wpdb; $pt = RTS_DB::table( 'participants' ); $ct = RTS_DB::table( 'cabin_credits' );
		$base = "FROM $pt p JOIN $ct cc ON cc.participant_id = p.id WHERE p.email_verified = 1";
		$grp = fn( $col ) => $wpdb->get_results( "SELECT p.$col AS k, COUNT(*) AS c $base AND p.$col IS NOT NULL GROUP BY p.$col ORDER BY c DESC" );
		$avg = $wpdb->get_var( "SELECT AVG(p.travel_party_size) $base AND p.travel_party_size IS NOT NULL" );
		return array(
			'total_customers' => (int) $wpdb->get_var( "SELECT COUNT(*) $base" ),
			'gender_breakdown' => $grp( 'gender' ), 'age_breakdown' => $grp( 'age_range' ),
			'income_breakdown' => $grp( 'household_income_bracket' ), 'geographic_distribution' => $grp( 'country' ),
			'acquisition_breakdown' => $grp( 'marketing_source' ), 'runner_breakdown' => $grp( 'runner_status' ),
			'avg_travel_party_size' => $avg ? round( (float) $avg, 1 ) : null,
			'top_themes' => self::top_themes( 5 ),
		);
	}

	// ---- Website Content Management ----
	public static function get_block( $key ) { global $wpdb; return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'content_blocks' ) . " WHERE block_key = %s", $key ) ); }
	public static function all_blocks() { global $wpdb; return $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'content_blocks' ) . " ORDER BY block_key" ); }
	public static function set_block( $key, $value, $by ) {
		global $wpdb;
		$wpdb->replace( RTS_DB::table( 'content_blocks' ), array( 'block_key' => $key, 'value' => $value, 'updated_by' => $by ?: 'admin', 'updated_at' => current_time( 'mysql' ) ) );
		self::audit( $by, "Content block updated: \"$key\"", 'Website Content Management' );
		return array( 'error' => null );
	}

	// ---- Export Center ----
	const DATASETS = array( 'participants', 'cabin_credits', 'referrals', 'comments' );
	private static function dataset_rows( $ds ) {
		global $wpdb;
		switch ( $ds ) {
			case 'participants':  return $wpdb->get_results( "SELECT id, name, email, founding_runner_number, email_verified, runner_status, country, registered_at FROM " . RTS_DB::table( 'participants' ), ARRAY_A );
			case 'cabin_credits': return $wpdb->get_results( "SELECT cc.id, p.name, p.email, cc.status, cc.value_usd, cc.issued_at FROM " . RTS_DB::table( 'cabin_credits' ) . " cc JOIN " . RTS_DB::table( 'participants' ) . " p ON p.id = cc.participant_id", ARRAY_A );
			case 'referrals':     return $wpdb->get_results( "SELECT r.id, p1.name AS referrer, p2.name AS referred, r.verified, r.fraud_review_status FROM " . RTS_DB::table( 'referrals' ) . " r JOIN " . RTS_DB::table( 'participants' ) . " p1 ON p1.id = r.referring_participant_id LEFT JOIN " . RTS_DB::table( 'participants' ) . " p2 ON p2.id = r.referred_participant_id", ARRAY_A );
			case 'comments':      return array_map( fn( $o ) => (array) $o, self::all_comments() );
		}
		return null;
	}
	public static function to_csv( $rows ) {
		if ( ! $rows ) { return ''; }
		$fh = fopen( 'php://temp', 'r+' ); fputcsv( $fh, array_keys( $rows[0] ) );
		foreach ( $rows as $r ) { fputcsv( $fh, array_map( fn( $v ) => $v ?? '', $r ) ); }
		rewind( $fh ); $csv = stream_get_contents( $fh ); fclose( $fh ); return $csv;
	}
	public static function export( $ds, $by ) {
		global $wpdb;
		if ( ! in_array( $ds, self::DATASETS, true ) ) { return array( 'error' => 'INVALID_DATASET', 'available' => self::DATASETS ); }
		$rows = self::dataset_rows( $ds );
		$wpdb->insert( RTS_DB::table( 'export_history' ), array( 'dataset' => $ds, 'format' => 'csv', 'requested_by' => $by ?: 'admin', 'row_count' => count( $rows ) ) );
		self::audit( $by, "Export generated: $ds (" . count( $rows ) . " rows)", 'Export Center' );
		return array( 'error' => null, 'csv' => self::to_csv( $rows ), 'row_count' => count( $rows ) );
	}
	public static function export_history() { global $wpdb; return $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'export_history' ) . " ORDER BY created_at DESC LIMIT 30" ); }
}
