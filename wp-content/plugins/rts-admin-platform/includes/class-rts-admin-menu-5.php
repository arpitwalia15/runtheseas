<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Admin_Menu_5 {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 50 );
		foreach ( array( 'ec_create','ec_save','ec_status','ec_trigger','ad_create','dup_review','reject_ref' ) as $a ) { add_action( "admin_post_rts_$a", array( __CLASS__, "handle_$a" ) ); }
	}
	public static function register_menu() {
		RTS_Auth::page( 'rts-admin', 'Email Campaigns', 'Email Campaigns', 'rts_view', 'rts-email-campaigns', array( __CLASS__, 'render_campaigns' ) );
		RTS_Auth::page( 'rts-admin', 'Email Reporting', 'Email Reporting', 'rts_view', 'rts-email-reporting', array( __CLASS__, 'render_reporting' ) );
		RTS_Auth::page( 'rts-admin', 'Ad Campaign Analysis', 'Ad Campaign Analysis', 'rts_view', 'rts-ad-campaigns', array( __CLASS__, 'render_ads' ) );
		RTS_Auth::page( 'rts-admin', 'Interest & Notification Lists', 'Interest Lists', 'rts_view', 'rts-interest-lists', array( __CLASS__, 'render_interest' ) );
		RTS_Auth::page( 'rts-admin', 'Duplicate Detection & Fraud', 'Fraud Detection', 'rts_view', 'rts-fraud', array( __CLASS__, 'render_fraud' ) );
	}
	private static function form( $action, $fields, $button, $hidden = array(), $class = 'button', $onsubmit = '' ) {
		$h = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline;"' . ( $onsubmit ? ' onsubmit="' . esc_attr( $onsubmit ) . '"' : '' ) . '><input type="hidden" name="action" value="rts_' . esc_attr( $action ) . '">' . wp_nonce_field( 'rts_' . $action, '_rts_nonce', true, false );
		foreach ( $hidden as $k => $v ) { $h .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">'; }
		return $h . $fields . '<button class="' . esc_attr( $class ) . '">' . esc_html( $button ) . '</button></form>';
	}
	private static function guard( $a ) { if ( ! current_user_can( RTS_Auth::action_cap( $a ) ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_' . $a ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); } }
	private static function back( $page, $msg = '', $extra = array() ) { $args = $extra; if ( $msg ) { $args['rts_msg'] = rawurlencode( $msg ); } wp_safe_redirect( RTSAP_Frontend_Dashboard::screen_url( $page, $args ) ); exit; }
	private static function notice() { if ( ! empty( $_GET['rts_msg'] ) ) { $m = rawurldecode( $_GET['rts_msg'] ); $cls = str_starts_with( $m, 'Error' ) ? 'notice-error' : 'notice-success'; echo "<div class=\"notice $cls is-dismissible\"><p>" . esc_html( $m ) . '</p></div>'; } }
	private static function admin() { $u = wp_get_current_user(); return $u ? $u->user_login : 'admin'; }
	private static function kpi( $l, $v, $sub = '' ) { return '<div style="background:#fff;border:1px solid #ccd0d4;border-top:3px solid #C9A24B;border-radius:4px;padding:12px 16px;min-width:170px;"><div style="font-size:11px;text-transform:uppercase;color:#666;font-weight:600;">' . esc_html( $l ) . '</div><div style="font-size:24px;font-weight:700;margin-top:4px;color:#0B1420;">' . esc_html( $v ) . '</div>' . ( $sub ? '<div style="font-size:11px;color:#888">' . esc_html( $sub ) . '</div>' : '' ) . '</div>'; }
	private static function tbl( $heads, $rows ) { $h = '<table class="wp-list-table widefat fixed striped"><thead><tr>'; foreach ( $heads as $x ) { $h .= '<th>' . esc_html( $x ) . '</th>'; } $h .= '</tr></thead><tbody>'; if ( ! $rows ) { $h .= '<tr><td colspan="' . count( $heads ) . '" style="color:#777">No data yet</td></tr>'; } foreach ( $rows as $r ) { $h .= '<tr>'; foreach ( $r as $c ) { $h .= '<td>' . ( is_string( $c ) && str_starts_with( $c, '<' ) ? $c : esc_html( (string) $c ) ) . '</td>'; } $h .= '</tr>'; } return $h . '</tbody></table>'; }

	// ---- Email Campaigns ----
	public static function render_campaigns() {
		global $wpdb;
		echo '<div class="wrap rtsap-campaign-builder"><h1>Email Campaign Builder</h1>'; self::notice();

	}
	public static function handle_ec_create()  { self::guard( 'ec_create' );  RTS_Business_Logic_5::create_campaign( array( 'name' => sanitize_text_field( $_POST['name'] ), 'trigger_type' => sanitize_key( $_POST['trigger_type'] ), 'trigger_days' => (int) $_POST['trigger_days'], 'audience_filter' => sanitize_key( $_POST['audience_filter'] ), 'category' => sanitize_key( $_POST['category'] ), 'created_by' => self::admin() ) ); self::back( 'rts-email-campaigns', 'Campaign created as draft.' ); }
	public static function handle_ec_save() {
		self::guard( 'ec_save' );
		$operation = sanitize_key( $_POST['operation'] ?? 'draft' );
		$status = in_array( $operation, array( 'schedule', 'send_now' ), true ) ? 'active' : sanitize_key( $_POST['existing_status'] ?? 'draft' );
		if ( 'draft' === $operation ) { $status = 'draft'; }
		$scheduled_at = '';
		$schedule_timestamp = 0;
		if ( ! empty( $_POST['scheduled_at'] ) ) {
			$date = date_create_immutable_from_format( 'Y-m-d\TH:i', sanitize_text_field( wp_unslash( $_POST['scheduled_at'] ) ), wp_timezone() );
			if ( $date ) { $scheduled_at = $date->format( 'Y-m-d H:i:s' ); $schedule_timestamp = $date->getTimestamp(); }
		}
		if ( 'send_now' === $operation && 'scheduled' === sanitize_key( $_POST['delivery_mode'] ?? '' ) && ! $scheduled_at ) { $scheduled_at = current_time( 'mysql' ); }
		$r = RTS_Business_Logic_5::save_campaign( array(
			'id' => absint( $_POST['id'] ?? 0 ), 'name' => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
			'template_id' => absint( $_POST['template_id'] ?? 0 ), 'delivery_mode' => sanitize_key( $_POST['delivery_mode'] ?? 'automation' ),
			'trigger_type' => sanitize_key( $_POST['trigger_type'] ?? 'days_after_registration' ), 'trigger_days' => absint( $_POST['trigger_days'] ?? 0 ),
			'audience_filter' => sanitize_key( $_POST['audience_filter'] ?? 'all' ), 'category' => sanitize_key( $_POST['category'] ?? 'general' ),
			'scheduled_at' => $scheduled_at, 'status' => $status, 'created_by' => self::admin(),
		) );
		if ( $r['error'] ) { self::back( 'rts-email-campaigns', 'Error: ' . $r['error'], array( 'campaign_id' => absint( $_POST['id'] ?? 0 ) ) ); }
		$id = (int) $r['campaign_id'];
		if ( in_array( $operation, array( 'draft', 'schedule', 'send_now' ), true ) ) { wp_clear_scheduled_hook( 'rts_run_scheduled_campaign', array( $id ) ); }
		if ( 'test' === $operation ) {
			$test = RTS_Business_Logic_5::send_campaign_test( $id, sanitize_email( wp_unslash( $_POST['test_email'] ?? '' ) ), self::admin() );
			self::back( 'rts-email-campaigns', $test['error'] ? 'Error: ' . $test['error'] : 'Test email sent and logged.', array( 'campaign_id' => $id ) );
		}
		if ( 'send_now' === $operation ) {
			$sent = RTS_Business_Logic_5::run_trigger_check( $id, self::admin(), true );
			self::back( 'rts-email-campaigns', $sent['error'] ? 'Error: ' . $sent['error'] : 'Campaign sent to ' . (int) $sent['newly_sent'] . ' recipient(s); ' . (int) $sent['excluded_unsubscribed'] . ' excluded by consent.', array( 'campaign_id' => $id ) );
		}
		if ( 'schedule' === $operation && 'scheduled' === sanitize_key( $_POST['delivery_mode'] ?? '' ) ) {
			if ( $schedule_timestamp <= time() ) {
				$sent = RTS_Business_Logic_5::run_trigger_check( $id, self::admin() );
				self::back( 'rts-email-campaigns', $sent['error'] ? 'Error: ' . $sent['error'] : 'Scheduled time had already arrived; campaign sent to ' . (int) $sent['newly_sent'] . ' recipient(s).', array( 'campaign_id' => $id ) );
			}
			$scheduled = wp_schedule_single_event( $schedule_timestamp, 'rts_run_scheduled_campaign', array( $id ), true );
			if ( is_wp_error( $scheduled ) ) { self::back( 'rts-email-campaigns', 'Error: SCHEDULE_FAILED — ' . $scheduled->get_error_message(), array( 'campaign_id' => $id ) ); }
		}
		self::back( 'rts-email-campaigns', 'schedule' === $operation ? 'Campaign activated. WordPress cron will process it at the configured trigger or scheduled time.' : 'Campaign saved as draft.', array( 'campaign_id' => $id ) );
	}
	public static function handle_ec_status()  {
		self::guard( 'ec_status' );
		$id = absint( $_POST['id'] ?? 0 ); $status = sanitize_key( $_POST['status'] ?? '' );
		$r = RTS_Business_Logic_5::set_campaign_status( $id, $status, self::admin() );
		if ( ! $r['error'] ) {
			wp_clear_scheduled_hook( 'rts_run_scheduled_campaign', array( $id ) );
			if ( 'active' === $status ) {
				global $wpdb; $campaign = $wpdb->get_row( $wpdb->prepare( "SELECT delivery_mode, scheduled_at FROM " . RTS_DB::table( 'email_campaigns' ) . " WHERE id = %d", $id ) );
				if ( $campaign && 'scheduled' === $campaign->delivery_mode && $campaign->scheduled_at ) {
					$when = date_create_immutable_from_format( 'Y-m-d H:i:s', $campaign->scheduled_at, wp_timezone() );
					if ( $when && $when->getTimestamp() > time() ) { wp_schedule_single_event( $when->getTimestamp(), 'rts_run_scheduled_campaign', array( $id ) ); }
				}
			}
		}
		self::back( 'rts-email-campaigns', $r['error'] ? 'Error: ' . $r['error'] : 'Status updated.' );
	}
	public static function handle_ec_trigger() { self::guard( 'ec_trigger' ); $r = RTS_Business_Logic_5::run_trigger_check( (int) $_POST['id'], self::admin() ); self::back( 'rts-email-campaigns', $r['error'] ? 'Error: ' . $r['error'] : 'Trigger check: eligible ' . (int) $r['eligible_count'] . ', newly sent ' . (int) $r['newly_sent'] . ', excluded (unsubscribed) ' . (int) $r['excluded_unsubscribed'] . '.' ); }

	// ---- Email Reporting ----
	public static function render_reporting() {
		$s = RTS_Business_Logic_5::reporting_stats();
		echo '<div class="wrap"><h1>Email Reporting Dashboard</h1><div style="display:flex;gap:12px;flex-wrap:wrap">' . self::kpi( 'Broadcast sends', $s['total_broadcast_sends'], $s['total_broadcast_recipients'] . ' total recipients' ) . self::kpi( 'Campaign sends', $s['total_campaign_sends'] ) . self::kpi( 'Open rate', 'n/a', 'no provider yet' ) . self::kpi( 'Click-through', 'n/a', 'no provider yet' ) . '</div>';
		echo '<p style="color:#666;font-size:12px;max-width:820px">Open / click / bounce rates require a real email provider webhook (Appendix F) — shown as n/a rather than a fake number.</p>';
		echo '<h3>By category</h3>' . self::tbl( array( 'Category', 'Recipients' ), array_map( fn( $r ) => array( $r->category, (int) $r->total ), $s['by_category'] ) );
		echo '<h3>Campaign breakdown</h3>' . self::tbl( array( 'Campaign', 'Sent' ), array_map( fn( $r ) => array( $r->name, (int) $r->sent_count ), $s['campaign_breakdown'] ) );
		echo '<h3>Recent broadcast sends</h3>' . self::tbl( array( 'Subject', 'Category', 'Recipients', 'Excluded (unsub)', 'When' ), array_map( fn( $r ) => array( $r->subject, $r->category, (int) $r->recipient_count, (int) $r->excluded_unsubscribed_count, $r->sent_at ), $s['recent_sends'] ) ) . '</div>';
	}

	// ---- Ad Campaign Analysis ----
	public static function render_ads() {
		echo '<div class="wrap"><h1>Ad Campaign Analysis</h1>'; self::notice();
		$rows = RTS_Business_Logic_5::ad_campaign_stats();
		$spend = array_sum( array_column( $rows, 'cost_charged' ) ); $int = array_sum( array_column( $rows, 'interested' ) ); $cred = array_sum( array_column( $rows, 'verified_credited' ) );
		echo '<div style="display:flex;gap:12px;flex-wrap:wrap">' . self::kpi( 'Total ad spend', '$' . $spend ) . self::kpi( 'Total interested', $int ) . self::kpi( 'Total verified & credited', $cred ) . self::kpi( 'Blended CAC', $cred ? '$' . round( $spend / $cred, 2 ) : '—' ) . '</div>';
		echo '<h3>New campaign</h3>' . self::form( 'ad_create', '<input type="text" name="name" placeholder="Name" required> <input type="text" name="platform" placeholder="Platform"> <input type="text" name="utm_campaign_code" placeholder="utm_campaign code" required> $<input type="number" step="0.01" name="cost_charged" placeholder="cost" style="width:90px"> <input type="number" name="impressions" placeholder="impressions" style="width:110px"> <input type="number" name="clicks" placeholder="clicks" style="width:90px"> ', 'Add' );
		echo '<h3>Campaigns</h3>' . self::tbl( array( 'Campaign', 'Platform', 'Cost', 'Impr.', 'Clicks', 'CTR', 'Interested', 'Cost/Interested', 'Verified & Credited', 'CAC' ), array_map( fn( $c ) => array( $c['name'], $c['platform'], '$' . $c['cost_charged'], $c['impressions'], $c['clicks'], $c['ctr'] . '%', $c['interested'], is_null( $c['cost_per_interested'] ) ? '—' : '$' . $c['cost_per_interested'], $c['verified_credited'], is_null( $c['cac'] ) ? '—' : '$' . $c['cac'] ), $rows ) );
		echo '<p style="color:#666;font-size:12px">"Interested" and "Verified &amp; Credited" are attributed automatically by matching <code>participants.utm_campaign</code> to the campaign\'s UTM code — not typed in. Cost/impressions/clicks are manual until a Google/Meta Ads API integration exists (Appendix F). Cost-per-Interested is a lead metric; CAC is the real customer-acquisition cost — don\'t substitute one for the other.</p></div>';
	}
	public static function handle_ad_create() { self::guard( 'ad_create' ); $r = RTS_Business_Logic_5::create_ad_campaign( array( 'name' => sanitize_text_field( $_POST['name'] ), 'platform' => sanitize_text_field( $_POST['platform'] ?? '' ), 'utm_campaign_code' => sanitize_text_field( $_POST['utm_campaign_code'] ), 'cost_charged' => (float) ( $_POST['cost_charged'] ?? 0 ), 'impressions' => (int) ( $_POST['impressions'] ?? 0 ), 'clicks' => (int) ( $_POST['clicks'] ?? 0 ), 'created_by' => self::admin() ) ); self::back( 'rts-ad-campaigns', $r['error'] ? 'Error: ' . $r['error'] : 'Campaign added.' ); }

	// ---- Interest & Notification Lists ----
	public static function render_interest() {
		$n = RTS_Business_Logic_5::notification_list(); $d = RTS_Business_Logic_5::declined_list();
		echo '<div class="wrap"><h1>Interest &amp; Notification Lists</h1><div style="display:flex;gap:12px;flex-wrap:wrap">' . self::kpi( 'Interested — notify list', count( $n ) ) . self::kpi( 'Declined further contact', count( $d ) ) . '</div>';
		echo '<h3>Interested — notify if the cruise goes ahead</h3>' . self::tbl( array( 'Name', 'Email', 'Verified', 'Source', 'Country' ), array_map( fn( $p ) => array( $p->name, $p->email, (int) $p->email_verified ? 'Yes' : 'Pending', $p->marketing_source, $p->country ), $n ) );
		echo '<h3>Declined further contact</h3><p style="color:#666;font-size:12px">Kept deliberately separate — excluded from every broadcast/campaign audience by default. The notify list above already excludes anyone here (mutually exclusive by construction).</p>' . self::tbl( array( 'Name', 'Email', 'Cabin credit', 'Country' ), array_map( fn( $p ) => array( $p->name, $p->email, $p->credit_status ?: '—', $p->country ), $d ) ) . '</div>';
	}

	// ---- Fraud Detection ----
	public static function render_fraud() {
		RTS_Business_Logic_5::duplicate_scan(); // refresh flags on every view
		$q = RTS_Business_Logic_5::fraud_queue();
		echo '<div class="wrap"><h1>Duplicate Detection &amp; Fraud Prevention</h1>'; self::notice();
		echo '<div style="display:flex;gap:12px;flex-wrap:wrap">' . self::kpi( 'Pending duplicate reviews', count( $q['duplicate_reviews'] ) ) . self::kpi( 'Flagged referrals', count( $q['flagged_referrals'] ) ) . '</div>';
		echo '<h3>Potential duplicate participants</h3><p style="color:#666;font-size:12px">Flagged by same name + same country — a heuristic, not a verdict. Every pair requires a human decision; nothing auto-merges or auto-suspends. A reviewed pair never reappears.</p>';
		$rows = array();
		foreach ( $q['duplicate_reviews'] as $x ) { $rows[] = array( "$x->name_a ($x->email_a)", "$x->name_b ($x->email_b)", $x->reason, self::form( 'dup_review', '', 'Approve as unique', array( 'a' => $x->participant_id_a, 'b' => $x->participant_id_b, 'decision' => 'approved_as_unique' ) ) . ' ' . self::form( 'dup_review', '', 'Confirm duplicate', array( 'a' => $x->participant_id_a, 'b' => $x->participant_id_b, 'decision' => 'rejected_as_duplicate' ), 'button button-link-delete' ) ); }
		echo self::tbl( array( 'Person A', 'Person B', 'Reason', 'Decision' ), $rows );
		$rows = array();
		foreach ( $q['flagged_referrals'] as $r ) { $rows[] = array( $r->referrer_name, $r->referred_name ?: '—', $r->fraud_review_status, 'rejected' === $r->fraud_review_status ? '' : self::form( 'reject_ref', '<input type="text" name="reason" placeholder="Reason" required> ', 'Reject', array( 'id' => $r->id ), 'button button-link-delete' ) ); }
		echo '<h3>Flagged referrals</h3>' . self::tbl( array( 'Referrer', 'Referred', 'Status', 'Action' ), $rows ) . '</div>';
	}
	public static function handle_dup_review() { self::guard( 'dup_review' ); $r = RTS_Business_Logic_5::review_duplicate( (int) $_POST['a'], (int) $_POST['b'], sanitize_key( $_POST['decision'] ), self::admin() ); self::back( 'rts-fraud', $r['error'] ? 'Error: ' . $r['error'] : 'Decision recorded — this pair will not be flagged again.' ); }
	public static function handle_reject_ref() { self::guard( 'reject_ref' ); $r = RTS_Business_Logic_5::reject_referral( (int) $_POST['id'], self::admin(), sanitize_text_field( $_POST['reason'] ?? '' ) ); self::back( 'rts-fraud', $r['error'] ? 'Error: ' . $r['error'] : 'Referral rejected.' ); }
}
