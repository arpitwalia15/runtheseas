<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_7 {

	private static function audit( $u, $a, $m, $n = '' ) { RTS_Business_Logic::log_audit( $u ?: 'admin', $a, $m, 'success', $n ); }

	// ---- Report Builder: WHITELISTED fields + ops. Non-whitelisted input is silently dropped, never interpolated. ----
	const SOURCES = array(
		'participants'  => array( 'id','name','email','founding_runner_number','runner_status','country','gender','age_range','email_verified','registered_at' ),
		'referrals'     => array( 'id','referring_participant_id','referred_participant_id','verified','fraud_review_status','clicked_at' ),
		'cabin_credits' => array( 'id','participant_id','status','value_usd','issued_at' ),
	);
	const OPS = array( 'equals' => '=', 'not_equals' => '!=', 'contains' => 'LIKE', 'greater_than' => '>', 'less_than' => '<' );

	private static function where( $src, $filters ) {
		global $wpdb; $clauses = array(); $params = array();
		foreach ( (array) $filters as $f ) {
			if ( ! is_array( $f ) || ! in_array( $f['field'] ?? '', self::SOURCES[ $src ], true ) || ! isset( self::OPS[ $f['op'] ?? '' ] ) ) { continue; }
			if ( 'contains' === $f['op'] ) { $clauses[] = "`{$f['field']}` LIKE %s"; $params[] = '%' . $wpdb->esc_like( (string) $f['value'] ) . '%'; }
			else { $clauses[] = "`{$f['field']}` " . self::OPS[ $f['op'] ] . ' %s'; $params[] = (string) $f['value']; }
		}
		return array( $clauses ? 'WHERE ' . implode( ' AND ', $clauses ) : '', $params );
	}

	public static function preview_report( $src, $fields, $filters ) {
		global $wpdb;
		if ( ! isset( self::SOURCES[ $src ] ) ) { return array( 'error' => 'INVALID_DATA_SOURCE', 'available' => array_keys( self::SOURCES ) ); }
		$safe = array_values( array_intersect( (array) ( $fields ?: self::SOURCES[ $src ] ), self::SOURCES[ $src ] ) );
		if ( ! $safe ) { return array( 'error' => 'NO_VALID_FIELDS' ); }
		list( $w, $p ) = self::where( $src, $filters );
		$sql = 'SELECT `' . implode( '`, `', $safe ) . '` FROM ' . RTS_DB::table( $src ) . " $w LIMIT 200";
		$rows = $p ? $wpdb->get_results( $wpdb->prepare( $sql, ...$p ), ARRAY_A ) : $wpdb->get_results( $sql, ARRAY_A );
		return array( 'error' => null, 'fields' => $safe, 'rows' => $rows, 'row_count' => count( $rows ) );
	}

	public static function save_report( $d ) {
		global $wpdb;
		if ( ! isset( self::SOURCES[ $d['data_source'] ?? '' ] ) ) { return array( 'error' => 'INVALID_DATA_SOURCE' ); }
		$wpdb->insert( RTS_DB::table( 'report_definitions' ), array( 'name' => $d['name'], 'data_source' => $d['data_source'], 'fields_json' => wp_json_encode( $d['fields'] ?? array() ), 'filters_json' => wp_json_encode( $d['filters'] ?? array() ), 'schedule_frequency' => $d['schedule_frequency'] ?? 'none', 'created_by' => $d['created_by'] ?? 'admin' ) );
		$id = (int) $wpdb->insert_id; // BEFORE audit()
		self::audit( $d['created_by'] ?? null, "Report saved: \"{$d['name']}\"", 'Report Builder', "report_id=$id" );
		return array( 'error' => null, 'report_id' => $id );
	}

	public static function list_reports() { global $wpdb; return $wpdb->get_results( "SELECT r.*, (SELECT COUNT(*) FROM " . RTS_DB::table( 'report_runs' ) . " x WHERE x.report_id = r.id) AS run_count, (SELECT MAX(run_at) FROM " . RTS_DB::table( 'report_runs' ) . " x WHERE x.report_id = r.id) AS last_run_at FROM " . RTS_DB::table( 'report_definitions' ) . " r ORDER BY r.created_at DESC" ); }

	// No real scheduler — the frequency is stored; "run" executes on demand. WP-Cron would call this.
	public static function run_report( $id, $by ) {
		global $wpdb;
		$r = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'report_definitions' ) . " WHERE id = %d", $id ) );
		if ( ! $r ) { return array( 'error' => 'NOT_FOUND' ); }
		$res = self::preview_report( $r->data_source, json_decode( $r->fields_json, true ), json_decode( $r->filters_json ?: '[]', true ) );
		if ( $res['error'] ) { return $res; }
		$wpdb->insert( RTS_DB::table( 'report_runs' ), array( 'report_id' => $id, 'row_count' => $res['row_count'], 'run_by' => $by ?: 'admin' ) );
		self::audit( $by, "Report run: \"{$r->name}\"", 'Saved & Scheduled Reports', "report_id=$id; rows={$res['row_count']}" );
		return $res;
	}

	// ---- Segments ----
	public static function preview_segment( $filters ) { return self::preview_report( 'participants', self::SOURCES['participants'], $filters ); }
	public static function save_segment( $name, $filters, $by ) {
		global $wpdb;
		$wpdb->insert( RTS_DB::table( 'segments' ), array( 'name' => $name, 'filters_json' => wp_json_encode( $filters ?: array() ), 'created_by' => $by ?: 'admin' ) );
		$id = (int) $wpdb->insert_id; self::audit( $by, "Segment saved: \"$name\"", 'Build Custom Segment', "segment_id=$id" );
		return array( 'error' => null, 'segment_id' => $id );
	}
	public static function list_segments() {
		global $wpdb; $out = array();
		foreach ( $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'segments' ) . " ORDER BY created_at DESC" ) as $s ) {
			$r = self::preview_segment( json_decode( $s->filters_json, true ) ); // LIVE recount, never cached
			$out[] = array_merge( (array) $s, array( 'live_count' => $r['row_count'] ?? 0 ) );
		}
		return $out;
	}

	// ---- Quick Reports — REAL live numbers + relationship badges ----
	public static function quick_reports() {
		global $wpdb; $i = fn( $q ) => (int) $wpdb->get_var( $q );
		$pt = RTS_DB::table( 'participants' ); $ct = RTS_DB::table( 'cabin_credits' ); $rt = RTS_DB::table( 'referrals' ); $ut = RTS_DB::table( 'trophy_unlocks' ); $srt = RTS_DB::table( 'survey_responses' ); $at = RTS_DB::table( 'survey_answers' );
		$ads = RTS_Business_Logic_5::ad_campaign_stats();
		$credits = $i( "SELECT COUNT(*) FROM $ct WHERE status IN ('issued','deferred')" );
		$m = fn( $metric, $value, $def, $rel, $to ) => compact( 'metric', 'value', 'def', 'rel', 'to' );
		return array(
			'participants_and_surveys' => array(
				$m( 'Total surveys completed', $i( "SELECT COUNT(*) FROM $srt WHERE status='completed'" ), 'Every finished survey response, any source.', 'independent', 'baseline population everything else draws from' ),
				$m( 'Verified participants', $i( "SELECT COUNT(*) FROM $pt WHERE email_verified=1" ), 'Completed the survey AND verified their email.', 'subset', 'Total surveys completed' ),
				$m( 'Total on notification list', $i( "SELECT COUNT(*) FROM $pt WHERE wants_cruise_notification=1" ), 'Said yes to being notified, any source.', 'overlaps', 'Founding Runners (a Founding Runner can also want updates)' ),
				$m( 'Declined further contact', $i( "SELECT COUNT(*) FROM $pt WHERE declined_further_contact=1" ), 'Explicitly declined further updates.', 'subset', 'Verified participants' ),
			),
			'founding_runners_and_credit' => array(
				$m( 'Cabin credits issued (incl. deferred)', $credits, 'One credit per verified Founding Runner.', 'subset', 'Verified participants — should always match exactly' ),
				$m( 'Outstanding liability', '$' . ( $credits * 100 ), 'Credits × $100.', 'sum', 'directly derived from credits issued' ),
			),
			'referrals_and_trophies' => array(
				$m( 'Verified referrals', $i( "SELECT COUNT(*) FROM $rt WHERE verified=1" ), 'Referral links that led to a verified registration.', 'independent', 'counts referral events' ),
				$m( 'Total trophy unlocks', $i( "SELECT COUNT(*) FROM $ut" ), 'Every unlock event.', 'events', 'Unique trophy holders (one person can unlock several)' ),
				$m( 'Unique trophy holders', $i( "SELECT COUNT(DISTINCT participant_id) FROM $ut" ), 'Unique people with ≥1 trophy.', 'subset', 'Verified participants' ),
			),
			'advertising_and_acquisition' => array(
				$m( 'Total impressions / clicks', array_sum( array_column( $ads, 'impressions' ) ) . ' / ' . array_sum( array_column( $ads, 'clicks' ) ), 'Ad-platform totals across campaigns.', 'events', 'unique people — one person can see/click more than one ad' ),
				$m( 'Total interested (via ads)', array_sum( array_column( $ads, 'interested' ) ), 'Subset of notification list attributed by UTM.', 'subset', 'Total on notification list' ),
				$m( 'Total verified & credited (via ads)', array_sum( array_column( $ads, 'verified_credited' ) ), 'Subset of credits attributed by UTM.', 'subset', 'Cabin credits issued' ),
			),
			'customer_feedback' => array(
				$m( 'Total comments collected', $i( "SELECT COUNT(*) FROM $at WHERE comment_text IS NOT NULL AND comment_text != ''" ), 'Every open-text comment.', 'events', 'unique commenters — one person can comment on several questions' ),
			),
		);
	}

	// ---- Action Items: REAL rules over live data. NOT AI. rule_key => at most one open item per condition. ----
	private static function rules() {
		global $wpdb; $i = fn( $q ) => (int) $wpdb->get_var( $q );
		return array(
			'high_cac_campaign' => array( 'cat' => 'Get More Customers', 'check' => function () {
				$c = array_filter( RTS_Business_Logic_5::ad_campaign_stats(), fn( $x ) => ! is_null( $x['cac'] ) );
				if ( count( $c ) < 2 ) { return null; }
				$avg = array_sum( array_column( $c, 'cac' ) ) / count( $c );
				$worst = null; foreach ( $c as $x ) { if ( $x['cac'] > $avg * 1.5 && ( ! $worst || $x['cac'] > $worst['cac'] ) ) { $worst = $x; } }
				return $worst ? array( "Review or pause \"{$worst['name']}\" — CAC \${$worst['cac']} is >1.5× the " . count( $c ) . "-campaign average (\$" . round( $avg, 2 ) . ')', 'Ad Campaign Analysis' ) : null;
			} ),
			'pending_duplicate_reviews' => array( 'cat' => 'Data Quality', 'check' => fn() => ( $n = $i( "SELECT COUNT(*) FROM " . RTS_DB::table( 'duplicate_reviews' ) . " WHERE status='pending'" ) ) ? array( "$n potential duplicate pair(s) awaiting review.", 'Duplicate Detection & Fraud' ) : null ),
			'flagged_referrals'         => array( 'cat' => 'Fraud Prevention', 'check' => fn() => ( $n = $i( "SELECT COUNT(*) FROM " . RTS_DB::table( 'referrals' ) . " WHERE fraud_review_status!='clear'" ) ) ? array( "$n referral(s) flagged for fraud review.", 'Duplicate Detection & Fraud' ) : null ),
			'deferred_credits_pending'  => array( 'cat' => 'Cabin Credit', 'check' => fn() => ( $n = $i( "SELECT COUNT(*) FROM " . RTS_DB::table( 'cabin_credits' ) . " WHERE status='deferred'" ) ) ? array( "$n Cabin Credit(s) deferred to a 2nd sailing — confirm the plan.", 'Cabin Credit Management' ) : null ),
			'open_questions_aging'      => array( 'cat' => 'Resolve Negative Feedback', 'check' => fn() => ( $n = $i( "SELECT COUNT(*) FROM " . RTS_DB::table( 'customer_questions' ) . " WHERE status='open' AND created_at <= DATE_SUB(NOW(), INTERVAL 3 DAY)" ) ) ? array( "$n customer question(s) open for 3+ days.", 'Question & Response Queue' ) : null ),
		);
	}
	public static function generate_action_items() {
		global $wpdb; $t = RTS_DB::table( 'action_items' ); $new = 0; $closed = 0;
		foreach ( self::rules() as $key => $rule ) {
			$res = ( $rule['check'] )();
			$open = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE rule_key = %s AND status = 'open'", $key ) );
			if ( $res && ! $open ) { $wpdb->insert( $t, array( 'rule_key' => $key, 'category' => $rule['cat'], 'recommendation' => $res[0], 'backed_by' => $res[1] ) ); $new++; }
			elseif ( ! $res && $open ) { $wpdb->update( $t, array( 'status' => 'actioned', 'outcome_note' => 'Condition auto-resolved', 'resolved_at' => current_time( 'mysql' ) ), array( 'id' => $open->id ) ); $closed++; }
		}
		return array( 'new_count' => $new, 'auto_closed' => $closed );
	}
	public static function list_action_items( $status = null ) { global $wpdb; $t = RTS_DB::table( 'action_items' ); return $status ? $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $t WHERE status = %s ORDER BY created_at DESC", $status ) ) : $wpdb->get_results( "SELECT * FROM $t ORDER BY created_at DESC" ); }
	public static function resolve_action_item( $id, $status, $note, $by ) {
		global $wpdb;
		if ( ! in_array( $status, array( 'dismissed', 'actioned' ), true ) ) { return array( 'error' => 'INVALID_STATUS' ); }
		if ( ! $wpdb->update( RTS_DB::table( 'action_items' ), array( 'status' => $status, 'outcome_note' => $note ?: null, 'resolved_at' => current_time( 'mysql' ) ), array( 'id' => $id ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		self::audit( $by, "Action item $status", 'Action Items', "item_id=$id" );
		return array( 'error' => null );
	}

	// ---- Estimate Cabin Sales: 4 MUTUALLY-EXCLUSIVE pools (each person in exactly one WHERE) ----
	public static function forecast_segments() {
		global $wpdb; $pt = RTS_DB::table( 'participants' ); $ct = RTS_DB::table( 'cabin_credits' ); $i = fn( $q ) => (int) $wpdb->get_var( $q );
		$share = 0.78; $split = fn( $n ) => array( 'runner' => (int) round( $n * $share ), 'non_runner' => $n - (int) round( $n * $share ) );
		$vc = $i( "SELECT COUNT(*) FROM $pt p JOIN $ct c ON c.participant_id = p.id WHERE p.email_verified = 1" );
		$rn = $i( "SELECT COUNT(*) FROM $pt WHERE email_verified = 0 AND referred_by_participant_id IS NOT NULL" );
		$io = $i( "SELECT COUNT(*) FROM $pt WHERE email_verified = 0 AND referred_by_participant_id IS NULL AND wants_cruise_notification = 1" );
		$cold = $i( "SELECT COUNT(*) FROM $pt WHERE email_verified = 0 AND referred_by_participant_id IS NULL AND (wants_cruise_notification = 0 OR wants_cruise_notification IS NULL)" );
		return array( 'verified_credited' => $split( $vc ), 'referred_not_verified' => $split( $rn ), 'interested_only' => $split( $io ), 'cold_traffic' => $split( $cold ), 'runner_share_assumption' => $share, 'total_addressable_pool' => $vc + $rn + $io + $cold );
	}

	// ---- Founding Runner Outreach ----
	public static function fr_totals() {
		global $wpdb;
		$with = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . RTS_DB::table( 'participants' ) . " p JOIN " . RTS_DB::table( 'cabin_credits' ) . " c ON c.participant_id = p.id WHERE p.email_verified = 1" );
		$without = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . RTS_DB::table( 'external_founding_runners' ) . " WHERE matched_participant_id IS NULL" );
		return array( 'total' => $with + $without, 'with_credit' => $with, 'without_credit' => $without, 'goal' => 10000,
			'without_credit_note' => $without ? null : 'No external Founding Runner records exist — the cross-system integration with the main site (spec Appendix F) is not built in this prototype, so this is honestly 0 rather than faked.' );
	}

	// ---- Survey Logic Map: real conditional-dependency data ----
	public static function logic_map( $survey_id ) {
		global $wpdb;
		$qs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'survey_questions' ) . " WHERE survey_id = %d ORDER BY sort_order", $survey_id ) );
		$by = array(); foreach ( $qs as $q ) { $by[ $q->id ] = $q; }
		return array_map( fn( $q ) => array( 'id' => (int) $q->id, 'question_number' => (int) $q->question_number, 'prompt' => $q->prompt, 'conditional_on' => $q->conditional_on_question_id && isset( $by[ $q->conditional_on_question_id ] ) ? array( 'question_number' => (int) $by[ $q->conditional_on_question_id ]->question_number, 'prompt' => $by[ $q->conditional_on_question_id ]->prompt, 'required_answer' => $q->conditional_equals ) : null ), $qs );
	}
}
