<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Admin_Menu_3 {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 30 );
		foreach ( array( 'defer_credit','void_credit','create_trophy','retro_unlock','run_draw','bc_create','bc_test_self','bc_test_group','bc_send_bulk' ) as $a ) {
			add_action( "admin_post_rts_$a", array( __CLASS__, "handle_$a" ) );
		}
	}

	public static function register_menu() {
		remove_submenu_page( 'rts-admin', 'rts-cabin-credits' ); // Batch 1 ledger replaced by the fuller v2 below
		RTS_Auth::page( 'rts-admin', 'Cabin Credits', 'Cabin Credits', 'rts_view', 'rts-cabin-credits', array( __CLASS__, 'render_credits' ) );
		RTS_Auth::page( 'rts-admin', 'Trophies', 'Trophies', 'rts_view', 'rts-trophies', array( __CLASS__, 'render_trophies' ) );
		RTS_Auth::page( 'rts-admin', 'Referrals & Draws', 'Referrals & Draws', 'rts_view', 'rts-referrals', array( __CLASS__, 'render_referrals' ) );
		RTS_Auth::page( 'rts-admin', 'Subscriptions', 'Subscriptions', 'rts_view', 'rts-subscriptions', array( __CLASS__, 'render_subscriptions' ) );
		RTS_Auth::page( 'rts-admin', 'Broadcast', 'Broadcast', 'rts_send_bulk', 'rts-broadcast', array( __CLASS__, 'render_broadcast' ) );
	}

	// ---- helpers (same pattern as Batch 2) ----
	private static function form( $action, $fields, $button, $hidden = array(), $class = 'button', $onsubmit = '' ) {
		$h = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline;"' . ( $onsubmit ? ' onsubmit="' . esc_attr( $onsubmit ) . '"' : '' ) . '>'
		   . '<input type="hidden" name="action" value="rts_' . esc_attr( $action ) . '">' . wp_nonce_field( 'rts_' . $action, '_rts_nonce', true, false );
		foreach ( $hidden as $k => $v ) { $h .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">'; }
		return $h . $fields . '<button class="' . esc_attr( $class ) . '">' . esc_html( $button ) . '</button></form>';
	}
	private static function guard( $a ) { if ( ! current_user_can( RTS_Auth::action_cap( $a ) ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_' . $a ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); } }
	private static function back( $page, $msg = '', $extra = array() ) { $args = $extra; if ( $msg ) { $args['rts_msg'] = rawurlencode( $msg ); } wp_safe_redirect( RTSAP_Frontend_Dashboard::screen_url( $page, $args ) ); exit; }
	private static function notice() { if ( ! empty( $_GET['rts_msg'] ) ) { $m = rawurldecode( $_GET['rts_msg'] ); $cls = str_starts_with( $m, 'Error' ) ? 'notice-error' : 'notice-success'; echo "<div class=\"notice $cls is-dismissible\"><p>" . esc_html( $m ) . '</p></div>'; } }
	private static function admin() { $u = wp_get_current_user(); return $u ? $u->user_login : 'admin'; }
	private static function kpi( $label, $value ) { return '<div style="background:#fff;border:1px solid #ccd0d4;border-top:3px solid #C9A24B;border-radius:4px;padding:12px 16px;min-width:160px;"><div style="font-size:11px;text-transform:uppercase;color:#666;font-weight:600;">' . esc_html( $label ) . '</div><div style="font-size:24px;font-weight:700;margin-top:4px;color:#0B1420;">' . esc_html( $value ) . '</div></div>'; }

	// ---- Cabin Credits (v2: defer + void) ----
	public static function render_credits() {
		$s = RTS_Business_Logic_3::credit_summary(); $rows = RTS_Business_Logic_3::credit_ledger();
		echo '<div class="wrap"><h1>Cabin Credit / Voucher Management</h1>'; self::notice();
		echo '<div style="display:flex;gap:12px;flex-wrap:wrap;margin:12px 0 20px">' . self::kpi( 'Issued', $s['issued'] ) . self::kpi( 'Deferred (2nd sailing)', $s['deferred'] ) . self::kpi( 'Redeemed', $s['redeemed'] ) . self::kpi( 'Cancelled / Void', $s['cancelled_or_void'] ) . self::kpi( 'Outstanding Liability', '$' . $s['outstanding_liability'] ) . '</div>';
		echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Founding Runner</th><th>Email</th><th>Status</th><th>Value</th><th>Issued</th><th>Actions</th></tr></thead><tbody>';
		foreach ( $rows as $c ) {
			echo '<tr><td>' . esc_html( $c->name ) . ' (' . esc_html( $c->founding_runner_number ) . ')</td><td>' . esc_html( $c->email ) . '</td><td>' . esc_html( $c->status ) . '</td><td>$' . esc_html( $c->value_usd ) . '</td><td>' . esc_html( $c->issued_at ) . '</td><td>';
			if ( 'issued' === $c->status ) {
				echo self::form( 'defer_credit', '<input type="text" name="reason" placeholder="Reason (e.g. sharing cabin)" required> ', 'Defer', array( 'id' => $c->id ) ) . ' ';
				echo self::form( 'void_credit', '<input type="text" name="reason" placeholder="Reason (required)" required> ', 'Void', array( 'id' => $c->id ), 'button button-link-delete' );
			}
			echo '</td></tr>';
		}
		echo '</tbody></table></div>';
	}
	public static function handle_defer_credit() { self::guard( 'defer_credit' ); $r = RTS_Business_Logic_3::defer_credit( (int) $_POST['id'], self::admin(), sanitize_text_field( $_POST['reason'] ?? '' ) ); self::back( 'rts-cabin-credits', $r['error'] ? 'Error: ' . $r['error'] : 'Credit deferred to 2nd sailing (still counted in liability).' ); }
	public static function handle_void_credit()  { self::guard( 'void_credit' );  $r = RTS_Business_Logic_3::void_credit(  (int) $_POST['id'], self::admin(), sanitize_text_field( $_POST['reason'] ?? '' ) ); self::back( 'rts-cabin-credits', $r['error'] ? 'Error: ' . $r['error'] : 'Credit voided.' ); }

	// ---- Trophies ----
	public static function render_trophies() {
		$s = RTS_Business_Logic_3::trophy_stats();
		echo '<div class="wrap"><h1>Trophy &amp; Achievement Management</h1>'; self::notice();
		echo '<div style="display:flex;gap:12px;flex-wrap:wrap;margin:12px 0 20px">' . self::kpi( 'Total unlocks (events)', $s['total_unlocks'] ) . self::kpi( 'Unique holders', $s['unique_holders'] ) . self::kpi( 'Verified w/ 0 trophies', $s['distribution']['0'] ) . '</div>';
		echo '<h3>Distribution</h3><table class="widefat striped" style="max-width:400px"><thead><tr><th>Trophy count</th><th>Participants</th></tr></thead><tbody>';
		foreach ( $s['distribution'] as $k => $v ) { echo '<tr><td>' . esc_html( $k ) . '</td><td>' . (int) $v . '</td></tr>'; }
		echo '</tbody></table>';
		echo '<h3>New Trophy</h3>' . self::form( 'create_trophy', '<input type="text" name="name" placeholder="Name" required> <input type="text" name="unlock_rule" placeholder="unlock_rule key" required> <select name="category"><option>repeatable</option><option>historical</option></select> ', 'Create' );
		echo '<h3>Trophies</h3><table class="wp-list-table widefat fixed striped"><thead><tr><th>Name</th><th>Unlock rule</th><th>Category</th><th>Unlocked</th><th>Retroactive unlock (explicit, logged)</th></tr></thead><tbody>';
		foreach ( $s['trophies'] as $t ) {
			$elig = RTS_Business_Logic_3::eligible_not_unlocked( $t->id ); $n = count( $elig );
			echo '<tr><td>' . esc_html( $t->name ) . '</td><td><code>' . esc_html( $t->unlock_rule ) . '</code></td><td>' . esc_html( $t->category ) . '</td><td>' . (int) $t->unlock_count . '</td><td>';
			echo $n ? self::form( 'retro_unlock', '', "Unlock for $n eligible", array( 'id' => $t->id, 'ids' => implode( ',', array_map( fn( $e ) => (int) $e->id, $elig ) ) ) ) : '<span style="color:#777">none eligible</span>';
			echo '</td></tr>';
		}
		echo '</tbody></table></div>';
	}
	public static function handle_create_trophy() { self::guard( 'create_trophy' ); RTS_Business_Logic_3::create_trophy( array( 'name' => sanitize_text_field( $_POST['name'] ), 'unlock_rule' => sanitize_key( $_POST['unlock_rule'] ), 'category' => sanitize_text_field( $_POST['category'] ?? 'repeatable' ), 'created_by' => self::admin() ) ); self::back( 'rts-trophies', 'Trophy created.' ); }
	public static function handle_retro_unlock()  { self::guard( 'retro_unlock' );  $ids = array_map( 'intval', explode( ',', $_POST['ids'] ?? '' ) ); $r = RTS_Business_Logic_3::retroactive_unlock( (int) $_POST['id'], $ids, self::admin() ); self::back( 'rts-trophies', $r['error'] ? 'Error: ' . $r['error'] : 'Retroactively unlocked for ' . (int) $r['unlocked_count'] . ' participant(s).' ); }

	// ---- Referrals & Draws ----
	public static function render_referrals() {
		$lb = RTS_Business_Logic_3::leaderboard(); $hist = RTS_Business_Logic_3::draw_history();
		echo '<div class="wrap"><h1>Referral &amp; Leaderboard Administration</h1>'; self::notice();
		echo '<h3>Live leaderboard (' . count( $lb ) . ')</h3><table class="wp-list-table widefat fixed striped"><thead><tr><th>Rank</th><th>Founding Runner</th><th>Verified referrals</th><th>Draw B eligible (42+)</th></tr></thead><tbody>';
		if ( ! $lb ) { echo '<tr><td colspan="4" style="color:#777">No verified referrals yet</td></tr>'; }
		foreach ( $lb as $i => $r ) { echo '<tr><td>' . ( $i + 1 ) . '</td><td>' . esc_html( $r->name ) . '</td><td>' . (int) $r->verified_referrals . '</td><td>' . ( $r->draw_b_eligible ? 'Yes' : 'No' ) . '</td></tr>'; }
		echo '</tbody></table><p style="margin:14px 0">'
		   . self::form( 'run_draw', '', 'Run Draw A', array( 'type' => 'A' ), 'button', 'return confirm(\'Run Draw A now? This selects a winner and is logged permanently with its seed.\')' ) . ' '
		   . self::form( 'run_draw', '', 'Run Draw B', array( 'type' => 'B' ), 'button button-primary', 'return confirm(\'Run Draw B now? This selects a winner and is logged permanently with its seed.\')' ) . '</p>';
		echo '<h3>Draw history</h3><table class="wp-list-table widefat fixed striped"><thead><tr><th>Type</th><th>Winner</th><th>Entries</th><th>Seed (reproducible)</th><th>Run by</th><th>When</th></tr></thead><tbody>';
		if ( ! $hist ) { echo '<tr><td colspan="6" style="color:#777">No draws run yet</td></tr>'; }
		foreach ( $hist as $d ) { echo '<tr><td>Draw ' . esc_html( $d->draw_type ) . '</td><td>' . esc_html( $d->winner_name ?: '—' ) . '</td><td>' . (int) $d->eligible_entry_count . '</td><td><code style="font-size:10px">' . esc_html( $d->random_seed ) . '</code></td><td>' . esc_html( $d->run_by ) . '</td><td>' . esc_html( $d->run_at ) . '</td></tr>'; }
		echo '</tbody></table></div>';
	}
	public static function handle_run_draw() { self::guard( 'run_draw' ); $r = RTS_Business_Logic_3::run_draw( sanitize_text_field( $_POST['type'] ), self::admin() ); self::back( 'rts-referrals', $r['error'] ? 'Error: ' . $r['error'] : 'Draw ' . sanitize_text_field( $_POST['type'] ) . ' winner: ' . $r['winner']->name . ' (from ' . (int) $r['entry_count'] . ' entries; seed ' . $r['seed'] . ').' ); }

	// ---- Subscriptions ----
	public static function render_subscriptions() {
		$s = RTS_Business_Logic_3::subscription_stats();
		echo '<div class="wrap"><h1>Subscription Management</h1>';
		echo '<table class="widefat striped" style="max-width:600px"><thead><tr><th>Category</th><th>Active</th><th>Unsubscribed</th><th>Rate</th></tr></thead><tbody>';
		foreach ( $s['by_category'] as $c ) { $t = $c['active'] + $c['unsubscribed']; echo '<tr><td>' . esc_html( $c['category'] ) . '</td><td>' . (int) $c['active'] . '</td><td>' . (int) $c['unsubscribed'] . '</td><td>' . ( $t ? round( $c['unsubscribed'] / $t * 100, 1 ) : 0 ) . '%</td></tr>'; }
		echo '</tbody></table><h3>Recent unsubscribes</h3><table class="wp-list-table widefat fixed striped"><thead><tr><th>Name</th><th>Email</th><th>Category</th><th>Reason</th><th>When</th></tr></thead><tbody>';
		if ( ! $s['recent_unsubscribes'] ) { echo '<tr><td colspan="5" style="color:#777">None yet</td></tr>'; }
		foreach ( $s['recent_unsubscribes'] as $r ) { echo '<tr><td>' . esc_html( $r->name ) . '</td><td>' . esc_html( $r->email ) . '</td><td>' . esc_html( $r->category ) . '</td><td>' . esc_html( $r->unsubscribe_reason ?: '—' ) . '</td><td>' . esc_html( $r->updated_at ) . '</td></tr>'; }
		echo '</tbody></table><p style="color:#666;font-size:12px">Unsubscribe takes effect instantly, no login required; transactional email (verification, credit confirmation) continues. Every bulk send is built through one function that excludes unsubscribed people — there is no bypass.</p></div>';
	}

	// ---- Broadcast with the send-gate ----
	public static function render_broadcast() {
		echo '<div class="wrap"><h1>Broadcast — Email Everyone</h1>'; self::notice();
		echo '<div class="notice notice-error" style="padding:10px"><b>⚠ Sends immediately</b> to the selected audience — no automation delay.</div>';
		$draft_id = (int) ( $_GET['draft'] ?? 0 );
		if ( ! $draft_id ) {
			echo '<h3>1. Draft &amp; audience</h3>' . self::form( 'bc_create',
				'<p><label>Audience <select name="audience_filter"><option value="all">All verified participants</option><option value="runners_only">Runners only</option><option value="non_runners_only">Non-runners only</option></select></label></p>'
			  . '<p><label>Category <select name="category"><option>general</option><option>survey</option><option>referral</option><option>trophy</option></select></label></p>'
			  . '<p><input type="text" name="subject" placeholder="Subject" required style="width:400px"></p><p><textarea name="body" placeholder="Body" style="width:400px;height:80px"></textarea></p>', 'Create draft & open send gate', array(), 'button button-primary' );
			echo '</div>'; return;
		}
		$g = RTS_Business_Logic_3::gate_status( $draft_id );
		if ( $g['error'] ) { echo '<p>Draft not found.</p></div>'; return; }
		$d = $g['draft']; $pv = RTS_Business_Logic_3::audience_preview( $d->category, $d->audience_filter );
		$self_ok = (int) $d->test_self_sent; $grp_ok = (int) $d->test_group_sent; $bulk_ok = (int) $d->bulk_sent;
		$dot = fn( $on ) => '<span style="display:inline-block;width:11px;height:11px;border-radius:50%;background:' . ( $on ? '#1E7B4D' : '#B23B3B' ) . ';margin-right:6px"></span>';
		$box = fn( $ok, $inner ) => '<div style="flex:1;min-width:240px;border:1.5px solid ' . ( $ok ? '#BFE3CF' : '#EFC0C0' ) . ';background:' . ( $ok ? '#E9F7EF' : '#FCEBEB' ) . ';border-radius:8px;padding:14px">' . $inner . '</div>';
		echo '<p>Draft #' . $draft_id . ' · <b>' . esc_html( $d->subject ) . '</b> · audience <b>' . esc_html( $d->audience_filter ) . '</b> · category <b>' . esc_html( $d->category ) . '</b> · preview: <b>' . (int) $pv['final_recipient_count'] . '</b> recipients (' . (int) $pv['excluded_unsubscribed'] . ' unsubscribed excluded)</p>';
		echo '<h3>2. Mandatory send sequence</h3><div style="display:flex;gap:12px;flex-wrap:wrap">';
		echo $box( $self_ok, $dot( $self_ok ) . '<b>1. Send test to myself</b><div style="font-size:12px;margin:6px 0">' . ( $self_ok ? 'Sent ✓ to ' . esc_html( $d->test_self_email ) : 'Not sent yet' ) . '</div>' . ( $self_ok ? '' : self::form( 'bc_test_self', '<input type="email" name="admin_email" value="' . esc_attr( wp_get_current_user()->user_email ) . '" required> ', 'Send test to me', array( 'id' => $draft_id ) ) ) );
		echo $box( $grp_ok, $dot( $grp_ok ) . '<b>2. Send to admin test group</b><div style="font-size:12px;margin:6px 0">' . ( $grp_ok ? 'Sent ✓ to ' . esc_html( $d->test_group_emails ) : 'Not sent yet' ) . '</div>' . ( $grp_ok ? '' : self::form( 'bc_test_group', '<input type="text" name="test_emails" placeholder="a@x.com, b@x.com" required style="width:220px"> ', 'Send to test group', array( 'id' => $draft_id ) ) ) );
		$ready = $g['ready_for_bulk'];
		$override_js = 'return confirm(\'You haven\\\'t sent the test to yourself or the admin test group yet. Are you sure you want to send to all recipients?\') && (document.getElementById(\'rts_force_reason\').value = prompt(\'This override will be logged. Why are you sending without completing both tests?\') || \'\') !== \'\';';
		echo $box( $ready || $bulk_ok, $dot( $ready || $bulk_ok ) . '<b>3. Send to bulk (' . (int) $pv['final_recipient_count'] . ')</b><div style="font-size:12px;margin:6px 0">' . ( $bulk_ok ? 'SENT ✓ at ' . esc_html( $d->bulk_sent_at ) . ( (int) $d->bulk_sent_forced ? ' <b style="color:#B23B3B">(gate overridden)</b>' : '' ) : ( $ready ? 'Ready to send' : 'Locked until steps 1 &amp; 2 are complete' ) ) . '</div>'
			. ( $bulk_ok ? '' : self::form( 'bc_send_bulk', '<input type="hidden" name="force_reason" id="rts_force_reason" value="">', 'Send to all', array( 'id' => $draft_id, 'force' => $ready ? '0' : '1' ), 'button button-primary', $ready ? '' : $override_js ) ) );
		echo '</div><p style="margin-top:16px"><a href="' . esc_url( admin_url( 'admin.php?page=rts-broadcast' ) ) . '">← New draft</a></p></div>';
	}
	public static function handle_bc_create()     { self::guard( 'bc_create' ); $r = RTS_Business_Logic_3::create_draft( array( 'category' => sanitize_text_field( $_POST['category'] ?? 'general' ), 'audience_filter' => sanitize_text_field( $_POST['audience_filter'] ?? 'all' ), 'subject' => sanitize_text_field( $_POST['subject'] ), 'body' => wp_kses_post( $_POST['body'] ?? '' ), 'created_by' => self::admin() ) ); if ( $r['error'] ) { self::back( 'rts-broadcast', 'Error: ' . $r['error'] ); } self::back( 'rts-broadcast', 'Draft created — complete the send sequence below.', array( 'draft' => $r['draft']->id ) ); }
	public static function handle_bc_test_self()  { self::guard( 'bc_test_self' );  $id = (int) $_POST['id']; $r = RTS_Business_Logic_3::test_self( $id, sanitize_email( $_POST['admin_email'] ?? '' ) ); self::back( 'rts-broadcast', $r['error'] ? 'Error: ' . $r['error'] : 'Test sent to yourself.', array( 'draft' => $id ) ); }
	public static function handle_bc_test_group() { self::guard( 'bc_test_group' ); $id = (int) $_POST['id']; $r = RTS_Business_Logic_3::test_group( $id, sanitize_text_field( $_POST['test_emails'] ?? '' ), self::admin() ); self::back( 'rts-broadcast', $r['error'] ? 'Error: ' . $r['error'] : 'Test sent to admin group.', array( 'draft' => $id ) ); }
	public static function handle_bc_send_bulk()  { self::guard( 'bc_send_bulk' );  $id = (int) $_POST['id']; $r = RTS_Business_Logic_3::send_bulk( $id, self::admin(), ! empty( $_POST['force'] ) && '1' === $_POST['force'], sanitize_text_field( $_POST['force_reason'] ?? '' ) ); self::back( 'rts-broadcast', $r['error'] ? 'Error: ' . $r['error'] . ( isset( $r['message'] ) ? ' — ' . $r['message'] : '' ) : 'Sent to ' . (int) $r['final_recipient_count'] . ' recipients (' . (int) $r['excluded_unsubscribed'] . ' unsubscribed excluded)' . ( $r['was_forced'] ? ' — GATE OVERRIDDEN, logged.' : '.' ), array( 'draft' => $id ) ); }
}
