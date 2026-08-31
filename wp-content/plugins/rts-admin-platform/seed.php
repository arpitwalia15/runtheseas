<?php
// Run via: wp eval-file seed.php --allow-root
global $wpdb;

$wpdb->query( "DELETE FROM " . RTS_DB::table( 'survey_answers' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'survey_responses' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'survey_questions' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'surveys' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'trophy_unlocks' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'trophies' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'referrals' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'cabin_credits' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'subscriptions' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'participants' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'audit_log' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'email_template_versions' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'email_templates' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'draws' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'email_drafts' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'sent_emails' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'backups' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'campaign_sends' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'email_campaigns' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'duplicate_reviews' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'campaigns' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'question_response_log' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'question_response_drafts' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'customer_questions' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'content_blocks' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'export_history' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'report_runs' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'report_definitions' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'segments' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'action_items' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'external_founding_runners' ) );
$wpdb->query( "DELETE FROM " . RTS_DB::table( 'email_outbox' ) );
delete_option( 'rts_site_offline' ); delete_option( 'rts_settings' );
// Remove any RTS role users left by prior test runs (keep the WP administrator).
foreach ( get_users( array( 'role__in' => array( 'rts_super_admin','rts_administrator','rts_content_editor','rts_contributor' ) ) ) as $u ) { wp_delete_user( $u->ID ); }
// Reset AUTO_INCREMENT so re-seeding is always predictable — the exact bug class caught and
// fixed in the Node.js prototype (Batch 2) is avoided here from the start.
foreach ( array( 'survey_answers','survey_responses','survey_questions','surveys','trophy_unlocks','trophies','referrals','cabin_credits','subscriptions','participants','audit_log','email_template_versions','email_templates','draws','email_drafts','sent_emails','backups','campaign_sends','email_campaigns','duplicate_reviews','campaigns','question_response_log','question_response_drafts','customer_questions','export_history','report_runs','report_definitions','segments','action_items','external_founding_runners','email_outbox' ) as $t ) {
	$wpdb->query( "ALTER TABLE " . RTS_DB::table( $t ) . " AUTO_INCREMENT = 1" );
}

$wpdb->insert( RTS_DB::table( 'surveys' ), array( 'name' => 'Run The Seas — Market Validation Survey (WordPress)', 'language' => 'EN', 'status' => 'live' ) );
$survey_id = $wpdb->insert_id;

$q = function ( $qn, $section, $prompt, $type, $options, $required = 1, $allow_comment = 0, $cond_on = null, $cond_eq = null ) use ( $wpdb, $survey_id ) {
	$wpdb->insert( RTS_DB::table( 'survey_questions' ), array(
		'survey_id' => $survey_id, 'question_number' => $qn, 'section' => $section, 'prompt' => $prompt,
		'question_type' => $type, 'options_json' => $options ? wp_json_encode( $options ) : null,
		'required' => $required, 'allow_comment' => $allow_comment,
		'conditional_on_question_id' => $cond_on, 'conditional_equals' => $cond_eq, 'sort_order' => $qn,
	) );
	return $wpdb->insert_id;
};

$q1 = $q( 1, 'Demographics', 'What is your age range?', 'multiple_choice', array( '18-24','25-34','35-44','45-54','55-64','65+' ) );
$q2 = $q( 2, 'Demographics', 'Have you completed a running race before?', 'yes_no', array( 'Yes','No' ) );
$q( 3, 'Demographics', 'What distance would you be most interested in?', 'multiple_choice', array( '1K Family Walk/Run','5K','Half Marathon' ), 1, 0, $q2, 'Yes' );
$q( 6, 'Preferences', 'What is most important to you in a cruise vacation?', 'multiple_choice', array( 'Price / value','Itinerary / destinations','Onboard activities','Cabin quality','Dining options' ) );
$q( 9, 'Pricing', 'What price range would you expect to pay per person?', 'multiple_choice', array( 'Under $1,000','$1,000-$2,000','$2,000-$3,000','$3,000+' ) );
$q( 14, 'Wrap-up', "Anything else you'd like us to know?", 'comment', null, 0, 1 );

echo "Seeded survey with id=$survey_id\n";

$trophies = array(
	array( 'Founding Runner', 'Awarded on verified registration', 'founding_runner', 'repeatable' ),
	array( 'First Referral', 'Awarded for your first verified referral', 'first_referral', 'repeatable' ),
	array( '42.2K Finisher', 'Awarded for reaching 42 verified referrals', 'referrals_42', 'historical' ),
);
foreach ( $trophies as $t ) {
	$wpdb->insert( RTS_DB::table( 'trophies' ), array( 'name' => $t[0], 'description' => $t[1], 'unlock_rule' => $t[2], 'category' => $t[3] ) );
}
echo "Seeded trophies\n";
$wpdb->insert( RTS_DB::table( 'campaigns' ), array( 'name' => 'Runners — Age 25-44 Lookalike', 'platform' => 'Google Ads', 'utm_campaign_code' => 'rts_runners_2544_lookalike', 'cost_charged' => 1240.00, 'impressions' => 210300, 'clicks' => 3980, 'target_age_groups' => '25-44', 'audience_focus' => 'Runners' ) );
$wpdb->insert( RTS_DB::table( 'campaigns' ), array( 'name' => 'Half Marathon Enthusiasts', 'platform' => 'Google Ads', 'utm_campaign_code' => 'rts_half_enthusiasts', 'cost_charged' => 860.00, 'impressions' => 142900, 'clicks' => 2640, 'target_age_groups' => '35-54', 'audience_focus' => 'Runners' ) );
echo "Seeded ad campaigns\n";
echo "Seed complete.\n";
