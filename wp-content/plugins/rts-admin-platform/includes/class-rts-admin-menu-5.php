<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Admin_Menu_5 {

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 50 );
		foreach ( array( 'ec_create','ec_save','ec_status','ec_trigger','ec_delete','ad_create','dup_review','reject_ref' ) as $a ) { add_action( "admin_post_rts_$a", array( __CLASS__, "handle_$a" ) ); }
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
		$campaign_id = isset( $_GET['campaign_id'] ) ? absint( $_GET['campaign_id'] ) : 0;
		$campaign = $campaign_id ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'email_campaigns' ) . " WHERE id = %d", $campaign_id ) ) : null;
		$templates = $wpdb->get_results( "SELECT id, name, subject, category, html_body FROM " . RTS_DB::table( 'email_templates' ) . " ORDER BY updated_at DESC" );
		$selected_template_id = $campaign ? (int) $campaign->template_id : ( $templates ? (int) $templates[0]->id : 0 );
		$preview_map = array();
		foreach ( $templates as $template ) {
			$body = RTS_Production::merge( (string) $template->html_body, (object) array( 'name' => 'Sample Runner', 'email' => 'runner@example.com', 'founding_runner_number' => 'FR-0000', 'referral_code' => 'SAMPLE', 'unsubscribe_token' => 'sample' ) );
			$body = strtr( $body, array( '{full_name}' => 'Sample Runner', '{last_name}' => 'Runner', '{site_name}' => get_bloginfo( 'name' ), '{site_url}' => home_url( '/' ), '{support_email}' => get_option( 'admin_email' ) ) );
			$preview_map[ $template->id ] = array(
				'name' => $template->name, 'subject' => $template->subject,
				'html' => false !== stripos( $body, '<html' ) ? $body : '<!doctype html><html><body style="margin:0;padding:24px;background:#f4f5f7;font-family:Arial,sans-serif">' . wpautop( $body ) . '</body></html>',
				'editUrl' => RTSAP_Frontend_Dashboard::screen_url( 'rts-email-templates', array( 'template_id' => (int) $template->id ) ),
			);
		}
		$recipient_candidates = RTS_Business_Logic_5::campaign_recipient_candidates();
		$current_user = wp_get_current_user();
		$list_url = RTSAP_Frontend_Dashboard::screen_url( 'rts-email-campaigns' );
		$template_library_url = RTSAP_Frontend_Dashboard::screen_url( 'rts-email-templates' );
		$name = $campaign->name ?? '';
		$delivery_mode = $campaign->delivery_mode ?? 'manual';
		$trigger_type = $campaign->trigger_type ?? 'days_after_registration';
		$trigger_days = isset( $campaign->trigger_days ) ? (int) $campaign->trigger_days : 3;
		$audience_filter = $campaign->audience_filter ?? 'all';
		if ( 'verified_only' === $audience_filter ) { $audience_filter = 'all'; }
		$category = $campaign->category ?? 'general';
		$exclusion_rules = RTS_Business_Logic_5::exclusion_rules( $campaign->exclusion_rules ?? array() );
		$exclusion_rule = $exclusion_rules[0] ?? array( 'metric' => 'none', 'operator' => '>=', 'value' => 1000 );
		$scheduled_at = ! empty( $campaign->scheduled_at ) ? str_replace( ' ', 'T', substr( $campaign->scheduled_at, 0, 16 ) ) : '';

		echo '<div class="wrap rtsap-campaign-builder"><h1>Email Campaign Builder</h1>'; self::notice();
		echo '<p class="rtsap-page-subtitle">Sections 31–32 — automation workflows, templates, notifications</p>';
		if ( ! $templates ) {
			echo '<div class="notice notice-warning"><p>No email templates are available. <a href="' . esc_url( $template_library_url ) . '">Create a template first</a>; its Visual editor includes WordPress Media Library image upload.</p></div></div>';
			return;
		}
		echo '<form id="rtsap-campaign-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="rts_ec_save"><input type="hidden" name="id" value="' . (int) $campaign_id . '"><input type="hidden" name="existing_status" value="' . esc_attr( $campaign->status ?? 'draft' ) . '">';
		wp_nonce_field( 'rts_ec_save', '_rts_nonce' );
		echo '<div class="rtsap-builder-tabs" role="tablist" aria-label="Campaign builder steps">';
		foreach ( array( 'design' => 'Design', 'audience' => 'Audience', 'trigger' => 'Automation Trigger', 'schedule' => 'Schedule', 'preview' => 'Preview & Send' ) as $tab => $label ) {
			echo '<button type="button" class="rtsap-builder-tab' . ( 'design' === $tab ? ' is-active' : '' ) . '" role="tab" aria-selected="' . ( 'design' === $tab ? 'true' : 'false' ) . '" data-tab="' . esc_attr( $tab ) . '">' . esc_html( $label ) . '</button>';
		}
		echo '</div><div class="rtsap-delivery-bar"><span>Delivery</span><div class="rtsap-delivery-choices"><label><input type="radio" name="delivery_mode" value="manual"' . checked( $delivery_mode, 'manual', false ) . '><b>Manual now</b></label><label><input type="radio" name="delivery_mode" value="scheduled"' . checked( $delivery_mode, 'scheduled', false ) . '><b>Schedule once</b></label><label><input type="radio" name="delivery_mode" value="automation"' . checked( $delivery_mode, 'automation', false ) . '><b>Automation</b></label></div></div>';

		echo '<section class="rtsap-builder-step is-active" data-step="design"><div class="rtsap-builder-grid"><div class="rtsap-panel"><div class="rtsap-panel__head"><h3>Campaign &amp; Template</h3></div>';
		echo '<label class="rtsap-field"><span>Campaign name</span><input class="regular-text" type="text" name="name" value="' . esc_attr( $name ) . '" placeholder="e.g. Founding Runner follow-up" required></label>';
		echo '<label class="rtsap-field"><span>Email template</span><select id="rtsap-campaign-template" name="template_id" required>';
		foreach ( $templates as $template ) { echo '<option value="' . (int) $template->id . '"' . selected( $selected_template_id, $template->id, false ) . '>' . esc_html( $template->name . ' — ' . $template->subject ) . '</option>'; }
		echo '</select></label><div class="rtsap-template-actions"><a id="rtsap-edit-template" class="button button-primary" href="' . esc_url( $preview_map[ $selected_template_id ]['editUrl'] ?? $template_library_url ) . '">Edit template &amp; images</a><a class="button" href="' . esc_url( $template_library_url ) . '">Template Library</a></div><p class="description">The shared editor supports Visual/HTML editing, merge fields, and WordPress Media Library image upload.</p></div>';
		echo '<div class="rtsap-panel"><div class="rtsap-panel__head"><h3>Template Preview</h3></div><div class="rtsap-email-preview-meta"><span>Subject</span><b id="rtsap-preview-subject"></b></div><iframe id="rtsap-template-preview" class="rtsap-email-preview" title="Email template preview" sandbox="allow-popups allow-popups-to-escape-sandbox"></iframe></div></div></section>';

		echo '<section class="rtsap-builder-step" data-step="audience"><div class="rtsap-builder-grid"><div class="rtsap-panel"><div class="rtsap-panel__head"><h3>Audience</h3></div>';
		echo '<label class="rtsap-field"><span>Audience segment</span><select id="rtsap-audience-filter" name="audience_filter"><option value="all"' . selected( $audience_filter, 'all', false ) . '>All verified participants</option><option value="runners_only"' . selected( $audience_filter, 'runners_only', false ) . '>Runners only</option><option value="non_runners_only"' . selected( $audience_filter, 'non_runners_only', false ) . '>Non-runners only</option></select></label>';
		echo '<label class="rtsap-field"><span>Subscription category</span><select id="rtsap-campaign-category" name="category"><option value="general"' . selected( $category, 'general', false ) . '>General updates</option><option value="survey"' . selected( $category, 'survey', false ) . '>Survey</option><option value="referral"' . selected( $category, 'referral', false ) . '>Referral</option><option value="trophy"' . selected( $category, 'trophy', false ) . '>Trophy</option></select></label><p class="description">Email verification, subscription consent, and declined-contact preferences are always enforced.</p></div>';
		echo '<div class="rtsap-panel rtsap-audience-summary"><div class="rtsap-panel__head"><h3>Estimated Reach</h3></div><strong id="rtsap-audience-final">0</strong><span>sendable recipients</span><dl><div><dt>Matching segment</dt><dd id="rtsap-audience-matching">0</dd></div><div><dt>Excluded by consent</dt><dd id="rtsap-audience-excluded">0</dd></div><div><dt>Excluded by condition</dt><dd id="rtsap-audience-conditional">0</dd></div></dl></div>';
		echo '<div class="rtsap-panel rtsap-panel--wide rtsap-conditional-exclusion"><div class="rtsap-panel__head"><div><h3>Conditional Exclusion</h3><p>Exclude participants whose stored referral or Captain Miles value matches this rule.</p></div></div><div class="rtsap-rule-builder"><span>Exclude when</span><label class="rtsap-field"><span>Metric</span><select id="rtsap-exclusion-metric" name="exclusion_metric"><option value="none"' . selected( $exclusion_rule['metric'], 'none', false ) . '>No conditional exclusion</option><option value="total_referral_bonus"' . selected( $exclusion_rule['metric'], 'total_referral_bonus', false ) . '>Referral bonus points</option><option value="successful_referrals"' . selected( $exclusion_rule['metric'], 'successful_referrals', false ) . '>Verified referrals</option><option value="referral_count"' . selected( $exclusion_rule['metric'], 'referral_count', false ) . '>Total referrals</option><option value="captain_miles_balance"' . selected( $exclusion_rule['metric'], 'captain_miles_balance', false ) . '>Captain Miles balance</option><option value="total_captain_miles_earned"' . selected( $exclusion_rule['metric'], 'total_captain_miles_earned', false ) . '>Total Captain Miles earned</option></select></label><label class="rtsap-field"><span>Condition</span><select id="rtsap-exclusion-operator" name="exclusion_operator"><option value=">="' . selected( $exclusion_rule['operator'], '>=', false ) . '>At least (≥)</option><option value=">"' . selected( $exclusion_rule['operator'], '>', false ) . '>More than (&gt;)</option><option value="="' . selected( $exclusion_rule['operator'], '=', false ) . '>Exactly (=)</option><option value="<="' . selected( $exclusion_rule['operator'], '<=', false ) . '>At most (≤)</option><option value="<"' . selected( $exclusion_rule['operator'], '<', false ) . '>Less than (&lt;)</option></select></label><label class="rtsap-field"><span>Value</span><input id="rtsap-exclusion-value" name="exclusion_value" type="number" min="0" step="1" value="' . (int) $exclusion_rule['value'] . '"></label></div><p class="description">Example: Referral bonus points at least 1,000 excludes users who already reached 1K points.</p></div>';
		echo '<div class="rtsap-panel rtsap-panel--wide rtsap-recipient-picker"><div class="rtsap-panel__head"><div><h3>Recipient Preview</h3><p>Search by name or email and see exactly why a participant is included or excluded.</p></div></div><label class="rtsap-field"><span>Search by name or email</span><input id="rtsap-recipient-search" type="search" placeholder="Start typing a name or email"></label><div class="rtsap-recipient-table-wrap"><table class="widefat striped rtsap-recipient-table"><thead><tr><th>Participant</th><th>Email</th><th>Runner status</th><th>Referral points</th><th>Verified referrals</th><th>Selection status</th></tr></thead><tbody>';
		if ( ! $recipient_candidates ) {
			echo '<tr><td colspan="6">No participants found.</td></tr>';
		} else {
			foreach ( $recipient_candidates as $recipient ) {
				$name_label = trim( (string) $recipient->name ) ?: __( 'Unnamed participant', 'run-the-seas' );
				echo '<tr class="rtsap-recipient-row" data-search="' . esc_attr( strtolower( $name_label . ' ' . $recipient->email ) ) . '" data-verified="' . (int) $recipient->email_verified . '" data-declined="' . (int) $recipient->declined_further_contact . '" data-runner="' . esc_attr( $recipient->runner_status ?: '' ) . '" data-sub-general="' . (int) $recipient->subscribed_general . '" data-sub-survey="' . (int) $recipient->subscribed_survey . '" data-sub-referral="' . (int) $recipient->subscribed_referral . '" data-sub-trophy="' . (int) $recipient->subscribed_trophy . '" data-total-referral-bonus="' . (int) $recipient->total_referral_bonus . '" data-successful-referrals="' . (int) $recipient->successful_referrals . '" data-referral-count="' . (int) $recipient->referral_count . '" data-captain-miles-balance="' . (int) $recipient->captain_miles_balance . '" data-total-captain-miles-earned="' . (int) $recipient->total_captain_miles_earned . '"><td><strong>' . esc_html( $name_label ) . '</strong></td><td>' . esc_html( $recipient->email ) . '</td><td>' . esc_html( $recipient->runner_status ? str_replace( '_', ' ', $recipient->runner_status ) : '—' ) . '</td><td>' . number_format_i18n( (int) $recipient->total_referral_bonus ) . '</td><td>' . number_format_i18n( (int) $recipient->successful_referrals ) . '</td><td class="rtsap-recipient-state">Checking…</td></tr>';
			}
		}
		echo '</tbody></table></div><p class="rtsap-recipient-visible-count"><span id="rtsap-recipient-visible">0</span> participants shown</p></div></div></section>';

		echo '<section class="rtsap-builder-step" data-step="trigger"><div class="rtsap-panel rtsap-panel--wide"><div class="rtsap-panel__head"><h3>Automation Trigger</h3></div><div class="rtsap-mode-help" data-mode-help="automation">Choose <strong>Automation</strong> in the delivery bar to use this trigger.</div><div class="rtsap-delivery-config" data-delivery-config="automation"><p>Send once when a participant becomes eligible. The hourly job checks for new recipients and duplicate-send protection prevents repeat delivery.</p><div class="rtsap-inline-fields"><label class="rtsap-field"><span>Wait</span><input type="number" name="trigger_days" value="' . (int) $trigger_days . '" min="0" max="365"></label><label class="rtsap-field"><span>Trigger event</span><select name="trigger_type"><option value="days_after_registration"' . selected( $trigger_type, 'days_after_registration', false ) . '>days after registration</option><option value="days_after_verification"' . selected( $trigger_type, 'days_after_verification', false ) . '>days after email verification</option></select></label></div></div></div></section>';

		echo '<section class="rtsap-builder-step" data-step="schedule"><div class="rtsap-panel rtsap-panel--wide"><div class="rtsap-panel__head"><h3>Schedule</h3></div><div class="rtsap-mode-help" data-mode-help="scheduled">Choose <strong>Schedule once</strong> in the delivery bar to set a date and time.</div><div class="rtsap-delivery-config" data-delivery-config="scheduled"><label class="rtsap-field rtsap-schedule-at"><span>Send date &amp; time (' . esc_html( wp_timezone_string() ) . ')</span><input type="datetime-local" name="scheduled_at" value="' . esc_attr( $scheduled_at ) . '"></label><p>The final consent-safe recipient list is calculated when the scheduled job runs.</p></div><div class="rtsap-delivery-config" data-delivery-config="manual"><h4>Manual delivery selected</h4><p>No schedule is required. Review the campaign and use <strong>Send Now</strong>.</p></div></div></section>';

		echo '<section class="rtsap-builder-step" data-step="preview"><div class="rtsap-builder-grid"><div class="rtsap-panel"><div class="rtsap-panel__head"><h3>Final Preview</h3></div><div class="rtsap-email-preview-meta"><span>Subject</span><b id="rtsap-final-subject"></b></div><iframe id="rtsap-final-preview" class="rtsap-email-preview" title="Final email preview" sandbox="allow-popups allow-popups-to-escape-sandbox"></iframe></div><div class="rtsap-panel"><div class="rtsap-panel__head"><h3>Test &amp; Send</h3></div><label class="rtsap-field"><span>Test recipient</span><input type="email" name="test_email" value="' . esc_attr( $current_user->user_email ) . '" placeholder="admin@example.com"></label><button class="button" type="submit" name="operation" value="test">Save &amp; Send Test</button><div class="rtsap-send-checklist"><p><span class="dashicons dashicons-yes-alt"></span> Unsubscribes enforced</p><p><span class="dashicons dashicons-yes-alt"></span> Duplicate sends prevented</p><p><span class="dashicons dashicons-yes-alt"></span> Every send logged</p></div></div></div></section>';

		echo '<div class="rtsap-builder-actions"><a class="button" href="' . esc_url( $list_url ) . '">New Campaign</a><button class="button" type="submit" name="operation" value="draft">Save Draft</button><button class="button button-primary rtsap-mode-action" data-delivery-action="manual" type="submit" name="operation" value="send_now" onclick="return confirm(\'Send this campaign now to the selected recipients?\')">Send Now</button><button class="button button-primary rtsap-mode-action" data-delivery-action="scheduled" type="submit" name="operation" value="schedule" onclick="return confirm(\'Schedule this one-time campaign?\')">Schedule Send</button><button class="button button-primary rtsap-mode-action" data-delivery-action="automation" type="submit" name="operation" value="activate" onclick="return confirm(\'Activate this automation for current and future eligible recipients?\')">Activate Automation</button></div></form>';

		$script_data = wp_json_encode( array( 'templates' => $preview_map ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
		echo '<script>window.rtsapCampaignBuilderData=' . $script_data . ';</script>';
		echo <<<'HTML'
<script>
(function(){
	var data=window.rtsapCampaignBuilderData||{},form=document.getElementById('rtsap-campaign-form');
	if(!form){return;}
	var tabs=form.querySelectorAll('.rtsap-builder-tab'),steps=form.querySelectorAll('.rtsap-builder-step'),templateSelect=document.getElementById('rtsap-campaign-template'),categorySelect=document.getElementById('rtsap-campaign-category'),filterSelect=document.getElementById('rtsap-audience-filter'),recipientRows=Array.prototype.slice.call(form.querySelectorAll('.rtsap-recipient-row')),search=document.getElementById('rtsap-recipient-search'),exclusionMetric=document.getElementById('rtsap-exclusion-metric'),exclusionOperator=document.getElementById('rtsap-exclusion-operator'),exclusionValue=document.getElementById('rtsap-exclusion-value');
	function openTab(name){tabs.forEach(function(tab){var active=tab.dataset.tab===name;tab.classList.toggle('is-active',active);tab.setAttribute('aria-selected',active?'true':'false');});steps.forEach(function(step){step.classList.toggle('is-active',step.dataset.step===name);});}
	function renderTemplate(){var item=(data.templates||{})[templateSelect.value];if(!item){return;}document.getElementById('rtsap-preview-subject').textContent=item.subject;document.getElementById('rtsap-final-subject').textContent=item.subject;document.getElementById('rtsap-template-preview').srcdoc=item.html;document.getElementById('rtsap-final-preview').srcdoc=item.html;document.getElementById('rtsap-edit-template').href=item.editUrl;}
	function segmentMatches(row,filter){return filter==='runners_only'?row.dataset.runner==='runner':(filter==='non_runners_only'?row.dataset.runner==='non_runner':true);}
	function consentEligible(row,category){return row.dataset.verified==='1'&&row.dataset.declined!=='1'&&row.dataset['sub'+category.charAt(0).toUpperCase()+category.slice(1)]==='1';}
	function consentReason(row,category){if(row.dataset.verified!=='1'){return 'Not verified';}if(row.dataset.declined==='1'){return 'Declined contact';}if(row.dataset['sub'+category.charAt(0).toUpperCase()+category.slice(1)]!=='1'){return 'Unsubscribed';}return '';}
	function conditionMatches(row){var metric=exclusionMetric.value;if(metric==='none'){return false;}var keys={total_referral_bonus:'totalReferralBonus',successful_referrals:'successfulReferrals',referral_count:'referralCount',captain_miles_balance:'captainMilesBalance',total_captain_miles_earned:'totalCaptainMilesEarned'},actual=parseInt(row.dataset[keys[metric]]||'0',10),expected=Math.max(0,parseInt(exclusionValue.value||'0',10));switch(exclusionOperator.value){case '>=':return actual>=expected;case '>':return actual>expected;case '=':return actual===expected;case '<=':return actual<=expected;case '<':return actual<expected;default:return false;}}
	function renderAudience(){var category=categorySelect.value,filter=filterSelect.value,matching=0,excludedConsent=0,excludedConditional=0,finalCount=0,visible=0,query=(search.value||'').trim().toLowerCase();recipientRows.forEach(function(row){var matches=segmentMatches(row,filter),consent=consentEligible(row,category),conditional=conditionMatches(row),selected=matches&&consent&&!conditional,state=row.querySelector('.rtsap-recipient-state');if(matches&&row.dataset.verified==='1'){matching++;if(!consent){excludedConsent++;}else if(conditional){excludedConditional++;}}if(selected){finalCount++;}if(!matches){state.textContent='Outside segment';state.dataset.state='outside';}else if(!consent){state.textContent=consentReason(row,category);state.dataset.state='blocked';}else if(conditional){state.textContent='Excluded by condition';state.dataset.state='excluded';}else{state.textContent='Selected';state.dataset.state='selected';}row.hidden=!!query&&row.dataset.search.indexOf(query)===-1;if(!row.hidden){visible++;}});document.getElementById('rtsap-audience-final').textContent=finalCount;document.getElementById('rtsap-audience-matching').textContent=matching;document.getElementById('rtsap-audience-excluded').textContent=excludedConsent;document.getElementById('rtsap-audience-conditional').textContent=excludedConditional;document.getElementById('rtsap-recipient-visible').textContent=visible;}
	function renderDelivery(){var selected=form.querySelector('input[name="delivery_mode"]:checked'),mode=selected?selected.value:'manual';form.querySelectorAll('[data-delivery-config]').forEach(function(panel){panel.hidden=panel.dataset.deliveryConfig!==mode;});form.querySelectorAll('[data-mode-help]').forEach(function(panel){panel.hidden=panel.dataset.modeHelp===mode;});form.querySelectorAll('[data-delivery-action]').forEach(function(button){button.hidden=button.dataset.deliveryAction!==mode;});}
	tabs.forEach(function(tab){tab.addEventListener('click',function(){openTab(tab.dataset.tab);});});
	templateSelect.addEventListener('change',renderTemplate);categorySelect.addEventListener('change',renderAudience);filterSelect.addEventListener('change',renderAudience);search.addEventListener('input',renderAudience);exclusionMetric.addEventListener('change',renderAudience);exclusionOperator.addEventListener('change',renderAudience);exclusionValue.addEventListener('input',renderAudience);form.querySelectorAll('input[name="delivery_mode"]').forEach(function(radio){radio.addEventListener('change',renderDelivery);});
	renderTemplate();renderAudience();renderDelivery();
})();
</script>
HTML;

		$rows = array();
		foreach ( RTS_Business_Logic_5::list_campaigns() as $c ) {
			$act = '';
			$edit_url = RTSAP_Frontend_Dashboard::screen_url( 'rts-email-campaigns', array( 'campaign_id' => (int) $c->id ) );
			$act .= '<a class="button" href="' . esc_url( $edit_url ) . '">Edit</a> ';
			if ( (int) $c->sent_count > 0 ) { $act .= '<a class="button" href="' . esc_url( RTSAP_Frontend_Dashboard::screen_url( 'rts-email-campaigns', array( 'sent_campaign_id' => (int) $c->id ) ) ) . '#rtsap-sent-recipients">View sent recipients</a> '; }
			if ( 'paused' === $c->status && 'manual' !== ( $c->delivery_mode ?? 'automation' ) ) { $act .= self::form( 'ec_status', '', 'Resume', array( 'id' => $c->id, 'status' => 'active' ) ) . ' '; }
			if ( 'active' === $c->status ) {
				if ( 'automation' === ( $c->delivery_mode ?? 'automation' ) ) { $act .= self::form( 'ec_trigger', '', 'Run trigger check', array( 'id' => $c->id ), 'button button-primary' ) . ' '; }
				$act .= self::form( 'ec_status', '', 'Pause', array( 'id' => $c->id, 'status' => 'paused' ) ) . ' ';
			}
			if ( (int) $c->sent_count > 0 && 'archived' !== $c->status ) { $act .= self::form( 'ec_status', '', 'Archive', array( 'id' => $c->id, 'status' => 'archived' ), 'button', 'return confirm(\'Archive this campaign? Its send history will be preserved.\')' ) . ' '; }
			if ( 'archived' === $c->status ) { $act .= self::form( 'ec_status', '', 'Restore', array( 'id' => $c->id, 'status' => 'completed' ) ) . ' '; }
			if ( 'draft' === $c->status && 0 === (int) $c->sent_count ) { $act .= self::form( 'ec_delete', '', 'Delete', array( 'id' => $c->id ), 'button rtsap-button-danger', 'return confirm(\'Permanently delete this unsent draft? This cannot be undone.\')' ); }
			if ( 'manual' === ( $c->delivery_mode ?? 'automation' ) ) { $delivery = 'Manual one-time'; }
			elseif ( 'scheduled' === ( $c->delivery_mode ?? 'automation' ) ) { $delivery = 'Scheduled: ' . ( $c->scheduled_at ?: 'not set' ); }
			else { $delivery = (int) $c->trigger_days . ' days after ' . str_replace( 'days_after_', '', $c->trigger_type ); }
			$rows[] = array( $c->name, $c->template_name ?: 'Built-in fallback', $delivery, $c->audience_filter, $c->status, (int) $c->sent_count, $act );
		}
		echo '<h3>Campaigns</h3>' . self::tbl( array( 'Name', 'Template', 'Delivery', 'Audience', 'Status', 'Sent', 'Actions' ), $rows );
		$sent_campaign_id = isset( $_GET['sent_campaign_id'] ) ? absint( $_GET['sent_campaign_id'] ) : 0;
		if ( $sent_campaign_id ) {
			$history = RTS_Business_Logic_5::sent_recipients( $sent_campaign_id );
			if ( empty( $history['error'] ) ) {
				echo '<section id="rtsap-sent-recipients" class="rtsap-panel rtsap-sent-recipients"><div class="rtsap-panel__head"><div><h3>Sent recipients — ' . esc_html( $history['campaign']->name ) . '</h3><p>' . count( $history['recipients'] ) . ' recorded deliveries</p></div><a class="button" href="' . esc_url( $list_url ) . '">Close</a></div><label class="rtsap-field"><span>Search sent recipients</span><input type="search" id="rtsap-sent-search" placeholder="Search name or email"></label><div class="rtsap-recipient-table-wrap"><table class="widefat striped rtsap-recipient-table"><thead><tr><th>Participant ID</th><th>Name</th><th>Email</th><th>Sent at</th></tr></thead><tbody>';
				foreach ( $history['recipients'] as $recipient ) { echo '<tr class="rtsap-sent-row" data-search="' . esc_attr( strtolower( (string) $recipient->name . ' ' . (string) $recipient->email ) ) . '"><td>' . (int) $recipient->participant_id . '</td><td>' . esc_html( $recipient->name ?: '—' ) . '</td><td>' . esc_html( $recipient->email ?: '—' ) . '</td><td>' . esc_html( $recipient->sent_at ) . '</td></tr>'; }
				echo '</tbody></table></div></section><script>(function(){var search=document.getElementById("rtsap-sent-search"),rows=document.querySelectorAll(".rtsap-sent-row");if(!search){return;}search.addEventListener("input",function(){var q=search.value.trim().toLowerCase();rows.forEach(function(row){row.hidden=!!q&&row.dataset.search.indexOf(q)===-1;});});})();</script>';
			}
		}
		echo '</div>';
	}
	public static function handle_ec_create()  { self::guard( 'ec_create' );  RTS_Business_Logic_5::create_campaign( array( 'name' => sanitize_text_field( $_POST['name'] ), 'trigger_type' => sanitize_key( $_POST['trigger_type'] ), 'trigger_days' => (int) $_POST['trigger_days'], 'audience_filter' => sanitize_key( $_POST['audience_filter'] ), 'category' => sanitize_key( $_POST['category'] ), 'created_by' => self::admin() ) ); self::back( 'rts-email-campaigns', 'Campaign created as draft.' ); }
	public static function handle_ec_save() {
		self::guard( 'ec_save' );
		$operation = sanitize_key( $_POST['operation'] ?? 'draft' );
		$delivery_mode = sanitize_key( $_POST['delivery_mode'] ?? 'automation' );
		$required_operation = array( 'manual' => 'send_now', 'scheduled' => 'schedule', 'automation' => 'activate' );
		if ( ! in_array( $delivery_mode, array_keys( $required_operation ), true ) ) { self::back( 'rts-email-campaigns', 'Error: INVALID_DELIVERY_MODE', array( 'campaign_id' => absint( $_POST['id'] ?? 0 ) ) ); }
		if ( ! in_array( $operation, array( 'draft', 'test' ), true ) && $required_operation[ $delivery_mode ] !== $operation ) { self::back( 'rts-email-campaigns', 'Error: DELIVERY_ACTION_MISMATCH', array( 'campaign_id' => absint( $_POST['id'] ?? 0 ) ) ); }
		$status = in_array( $operation, array( 'schedule', 'send_now', 'activate' ), true ) ? 'active' : sanitize_key( $_POST['existing_status'] ?? 'draft' );
		if ( 'draft' === $operation ) { $status = 'draft'; }
		$scheduled_at = '';
		$schedule_timestamp = 0;
		if ( 'scheduled' === $delivery_mode && ! empty( $_POST['scheduled_at'] ) ) {
			$date = date_create_immutable_from_format( 'Y-m-d\TH:i', sanitize_text_field( wp_unslash( $_POST['scheduled_at'] ) ), wp_timezone() );
			if ( $date ) { $scheduled_at = $date->format( 'Y-m-d H:i:s' ); $schedule_timestamp = $date->getTimestamp(); }
		}
		$exclusion_rules = array();
		$exclusion_metric = sanitize_key( $_POST['exclusion_metric'] ?? 'none' );
		if ( 'none' !== $exclusion_metric ) {
			$exclusion_rules = RTS_Business_Logic_5::exclusion_rules( array( array(
				'metric' => $exclusion_metric,
				'operator' => sanitize_text_field( wp_unslash( $_POST['exclusion_operator'] ?? '>=' ) ),
				'value' => absint( $_POST['exclusion_value'] ?? 0 ),
			) ) );
		}
		$r = RTS_Business_Logic_5::save_campaign( array(
			'id' => absint( $_POST['id'] ?? 0 ), 'name' => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
			'template_id' => absint( $_POST['template_id'] ?? 0 ), 'delivery_mode' => $delivery_mode,
			'trigger_type' => sanitize_key( $_POST['trigger_type'] ?? 'days_after_registration' ), 'trigger_days' => absint( $_POST['trigger_days'] ?? 0 ),
			'audience_filter' => sanitize_key( $_POST['audience_filter'] ?? 'all' ), 'category' => sanitize_key( $_POST['category'] ?? 'general' ),
			'recipient_include_ids' => array(), 'recipient_exclude_ids' => array(), 'exclusion_rules' => $exclusion_rules,
			'scheduled_at' => $scheduled_at, 'status' => $status, 'created_by' => self::admin(),
		) );
		if ( $r['error'] ) { self::back( 'rts-email-campaigns', 'Error: ' . $r['error'], array( 'campaign_id' => absint( $_POST['id'] ?? 0 ) ) ); }
		$id = (int) $r['campaign_id'];
		if ( in_array( $operation, array( 'draft', 'schedule', 'send_now', 'activate' ), true ) ) { wp_clear_scheduled_hook( 'rts_run_scheduled_campaign', array( $id ) ); }
		if ( 'test' === $operation ) {
			$test = RTS_Business_Logic_5::send_campaign_test( $id, sanitize_email( wp_unslash( $_POST['test_email'] ?? '' ) ), self::admin() );
			self::back( 'rts-email-campaigns', $test['error'] ? 'Error: ' . $test['error'] : 'Test email sent and logged.', array( 'campaign_id' => $id ) );
		}
		if ( 'send_now' === $operation ) {
			$sent = RTS_Business_Logic_5::run_trigger_check( $id, self::admin(), true );
			self::back( 'rts-email-campaigns', $sent['error'] ? 'Error: ' . $sent['error'] : 'Campaign sent to ' . (int) $sent['newly_sent'] . ' recipient(s); ' . (int) $sent['excluded_unsubscribed'] . ' excluded by consent and ' . (int) $sent['excluded_by_conditions'] . ' by campaign condition.', array( 'campaign_id' => $id ) );
		}
		if ( 'schedule' === $operation ) {
			if ( $schedule_timestamp <= time() ) {
				$sent = RTS_Business_Logic_5::run_trigger_check( $id, self::admin() );
				self::back( 'rts-email-campaigns', $sent['error'] ? 'Error: ' . $sent['error'] : 'Scheduled time had already arrived; campaign sent to ' . (int) $sent['newly_sent'] . ' recipient(s), with ' . (int) $sent['excluded_by_conditions'] . ' excluded by campaign condition.', array( 'campaign_id' => $id ) );
			}
			$scheduled = wp_schedule_single_event( $schedule_timestamp, 'rts_run_scheduled_campaign', array( $id ), true );
			if ( is_wp_error( $scheduled ) ) { self::back( 'rts-email-campaigns', 'Error: SCHEDULE_FAILED — ' . $scheduled->get_error_message(), array( 'campaign_id' => $id ) ); }
			self::back( 'rts-email-campaigns', 'Campaign scheduled for ' . $scheduled_at . '.', array( 'campaign_id' => $id ) );
		}
		if ( 'activate' === $operation ) { self::back( 'rts-email-campaigns', 'Automation activated. The hourly job will send to newly eligible recipients.', array( 'campaign_id' => $id ) ); }
		self::back( 'rts-email-campaigns', 'Campaign saved as draft.', array( 'campaign_id' => $id ) );
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
	public static function handle_ec_trigger() { self::guard( 'ec_trigger' ); $r = RTS_Business_Logic_5::run_trigger_check( (int) $_POST['id'], self::admin() ); self::back( 'rts-email-campaigns', $r['error'] ? 'Error: ' . $r['error'] : 'Trigger check: eligible ' . (int) $r['eligible_count'] . ', newly sent ' . (int) $r['newly_sent'] . ', excluded by consent ' . (int) $r['excluded_unsubscribed'] . ', excluded by condition ' . (int) $r['excluded_by_conditions'] . '.' ); }
	public static function handle_ec_delete() { self::guard( 'ec_delete' ); $r = RTS_Business_Logic_5::delete_campaign( absint( $_POST['id'] ?? 0 ), self::admin() ); self::back( 'rts-email-campaigns', $r['error'] ? 'Error: ' . $r['error'] : 'Unsent draft permanently deleted.' ); }

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
