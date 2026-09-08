<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Admin_Menu_7 {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 70 );
		foreach ( array( 'rb_save','rb_run','seg_save','ai_resolve' ) as $a ) { add_action( "admin_post_rts_$a", array( __CLASS__, "handle_$a" ) ); }
	}
	public static function register_menu() {
		RTS_Auth::page( 'rts-admin', 'Report Builder', 'Report Builder', 'rts_view', 'rts-report-builder', array( __CLASS__, 'render_builder' ) );
		RTS_Auth::page( 'rts-admin', 'Saved & Scheduled Reports', 'Saved Reports', 'rts_view', 'rts-saved-reports', array( __CLASS__, 'render_saved' ) );
		RTS_Auth::page( 'rts-admin', 'Build Custom Segment', 'Segments', 'rts_view', 'rts-segments', array( __CLASS__, 'render_segments' ) );
		RTS_Auth::page( 'rts-admin', 'Quick Reports — Number Reference', 'Quick Reports', 'rts_view', 'rts-quick-reports', array( __CLASS__, 'render_quick' ) );
		RTS_Auth::page( 'rts-admin', 'Action Items', 'Action Items', 'rts_view', 'rts-action-items', array( __CLASS__, 'render_actions' ) );
		RTS_Auth::page( 'rts-admin', 'Estimate Cabin Sales', 'Cabin Sales Forecast', 'rts_view', 'rts-forecast', array( __CLASS__, 'render_forecast' ) );
		RTS_Auth::page( 'rts-admin', 'Founding Runner Outreach', 'FR Outreach', 'rts_view', 'rts-fr-outreach', array( __CLASS__, 'render_fr' ) );
		RTS_Auth::page( 'rts-admin', 'Survey Logic Map', 'Survey Logic Map', 'rts_view', 'rts-logic-map', array( __CLASS__, 'render_logic' ) );
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
	private static function filters_from_post() { $f = sanitize_key( $_POST['f_field'] ?? '' ); $v = sanitize_text_field( $_POST['f_value'] ?? '' ); return $v ? array( array( 'field' => $f, 'op' => sanitize_key( $_POST['f_op'] ?? 'equals' ), 'value' => $v ) ) : array(); }

	// ---- Report Builder ----
	public static function render_builder() {
		echo '<div class="wrap"><h1>Custom Report Builder</h1>'; self::notice();
		$src = sanitize_key( $_GET['src'] ?? 'participants' ); if ( ! isset( RTS_Business_Logic_7::SOURCES[ $src ] ) ) { $src = 'participants'; }
		$fields = RTS_Business_Logic_7::SOURCES[ $src ];
		echo '<form method="get" action="' . esc_url( RTSAP_Frontend_Dashboard::screen_url( 'rts-report-builder' ) ) . '">' . RTSAP_Frontend_Dashboard::screen_field( 'rts-report-builder' ) . '<label>1. Data source <select name="src" onchange="this.form.submit()">';
		foreach ( array_keys( RTS_Business_Logic_7::SOURCES ) as $s ) { echo '<option value="' . esc_attr( $s ) . '"' . selected( $src, $s, false ) . '>' . esc_html( $s ) . '</option>'; }
		echo '</select></label></form>';
		$fchk = ''; foreach ( $fields as $f ) { $fchk .= '<label style="margin-right:12px"><input type="checkbox" name="fields[]" value="' . esc_attr( $f ) . '" checked> ' . esc_html( $f ) . '</label>'; }
		$fsel = '<select name="f_field">'; foreach ( $fields as $f ) { $fsel .= '<option>' . esc_html( $f ) . '</option>'; } $fsel .= '</select> <select name="f_op"><option value="equals">equals</option><option value="not_equals">not equals</option><option value="contains">contains</option></select> <input type="text" name="f_value" placeholder="value">';
		echo '<h3>2. Fields</h3><h3 style="font-size:13px">3. Filter (optional)</h3>';
		echo self::form( 'rb_run', '<div style="margin:8px 0">' . $fchk . '</div><div style="margin:8px 0">' . $fsel . '</div><input type="hidden" name="preview" value="1"> ', 'Preview', array( 'src' => $src ) ) . ' ' . self::form( 'rb_save', '<input type="text" name="name" placeholder="Report name" required> <input type="hidden" name="src" value="' . esc_attr( $src ) . '"><input type="hidden" name="fields_csv" value="' . esc_attr( implode( ',', $fields ) ) . '"> ', 'Save all-fields report', array(), 'button button-primary' );
		$pk = 'rts_rb_preview_' . get_current_user_id();
		if ( isset( $_GET['preview'] ) && ( $pv = get_transient( $pk ) ) ) {
			delete_transient( $pk );
			echo '<h3>Preview (' . (int) $pv['row_count'] . ' rows, up to 200)</h3>' . self::tbl( $pv['fields'], array_map( fn( $r ) => array_values( $r ), $pv['rows'] ) );
		}
		echo '</div>';
	}
	public static function handle_rb_run() {
		self::guard( 'rb_run' ); $src = sanitize_key( $_POST['src'] );
		if ( ! empty( $_POST['saved_id'] ) ) { // "Run now" from Saved & Scheduled Reports — logs a real run
			$r = RTS_Business_Logic_7::run_report( (int) $_POST['saved_id'], self::admin() );
			if ( $r['error'] ) { self::back( 'rts-saved-reports', 'Error: ' . $r['error'] ); }
			set_transient( 'rts_rb_preview_' . get_current_user_id(), $r, 5 * MINUTE_IN_SECONDS );
			self::back( 'rts-saved-reports', 'Ran — ' . (int) $r['row_count'] . ' rows.', array( 'ran' => 1 ) );
		}
		$fields = array_map( 'sanitize_key', (array) ( $_POST['fields'] ?? array() ) );
		$r = RTS_Business_Logic_7::preview_report( $src, $fields, self::filters_from_post() );
		if ( $r['error'] ) { self::back( 'rts-report-builder', 'Error: ' . $r['error'], array( 'src' => $src ) ); }
		set_transient( 'rts_rb_preview_' . get_current_user_id(), $r, 5 * MINUTE_IN_SECONDS ); // transient, not URL — lesson from Batch 2
		self::back( 'rts-report-builder', '', array( 'src' => $src, 'preview' => 1 ) );
	}
	public static function handle_rb_save() { self::guard( 'rb_save' ); $r = RTS_Business_Logic_7::save_report( array( 'name' => sanitize_text_field( $_POST['name'] ), 'data_source' => sanitize_key( $_POST['src'] ), 'fields' => array_map( 'sanitize_key', explode( ',', $_POST['fields_csv'] ) ), 'filters' => array(), 'created_by' => self::admin() ) ); self::back( 'rts-saved-reports', $r['error'] ? 'Error: ' . $r['error'] : 'Report saved.' ); }

	// ---- Saved & Scheduled Reports ----
	public static function render_saved() {
		echo '<div class="wrap"><h1>Saved &amp; Scheduled Reports</h1>'; self::notice();
		echo '<p style="color:#666;font-size:12px;max-width:820px">No real scheduler in this prototype — "Run now" executes on demand. The schedule frequency is stored and real; a WP-Cron <code>wp_schedule_event</code> would call <code>run_report()</code>.</p>';
		echo self::tbl( array( 'Name', 'Source', 'Schedule', 'Runs', 'Last run', '' ), array_map( fn( $r ) => array( $r->name, $r->data_source, $r->schedule_frequency, (int) $r->run_count, $r->last_run_at ?: 'Never', self::form( 'rb_run', '<input type="hidden" name="saved_id" value="' . (int) $r->id . '">', 'Run now', array( 'src' => $r->data_source ) ) ), RTS_Business_Logic_7::list_reports() ) );
		$pk = 'rts_rb_preview_' . get_current_user_id();
		if ( isset( $_GET['ran'] ) && ( $pv = get_transient( $pk ) ) ) { delete_transient( $pk ); echo '<h3>Result (' . (int) $pv['row_count'] . ' rows)</h3>' . self::tbl( $pv['fields'], array_map( fn( $r ) => array_values( $r ), $pv['rows'] ) ); }
		echo '</div>';
	}

	// ---- Segments ----
	public static function render_segments() {
		echo '<div class="wrap"><h1>Build Custom Segment</h1>'; self::notice();
		$fsel = '<select name="f_field">'; foreach ( array( 'runner_status','country','gender','age_range','email_verified' ) as $f ) { $fsel .= '<option>' . esc_html( $f ) . '</option>'; } $fsel .= '</select> <select name="f_op"><option value="equals">equals</option><option value="contains">contains</option></select> <input type="text" name="f_value" placeholder="value, e.g. runner" required> <input type="text" name="name" placeholder="Segment name" required>';
		echo '<h3>Pick a filter &amp; save</h3>' . self::form( 'seg_save', $fsel . ' ', 'Save segment', array(), 'button button-primary' );
		echo '<h3>Saved segments — recounted live every time you look</h3>' . self::tbl( array( 'Name', 'Live count', 'Filters', 'Created' ), array_map( fn( $s ) => array( $s['name'], (int) $s['live_count'], $s['filters_json'], $s['created_at'] ), RTS_Business_Logic_7::list_segments() ) ) . '</div>';
	}
	public static function handle_seg_save() { self::guard( 'seg_save' ); RTS_Business_Logic_7::save_segment( sanitize_text_field( $_POST['name'] ), self::filters_from_post(), self::admin() ); self::back( 'rts-segments', 'Segment saved.' ); }

	// ---- Quick Reports ----
	public static function render_quick() {
		$d = RTS_Business_Logic_7::quick_reports();
		$badge = array( 'independent' => array( 'Independent', '#E9F7EF', '#1E7B4D' ), 'subset' => array( 'Subset of →', '#FBF3DD', '#9A6B10' ), 'overlaps' => array( 'Overlaps with →', '#FCEBEB', '#B23B3B' ), 'sum' => array( '= Sum of →', '#EEE', '#555' ), 'events' => array( 'Event count, not people →', '#FBF3DD', '#9A6B10' ) );
		echo '<div class="wrap"><h1>Quick Reports — Number Reference</h1><p style="color:#666;font-size:12px;max-width:820px">Real live numbers, generated from the same data as every other screen. Read the relationship badge before adding two numbers together — it tells you whether that is valid.</p>';
		foreach ( array( 'participants_and_surveys' => 'Participants & Surveys', 'founding_runners_and_credit' => 'Founding Runners & Cabin Credit', 'referrals_and_trophies' => 'Referrals & Trophies', 'advertising_and_acquisition' => 'Advertising & Acquisition', 'customer_feedback' => 'Customer Feedback' ) as $k => $title ) {
			echo '<div style="background:#fff;border:1px solid #ccd0d4;padding:14px 18px;margin:14px 0;max-width:860px"><h3 style="margin-top:0">' . esc_html( $title ) . '</h3>';
			foreach ( $d[ $k ] as $m ) { $b = $badge[ $m['rel'] ]; echo '<div style="padding:10px 0;border-bottom:1px solid #eee"><div style="display:flex;justify-content:space-between"><b>' . esc_html( $m['metric'] ) . '</b><b>' . esc_html( $m['value'] ) . '</b></div><div style="color:#666;font-size:12px;margin:3px 0">' . esc_html( $m['def'] ) . '</div><span style="display:inline-block;font-size:10.5px;padding:2px 8px;border-radius:10px;font-weight:700;background:' . $b[1] . ';color:' . $b[2] . '">' . esc_html( $b[0] ) . '</span> <span style="color:#666;font-size:11.5px">' . esc_html( $m['to'] ) . '</span></div>'; }
			echo '</div>';
		}
		echo '</div>';
	}

	// ---- Action Items ----
	public static function render_actions() {
		RTS_Business_Logic_7::generate_action_items();
		echo '<div class="wrap"><h1>Action Items &amp; Recommendations</h1>'; self::notice();
		echo '<p style="color:#666;font-size:12px;max-width:820px"><b>Real rule-based recommendations</b> computed from live data thresholds — <b>not</b> AI-generated (no LLM integration). Each rule has a stable key so re-running never creates a duplicate open item; a condition that resolves itself auto-closes its item.</p>';
		$open = RTS_Business_Logic_7::list_action_items( 'open' ); $done = array_filter( RTS_Business_Logic_7::list_action_items(), fn( $x ) => 'open' !== $x->status );
		echo '<div style="display:flex;gap:12px"><div>' . self::kpi( 'Open', count( $open ) ) . '</div><div>' . self::kpi( 'Resolved', count( $done ) ) . '</div></div>';
		echo '<h3>Open</h3>' . self::tbl( array( 'Category', 'Recommendation', 'Backed by', '' ), array_map( fn( $i ) => array( $i->category, $i->recommendation, $i->backed_by, self::form( 'ai_resolve', '<input type="text" name="note" placeholder="Outcome note"> ', 'Mark actioned', array( 'id' => $i->id, 'status' => 'actioned' ) ) . ' ' . self::form( 'ai_resolve', '', 'Dismiss', array( 'id' => $i->id, 'status' => 'dismissed' ), 'button button-link-delete' ) ), $open ) );
		echo '<h3>Resolved</h3>' . self::tbl( array( 'Category', 'Recommendation', 'Status', 'Outcome', 'Resolved' ), array_map( fn( $i ) => array( $i->category, $i->recommendation, $i->status, $i->outcome_note ?: '—', $i->resolved_at ), $done ) ) . '</div>';
	}
	public static function handle_ai_resolve() { self::guard( 'ai_resolve' ); $r = RTS_Business_Logic_7::resolve_action_item( (int) $_POST['id'], sanitize_key( $_POST['status'] ), sanitize_text_field( $_POST['note'] ?? '' ), self::admin() ); self::back( 'rts-action-items', $r['error'] ? 'Error: ' . $r['error'] : 'Updated.' ); }

	// ---- Estimate Cabin Sales ----
	public static function render_forecast() {
		$s = RTS_Business_Logic_7::forecast_segments();
		echo '<div class="wrap"><h1>Estimate Cabin Sales</h1><p style="color:#666;font-size:12px;max-width:820px">Four <b>mutually-exclusive</b> pools queried live (each participant counted in exactly one). Type a conversion-rate assumption per cell; the projection recalculates live (JavaScript). Runner/non-runner split uses a platform-wide 78% assumption applied uniformly — a simplification, not measured per segment.</p>';
		$rows = array( array( 'Verified & credited', 'vc', $s['verified_credited'] ), array( 'Referred-in, not yet verified', 'rn', $s['referred_not_verified'] ), array( 'Interested/notify, not verified or referred', 'io', $s['interested_only'] ), array( 'Cold traffic, engaged', 'ct', $s['cold_traffic'] ) );
		echo '<table class="wp-list-table widefat fixed striped" style="max-width:900px"><thead><tr><th>Segment</th><th>Runner pool</th><th>Runner rate %</th><th>Non-runner pool</th><th>Non-runner rate %</th><th>Projected</th></tr></thead><tbody>';
		foreach ( $rows as $r ) { echo '<tr><td>' . esc_html( $r[0] ) . '</td><td data-pool="' . (int) $r[2]['runner'] . '">' . (int) $r[2]['runner'] . '</td><td><input type="number" class="rts-rate" data-k="' . $r[1] . '_r" value="0" min="0" max="100" style="width:70px"></td><td data-pool="' . (int) $r[2]['non_runner'] . '">' . (int) $r[2]['non_runner'] . '</td><td><input type="number" class="rts-rate" data-k="' . $r[1] . '_n" value="0" min="0" max="100" style="width:70px"></td><td class="rts-proj">0</td></tr>'; }
		echo '</tbody></table><div style="display:flex;gap:12px;margin-top:14px"><div>' . self::kpi( 'Total addressable pool', $s['total_addressable_pool'] ) . '</div><div>' . str_replace( '>0<', ' id="rts-total">0<', self::kpi( 'Projected cabins', 0 ) ) . '</div></div>';
		echo "<script>document.querySelectorAll('.rts-rate').forEach(function(i){i.addEventListener('input',function(){var t=0;document.querySelectorAll('tbody tr').forEach(function(tr){var c=tr.querySelectorAll('td');if(c.length<6)return;var rp=+c[1].dataset.pool,rr=+c[2].querySelector('input').value/100,np=+c[3].dataset.pool,nr=+c[4].querySelector('input').value/100;var p=Math.round(rp*rr+np*nr);c[5].textContent=p;t+=p;});document.getElementById('rts-total').textContent=t;});});</script></div>";
	}

	// ---- Founding Runner Outreach ----
	public static function render_fr() {
		$t = RTS_Business_Logic_7::fr_totals();
		echo '<div class="wrap"><h1>Founding Runner Totals &amp; Outreach</h1><div style="display:flex;gap:12px;flex-wrap:wrap">' . self::kpi( 'Total Founding Runners', $t['total'], 'of ' . $t['goal'] . ' goal' ) . self::kpi( 'With Cruise Credit', $t['with_credit'] ) . self::kpi( 'Without Cruise Credit', $t['without_credit'] ) . self::kpi( 'Remaining to goal', $t['goal'] - $t['total'] ) . '</div>';
		if ( $t['without_credit_note'] ) { echo '<p style="color:#666;font-size:12px;max-width:820px">' . esc_html( $t['without_credit_note'] ) . '</p>'; }
		echo '<h3>Email groups</h3><p style="color:#666;font-size:12px;max-width:820px">Reuses the same audience-scoped send-gate as <a href="' . esc_url( admin_url( 'admin.php?page=rts-broadcast' ) ) . '">Broadcast</a> — pick the audience there; the test-then-bulk gate and unsubscribe exclusion are identical.</p><a class="button" href="' . esc_url( admin_url( 'admin.php?page=rts-broadcast' ) ) . '">Go to Compose &amp; Send →</a></div>';
	}

	// ---- Survey Logic Map ----
	public static function render_logic() {
		echo '<div class="wrap"><h1>Survey Logic Map</h1><p style="color:#666;font-size:12px;max-width:820px">Real dependency data extracted live from the survey question table — not a hand-drawn diagram. Gold-highlighted questions only appear if their trigger condition is met.</p><div style="max-width:640px">';
		foreach ( RTS_Business_Logic_7::logic_map( 1 ) as $q ) {
			if ( $q['conditional_on'] ) { echo '<div style="text-align:center;color:#888;font-size:12px;margin:2px 0">↓ shown only if Q' . (int) $q['conditional_on']['question_number'] . ' = "' . esc_html( $q['conditional_on']['required_answer'] ) . '"</div>'; }
			echo '<div style="border:1.5px solid #C7CDD6;border-radius:8px;padding:12px 16px;margin-bottom:8px;background:' . ( $q['conditional_on'] ? '#FBF7EE;border-left:4px solid #C9A24B' : '#fff' ) . '"><b>Q' . (int) $q['question_number'] . '</b> ' . esc_html( $q['prompt'] ) . '</div>';
		}
		echo '</div></div>';
	}
}
