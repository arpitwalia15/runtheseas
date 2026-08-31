<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_3 {

	private static function audit( $u, $a, $m, $n = '' ) { RTS_Business_Logic::log_audit( $u ?: 'admin', $a, $m, 'success', $n ); }

	// ---- Cabin Credit Management ----

	public static function credit_ledger() {
		global $wpdb;
		return $wpdb->get_results( "SELECT cc.*, p.name, p.founding_runner_number, p.email FROM " . RTS_DB::table( 'cabin_credits' ) . " cc JOIN " . RTS_DB::table( 'participants' ) . " p ON p.id = cc.participant_id ORDER BY cc.issued_at DESC" );
	}

	public static function credit_summary() {
		global $wpdb; $t = RTS_DB::table( 'cabin_credits' );
		$c = fn( $s ) => (int) $wpdb->get_var( "SELECT COUNT(*) FROM $t WHERE status IN ($s)" );
		$issued = $c( "'issued'" ); $deferred = $c( "'deferred'" );
		return array( 'issued' => $issued, 'deferred' => $deferred, 'redeemed' => $c( "'redeemed'" ), 'cancelled_or_void' => $c( "'cancelled','void'" ), 'outstanding_liability' => ( $issued + $deferred ) * 100 );
	}

	// Business rule (T&Cs §17): two eligible people sharing a cabin -> second credit DEFERRED to 2nd sailing, not forfeited.
	public static function defer_credit( $id, $admin, $reason ) {
		global $wpdb; $t = RTS_DB::table( 'cabin_credits' );
		$c = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $t WHERE id = %d", $id ) );
		if ( ! $c ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( 'issued' !== $c->status ) { return array( 'error' => 'INVALID_STATE', 'message' => "Cannot defer a credit with status \"{$c->status}\"" ); }
		$wpdb->update( $t, array( 'status' => 'deferred' ), array( 'id' => $id ) );
		self::audit( $admin, 'Cabin Credit deferred to 2nd sailing', 'Cabin Credit Management', "credit_id=$id; reason=$reason" );
		return array( 'error' => null );
	}

	public static function void_credit( $id, $admin, $reason ) {
		global $wpdb; $t = RTS_DB::table( 'cabin_credits' );
		if ( ! $reason ) { return array( 'error' => 'REASON_REQUIRED' ); }
		if ( ! $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $t WHERE id = %d", $id ) ) ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( $t, array( 'status' => 'void' ), array( 'id' => $id ) );
		self::audit( $admin, 'Cabin Credit voided', 'Cabin Credit Management', "credit_id=$id; reason=$reason" );
		return array( 'error' => null );
	}

	// ---- Trophy Management ----

	public static function create_trophy( $d ) {
		global $wpdb;
		$wpdb->insert( RTS_DB::table( 'trophies' ), array( 'name' => $d['name'], 'description' => $d['description'] ?? '', 'unlock_rule' => $d['unlock_rule'], 'category' => $d['category'] ?? 'repeatable' ) );
		$tid = (int) $wpdb->insert_id; // capture BEFORE audit() clobbers insert_id
		self::audit( $d['created_by'] ?? null, "Trophy created: \"{$d['name']}\"", 'Trophy Management', "trophy_id=$tid; unlock_rule={$d['unlock_rule']}" );
		return array( 'error' => null, 'trophy_id' => $tid );
	}

	public static function trophy_stats() {
		global $wpdb; $tt = RTS_DB::table( 'trophies' ); $ut = RTS_DB::table( 'trophy_unlocks' ); $pt = RTS_DB::table( 'participants' );
		$trophies = $wpdb->get_results( "SELECT t.*, (SELECT COUNT(*) FROM $ut u WHERE u.trophy_id = t.id) AS unlock_count FROM $tt t" );
		$total_unlocks  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $ut" );
		$unique_holders = (int) $wpdb->get_var( "SELECT COUNT(DISTINCT participant_id) FROM $ut" );
		$total_verified = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $pt WHERE email_verified = 1" );
		$dist = array( '0' => $total_verified - $unique_holders, '1' => 0, '2' => 0, '3' => 0, '4+' => 0 );
		foreach ( $wpdb->get_results( "SELECT COUNT(*) AS c FROM $ut GROUP BY participant_id" ) as $r ) { $k = (int) $r->c >= 4 ? '4+' : (string) (int) $r->c; $dist[ $k ]++; }
		return array( 'trophies' => $trophies, 'total_unlocks' => $total_unlocks, 'unique_holders' => $unique_holders, 'total_verified' => $total_verified, 'distribution' => $dist );
	}

	// Answers spec Appendix L: retroactive unlock is an explicit admin choice, never silent.
	public static function eligible_not_unlocked( $trophy_id ) {
		global $wpdb; $tt = RTS_DB::table( 'trophies' ); $ut = RTS_DB::table( 'trophy_unlocks' ); $pt = RTS_DB::table( 'participants' );
		$t = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tt WHERE id = %d", $trophy_id ) );
		if ( ! $t ) { return array(); }
		if ( 'founding_runner' === $t->unlock_rule ) {
			return $wpdb->get_results( $wpdb->prepare( "SELECT p.id, p.name FROM $pt p WHERE p.email_verified = 1 AND p.id NOT IN (SELECT participant_id FROM $ut WHERE trophy_id = %d)", $trophy_id ) );
		}
		return array(); // other rules would add their own eligibility query here as introduced
	}

	public static function retroactive_unlock( $trophy_id, $ids, $admin ) {
		global $wpdb; $tt = RTS_DB::table( 'trophies' ); $ut = RTS_DB::table( 'trophy_unlocks' );
		$t = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tt WHERE id = %d", $trophy_id ) );
		if ( ! $t ) { return array( 'error' => 'NOT_FOUND' ); }
		$n = 0;
		foreach ( (array) $ids as $pid ) { if ( $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $ut (trophy_id, participant_id) VALUES (%d, %d)", $trophy_id, (int) $pid ) ) ) { $n++; } }
		self::audit( $admin, "Retroactive trophy unlock: \"{$t->name}\"", 'Trophy Management', "trophy_id=$trophy_id; newly_unlocked=$n; scanned=" . count( (array) $ids ) );
		return array( 'error' => null, 'unlocked_count' => $n );
	}

	// ---- Referral Leaderboard & Draws ----

	public static function leaderboard() {
		global $wpdb; $pt = RTS_DB::table( 'participants' ); $rt = RTS_DB::table( 'referrals' );
		$rows = $wpdb->get_results( "SELECT p.id, p.founding_runner_number, p.name, SUM(CASE WHEN r.verified = 1 AND r.fraud_review_status = 'clear' THEN 1 ELSE 0 END) AS verified_referrals FROM $pt p LEFT JOIN $rt r ON r.referring_participant_id = p.id GROUP BY p.id HAVING verified_referrals > 0 ORDER BY verified_referrals DESC" );
		foreach ( $rows as &$r ) { $r->verified_referrals = (int) $r->verified_referrals; $r->draw_b_eligible = $r->verified_referrals >= 42; }
		return $rows;
	}

	// Draw A: one entry per verified referral. Draw B: 42+ only, one entry each.
	// The seed is stored so the pick is REPRODUCIBLE after the fact — required for a legally-governed contest.
	public static function run_draw( $type, $admin ) {
		global $wpdb; $rt = RTS_DB::table( 'referrals' ); $pt = RTS_DB::table( 'participants' );
		if ( ! in_array( $type, array( 'A', 'B' ), true ) ) { return array( 'error' => 'INVALID_DRAW_TYPE' ); }
		$entries = array();
		if ( 'A' === $type ) {
			foreach ( $wpdb->get_results( "SELECT referring_participant_id AS pid, COUNT(*) AS c FROM $rt WHERE verified = 1 AND fraud_review_status = 'clear' GROUP BY referring_participant_id" ) as $r ) {
				for ( $i = 0; $i < (int) $r->c; $i++ ) { $entries[] = (int) $r->pid; }
			}
		} else {
			foreach ( self::leaderboard() as $r ) { if ( $r->draw_b_eligible ) { $entries[] = (int) $r->id; } }
		}
		if ( ! $entries ) { return array( 'error' => 'NO_ELIGIBLE_ENTRIES' ); }
		$seed = bin2hex( random_bytes( 16 ) );
		$idx  = self::seed_to_index( $seed, count( $entries ) );
		$winner_id = $entries[ $idx ];
		$wpdb->insert( RTS_DB::table( 'draws' ), array( 'draw_type' => $type, 'random_seed' => $seed, 'eligible_entry_count' => count( $entries ), 'winner_participant_id' => $winner_id, 'run_by' => $admin ?: 'admin' ) );
		$draw_id = (int) $wpdb->insert_id; // capture BEFORE audit() clobbers insert_id
		$winner = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, email, founding_runner_number FROM $pt WHERE id = %d", $winner_id ) );
		self::audit( $admin, "Draw $type executed — winner: {$winner->name}", 'Referral Management', "draw_id=$draw_id; seed=$seed; entries=" . count( $entries ) );
		return array( 'error' => null, 'draw_id' => $draw_id, 'winner' => $winner, 'entry_count' => count( $entries ), 'seed' => $seed );
	}

	// Deterministic: same seed + same entry count => same index. Mirrors the Node version (sha256, first 4 bytes).
	public static function seed_to_index( $seed, $n ) {
		$h = hash( 'sha256', $seed, true );
		$u = unpack( 'N', substr( $h, 0, 4 ) )[1];
		return $u % $n;
	}

	public static function draw_history() {
		global $wpdb;
		return $wpdb->get_results( "SELECT d.*, p.name AS winner_name, p.email AS winner_email FROM " . RTS_DB::table( 'draws' ) . " d LEFT JOIN " . RTS_DB::table( 'participants' ) . " p ON p.id = d.winner_participant_id ORDER BY d.run_at DESC" );
	}

	// ---- Subscription Management (admin aggregate) ----

	public static function subscription_stats() {
		global $wpdb; $st = RTS_DB::table( 'subscriptions' ); $pt = RTS_DB::table( 'participants' );
		$by = array();
		foreach ( array( 'survey', 'referral', 'trophy', 'general' ) as $cat ) {
			$by[] = array( 'category' => $cat,
				'active'       => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $st WHERE category = %s AND subscribed = 1", $cat ) ),
				'unsubscribed' => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $st WHERE category = %s AND subscribed = 0", $cat ) ) );
		}
		$recent = $wpdb->get_results( "SELECT s.category, s.unsubscribe_reason, s.updated_at, p.name, p.email FROM $st s JOIN $pt p ON p.id = s.participant_id WHERE s.subscribed = 0 ORDER BY s.updated_at DESC LIMIT 20" );
		return array( 'by_category' => $by, 'recent_unsubscribes' => $recent );
	}

	public static function unsubscribe_with_reason( $token, $category, $reason ) {
		global $wpdb; $pt = RTS_DB::table( 'participants' ); $st = RTS_DB::table( 'subscriptions' );
		$p = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $pt WHERE unsubscribe_token = %s", $token ) );
		if ( ! $p ) { return array( 'error' => 'INVALID_TOKEN' ); }
		$cats = 'all' === $category ? array( 'survey', 'referral', 'trophy', 'general' ) : array( $category );
		foreach ( $cats as $c ) { $wpdb->update( $st, array( 'subscribed' => 0, 'unsubscribe_reason' => $reason ?: null, 'updated_at' => current_time( 'mysql' ) ), array( 'participant_id' => $p->id, 'category' => $c ) ); }
		self::audit( $p->email, "Unsubscribed ($category)" . ( $reason ? " — reason: $reason" : '' ), 'Subscription Management', "participant_id={$p->id}" );
		return array( 'error' => null );
	}

	// ---- Audience filters (shared by Broadcast / Founding Runner Outreach) ----

	public static function audience_where( $filter ) {
		switch ( $filter ) {
			case 'runners_only':     return array( ' AND p.runner_status = %s', array( 'runner' ) );
			case 'non_runners_only': return array( ' AND p.runner_status = %s', array( 'non_runner' ) );
			case 'verified_only':    return array( ' AND p.email_verified = 1', array() );
			default:                 return array( '', array() );
		}
	}

	public static function audience_preview( $category, $filter ) {
		list( $sql, $params ) = self::audience_where( $filter );
		$a = RTS_Business_Logic::get_bulk_email_audience( $category, $sql, $params );
		return array( 'total_matching_filter' => $a['total_matching_filter'], 'excluded_unsubscribed' => $a['excluded_unsubscribed'], 'final_recipient_count' => $a['final_recipient_count'] );
	}

	// ---- The send-gate: draft -> test-to-self -> test-to-group -> bulk. Enforced HERE, server-side. ----

	public static function create_draft( $d ) {
		global $wpdb;
		if ( ! in_array( $d['category'] ?? 'general', array( 'survey', 'referral', 'trophy', 'general' ), true ) ) { return array( 'error' => 'INVALID_CATEGORY' ); }
		$wpdb->insert( RTS_DB::table( 'email_drafts' ), array( 'category' => $d['category'] ?? 'general', 'audience_filter' => $d['audience_filter'] ?? 'all', 'subject' => $d['subject'], 'body' => $d['body'] ?? '', 'created_by' => $d['created_by'] ?? 'admin' ) );
		$new_id = (int) $wpdb->insert_id; // capture BEFORE audit() — audit does its own insert and would clobber $wpdb->insert_id
		self::audit( $d['created_by'] ?? null, "Email draft created: \"{$d['subject']}\"", 'Email', 'category=' . ( $d['category'] ?? 'general' ) . '; audience=' . ( $d['audience_filter'] ?? 'all' ) );
		return self::gate_status( $new_id );
	}

	public static function gate_status( $id ) {
		global $wpdb;
		$d = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'email_drafts' ) . " WHERE id = %d", $id ) );
		if ( ! $d ) { return array( 'error' => 'NOT_FOUND' ); }
		return array( 'error' => null, 'draft' => $d, 'ready_for_bulk' => (bool) ( (int) $d->test_self_sent && (int) $d->test_group_sent ) );
	}

	public static function test_self( $id, $email ) {
		global $wpdb;
		if ( ! $email ) { return array( 'error' => 'EMAIL_REQUIRED' ); }
		$d = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'email_drafts' ) . " WHERE id = %d", $id ) );
		if ( ! $d ) { return array( 'error' => 'NOT_FOUND' ); }
		RTS_Production::send( $email, '[TEST] ' . $d->subject, $d->body, 'transactional', array( 'draft_id' => $id, 'test' => 'self' ) );
		$wpdb->update( RTS_DB::table( 'email_drafts' ), array( 'test_self_sent' => 1, 'test_self_sent_at' => current_time( 'mysql' ), 'test_self_email' => $email ), array( 'id' => $id ) );
		self::audit( $email, "Test email sent to self: \"{$d->subject}\"", 'Email', "draft_id=$id" );
		return self::gate_status( $id );
	}

	public static function test_group( $id, $emails, $by ) {
		global $wpdb;
		$list = array_values( array_filter( array_map( 'trim', explode( ',', (string) $emails ) ) ) );
		if ( ! $list ) { return array( 'error' => 'NO_TEST_EMAILS_PROVIDED' ); }
		$d = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'email_drafts' ) . " WHERE id = %d", $id ) );
		if ( ! $d ) { return array( 'error' => 'NOT_FOUND' ); }
		foreach ( $list as $e ) { if ( is_email( $e ) ) { RTS_Production::send( $e, '[TEST] ' . $d->subject, $d->body, 'transactional', array( 'draft_id' => $id, 'test' => 'group' ) ); } }
		$wpdb->update( RTS_DB::table( 'email_drafts' ), array( 'test_group_sent' => 1, 'test_group_sent_at' => current_time( 'mysql' ), 'test_group_emails' => implode( ', ', $list ) ), array( 'id' => $id ) );
		self::audit( $by ?: $d->created_by, 'Test email sent to admin group (' . count( $list ) . " recipients): \"{$d->subject}\"", 'Email', "draft_id=$id; emails=" . implode( ', ', $list ) );
		return self::gate_status( $id );
	}

	// THE ENFORCEMENT POINT. force=true allowed (admins can always act) but requires a reason and is logged distinctly.
	public static function send_bulk( $id, $by, $force = false, $force_reason = '' ) {
		global $wpdb; $dt = RTS_DB::table( 'email_drafts' );
		$g = self::gate_status( $id );
		if ( $g['error'] ) { return $g; }
		$d = $g['draft'];
		if ( (int) $d->bulk_sent ) { return array( 'error' => 'ALREADY_SENT' ); }
		if ( ! $g['ready_for_bulk'] && ! $force ) {
			return array( 'error' => 'GATE_NOT_CLEARED', 'message' => "You haven't sent the test to yourself or the admin test group yet. Are you sure you want to send to all recipients?", 'test_self_sent' => (bool) (int) $d->test_self_sent, 'test_group_sent' => (bool) (int) $d->test_group_sent );
		}
		$forced = ! $g['ready_for_bulk'] && $force;
		if ( $forced && ! $force_reason ) { return array( 'error' => 'FORCE_REASON_REQUIRED', 'message' => 'Overriding the send gate requires a reason, logged to the Audit Log.' ); }

		list( $sql, $params ) = self::audience_where( $d->audience_filter );
		$a = RTS_Business_Logic::get_bulk_email_audience( $d->category, $sql, $params );
		$delivery = RTS_Production::send_to_participants( $a['final_recipients'], $d->subject, $d->body, array( 'draft_id' => $id ) );
		$wpdb->insert( RTS_DB::table( 'sent_emails' ), array( 'category' => $d->category, 'subject' => $d->subject, 'recipient_count' => $a['final_recipient_count'], 'excluded_unsubscribed_count' => $a['excluded_unsubscribed'], 'sent_by' => $by ?: 'admin', 'test_mode' => 0 ) );
		$wpdb->update( $dt, array( 'bulk_sent' => 1, 'bulk_sent_at' => current_time( 'mysql' ), 'bulk_sent_forced' => $forced ? 1 : 0, 'bulk_sent_force_reason' => $forced ? $force_reason : null ), array( 'id' => $id ) );
		self::audit( $by, "Bulk email sent: \"{$d->subject}\"", 'Email', "category={$d->category}; audience={$d->audience_filter}; recipients={$a['final_recipient_count']}; excluded_unsubscribed={$a['excluded_unsubscribed']}" );
		if ( $forced ) { self::audit( $by, "⚠ BULK SEND GATE OVERRIDDEN — sent without completing both tests: \"{$d->subject}\"", 'Email', "draft_id=$id; reason=$force_reason" ); }
		return array_merge( array( 'error' => null, 'was_forced' => $forced, 'delivery' => $delivery ), $a );
	}
}
