<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Admin_Menu_2 {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 20 );
		// WordPress-native form handling: admin_post_{action} with nonce verification.
		foreach ( array( 'clone_survey','survey_status','suspend','reinstate','merge','manual_verify','create_template','update_template','rollback_template' ) as $a ) {
			add_action( "admin_post_rts_$a", array( __CLASS__, "handle_$a" ) );
		}
	}

	public static function register_menu() {
		RTS_Auth::page( 'rts-admin', 'Survey Administration', 'Survey Administration', 'rts_view', 'rts-surveys', array( __CLASS__, 'render_surveys' ) );
		RTS_Auth::page( 'rts-admin', 'Verification Queue', 'Verification Queue', 'rts_view', 'rts-verification-queue', array( __CLASS__, 'render_queue' ) );
		RTS_Auth::page( 'rts-admin', 'Email Templates', 'Email Templates', 'rts_view', 'rts-email-templates', array( __CLASS__, 'render_templates' ) );
		RTS_Auth::page( null, 'Participant Profile', 'Participant Profile', 'rts_view', 'rts-participant-profile', array( __CLASS__, 'render_profile' ) ); // hidden from menu; reached via Participants list
	}

	// ---------- helpers ----------
	private static function form( $action, $fields_html, $button, $extra_hidden = array() ) {
		$h = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline;">'
		   . '<input type="hidden" name="action" value="rts_' . esc_attr( $action ) . '">' . wp_nonce_field( 'rts_' . $action, '_rts_nonce', true, false );
		foreach ( $extra_hidden as $k => $v ) { $h .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">'; }
		return $h . $fields_html . '<button class="button">' . esc_html( $button ) . '</button></form>';
	}
	private static function guard( $action ) {
		if ( ! current_user_can( RTS_Auth::action_cap( $action ) ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_' . $action ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); }
	}
	private static function back( $page, $msg = '', $extra = array() ) {
		$args = array_merge( array( 'page' => $page ), $extra );
		if ( $msg ) { $args['rts_msg'] = rawurlencode( $msg ); }
		wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) ); exit;
	}
	private static function notice() {
		if ( ! empty( $_GET['rts_msg'] ) ) { echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( rawurldecode( $_GET['rts_msg'] ) ) . '</p></div>'; }
	}
	private static function admin() { $u = wp_get_current_user(); return $u ? $u->user_login : 'admin'; }

	// ---------- Survey Administration ----------
	public static function render_surveys() {
		echo '<div class="wrap"><h1>Survey Administration</h1>'; self::notice();
		$rows = RTS_Business_Logic_2::list_surveys();
		echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Name</th><th>Status</th><th>Responses</th><th>Completion</th><th>Actions</th></tr></thead><tbody>';
		foreach ( $rows as $s ) {
			echo '<tr><td>' . esc_html( $s->name ) . '</td><td>' . esc_html( $s->status ) . '</td><td>' . (int) $s->total_responses . '</td><td>' . esc_html( $s->completion_rate ) . '%</td><td>';
			echo self::form( 'clone_survey', '', 'Clone', array( 'id' => $s->id ) ) . ' ';
			if ( 'live' !== $s->status ) { echo self::form( 'survey_status', '', 'Publish', array( 'id' => $s->id, 'status' => 'live' ) ) . ' '; }
			if ( 'live' === $s->status ) { echo self::form( 'survey_status', '', 'Archive', array( 'id' => $s->id, 'status' => 'archived' ) ); }
			echo '</td></tr>';
		}
		echo '</tbody></table></div>';
	}
	public static function handle_clone_survey() { self::guard( 'clone_survey' ); $r = RTS_Business_Logic_2::clone_survey( (int) $_POST['id'], null, self::admin() ); self::back( 'rts-surveys', $r['error'] ? 'Error: ' . $r['error'] : 'Survey cloned (new id ' . $r['new_survey_id'] . ') — conditional logic preserved.' ); }
	public static function handle_survey_status() { self::guard( 'survey_status' ); $r = RTS_Business_Logic_2::set_survey_status( (int) $_POST['id'], sanitize_text_field( $_POST['status'] ), self::admin() ); self::back( 'rts-surveys', $r['error'] ? 'Error: ' . $r['error'] : 'Status updated.' ); }

	// ---------- Participant Profile ----------
	public static function render_profile() {
		global $wpdb;
		$id = (int) ( $_GET['id'] ?? 0 );
		$p = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'participants' ) . " WHERE id = %d", $id ) );
		echo '<div class="wrap"><h1>Participant Profile</h1>'; self::notice();
		if ( ! $p ) { echo '<p>Not found.</p></div>'; return; }
		$refs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'referrals' ) . " WHERE referring_participant_id = %d", $id ) );
		$troph = $wpdb->get_results( $wpdb->prepare( "SELECT t.name, u.unlocked_at FROM " . RTS_DB::table( 'trophy_unlocks' ) . " u JOIN " . RTS_DB::table( 'trophies' ) . " t ON t.id = u.trophy_id WHERE u.participant_id = %d", $id ) );
		$credit = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'cabin_credits' ) . " WHERE participant_id = %d", $id ) );

		echo '<h2>' . esc_html( $p->name ) . ' <small>(' . esc_html( $p->account_status ) . ')</small></h2>';
		echo '<p>FRN: <b>' . esc_html( $p->founding_runner_number ?: '—' ) . '</b> · ' . esc_html( $p->email ) . ' · ' . ( $p->email_verified ? 'Verified' : 'Pending' ) . '</p>';
		echo '<p>';
		if ( 'suspended' === $p->account_status ) { echo self::form( 'reinstate', '', 'Reinstate Account', array( 'id' => $id ) ); }
		else { echo self::form( 'suspend', '<input type="text" name="reason" placeholder="Reason" required> ', 'Suspend Account', array( 'id' => $id ) ); }
		echo '</p>';

		echo '<h3>Referrals (' . count( $refs ) . ')</h3><table class="widefat striped"><thead><tr><th>Referred ID</th><th>Verified</th><th>Fraud status</th></tr></thead><tbody>';
		foreach ( $refs as $r ) { echo '<tr><td>' . (int) $r->referred_participant_id . '</td><td>' . ( $r->verified ? 'Yes' : 'No' ) . '</td><td>' . esc_html( $r->fraud_review_status ) . '</td></tr>'; }
		echo '</tbody></table>';

		echo '<h3>Trophies (' . count( $troph ) . ')</h3><ul>'; foreach ( $troph as $t ) { echo '<li>' . esc_html( $t->name ) . ' — ' . esc_html( $t->unlocked_at ) . '</li>'; } echo '</ul>';
		echo '<h3>Cabin Credit</h3><p>' . ( $credit ? esc_html( $credit->status ) . ' · $' . esc_html( $credit->value_usd ) : 'None yet — verification issues it.' ) . '</p>';

		echo '<h3>Merge Duplicate</h3><p>Merge another participant INTO this one. Preview shows exactly what will change; nothing commits until you confirm.</p>';
		echo self::form( 'merge', '<input type="number" name="merge_id" placeholder="Duplicate participant ID" required> <input type="hidden" name="mode" value="preview">', 'Preview Merge', array( 'keep_id' => $id ) );
		// Preview is stashed in a short-lived transient keyed to the current user — NOT passed
		// through the URL. (A first attempt JSON-encoded it into a query arg; add_query_arg mangled
		// the empty-array brackets and the page fatal-errored on count(null). Transients are the
		// WordPress-idiomatic way to hand state between a POST handler and the redirect target.)
		$pv_key = 'rts_merge_preview_' . get_current_user_id();
		if ( isset( $_GET['merge_preview'] ) && ( $pv = get_transient( $pv_key ) ) ) {
			delete_transient( $pv_key );
			$mid = (int) $pv['merge_id']; $pv = $pv['preview'];
			echo '<div class="notice notice-warning" style="padding:12px"><b>This merge will:</b><ul>'
			   . '<li>Reassign ' . (int) $pv['referrals_to_reassign'] . ' referral(s) to this record</li>'
			   . '<li>Merge ' . count( (array) $pv['trophies_to_merge'] ) . ' trophy unlock(s)</li>'
			   . '<li>' . ( $pv['credit_conflict'] ? '⚠ BOTH records have a Cabin Credit — the duplicate\'s will be cancelled' : 'No Cabin Credit conflict' ) . '</li></ul>'
			   . self::form( 'merge', '<input type="hidden" name="mode" value="commit">', 'Confirm — Commit This Merge', array( 'keep_id' => $id, 'merge_id' => $mid ) ) . '</div>';
		}
		echo '</div>';
	}
	public static function handle_suspend()   { self::guard( 'suspend' );   RTS_Business_Logic_2::set_account_status( (int) $_POST['id'], 'suspended', self::admin(), sanitize_text_field( $_POST['reason'] ?? '' ) ); self::back( 'rts-participant-profile', 'Account suspended.', array( 'id' => (int) $_POST['id'] ) ); }
	public static function handle_reinstate() { self::guard( 'reinstate' ); RTS_Business_Logic_2::set_account_status( (int) $_POST['id'], 'active', self::admin() ); self::back( 'rts-participant-profile', 'Account reinstated.', array( 'id' => (int) $_POST['id'] ) ); }
	public static function handle_merge() {
		self::guard( 'merge' );
		$keep = (int) $_POST['keep_id']; $merge = (int) $_POST['merge_id']; $mode = $_POST['mode'] ?? 'preview';
		$r = RTS_Business_Logic_2::merge_duplicates( $keep, $merge, 'commit' === $mode );
		if ( $r['error'] ) { self::back( 'rts-participant-profile', 'Error: ' . $r['error'], array( 'id' => $keep ) ); }
		if ( 'commit' === $mode ) { self::back( 'rts-participant-profile', 'Merge committed.', array( 'id' => $keep ) ); }
		set_transient( 'rts_merge_preview_' . get_current_user_id(), array( 'merge_id' => $merge, 'preview' => $r['preview'] ), 5 * MINUTE_IN_SECONDS );
		self::back( 'rts-participant-profile', '', array( 'id' => $keep, 'merge_preview' => 1 ) );
	}

	// ---------- Verification Queue ----------
	public static function render_queue() {
		$q = RTS_Business_Logic_2::get_verification_queue();
		echo '<div class="wrap"><h1>Email Verification Queue</h1>'; self::notice();
		echo '<p><b>Pending:</b> ' . (int) $q['pending_count'] . ' · <b>Verification rate:</b> ' . esc_html( $q['verification_rate'] ) . '%</p>';
		if ( ! $q['pending_count'] ) { echo '<p>🎉 All caught up.</p></div>'; return; }
		echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Name</th><th>Email</th><th>Sent</th><th>Manual verify (reason required, logged)</th></tr></thead><tbody>';
		foreach ( $q['pending'] as $p ) {
			echo '<tr><td>' . esc_html( $p->name ) . '</td><td>' . esc_html( $p->email ) . '</td><td>' . esc_html( $p->verification_sent_at ) . '</td><td>'
			   . self::form( 'manual_verify', '<input type="text" name="reason" placeholder="Reason" required> ', 'Manually Verify', array( 'id' => $p->id ) ) . '</td></tr>';
		}
		echo '</tbody></table></div>';
	}
	public static function handle_manual_verify() { self::guard( 'manual_verify' ); $r = RTS_Business_Logic_2::manually_verify( (int) $_POST['id'], self::admin(), sanitize_text_field( $_POST['reason'] ?? '' ) ); self::back( 'rts-verification-queue', $r['error'] ? 'Error: ' . $r['error'] : 'Verified — Cabin Credit issued, same side effects as a real link click.' ); }

	// ---------- Email Templates ----------
	public static function render_templates() {
		global $wpdb;
		echo '<div class="wrap"><h1>Email Template Library</h1>'; self::notice();
		$rows = $wpdb->get_results( "SELECT * FROM " . RTS_DB::table( 'email_templates' ) . " ORDER BY updated_at DESC" );
		echo '<h3>New Template</h3>' . self::form( 'create_template',
			'<input type="text" name="name" placeholder="Name" required> <input type="text" name="subject" placeholder="Subject" required> <select name="category"><option>onboarding</option><option>acquisition</option><option>engagement</option><option>transactional</option><option>milestone</option></select> <input type="text" name="html_body" placeholder="Body" style="width:300px"> ', 'Create' );
		echo '<h3>Templates (' . count( $rows ) . ')</h3><table class="wp-list-table widefat fixed striped"><thead><tr><th>Name</th><th>Subject</th><th>Category</th><th>Version</th><th>Update (new version)</th><th>History / Rollback</th></tr></thead><tbody>';
		foreach ( $rows as $t ) {
			$versions = RTS_Business_Logic_2::template_versions( $t->id );
			$hist = '';
			foreach ( $versions as $v ) {
				$hist .= 'v' . (int) $v->version . ': ' . esc_html( $v->subject ) . ( (int) $v->version === (int) $t->version ? ' <b>(current)</b>' : ' ' . self::form( 'rollback_template', '', 'Roll back to v' . (int) $v->version, array( 'id' => $t->id, 'to_version' => $v->version ) ) ) . '<br>';
			}
			echo '<tr><td>' . esc_html( $t->name ) . '</td><td>' . esc_html( $t->subject ) . '</td><td>' . esc_html( $t->category ) . '</td><td>v' . (int) $t->version . '</td>'
			   . '<td>' . self::form( 'update_template', '<input type="text" name="subject" placeholder="New subject" required> ', 'Save', array( 'id' => $t->id ) ) . '</td><td style="font-size:12px">' . $hist . '</td></tr>';
		}
		echo '</tbody></table></div>';
	}
	public static function handle_create_template()   { self::guard( 'create_template' );   $r = RTS_Business_Logic_2::create_template( array( 'name' => sanitize_text_field( $_POST['name'] ), 'subject' => sanitize_text_field( $_POST['subject'] ), 'category' => sanitize_text_field( $_POST['category'] ?? 'general' ), 'html_body' => wp_kses_post( $_POST['html_body'] ?? '' ), 'created_by' => self::admin() ) ); self::back( 'rts-email-templates', 'Template created.' ); }
	public static function handle_update_template()   { self::guard( 'update_template' );   $r = RTS_Business_Logic_2::update_template( (int) $_POST['id'], array( 'subject' => sanitize_text_field( $_POST['subject'] ), 'updated_by' => self::admin() ) ); self::back( 'rts-email-templates', $r['error'] ? 'Error: ' . $r['error'] : 'Saved as v' . $r['new_version'] . ' — prior versions kept.' ); }
	public static function handle_rollback_template() { self::guard( 'rollback_template' ); $r = RTS_Business_Logic_2::rollback_template( (int) $_POST['id'], (int) $_POST['to_version'], self::admin() ); self::back( 'rts-email-templates', $r['error'] ? 'Error: ' . $r['error'] : 'Rolled back — created v' . $r['new_version'] . ' with the old content; history untouched.' ); }
}
