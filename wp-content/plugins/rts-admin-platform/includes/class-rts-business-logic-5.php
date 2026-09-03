<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_5 {

	private static function audit( $u, $a, $m, $n = '' ) { RTS_Business_Logic::log_audit( $u ?: 'admin', $a, $m, 'success', $n ); }

	/** Normalize a JSON string, CSV string or array into unique participant IDs. */
	public static function recipient_ids( $value ) {
		if ( is_string( $value ) ) {
			$decoded = json_decode( $value, true );
			$value = is_array( $decoded ) ? $decoded : preg_split( '/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY );
		}
		if ( ! is_array( $value ) ) { return array(); }
		return array_values( array_unique( array_filter( array_map( 'absint', $value ) ) ) );
	}

	/** Normalize conditional campaign exclusions into safe, extensible rules. */
	public static function exclusion_rules( $value ) {
		if ( is_string( $value ) ) { $value = json_decode( $value, true ); }
		if ( ! is_array( $value ) ) { return array(); }
		$metrics = array( 'total_referral_bonus', 'successful_referrals', 'referral_count', 'captain_miles_balance', 'total_captain_miles_earned' );
		$operators = array( '>=', '>', '=', '<=', '<' );
		$rules = array();
		foreach ( $value as $rule ) {
			if ( ! is_array( $rule ) || ! in_array( $rule['metric'] ?? '', $metrics, true ) || ! in_array( $rule['operator'] ?? '', $operators, true ) ) { continue; }
			$rules[] = array( 'metric' => $rule['metric'], 'operator' => $rule['operator'], 'value' => max( 0, (int) ( $rule['value'] ?? 0 ) ) );
		}
		return $rules;
	}

	/** All participants needed by the searchable audience picker, including consent state per category. */
	public static function campaign_recipient_candidates() {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' );
		$st = RTS_DB::table( 'subscriptions' );
		$rows = $wpdb->get_results( "SELECT p.id, p.name, p.email, p.email_verified, p.runner_status, p.declined_further_contact,
			COALESCE(p.total_referral_bonus, 0) AS total_referral_bonus,
			COALESCE(p.successful_referrals, 0) AS successful_referrals,
			COALESCE(p.referral_count, 0) AS referral_count,
			COALESCE(p.captain_miles_balance, 0) AS captain_miles_balance,
			COALESCE(p.total_captain_miles_earned, 0) AS total_captain_miles_earned,
			EXISTS(SELECT 1 FROM $st s WHERE s.participant_id = p.id AND s.category = 'general' AND s.subscribed = 1) AS subscribed_general,
			EXISTS(SELECT 1 FROM $st s WHERE s.participant_id = p.id AND s.category = 'survey' AND s.subscribed = 1) AS subscribed_survey,
			EXISTS(SELECT 1 FROM $st s WHERE s.participant_id = p.id AND s.category = 'referral' AND s.subscribed = 1) AS subscribed_referral,
			EXISTS(SELECT 1 FROM $st s WHERE s.participant_id = p.id AND s.category = 'trophy' AND s.subscribed = 1) AS subscribed_trophy
			FROM $pt p ORDER BY p.name ASC, p.email ASC" );
		return is_array( $rows ) ? $rows : array();
	}

	/** Build the final consent-safe campaign audience with optional legacy overrides and conditional exclusions. */
	public static function campaign_audience( $category, $filter, $include_ids = array(), $exclude_ids = array(), $eligible_ids = null, $exclusion_rules = array() ) {
		$include_ids = self::recipient_ids( $include_ids );
		$exclude_ids = self::recipient_ids( $exclude_ids );
		$exclusion_rules = self::exclusion_rules( $exclusion_rules );
		$filter = in_array( $filter, array( 'all', 'verified_only', 'runners_only', 'non_runners_only' ), true ) ? $filter : 'all';
		$where = '';
		$params = array();

		if ( null !== $eligible_ids ) {
			$eligible_ids = self::recipient_ids( $eligible_ids );
			if ( ! $eligible_ids ) {
				return array( 'category' => $category, 'total_matching_filter' => 0, 'excluded_unsubscribed' => 0, 'excluded_by_conditions' => 0, 'final_recipients' => array(), 'final_recipient_count' => 0 );
			}
			$where .= ' AND p.id IN (' . implode( ',', array_fill( 0, count( $eligible_ids ), '%d' ) ) . ')';
			$params = array_merge( $params, $eligible_ids );
		}

		$segment_sql = '1=1';
		$segment_params = array();
		if ( 'runners_only' === $filter ) {
			$segment_sql = 'p.runner_status = %s';
			$segment_params[] = 'runner';
		} elseif ( 'non_runners_only' === $filter ) {
			$segment_sql = 'p.runner_status = %s';
			$segment_params[] = 'non_runner';
		}

		if ( $include_ids ) {
			$where .= ' AND ((' . $segment_sql . ') OR p.id IN (' . implode( ',', array_fill( 0, count( $include_ids ), '%d' ) ) . '))';
			$params = array_merge( $params, $segment_params, $include_ids );
		} else {
			$where .= ' AND (' . $segment_sql . ')';
			$params = array_merge( $params, $segment_params );
		}

		if ( $exclude_ids ) {
			$where .= ' AND p.id NOT IN (' . implode( ',', array_fill( 0, count( $exclude_ids ), '%d' ) ) . ')';
			$params = array_merge( $params, $exclude_ids );
		}
		$before_conditions = RTS_Business_Logic::get_bulk_email_audience( $category, $where, $params );

		foreach ( $exclusion_rules as $rule ) {
			// Both the column and operator are selected from strict allowlists in exclusion_rules().
			$where .= ' AND NOT (COALESCE(p.' . $rule['metric'] . ', 0) ' . $rule['operator'] . ' %d)';
			$params[] = $rule['value'];
		}

		$audience = $exclusion_rules ? RTS_Business_Logic::get_bulk_email_audience( $category, $where, $params ) : $before_conditions;
		$audience['total_matching_filter'] = $before_conditions['total_matching_filter'];
		$audience['excluded_unsubscribed'] = $before_conditions['excluded_unsubscribed'];
		$audience['excluded_by_conditions'] = max( 0, $before_conditions['final_recipient_count'] - $audience['final_recipient_count'] );
		return $audience;
	}

	// ---- Email Campaign Builder: automated, triggered (NOT immediate like Broadcast) ----

	public static function create_campaign( $d ) {
		$d['status'] = 'draft';
		return self::save_campaign( $d );
	}

	/** Create or update a campaign while keeping its reusable email template separate. */
	public static function save_campaign( $d ) {
		global $wpdb;
		$name = trim( (string) ( $d['name'] ?? '' ) );
		$template_id = absint( $d['template_id'] ?? 0 );
		$delivery_mode = in_array( $d['delivery_mode'] ?? '', array( 'manual', 'automation', 'scheduled' ), true ) ? $d['delivery_mode'] : 'automation';
		$trigger_type = in_array( $d['trigger_type'] ?? '', array( 'days_after_registration', 'days_after_verification' ), true ) ? $d['trigger_type'] : 'days_after_registration';
		$audience_filter = in_array( $d['audience_filter'] ?? '', array( 'all', 'runners_only', 'non_runners_only', 'verified_only' ), true ) ? $d['audience_filter'] : 'all';
		$category = in_array( $d['category'] ?? '', array( 'general', 'survey', 'referral', 'trophy' ), true ) ? $d['category'] : 'general';
		$status = in_array( $d['status'] ?? '', array( 'draft', 'active', 'paused', 'completed', 'archived' ), true ) ? $d['status'] : 'draft';
		if ( '' === $name ) { return array( 'error' => 'NAME_REQUIRED' ); }
		if ( $template_id && ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM " . RTS_DB::table( 'email_templates' ) . " WHERE id = %d", $template_id ) ) ) {
			return array( 'error' => 'TEMPLATE_NOT_FOUND' );
		}
		if ( 'scheduled' === $delivery_mode && 'active' === $status && empty( $d['scheduled_at'] ) ) { return array( 'error' => 'SCHEDULE_REQUIRED' ); }

		$row = array(
			'name' => $name, 'template_id' => $template_id, 'delivery_mode' => $delivery_mode,
			'trigger_type' => $trigger_type, 'trigger_days' => max( 0, (int) ( $d['trigger_days'] ?? 0 ) ),
			'audience_filter' => $audience_filter, 'category' => $category,
			'recipient_include_ids' => wp_json_encode( self::recipient_ids( $d['recipient_include_ids'] ?? array() ) ),
			'recipient_exclude_ids' => wp_json_encode( self::recipient_ids( $d['recipient_exclude_ids'] ?? array() ) ),
			'exclusion_rules' => wp_json_encode( self::exclusion_rules( $d['exclusion_rules'] ?? array() ) ),
			'scheduled_at' => ! empty( $d['scheduled_at'] ) ? $d['scheduled_at'] : null,
			'status' => $status, 'updated_at' => current_time( 'mysql' ),
		);
		$id = absint( $d['id'] ?? 0 );
		if ( $id ) {
			if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM " . RTS_DB::table( 'email_campaigns' ) . " WHERE id = %d", $id ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
			$wpdb->update( RTS_DB::table( 'email_campaigns' ), $row, array( 'id' => $id ) );
			$verb = 'updated';
		} else {
			$wpdb->insert( RTS_DB::table( 'email_campaigns' ), $row );
			$id = (int) $wpdb->insert_id; // BEFORE audit(), which also inserts.
			$verb = 'created';
		}
		if ( ! $id ) { return array( 'error' => 'SAVE_FAILED' ); }
		self::audit( $d['created_by'] ?? null, "Email campaign $verb: \"$name\"", 'Email Campaign Builder', "campaign_id=$id; status=$status; delivery=$delivery_mode" );
		return array( 'error' => null, 'campaign_id' => $id );
	}

	public static function list_campaigns() {
		global $wpdb;
		return $wpdb->get_results( "SELECT ec.*, et.name AS template_name, (SELECT COUNT(*) FROM " . RTS_DB::table( 'campaign_sends' ) . " cs WHERE cs.campaign_id = ec.id) AS sent_count FROM " . RTS_DB::table( 'email_campaigns' ) . " ec LEFT JOIN " . RTS_DB::table( 'email_templates' ) . " et ON et.id = ec.template_id ORDER BY ec.created_at DESC" );
	}

	/** Return the immutable recipient history for one campaign. */
	public static function sent_recipients( $id ) {
		global $wpdb;
		$ct = RTS_DB::table( 'email_campaigns' );
		$st = RTS_DB::table( 'campaign_sends' );
		$pt = RTS_DB::table( 'participants' );
		$campaign = $wpdb->get_row( $wpdb->prepare( "SELECT id, name FROM $ct WHERE id = %d", absint( $id ) ) );
		if ( ! $campaign ) { return array( 'error' => 'NOT_FOUND', 'campaign' => null, 'recipients' => array() ); }
		$recipients = $wpdb->get_results( $wpdb->prepare( "SELECT cs.participant_id, cs.sent_at, p.name, p.email
			FROM $st cs LEFT JOIN $pt p ON p.id = cs.participant_id
			WHERE cs.campaign_id = %d ORDER BY cs.sent_at DESC, p.name ASC", absint( $id ) ) );
		return array( 'error' => null, 'campaign' => $campaign, 'recipients' => is_array( $recipients ) ? $recipients : array() );
	}

	/** Permanently delete only an unsent draft; sent campaigns must be archived. */
	public static function delete_campaign( $id, $by ) {
		global $wpdb;
		$ct = RTS_DB::table( 'email_campaigns' );
		$st = RTS_DB::table( 'campaign_sends' );
		$campaign = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, status FROM $ct WHERE id = %d", absint( $id ) ) );
		if ( ! $campaign ) { return array( 'error' => 'NOT_FOUND' ); }
		$sent_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $st WHERE campaign_id = %d", absint( $id ) ) );
		if ( 'draft' !== $campaign->status || $sent_count > 0 ) { return array( 'error' => 'DELETE_ONLY_UNSENT_DRAFTS' ); }
		wp_clear_scheduled_hook( 'rts_run_scheduled_campaign', array( absint( $id ) ) );
		$deleted = $wpdb->delete( $ct, array( 'id' => absint( $id ) ), array( '%d' ) );
		if ( ! $deleted ) { return array( 'error' => 'DELETE_FAILED' ); }
		self::audit( $by, "Unsent email campaign deleted: \"{$campaign->name}\"", 'Email Campaign Builder', 'campaign_id=' . absint( $id ) );
		return array( 'error' => null );
	}

	public static function set_campaign_status( $id, $status, $by ) {
		global $wpdb;
		if ( ! in_array( $status, array( 'draft', 'active', 'paused', 'completed', 'archived' ), true ) ) { return array( 'error' => 'INVALID_STATUS' ); }
		$campaign = $wpdb->get_row( $wpdb->prepare( "SELECT id, delivery_mode, scheduled_at FROM " . RTS_DB::table( 'email_campaigns' ) . " WHERE id = %d", $id ) );
		if ( ! $campaign ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( 'active' === $status && 'manual' === ( $campaign->delivery_mode ?? 'automation' ) ) { return array( 'error' => 'MANUAL_SEND_REQUIRED' ); }
		if ( 'active' === $status && 'scheduled' === ( $campaign->delivery_mode ?? 'automation' ) && empty( $campaign->scheduled_at ) ) { return array( 'error' => 'SCHEDULE_REQUIRED' ); }
		if ( 'archived' === $status ) {
			$sent_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . RTS_DB::table( 'campaign_sends' ) . " WHERE campaign_id = %d", $id ) );
			if ( 0 === $sent_count ) { return array( 'error' => 'ARCHIVE_REQUIRES_SEND_HISTORY' ); }
		}
		$wpdb->update( RTS_DB::table( 'email_campaigns' ), array( 'status' => $status, 'archived_at' => 'archived' === $status ? current_time( 'mysql' ) : null ), array( 'id' => $id ) );
		if ( 'active' !== $status ) { wp_clear_scheduled_hook( 'rts_run_scheduled_campaign', array( absint( $id ) ) ); }
		self::audit( $by, "Email campaign status -> $status", 'Email Campaign Builder', "campaign_id=$id" );
		return array( 'error' => null );
	}

	// THE REAL TRIGGER: find everyone newly eligible, send (respecting subscriptions via the SAME
	// audience function as Broadcast), log each send so re-running never double-sends.
	// In production this is what a WP-Cron job (wp_schedule_event) would call.
	public static function run_trigger_check( $id, $by, $force_immediate = false ) {
		global $wpdb; $ct = RTS_DB::table( 'email_campaigns' ); $st = RTS_DB::table( 'campaign_sends' ); $pt = RTS_DB::table( 'participants' );
		$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ct WHERE id = %d", $id ) );
		if ( ! $c ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( 'active' !== $c->status ) { return array( 'error' => 'CAMPAIGN_NOT_ACTIVE', 'message' => 'Campaign must be status=active to run trigger checks.' ); }
		$is_scheduled = 'scheduled' === ( $c->delivery_mode ?? 'automation' );
		if ( 'manual' === ( $c->delivery_mode ?? 'automation' ) && ! $force_immediate ) {
			return array( 'error' => 'MANUAL_SEND_REQUIRED', 'message' => 'Manual campaigns can run only through Send Now.' );
		}
		if ( $is_scheduled && ! $force_immediate && ( empty( $c->scheduled_at ) || $c->scheduled_at > current_time( 'mysql' ) ) ) {
			return array( 'error' => null, 'eligible_count' => 0, 'newly_sent' => 0, 'excluded_unsubscribed' => 0, 'excluded_by_conditions' => 0, 'message' => 'Scheduled time has not arrived.' );
		}
		if ( $is_scheduled || $force_immediate ) {
			$eligible = $wpdb->get_col( $wpdb->prepare( "SELECT p.id FROM $pt p WHERE p.id NOT IN (SELECT participant_id FROM $st WHERE campaign_id = %d)", $id ) );
		} else {
			$field = 'days_after_verification' === $c->trigger_type ? 'verified_at' : 'registered_at';
			$eligible = $wpdb->get_col( $wpdb->prepare( "SELECT p.id FROM $pt p WHERE p.$field IS NOT NULL AND p.$field <= DATE_SUB(NOW(), INTERVAL %d DAY) AND p.id NOT IN (SELECT participant_id FROM $st WHERE campaign_id = %d)", (int) $c->trigger_days, $id ) );
		}
		if ( ! $eligible ) {
			if ( $is_scheduled || $force_immediate ) { $wpdb->update( $ct, array( 'status' => 'completed', 'sent_at' => current_time( 'mysql' ), 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ) ); }
			return array( 'error' => null, 'eligible_count' => 0, 'newly_sent' => 0, 'excluded_unsubscribed' => 0, 'excluded_by_conditions' => 0, 'message' => 'No newly-eligible participants this run.' );
		}
		$a = self::campaign_audience( $c->category, $c->audience_filter, $c->recipient_include_ids ?? array(), $c->recipient_exclude_ids ?? array(), $eligible, $c->exclusion_rules ?? array() );
		$tpl = $c->template_id ? $wpdb->get_row( $wpdb->prepare( "SELECT subject, html_body FROM " . RTS_DB::table( 'email_templates' ) . " WHERE id = %d", $c->template_id ) ) : null;
		$subject = $tpl ? $tpl->subject : $c->name; $body = $tpl ? $tpl->html_body : "Hi {first_name},\n\nAn update from Run The Seas.";
		foreach ( $a['final_recipients'] as $r ) {
			$ins = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $st (campaign_id, participant_id) VALUES (%d, %d)", $id, $r->id ) );
			if ( $ins ) { RTS_Production::send( $r->email, RTS_Production::merge( $subject, $r ), RTS_Production::merge( $body, $r ), 'marketing', array( 'campaign_id' => $id, 'participant_id' => $r->id, 'unsubscribe_url' => RTS_Production::unsubscribe_url( $r->unsubscribe_token ) ) ); }
		}
		if ( $is_scheduled || $force_immediate ) {
			$wpdb->update( $ct, array( 'status' => 'completed', 'sent_at' => current_time( 'mysql' ), 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ) );
		}
		self::audit( $by ?: 'system', "Campaign trigger check run: \"{$c->name}\"", 'Email Campaign Builder', "campaign_id=$id; eligible=" . count( $eligible ) . "; sent={$a['final_recipient_count']}; excluded_unsubscribed={$a['excluded_unsubscribed']}; excluded_by_conditions={$a['excluded_by_conditions']}" );
		return array( 'error' => null, 'eligible_count' => count( $eligible ), 'newly_sent' => $a['final_recipient_count'], 'excluded_unsubscribed' => $a['excluded_unsubscribed'], 'excluded_by_conditions' => $a['excluded_by_conditions'] );
	}

	/** Send a representative merged copy without consuming a real campaign recipient. */
	public static function send_campaign_test( $id, $email, $by ) {
		global $wpdb;
		if ( ! is_email( $email ) ) { return array( 'error' => 'VALID_TEST_EMAIL_REQUIRED' ); }
		$c = $wpdb->get_row( $wpdb->prepare( "SELECT ec.*, et.subject, et.html_body FROM " . RTS_DB::table( 'email_campaigns' ) . " ec LEFT JOIN " . RTS_DB::table( 'email_templates' ) . " et ON et.id = ec.template_id WHERE ec.id = %d", $id ) );
		if ( ! $c || null === $c->subject ) { return array( 'error' => 'TEMPLATE_NOT_FOUND' ); }
		$sample = (object) array( 'id' => 0, 'name' => 'Sample Runner', 'email' => $email, 'founding_runner_number' => 'FR-0000', 'referral_code' => 'SAMPLE', 'unsubscribe_token' => 'sample' );
		$result = RTS_Production::send( $email, '[TEST] ' . RTS_Production::merge( $c->subject, $sample ), RTS_Production::merge( $c->html_body, $sample ), 'transactional', array( 'campaign_id' => $id, 'test' => true ) );
		self::audit( $by, "Campaign test sent: \"{$c->name}\"", 'Email Campaign Builder', "campaign_id=$id; email=$email" );
		return array( 'error' => $result['error'] ?: null, 'delivery' => $result );
	}

	// ---- Email Reporting — honest about what's unavailable ----
	public static function reporting_stats() {
		global $wpdb; $se = RTS_DB::table( 'sent_emails' );
		$sends = $wpdb->get_results( "SELECT * FROM $se ORDER BY sent_at DESC" );
		$by_camp = $wpdb->get_results( "SELECT ec.name, COUNT(cs.id) AS sent_count FROM " . RTS_DB::table( 'email_campaigns' ) . " ec LEFT JOIN " . RTS_DB::table( 'campaign_sends' ) . " cs ON cs.campaign_id = ec.id GROUP BY ec.id ORDER BY sent_count DESC" );
		return array(
			'total_broadcast_sends' => count( $sends ),
			'total_broadcast_recipients' => array_sum( array_map( fn( $s ) => (int) $s->recipient_count, $sends ) ),
			'total_campaign_sends' => array_sum( array_map( fn( $c ) => (int) $c->sent_count, $by_camp ) ),
			'campaign_breakdown' => $by_camp,
			'by_category' => $wpdb->get_results( "SELECT category, SUM(recipient_count) AS total FROM $se GROUP BY category" ),
			'recent_sends' => array_slice( $sends, 0, 15 ),
			// No real provider webhook (Appendix F) — reported as null, not faked:
			'open_rate' => null, 'click_through_rate' => null, 'bounce_rate' => null,
		);
	}

	// ---- Ad Campaign Analysis: REAL UTM attribution against participant records ----
	public static function create_ad_campaign( $d ) {
		global $wpdb;
		$ok = $wpdb->insert( RTS_DB::table( 'campaigns' ), array( 'name' => $d['name'], 'platform' => $d['platform'] ?? '', 'utm_campaign_code' => $d['utm_campaign_code'], 'cost_charged' => (float) ( $d['cost_charged'] ?? 0 ), 'impressions' => (int) ( $d['impressions'] ?? 0 ), 'clicks' => (int) ( $d['clicks'] ?? 0 ), 'ad_wording' => $d['ad_wording'] ?? '', 'target_age_groups' => $d['target_age_groups'] ?? '', 'audience_focus' => $d['audience_focus'] ?? '', 'geography' => $d['geography'] ?? '' ) );
		if ( false === $ok ) { return array( 'error' => 'UTM_CODE_ALREADY_EXISTS' ); }
		$id = (int) $wpdb->insert_id; // BEFORE audit()
		self::audit( $d['created_by'] ?? null, "Ad campaign created: \"{$d['name']}\"", 'Ad Campaign Analysis', "campaign_id=$id; utm={$d['utm_campaign_code']}" );
		return array( 'error' => null, 'campaign_id' => $id );
	}

	public static function ad_campaign_stats() {
		global $wpdb; $pt = RTS_DB::table( 'participants' ); $ct = RTS_DB::table( 'cabin_credits' );
		$out = array();
		foreach ( $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'campaigns' ) . " ORDER BY created_at DESC" ) as $c ) {
			$interested = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $pt WHERE utm_campaign = %s AND wants_cruise_notification = 1", $c->utm_campaign_code ) );
			$credited   = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $pt p JOIN $ct cc ON cc.participant_id = p.id WHERE p.utm_campaign = %s AND p.email_verified = 1", $c->utm_campaign_code ) );
			$imp = (int) $c->impressions; $clk = (int) $c->clicks; $cost = (float) $c->cost_charged;
			$row = (array) $c;
			$row['interested'] = $interested; $row['verified_credited'] = $credited;
			$row['ctr'] = $imp ? round( $clk / $imp * 100, 2 ) : 0;
			$row['cost_per_interested'] = $interested ? round( $cost / $interested, 2 ) : null;   // null, not a divide-by-zero
			$row['cac'] = $credited ? round( $cost / $credited, 2 ) : null;
			$out[] = $row;
		}
		return $out;
	}

	// ---- Interest & Notification Lists ----
	public static function set_notification_pref( $pid, $wants ) {
		global $wpdb;
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM " . RTS_DB::table( 'participants' ) . " WHERE id = %d", $pid ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( RTS_DB::table( 'participants' ), array( 'wants_cruise_notification' => $wants ? 1 : 0 ), array( 'id' => $pid ) );
		self::audit( 'system', 'Notification preference set to ' . ( $wants ? 'yes' : 'no' ), 'Interest & Notification Lists', "participant_id=$pid" );
		return array( 'error' => null );
	}
	public static function set_declined_contact( $pid, $declined, $reason = '' ) {
		global $wpdb;
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM " . RTS_DB::table( 'participants' ) . " WHERE id = %d", $pid ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( RTS_DB::table( 'participants' ), array( 'declined_further_contact' => $declined ? 1 : 0 ), array( 'id' => $pid ) );
		self::audit( 'system', 'Declined further contact set to ' . ( $declined ? 'yes' : 'no' ) . ( $reason ? " — $reason" : '' ), 'Interest & Notification Lists', "participant_id=$pid" );
		return array( 'error' => null );
	}
	// Mutually exclusive by construction: notify list EXCLUDES anyone who declined.
	public static function notification_list() { global $wpdb; return $wpdb->get_results( "SELECT id, name, email, email_verified, marketing_source, country, registered_at FROM " . RTS_DB::table( 'participants' ) . " WHERE wants_cruise_notification = 1 AND declined_further_contact = 0 ORDER BY registered_at DESC" ); }
	public static function declined_list()     { global $wpdb; return $wpdb->get_results( "SELECT p.id, p.name, p.email, p.country, (SELECT status FROM " . RTS_DB::table( 'cabin_credits' ) . " WHERE participant_id = p.id) AS credit_status FROM " . RTS_DB::table( 'participants' ) . " p WHERE p.declined_further_contact = 1 ORDER BY p.registered_at DESC" ); }

	// ---- Duplicate Detection & Fraud: heuristic (same name + country) -> human review; reviewed pairs never reappear ----
	public static function duplicate_scan() {
		global $wpdb; $pt = RTS_DB::table( 'participants' ); $dt = RTS_DB::table( 'duplicate_reviews' );
		$flagged = array();
		foreach ( $wpdb->get_results( "SELECT LOWER(name) AS norm_name, country, GROUP_CONCAT(id ORDER BY id) AS ids FROM $pt WHERE name IS NOT NULL AND country IS NOT NULL GROUP BY norm_name, country HAVING COUNT(*) > 1" ) as $g ) {
			$ids = array_map( 'intval', explode( ',', $g->ids ) );
			for ( $i = 0; $i < count( $ids ); $i++ ) { for ( $j = $i + 1; $j < count( $ids ); $j++ ) {
				$a = min( $ids[ $i ], $ids[ $j ] ); $b = max( $ids[ $i ], $ids[ $j ] ); // canonical order -> UNIQUE key works
				$ex = $wpdb->get_row( $wpdb->prepare( "SELECT status FROM $dt WHERE participant_id_a = %d AND participant_id_b = %d", $a, $b ) );
				if ( $ex && 'pending' !== $ex->status ) { continue; } // already decided — skip
				if ( ! $ex ) { $wpdb->insert( $dt, array( 'participant_id_a' => $a, 'participant_id_b' => $b, 'reason' => "Same name (\"{$g->norm_name}\") and country (\"{$g->country}\")" ) ); }
				$pa = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, email, country FROM $pt WHERE id = %d", $a ) );
				$pb = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, email, country FROM $pt WHERE id = %d", $b ) );
				$flagged[] = array( 'a' => $pa, 'b' => $pb, 'reason' => 'Same name and country' );
			} }
		}
		return $flagged;
	}
	public static function review_duplicate( $a, $b, $decision, $by ) {
		global $wpdb;
		if ( ! in_array( $decision, array( 'approved_as_unique', 'rejected_as_duplicate' ), true ) ) { return array( 'error' => 'INVALID_DECISION' ); }
		$lo = min( (int) $a, (int) $b ); $hi = max( (int) $a, (int) $b );
		$n = $wpdb->update( RTS_DB::table( 'duplicate_reviews' ), array( 'status' => $decision, 'reviewed_by' => $by ?: 'admin', 'reviewed_at' => current_time( 'mysql' ) ), array( 'participant_id_a' => $lo, 'participant_id_b' => $hi ) );
		if ( ! $n ) { return array( 'error' => 'REVIEW_NOT_FOUND' ); }
		self::audit( $by, "Duplicate review: $decision", 'Fraud Prevention', "participant_a=$lo; participant_b=$hi" );
		return array( 'error' => null );
	}
	public static function fraud_queue() {
		global $wpdb; $pt = RTS_DB::table( 'participants' );
		return array(
			'flagged_referrals' => $wpdb->get_results( "SELECT r.*, p1.name AS referrer_name, p2.name AS referred_name FROM " . RTS_DB::table( 'referrals' ) . " r JOIN $pt p1 ON p1.id = r.referring_participant_id LEFT JOIN $pt p2 ON p2.id = r.referred_participant_id WHERE r.fraud_review_status != 'clear' ORDER BY r.clicked_at DESC" ),
			'duplicate_reviews' => $wpdb->get_results( "SELECT d.*, p1.name AS name_a, p1.email AS email_a, p2.name AS name_b, p2.email AS email_b FROM " . RTS_DB::table( 'duplicate_reviews' ) . " d JOIN $pt p1 ON p1.id = d.participant_id_a JOIN $pt p2 ON p2.id = d.participant_id_b WHERE d.status = 'pending' ORDER BY d.created_at DESC" ),
		);
	}
	public static function reject_referral( $id, $by, $reason ) {
		global $wpdb; $rt = RTS_DB::table( 'referrals' );
		$r = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $rt WHERE id = %d", $id ) );
		if ( ! $r ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( $rt, array( 'fraud_review_status' => 'rejected' ), array( 'id' => $id ) );
		self::audit( $by, 'Referral rejected', 'Fraud Prevention', "referral_id=$id; reason=$reason" );
		return array( 'error' => null );
	}
}
