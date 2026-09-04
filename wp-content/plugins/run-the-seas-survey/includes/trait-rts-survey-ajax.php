<?php

if (!defined('ABSPATH')) {
    exit;
}

trait RTS_Survey_Ajax
{
    public function ajax_track_survey_start()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $form_id = intval($_POST['form_id']);

        if ($this->is_form_excluded($form_id)) {
            error_log('RTS: Form ' . $form_id . ' is excluded from tracking');
            wp_send_json_error('This form is excluded from tracking');
            return;
        }

        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $referral_code = sanitize_text_field($_POST['referral_code'] ?? '');
        $referral_source = sanitize_text_field($_POST['referral_source'] ?? '');
        $cookie_consent = isset($_POST['cookie_consent']) && $_POST['cookie_consent'] === 'accepted';

        if (!empty($session_id)) {
            if ($cookie_consent && !headers_sent()) {
                // This preference is readable by the consent prompt so it is
                // not HttpOnly; tracking identifiers remain HttpOnly.
                setcookie('rts_survey_cookie_consent', 'accepted', time() + (86400 * 30), '/', '', false, false);
                setcookie('rts_session_id', $session_id, time() + (86400 * 30), '/', '', false, true);
            }
            $this->tracking->set_session_id($session_id);
        }

        $result = $this->tracking->start_tracking($form_id, $referral_code, $referral_source, $cookie_consent);

        if ($result) {
            wp_send_json_success(array(
                'tracking_id' => $result['tracking_id'],
                'submission_id' => $result['submission_id']
            ));
        } else {
            wp_send_json_error('Failed to start tracking');
        }
    }

    public function is_form_excluded($form_id)
    {
        if ($this->tracking) {
            return $this->tracking->is_form_excluded($form_id);
        }

        // Fallback logic
        $settings = get_option('rts_survey_settings', array());
        $form_settings = $settings[$form_id] ?? array(
            'excluded' => 0
        );
        return isset($form_settings['excluded']) && $form_settings['excluded'] == 1;
    }


    public function ajax_track_question_answer()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $form_id = intval($_POST['form_id']);
        $question_id = sanitize_text_field($_POST['question_id']);
        $answer = sanitize_text_field($_POST['answer']);
        $question_label = sanitize_text_field($_POST['question_label'] ?? '');
        $question_type = sanitize_text_field($_POST['question_type'] ?? 'text');
        $step = intval($_POST['step'] ?? 0);

        // Get or create tracking ID
        $tracking_id = intval($_POST['tracking_id'] ?? 0);
        if (!$tracking_id) {
            $tracking_id = $this->tracking->start_tracking($form_id);
        }

        // Special handling for email - store full value
        if ($question_id === 'email') {
            $answer = sanitize_email($answer);
        }

        $this->tracking->track_question_answer(
            $tracking_id,
            $form_id,
            $question_id,
            $answer,
            $question_label,
            $question_type,
            $step
        );

        wp_send_json_success(array('tracking_id' => $tracking_id));
    }

    public function ajax_track_abandonment()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $tracking_id = intval($_POST['tracking_id']);
        $step = intval($_POST['step'] ?? 0);

        if ($tracking_id) {
            $this->tracking->track_abandonment($tracking_id, $step);
        }

        wp_send_json_success();
    }

    public function ajax_complete_survey()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $tracking_id = intval($_POST['tracking_id']);
        $form_id = intval($_POST['form_id'] ?? 0);
        $email = sanitize_email($_POST['email'] ?? '');
        $final_step = intval($_POST['final_step'] ?? 0);
        $skip_registration = isset($_POST['skip_registration']) ? intval($_POST['skip_registration']) : 0;

        error_log('RTS Complete Survey - Tracking ID: ' . $tracking_id . ', Email: ' . $email . ', Skip Registration: ' . $skip_registration);

        if ($tracking_id) {
            $result = $this->tracking->complete_survey($tracking_id, $email, $final_step);

            if ($result) {
                // Always use the form stored with this tracking record. The browser
                // also posts it, but the stored value prevents a changed URL/value.
                global $wpdb;
                $tracked_form_id = $wpdb->get_var($wpdb->prepare(
                    "SELECT form_id FROM {$wpdb->prefix}rts_survey_tracking WHERE id = %d",
                    $tracking_id
                ));
                if ($tracked_form_id) {
                    $form_id = absint($tracked_form_id);
                }

                // If user chose to skip registration, store that preference
                if ($skip_registration) {
                    update_option('rts_survey_skipped_registration_' . $tracking_id, array(
                        'tracking_id' => $tracking_id,
                        'email' => $email,
                        'timestamp' => current_time('mysql')
                    ));
                    error_log('RTS: User skipped registration for tracking ID: ' . $tracking_id);
                }

                wp_send_json_success(array(
                    'completed' => true,
                    'tracking_id' => $tracking_id,
                    'email' => $email,
                    'skip_registration' => $skip_registration,
                    'redirect_url' => rts_get_captains_update_page_url($tracking_id, $form_id),
                ));
            } else {
                wp_send_json_error('Failed to complete survey');
            }
        }

        wp_send_json_error('Invalid tracking ID');
    }

    public function ajax_track_step_change()
    {
        // Log the incoming request for debugging
        error_log('RTS Step Change Request: ' . print_r($_POST, true));

        check_ajax_referer('rts_nonce', 'nonce');

        $tracking_id = intval($_POST['tracking_id']);
        $step = intval($_POST['step']);
        $old_step = intval($_POST['old_step'] ?? 0);
        $button_action = sanitize_text_field($_POST['button_action'] ?? 'unknown');

        error_log("RTS Step Change - ID: $tracking_id, Step: $step, Old: $old_step, Button Action: $button_action");

        if ($tracking_id) {
            $result = $this->tracking->track_step_change($tracking_id, $step, $old_step, $button_action);
            if ($result) {
                wp_send_json_success(array(
                    'tracking_id' => $tracking_id,
                    'step' => $step
                ));
            } else {
                wp_send_json_error('Failed to update step');
            }
        }

        wp_send_json_error('Invalid tracking ID');
    }

    // In run-the-seas-survey.php - RunTheSeasPlugin class

    public function ajax_check_survey_status()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rts_nonce')) {
            error_log('RTS: Invalid nonce for status check');
            wp_send_json_error('Invalid nonce');
            return;
        }

        $form_id = intval($_POST['form_id']);
        if (!$form_id) {
            wp_send_json_error('Invalid form ID');
            return;
        }

        // Get settings
        $settings = get_option('rts_survey_settings', array());
        $form_settings = $settings[$form_id] ?? array(
            'active' => 0,
            'excluded' => 0,
            'start_date' => '',
            'end_date' => '',
            'timezone' => 'UTC'
        );

        // Get status - use the same method as admin
        $admin = new RTS_Admin();
        $status = $admin->get_survey_status($form_id, $form_settings);

        $response = array(
            'active' => intval($form_settings['active']),
            'excluded' => intval($form_settings['excluded'] ?? 0),
            'start_date' => $form_settings['start_date'] ?? '',
            'end_date' => $form_settings['end_date'] ?? '',
            'status' => $status,
            'current_time_utc' => current_time('mysql', true),
            'timezone' => wp_timezone_string()
        );

        wp_send_json_success($response);
    }



    /**
     * Get survey status for frontend
     * This is used by the AJAX status check
     */
    private function get_survey_status($form_id, $settings)
    {
        $active = isset($settings['active']) ? intval($settings['active']) : 0;
        $excluded = isset($settings['excluded']) ? intval($settings['excluded']) : 0;
        $start_date = isset($settings['start_date']) ? $settings['start_date'] : '';
        $end_date = isset($settings['end_date']) ? $settings['end_date'] : '';

        $now = current_time('timestamp', true);

        // Check if excluded first
        if ($excluded) {
            return array(
                'class' => 'excluded',
                'label' => __('Excluded', 'run-the-seas'),
                'message' => __('This survey is excluded from tracking.', 'run-the-seas'),
                'excluded' => true
            );
        }

        if (!$active) {
            return array(
                'class' => 'inactive',
                'label' => __('Inactive', 'run-the-seas'),
                'message' => __('Survey is currently deactivated.', 'run-the-seas'),
                'excluded' => false
            );
        }

        if (!empty($start_date)) {
            $start_timestamp = strtotime($start_date);
            if ($start_timestamp && $now < $start_timestamp) {
                return array(
                    'class' => 'upcoming',
                    'label' => __('Scheduled', 'run-the-seas'),
                    'message' => sprintf(
                        __('Survey will start on %s (UTC)', 'run-the-seas'),
                        date_i18n('Y-m-d H:i:s', $start_timestamp)
                    ),
                    'excluded' => false
                );
            }
        }

        if (!empty($end_date)) {
            $end_timestamp = strtotime($end_date);
            if ($end_timestamp && $now > $end_timestamp) {
                return array(
                    'class' => 'ended',
                    'label' => __('Ended', 'run-the-seas'),
                    'message' => sprintf(
                        __('Survey ended on %s (UTC)', 'run-the-seas'),
                        date_i18n('Y-m-d H:i:s', $end_timestamp)
                    ),
                    'excluded' => false
                );
            }
        }

        return array(
            'class' => 'active',
            'label' => __('Active', 'run-the-seas'),
            'message' => __('Survey is currently active and accepting responses.', 'run-the-seas'),
            'excluded' => false
        );
    }

    /**
     * AJAX handler for accurate browser location
     */
    public function ajax_update_location()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $tracking_id = intval($_POST['tracking_id']);
        $form_id = intval($_POST['form_id']);
        $lat = floatval($_POST['lat']);
        $lng = floatval($_POST['lng']);
        $accuracy = floatval($_POST['accuracy']);

        if (!$tracking_id) {
            wp_send_json_error('Invalid tracking ID');
            return;
        }

        // Update the tracking record with accurate location
        global $wpdb;
        $updated = $wpdb->update(
            $wpdb->prefix . 'rts_survey_tracking',
            array(
                'latitude' => $lat,
                'longitude' => $lng,
                'location_accuracy' => $accuracy,
                'location_source' => 'browser',
                'last_activity' => current_time('mysql')
            ),
            array('id' => $tracking_id)
        );

        if ($updated !== false) {
            $this->sync_participant_country_from_tracking($tracking_id);
            // Get submission_id for logging
            $submission_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT submission_id FROM {$wpdb->prefix}rts_survey_tracking WHERE id = %d",
                    $tracking_id
                )
            );

            // Log the location update
            if ($this->tracking) {
                $this->tracking->log_activity(
                    $tracking_id,
                    $submission_id,
                    'location_update',
                    "Accurate location: lat={$lat}, lng={$lng}, accuracy={$accuracy}m"
                );
            }

            wp_send_json_success(array(
                'message' => 'Location updated',
                'lat' => $lat,
                'lng' => $lng,
                'accuracy' => $accuracy
            ));
        } else {
            wp_send_json_error('Failed to update location');
        }
    }

    /**
     * AJAX handler for IP geolocation fallback
     */
    public function ajax_geo_ip_fallback()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $tracking_id = intval($_POST['tracking_id']);
        $form_id = intval($_POST['form_id']);

        if (!$tracking_id) {
            wp_send_json_error('Invalid tracking ID');
            return;
        }

        // Get IP geolocation data
        $geo_data = $this->tracking->get_geo_data();

        global $wpdb;
        $updated = $wpdb->update(
            $wpdb->prefix . 'rts_survey_tracking',
            array(
                'country' => $geo_data['country'],
                'city' => $geo_data['city'],
                'region' => $geo_data['region'],
                'ip_address' => $geo_data['ip'],
                'location_source' => 'ip_fallback',
                'last_activity' => current_time('mysql')
            ),
            array('id' => $tracking_id)
        );

        if ($updated !== false) {
            $this->sync_participant_country_from_tracking($tracking_id);
            // Get submission_id for logging
            $submission_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT submission_id FROM {$wpdb->prefix}rts_survey_tracking WHERE id = %d",
                    $tracking_id
                )
            );

            if ($this->tracking) {
                $this->tracking->log_activity(
                    $tracking_id,
                    $submission_id,
                    'location_fallback',
                    "IP Fallback: country={$geo_data['country']}, city={$geo_data['city']}"
                );
            }

            wp_send_json_success(array(
                'message' => 'IP fallback location updated',
                'data' => $geo_data
            ));
        } else {
            wp_send_json_error('Failed to update fallback location');
        }
    }

    /** Keep the participant's effective country aligned with survey tracking. */
    private function sync_participant_country_from_tracking($tracking_id)
    {
        global $wpdb;

        $participant_id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}rts_participants WHERE survey_tracking_id = %d LIMIT 1",
            absint($tracking_id)
        ));
        if ($participant_id && $this->registration instanceof RTS_Registration) {
            $this->registration->sync_location_from_tracking($participant_id, $tracking_id);
        }
    }



    /**
     * AJAX handler for registration - FIXED with better error handling
     */
    public function ajax_track_share()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $share_action = sanitize_text_field($_POST['share_action'] ?? '');
        $platform = sanitize_text_field($_POST['platform'] ?? '');
        $referral_code = sanitize_text_field($_POST['referral_code'] ?? '');

        error_log("RTS: Share tracked - Action: $share_action, Platform: $platform, Referral: $referral_code");

        // Log to database
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'rts_activity_logs',
            array(
                'tracking_id' => 0,
                'submission_id' => 'share_' . uniqid(),
                'action' => 'share_' . $share_action,
                'description' => "Share: $share_action on $platform - Ref: $referral_code",
                'created_at' => current_time('mysql')
            )
        );

        wp_send_json_success(array(
            'message' => 'Share tracked successfully',
            'action' => $share_action,
            'platform' => $platform
        ));
    }


    /**
     * AJAX handler for tracking review changes
     */
    public function ajax_track_review_changes()
    {
        check_ajax_referer('rts_nonce', 'nonce');

        $tracking_id = intval($_POST['tracking_id']);
        $changes = isset($_POST['changes']) ? $_POST['changes'] : array();

        if (!$tracking_id || empty($changes)) {
            wp_send_json_error('Invalid data');
            return;
        }

        global $wpdb;
        $submission_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT submission_id FROM {$wpdb->prefix}rts_survey_tracking WHERE id = %d",
                $tracking_id
            )
        );

        if ($submission_id && $this->tracking) {
            // Log the review changes
            $this->tracking->log_activity(
                $tracking_id,
                $submission_id,
                'review_changes',
                "Review changes: " . json_encode($changes)
            );

            // Store changes in a meta table or option for later analysis
            $review_data = array(
                'tracking_id' => $tracking_id,
                'submission_id' => $submission_id,
                'changes' => $changes,
                'timestamp' => current_time('mysql')
            );

            // Store in a new table or as an option
            add_option('rts_review_changes_' . $tracking_id, $review_data);

            wp_send_json_success('Review changes logged');
        }

        wp_send_json_error('Failed to log review changes');
    }
}
