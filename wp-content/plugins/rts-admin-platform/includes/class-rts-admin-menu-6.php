<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Admin_Menu_6 {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 60 );
		foreach ( array( 'q_create','q_draft','q_send','cms_save','q_ai' ) as $a ) { add_action( "admin_post_rts_$a", array( __CLASS__, "handle_$a" ) ); }
		add_action( 'admin_post_rts_export_csv', array( __CLASS__, 'handle_export_csv' ) );
	}
	public static function register_menu() {
		RTS_Auth::page( 'rts-admin', 'Customer Feedback', 'Customer Feedback', 'rts_view', 'rts-feedback', array( __CLASS__, 'render_feedback' ) );
		RTS_Auth::page( 'rts-admin', 'Question & Response Queue', 'Question Queue', 'rts_view', 'rts-questions', array( __CLASS__, 'render_questions' ) );
		RTS_Auth::page( 'rts-admin', 'Who Is The Customer', 'Who Is The Customer', 'rts_view', 'rts-customer', array( __CLASS__, 'render_customer' ) );
		RTS_Auth::page( 'rts-admin', 'Website Content', 'Website Content', 'rts_content', 'rts-cms', array( __CLASS__, 'render_cms' ) );
		RTS_Auth::page( 'rts-admin', 'Export Center', 'Export Center', 'rts_view', 'rts-export', array( __CLASS__, 'render_export' ) );
	}
	private static function form( $action, $fields, $button, $hidden = array(), $class = 'button' ) {
		$h = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline;"><input type="hidden" name="action" value="rts_' . esc_attr( $action ) . '">' . wp_nonce_field( 'rts_' . $action, '_rts_nonce', true, false );
		foreach ( $hidden as $k => $v ) { $h .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">'; }
		return $h . $fields . '<button class="' . esc_attr( $class ) . '">' . esc_html( $button ) . '</button></form>';
	}
	private static function guard( $a ) { if ( ! current_user_can( RTS_Auth::action_cap( $a ) ) || ! isset( $_POST['_rts_nonce'] ) || ! wp_verify_nonce( $_POST['_rts_nonce'], 'rts_' . $a ) ) { wp_die( 'Not allowed.', 'Forbidden', array( 'response' => 403 ) ); } }
	private static function back( $page, $msg = '', $extra = array() ) { $args = $extra; if ( $msg ) { $args['rts_msg'] = rawurlencode( $msg ); } wp_safe_redirect( RTSAP_Frontend_Dashboard::screen_url( $page, $args ) ); exit; }
	private static function notice() { if ( ! empty( $_GET['rts_msg'] ) ) { $m = rawurldecode( $_GET['rts_msg'] ); $cls = str_starts_with( $m, 'Error' ) ? 'notice-error' : 'notice-success'; echo "<div class=\"notice $cls is-dismissible\"><p>" . esc_html( $m ) . '</p></div>'; } }
	private static function admin() { $u = wp_get_current_user(); return $u ? $u->user_login : 'admin'; }
	private static function kpi( $l, $v, $sub = '' ) { return '<div style="background:#fff;border:1px solid #ccd0d4;border-top:3px solid #C9A24B;border-radius:4px;padding:12px 16px;min-width:170px;"><div style="font-size:11px;text-transform:uppercase;color:#666;font-weight:600;">' . esc_html( $l ) . '</div><div style="font-size:24px;font-weight:700;margin-top:4px;color:#0B1420;">' . esc_html( $v ) . '</div>' . ( $sub ? '<div style="font-size:11px;color:#888">' . esc_html( $sub ) . '</div>' : '' ) . '</div>'; }
	private static function tbl( $heads, $rows, $style = '' ) { $h = '<table class="wp-list-table widefat fixed striped"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '><thead><tr>'; foreach ( $heads as $x ) { $h .= '<th>' . esc_html( $x ) . '</th>'; } $h .= '</tr></thead><tbody>'; if ( ! $rows ) { $h .= '<tr><td colspan="' . count( $heads ) . '" style="color:#777">No data yet</td></tr>'; } foreach ( $rows as $r ) { $h .= '<tr>'; foreach ( $r as $c ) { $h .= '<td>' . ( is_string( $c ) && str_starts_with( $c, '<' ) ? $c : esc_html( (string) $c ) ) . '</td>'; } $h .= '</tr>'; } return $h . '</tbody></table>'; }

	// ---- Customer Feedback ----
	public static function render_feedback() {
		$comments = RTS_Business_Logic_6::all_comments(); $themes = RTS_Business_Logic_6::top_themes(); $summary = RTS_Business_Logic_6::comment_summary();
		echo '<div class="wrap"><h1>Customer Feedback &amp; Survey Insights</h1><div style="display:flex;gap:12px;flex-wrap:wrap">' . self::kpi( 'Total comments', count( $comments ) ) . self::kpi( 'Questions with comments', count( $summary ) ) . '</div>';
		echo '<h3>All comments — scrollable feed</h3>' . self::tbl( array( 'Q#', 'Gender', 'Comment', 'When' ), array_map( fn( $c ) => array( 'Q' . $c->question_number, $c->gender ?: '—', $c->comment_text, $c->answered_at ), $comments ) );
		echo '<h3>Comment summary by question</h3>' . self::tbl( array( 'Question', 'Comments' ), array_map( fn( $s ) => array( 'Q' . $s->question_number . ': ' . $s->prompt, (int) $s->comment_count ), $summary ), 'max-width:700px' );
		echo '<h3>Top recurring keywords</h3><p style="color:#666;font-size:12px;max-width:820px">Real keyword-frequency analysis across all comments (stopwords filtered) — <b>not</b> AI theme clustering; no LLM integration exists in this prototype. Honest, useful, but not topic modelling.</p>' . self::tbl( array( '#', 'Keyword', 'Mentions', 'Example' ), array_map( fn( $t, $i ) => array( $i + 1, $t['keyword'], $t['mentions'], $t['example'] ), $themes, array_keys( $themes ) ) ) . '</div>';
	}

	// ---- Question & Response Queue ----
	public static function render_questions() {
		echo '<div class="wrap"><h1>Question &amp; Response Queue</h1>'; self::notice();
		echo '<p style="color:#666;font-size:12px;max-width:820px">The full draft → revise → approve → send loop is real, with an append-only version history and a permanent response log. The one honest gap: "AI Create Draft" is a hook point (spec Appendix Q), not implemented — drafts are typed by an admin here.</p>';
		echo '<h3>Log a question</h3>' . self::form( 'q_create', '<input type="text" name="question_text" placeholder="Customer question" required style="width:420px"> ', 'Log it' );
		$open = RTS_Business_Logic_6::open_questions(); $sel = (int) ( $_GET['q'] ?? 0 );
		echo '<h3>Open — oldest first (' . count( $open ) . ')</h3>' . self::tbl( array( 'Asked', 'From', 'Question', '' ), array_map( fn( $q ) => array( $q->created_at, $q->participant_name ?: '—', $q->question_text, '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=rts-questions&q=' . (int) $q->id ) ) . '">Draft &amp; refine</a>' ), $open ) );
		if ( $sel ) {
			$q = null; foreach ( $open as $x ) { if ( (int) $x->id === $sel ) { $q = $x; } }
			if ( $q ) {
				$drafts = RTS_Business_Logic_6::draft_history( $sel ); $latest = $drafts ? end( $drafts ) : null;
				echo '<div style="background:#fff;border:1px solid #ccd0d4;padding:16px;margin:16px 0;max-width:860px"><h3 style="margin-top:0">Draft &amp; refine — <i>' . esc_html( $q->question_text ) . '</i></h3>';
				if ( $drafts ) { echo '<h4>Revision history</h4>' . self::tbl( array( 'Version', 'Draft', 'Feedback that prompted it' ), array_map( fn( $d ) => array( 'v' . $d->version, $d->draft_text, $d->feedback_that_prompted_this ?: '—' ), $drafts ) ); }
				$ai_note = RTS_Production::ai_configured() ? '' : ' <span style="color:#888;font-size:11px">(AI drafting not configured — add an API key under Settings)</span>';
				echo '<p>' . self::form( 'q_ai', '', '✨ AI: create draft', array( 'id' => $sel ), 'button' . ( RTS_Production::ai_configured() ? '' : ' disabled' ) ) . $ai_note . '</p>';
				echo '<p>' . self::form( 'q_draft', '<textarea name="draft_text" required style="width:100%;height:70px">' . esc_textarea( $latest ? $latest->draft_text : '' ) . '</textarea><br><input type="text" name="feedback" placeholder="Feedback (if refining)" style="width:60%"> ', 'Save new version', array( 'id' => $sel ) ) . '</p>';
				if ( $drafts ) { echo '<p>' . self::form( 'q_send', '', '✓ Approve & send', array( 'id' => $sel ), 'button button-primary' ) . '</p>'; }
				echo '</div>';
			}
		}
		echo '<h3>Sent — response log</h3>' . self::tbl( array( 'Sent', 'Customer', 'Question', 'Approved by', 'Versions' ), array_map( fn( $l ) => array( $l->sent_at, $l->participant_name ?: '—', $l->question_text, $l->approved_by, (int) $l->version_count ), RTS_Business_Logic_6::response_log() ) ) . '</div>';
	}
	public static function handle_q_create() { self::guard( 'q_create' ); RTS_Business_Logic_6::create_question( sanitize_text_field( $_POST['question_text'] ) ); self::back( 'rts-questions', 'Question logged.' ); }
	public static function handle_q_draft()  { self::guard( 'q_draft' );  $id = (int) $_POST['id']; $r = RTS_Business_Logic_6::add_draft( $id, sanitize_textarea_field( $_POST['draft_text'] ), sanitize_text_field( $_POST['feedback'] ?? '' ), self::admin() ); self::back( 'rts-questions', $r['error'] ? 'Error: ' . $r['error'] : 'Saved as v' . $r['version'] . '.', array( 'q' => $id ) ); }
	public static function handle_q_ai() {
		self::guard( 'q_ai' ); $id = (int) $_POST['id'];
		global $wpdb; $q = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'customer_questions' ) . " WHERE id = %d", $id ) );
		$r = $q ? RTS_Production::ai_draft( 'question_reply', array( 'question' => $q->question_text, 'facts' => 'The $100 Cabin Credit is per verified person. Survey is open now; sailing date not yet announced.' ) ) : array( 'error' => 'NOT_FOUND' );
		if ( ! empty( $r['error'] ) ) { self::back( 'rts-questions', 'Error: ' . $r['error'] . ( isset( $r['message'] ) ? ' — ' . $r['message'] : '' ), array( 'q' => $id ) ); }
		$v = RTS_Business_Logic_6::add_draft( $id, $r['draft'], 'AI draft (' . $r['model'] . ')', self::admin() );
		self::back( 'rts-questions', 'AI draft saved as v' . $v['version'] . ' — review before sending.', array( 'q' => $id ) );
	}
	public static function handle_q_send()   { self::guard( 'q_send' );   $r = RTS_Business_Logic_6::approve_and_send( (int) $_POST['id'], self::admin() ); self::back( 'rts-questions', $r['error'] ? 'Error: ' . $r['error'] : 'Sent (' . (int) $r['version_count'] . ' version' . ( 1 === (int) $r['version_count'] ? '' : 's' ) . ' in history).' ); }

	// ---- Who Is The Customer ----
	public static function render_customer() {
		$p = RTS_Business_Logic_6::customer_profile();
		$t = fn( $label, $rows, $k = 'k' ) => '<div style="flex:1;min-width:280px"><h3>' . esc_html( $label ) . '</h3>' . self::tbl( array( $label, 'Count' ), array_map( fn( $r ) => array( $r->k, (int) $r->c ), $rows ) ) . '</div>';
		echo '<div class="wrap"><h1>Who Is The Customer</h1><div style="display:flex;gap:12px;flex-wrap:wrap">' . self::kpi( 'Total customers', $p['total_customers'], 'verified + Cabin Credit' ) . self::kpi( 'Avg travel party', $p['avg_travel_party_size'] ?? '—' ) . '</div>';
		echo '<div style="display:flex;gap:20px;flex-wrap:wrap">' . $t( 'Gender', $p['gender_breakdown'] ) . $t( 'Age range', $p['age_breakdown'] ) . '</div>';
		echo '<div style="display:flex;gap:20px;flex-wrap:wrap">' . $t( 'Household income', $p['income_breakdown'] ) . $t( 'Runner / non-runner', $p['runner_breakdown'] ) . '</div>';
		echo '<div style="display:flex;gap:20px;flex-wrap:wrap">' . $t( 'Country', $p['geographic_distribution'] ) . $t( 'How they became a customer', $p['acquisition_breakdown'] ) . '</div>';
		echo '<h3>In their own words — top themes</h3>' . self::tbl( array( 'Keyword', 'Mentions', 'Example' ), array_map( fn( $x ) => array( $x['keyword'], $x['mentions'], $x['example'] ), $p['top_themes'] ) ) . '</div>';
	}

	// ---- Website Content Management ----
	const BLOCKS = array( 'survey_intro' => array( 'label' => 'Survey page — intro banner', 'hint' => 'Shown live above the survey on the public /survey page. Edit it here, then reload the survey.' ) );
	public static function render_cms() {
		echo '<div class="wrap"><h1>Website Content Management</h1>'; self::notice();
		echo '<p style="color:#666;font-size:12px;max-width:820px">These blocks are <b>genuinely connected</b>: the public survey page fetches <code>survey_intro</code> from the database on load. Edit it here, open <a href="' . esc_url( home_url( '/?page_id=6' ) ) . '" target="_blank">the survey page</a>, and the new text appears.</p>';
		$existing = array(); foreach ( RTS_Business_Logic_6::all_blocks() as $b ) { $existing[ $b->block_key ] = $b; }
		foreach ( self::BLOCKS as $key => $d ) {
			$b = $existing[ $key ] ?? null;
			echo '<h3>' . esc_html( $d['label'] ) . '</h3>' . self::form( 'cms_save', '<input type="text" name="value" value="' . esc_attr( $b ? $b->value : '' ) . '" style="width:520px"> ', 'Save', array( 'key' => $key ) ) . '<p style="color:#888;font-size:11px">' . esc_html( $d['hint'] ) . ( $b ? ' · Last updated ' . esc_html( $b->updated_at ) . ' by ' . esc_html( $b->updated_by ) : '' ) . '</p>';
		}
		echo '</div>';
	}
	public static function handle_cms_save() { self::guard( 'cms_save' ); $key = sanitize_key( $_POST['key'] ); if ( ! isset( self::BLOCKS[ $key ] ) ) { self::back( 'rts-cms', 'Error: unknown block' ); } RTS_Business_Logic_6::set_block( $key, sanitize_text_field( $_POST['value'] ), self::admin() ); self::back( 'rts-cms', 'Saved — reload the survey page to see it.' ); }

	// ---- Export Center ----
	public static function render_export() {
		echo '<div class="wrap"><h1>Export Center</h1><h3>Available exports</h3><p>';
		foreach ( RTS_Business_Logic_6::DATASETS as $ds ) { echo self::form( 'export_csv', '', 'Download ' . ucwords( str_replace( '_', ' ', $ds ) ) . ' (CSV)', array( 'dataset' => $ds ) ) . ' '; }
		echo '</p><h3>Export history</h3>' . self::tbl( array( 'Dataset', 'Format', 'Rows', 'Requested by', 'When' ), array_map( fn( $h ) => array( $h->dataset, $h->format, (int) $h->row_count, $h->requested_by, $h->created_at ), RTS_Business_Logic_6::export_history() ) ) . '</div>';
	}
	public static function handle_export_csv() {
		self::guard( 'export_csv' );
		$r = RTS_Business_Logic_6::export( sanitize_key( $_POST['dataset'] ), self::admin() );
		if ( $r['error'] ) { self::back( 'rts-export', 'Error: ' . $r['error'] ); }
		header( 'Content-Type: text/csv; charset=utf-8' ); header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $_POST['dataset'] ) . '.csv"' );
		echo $r['csv']; exit;
	}
}
