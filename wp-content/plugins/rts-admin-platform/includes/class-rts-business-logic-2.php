<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_2 {

	// ---- Survey Administration ----

	public static function clone_survey( $survey_id, $new_name, $created_by ) {
		global $wpdb;
		$st = RTS_DB::table( 'surveys' ); $qt = RTS_DB::table( 'survey_questions' );
		$source = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $st WHERE id = %d", $survey_id ) );
		if ( ! $source ) { return array( 'error' => 'NOT_FOUND' ); }

		$wpdb->insert( $st, array( 'name' => $new_name ?: ( $source->name . ' (copy)' ), 'language' => $source->language, 'status' => 'draft', 'version' => 1 ) );
		$new_id = $wpdb->insert_id;

		$questions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $qt WHERE survey_id = %d ORDER BY sort_order", $survey_id ) );
		$id_map = array();
		foreach ( $questions as $q ) {
			$wpdb->insert( $qt, array(
				'survey_id' => $new_id, 'question_number' => $q->question_number, 'section' => $q->section, 'prompt' => $q->prompt,
				'question_type' => $q->question_type, 'options_json' => $q->options_json, 'required' => $q->required,
				'allow_comment' => $q->allow_comment, 'conditional_on_question_id' => null, 'conditional_equals' => $q->conditional_equals,
				'sort_order' => $q->sort_order,
			) );
			$id_map[ $q->id ] = $wpdb->insert_id;
		}
		// Second pass: remap conditional references to the NEW ids — this is what preserves branching logic.
		foreach ( $questions as $q ) {
			if ( $q->conditional_on_question_id && isset( $id_map[ $q->conditional_on_question_id ] ) ) {
				$wpdb->update( $qt, array( 'conditional_on_question_id' => $id_map[ $q->conditional_on_question_id ] ), array( 'id' => $id_map[ $q->id ] ) );
			}
		}
		RTS_Business_Logic::log_audit( $created_by ?: 'admin', "Survey cloned: \"{$source->name}\"", 'Survey Administration', 'success', "source_id=$survey_id; new_id=$new_id" );
		return array( 'error' => null, 'new_survey_id' => $new_id );
	}

	public static function set_survey_status( $survey_id, $status, $updated_by ) {
		global $wpdb;
		if ( ! in_array( $status, array( 'draft', 'live', 'archived' ), true ) ) { return array( 'error' => 'INVALID_STATUS' ); }
		$st = RTS_DB::table( 'surveys' );
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $st WHERE id = %d", $survey_id ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( $st, array( 'status' => $status ), array( 'id' => $survey_id ) );
		RTS_Business_Logic::log_audit( $updated_by ?: 'admin', "Survey status -> $status", 'Survey Administration', 'success', "survey_id=$survey_id" );
		return array( 'error' => null );
	}

	public static function list_surveys() {
		global $wpdb;
		$st = RTS_DB::table( 'surveys' ); $rt = RTS_DB::table( 'survey_responses' );
		$rows = $wpdb->get_results( "SELECT * FROM $st ORDER BY created_at DESC" );
		foreach ( $rows as &$s ) {
			$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $rt WHERE survey_id = %d", $s->id ) );
			$done  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $rt WHERE survey_id = %d AND status='completed'", $s->id ) );
			$s->total_responses = $total; $s->completed = $done;
			$s->completion_rate = $total ? round( $done / $total * 100, 1 ) : 0;
		}
		return $rows;
	}

	// ---- Participant Profile actions ----

	public static function update_participant( $id, $fields ) {
		global $wpdb;
		$allowed = array( 'name','runner_status','country','province','city','gender','age_range','travel_party_size','household_income_bracket','account_status' );
		$data = array_intersect_key( (array) $fields, array_flip( $allowed ) );
		if ( empty( $data ) ) { return array( 'error' => 'NO_FIELDS_TO_UPDATE' ); }
		$wpdb->update( RTS_DB::table( 'participants' ), $data, array( 'id' => $id ) );
		RTS_Business_Logic::log_audit( 'admin', 'Participant profile updated', 'Participants', 'success', "participant_id=$id; fields=" . implode( ',', array_keys( $data ) ) );
		return array( 'error' => null );
	}

	public static function set_account_status( $id, $status, $admin, $reason = '' ) {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' );
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $pt WHERE id = %d", $id ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( $pt, array( 'account_status' => $status ), array( 'id' => $id ) );
		RTS_Business_Logic::log_audit( $admin ?: 'admin', "Participant $status", 'Participants', 'success', "participant_id=$id; reason=$reason" );
		return array( 'error' => null );
	}

	// Preview by default; commits ONLY when $commit === true. Highest-risk action on the platform.
	public static function merge_duplicates( $keep_id, $merge_id, $commit = false ) {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' ); $rt = RTS_DB::table( 'referrals' );
		$ut = RTS_DB::table( 'trophy_unlocks' ); $ct = RTS_DB::table( 'cabin_credits' );
		if ( (int) $keep_id === (int) $merge_id ) { return array( 'error' => 'CANNOT_MERGE_SAME_RECORD' ); }
		$keep  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $pt WHERE id = %d", $keep_id ) );
		$merge = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $pt WHERE id = %d", $merge_id ) );
		if ( ! $keep || ! $merge ) { return array( 'error' => 'NOT_FOUND' ); }

		$merge_refs    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $rt WHERE referring_participant_id = %d", $merge_id ) );
		$merge_trophies= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $ut WHERE participant_id = %d", $merge_id ) );
		$merge_credit  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ct WHERE participant_id = %d", $merge_id ) );
		$keep_credit   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ct WHERE participant_id = %d", $keep_id ) );

		$preview = array(
			'referrals_to_reassign' => count( $merge_refs ),
			'trophies_to_merge'     => array_map( fn( $t ) => (int) $t->trophy_id, $merge_trophies ),
			'credit_conflict'       => (bool) ( $merge_credit && $keep_credit ),
		);
		if ( ! $commit ) { return array( 'error' => null, 'preview' => $preview, 'dry_run' => true ); }

		if ( $preview['credit_conflict'] ) {
			$wpdb->update( $ct, array( 'status' => 'cancelled' ), array( 'participant_id' => $merge_id ) ); // one credit per person survives
		} elseif ( $merge_credit && ! $keep_credit ) {
			$wpdb->update( $ct, array( 'participant_id' => $keep_id ), array( 'participant_id' => $merge_id ) );
		}
		$wpdb->update( $rt, array( 'referring_participant_id' => $keep_id ), array( 'referring_participant_id' => $merge_id ) );
		foreach ( $merge_trophies as $t ) {
			$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $ut (trophy_id, participant_id, unlocked_at) VALUES (%d, %d, %s)", $t->trophy_id, $keep_id, $t->unlocked_at ) );
		}
		$wpdb->update( $pt, array( 'account_status' => 'suspended' ), array( 'id' => $merge_id ) );
		RTS_Business_Logic::log_audit( 'admin', "Participants merged: kept $keep_id, merged $merge_id", 'Participants', 'success',
			"referrals_reassigned={$preview['referrals_to_reassign']}; trophies_merged=" . count( $preview['trophies_to_merge'] ) . "; credit_conflict=" . (int) $preview['credit_conflict'] );
		return array( 'error' => null, 'preview' => $preview, 'committed' => true );
	}

	// ---- Email Verification Queue ----

	public static function get_verification_queue() {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' );
		$pending = $wpdb->get_results( "SELECT id, name, email, verification_sent_at, founding_runner_number FROM $pt WHERE email_verified = 0 ORDER BY verification_sent_at ASC" );
		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $pt" );
		$verified = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $pt WHERE email_verified = 1" );
		return array( 'pending' => $pending, 'pending_count' => count( $pending ), 'verification_rate' => $total ? round( $verified / $total * 100, 1 ) : 0 );
	}

	// Requires a reason, then delegates to the SAME verify_email() as the real link — so manual
	// verification triggers identical side effects (credit, trophy, referral completion).
	public static function manually_verify( $id, $admin, $reason ) {
		global $wpdb;
		if ( ! $reason ) { return array( 'error' => 'REASON_REQUIRED' ); }
		$p = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'participants' ) . " WHERE id = %d", $id ) );
		if ( ! $p ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( $p->email_verified ) { return array( 'error' => 'ALREADY_VERIFIED' ); }
		RTS_Business_Logic::log_audit( $admin ?: 'admin', 'Manual verification override', 'Email Verification', 'success', "participant_id=$id; reason=$reason" );
		return RTS_Business_Logic::verify_email( $p->verification_token );
	}

	// ---- Email Templates (version history is never destroyed; rollback creates a NEW version) ----

	public static function create_template( $d ) {
		global $wpdb;
		$tt = RTS_DB::table( 'email_templates' ); $vt = RTS_DB::table( 'email_template_versions' );
		$wpdb->insert( $tt, array( 'name' => $d['name'], 'category' => $d['category'] ?? 'general', 'subject' => $d['subject'],
			'html_body' => $d['html_body'] ?? '', 'plain_text_body' => $d['plain_text_body'] ?? '', 'status' => 'draft', 'version' => 1 ) );
		$id = $wpdb->insert_id;
		$wpdb->insert( $vt, array( 'template_id' => $id, 'version' => 1, 'subject' => $d['subject'], 'html_body' => $d['html_body'] ?? '', 'plain_text_body' => $d['plain_text_body'] ?? '' ) );
		RTS_Business_Logic::log_audit( $d['created_by'] ?? 'admin', "Email template created: \"{$d['name']}\"", 'Email Templates', 'success', "template_id=$id" );
		return array( 'error' => null, 'template_id' => $id );
	}

	public static function update_template( $id, $d ) {
		global $wpdb;
		$tt = RTS_DB::table( 'email_templates' ); $vt = RTS_DB::table( 'email_template_versions' );
		$t = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tt WHERE id = %d", $id ) );
		if ( ! $t ) { return array( 'error' => 'NOT_FOUND' ); }
		$v = (int) $t->version + 1;
		$subject = $d['subject'] ?? $t->subject; $html = $d['html_body'] ?? $t->html_body; $plain = $d['plain_text_body'] ?? $t->plain_text_body;
		$wpdb->update( $tt, array( 'subject' => $subject, 'html_body' => $html, 'plain_text_body' => $plain, 'version' => $v, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ) );
		$wpdb->insert( $vt, array( 'template_id' => $id, 'version' => $v, 'subject' => $subject, 'html_body' => $html, 'plain_text_body' => $plain ) );
		RTS_Business_Logic::log_audit( $d['updated_by'] ?? 'admin', "Email template updated (v$v): \"{$t->name}\"", 'Email Templates', 'success', "template_id=$id" );
		return array( 'error' => null, 'new_version' => $v );
	}

	public static function template_versions( $id ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'email_template_versions' ) . " WHERE template_id = %d ORDER BY version DESC", $id ) );
	}

	public static function rollback_template( $id, $to_version, $admin ) {
		global $wpdb;
		$tt = RTS_DB::table( 'email_templates' ); $vt = RTS_DB::table( 'email_template_versions' );
		$vr = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $vt WHERE template_id = %d AND version = %d", $id, $to_version ) );
		if ( ! $vr ) { return array( 'error' => 'VERSION_NOT_FOUND' ); }
		$t = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tt WHERE id = %d", $id ) );
		$v = (int) $t->version + 1; // rollback = NEW version with old content; history is append-only
		$wpdb->update( $tt, array( 'subject' => $vr->subject, 'html_body' => $vr->html_body, 'plain_text_body' => $vr->plain_text_body, 'version' => $v, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ) );
		$wpdb->insert( $vt, array( 'template_id' => $id, 'version' => $v, 'subject' => $vr->subject, 'html_body' => $vr->html_body, 'plain_text_body' => $vr->plain_text_body ) );
		RTS_Business_Logic::log_audit( $admin ?: 'admin', "Email template rolled back to v$to_version (now v$v): \"{$t->name}\"", 'Email Templates', 'success', "template_id=$id" );
		return array( 'error' => null, 'new_version' => $v );
	}
}
