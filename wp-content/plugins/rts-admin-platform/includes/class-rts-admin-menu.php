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

	public static function render_participants() {
		global $wpdb;
		$table = RTS_DB::table( 'participants' );
		$rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY registered_at DESC" );

		$html = '<table class="wp-list-table widefat fixed striped"><thead><tr>
			<th>Name</th><th>FRN</th><th>Email</th><th>Verified</th><th>Country</th><th>Referral Code</th><th>Registered</th>
			</tr></thead><tbody>';
		foreach ( $rows as $p ) {
			$html .= '<tr><td><a href="' . esc_url( admin_url( 'admin.php?page=rts-participant-profile&id=' . (int) $p->id ) ) . '">' . esc_html( $p->name ) . '</a></td><td>' . esc_html( $p->founding_runner_number ?: '—' ) . '</td>'
				. '<td>' . esc_html( $p->email ) . '</td>'
				. '<td>' . ( $p->email_verified ? '<span style="color:#1E7B4D;font-weight:600;">Verified</span>' : '<span style="color:#9A6B10;">Pending</span>' ) . '</td>'
				. '<td>' . esc_html( $p->country ) . '</td><td><code>' . esc_html( $p->referral_code ) . '</code></td>'
				. '<td>' . esc_html( $p->registered_at ) . '</td></tr>';
		}
		$html .= '</tbody></table>';
		self::wrap( 'Participants (' . count( $rows ) . ')', $html );
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

	public static function render_audit_log() {
		global $wpdb;
		$table = RTS_DB::table( 'audit_log' );
		$rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 100" );
		$html = '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Time</th><th>User</th><th>Action</th><th>Module</th></tr></thead><tbody>';
		foreach ( $rows as $a ) {
			$html .= '<tr><td>' . esc_html( $a->created_at ) . '</td><td>' . esc_html( $a->user ) . '</td><td>' . esc_html( $a->action ) . '</td><td>' . esc_html( $a->module ) . '</td></tr>';
		}
		$html .= '</tbody></table>';
		self::wrap( 'Audit Log', $html );
	}

	private static function get_survey_page_id() {
		$page = get_page_by_path( 'survey' );
		return $page ? $page->ID : 0;
	}
}
