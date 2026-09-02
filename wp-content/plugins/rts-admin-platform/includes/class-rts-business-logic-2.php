<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Business_Logic_2 {

	// ---- Survey Administration ----

	public static function clone_survey( $survey_id, $new_name, $created_by ) {
		global $wpdb;
		$st = RTS_DB::table( 'surveys' ); $qt = RTS_DB::table( 'survey_questions' );
		$source = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $st WHERE id = %d", $survey_id ) );
		if ( ! $source ) { return array( 'error' => 'NOT_FOUND' ); }

		// Fluent Forms is the source of truth for live surveys. Its service copies
		// fields, conditional rules, settings, notifications and related metadata.
		if ( ! empty( $source->source_form_id ) ) {
			if ( ! class_exists( '\\FluentForm\\App\\Services\\Form\\FormService' ) ) {
				return array( 'error' => 'FLUENT_FORMS_NOT_AVAILABLE' );
			}

			try {
				$service = new \FluentForm\App\Services\Form\FormService();
				$form = $service->duplicate( array( 'form_id' => (int) $source->source_form_id ) );
			} catch ( \Throwable $exception ) {
				return array( 'error' => 'FLUENT_FORM_CLONE_FAILED', 'message' => $exception->getMessage() );
			}

			$new_form_id = (int) $form->id;
			$copy_name = $new_name ?: (string) $form->title;
			$fluent_update = array( 'status' => 'unpublished' );
			if ( $new_name ) { $fluent_update['title'] = $copy_name; }
			$wpdb->update( $wpdb->prefix . 'fluentform_forms', $fluent_update, array( 'id' => $new_form_id ) );

			$wpdb->insert( $st, array(
				'source_form_id' => $new_form_id,
				'name'           => $copy_name,
				'language'       => $source->language,
				'status'         => 'draft',
				'version'        => 1,
			) );
			$new_id = (int) $wpdb->insert_id;
			RTS_Business_Logic::log_audit( $created_by ?: 'admin', "Fluent Form cloned: \"{$source->name}\"", 'Survey Administration', 'success', "source_id=$survey_id; source_form_id={$source->source_form_id}; new_id=$new_id; new_form_id=$new_form_id" );
			return array( 'error' => null, 'new_survey_id' => $new_id, 'new_form_id' => $new_form_id );
		}

		$wpdb->insert( $st, array( 'name' => $new_name ?: ( $source->name . ' (copy)' ), 'language' => $source->language, 'status' => 'draft', 'version' => 1 ) );
		$new_id = $wpdb->insert_id;

		$questions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $qt WHERE survey_id = %d ORDER BY sort_order", $survey_id ) );
		$id_map = array();
		foreach ( $questions as $q ) {
			$wpdb->insert( $qt, array(
				'survey_id' => $new_id, 'question_number' => $q->question_number, 'section' => $q->section, 'prompt' => $q->prompt,
				'question_type' => $q->question_type, 'options_json' => $q->options_json, 'required' => $q->required,
				'allow_comment' => $q->allow_comment, 'conditional_on_question_id' => null, 'conditional_equals' => $q->conditional_equals,
				'sort_order' => $q->sort_order,
			) );
			$id_map[ $q->id ] = $wpdb->insert_id;
		}
		// Second pass: remap conditional references to the NEW ids — this is what preserves branching logic.
		foreach ( $questions as $q ) {
			if ( $q->conditional_on_question_id && isset( $id_map[ $q->conditional_on_question_id ] ) ) {
				$wpdb->update( $qt, array( 'conditional_on_question_id' => $id_map[ $q->conditional_on_question_id ] ), array( 'id' => $id_map[ $q->id ] ) );
			}
		}
		RTS_Business_Logic::log_audit( $created_by ?: 'admin', "Survey cloned: \"{$source->name}\"", 'Survey Administration', 'success', "source_id=$survey_id; new_id=$new_id" );
		return array( 'error' => null, 'new_survey_id' => $new_id );
	}

	public static function set_survey_status( $survey_id, $status, $updated_by ) {
		global $wpdb;
		if ( ! in_array( $status, array( 'draft', 'live', 'archived' ), true ) ) { return array( 'error' => 'INVALID_STATUS' ); }
		$st = RTS_DB::table( 'surveys' );
		$survey = $wpdb->get_row( $wpdb->prepare( "SELECT id, source_form_id FROM $st WHERE id = %d", $survey_id ) );
		if ( ! $survey ) { return array( 'error' => 'NOT_FOUND' ); }
		$wpdb->update( $st, array( 'status' => $status ), array( 'id' => $survey_id ) );
		if ( ! empty( $survey->source_form_id ) && self::fluent_forms_table_exists() ) {
			$fluent_status = 'live' === $status ? 'published' : 'unpublished';
			$wpdb->update( $wpdb->prefix . 'fluentform_forms', array( 'status' => $fluent_status ), array( 'id' => (int) $survey->source_form_id ) );
		}
		RTS_Business_Logic::log_audit( $updated_by ?: 'admin', "Survey status -> $status", 'Survey Administration', 'success', "survey_id=$survey_id" );
		return array( 'error' => null );
	}

	public static function list_surveys() {
		global $wpdb;
		$st = RTS_DB::table( 'surveys' ); $rt = RTS_DB::table( 'survey_responses' );
		$rows = $wpdb->get_results( "SELECT * FROM $st ORDER BY created_at DESC" );
		foreach ( $rows as &$s ) {
			$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $rt WHERE survey_id = %d", $s->id ) );
			$done  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $rt WHERE survey_id = %d AND status='completed'", $s->id ) );
			$s->total_responses = $total; $s->completed = $done;
			$s->completion_rate = $total ? round( $done / $total * 100, 1 ) : 0;
			$s->edit_url = ! empty( $s->source_form_id ) ? self::fluent_form_editor_url( $s->source_form_id ) : '';
		}
		return $rows;
	}

	private static function fluent_forms_table_exists() {
		global $wpdb;
		$table = $wpdb->prefix . 'fluentform_forms';
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	public static function fluent_form_editor_url( $form_id ) {
		if ( class_exists( 'RTSAP_Frontend_Dashboard' ) && RTSAP_Frontend_Dashboard::is_platform_user() ) {
			return RTSAP_Frontend_Dashboard::dashboard_url( 'rts-fluent-form', array( 'form_id' => (int) $form_id, 'route' => 'editor' ) );
		}
		return add_query_arg( array( 'page' => 'fluent_forms', 'form_id' => (int) $form_id, 'route' => 'editor' ), admin_url( 'admin.php' ) );
	}

	public static function fluent_form_design_url( $form_id ) {
		if ( class_exists( 'RTSAP_Frontend_Dashboard' ) && RTSAP_Frontend_Dashboard::is_platform_user() ) {
			return RTSAP_Frontend_Dashboard::dashboard_url( 'rts-fluent-form', array( 'form_id' => (int) $form_id, 'route' => 'settings', 'sub_route' => 'form_settings' ) ) . '#/custom-css-js';
		}
		return admin_url( 'admin.php?page=fluent_forms&form_id=' . (int) $form_id . '&route=settings&sub_route=form_settings#/custom-css-js' );
	}

	public static function fluent_form_preview_url( $form_id ) {
		if ( class_exists( '\\FluentForm\\App\\Helpers\\Helper' ) ) {
			return \FluentForm\App\Helpers\Helper::getPreviewUrl( (int) $form_id, 'classic' );
		}
		return add_query_arg( array( 'fluent_forms_pages' => 1, 'design_mode' => 1, 'preview_id' => (int) $form_id ), site_url( '/' ) ) . '#ff_preview';
	}

	/** Return a selected survey and the real Fluent Form field/logic definition. */
	public static function survey_workspace( $survey_id ) {
		global $wpdb;
		$survey = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . RTS_DB::table( 'surveys' ) . ' WHERE id = %d', $survey_id ) );
		if ( ! $survey ) { return array( 'error' => 'NOT_FOUND' ); }

		$questions = array();
		$form = null;
		if ( ! empty( $survey->source_form_id ) && self::fluent_forms_table_exists() ) {
			$form = $wpdb->get_row( $wpdb->prepare( "SELECT id, title, status, form_fields, appearance_settings, created_at, updated_at FROM {$wpdb->prefix}fluentform_forms WHERE id = %d", $survey->source_form_id ) );
			if ( $form ) {
				$definition = json_decode( (string) $form->form_fields, true );
				$step = 1;
				self::flatten_fluent_fields( $definition['fields'] ?? array(), $questions, $step, true );
			}
		}

		// Keep legacy platform-created surveys usable even when they are not linked
		// to Fluent Forms.
		if ( ! $form ) {
			$rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . RTS_DB::table( 'survey_questions' ) . ' WHERE survey_id = %d ORDER BY sort_order, question_number', $survey_id ) );
			foreach ( $rows as $row ) {
				$questions[] = array(
					'name'       => 'question_' . (int) $row->id,
					'label'      => (string) $row->prompt,
					'type'       => (string) $row->question_type,
					'type_label' => ucwords( str_replace( '_', ' ', (string) $row->question_type ) ),
					'required'   => (bool) $row->required,
					'section'    => $row->section ?: 'Survey',
					'conditions' => $row->conditional_on_question_id ? array( array( 'field' => 'question_' . (int) $row->conditional_on_question_id, 'value' => (string) $row->conditional_equals, 'operator' => '=' ) ) : array(),
					'condition_type' => 'all',
					'is_display' => false,
				);
			}
		}

		return array(
			'error'       => null,
			'survey'      => $survey,
			'form'        => $form,
			'questions'   => $questions,
			'editor_url'  => $form ? self::fluent_form_editor_url( $form->id ) : '',
			'design_url'  => $form ? self::fluent_form_design_url( $form->id ) : '',
			'preview_url' => $form ? self::fluent_form_preview_url( $form->id ) : '',
		);
	}

	private static function fluent_conditions( $logic ) {
		$enabled = in_array( $logic['status'] ?? false, array( true, 1, '1', 'true' ), true );
		if ( ! $enabled ) { return array(); }

		$conditions = (array) ( $logic['conditions'] ?? array() );
		$valid = array_values( array_filter( $conditions, fn( $condition ) => ! empty( $condition['field'] ) ) );
		if ( ! $valid ) {
			foreach ( (array) ( $logic['condition_groups'] ?? array() ) as $group ) {
				foreach ( (array) ( $group['rules'] ?? array() ) as $rule ) {
					if ( ! empty( $rule['field'] ) ) { $valid[] = $rule; }
				}
			}
		}

		$unique = array();
		foreach ( $valid as $condition ) {
			$condition = array(
				'field'    => (string) ( $condition['field'] ?? '' ),
				'value'    => (string) ( $condition['value'] ?? '' ),
				'operator' => (string) ( $condition['operator'] ?? '=' ),
			);
			$unique[ md5( wp_json_encode( $condition ) ) ] = $condition;
		}
		return array_values( $unique );
	}

	private static function fluent_display_text( $html ) {
		$text = html_entity_decode( wp_strip_all_tags( (string) $html, true ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		return trim( preg_replace( '/\s+/u', ' ', $text ) );
	}

	private static function flatten_fluent_fields( $fields, &$questions, &$step, $include_display = false ) {
		$type_labels = array(
			'input_radio' => 'Multiple Choice', 'input_checkbox' => 'Checkboxes', 'select' => 'Dropdown',
			'select_country' => 'Country', 'input_number' => 'Number', 'input_text' => 'Short Text',
			'textarea' => 'Open Text', 'input_email' => 'Email', 'input_date' => 'Date', 'rangeslider' => 'Range',
		);

		foreach ( (array) $fields as $field ) {
			if ( ! empty( $field['columns'] ) ) {
				foreach ( $field['columns'] as $column ) {
					// Nested Custom HTML is normally a visual table label or suffix; the
					// associated input field already carries the real accessible label.
					self::flatten_fluent_fields( $column['fields'] ?? array(), $questions, $step, false );
				}
				continue;
			}

			$element = (string) ( $field['element'] ?? '' );
			if ( 'form_step' === $element ) { $step++; continue; }
			$settings = (array) ( $field['settings'] ?? array() );
			$logic = (array) ( $settings['conditional_logics'] ?? array() );
			$conditions = self::fluent_conditions( $logic );

			if ( 'custom_html' === $element ) {
				if ( ! $include_display ) { continue; }
				$display_text = self::fluent_display_text( $settings['html_codes'] ?? '' );
				if ( '' === $display_text ) { continue; }
				$questions[] = array(
					'name'           => (string) ( $field['uniqElKey'] ?? 'display_' . count( $questions ) ),
					'label'          => $display_text,
					'type'           => 'custom_html',
					'type_label'     => 'Information / Prompt',
					'required'       => false,
					'section'        => 'Step ' . $step,
					'conditions'     => $conditions,
					'condition_type' => (string) ( $logic['type'] ?? 'all' ),
					'is_display'     => true,
				);
				continue;
			}
			if ( in_array( $element, array( '', 'button', 'custom_submit_button' ), true ) ) { continue; }

			$name = (string) ( $field['attributes']['name'] ?? '' );
			$label = trim( wp_strip_all_tags( (string) ( $settings['label'] ?? '' ) ) );
			if ( '' === $label ) { $label = trim( wp_strip_all_tags( (string) ( $settings['admin_field_label'] ?? '' ) ) ); }
			if ( '' === $label ) { $label = $name ?: ucwords( str_replace( '_', ' ', $element ) ); }
			$required = $settings['validation_rules']['required']['value'] ?? false;

			$questions[] = array(
				'name'           => $name,
				'label'          => $label,
				'type'           => $element,
				'type_label'     => $type_labels[ $element ] ?? ucwords( str_replace( '_', ' ', $element ) ),
				'required'       => in_array( $required, array( true, 1, '1', 'true' ), true ),
				'section'        => 'Step ' . $step,
				'conditions'     => $conditions,
				'condition_type' => (string) ( $logic['type'] ?? 'all' ),
				'is_display'     => false,
			);
		}
	}

	// ---- Survey Analytics & Statistical Reporting ----

	private static function reporting_range_days( $range ) {
		return in_array( (string) $range, array( '30', '90' ), true ) ? (int) $range : 0;
	}

	public static function survey_reporting( $survey_id, $range = '30', $question_id = '', $audience = 'all' ) {
		global $wpdb;
		$survey = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . RTS_DB::table( 'surveys' ) . ' WHERE id = %d', $survey_id ) );
		if ( ! $survey ) { return array( 'error' => 'NOT_FOUND' ); }

		$form_id = (int) $survey->source_form_id;
		$days = self::reporting_range_days( $range );
		$tracking = RTS_DB::table( 'survey_tracking' );
		$answers = RTS_DB::table( 'survey_answers' );
		$tracking_date = $days ? $wpdb->prepare( ' AND started_at >= DATE_SUB(NOW(), INTERVAL %d DAY)', $days ) : '';
		$answer_date = $days ? $wpdb->prepare( ' AND a.answered_at >= DATE_SUB(NOW(), INTERVAL %d DAY)', $days ) : '';

		$stats = (object) array( 'started' => 0, 'step5' => 0, 'step10' => 0, 'completed' => 0, 'avg_seconds' => null );
		$daily_times = array(); $time_values = array(); $questions = array(); $breakdown = array();
		if ( $form_id ) {
			$stats = $wpdb->get_row( $wpdb->prepare(
				"SELECT COUNT(*) AS started,
				 SUM(current_step >= 5 OR completion_status='completed' OR completed_at IS NOT NULL) AS step5,
				 SUM(current_step >= 10 OR completion_status='completed' OR completed_at IS NOT NULL) AS step10,
				 SUM(completion_status='completed' OR completed_at IS NOT NULL) AS completed,
				 AVG(CASE WHEN completion_status='completed' OR completed_at IS NOT NULL THEN NULLIF(time_spent_seconds,0) END) AS avg_seconds
				 FROM $tracking WHERE form_id=%d$tracking_date",
				$form_id
			) );
			$daily_times = $wpdb->get_results( $wpdb->prepare(
				"SELECT DATE(completed_at) AS label, ROUND(AVG(NULLIF(time_spent_seconds,0)),1) AS value
				 FROM $tracking WHERE form_id=%d AND completed_at IS NOT NULL$tracking_date
				 GROUP BY DATE(completed_at) ORDER BY label ASC",
				$form_id
			) );
			$time_values = array_map( 'intval', $wpdb->get_col( $wpdb->prepare(
				"SELECT time_spent_seconds FROM $tracking WHERE form_id=%d AND time_spent_seconds > 0 AND (completion_status='completed' OR completed_at IS NOT NULL)$tracking_date ORDER BY time_spent_seconds",
				$form_id
			) ) );
			$questions = $wpdb->get_results( $wpdb->prepare(
				"SELECT REPLACE(question_id,'[]','') AS question_id, MAX(question_label) AS label, MAX(question_type) AS question_type, COUNT(DISTINCT tracking_id) AS respondents
				 FROM $answers a WHERE form_id=%d$answer_date AND question_label IS NOT NULL AND question_label != ''
				 GROUP BY REPLACE(question_id,'[]','') ORDER BY MIN(id)",
				$form_id
			) );
			$questions = array_values( array_filter( $questions, fn( $question ) => ! preg_match( '/_description$/', (string) $question->question_id ) && 0 !== stripos( trim( (string) $question->label ), 'Comments' ) ) );

			// Keep the analysis selector in the exact order of the current Fluent
			// Form, rather than the order in which historical answer rows arrived.
			$workspace = self::survey_workspace( $survey_id );
			$field_order = array(); $current_labels = array(); $position = 0;
			foreach ( (array) ( $workspace['questions'] ?? array() ) as $workspace_question ) {
				if ( ! empty( $workspace_question['is_display'] ) || empty( $workspace_question['name'] ) ) { continue; }
				$field_name = preg_replace( '/\[\]$/', '', (string) $workspace_question['name'] );
				$field_order[ $field_name ] = $position++;
				$current_labels[ $field_name ] = (string) $workspace_question['label'];
			}
			foreach ( $questions as $question ) {
				if ( isset( $current_labels[ $question->question_id ] ) ) { $question->label = $current_labels[ $question->question_id ]; }
			}
			usort( $questions, function ( $left, $right ) use ( $field_order ) {
				$left_order = $field_order[ $left->question_id ] ?? PHP_INT_MAX;
				$right_order = $field_order[ $right->question_id ] ?? PHP_INT_MAX;
				if ( $left_order !== $right_order ) { return $left_order <=> $right_order; }
				return strnatcasecmp( (string) $left->label, (string) $right->label );
			} );
		}

		$valid_question_ids = array_map( fn( $question ) => (string) $question->question_id, $questions );
		if ( ! in_array( $question_id, $valid_question_ids, true ) ) { $question_id = $valid_question_ids[0] ?? ''; }
		if ( ! in_array( $audience, array( 'all', 'runner', 'non_runner' ), true ) ) { $audience = 'all'; }
		if ( $form_id && $question_id ) {
			$audience_sql = '';
			if ( 'runner' === $audience ) {
				$audience_sql = " AND EXISTS (SELECT 1 FROM $answers segment WHERE segment.tracking_id=a.tracking_id AND REPLACE(segment.question_id,'[]','')='s1_radio' AND COALESCE(NULLIF(segment.answer_label,''),segment.answer_value)='I currently run or run/walk for exercise or events')";
			} elseif ( 'non_runner' === $audience ) {
				$audience_sql = " AND EXISTS (SELECT 1 FROM $answers segment WHERE segment.tracking_id=a.tracking_id AND REPLACE(segment.question_id,'[]','')='s1_radio' AND COALESCE(NULLIF(segment.answer_label,''),segment.answer_value)!='I currently run or run/walk for exercise or events')";
			}
			$breakdown = $wpdb->get_results( $wpdb->prepare(
				"SELECT COALESCE(NULLIF(answer_label,''),NULLIF(answer_value,''),'No answer') AS answer, COUNT(*) AS answer_count
				 FROM $answers a WHERE form_id=%d AND REPLACE(question_id,'[]','')=%s$answer_date$audience_sql
				 GROUP BY COALESCE(NULLIF(answer_label,''),NULLIF(answer_value,''),'No answer') ORDER BY answer_count DESC LIMIT 20",
				$form_id, $question_id
			) );
		}

		$median = null; $time_count = count( $time_values );
		if ( $time_count ) {
			$middle = intdiv( $time_count, 2 );
			$median = $time_count % 2 ? $time_values[ $middle ] : (int) round( ( $time_values[ $middle - 1 ] + $time_values[ $middle ] ) / 2 );
		}
		$breakdown_total = array_sum( array_map( fn( $row ) => (int) $row->answer_count, $breakdown ) );

		return array(
			'error'           => null,
			'survey'          => $survey,
			'form_id'         => $form_id,
			'range'           => $days ? (string) $days : 'all',
			'audience'        => $audience,
			'stats'           => array(
				'started' => (int) ( $stats->started ?? 0 ), 'step5' => (int) ( $stats->step5 ?? 0 ),
				'step10' => (int) ( $stats->step10 ?? 0 ), 'completed' => (int) ( $stats->completed ?? 0 ),
				'completion_rate' => ! empty( $stats->started ) ? round( (int) $stats->completed / (int) $stats->started * 100, 1 ) : 0,
				'avg_seconds' => is_null( $stats->avg_seconds ?? null ) ? null : (int) round( $stats->avg_seconds ), 'median_seconds' => $median,
			),
			'daily_times'     => $daily_times,
			'questions'       => $questions,
			'question_id'     => $question_id,
			'breakdown'       => $breakdown,
			'breakdown_total' => $breakdown_total,
		);
	}

	public static function survey_reporting_snapshot() {
		global $wpdb;
		$tracking = RTS_DB::table( 'survey_tracking' ); $participants = RTS_DB::table( 'participants' );
		$stats = $wpdb->get_row( "SELECT COUNT(*) AS total,
			SUM(email IS NOT NULL AND email != '') AS registered,
			SUM(completion_status='completed' OR completed_at IS NOT NULL) AS completed,
			SUM((completion_status='completed' OR completed_at IS NOT NULL) AND completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS completed_30,
			SUM((completion_status='completed' OR completed_at IS NOT NULL) AND completed_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND completed_at < DATE_SUB(NOW(), INTERVAL 30 DAY)) AS completed_previous_30
			FROM $tracking" );
		$verified = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $tracking t JOIN $participants p ON LOWER(p.email)=LOWER(t.email) WHERE t.email IS NOT NULL AND t.email != '' AND p.email_verified=1" );
		$total = (int) ( $stats->total ?? 0 ); $registered = (int) ( $stats->registered ?? 0 ); $completed = (int) ( $stats->completed ?? 0 );
		$current = (int) ( $stats->completed_30 ?? 0 ); $previous = (int) ( $stats->completed_previous_30 ?? 0 );
		return array(
			'total' => $total, 'registered' => $registered, 'anonymous' => max( 0, $total - $registered ),
			'verified' => $verified, 'unverified' => max( 0, $registered - $verified ), 'completed' => $completed,
			'completion_rate' => $total ? round( $completed / $total * 100, 1 ) : 0,
			'trend' => $previous ? round( ( $current - $previous ) / $previous * 100, 1 ) : null,
		);
	}

	// ---- Participant Profile actions ----

	public static function update_participant( $id, $fields, $admin = null ) {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' );
		$current = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $pt WHERE id = %d", $id ) );
		if ( ! $current ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( ! empty( $current->merged_into_participant_id ) ) { return array( 'error' => 'PARTICIPANT_ALREADY_MERGED' ); }

		$allowed = array( 'email','name','runner_status','marketing_source','country','province','city','gender','age_range','travel_party_size','household_income_bracket','account_status' );
		$data = array_intersect_key( (array) $fields, array_flip( $allowed ) );
		if ( isset( $data['email'] ) ) {
			$data['email'] = sanitize_email( $data['email'] );
			if ( ! is_email( $data['email'] ) ) { return array( 'error' => 'INVALID_EMAIL' ); }
			$duplicate_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $pt WHERE email = %s AND id <> %d LIMIT 1", $data['email'], $id ) );
			if ( $duplicate_id ) { return array( 'error' => 'EMAIL_ALREADY_IN_USE' ); }
		}
		if ( isset( $data['runner_status'] ) && ! in_array( $data['runner_status'], array( 'runner', 'non_runner', 'not_specified' ), true ) ) { return array( 'error' => 'INVALID_RUNNER_STATUS' ); }
		if ( isset( $data['account_status'] ) && ! in_array( $data['account_status'], array( 'active', 'suspended' ), true ) ) { return array( 'error' => 'INVALID_ACCOUNT_STATUS' ); }
		if ( isset( $data['marketing_source'] ) ) { $data['marketing_source'] = sanitize_text_field( $data['marketing_source'] ); }

		$changes = array();
		foreach ( $data as $field => $value ) {
			$old = (string) ( $current->$field ?? '' );
			if ( $old !== (string) $value ) { $changes[ $field ] = array( $old, (string) $value ); }
		}
		if ( ! $changes ) { return array( 'error' => null, 'changed' => array() ); }

		if ( isset( $changes['email'] ) ) {
			$user = $current->user_id ? get_user_by( 'id', (int) $current->user_id ) : get_user_by( 'email', $current->email );
			$email_owner = email_exists( $data['email'] );
			if ( $email_owner && ( ! $user || (int) $email_owner !== (int) $user->ID ) ) { return array( 'error' => 'WORDPRESS_EMAIL_ALREADY_IN_USE' ); }
			if ( $user ) {
				$updated_user = wp_update_user( array( 'ID' => $user->ID, 'user_email' => $data['email'] ) );
				if ( is_wp_error( $updated_user ) ) { return array( 'error' => $updated_user->get_error_message() ); }
			}
		}

		$write = array();
		foreach ( array_keys( $changes ) as $field ) { $write[ $field ] = $data[ $field ]; }
		$write['updated_at'] = current_time( 'mysql' );
		if ( false === $wpdb->update( $pt, $write, array( 'id' => $id ) ) ) { return array( 'error' => $wpdb->last_error ?: 'UPDATE_FAILED' ); }
		if ( isset( $changes['email'] ) ) {
			$wpdb->update( RTS_DB::table( 'referrals' ), array( 'referred_email' => $data['email'] ), array( 'referred_participant_id' => $id ) );
			$wpdb->update( RTS_DB::table( 'survey_tracking' ), array( 'email' => $data['email'] ), array( 'id' => (int) $current->survey_tracking_id ) );
		}
		$labels = array( 'email' => 'Email', 'runner_status' => 'Runner Status', 'marketing_source' => 'Marketing Source', 'account_status' => 'Account Status' );
		$details = array();
		foreach ( $changes as $field => $values ) {
			$label = $labels[ $field ] ?? ucwords( str_replace( '_', ' ', $field ) );
			$details[] = $label . ': ' . ( '' === $values[0] ? '(empty)' : $values[0] ) . ' -> ' . ( '' === $values[1] ? '(empty)' : $values[1] );
		}
		RTS_Business_Logic::log_audit( $admin ?: 'admin', 'Participant information edited', 'Participants', 'success', "participant_id=$id; " . implode( '; ', $details ) );
		return array( 'error' => null, 'changed' => array_keys( $changes ) );
	}

	public static function set_account_status( $id, $status, $admin, $reason = '' ) {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' );
		if ( ! in_array( $status, array( 'active', 'suspended' ), true ) ) { return array( 'error' => 'INVALID_ACCOUNT_STATUS' ); }
		$participant = $wpdb->get_row( $wpdb->prepare( "SELECT id, merged_into_participant_id FROM $pt WHERE id = %d", $id ) );
		if ( ! $participant ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( ! empty( $participant->merged_into_participant_id ) ) { return array( 'error' => 'PARTICIPANT_ALREADY_MERGED' ); }
		if ( false === $wpdb->update( $pt, array( 'account_status' => $status, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ) ) ) { return array( 'error' => $wpdb->last_error ?: 'UPDATE_FAILED' ); }
		RTS_Business_Logic::log_audit( $admin ?: 'admin', "Participant $status", 'Participants', 'success', "participant_id=$id; reason=$reason" );
		return array( 'error' => null );
	}

	// Preview by default; commits ONLY when $commit === true. Highest-risk action on the platform.
	public static function merge_duplicates( $keep_id, $merge_id, $commit = false, $field_sources = array(), $admin = null ) {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' ); $rt = RTS_DB::table( 'referrals' );
		$ut = RTS_DB::table( 'trophy_unlocks' ); $earned = RTS_DB::table( 'user_trophies' ); $ct = RTS_DB::table( 'cabin_credits' );
		$survey_links = RTS_DB::table( 'participant_survey_links' );
		if ( (int) $keep_id === (int) $merge_id ) { return array( 'error' => 'CANNOT_MERGE_SAME_RECORD' ); }
		$keep  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $pt WHERE id = %d", $keep_id ) );
		$merge = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $pt WHERE id = %d", $merge_id ) );
		if ( ! $keep || ! $merge ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( $keep->merged_into_participant_id || $merge->merged_into_participant_id ) { return array( 'error' => 'PARTICIPANT_ALREADY_MERGED' ); }
		$merged_account_user = $merge->user_id ? get_user_by( 'id', (int) $merge->user_id ) : get_user_by( 'email', $merge->email );

		$merge_refs    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $rt WHERE referring_participant_id = %d", $merge_id ) );
		$merge_trophies= $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $ut WHERE participant_id = %d", $merge_id ) );
		$merge_earned  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $earned WHERE participant_id = %d", $merge_id ) );
		$merge_credit  = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ct WHERE participant_id = %d", $merge_id ) );
		$keep_credit   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $ct WHERE participant_id = %d", $keep_id ) );
		$merge_fields = array( 'email','name','first_name','last_name','phone','runner_status','marketing_source','country','province','city','gender','age_range','travel_party_size' );

		$preview = array(
			'referrals_to_reassign' => count( $merge_refs ),
			'trophies_to_merge'     => array_map( fn( $t ) => (string) $t->trophy_key, $merge_earned ),
			'credit_conflict'       => (bool) ( $merge_credit && $keep_credit ),
			'keep'                  => $keep,
			'merge'                 => $merge,
			'merge_fields'          => $merge_fields,
		);
		if ( ! $commit ) { return array( 'error' => null, 'preview' => $preview, 'dry_run' => true ); }

		$selected = array();
		foreach ( $merge_fields as $field ) {
			if ( 'merge' === ( $field_sources[ $field ] ?? 'keep' ) ) { $selected[ $field ] = $merge->$field; }
		}
		if ( isset( $selected['email'] ) && ! is_email( $selected['email'] ) ) { return array( 'error' => 'INVALID_SURVIVING_EMAIL' ); }
		$wpdb->query( 'START TRANSACTION' );
		$failed = false;
		if ( isset( $selected['email'] ) && 0 !== strcasecmp( $keep->email, $selected['email'] ) ) {
			$placeholder = 'merged-' . (int) $merge_id . '-' . time() . '@invalid.example';
			$merge_user = $merge->user_id ? get_user_by( 'id', (int) $merge->user_id ) : get_user_by( 'email', $merge->email );
			$keep_user = $keep->user_id ? get_user_by( 'id', (int) $keep->user_id ) : get_user_by( 'email', $keep->email );
			if ( $merge_user && ( ! $keep_user || (int) $merge_user->ID !== (int) $keep_user->ID ) ) {
				$result = wp_update_user( array( 'ID' => $merge_user->ID, 'user_email' => $placeholder ) );
				$failed = is_wp_error( $result );
			}
			if ( ! $failed ) { $failed = false === $wpdb->update( $pt, array( 'email' => $placeholder ), array( 'id' => $merge_id ) ); }
			if ( ! $failed && $keep_user ) {
				$result = wp_update_user( array( 'ID' => $keep_user->ID, 'user_email' => $selected['email'] ) );
				$failed = is_wp_error( $result );
			}
		}
		if ( ! $failed && $selected ) { $failed = false === $wpdb->update( $pt, $selected, array( 'id' => $keep_id ) ); }

		if ( ! $failed && $preview['credit_conflict'] ) {
			$wpdb->update( $ct, array( 'status' => 'cancelled' ), array( 'participant_id' => $merge_id ) ); // one credit per person survives
		} elseif ( ! $failed && $merge_credit && ! $keep_credit ) {
			$wpdb->update( $ct, array( 'participant_id' => $keep_id ), array( 'participant_id' => $merge_id ) );
		}
		if ( ! $failed ) {
			$wpdb->update( $rt, array( 'referring_participant_id' => $keep_id, 'referrer_id' => $keep_id ), array( 'referring_participant_id' => $merge_id ) );
			$wpdb->update( $rt, array( 'referred_participant_id' => $keep_id ), array( 'referred_participant_id' => $merge_id ) );
			foreach ( $merge_trophies as $t ) {
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $ut WHERE participant_id = %d AND trophy_id = %d LIMIT 1", $keep_id, $t->trophy_id ) );
				$exists ? $wpdb->delete( $ut, array( 'id' => $t->id ) ) : $wpdb->update( $ut, array( 'participant_id' => $keep_id ), array( 'id' => $t->id ) );
			}
			foreach ( $merge_earned as $t ) {
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $earned WHERE participant_id = %d AND trophy_key = %s LIMIT 1", $keep_id, $t->trophy_key ) );
				$exists ? $wpdb->delete( $earned, array( 'id' => $t->id ) ) : $wpdb->update( $earned, array( 'participant_id' => $keep_id ), array( 'id' => $t->id ) );
			}
			foreach ( array( 'timeline', 'survey_responses', 'participant_notes' ) as $table_suffix ) {
				$wpdb->update( RTS_DB::table( $table_suffix ), array( 'participant_id' => $keep_id ), array( 'participant_id' => $merge_id ) );
			}
			$wpdb->update( $survey_links, array( 'participant_id' => $keep_id ), array( 'participant_id' => $merge_id ) );
			if ( $merge->survey_tracking_id && (int) $merge->survey_tracking_id !== (int) $keep->survey_tracking_id ) {
				$wpdb->query( $wpdb->prepare(
					"INSERT INTO $survey_links (participant_id, tracking_id, linked_by, linked_by_name, linked_at)
					 VALUES (%d, %d, %d, %s, %s)
					 ON DUPLICATE KEY UPDATE participant_id = VALUES(participant_id), linked_by = VALUES(linked_by), linked_by_name = VALUES(linked_by_name), linked_at = VALUES(linked_at)",
					$keep_id, $merge->survey_tracking_id, get_current_user_id(), $admin ?: 'admin', current_time( 'mysql' )
				) );
			}
			$wpdb->update( $pt, array( 'account_status' => 'suspended', 'merged_into_participant_id' => $keep_id, 'merged_at' => current_time( 'mysql' ), 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $merge_id ) );
		}
		if ( $failed || $wpdb->last_error ) {
			$wpdb->query( 'ROLLBACK' );
			return array( 'error' => $wpdb->last_error ?: 'MERGE_FAILED' );
		}
		$wpdb->query( 'COMMIT' );
		if ( $merged_account_user ) { WP_Session_Tokens::get_instance( $merged_account_user->ID )->destroy_all(); }
		RTS_Business_Logic::log_audit( $admin ?: 'admin', "Participants merged: kept $keep_id, merged $merge_id", 'Participants', 'success',
			"participant_id=$keep_id; merged_participant_id=$merge_id; referrals_reassigned={$preview['referrals_to_reassign']}; trophies_merged=" . count( $preview['trophies_to_merge'] ) . "; selected_duplicate_fields=" . implode( ',', array_keys( $selected ) ) . "; credit_conflict=" . (int) $preview['credit_conflict'] );
		return array( 'error' => null, 'preview' => $preview, 'committed' => true );
	}

	/** Attach a completed survey-only record to a registered participant. */
	public static function merge_survey_record( $keep_id, $tracking_id, $commit = false, $admin = null ) {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' );
		$tracking_table = RTS_DB::table( 'survey_tracking' );
		$links_table = RTS_DB::table( 'participant_survey_links' );
		$responses_table = RTS_DB::table( 'survey_responses' );
		$answers_table = RTS_DB::table( 'survey_answers' );
		$keep = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $pt WHERE id = %d AND merged_into_participant_id IS NULL", $keep_id ) );
		$source = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tracking_table WHERE id = %d", $tracking_id ) );
		if ( ! $keep || ! $source ) { return array( 'error' => 'NOT_FOUND' ); }
		$owner = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $pt WHERE survey_tracking_id = %d AND merged_into_participant_id IS NULL LIMIT 1", $tracking_id ) );
		if ( $owner ) { return array( 'error' => 'SURVEY_RECORD_HAS_REGISTERED_PARTICIPANT' ); }
		$linked_owner = (int) $wpdb->get_var( $wpdb->prepare( "SELECT participant_id FROM $links_table WHERE tracking_id = %d LIMIT 1", $tracking_id ) );
		if ( $linked_owner ) { return array( 'error' => $linked_owner === (int) $keep_id ? 'SURVEY_RECORD_ALREADY_ATTACHED' : 'SURVEY_RECORD_ATTACHED_ELSEWHERE' ); }
		$keep_tracking = $keep->survey_tracking_id ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tracking_table WHERE id = %d", $keep->survey_tracking_id ) ) : null;
		if ( ! $keep_tracking || ! $source->session_id || $source->session_id !== $keep_tracking->session_id || (int) $source->form_id !== (int) $keep_tracking->form_id ) {
			return array( 'error' => 'SURVEY_RECORD_NOT_IN_SAME_SESSION' );
		}
		$answer_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $answers_table WHERE tracking_id = %d", $tracking_id ) );
		$response_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $responses_table WHERE source_tracking_id = %d", $tracking_id ) );
		$preview = array( 'keep' => $keep, 'source_tracking' => $source, 'answer_count' => $answer_count, 'response_count' => $response_count );
		if ( ! $commit ) { return array( 'error' => null, 'preview' => $preview, 'dry_run' => true, 'source_type' => 'tracking' ); }

		$wpdb->query( 'START TRANSACTION' );
		$failed = false;
		$response_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $responses_table WHERE source_tracking_id = %d", $tracking_id ) );
		if ( $response_ids ) {
			$failed = false === $wpdb->update( $responses_table, array( 'participant_id' => $keep_id ), array( 'source_tracking_id' => $tracking_id ) );
		} else {
			$survey_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM " . RTS_DB::table( 'surveys' ) . " WHERE source_form_id = %d LIMIT 1", $source->form_id ) );
			if ( $survey_id ) {
				$failed = false === $wpdb->insert( $responses_table, array(
					'survey_id' => $survey_id, 'participant_id' => $keep_id, 'source_tracking_id' => $tracking_id,
					'source_submission_id' => $source->submission_id, 'session_token' => substr( $source->submission_id, 0, 64 ),
					'status' => 'completed' === $source->completion_status ? 'completed' : $source->completion_status,
					'started_at' => $source->started_at, 'completed_at' => $source->completed_at,
				) );
				if ( ! $failed ) {
					$response_ids = array( (int) $wpdb->insert_id );
					$wpdb->update( $answers_table, array( 'response_id' => (int) $wpdb->insert_id ), array( 'tracking_id' => $tracking_id ) );
				}
			}
		}
		if ( ! $failed ) {
			$failed = false === $wpdb->insert( $links_table, array(
				'participant_id' => $keep_id, 'tracking_id' => $tracking_id, 'linked_by' => get_current_user_id(),
				'linked_by_name' => $admin ?: 'admin', 'linked_at' => current_time( 'mysql' ),
			) );
		}
		if ( $failed ) {
			$error = $wpdb->last_error ?: 'SURVEY_RECORD_MERGE_FAILED';
			$wpdb->query( 'ROLLBACK' );
			return array( 'error' => $error );
		}
		$wpdb->query( 'COMMIT' );
		RTS_Business_Logic::log_audit( $admin ?: 'admin', 'Survey-only record merged into participant', 'Participants', 'success',
			"participant_id=$keep_id; tracking_id=$tracking_id; session_id={$source->session_id}; answers=$answer_count" );
		return array( 'error' => null, 'preview' => $preview, 'committed' => true, 'source_type' => 'tracking' );
	}

	// ---- Email Verification Queue ----

	public static function get_verification_queue() {
		global $wpdb;
		$pt = RTS_DB::table( 'participants' );
		$pending = $wpdb->get_results( "SELECT id, name, email, verification_sent_at, founding_runner_number FROM $pt WHERE email_verified = 0 ORDER BY verification_sent_at ASC" );
		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $pt" );
		$verified = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $pt WHERE email_verified = 1" );
		return array( 'pending' => $pending, 'pending_count' => count( $pending ), 'verification_rate' => $total ? round( $verified / $total * 100, 1 ) : 0 );
	}

	// Requires a reason, then delegates to the SAME verify_email() as the real link — so manual
	// verification triggers identical side effects (credit, trophy, referral completion).
	public static function manually_verify( $id, $admin, $reason ) {
		global $wpdb;
		if ( ! $reason ) { return array( 'error' => 'REASON_REQUIRED' ); }
		$p = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . RTS_DB::table( 'participants' ) . " WHERE id = %d", $id ) );
		if ( ! $p ) { return array( 'error' => 'NOT_FOUND' ); }
		if ( $p->email_verified ) { return array( 'error' => 'ALREADY_VERIFIED' ); }
		RTS_Business_Logic::log_audit( $admin ?: 'admin', 'Manual verification override', 'Email Verification', 'success', "participant_id=$id; reason=$reason" );
		return RTS_Business_Logic::verify_email( $p->verification_token );
	}

	// ---- Email Templates ----
	public static function email_template_actions() {
		$actions = array(
			'password_reset'             => 'Password Reset',
			'email_verification'         => 'Email Verification',
			'founding_runner_certificate' => 'Founding Runner Certificate',
		);
		return apply_filters( 'rts_email_template_actions', $actions );
	}

	public static function create_template( $d ) {
		global $wpdb;
		if ( empty( $d['name'] ) || empty( $d['subject'] ) ) { return array( 'error' => 'NAME_AND_SUBJECT_REQUIRED' ); }
		if ( ! empty( $d['action_key'] ) && ! isset( self::email_template_actions()[ sanitize_key( $d['action_key'] ) ] ) ) { return array( 'error' => 'INVALID_ACTION' ); }
		$tt = RTS_DB::table( 'email_templates' );
		$wpdb->insert( $tt, array( 'name' => $d['name'], 'category' => $d['category'] ?? 'general', 'subject' => $d['subject'],
			'html_body' => $d['html_body'] ?? '', 'plain_text_body' => $d['plain_text_body'] ?? '', 'status' => 'draft' ) );
		if ( ! $wpdb->insert_id ) { return array( 'error' => 'DATABASE_ERROR' ); }
		$id = $wpdb->insert_id;
		if ( ! empty( $d['action_key'] ) ) {
			$assigned = self::assign_template_action( $id, $d['action_key'], $d['created_by'] ?? 'admin' );
			if ( $assigned['error'] ) { return $assigned + array( 'template_id' => $id ); }
		}
		RTS_Business_Logic::log_audit( $d['created_by'] ?? 'admin', "Email template created: \"{$d['name']}\"", 'Email Templates', 'success', "template_id=$id" );
		return array( 'error' => null, 'template_id' => $id );
	}

	public static function update_template( $id, $d ) {
		global $wpdb;
		$tt = RTS_DB::table( 'email_templates' );
		$t = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tt WHERE id = %d", $id ) );
		if ( ! $t ) { return array( 'error' => 'NOT_FOUND' ); }
		$subject = $d['subject'] ?? $t->subject; $html = $d['html_body'] ?? $t->html_body; $plain = $d['plain_text_body'] ?? $t->plain_text_body;
		$update = array( 'subject' => $subject, 'html_body' => $html, 'plain_text_body' => $plain, 'updated_at' => current_time( 'mysql' ) );
		if ( isset( $d['name'] ) && '' !== $d['name'] ) { $update['name'] = $d['name']; }
		if ( isset( $d['category'] ) && '' !== $d['category'] ) { $update['category'] = $d['category']; }
		$updated = $wpdb->update( $tt, $update, array( 'id' => $id ) );
		if ( false === $updated ) { return array( 'error' => 'DATABASE_ERROR: ' . $wpdb->last_error ); }
		RTS_Business_Logic::log_audit( $d['updated_by'] ?? 'admin', "Email template updated: \"{$t->name}\"", 'Email Templates', 'success', "template_id=$id" );
		return array( 'error' => null );
	}

	/** Assign at most one template to an action; moving a template clears its prior action. */
	public static function assign_template_action( $id, $action_key, $admin = 'admin' ) {
		global $wpdb;
		$tt = RTS_DB::table( 'email_templates' );
		$template = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, action_key FROM $tt WHERE id = %d", $id ) );
		if ( ! $template ) { return array( 'error' => 'NOT_FOUND' ); }

		$action_key = sanitize_key( (string) $action_key );
		$actions = self::email_template_actions();
		if ( '' !== $action_key && ! isset( $actions[ $action_key ] ) ) { return array( 'error' => 'INVALID_ACTION' ); }

		$wpdb->query( 'START TRANSACTION' );
		try {
			if ( '' !== $action_key ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $tt SET action_key = NULL, updated_at = %s WHERE action_key = %s AND id <> %d", current_time( 'mysql' ), $action_key, $id ) );
			}
			$updated = $wpdb->update( $tt, array( 'action_key' => '' === $action_key ? null : $action_key, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s' ), array( '%d' ) );
			if ( false === $updated ) { throw new RuntimeException( $wpdb->last_error ?: 'Assignment failed.' ); }
			$wpdb->query( 'COMMIT' );
		} catch ( Throwable $e ) {
			$wpdb->query( 'ROLLBACK' );
			return array( 'error' => 'DATABASE_ERROR' );
		}

		$label = '' === $action_key ? 'Unassigned' : $actions[ $action_key ];
		RTS_Business_Logic::log_audit( $admin ?: 'admin', "Email template action changed: \"{$template->name}\" → {$label}", 'Email Templates', 'success', "template_id=$id; action=$action_key" );
		return array( 'error' => null, 'action_key' => $action_key );
	}

}
