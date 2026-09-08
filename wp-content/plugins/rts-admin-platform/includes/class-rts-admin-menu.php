<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Admin_Menu {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
	}

	public static function register_menu() {
		add_menu_page(
			'Run The Seas Admin', 'Run The Seas', 'read', 'rts-admin',
			array( __CLASS__, 'render_dashboard' ), 'dashicons-palmtree', 3
		);
		RTS_Auth::page( 'rts-admin', 'Executive Dashboard', 'Executive Dashboard', 'rts_view', 'rts-admin', array( __CLASS__, 'render_dashboard' ) );
		RTS_Auth::page( 'rts-admin', 'Participants', 'Participants', 'rts_view', 'rts-participants', array( __CLASS__, 'render_participants' ) );
		RTS_Auth::page( 'rts-admin', 'Cabin Credits', 'Cabin Credits', 'rts_view', 'rts-cabin-credits', array( __CLASS__, 'render_cabin_credits' ) );
		RTS_Auth::page( 'rts-admin', 'Audit Log', 'Audit Log', 'rts_view', 'rts-audit-log', array( __CLASS__, 'render_audit_log' ) );
	}

	private static function wrap( $title, $inner ) {
		echo '<div class="wrap"><h1>' . esc_html( $title ) . '</h1>' . $inner . '</div>';
	}

	public static function render_dashboard() {
		// Call the REST callback directly rather than over HTTP loopback — the WordPress-idiomatic
		// approach, and it avoids depending on the web server being able to call itself (which
		// PHP's single-threaded built-in dev server can't do). Same data, no network round-trip.
		$summary = RTS_REST_API::executive_summary( null )->get_data();
		$k = is_array( $summary['referralCoefficient'] ) ? $summary['referralCoefficient']['k'] : $summary['referralCoefficient'];

		$html = '<div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:20px;">';
		$cards = array(
			array( 'Total Surveys Completed', $summary['totalSurveysCompleted'] ),
			array( 'Completion Rate', $summary['surveyCompletionRate'] . '%' ),
			array( 'Referral Coefficient (K)', $k ),
			array( 'Verified Referrals', $summary['verifiedReferralsTotal'] . ' of ' . $summary['totalReferralsSent'] ),
			array( 'Total Participants', $summary['totalParticipants'] ),
			array( 'Verified Participants', $summary['verifiedParticipants'] ),
			array( 'Runners / Non-Runners', $summary['runnersVsNonRunners']['runners'] . ' / ' . $summary['runnersVsNonRunners']['nonRunners'] ),
			array( 'Cabin Credits Issued', $summary['cabinCreditsIssued'] . ' / ' . $summary['cabinCreditFloor'] . ' floor' ),
		);
		foreach ( $cards as $c ) {
			$html .= '<div style="background:#fff;border:1px solid #ccd0d4;border-top:3px solid #C9A24B;border-radius:4px;padding:14px 18px;min-width:200px;">'
				. '<div style="font-size:11px;text-transform:uppercase;color:#666;font-weight:600;">' . esc_html( $c[0] ) . '</div>'
				. '<div style="font-size:26px;font-weight:700;margin-top:6px;color:#0B1420;">' . esc_html( $c[1] ) . '</div></div>';
		}
		$html .= '</div>';
		$html .= '<p style="margin-top:20px;"><a href="' . esc_url( home_url( '/?page_id=' . self::get_survey_page_id() ) ) . '" class="button" target="_blank">View Public Survey Page →</a></p>';

		self::wrap( 'Executive Dashboard — Live, real WordPress + MySQL data', $html );
	}

	// public static function render_participants() {
	// 	global $wpdb;
	// 	$table = RTS_DB::table( 'participants' );
	// 	$rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY registered_at DESC" );

	// 	$html = '<table class="wp-list-table widefat fixed striped"><thead><tr>
	// 		<th>Name</th><th>FRN</th><th>Email</th><th>Verified</th><th>Country</th><th>Referral Code</th><th>Registered</th>
	// 		</tr></thead><tbody>';
	// 	foreach ( $rows as $p ) {
	// 		$html .= '<tr><td><a href="' . esc_url( admin_url( 'admin.php?page=rts-participant-profile&id=' . (int) $p->id ) ) . '">' . esc_html( $p->name ) . '</a></td><td>' . esc_html( $p->founding_runner_number ?: '—' ) . '</td>'
	// 			. '<td>' . esc_html( $p->email ) . '</td>'
	// 			. '<td>' . ( $p->email_verified ? '<span style="color:#1E7B4D;font-weight:600;">Verified</span>' : '<span style="color:#9A6B10;">Pending</span>' ) . '</td>'
	// 			. '<td>' . esc_html( $p->country ) . '</td><td><code>' . esc_html( $p->referral_code ) . '</code></td>'
	// 			. '<td>' . esc_html( $p->registered_at ) . '</td></tr>';
	// 	}
	// 	$html .= '</tbody></table>';
	// 	self::wrap( 'Participants (' . count( $rows ) . ')', $html );
	// }

	public static function render_participants() {
		global $wpdb;
		$table = RTS_DB::table( 'participants' );
		$referrals_table = RTS_DB::table( 'referrals' );
		$credits_table = RTS_DB::table( 'cabin_credits' );
		$status = isset( $_GET['participant_status'] ) ? sanitize_key( wp_unslash( $_GET['participant_status'] ) ) : 'all';
		$country = isset( $_GET['country'] ) ? sanitize_text_field( wp_unslash( $_GET['country'] ) ) : '';
		$runner = isset( $_GET['runner'] ) ? sanitize_key( wp_unslash( $_GET['runner'] ) ) : 'all';
		$search = isset( $_GET['participant_search'] ) ? sanitize_text_field( wp_unslash( $_GET['participant_search'] ) ) : '';
		if ( ! in_array( $status, array( 'all', 'verified', 'pending' ), true ) ) { $status = 'all'; }
		if ( ! in_array( $runner, array( 'all', 'runner', 'non_runner', 'not_specified' ), true ) ) { $runner = 'all'; }
		$where = array( 'p.merged_into_participant_id IS NULL' ); $params = array();
		if ( 'verified' === $status ) { $where[] = 'p.email_verified = 1'; }
		if ( 'pending' === $status ) { $where[] = 'p.email_verified = 0'; }
		if ( $country ) { $where[] = 'p.country = %s'; $params[] = $country; }
		if ( 'all' !== $runner ) {
			if ( 'not_specified' === $runner ) { $where[] = "(p.runner_status IS NULL OR p.runner_status = '' OR p.runner_status = 'not_specified')"; }
			else { $where[] = 'p.runner_status = %s'; $params[] = $runner; }
		}
		if ( $search ) {
			$like = '%' . $wpdb->esc_like( $search ) . '%';
			$where[] = '(p.name LIKE %s OR p.first_name LIKE %s OR p.last_name LIKE %s OR p.email LIKE %s OR p.founding_runner_number LIKE %s)';
			array_push( $params, $like, $like, $like, $like, $like );
		}
		$where_sql = implode( ' AND ', $where );
		$prepare = static function ( $sql, $values ) use ( $wpdb ) { return $values ? $wpdb->prepare( $sql, ...$values ) : $sql; };
		$per_page = 20; $current_page = self::current_page(); $offset = ( $current_page - 1 ) * $per_page;
		$total_items = (int) $wpdb->get_var( $prepare( "SELECT COUNT(*) FROM $table p WHERE $where_sql", $params ) );
		$total_pages = (int) ceil( $total_items / $per_page );
		$rows = $wpdb->get_results( $prepare(
			"SELECT p.*,
				(SELECT COUNT(*) FROM $referrals_table r WHERE COALESCE(r.referring_participant_id, r.referrer_id) = p.id) AS referral_total,
				(SELECT cc.status FROM $credits_table cc WHERE cc.participant_id = p.id ORDER BY cc.id DESC LIMIT 1) AS latest_credit_status
			 FROM $table p WHERE $where_sql
			 ORDER BY COALESCE(p.registered_at, p.created_at) DESC, p.id DESC LIMIT %d OFFSET %d",
			array_merge( $params, array( $per_page, $offset ) )
		) );
		$countries = $wpdb->get_col( "SELECT DISTINCT country FROM $table WHERE merged_into_participant_id IS NULL AND country IS NOT NULL AND country != '' ORDER BY country ASC" );

		$html = '<p class="rtsap-page-subtitle">' . esc_html__( 'Search, filter and open registered participant records.', 'run-the-seas' ) . '</p>';
		$html .= '<form class="rtsap-participant-toolbar" method="get" action="' . esc_url( RTSAP_Frontend_Dashboard::screen_url( 'rts-participants' ) ) . '">'
			. RTSAP_Frontend_Dashboard::screen_field( 'rts-participants' )
			. '<select name="participant_status" aria-label="' . esc_attr__( 'Verification status', 'run-the-seas' ) . '"><option value="all">' . esc_html__( 'Status: All', 'run-the-seas' ) . '</option><option value="verified"' . selected( $status, 'verified', false ) . '>' . esc_html__( 'Verified', 'run-the-seas' ) . '</option><option value="pending"' . selected( $status, 'pending', false ) . '>' . esc_html__( 'Pending', 'run-the-seas' ) . '</option></select>'
			. '<select name="country" aria-label="' . esc_attr__( 'Country', 'run-the-seas' ) . '"><option value="">' . esc_html__( 'Country: All', 'run-the-seas' ) . '</option>';
		foreach ( $countries as $country_option ) { $html .= '<option value="' . esc_attr( $country_option ) . '"' . selected( $country, $country_option, false ) . '>' . esc_html( $country_option ) . '</option>'; }
		$html .= '</select><select name="runner" aria-label="' . esc_attr__( 'Runner status', 'run-the-seas' ) . '"><option value="all">' . esc_html__( 'Runner: All', 'run-the-seas' ) . '</option><option value="runner"' . selected( $runner, 'runner', false ) . '>' . esc_html__( 'Runner', 'run-the-seas' ) . '</option><option value="non_runner"' . selected( $runner, 'non_runner', false ) . '>' . esc_html__( 'Non-runner', 'run-the-seas' ) . '</option><option value="not_specified"' . selected( $runner, 'not_specified', false ) . '>' . esc_html__( 'Not specified', 'run-the-seas' ) . '</option></select>'
			. '<label class="rtsap-participant-search"><span class="dashicons dashicons-search" aria-hidden="true"></span><input type="search" name="participant_search" value="' . esc_attr( $search ) . '" placeholder="' . esc_attr__( 'Search name, email or FR number…', 'run-the-seas' ) . '"></label><button class="button">' . esc_html__( 'Apply Filters', 'run-the-seas' ) . '</button></form>';
		$html .= '<div class="rtsap-participant-actions"><span>' . esc_html( sprintf( _n( '%s participant', '%s participants', $total_items, 'run-the-seas' ), number_format_i18n( $total_items ) ) ) . '</span>'
			. '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="rts_export_csv"><input type="hidden" name="dataset" value="participants">' . wp_nonce_field( 'rts_export_csv', '_rts_nonce', true, false ) . '<button class="button button-primary">' . esc_html__( 'Export Registered CSV', 'run-the-seas' ) . '</button></form></div>';

		$html .= '<table class="wp-list-table widefat striped rtsap-participant-table"><thead><tr><th>' . esc_html__( 'Name', 'run-the-seas' ) . '</th><th>' . esc_html__( 'Founding Runner #', 'run-the-seas' ) . '</th><th>' . esc_html__( 'Email Verified', 'run-the-seas' ) . '</th><th>' . esc_html__( 'Country', 'run-the-seas' ) . '</th><th>' . esc_html__( 'Referrals', 'run-the-seas' ) . '</th><th>' . esc_html__( 'Cabin Credit', 'run-the-seas' ) . '</th><th>' . esc_html__( 'Actions', 'run-the-seas' ) . '</th></tr></thead><tbody>';
		if ( $rows ) {
			foreach ( $rows as $p ) {
				$name = trim( (string) $p->name ) ?: trim( $p->first_name . ' ' . $p->last_name ); if ( ! $name ) { $name = $p->email; }
				$credit_status = $p->latest_credit_status ?: $p->cabin_credit_status; if ( ! $credit_status && $p->cabin_credit_requested ) { $credit_status = 'pending'; }
				$profile_url = RTSAP_Frontend_Dashboard::screen_url( 'rts-participant-profile', array( 'id' => (int) $p->id ) );
				$html .= '<tr><td><b>' . esc_html( $name ) . '</b><small>' . esc_html( $p->email ) . '</small></td><td>' . esc_html( $p->founding_runner_number ?: '—' ) . '</td><td><span class="rtsap-directory-badge ' . ( $p->email_verified ? 'is-verified' : 'is-pending' ) . '">' . esc_html( $p->email_verified ? 'Verified' : 'Pending' ) . '</span></td><td>' . esc_html( $p->country ?: '—' ) . '</td><td>' . (int) $p->referral_total . '</td><td><span class="rtsap-directory-badge is-credit">' . esc_html( $credit_status ? ucwords( str_replace( '_', ' ', $credit_status ) ) : 'N/A' ) . '</span></td><td><a class="button rtsap-open-participant" href="' . esc_url( $profile_url ) . '">' . esc_html__( 'Open', 'run-the-seas' ) . '</a></td></tr>';
			}
		} else {
			$html .= '<tr><td colspan="7" class="rtsap-empty-row">' . esc_html__( 'No participants match the selected filters.', 'run-the-seas' ) . '</td></tr>';
		}
		$html .= '</tbody></table>';
		$html .= self::pagination( $current_page, $total_pages, $total_items );
		$html .= '<div class="rtsap-directory-note">' . esc_html__( 'Open a participant to view the profile. Merge Account appears only when another eligible record shares that participant’s survey session.', 'run-the-seas' ) . '</div>';
		self::wrap( __( 'Participant Directory', 'run-the-seas' ), $html );
	}

	public static function render_cabin_credits() {
		global $wpdb;
		$table = RTS_DB::table( 'cabin_credits' );
		$ptable = RTS_DB::table( 'participants' );
		$rows = $wpdb->get_results( "SELECT cc.*, p.name, p.founding_runner_number FROM $table cc JOIN $ptable p ON p.id = cc.participant_id ORDER BY cc.issued_at DESC" );

		$issued = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status = 'issued'" );
		$html = '<p><strong>Issued:</strong> ' . $issued . ' &middot; <strong>Outstanding Liability:</strong> $' . ( $issued * 100 ) . '</p>';
		$html .= '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Founding Runner</th><th>Status</th><th>Value</th><th>Issued</th></tr></thead><tbody>';
		foreach ( $rows as $c ) {
			$html .= '<tr><td>' . esc_html( $c->name ) . ' (' . esc_html( $c->founding_runner_number ) . ')</td>'
				. '<td>' . esc_html( $c->status ) . '</td><td>$' . esc_html( $c->value_usd ) . '</td><td>' . esc_html( $c->issued_at ) . '</td></tr>';
		}
		$html .= '</tbody></table>';
		self::wrap( 'Cabin Credit Ledger', $html );
	}

	// public static function render_audit_log() {
	// 	global $wpdb;
	// 	$table = RTS_DB::table( 'audit_log' );
	// 	$rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 100" );
	// 	$html = '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Time</th><th>User</th><th>Action</th><th>Module</th></tr></thead><tbody>';
	// 	foreach ( $rows as $a ) {
	// 		$html .= '<tr><td>' . esc_html( $a->created_at ) . '</td><td>' . esc_html( $a->user ) . '</td><td>' . esc_html( $a->action ) . '</td><td>' . esc_html( $a->module ) . '</td></tr>';
	// 	}
	// 	$html .= '</tbody></table>';
	// 	self::wrap( 'Audit Log', $html );
	// }

	public static function render_audit_log() {
		global $wpdb;

		$table = RTS_DB::table( 'audit_log' );

		// Pagination.
		$per_page     = 50;
		$current_page = self::current_page();
		$offset       = ( $current_page - 1 ) * $per_page;

		// Total audit records.
		$total_items = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM $table"
		);

		$total_pages = (int) ceil( $total_items / $per_page );

		// Get current page records.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM $table
				ORDER BY created_at DESC
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		$html = '<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th>Time</th>
					<th>User</th>
					<th>Action</th>
					<th>Module</th>
				</tr>
			</thead>
			<tbody>';

		if ( $rows ) {
			foreach ( $rows as $a ) {

				$html .= '<tr>';

				$html .= '<td>'
					. esc_html( $a->created_at )
					. '</td>';

				$html .= '<td>'
					. esc_html( $a->user )
					. '</td>';

				$html .= '<td>'
					. esc_html( $a->action )
					. '</td>';

				$html .= '<td>'
					. esc_html( $a->module )
					. '</td>';

				$html .= '</tr>';
			}
		} else {
			$html .= '<tr>
				<td colspan="4">No audit log entries found.</td>
			</tr>';
		}

		$html .= '</tbody></table>';

		// Pagination.
		$html .= self::pagination(
			$current_page,
			$total_pages,
			$total_items
		);

		self::wrap(
			'Audit Log (' . $total_items . ')',
			$html
		);
	}

	private static function get_survey_page_id() {
		$page = get_page_by_path( 'survey' );
		return $page ? $page->ID : 0;
	}

	private static function pagination( $current_page, $total_pages, $total_items ) {

		if ( $total_pages <= 1 ) {
			return '';
		}

		$page_arg = self::pagination_query_arg();
		$base_url = remove_query_arg( array( 'paged', 'rts_paged' ) );

		$links = paginate_links(
			array(
				'base'      => add_query_arg( $page_arg, '%#%', $base_url ),
				'format'    => '',
				'current'   => $current_page,
				'total'     => $total_pages,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
				'type'      => 'array',
				'end_size'  => 1,
				'mid_size'  => 2,
			)
		);

		if ( empty( $links ) ) {
			return '';
		}

		$html  = '<div class="rts-pagination">';
		$html .= '<span class="rts-pagination-count">'
			. number_format_i18n( $total_items )
			. ' items</span>';

		$html .= '<div class="rts-pagination-links">';

		foreach ( $links as $link ) {
			$html .= $link;
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Avoid WordPress's reserved `paged` query variable on the front-end shell.
	 *
	 * On a normal page request, `paged=2` is consumed by the main query and can
	 * trigger canonical redirects or a 404 before the RTS dashboard callback is
	 * rendered. wp-admin does not have that conflict, so retain its conventional
	 * parameter there and use a namespaced parameter for platform users.
	 */
	private static function pagination_query_arg() {
		return RTSAP_Frontend_Dashboard::is_platform_user() ? 'rts_paged' : 'paged';
	}

	private static function current_page() {
		$page_arg = self::pagination_query_arg();
		return isset( $_GET[ $page_arg ] ) ? max( 1, absint( $_GET[ $page_arg ] ) ) : 1;
	}
}
