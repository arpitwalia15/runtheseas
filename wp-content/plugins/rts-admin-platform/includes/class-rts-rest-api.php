<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_REST_API {

	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	public static function register_routes() {
		$ns = 'rts/v1';
		$r = function ( $route, $m, $cb, $cap ) use ( $ns ) { RTS_Auth::route( $ns, $route, $m, array( __CLASS__, $cb ), $cap ); };
		$PUB = RTS_Auth::PUBLIC_ROUTE;

		// Participant-facing flows — cannot carry a login (the survey page and email links are anonymous).
		$r( '/surveys/(?P<id>\d+)/questions',                  'GET',  'get_survey_questions', $PUB );
		$r( '/surveys/(?P<id>\d+)/start',                      'POST', 'start_survey',         $PUB );
		$r( '/responses/(?P<id>\d+)/answers',                  'POST', 'submit_answer',        $PUB );
		$r( '/responses/(?P<id>\d+)/complete',                 'POST', 'complete_survey',      $PUB );
		$r( '/participants/register',                           'POST', 'register_participant', $PUB );
		$r( '/participants/verify/(?P<token>[A-Za-z0-9]+)',     'GET',  'verify_participant',   $PUB );
		$r( '/subscriptions/(?P<token>[A-Za-z0-9]+)',           'GET',  'get_subscriptions',    $PUB ); // token IS the credential
		$r( '/subscriptions/(?P<token>[A-Za-z0-9]+)/unsubscribe',   'POST', 'post_unsubscribe', $PUB );
		$r( '/subscriptions/(?P<token>[A-Za-z0-9]+)/resubscribe',   'POST', 'post_resubscribe', $PUB );

		// Admin — participant PII and platform data.
		$r( '/participants/(?P<id>\d+)',     'GET',  'get_participant',      'rts_view' );
		$r( '/participants',                  'GET',  'list_participants',    'rts_view' );
		$r( '/cabin-credits/summary',         'GET',  'cabin_credit_summary', 'rts_view' );
		$r( '/cabin-credits/void-all',        'POST', 'void_all_credits',     'rts_system' ); // Tier-3 action (spec Appendix B2)
		$r( '/referrals/leaderboard',         'GET',  'referral_leaderboard', 'rts_view' );
		$r( '/reports/executive-summary',     'GET',  'executive_summary',    'rts_view' );
		$r( '/audit-log',                     'GET',  'get_audit_log',        'rts_view' );

		// System / production (Batch 7 production pass)
		$r( '/system/status',                 'GET',  'system_status',        'rts_view' );
		$r( '/system/take-offline',           'POST', 'system_take_offline',  'rts_system' ); // Tier-3
		$r( '/system/restore',                'POST', 'system_restore',       'rts_system' );
		$r( '/ai/draft',                      'POST', 'ai_draft',             'rts_manage' );
		$r( '/founding-runners/import',       'POST', 'fr_import',            'rts_system' );
		$r( '/founding-runners/sync',         'POST', 'fr_sync',              'rts_system' );
		$r( '/email/outbox',                  'GET',  'email_outbox',         'rts_view' );
	}

	// ----- Surveys -----

	public static function get_survey_questions( $req ) {
		global $wpdb;
		$table = RTS_DB::table( 'survey_questions' );
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE survey_id = %d ORDER BY sort_order", $req['id'] ) );
		foreach ( $rows as &$r ) { $r->options_json = $r->options_json ? json_decode( $r->options_json ) : null; }
		return rest_ensure_response( $rows );
	}

	public static function start_survey( $req ) {
		global $wpdb;
		$token = wp_generate_password( 16, false );
		$wpdb->insert( RTS_DB::table( 'survey_responses' ), array(
			'survey_id' => $req['id'], 'session_token' => $token, 'status' => 'in_progress',
		) );
		return rest_ensure_response( array( 'responseId' => $wpdb->insert_id, 'sessionToken' => $token ) );
	}

	public static function submit_answer( $req ) {
		global $wpdb;
		$body = json_decode( $req->get_body(), true );
		if ( empty( $body['question_id'] ) ) { return new WP_REST_Response( array( 'error' => 'QUESTION_ID_REQUIRED' ), 400 ); }
		$question_id = (int) $body['question_id'];
		$response = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'survey_responses' ) . " WHERE id = %d", $req['id'] ) );
		$question = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'survey_questions' ) . " WHERE id = %d", $question_id ) );
		$wpdb->insert( RTS_DB::table( 'survey_answers' ), array(
			'tracking_id' => 0,
			'tracking_submission_id' => $response ? substr( $response->session_token, 0, 36 ) : wp_generate_uuid4(),
			'form_id' => $question && $question->source_form_id ? (int) $question->source_form_id : 0,
			'response_id' => (int) $req['id'],
			'question_id' => $question && $question->source_question_id ? $question->source_question_id : (string) $question_id,
			'question_label' => $question ? $question->prompt : null,
			'question_type' => $question ? $question->question_type : null,
			'platform_question_id' => $question_id,
			'answer_value' => isset( $body['answer_value'] ) ? mb_substr( sanitize_text_field( (string) $body['answer_value'] ), 0, 500 ) : null,
			'comment_text' => isset( $body['comment_text'] ) ? mb_substr( sanitize_textarea_field( (string) $body['comment_text'] ), 0, 5000 ) : null,
		) );
		return rest_ensure_response( array( 'ok' => true ) );
	}

	public static function complete_survey( $req ) {
		global $wpdb;
		$wpdb->update( RTS_DB::table( 'survey_responses' ),
			array( 'status' => 'completed', 'completed_at' => current_time( 'mysql' ) ),
			array( 'id' => $req['id'] )
		);
		return rest_ensure_response( array( 'ok' => true ) );
	}

	// ----- Participants -----

	public static function register_participant( $req ) {
		$rl = RTS_Production::rate_limit( 'register' ); if ( is_wp_error( $rl ) ) { return $rl; }
		$body = json_decode( $req->get_body(), true ) ?: array();
		// Input validation (public route): email must be valid; free-text fields sanitized + length-capped.
		if ( empty( $body['email'] ) || ! is_email( $body['email'] ) ) { return new WP_REST_Response( array( 'error' => 'INVALID_EMAIL' ), 400 ); }
		foreach ( array( 'name','phone','country','registration_country','detected_country','province','registration_province','city','postal_code','address','address_2','date_of_birth','gender','age_range','emergency_contact_name','emergency_contact_phone','household_income_bracket','marketing_source','utm_campaign','referred_by_code','runner_status' ) as $k ) {
			if ( isset( $body[ $k ] ) ) { $body[ $k ] = mb_substr( sanitize_text_field( (string) $body[ $k ] ), 0, 255 ); }
		}
		$body['email'] = sanitize_email( $body['email'] );
		if ( isset( $body['travel_party_size'] ) ) { $body['travel_party_size'] = max( 0, min( 50, (int) $body['travel_party_size'] ) ); }
		if ( isset( $body['runner_status'] ) && ! in_array( $body['runner_status'], array( 'runner', 'non_runner' ), true ) ) { unset( $body['runner_status'] ); }
		$result = RTS_Business_Logic::register_participant( $body );
		if ( $result['error'] ) {
			return new WP_REST_Response( $result, 409 );
		}
		RTS_Production::send_verification( $result['participant_id'] ); // 'log' mode: outbox only; 'send' mode: wp_mail()
		do_action( 'rts_participant_registered', $result['participant_id'], $body, 'rest' );
		return rest_ensure_response( array(
			'error' => null, 'participantId' => $result['participant_id'],
			'verificationToken' => $result['verification_token'], 'referralCode' => $result['referral_code'],
			'unsubscribeToken' => $result['unsubscribe_token'],
		) );
	}

	public static function verify_participant( $req ) {
		$rl = RTS_Production::rate_limit( 'verify' ); if ( is_wp_error( $rl ) ) { return $rl; }
		$result = RTS_Business_Logic::verify_email( $req['token'] );
		if ( $result['error'] ) {
			return new WP_REST_Response( $result, 400 );
		}
		return rest_ensure_response( array(
			'error' => null,
			'participant' => $result['participant'],
			'creditResult' => $result['credit_result'] ?? null,
		) );
	}

	public static function get_participant( $req ) {
		global $wpdb;
		$ptable = RTS_DB::table( 'participants' );
		$participant = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ptable WHERE id = %d", $req['id'] ) );
		if ( ! $participant ) { return new WP_REST_Response( array( 'error' => 'NOT_FOUND' ), 404 ); }

		$rtable = RTS_DB::table( 'referrals' );
		$referrals = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $rtable WHERE referring_participant_id = %d", $req['id'] ) );

		$ttable = RTS_DB::table( 'trophies' );
		$utable = RTS_DB::table( 'trophy_unlocks' );
		$trophies = $wpdb->get_results( $wpdb->prepare(
			"SELECT t.name, tu.unlocked_at FROM $utable tu JOIN $ttable t ON t.id = tu.trophy_id WHERE tu.participant_id = %d", $req['id']
		) );

		$ctable = RTS_DB::table( 'cabin_credits' );
		$credit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ctable WHERE participant_id = %d", $req['id'] ) );

		$response = (array) $participant;
		$response['referrals'] = $referrals;
		$response['trophies'] = $trophies;
		$response['cabin_credit'] = $credit;
		return rest_ensure_response( $response );
	}

	public static function list_participants( $req ) {
		global $wpdb;
		$table = RTS_DB::table( 'participants' );
		return rest_ensure_response( $wpdb->get_results( "SELECT * FROM $table ORDER BY registered_at DESC" ) );
	}

	// ----- Cabin Credits -----

	public static function cabin_credit_summary( $req ) {
		global $wpdb;
		$table = RTS_DB::table( 'cabin_credits' );
		$issued = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status = 'issued'" );
		$redeemed = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status = 'redeemed'" );
		$cancelled = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status IN ('cancelled','void')" );
		return rest_ensure_response( array(
			'issued' => $issued, 'redeemed' => $redeemed, 'cancelled' => $cancelled,
			'outstandingLiability' => $issued * 100,
		) );
	}

	public static function void_all_credits( $req ) {
		$body = json_decode( $req->get_body(), true );
		$result = RTS_Business_Logic::void_all_outstanding_credits( $body['adminUser'] ?? 'admin', $body['reason'] ?? '' );
		return rest_ensure_response( $result );
	}

	// ----- Referrals -----

	public static function referral_leaderboard( $req ) {
		global $wpdb;
		$ptable = RTS_DB::table( 'participants' );
		$rtable = RTS_DB::table( 'referrals' );
		$rows = $wpdb->get_results( "
			SELECT p.id, p.founding_runner_number, p.name,
				SUM(CASE WHEN r.verified = 1 AND r.fraud_review_status = 'clear' THEN 1 ELSE 0 END) as verified_referrals
			FROM $ptable p LEFT JOIN $rtable r ON r.referring_participant_id = p.id
			GROUP BY p.id HAVING verified_referrals > 0 ORDER BY verified_referrals DESC
		" );
		foreach ( $rows as &$r ) { $r->draw_b_eligible = $r->verified_referrals >= 42; }
		return rest_ensure_response( $rows );
	}

	// ----- Executive Summary -----

	public static function executive_summary( $req ) {
		global $wpdb;
		$ptable = RTS_DB::table( 'participants' );
		$rtable = RTS_DB::table( 'referrals' );
		$ctable = RTS_DB::table( 'cabin_credits' );
		$srtable = RTS_DB::table( 'survey_responses' );

		$total_completed = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $srtable WHERE status = 'completed'" );
		$total_started = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $srtable" );
		$completion_rate = $total_started > 0 ? round( ( $total_completed / $total_started ) * 100, 1 ) : 0;

		$k = RTS_Business_Logic::calculate_referral_coefficient();
		$total_referrals = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $rtable" );
		$verified_referrals = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $rtable WHERE verified = 1" );

		$total_participants = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $ptable" );
		$verified_participants = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $ptable WHERE email_verified = 1" );
		$runners = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $ptable WHERE runner_status = 'runner'" );
		$non_runners = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $ptable WHERE runner_status = 'non_runner'" );
		$credits_issued = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $ctable WHERE status = 'issued'" );

		return rest_ensure_response( array(
			'totalSurveysCompleted' => $total_completed,
			'surveyCompletionRate' => $completion_rate,
			'referralCoefficient' => $k,
			'verifiedReferralsTotal' => $verified_referrals,
			'totalReferralsSent' => $total_referrals,
			'totalParticipants' => $total_participants,
			'verifiedParticipants' => $verified_participants,
			'runnersVsNonRunners' => array( 'runners' => $runners, 'nonRunners' => $non_runners ),
			'cabinCreditsIssued' => $credits_issued,
			'cabinCreditFloor' => 400,
			'cabinCreditTarget' => 500,
		) );
	}

	// ----- Subscriptions -----

	public static function get_subscriptions( $req ) {
		$result = RTS_Business_Logic::get_subscription_status( $req['token'] );
		$status_code = $result['error'] ? 404 : 200;
		return new WP_REST_Response( $result, $status_code );
	}

	public static function post_unsubscribe( $req ) {
		$body = json_decode( $req->get_body(), true );
		$result = RTS_Business_Logic::unsubscribe( $req['token'], $body['category'] ?? 'all' );
		return new WP_REST_Response( $result, $result['error'] ? 400 : 200 );
	}

	public static function post_resubscribe( $req ) {
		$body = json_decode( $req->get_body(), true );
		$result = RTS_Business_Logic::resubscribe( $req['token'], $body['category'] ?? 'general' );
		return new WP_REST_Response( $result, $result['error'] ? 400 : 200 );
	}

	// ----- System / production -----
	public static function system_status( $req )       { return rest_ensure_response( RTS_Production::status() ); }
	public static function system_take_offline( $req ) {
		$b = json_decode( $req->get_body(), true ) ?: array();
		if ( ( $b['confirm'] ?? '' ) !== 'OFFLINE' ) { return new WP_REST_Response( array( 'error' => 'CONFIRMATION_REQUIRED', 'message' => 'Send {"confirm":"OFFLINE"} to confirm (type-to-confirm, Tier-3 action).' ), 400 ); }
		return rest_ensure_response( RTS_Production::take_offline( $b['admin_user'] ?? null, $b['message'] ?? null ) );
	}
	public static function system_restore( $req )      { $b = json_decode( $req->get_body(), true ) ?: array(); return rest_ensure_response( RTS_Production::restore( $b['admin_user'] ?? null ) ); }
	public static function ai_draft( $req ) {
		$b = json_decode( $req->get_body(), true ) ?: array();
		$task = in_array( $b['task'] ?? '', array( 'question_reply', 'email_draft' ), true ) ? $b['task'] : 'question_reply';
		$ctx = array( 'question' => sanitize_textarea_field( $b['question'] ?? '' ), 'facts' => sanitize_textarea_field( $b['facts'] ?? '' ), 'brief' => sanitize_textarea_field( $b['brief'] ?? '' ) );
		$r = RTS_Production::ai_draft( $task, $ctx );
		return new WP_REST_Response( $r, empty( $r['error'] ) ? 200 : ( 'AI_NOT_CONFIGURED' === $r['error'] ? 409 : 502 ) );
	}
	public static function fr_import( $req ) { $b = json_decode( $req->get_body(), true ) ?: array(); if ( empty( $b['rows'] ) || ! is_array( $b['rows'] ) ) { return new WP_REST_Response( array( 'error' => 'ROWS_REQUIRED' ), 400 ); } return rest_ensure_response( RTS_Production::fr_import_rows( $b['rows'], $b['source'] ?? 'main_site', $b['imported_by'] ?? null ) ); }
	public static function fr_sync( $req )   { return rest_ensure_response( RTS_Production::fr_sync( 'rest' ) ); }
	public static function email_outbox( $req ) { return rest_ensure_response( RTS_Production::outbox( 50 ) ); }

	// ----- Audit Log -----

	public static function get_audit_log( $req ) {
		global $wpdb;
		$table = RTS_DB::table( 'audit_log' );
		return rest_ensure_response( $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 200" ) );
	}
}
