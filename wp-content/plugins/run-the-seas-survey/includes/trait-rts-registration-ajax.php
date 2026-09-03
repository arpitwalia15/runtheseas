<?php

if (!defined('ABSPATH')) {
    exit;
}

trait RTS_Registration_Ajax
{
    public function ajax_save_registration()
    {
        // Check nonce
        if (
            !isset($_POST['rts_registration_nonce']) ||
            !wp_verify_nonce($_POST['rts_registration_nonce'], 'rts_registration_nonce')
        ) {
            error_log('RTS: Invalid registration nonce');
            wp_send_json_error('Invalid security token. Please refresh the page and try again.');
            return;
        }

        // This must remain a strict server-side check: client validation can be bypassed.
        $age_consent = isset($_POST['age_consent']) &&
            'true' === sanitize_text_field(wp_unslash($_POST['age_consent']));
        if (!$age_consent) {
            error_log('RTS: Registration rejected because age/legal consent was not confirmed');
            wp_send_json_error('You must confirm your age and agree to the Terms & Conditions and Privacy Policy.');
            return;
        }

        // Check required fields
        $email = sanitize_email($_POST['email'] ?? '');
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name = sanitize_text_field($_POST['last_name'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $city = sanitize_text_field($_POST['city'] ?? '');
        $country = sanitize_text_field($_POST['country'] ?? '');
        $address = sanitize_textarea_field($_POST['address'] ?? '');

        $tracking_id = isset($_POST['tracking_id']) ? intval($_POST['tracking_id']) : 0;
        $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;

        error_log('RTS: Registration attempt - Email: ' . $email . ', Tracking ID: ' . $tracking_id);

        if (empty($email) || empty($first_name) || empty($last_name) || empty($phone)) {
            error_log('RTS: Missing required fields');
            wp_send_json_error('Required fields missing. Please fill in all required fields.');
            return;
        }

        if (!is_email($email)) {
            error_log('RTS: Invalid email: ' . $email);
            wp_send_json_error('Please enter a valid email address.');
            return;
        }

        // A completed survey is linked by its immutable tracking ID
        if ($tracking_id) {
            global $wpdb;
            $tracking = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT id, completion_status FROM {$wpdb->prefix}rts_survey_tracking WHERE id = %d",
                    $tracking_id
                )
            );

            if (!$tracking || $tracking->completion_status !== 'completed') {
                error_log('RTS: Survey not completed for tracking ID: ' . $tracking_id);
                wp_send_json_error('Please complete the survey before claiming your rewards.');
                return;
            }
            error_log('RTS: Survey completed for tracking ID: ' . $tracking_id);
        } else {
            error_log('RTS: No tracking ID provided');
            wp_send_json_error('Please complete the survey before registering.');
            return;
        }

        // Initialize registration
        if (!class_exists('RTS_Registration')) {
            require_once RTS_PLUGIN_PATH . 'includes/class-rts-registration.php';
        }

        $registration = new RTS_Registration($this->tracking);

        // Check if already registered by email
        $existing = $registration->get_participant_by_email($email);
        if ($existing) {
            error_log('RTS: Email already registered: ' . $email . ', participant ID: ' . $existing->id);
            // If user already registered but this survey isn't linked, link it
            if ($tracking_id) {
                global $wpdb;
                $wpdb->update(
                    $wpdb->prefix . 'rts_participants',
                    array('survey_tracking_id' => $tracking_id),
                    array('id' => $existing->id)
                );
                $registration->sync_travel_party_size($existing->id, $tracking_id);
                wp_send_json_success(array(
                    'message' => 'Survey linked to existing account.',
                    'redirect_url' => home_url('/captains-suite'),
                    'is_existing_user' => true,
                ));
                return;
            }
            wp_send_json_error('This email is already registered. Please login or use a different email.');
            return;
        }

        // The registration name is displayed on member and QR-card surfaces,
        // so it must pass the same moderation review as later QR name edits.
        $name_moderator = function_exists('rts_init_buddypress_qr') ? rts_init_buddypress_qr() : null;
        if (!$name_moderator) {
            wp_send_json_error('Name review is temporarily unavailable. Please try again.');
            return;
        }
        foreach (array($first_name, $last_name) as $name_part) {
            $moderation = $name_moderator->moderate_display_name($name_part);
            if (!$moderation['valid']) {
                wp_send_json_error($moderation['message']);
                return;
            }
        }

        // CREATE WORDPRESS USER
        $user_id = $this->create_wordpress_user($email, $first_name, $last_name, $phone, $country, $city);
        if (!$user_id) {
            error_log('RTS: Failed to create WordPress user for: ' . $email);
            wp_send_json_error('Failed to create user account. Please try again.');
            return;
        }
        error_log('RTS: Created WordPress user ID: ' . $user_id . ' for: ' . $email);

        $request_cabin_credit = isset($_POST['request_cabin_credit']) &&
            $_POST['request_cabin_credit'] === 'Yes';

        $_POST['tracking_id'] = $tracking_id;

        // Create participant with user_id
        $participant_id = $registration->create_participant(
            $_POST,
            $request_cabin_credit,
            $user_id,
            $age_consent,
            $registration->get_request_ip_address()
        );

        if (!$participant_id) {
            error_log('RTS: Failed to create participant for: ' . $email);
            wp_send_json_error('Failed to create registration. Please try again.');
            return;
        }
        error_log('RTS: Created participant ID: ' . $participant_id . ' for: ' . $email);

        // Store the email against the completed survey
        if ($tracking_id) {
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'rts_survey_tracking',
                array('email' => $email),
                array('id' => $tracking_id),
                array('%s'),
                array('%d')
            );
            error_log('RTS: Updated tracking record ' . $tracking_id . ' with email: ' . $email);
        }

        // Handle cabin credit if requested
        if ($request_cabin_credit) {
            $registration->handle_cabin_credit_request($participant_id, $_POST);
            error_log('RTS: Cabin credit requested for participant: ' . $participant_id);
        }

        // Get participant data
        $participant = $registration->get_participant($participant_id);

        // ============================================
        // CLEAN REFERRAL URL - NO UTM PARAMETERS
        // ============================================
        $clean_referral_url = home_url('/survey?ref=' . $participant->referral_code);
        $referral_link = $clean_referral_url;

        // Simple sharing links with clean URL (UTM will be added by JavaScript)
        $sharing_links = array(
            'direct' => array(
                'url' => $clean_referral_url,
                'utm_source' => 'direct',
                'utm_medium' => 'direct',
                'utm_campaign' => 'direct_share',
                'icon' => '🔗',
                'color' => '#1a7efb',
                'share_url' => null
            )
        );

        // Passcode creation now starts only after the member proves ownership
        // of this address through the verification link. Do not send a reset-
        // style email to an account that has never chosen a passcode.
        $registration->log_timeline(
            $participant_id,
            'passcode_setup_pending',
            'Passcode creation will begin after email verification'
        );

        // ============================================
        // OPTIMIZATION: Store email data for background sending
        // ============================================
        $email_data = array(
            'participant_id' => $participant_id,
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'participant' => $participant,
            'referral_link' => $clean_referral_url,
            'post_data' => $_POST,
            'tracking_id' => $tracking_id,
            'form_id' => $form_id,
            'user_id' => $user_id
        );

        // Store in database for background processing
        update_option('rts_pending_registration_' . $participant_id, $email_data);
        error_log('RTS: Stored pending registration for participant: ' . $participant_id);

        // Send the verification email
        $verification_sent = $registration->send_verification_email($participant_id);
        error_log('RTS: Verification email sent: ' . ($verification_sent ? 'YES' : 'NO'));

        // ============================================
        // AUTO-LOGIN THE USER
        // ============================================
        wp_logout();

        if ($tracking_id) {
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . 'rts_participants',
                array('survey_tracking_id' => $tracking_id),
                array('id' => $participant_id)
            );
            error_log('RTS: Updated participant ' . $participant_id . ' with survey_tracking_id: ' . $tracking_id);
        }

        $user = get_userdata($user_id);
        if ($user) {
            do_action('wp_login', $user->user_login, $user);
            error_log('RTS: Auto-logged in user: ' . $user->user_login);
        }

        update_user_meta($user_id, 'rts_registration_completed', current_time('mysql'));

        do_action('rts_registration_completed', $participant_id);

        $redirect_url = home_url('/captains-suite');

        $resend_token = wp_generate_password(32, false, false);
        set_transient(
            'rts_registration_resend_' . $resend_token,
            array('participant_id' => $participant_id),
            30 * MINUTE_IN_SECONDS
        );
        $resend_url = add_query_arg(
            'rts_registration_resend',
            rawurlencode($resend_token),
            home_url('/register')
        );

        error_log('RTS: Registration completed successfully for participant: ' . $participant_id);

        // ============================================
        // SEND RESPONSE WITH CLEAN URL
        // ============================================
        wp_send_json_success(array(
            'participant_id' => $participant_id,
            'user_id' => $user_id,
            'email' => $email,
            'cabin_credit_number' => $participant->cabin_credit_number,
            'referral_code' => $participant->referral_code,
            'referral_link' => $clean_referral_url,
            'sharing_links' => $sharing_links,
            'redirect_url' => $redirect_url,
            'resend_url' => $resend_url,
            'message' => 'Registration completed successfully'
        ));
    }

    private function generate_all_sharing_links($referral_code, $form_page_url)
    {
        if (empty($form_page_url)) {
            $form_page_url = home_url('/survey');
        }

        // Parse the base URL
        $url_parts = parse_url($form_page_url);
        $base_url = $url_parts['scheme'] . '://' . $url_parts['host'] . (isset($url_parts['port']) ? ':' . $url_parts['port'] : '') . ($url_parts['path'] ?? '/');

        // Platform-specific configurations
        $platforms = array(
            'facebook' => array(
                'utm_source' => 'facebook',
                'utm_medium' => 'social',
                'utm_campaign' => 'facebook_share',
                'icon' => '📘',
                'color' => '#1877f2',
                'share_url' => 'https://www.facebook.com/sharer/sharer.php?u='
            ),
            'instagram' => array(
                'utm_source' => 'instagram',
                'utm_medium' => 'social',
                'utm_campaign' => 'instagram_share',
                'icon' => '📸',
                'color' => '#e4405f',
                'share_url' => null // Copy link for Instagram
            ),
            'twitter' => array(
                'utm_source' => 'twitter',
                'utm_medium' => 'social',
                'utm_campaign' => 'twitter_share',
                'icon' => '🐦',
                'color' => '#000000',
                'share_url' => 'https://twitter.com/intent/tweet?text='
            ),
            'linkedin' => array(
                'utm_source' => 'linkedin',
                'utm_medium' => 'social',
                'utm_campaign' => 'linkedin_share',
                'icon' => '💼',
                'color' => '#0a66c2',
                'share_url' => 'https://www.linkedin.com/sharing/share-offsite/?url='
            ),
            'reddit' => array(
                'utm_source' => 'reddit',
                'utm_medium' => 'social',
                'utm_campaign' => 'reddit_share',
                'icon' => '🤖',
                'color' => '#ff4500',
                'share_url' => 'https://www.reddit.com/submit?url='
            ),
            'running_club' => array(
                'utm_source' => 'running_club',
                'utm_medium' => 'website',
                'utm_campaign' => 'club_share',
                'icon' => '🏃',
                'color' => '#28a745',
                'share_url' => null // Copy link for website
            ),
            'forum' => array(
                'utm_source' => 'forum',
                'utm_medium' => 'forum',
                'utm_campaign' => 'forum_share',
                'icon' => '💬',
                'color' => '#6c757d',
                'share_url' => null // Copy link for forum
            ),
            'email' => array(
                'utm_source' => 'email',
                'utm_medium' => 'email',
                'utm_campaign' => 'email_share',
                'icon' => '📧',
                'color' => '#6c757d',
                'share_url' => 'mailto:?subject='
            ),
            'sms' => array(
                'utm_source' => 'sms',
                'utm_medium' => 'sms',
                'utm_campaign' => 'sms_share',
                'icon' => '📱',
                'color' => '#25D366',
                'share_url' => null // Copy link for SMS
            ),
            'direct' => array(
                'utm_source' => 'direct',
                'utm_medium' => 'direct',
                'utm_campaign' => 'direct_share',
                'icon' => '🔗',
                'color' => '#1a7efb',
                'share_url' => null // Direct link
            )
        );

        $links = array();
        foreach ($platforms as $platform => $config) {
            // Build the URL with UTM parameters
            $params = array(
                'ref' => $referral_code,
                'utm_source' => $config['utm_source'],
                'utm_medium' => $config['utm_medium'],
                'utm_campaign' => $config['utm_campaign'],
                'utm_content' => $referral_code
            );

            // Preserve existing query parameters
            if (isset($url_parts['query']) && !empty($url_parts['query'])) {
                parse_str($url_parts['query'], $existing_params);
                unset($existing_params['utm_source']);
                unset($existing_params['utm_medium']);
                unset($existing_params['utm_campaign']);
                unset($existing_params['utm_content']);
                unset($existing_params['ref']);
                $params = array_merge($existing_params, $params);
            }

            $query_string = http_build_query($params);
            $full_url = $base_url . '?' . $query_string;

            $links[$platform] = array(
                'url' => $full_url,
                'utm_source' => $config['utm_source'],
                'utm_medium' => $config['utm_medium'],
                'utm_campaign' => $config['utm_campaign'],
                'icon' => $config['icon'],
                'color' => $config['color'],
                'share_url' => $config['share_url'] ? $this->build_share_url($config['share_url'], $full_url, $referral_code) : null
            );
        }

        return $links;
    }

    /**
     * Build platform-specific share URL
     */
    private function build_share_url($share_url_template, $full_url, $referral_code)
    {
        $message = urlencode("Join me as a Founding Runner with Run The Seas! 🏃 Get \$100 credit when you register! Use my referral link: " . $full_url);

        if (strpos($share_url_template, 'facebook') !== false) {
            return $share_url_template . urlencode($full_url) . '&quote=' . urlencode("Join me as a Founding Runner with Run The Seas! 🏃 Get \$100 credit when you register!");
        } elseif (strpos($share_url_template, 'twitter') !== false) {
            return $share_url_template . $message . '&url=' . urlencode($full_url);
        } elseif (strpos($share_url_template, 'linkedin') !== false) {
            return $share_url_template . urlencode($full_url);
        } elseif (strpos($share_url_template, 'reddit') !== false) {
            return $share_url_template . urlencode($full_url) . '&title=' . urlencode("Join me as a Founding Runner with Run The Seas! 🏃");
        } elseif (strpos($share_url_template, 'mailto') !== false) {
            $subject = urlencode("Join me as a Founding Runner with Run The Seas!");
            $body = urlencode("I've just registered as a Founding Runner with Run The Seas! 🏃\n\nUse my referral link to join and get \$100 credit:\n\n" . $full_url . "\n\nJoin me on this exciting journey! 🏆");
            return $share_url_template . $subject . '&body=' . $body;
        }

        return $share_url_template . urlencode($full_url);
    }
    

    /**
     * Build platform-specific URL with custom UTM parameters
     */
    private function build_platform_url($referral_code, $form_page_url, $utm_source, $utm_medium, $utm_campaign)
    {
        if (empty($form_page_url)) {
            $form_page_url = home_url('/survey');
        }

        // Parse the form page URL
        $url_parts = parse_url($form_page_url);
        $scheme = isset($url_parts['scheme']) ? $url_parts['scheme'] : 'http';
        $host = isset($url_parts['host']) ? $url_parts['host'] : $_SERVER['HTTP_HOST'];
        $port = isset($url_parts['port']) ? ':' . $url_parts['port'] : '';
        $path = isset($url_parts['path']) ? $url_parts['path'] : '/';

        // Build the base URL
        $base_url = $scheme . '://' . $host . $port . $path;

        // Add query parameters with platform-specific UTM
        $params = array(
            'ref' => $referral_code,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'utm_content' => $referral_code
        );

        // Preserve existing query parameters
        if (isset($url_parts['query']) && !empty($url_parts['query'])) {
            parse_str($url_parts['query'], $existing_params);
            // Remove any existing UTM params to avoid duplication
            unset($existing_params['utm_source']);
            unset($existing_params['utm_medium']);
            unset($existing_params['utm_campaign']);
            unset($existing_params['utm_content']);
            unset($existing_params['ref']);
            $params = array_merge($existing_params, $params);
        }

        $query_string = http_build_query($params);

        // Build the full URL
        $referral_url = $base_url . '?' . $query_string;

        return $referral_url;
    }    

    /**
     * Build the full referral URL with proper parameters
     */
    private function build_referral_url($referral_code, $form_page_url)
    {
        if (empty($form_page_url) || $form_page_url == home_url('/survey')) {
            // Try to get the current page URL
            $form_page_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .
                $_SERVER['HTTP_HOST'] .
                $_SERVER['REQUEST_URI'];
        }

        // Parse the form page URL
        $url_parts = parse_url($form_page_url);
        $scheme = isset($url_parts['scheme']) ? $url_parts['scheme'] : 'http';
        $host = isset($url_parts['host']) ? $url_parts['host'] : $_SERVER['HTTP_HOST'];
        $port = isset($url_parts['port']) ? ':' . $url_parts['port'] : '';
        $path = isset($url_parts['path']) ? $url_parts['path'] : '/';

        // Build the base URL
        $base_url = $scheme . '://' . $host . $port . $path;

        // Add query parameters
        $params = array(
            'ref' => $referral_code,
            'utm_source' => 'referral',
            'utm_medium' => 'share',
            'utm_campaign' => 'founding_runner'
        );

        // Preserve existing query parameters
        if (isset($url_parts['query']) && !empty($url_parts['query'])) {
            parse_str($url_parts['query'], $existing_params);
            $params = array_merge($existing_params, $params);
        }

        $query_string = http_build_query($params);

        // Build the full URL
        $referral_url = $base_url . '?' . $query_string;

        error_log('RTS: Generated referral URL: ' . $referral_url);

        return $referral_url;
    }

    /**
     * Create WordPress user
     */
    private function create_wordpress_user($email, $first_name, $last_name, $phone = '', $country = '', $city = '')
    {
        // Check if user already exists
        $user_id = email_exists($email);
        if ($user_id) {
            return $user_id;
        }

        // Generate a random password
        $password = wp_generate_password(12, true);

        // Create the account while suppressing BuddyNext's separate generic
        // welcome email. The member creates their own passcode immediately
        // after proving ownership of their email address.
        $GLOBALS['rts_creating_member_account'] = true;
        try {
            $user_id = wp_create_user($email, $password, $email);
        } finally {
            unset($GLOBALS['rts_creating_member_account']);
        }

        if (is_wp_error($user_id)) {
            error_log('RTS: Failed to create WordPress user: ' . $user_id->get_error_message());
            return false;
        }

        // Update user meta
        wp_update_user(array(
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => $first_name . ' ' . $last_name
        ));

        // Keep the BuddyPress profile name in sync with this custom registration.
        if (function_exists('xprofile_set_field_data')) {
            xprofile_set_field_data('Name', $user_id, trim($first_name . ' ' . $last_name));
        }

        // Add user role (default subscriber)
        $user = new WP_User($user_id);
        $user->set_role('subscriber');

        // Set user meta
        update_user_meta($user_id, 'rts_registration_date', current_time('mysql'));
        update_user_meta($user_id, 'rts_phone', $phone ?? '');
        update_user_meta($user_id, 'rts_country', $country ?? '');
        update_user_meta($user_id, 'rts_city', $city ?? '');

        error_log('RTS: Created WordPress user ID: ' . $user_id . ' with email: ' . $email);
        // Keep a marker only; never store a plaintext password in user meta.
        update_user_meta($user_id, 'rts_temp_password', '1');

        // Send welcome email with password
        //wp_new_user_notification($user_id, null, 'both');

        return $user_id;
    }
    
   
    public function send_welcome_email_with_reset_link($user_id, $email, $first_name, $last_name)
    {
        // Deprecated: first-time members create their passcode directly after
        // email verification. Never send a password-reset email at registration,
        // including from legacy cron jobs or third-party callers.
        unset($user_id, $email, $first_name, $last_name);
        return false;

        /*
        * Get the WordPress user.
        */
        $user = get_user_by('ID', $user_id);

        if (!$user) {
            error_log('RTS: User not found for password reset: ' . $user_id);
            return false;
        }

        /*
         * One password-reset key is valid at a time. Reserve this initial send
         * before generating the key so overlapping registration processors do
         * not invalidate each other's emailed link.
         */
        if (!add_user_meta($user_id, 'rts_initial_password_reset_requested', current_time('mysql'), true)) {
            error_log('RTS: Initial password-reset email already requested for user ' . $user_id);
            return true;
        }

        /* Generate a one-time WordPress reset key for BuddyNext. */
        $password_reset_key = get_password_reset_key($user);

        if (is_wp_error($password_reset_key)) {
            delete_user_meta($user_id, 'rts_initial_password_reset_requested');
            error_log(
                'RTS: Failed to generate password reset key: ' .
                $password_reset_key->get_error_message()
            );

            return false;
        }

        $reset_page_url = function_exists('rts_get_member_password_reset_url')
            ? rts_get_member_password_reset_url($password_reset_key, $user->user_login)
            : network_site_url(
                'wp-login.php?action=rp&key=' . rawurlencode($password_reset_key) .
                '&login=' . rawurlencode($user->user_login),
                'login'
            );

        /*
        * Subject.
        */
        $subject = 'Welcome to Run The Seas - Set Your Password';

        /*
        * Login page.
        */
        $login_url = function_exists('rts_get_member_login_url')
            ? rts_get_member_login_url()
            : home_url('/login/');

        /* BuddyNext profile, with a safe fallback while its routes load. */
        $account_url = (
            class_exists('\\BuddyNext\\Core\\PageRouter')
            && method_exists('\\BuddyNext\\Core\\PageRouter', 'edit_profile_url')
        )
            ? \BuddyNext\Core\PageRouter::edit_profile_url($user_id)
            : home_url('/captains-suite/');

        /*
        * Build email.
        */
        $message = "<html><body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>";

        /*
        * Header.
        */
        $message .= "<div style='text-align: center; padding: 30px; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 12px; color: #fff;'>";

        $message .= "<h1 style='font-size: 28px; margin: 0;'>🏆 Welcome to Run The Seas!</h1>";

        $message .= "<p style='font-size: 18px; opacity: 0.9;'>Your Founding Runner Account Has Been Created</p>";

        $message .= "</div>";

        /*
        * Main content.
        */
        $message .= "<div style='background: #f8f9fa; border-radius: 12px; padding: 25px; margin: 20px 0; border: 2px solid #1a7efb;'>";

        $message .= "<h2 style='color: #1a7efb; margin-top: 0; text-align: center;'>🎫 Welcome, " .
            esc_html($first_name) .
            " " .
            esc_html($last_name) .
            "!</h2>";

        /*
        * Account created message.
        */
        $message .= "<div style='background: #fff; border-radius: 8px; padding: 20px; margin: 15px 0; border: 1px solid #dee2e6;'>";

        $message .= "<p style='font-size: 16px; color: #333;'>Your Founding Runner account has been created successfully!</p>";

        $message .= "<p style='font-size: 14px; color: #666;'>To get started, please set your password by clicking the link below:</p>";

        $message .= "</div>";

        /*
        * Password reset button.
        */
        $message .= "<div style='text-align: center; padding: 20px;'>";

        $message .= "<a href='" .
            esc_url($reset_page_url) .
            "' style='display: inline-block; padding: 14px 40px; background: #1a7efb; color: #fff; text-decoration: none; border-radius: 6px; font-size: 16px; font-weight: bold;'>";

        $message .= "🔑 Set Your Password";

        $message .= "</a>";

        $message .= "</div>";

        /*
        * Existing login link.
        */
        $message .= "<p style='font-size: 12px; color: #999; text-align: center;'>";

        $message .= "If you already have a password, you can ignore this email and ";

        $message .= "<a href='" .
            esc_url($login_url) .
            "' style='color: #1a7efb;'>login here</a>.";

        $message .= "</p>";

        /*
        * Account details.
        */
        $message .= "<div style='background: #f8f9fa; border-radius: 8px; padding: 15px; margin: 15px 0;'>";

        $message .= "<p style='margin: 5px 0; font-size: 13px;'><strong>Email:</strong> " .
            esc_html($email) .
            "</p>";

        $message .= "<p style='margin: 5px 0; font-size: 13px;'><strong>Account Type:</strong> Founding Runner</p>";

        $message .= "</div>";

        /*
        * BuddyNext profile.
        */
        $message .= "<p style='text-align: center; color: #666; font-size: 14px;'>";

        $message .= "Once you've set your password, you can access your ";

        $message .= "<a href='" .
            esc_url($account_url) .
            "' style='color: #1a7efb;'>BuddyNext profile</a>.";

        $message .= "</p>";

        $message .= "</div>";

        /*
        * Footer.
        */
        $message .= "<p style='text-align: center; color: #999; font-size: 12px; margin-top: 20px;'>";

        $message .= "Your information is secure and encrypted. © Run The Seas®";

        $message .= "</p>";

        $message .= "</body></html>";

        /*
        * Email headers.
        */
        $headers = rts_mail_headers();

        $email_template = rts_resolve_transactional_email_template(
            'password_reset',
            $subject,
            $message,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password_reset_url' => $reset_page_url,
                'login_url' => $login_url,
                'account_url' => $account_url,
                'captains_suite_url' => $account_url,
                'logo_url' => function_exists('rts_password_email_logo_url') ? rts_password_email_logo_url() : '',
                'support_email' => 'support@runtheseas.com',
            )
        );
        $subject = $email_template['subject'];
        $message = $email_template['html_body'];

        /*
        * Send email.
        */
        $sent = wp_mail(
            $email,
            $subject,
            $message,
            $headers
        );

        if ($sent) {
            error_log(
                'RTS: Welcome email with WordPress password reset link sent to ' . $email
            );
        } else {
            delete_user_meta($user_id, 'rts_initial_password_reset_requested');
            error_log(
                'RTS: Failed to send welcome email to ' . $email
            );
        }

        return $sent;
    }





}
