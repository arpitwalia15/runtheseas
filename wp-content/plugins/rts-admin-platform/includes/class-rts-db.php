<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_DB {

	public static function create_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$prefix = $wpdb->prefix . 'rts_';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// ===== SURVEYS =====
		dbDelta( "CREATE TABLE {$prefix}surveys (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			source_form_id BIGINT UNSIGNED NULL,
			name VARCHAR(255) NOT NULL,
			language VARCHAR(10) DEFAULT 'EN',
			status VARCHAR(20) DEFAULT 'draft',
			version INT DEFAULT 1,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY source_form_id (source_form_id)
		) $charset_collate;" );

		dbDelta( "CREATE TABLE {$prefix}survey_questions (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			survey_id BIGINT UNSIGNED NOT NULL,
			source_form_id BIGINT UNSIGNED NULL,
			source_question_id VARCHAR(255) NULL,
			question_number INT NOT NULL,
			section VARCHAR(100),
			prompt TEXT NOT NULL,
			question_type VARCHAR(30) NOT NULL,
			options_json TEXT,
			required TINYINT(1) DEFAULT 1,
			allow_comment TINYINT(1) DEFAULT 0,
			conditional_on_question_id BIGINT UNSIGNED NULL,
			conditional_equals VARCHAR(255) NULL,
			sort_order INT DEFAULT 0,
			PRIMARY KEY (id),
			KEY survey_id (survey_id),
			UNIQUE KEY source_question (source_form_id, source_question_id)
		) $charset_collate;" );

		// ===== PARTICIPANTS =====
		if ( self::table_exists( "{$prefix}participants" ) ) {
			self::ensure_participant_columns( "{$prefix}participants" );
		} else {
		dbDelta( "CREATE TABLE {$prefix}participants (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT NULL,
			survey_tracking_id BIGINT NULL,
			email VARCHAR(255) NOT NULL,
			first_name VARCHAR(100) NOT NULL DEFAULT '',
			last_name VARCHAR(100) NOT NULL DEFAULT '',
			phone VARCHAR(50) NULL,
			country VARCHAR(100) NULL,
			city VARCHAR(100) NULL,
			address TEXT NULL,
			date_of_birth DATE NULL,
			gender VARCHAR(20) NULL,
			emergency_contact_name VARCHAR(255) NULL,
			emergency_contact_phone VARCHAR(50) NULL,
			registration_date DATETIME NULL,
			email_verified TINYINT(1) DEFAULT 0,
			email_verification_token VARCHAR(64) NULL,
			email_verification_date DATETIME NULL,
			cabin_credit_requested TINYINT(1) DEFAULT 0,
			cabin_credit_number VARCHAR(50) NULL,
			cabin_credit_status VARCHAR(20) NULL,
			cabin_credit_approved_date DATETIME NULL,
			captain_suite_status VARCHAR(20) NULL,
			captain_referral_participation VARCHAR(20) NULL,
			captain_miles_balance INT DEFAULT 0,
			total_captain_miles_earned INT DEFAULT 0,
			total_captain_miles_used INT DEFAULT 0,
			referral_code VARCHAR(50) NULL,
			referral_count INT DEFAULT 0,
			successful_referrals INT DEFAULT 0,
			total_referral_bonus INT DEFAULT 0,
			created_at DATETIME NULL,
			updated_at DATETIME NULL,
			referred_by BIGINT NULL,
			referral_completed TINYINT(1) DEFAULT 0,
			referral_completed_date DATETIME NULL,
			qr_code_url VARCHAR(500) NULL,
			cabin_credit_amount DECIMAL(10,2) NULL,
			cabin_credit_issued_at DATETIME NULL,
			cabin_credit_issued_by BIGINT NULL,
			captain_suite_activated_at DATETIME NULL,
			captain_suite_activated_by BIGINT NULL,
			certificate_number VARCHAR(50) NULL,
			certificate_issued_at DATETIME NULL,
			certificate_sent_at DATETIME NULL,
			age_consent_confirmed_at DATETIME NULL,
			age_consent_ip_address VARCHAR(45) NULL,
			founding_runner_number VARCHAR(50) NULL,
			unsubscribe_token VARCHAR(40) NULL,
			name VARCHAR(255),
			verification_token VARCHAR(64) NULL,
			verification_sent_at DATETIME NULL,
			verified_at DATETIME NULL,
			runner_status VARCHAR(20) NULL,
			province VARCHAR(100) NULL,
			age_range VARCHAR(20) NULL,
			travel_party_size INT NULL,
			household_income_bracket VARCHAR(50) NULL,
			marketing_source VARCHAR(50) NULL,
			utm_campaign VARCHAR(100) NULL,
			account_status VARCHAR(20) DEFAULT 'active',
			merged_into_participant_id BIGINT UNSIGNED NULL,
			merged_at DATETIME NULL,
			wants_cruise_notification TINYINT(1) DEFAULT 0,
			declined_further_contact TINYINT(1) DEFAULT 0,
			referred_by_participant_id BIGINT UNSIGNED NULL,
			registered_at DATETIME NULL,
			PRIMARY KEY (id),
			UNIQUE KEY email (email),
			KEY survey_tracking_id (survey_tracking_id),
			KEY merged_into_participant_id (merged_into_participant_id),
			UNIQUE KEY founding_runner_number (founding_runner_number),
			UNIQUE KEY referral_code (referral_code),
			UNIQUE KEY unsubscribe_token (unsubscribe_token)
		) $charset_collate;" );
		}

		// ===== SURVEY RESPONSES / ANSWERS =====
		dbDelta( "CREATE TABLE {$prefix}survey_responses (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			survey_id BIGINT UNSIGNED NOT NULL,
			participant_id BIGINT UNSIGNED NULL,
			source_tracking_id BIGINT UNSIGNED NULL,
			source_submission_id VARCHAR(36) NULL,
			session_token VARCHAR(64) NOT NULL,
			status VARCHAR(20) DEFAULT 'in_progress',
			started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			completed_at DATETIME NULL,
			PRIMARY KEY (id),
			KEY survey_id (survey_id),
			UNIQUE KEY source_tracking_id (source_tracking_id),
			UNIQUE KEY source_submission_id (source_submission_id)
		) $charset_collate;" );

		if ( self::table_exists( "{$prefix}survey_answers" ) ) {
			self::ensure_answer_columns( "{$prefix}survey_answers" );
		} else {
		dbDelta( "CREATE TABLE {$prefix}survey_answers (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			tracking_id BIGINT UNSIGNED NULL,
			tracking_submission_id VARCHAR(36) NULL,
			form_id BIGINT UNSIGNED NULL,
			question_id VARCHAR(255) NOT NULL,
			question_label TEXT NULL,
			question_type VARCHAR(50) NULL,
			answer_value TEXT,
			answer_label TEXT NULL,
			step_number INT NULL,
			is_final_answer TINYINT(1) DEFAULT 0,
			response_id BIGINT UNSIGNED NULL,
			platform_question_id BIGINT UNSIGNED NULL,
			comment_text TEXT,
			answered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY response_id (response_id),
			KEY tracking_id (tracking_id),
			KEY tracking_submission_id (tracking_submission_id),
			KEY form_id (form_id),
			KEY question_id (question_id),
			KEY platform_question_id (platform_question_id)
		) $charset_collate;" );
		}

		// ===== REFERRALS =====
		if ( self::table_exists( "{$prefix}referrals" ) ) {
			self::ensure_referral_columns( "{$prefix}referrals" );
		} else {
		dbDelta( "CREATE TABLE {$prefix}referrals (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			referrer_id BIGINT UNSIGNED NULL,
			referred_email VARCHAR(255) NULL,
			referred_participant_id BIGINT UNSIGNED NULL,
			referral_code VARCHAR(50) NOT NULL,
			referral_source VARCHAR(100) NULL,
			status VARCHAR(20) NULL,
			bonus_earned INT DEFAULT 0,
			referral_date DATETIME NULL,
			completed_date DATETIME NULL,
			created_at DATETIME NULL,
			referring_participant_id BIGINT UNSIGNED NULL,
			clicked_at DATETIME NULL,
			verified_at DATETIME NULL,
			verified TINYINT(1) DEFAULT 0,
			fraud_review_status VARCHAR(20) DEFAULT 'clear',
			PRIMARY KEY (id),
			KEY referrer_id (referrer_id),
			KEY referred_email (referred_email),
			KEY referring_participant_id (referring_participant_id)
		) $charset_collate;" );
		}

		// ===== TROPHIES =====
		dbDelta( "CREATE TABLE {$prefix}trophies (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(100) NOT NULL,
			description TEXT,
			unlock_rule VARCHAR(50),
			category VARCHAR(20) DEFAULT 'repeatable',
			PRIMARY KEY (id)
		) $charset_collate;" );

		dbDelta( "CREATE TABLE {$prefix}trophy_unlocks (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			trophy_id BIGINT UNSIGNED NOT NULL,
			participant_id BIGINT UNSIGNED NOT NULL,
			unlocked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY trophy_participant (trophy_id, participant_id)
		) $charset_collate;" );

		// ===== CABIN CREDITS =====
		dbDelta( "CREATE TABLE {$prefix}cabin_credits (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			participant_id BIGINT UNSIGNED NOT NULL,
			status VARCHAR(20) DEFAULT 'issued',
			value_usd DECIMAL(10,2) DEFAULT 100.00,
			issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			cabin_reservation_id VARCHAR(50) NULL,
			PRIMARY KEY (id),
			UNIQUE KEY participant_id (participant_id)
		) $charset_collate;" );

		// ===== SUBSCRIPTIONS =====
		dbDelta( "CREATE TABLE {$prefix}subscriptions (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			participant_id BIGINT UNSIGNED NOT NULL,
			category VARCHAR(20) NOT NULL,
			subscribed TINYINT(1) DEFAULT 1,
			unsubscribe_reason TEXT NULL,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY participant_category (participant_id, category)
		) $charset_collate;" );

		// ===== SENT EMAILS (bulk send log) =====
		dbDelta( "CREATE TABLE {$prefix}sent_emails (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			category VARCHAR(20) NOT NULL,
			subject VARCHAR(255),
			recipient_count INT,
			excluded_unsubscribed_count INT,
			sent_by VARCHAR(100),
			test_mode TINYINT(1) DEFAULT 0,
			sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		// ===== EMAIL TEMPLATES (Batch 2) — version history is NEVER destroyed =====
		dbDelta( "CREATE TABLE {$prefix}email_templates (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			template_key VARCHAR(64) NULL,
			action_key VARCHAR(64) NULL,
			name VARCHAR(255) NOT NULL,
			category VARCHAR(30) DEFAULT 'general',
			subject VARCHAR(255) NOT NULL,
			html_body LONGTEXT,
			plain_text_body LONGTEXT,
			status VARCHAR(20) DEFAULT 'draft',
			version INT DEFAULT 1,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY template_key (template_key),
			UNIQUE KEY action_key (action_key)
		) $charset_collate;" );

		dbDelta( "CREATE TABLE {$prefix}email_template_versions (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			template_id BIGINT UNSIGNED NOT NULL,
			version INT NOT NULL,
			subject VARCHAR(255),
			html_body LONGTEXT,
			plain_text_body LONGTEXT,
			saved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY template_id (template_id)
		) $charset_collate;" );

		// ===== DRAWS (Batch 3 — Draw A / Draw B execution log, seed stored for reproducibility) =====
		dbDelta( "CREATE TABLE {$prefix}draws (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			draw_type VARCHAR(1) NOT NULL,
			random_seed VARCHAR(64) NOT NULL,
			eligible_entry_count INT,
			winner_participant_id BIGINT UNSIGNED NULL,
			run_by VARCHAR(100),
			run_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		// ===== EMAIL DRAFTS (Batch 3 — the send-gate: test-to-self -> test-to-group -> bulk) =====
		dbDelta( "CREATE TABLE {$prefix}email_drafts (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			category VARCHAR(20) NOT NULL,
			audience_filter VARCHAR(30) DEFAULT 'all',
			subject VARCHAR(255) NOT NULL,
			body LONGTEXT,
			created_by VARCHAR(100),
			test_self_sent TINYINT(1) DEFAULT 0,
			test_self_sent_at DATETIME NULL,
			test_self_email VARCHAR(255) NULL,
			test_group_sent TINYINT(1) DEFAULT 0,
			test_group_sent_at DATETIME NULL,
			test_group_emails TEXT NULL,
			bulk_sent TINYINT(1) DEFAULT 0,
			bulk_sent_at DATETIME NULL,
			bulk_sent_forced TINYINT(1) DEFAULT 0,
			bulk_sent_force_reason TEXT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		// ===== BACKUPS (Batch 4) =====
		dbDelta( "CREATE TABLE {$prefix}backups (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			triggered_by VARCHAR(100),
			status VARCHAR(20) DEFAULT 'completed',
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		// ===== AD CAMPAIGNS (Batch 5 — UTM attribution) =====
		dbDelta( "CREATE TABLE {$prefix}campaigns (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			platform VARCHAR(50),
			utm_campaign_code VARCHAR(100) NOT NULL,
			status VARCHAR(20) DEFAULT 'active',
			cost_charged DECIMAL(10,2) DEFAULT 0,
			impressions INT DEFAULT 0,
			clicks INT DEFAULT 0,
			ad_wording TEXT,
			target_age_groups VARCHAR(100),
			audience_focus VARCHAR(50),
			geography VARCHAR(100),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY utm_campaign_code (utm_campaign_code)
		) $charset_collate;" );

		// ===== EMAIL CAMPAIGNS (Batch 5 — automated, triggered; distinct from Broadcast) =====
		dbDelta( "CREATE TABLE {$prefix}email_campaigns (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			template_id BIGINT UNSIGNED NULL,
			trigger_type VARCHAR(40) DEFAULT 'days_after_registration',
			trigger_days INT DEFAULT 3,
			audience_filter VARCHAR(30) DEFAULT 'all',
			category VARCHAR(20) DEFAULT 'general',
			status VARCHAR(20) DEFAULT 'draft',
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		dbDelta( "CREATE TABLE {$prefix}campaign_sends (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			campaign_id BIGINT UNSIGNED NOT NULL,
			participant_id BIGINT UNSIGNED NOT NULL,
			sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY campaign_participant (campaign_id, participant_id)
		) $charset_collate;" );

		// ===== DUPLICATE REVIEWS (Batch 5 — a reviewed pair never reappears) =====
		dbDelta( "CREATE TABLE {$prefix}duplicate_reviews (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			participant_id_a BIGINT UNSIGNED NOT NULL,
			participant_id_b BIGINT UNSIGNED NOT NULL,
			reason TEXT,
			status VARCHAR(30) DEFAULT 'pending',
			reviewed_by VARCHAR(100),
			reviewed_at DATETIME NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY pair (participant_id_a, participant_id_b)
		) $charset_collate;" );

		// ===== CUSTOMER QUESTIONS (Batch 6 — full draft->revise->approve->send loop) =====
		// NOTE: no LLM integration. "AI Create Draft" is a real hook point (spec Appendix Q) but the
		// draft here is typed by an admin. The loop, versioned history, and response log ARE real.
		dbDelta( "CREATE TABLE {$prefix}customer_questions (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			participant_id BIGINT UNSIGNED NULL,
			question_text TEXT NOT NULL,
			source VARCHAR(20) DEFAULT 'manual',
			status VARCHAR(20) DEFAULT 'open',
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );
		dbDelta( "CREATE TABLE {$prefix}question_response_drafts (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			customer_question_id BIGINT UNSIGNED NOT NULL,
			version INT NOT NULL,
			draft_text TEXT NOT NULL,
			feedback_that_prompted_this TEXT NULL,
			created_by VARCHAR(100),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY customer_question_id (customer_question_id)
		) $charset_collate;" );
		dbDelta( "CREATE TABLE {$prefix}question_response_log (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			customer_question_id BIGINT UNSIGNED NOT NULL,
			final_response TEXT NOT NULL,
			version_count INT,
			approved_by VARCHAR(100),
			sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		// ===== CONTENT BLOCKS (Batch 6 — CMS; survey.js fetches from here at runtime) =====
		dbDelta( "CREATE TABLE {$prefix}content_blocks (
			block_key VARCHAR(100) NOT NULL,
			value TEXT,
			updated_by VARCHAR(100),
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (block_key)
		) $charset_collate;" );

		// ===== EXPORT HISTORY (Batch 6) =====
		dbDelta( "CREATE TABLE {$prefix}export_history (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			dataset VARCHAR(50) NOT NULL,
			format VARCHAR(10) DEFAULT 'csv',
			requested_by VARCHAR(100),
			row_count INT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		// ===== REPORT BUILDER / SAVED REPORTS (Batch 7) =====
		dbDelta( "CREATE TABLE {$prefix}report_definitions (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			data_source VARCHAR(40) NOT NULL,
			fields_json TEXT NOT NULL,
			filters_json TEXT NULL,
			schedule_frequency VARCHAR(20) DEFAULT 'none',
			created_by VARCHAR(100),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );
		dbDelta( "CREATE TABLE {$prefix}report_runs (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			report_id BIGINT UNSIGNED NOT NULL,
			row_count INT,
			run_by VARCHAR(100),
			run_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY report_id (report_id)
		) $charset_collate;" );

		// ===== SEGMENTS (Batch 7 — saved filters, recounted live every view) =====
		dbDelta( "CREATE TABLE {$prefix}segments (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255) NOT NULL,
			filters_json TEXT NOT NULL,
			created_by VARCHAR(100),
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;" );

		// ===== ACTION ITEMS (Batch 7 — REAL rule-based, explicitly NOT AI; rule_key prevents duplicate open items) =====
		dbDelta( "CREATE TABLE {$prefix}action_items (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			rule_key VARCHAR(60) NOT NULL,
			category VARCHAR(60) NOT NULL,
			recommendation TEXT NOT NULL,
			backed_by VARCHAR(255),
			status VARCHAR(20) DEFAULT 'open',
			outcome_note TEXT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			resolved_at DATETIME NULL,
			PRIMARY KEY (id),
			KEY rule_key (rule_key)
		) $charset_collate;" );

		// ===== EXTERNAL FOUNDING RUNNERS (Batch 7 — no real main-site integration; honestly 0 rows) =====
		dbDelta( "CREATE TABLE {$prefix}external_founding_runners (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(255),
			email VARCHAR(255) NOT NULL,
			source VARCHAR(50) DEFAULT 'main_site',
			matched_participant_id BIGINT UNSIGNED NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY email (email)
		) $charset_collate;" );

		// ===== EMAIL OUTBOX (every email the platform generates; 'log' mode stores only, 'send' mode also wp_mail()s) =====
		dbDelta( "CREATE TABLE {$prefix}email_outbox (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			to_email VARCHAR(255) NOT NULL,
			subject VARCHAR(255),
			body_html LONGTEXT,
			kind VARCHAR(20) DEFAULT 'marketing',
			mode VARCHAR(10) DEFAULT 'log',
			delivered TINYINT(1) NULL,
			error TEXT NULL,
			meta TEXT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY to_email (to_email)
		) $charset_collate;" );

		// ===== PARTICIPANT ADMIN NOTES (private; never exposed to participant-facing code) =====
		dbDelta( "CREATE TABLE {$prefix}participant_notes (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			participant_id BIGINT UNSIGNED NOT NULL,
			admin_user_id BIGINT UNSIGNED NULL,
			admin_name VARCHAR(255) NOT NULL,
			note_text TEXT NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY participant_id (participant_id),
			KEY admin_user_id (admin_user_id)
		) $charset_collate;" );

		// A participant can own several completed survey records even though the
		// legacy participants table has only one primary survey_tracking_id.
		dbDelta( "CREATE TABLE {$prefix}participant_survey_links (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			participant_id BIGINT UNSIGNED NOT NULL,
			tracking_id BIGINT UNSIGNED NOT NULL,
			linked_by BIGINT UNSIGNED NULL,
			linked_by_name VARCHAR(255) NULL,
			linked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY tracking_id (tracking_id),
			KEY participant_id (participant_id)
		) $charset_collate;" );

		// ===== AUDIT LOG =====
		dbDelta( "CREATE TABLE {$prefix}audit_log (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			source_table VARCHAR(30) NULL,
			source_id BIGINT UNSIGNED NULL,
			user VARCHAR(100),
			action VARCHAR(255),
			module VARCHAR(100),
			ip_address VARCHAR(45),
			result VARCHAR(20) DEFAULT 'success',
			notes TEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY source_record (source_table, source_id)
		) $charset_collate;" );

		self::ensure_email_template_columns( "{$prefix}email_templates" );
		self::seed_transactional_email_templates( "{$prefix}email_templates", "{$prefix}email_template_versions" );

		update_option( 'rts_admin_platform_db_version', RTSAP_DB_VERSION );
	}

	private static function table_exists( $table ) {
		global $wpdb;
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	private static function add_columns( $table, $columns ) {
		global $wpdb;
		$existing = $wpdb->get_col( "SHOW COLUMNS FROM `$table`", 0 );
		foreach ( $columns as $name => $definition ) {
			if ( ! in_array( $name, $existing, true ) ) {
				$wpdb->query( "ALTER TABLE `$table` ADD COLUMN `$name` $definition" );
			}
		}
	}

	private static function add_index( $table, $name, $definition ) {
		global $wpdb;
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = %s AND index_name = %s", $table, $name ) );
		if ( ! $exists ) { $wpdb->query( "ALTER TABLE `$table` ADD $definition" ); }
	}

	private static function ensure_participant_columns( $table ) {
		self::add_columns( $table, array(
			'founding_runner_number' => 'VARCHAR(50) NULL',
			'unsubscribe_token' => 'VARCHAR(40) NULL',
			'name' => 'VARCHAR(255) NULL',
			'verification_token' => 'VARCHAR(64) NULL',
			'verification_sent_at' => 'DATETIME NULL',
			'verified_at' => 'DATETIME NULL',
			'runner_status' => 'VARCHAR(20) NULL',
			'province' => 'VARCHAR(100) NULL',
			'age_range' => 'VARCHAR(20) NULL',
			'travel_party_size' => 'INT NULL',
			'household_income_bracket' => 'VARCHAR(50) NULL',
			'marketing_source' => 'VARCHAR(50) NULL',
			'utm_campaign' => 'VARCHAR(100) NULL',
			'account_status' => "VARCHAR(20) DEFAULT 'active'",
			'merged_into_participant_id' => 'BIGINT UNSIGNED NULL',
			'merged_at' => 'DATETIME NULL',
			'wants_cruise_notification' => 'TINYINT(1) DEFAULT 0',
			'declined_further_contact' => 'TINYINT(1) DEFAULT 0',
			'referred_by_participant_id' => 'BIGINT UNSIGNED NULL',
			'registered_at' => 'DATETIME NULL',
		) );
		self::add_index( $table, 'founding_runner_number', 'UNIQUE KEY `founding_runner_number` (`founding_runner_number`)' );
		self::add_index( $table, 'unsubscribe_token', 'UNIQUE KEY `unsubscribe_token` (`unsubscribe_token`)' );
		self::add_index( $table, 'merged_into_participant_id', 'KEY `merged_into_participant_id` (`merged_into_participant_id`)' );
	}

	private static function ensure_answer_columns( $table ) {
		self::add_columns( $table, array(
			'response_id' => 'BIGINT UNSIGNED NULL',
			'platform_question_id' => 'BIGINT UNSIGNED NULL',
			'comment_text' => 'TEXT NULL',
		) );
		self::add_index( $table, 'response_id', 'KEY `response_id` (`response_id`)' );
		self::add_index( $table, 'platform_question_id', 'KEY `platform_question_id` (`platform_question_id`)' );
	}

	private static function ensure_referral_columns( $table ) {
		self::add_columns( $table, array(
			'referring_participant_id' => 'BIGINT UNSIGNED NULL',
			'clicked_at' => 'DATETIME NULL',
			'verified_at' => 'DATETIME NULL',
			'verified' => 'TINYINT(1) DEFAULT 0',
			'fraud_review_status' => "VARCHAR(20) DEFAULT 'clear'",
		) );
		self::add_index( $table, 'referring_participant_id', 'KEY `referring_participant_id` (`referring_participant_id`)' );
	}

	private static function ensure_email_template_columns( $table ) {
		self::add_columns( $table, array(
			'template_key' => 'VARCHAR(64) NULL',
			'action_key'   => 'VARCHAR(64) NULL',
		) );
		self::add_index( $table, 'template_key', 'UNIQUE KEY `template_key` (`template_key`)' );
		self::add_index( $table, 'action_key', 'UNIQUE KEY `action_key` (`action_key`)' );
	}

	/** Seed editable action templates while preserving every existing template. */
	private static function seed_transactional_email_templates( $templates_table, $versions_table ) {
		global $wpdb;

		$defaults = self::default_transactional_email_templates();
		$content_version = '1.0';
		$upgrade_empty_defaults = get_option( 'rts_default_email_template_content_version' ) !== $content_version;

		foreach ( $defaults as $default ) {
			$existing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `$templates_table` WHERE template_key = %s", $default['template_key'] ) );
			if ( $existing ) {
				if ( $upgrade_empty_defaults && '' === trim( (string) $existing->html_body ) ) {
					$version = (int) $existing->version + 1;
					$wpdb->update( $templates_table, array( 'html_body' => $default['html_body'], 'version' => $version, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $existing->id ) );
					$wpdb->insert( $versions_table, array( 'template_id' => $existing->id, 'version' => $version, 'subject' => $existing->subject, 'html_body' => $default['html_body'], 'plain_text_body' => $existing->plain_text_body ) );
				}
				continue;
			}

			$inserted = $wpdb->insert(
				$templates_table,
				array(
					'template_key' => $default['template_key'],
					'action_key'   => $default['action_key'],
					'name'         => $default['name'],
					'category'     => 'transactional',
					'subject'      => $default['subject'],
					'html_body'    => $default['html_body'],
					'status'       => 'active',
					'version'      => 1,
				)
			);
			if ( ! $inserted ) {
				continue;
			}

			$wpdb->insert(
				$versions_table,
				array(
					'template_id' => (int) $wpdb->insert_id,
					'version'     => 1,
					'subject'     => $default['subject'],
					'html_body'   => $default['html_body'],
				)
			);
		}

		update_option( 'rts_default_email_template_content_version', $content_version, false );
	}

	/** Editable starter content for the three survey-plugin transactional emails. */
	private static function default_transactional_email_templates() {
		$password_reset = <<<'HTML'
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#eef2f5;padding:24px 8px;"><tr><td align="center">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:640px;background:#ffffff;border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;color:#071b3b;">
<tr><td align="center" style="padding:28px;background:#041c3a;border-bottom:3px solid #d99a1b;color:#ffffff;"><h1 style="margin:0;font-family:Georgia,serif;font-size:30px;color:#f6bd32;">Welcome to Run The Seas!</h1><p style="margin:10px 0 0;font-size:17px;">Your Founding Runner account has been created</p></td></tr>
<tr><td style="padding:32px;"><h2 style="margin:0 0 18px;font-family:Georgia,serif;color:#a65d12;">Ahoy {full_name},</h2><p>Your account is ready. Set your password to sign in and access your Captain&rsquo;s Suite.</p><p style="padding:18px 0;text-align:center;"><a href="{password_reset_url}" style="display:inline-block;padding:14px 30px;border-radius:6px;background:#e2a11d;color:#071b3b;font-weight:bold;text-decoration:none;">SET YOUR PASSWORD</a></p><p style="font-size:13px;color:#5e6877;">If the button does not work, copy this link:<br><a href="{password_reset_url}" style="color:#a65d12;word-break:break-all;">{password_reset_url}</a></p><p style="font-size:13px;color:#5e6877;">Already set a password? <a href="{login_url}" style="color:#a65d12;">Sign in here</a>.</p><p style="margin-top:24px;"><strong>Account email:</strong> {email}</p></td></tr>
<tr><td align="center" style="padding:18px;background:#041c3a;color:#d9e3ef;font-size:12px;">Your information is secure. &copy; {site_name}</td></tr>
</table></td></tr></table>
HTML;

		$email_verification = <<<'HTML'
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#eef2f5;padding:24px 8px;"><tr><td align="center">
<table role="presentation" width="760" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:760px;background:#ffffff;border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;color:#071b3b;">
<tr><td align="center" style="padding:27px;background:#041c3a;border-bottom:3px solid #d48618;"><div style="font-family:Georgia,serif;font-size:30px;font-weight:bold;color:#f6bd32;">RUN THE SEAS</div><div style="margin-top:8px;color:#ffffff;">Run. Explore. Celebrate. Belong.</div></td></tr>
<tr><td style="padding:38px 34px;"><h1 style="margin:0 0 15px;font-family:Georgia,serif;font-size:34px;color:#071b3b;">Your Founding Runner Cruise Credit Is Almost Ready!</h1><h2 style="margin:0 0 20px;font-family:Georgia,serif;color:#a94f12;">Ahoy {full_name},</h2><p>Thank you for helping shape the future of Run The Seas. Please confirm your email address so we can securely issue your Founding Runner benefits.</p><p style="padding:22px 0;text-align:center;"><a href="{verification_url}" style="display:inline-block;padding:16px 34px;border-radius:6px;background:#e2a11d;color:#071b3b;font-size:17px;font-weight:bold;text-decoration:none;">CONFIRM MY EMAIL ADDRESS</a></p><p style="font-size:13px;color:#5e6877;">If the button does not work, copy this secure link:<br><a href="{verification_url}" style="color:#a65d12;word-break:break-all;">{verification_url}</a></p></td></tr>
<tr><td align="center" style="padding:18px;background:#041c3a;color:#d9e3ef;font-size:12px;">This verification message was sent to {email}. &copy; {site_name}</td></tr>
</table></td></tr></table>
HTML;

		$certificate = <<<'HTML'
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#edf1f4;padding:24px 8px;"><tr><td align="center">
<table role="presentation" width="760" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:760px;background:#ffffff;border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;color:#071b3b;">
<tr><td align="center" style="padding:27px;background:#031b38;border-bottom:3px solid #d99a1b;"><div style="font-family:Georgia,serif;font-size:30px;font-weight:bold;color:#e7a41e;">RUN THE SEAS</div><div style="margin-top:8px;color:#ffffff;">You are officially a Founding Runner</div></td></tr>
<tr><td style="padding:38px 34px;"><h1 style="margin:0 0 15px;font-family:Georgia,serif;font-size:34px;color:#a65d12;">Congratulations, {first_name}!</h1><p style="font-size:18px;line-height:1.55;">Your personalized Founding Runner certificate and promotional cruise credit are ready.</p><div style="margin:24px 0;padding:20px;border:1px solid #e2c48d;background:#fffaf0;text-align:center;"><div style="font-size:13px;color:#76531b;">FOUNDING RUNNER NUMBER</div><div style="margin-top:7px;font-family:Georgia,serif;font-size:26px;font-weight:bold;color:#071b3b;">{founding_runner_number}</div><div style="margin-top:9px;font-size:13px;">Certificate: {certificate_number}</div></div><p>Your personalized certificate PDF is attached to this email. Download it, print it, and share it.</p><p style="padding:20px 0;text-align:center;"><a href="{captains_suite_url}" style="display:inline-block;padding:15px 30px;border-radius:6px;background:#eaa20f;color:#15110a;font-weight:bold;text-decoration:none;">ENTER THE CAPTAIN&rsquo;S SUITE</a></p><p>We can&rsquo;t wait to welcome you aboard.</p></td></tr>
<tr><td align="center" style="padding:18px;background:#031b38;color:#d9e3ef;font-size:12px;">Sent to {email}. &copy; {site_name}</td></tr>
</table></td></tr></table>
HTML;

		return array(
			array( 'template_key' => 'default_password_reset', 'action_key' => 'password_reset', 'name' => 'Password Reset', 'subject' => 'Welcome to Run The Seas - Set Your Password', 'html_body' => $password_reset ),
			array( 'template_key' => 'default_email_verification', 'action_key' => 'email_verification', 'name' => 'Email Verification', 'subject' => 'Confirm Your Email Address | Run The Seas', 'html_body' => $email_verification ),
			array( 'template_key' => 'default_founding_runner_certificate', 'action_key' => 'founding_runner_certificate', 'name' => 'Founding Runner Certificate', 'subject' => 'Your Run The Seas Founding Runner Certificate', 'html_body' => $certificate ),
		);
	}

	/**
	 * Import exact copies from the survey plugin.
	 *
	 * This migration normalises the three automatically seeded templates to a
	 * clean v1. The removed revisions were bootstrap/import copies, not admin edits.
	 * Once this migration has run, later admin edits continue at v2 normally.
	 */
	public static function sync_production_transactional_email_templates() {
		global $wpdb;
		$content_version = '4.0';
		if ( get_option( 'rts_production_email_template_content_version' ) === $content_version ) { return; }
		if ( ! function_exists( 'rts_get_production_transactional_email_templates' ) ) { return; }

		$defaults = rts_get_production_transactional_email_templates();
		if ( 3 !== count( $defaults ) ) { return; }
		$templates_table = self::table( 'email_templates' );
		$versions_table = self::table( 'email_template_versions' );
		if ( ! self::table_exists( $templates_table ) || ! self::table_exists( $versions_table ) ) { return; }

		foreach ( $defaults as $default ) {
			$template = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `$templates_table` WHERE template_key = %s", $default['template_key'] ) );
			if ( $template ) {
				$wpdb->update(
					$templates_table,
					array( 'subject' => $default['subject'], 'html_body' => $default['html_body'], 'version' => 1, 'updated_at' => current_time( 'mysql' ) ),
					array( 'id' => $template->id )
				);
				$wpdb->delete( $versions_table, array( 'template_id' => $template->id ), array( '%d' ) );
				$wpdb->insert( $versions_table, array( 'template_id' => $template->id, 'version' => 1, 'subject' => $default['subject'], 'html_body' => $default['html_body'], 'plain_text_body' => $template->plain_text_body ) );
				continue;
			}

			$action_in_use = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM `$templates_table` WHERE action_key = %s", $default['action_key'] ) );
			$wpdb->insert(
				$templates_table,
				array(
					'template_key' => $default['template_key'],
					'action_key' => $action_in_use ? null : $default['action_key'],
					'name' => $default['name'],
					'category' => 'transactional',
					'subject' => $default['subject'],
					'html_body' => $default['html_body'],
					'status' => 'active',
					'version' => 1,
				)
			);
			if ( $wpdb->insert_id ) {
				$wpdb->insert( $versions_table, array( 'template_id' => $wpdb->insert_id, 'version' => 1, 'subject' => $default['subject'], 'html_body' => $default['html_body'] ) );
			}
		}

		update_option( 'rts_production_email_template_content_version', $content_version, false );
	}

	public static function table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'rts_' . $name;
	}
}
