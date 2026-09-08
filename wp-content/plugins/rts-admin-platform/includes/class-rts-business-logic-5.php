<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_5 {

	private static function audit( $u, $a, $m, $n = '' ) { RTS_Business_Logic::log_audit( $u ?: 'admin', $a, $m, 'success', $n ); }

	// ---- Email Campaign Builder: automated, triggered (NOT immediate like Broadcast) ----

	public static function create_campaign( $d ) {
		global $wpdb;
		$wpdb->insert( RTS_DB::table( 'email_campaigns' ), array( 'name' => $d['name'], 'template_id' => $d['template_id'] ?? null, 'trigger_type' => $d['trigger_type'] ?? 'days_after_registration', 'trigger_days' => (int) ( $d['trigger_days'] ?? 3 ), 'audience_filter' => $d['audience_filter'] ?? 'all', 'category' => $d['category'] ?? 'general', 'status' => 'draft' ) );
		$id = (int) $wpdb->insert_id; // BEFORE audit()
		self::audit( $d['created_by'] ?? null, "Email campaign created: \"{$d['name']}\"", 'Email Campaign Builder', "campaign_id=$id" );
		return array( 'error' => null, 'campaign_id' => $id );
	}

	public static function list_campaigns() {
		global $wpdb;
		return $wpdb->get_results( "SELECT ec.*, et.name AS template_name, (SELECT COUNT(*) FROM " . RTS_DB::table( 'campaign_sends' ) . " cs WHERE cs.campaign_id = ec.id) AS sent_count FROM " . RTS_DB::table( 'email_campaigns' ) . " ec LEFT JOIN " . RTS_DB::table( 'email_templates' ) . " et ON et.id = ec.template_id ORDER BY ec.created_at DESC" );
	}

	public static function set_campaign_status( $id, $status, $by ) {
		global $wpdb;
		if ( ! in_array( $status, array( 'draft', 'active', 'paused' ), true ) ) { return array( 'error' => 'INVALID_STATUS' ); }
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM " . RTS_DB::table( 'email_campaigns' ) . " WHERE id = %d", $id ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( RTS_DB::table( 'email_campaigns' ), array( 'status' => $status ), array( 'id' => $id ) );
		self::audit( $by, "Email campaign status -> $status", 'Email Campaign Builder', "campaign_id=$id" );
		return array( 'error' => null );
	}

	// THE REAL TRIGGER: find everyone newly eligible, send (respecting subscriptions via the SAME
	// audience function as Broadcast), log each send so re-running never double-sends.
	// In production this is what a WP-Cron job (wp_schedule_event) would call.
	public static function run_trigger_check( $id, $by ) {
		global $wpdb; $ct = RTS_DB::table( 'email_campaigns' ); $st = RTS_DB::table( 'campaign_sends' ); $pt = RTS_DB::table( 'participants' );
		$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ct WHERE id = %d", $id ) );
		if ( ! $c ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( 'active' !== $c->status ) { return array( 'error' => 'CAMPAIGN_NOT_ACTIVE', 'message' => 'Campaign must be status=active to run trigger checks.' ); }
		$field = 'days_after_verification' === $c->trigger_type ? 'verified_at' : 'registered_at';
		$eligible = $wpdb->get_col( $wpdb->prepare( "SELECT p.id FROM $pt p WHERE p.$field IS NOT NULL AND p.$field <= DATE_SUB(NOW(), INTERVAL %d DAY) AND p.id NOT IN (SELECT participant_id FROM $st WHERE campaign_id = %d)", (int) $c->trigger_days, $id ) );
		if ( ! $eligible ) { return array( 'error' => null, 'eligible_count' => 0, 'newly_sent' => 0, 'excluded_unsubscribed' => 0, 'message' => 'No newly-eligible participants this run.' ); }
		$ph = implode( ',', array_fill( 0, count( $eligible ), '%d' ) );
		list( $fsql, $fparams ) = RTS_Business_Logic_3::audience_where( $c->audience_filter );
		$a = RTS_Business_Logic::get_bulk_email_audience( $c->category, " AND p.id IN ($ph)" . $fsql, array_merge( array_map( 'intval', $eligible ), $fparams ) );
		$tpl = $c->template_id ? $wpdb->get_row( $wpdb->prepare( "SELECT subject, html_body FROM " . RTS_DB::table( 'email_templates' ) . " WHERE id = %d", $c->template_id ) ) : null;
		$subject = $tpl ? $tpl->subject : $c->name; $body = $tpl ? $tpl->html_body : "Hi {first_name},\n\nAn update from Run The Seas.";
		foreach ( $a['final_recipients'] as $r ) {
			$ins = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $st (campaign_id, participant_id) VALUES (%d, %d)", $id, $r->id ) );
			if ( $ins ) { RTS_Production::send( $r->email, RTS_Production::merge( $subject, $r ), RTS_Production::merge( $body, $r ), 'marketing', array( 'campaign_id' => $id, 'participant_id' => $r->id, 'unsubscribe_url' => RTS_Production::unsubscribe_url( $r->unsubscribe_token ) ) ); }
		}
		self::audit( $by ?: 'system', "Campaign trigger check run: \"{$c->name}\"", 'Email Campaign Builder', "campaign_id=$id; eligible=" . count( $eligible ) . "; sent={$a['final_recipient_count']}; excluded_unsubscribed={$a['excluded_unsubscribed']}" );
		return array( 'error' => null, 'eligible_count' => count( $eligible ), 'newly_sent' => $a['final_recipient_count'], 'excluded_unsubscribed' => $a['excluded_unsubscribed'] );
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
