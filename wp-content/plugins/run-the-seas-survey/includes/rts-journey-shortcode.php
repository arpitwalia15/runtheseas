<?php

if (!defined('ABSPATH')) {
    exit;
}

/** Convert an RTS timeline key into concise member-facing report text. */
function rts_journey_activity_label($type)
{
    $labels = array(
        'email_verified' => 'Email Verified',
        'manual_email_verified' => 'Email Verified',
        'verification_sent' => 'Verification Email Sent',
        'referral_made' => 'Referral Verified',
        'referral_completed' => 'Referral Verified',
        'captain_miles_earned' => "Captain's Miles Earned",
        'trophy_earned' => 'Trophy Earned',
        'captain_suite_activated' => "Captain's Suite Opened",
        'certificate_issued' => 'Certificate Issued',
        'certificate_emailed' => 'Certificate Emailed',
        'cabin_credit_issued' => 'Cruise Credit Issued',
        'registration_completed' => 'Registration Completed',
        'survey_completed' => 'Survey Completed',
    );

    return isset($labels[$type]) ? $labels[$type] : ucwords(str_replace('_', ' ', $type));
}

/** Return the closest upcoming virtual-marathon milestone. */
function rts_journey_next_milestone($distance)
{
    foreach (array(5, 10, 15, 21.1, 25, 30, 35, 42.2) as $milestone) {
        if ($distance < $milestone) {
            return $milestone;
        }
    }

    return 42.2;
}

/** Return a display name for the country codes saved by registration. */
function rts_journey_country_name($country)
{
    $country = trim((string) $country);
    $countries = array(
        'US' => 'United States', 'CA' => 'Canada', 'GB' => 'United Kingdom',
        'AU' => 'Australia', 'DE' => 'Germany', 'FR' => 'France', 'IN' => 'India',
        'JP' => 'Japan', 'CN' => 'China', 'BR' => 'Brazil', 'MX' => 'Mexico',
        'ZA' => 'South Africa', 'NG' => 'Nigeria', 'EG' => 'Egypt',
        'AE' => 'United Arab Emirates',
    );
    $code = strtoupper($country);

    return isset($countries[$code]) ? $countries[$code] : ($country ?: __('International', 'run-the-seas'));
}

/**
 * Render the logged-in member's complete journey report.
 *
 * Elementor usage: add a Shortcode widget containing [rts_journey].
 * Optional: [rts_journey hero_image="https://..." activity_limit="30"].
 */
function rts_journey_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'hero_image' => '',
        'title' => 'Run The Seas Journey',
        'activity_limit' => 50,
    ), $atts, 'rts_journey');

    $design = get_option('rts_journey_design_assets', array());
    $design = is_array($design) ? $design : array();
    $design_image = static function ($key) use ($design) {
        return !empty($design[$key]) ? esc_url($design[$key]) : '';
    };

    if (!is_user_logged_in()) {
        return '<section class="rts-journey-message"><h2>' . esc_html__('Your Journey Awaits', 'run-the-seas') . '</h2><p>'
            . esc_html__('Please sign in to view your personalized Run The Seas journey.', 'run-the-seas') . '</p><a href="'
            . esc_url(wp_login_url(get_permalink())) . '">' . esc_html__('Sign in', 'run-the-seas') . '</a></section>';
    }

    $participant = function_exists('rts_get_current_member_participant') ? rts_get_current_member_participant() : null;
    if (!$participant) {
        return '<section class="rts-journey-message"><h2>' . esc_html__('Journey Not Available Yet', 'run-the-seas') . '</h2><p>'
            . esc_html__('Complete your Founding Runner registration to activate this report.', 'run-the-seas') . '</p></section>';
    }

    global $wpdb;
    $participant_id = absint($participant->id);
    $limit = max(5, min(200, absint($atts['activity_limit'])));
    $timeline_table = $wpdb->prefix . 'rts_timeline';
    $participants_table = $wpdb->prefix . 'rts_participants';
    $trophies_table = $wpdb->prefix . 'rts_user_trophies';
    $timeline = $wpdb->get_results($wpdb->prepare(
        "SELECT activity_type, activity_description, activity_data, activity_date
         FROM {$timeline_table} WHERE participant_id = %d
         ORDER BY activity_date DESC, id DESC LIMIT %d",
        $participant_id,
        $limit
    ));
    $earned_trophies = $wpdb->get_results($wpdb->prepare(
        "SELECT trophy_key, trophy_name, earned_date, miles_required
         FROM {$trophies_table} WHERE participant_id = %d ORDER BY earned_date ASC",
        $participant_id
    ));

    $verified_referrals = max(absint($participant->successful_referrals ?? 0), absint($participant->referral_count ?? 0));
    $total_miles = absint($participant->total_captain_miles_earned ?? 0);
    $distance = min(42.2, max($verified_referrals, $total_miles / 1000));
    $next_trophy = rts_journey_next_milestone($distance);
    $credit = (float) ($participant->cabin_credit_amount ?? 0);
    $country_code = strtoupper(trim((string) ($participant->country ?? '')));
    $country = rts_journey_country_name($country_code);
    $country_flag = function_exists('rts_country_flag') ? rts_country_flag($country_code ?: $country) : '🌐';
    $registration_date = !empty($participant->registration_date) ? strtotime($participant->registration_date) : false;
    $last_activity = !empty($timeline[0]->activity_date) ? strtotime($timeline[0]->activity_date) : $registration_date;
    $overall_position = 1 + (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$participants_table}
         WHERE successful_referrals > %d OR (successful_referrals = %d AND id < %d)",
        $verified_referrals,
        $verified_referrals,
        $participant_id
    ));
    $country_position = 1 + (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$participants_table}
         WHERE country = %s AND (successful_referrals > %d OR (successful_referrals = %d AND id < %d))",
        (string) ($participant->country ?? ''),
        $verified_referrals,
        $verified_referrals,
        $participant_id
    ));
    $user = wp_get_current_user();
    $full_name = trim((string) $participant->first_name . ' ' . (string) $participant->last_name);
    $full_name = $full_name ?: $user->display_name;
    $avatar = get_avatar_url($user->ID, array('size' => 120));
    $hero_image = esc_url($atts['hero_image']) ?: $design_image('hero_image');
    $logo_image = $design_image('logo_image');
    $print_icon = $design_image('print_icon_image');
    $email_icon = $design_image('email_icon_image');
    $frame_image = $design_image('frame_image');
    $progress_start_icon = $design_image('progress_start_icon_image');
    $footer_icon = $design_image('footer_icon_image');
    $founding_runner_icon = $design_image('founding_runner_icon');
    $gold_color = sanitize_hex_color($design['gold_color'] ?? '') ?: '#d99214';
    $page_width = max(700, min(1600, absint($design['page_width'] ?? 1180)));
    $journey_style = sprintf('--rts-gold:%s;--rts-journey-width:%dpx;', $gold_color, $page_width);
    $certificate = (string) ($participant->certificate_number ?? 'Pending');
    $last_trophy = !empty($earned_trophies) ? end($earned_trophies) : null;
    $title = sanitize_text_field($atts['title']);
    $updated = current_time('timestamp');
    $email_subject = sprintf('%s — %s', $title, $full_name);
    $email_body = sprintf(
        "My Run The Seas Journey\n\nCertificate: %s\nVerified referrals: %d\nDistance: %sK of 42.2K\nCruise credits: $%s",
        $certificate,
        $verified_referrals,
        number_format_i18n($distance, $distance == floor($distance) ? 0 : 1),
        number_format_i18n($credit, 0)
    );
    $milestones = array(5, 10, 15, 21.1, 25, 30, 35, 42.2);

    wp_enqueue_style('rts-journey', RTS_PLUGIN_URL . 'assets/css/journey.css', array(), RTS_VERSION . '.' . filemtime(RTS_PLUGIN_PATH . 'assets/css/journey.css'));
    wp_enqueue_script('rts-dom-to-image', RTS_PLUGIN_URL . 'assets/js/vendor/dom-to-image.min.js', array(), '2.6.0', true);
    wp_enqueue_script('rts-journey', RTS_PLUGIN_URL . 'assets/js/journey.js', array('rts-dom-to-image'), RTS_VERSION . '.' . filemtime(RTS_PLUGIN_PATH . 'assets/js/journey.js'), true);

    ob_start();
    ?>
    <section class="rts-journey<?php echo $frame_image ? ' has-report-frame' : ''; ?>" data-rts-journey style="<?php echo esc_attr($journey_style); ?>">
        <?php if ($frame_image) : ?><img class="rts-journey__report-frame" src="<?php echo esc_url($frame_image); ?>" alt="" aria-hidden="true"><?php endif; ?>
        <header class="rts-journey__header">
            <div class="rts-journey__brand"><?php if ($logo_image) : ?><img src="<?php echo esc_url($logo_image); ?>" alt="<?php esc_attr_e('Run The Seas', 'run-the-seas'); ?>"><?php else : ?><span aria-hidden="true">&#10021;</span><strong>RUN THE SEAS<sup>&trade;</sup></strong><?php endif; ?></div>
            <div class="rts-journey__heading"><h1><?php echo esc_html($title); ?><sup>&trade;</sup></h1><p><?php esc_html_e('Your complete journey from your first survey to your latest achievement.', 'run-the-seas'); ?></p></div>
            <div class="rts-journey__actions"><button type="button" data-rts-journey-print><?php if ($print_icon) : ?><img src="<?php echo esc_url($print_icon); ?>" alt=""><?php else : ?><span aria-hidden="true">&#128438;</span><?php endif; ?> <?php esc_html_e('Print Report', 'run-the-seas'); ?></button><button type="button" data-rts-journey-email data-email-subject="<?php echo esc_attr($email_subject); ?>" data-email-body="<?php echo esc_attr($email_body); ?>" data-email-default="<?php echo esc_attr($user->user_email); ?>" data-email-ajax="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-email-nonce="<?php echo esc_attr(wp_create_nonce('rts_email_journey_report')); ?>"><?php if ($email_icon) : ?><img src="<?php echo esc_url($email_icon); ?>" alt=""><?php else : ?><span aria-hidden="true">&#9993;</span><?php endif; ?> <?php esc_html_e('Email Report', 'run-the-seas'); ?></button></div>
        </header>

        <div class="rts-journey__member-strip">
            <div class="rts-journey__identity"><img src="<?php echo esc_url($avatar); ?>" alt=""><div><span class="rts-journey__country"><b aria-hidden="true"><?php echo esc_html($country_flag); ?></b><?php echo esc_html(strtoupper($country)); ?></span><small><?php echo esc_html(sprintf(__('Member since: %s', 'run-the-seas'), $registration_date ? wp_date('F j, Y', $registration_date) : '—')); ?></small></div></div>
            <div class="rts-journey__stat rts-journey__stat--badge"><img src="<?php echo esc_url($founding_runner_icon); ?>" alt="founding runner icon" /><span><?php esc_html_e('Certificate No.', 'run-the-seas'); ?></span><b><?php echo esc_html($certificate); ?></b></div>
            <div class="rts-journey__stat"><span><?php esc_html_e('Overall Position', 'run-the-seas'); ?></span><b><?php echo esc_html(number_format_i18n($overall_position)); ?></b></div>
            <div class="rts-journey__stat"><span><?php echo esc_html(sprintf(__('%s Position', 'run-the-seas'), $country)); ?></span><b><?php echo esc_html(number_format_i18n($country_position)); ?></b></div>
            <div class="rts-journey__stat"><span><?php esc_html_e('Cruise Credits Earned', 'run-the-seas'); ?></span><b>$<?php echo esc_html(number_format_i18n($credit, 0)); ?></b></div>
            <div class="rts-journey__stat"><span><?php esc_html_e('Last Activity', 'run-the-seas'); ?></span><strong><?php echo esc_html($last_activity ? wp_date('F j, Y', $last_activity) : '—'); ?></strong></div>
        </div>

        <div class="rts-journey__view<?php echo $hero_image ? ' has-image' : ''; ?>"<?php echo $hero_image ? ' style="--rts-journey-hero:url(\'' . esc_url($hero_image) . '\')"' : ''; ?>><?php if ($hero_image) : ?><img class="rts-journey__scene-image" src="<?php echo esc_url($hero_image); ?>" alt="" aria-hidden="true"><?php else : ?><span class="rts-journey__porthole" aria-hidden="true"></span><span class="rts-journey__compass" aria-hidden="true">&#10021;</span><?php endif; ?></div>

        <div class="rts-journey__progress">
            <div class="rts-journey__distance"><span><?php esc_html_e('Current Distance', 'run-the-seas'); ?></span><strong><?php echo esc_html(number_format_i18n($distance, $distance == floor($distance) ? 0 : 1)); ?>K OF 42.2K</strong></div>
            <div class="rts-journey__track-wrap"><?php if ($progress_start_icon) : ?><img class="rts-journey__track-start" src="<?php echo esc_url($progress_start_icon); ?>" alt=""><?php endif; ?><div class="rts-journey__track"><div class="rts-journey__track-fill" style="width:<?php echo esc_attr(min(100, ($distance / 42.2) * 100)); ?>%"></div><?php foreach ($milestones as $milestone) : $percent = ($milestone / 42.2) * 100; ?><span class="rts-journey__mile<?php echo $distance >= $milestone ? ' is-earned' : ''; ?>" style="left:<?php echo esc_attr($percent); ?>%"><b><?php echo esc_html($milestone); ?>K</b><i></i></span><?php endforeach; ?></div></div>
        </div>

        <div class="rts-journey__summary">
            <div><span><?php esc_html_e('Certificate', 'run-the-seas'); ?></span><strong><?php echo esc_html($certificate); ?></strong></div>
            <div><span><?php esc_html_e('Verified Referrals', 'run-the-seas'); ?></span><strong><?php echo esc_html(number_format_i18n($verified_referrals)); ?></strong></div>
            <div><span><?php esc_html_e('Current Distance', 'run-the-seas'); ?></span><strong><?php echo esc_html(number_format_i18n($distance, $distance == floor($distance) ? 0 : 1)); ?>K OF 42.2K</strong></div>
            <div><span><?php esc_html_e('Next Trophy', 'run-the-seas'); ?></span><strong><?php echo esc_html($next_trophy); ?>K</strong></div>
            <div><span><?php esc_html_e('Cruise Credits', 'run-the-seas'); ?></span><strong>$<?php echo esc_html(number_format_i18n($credit, 0)); ?></strong></div>
            <div><span><?php esc_html_e('Last Activity', 'run-the-seas'); ?></span><strong><?php echo esc_html($last_activity ? wp_date('F j, Y', $last_activity) : '—'); ?></strong></div>
        </div>

        <div class="rts-journey__filters">
            <label><span><?php esc_html_e('Show', 'run-the-seas'); ?></span><select data-rts-journey-type><option value="all"><?php esc_html_e('All Activity', 'run-the-seas'); ?></option><option value="referral"><?php esc_html_e('Referrals', 'run-the-seas'); ?></option><option value="trophy"><?php esc_html_e('Trophies', 'run-the-seas'); ?></option><option value="certificate"><?php esc_html_e('Certificates', 'run-the-seas'); ?></option><option value="verification"><?php esc_html_e('Verification', 'run-the-seas'); ?></option></select></label>
            <label><span><?php esc_html_e('Period', 'run-the-seas'); ?></span><select data-rts-journey-period><option value="all"><?php esc_html_e('All Time', 'run-the-seas'); ?></option><option value="30">30 <?php esc_html_e('Days', 'run-the-seas'); ?></option><option value="90">90 <?php esc_html_e('Days', 'run-the-seas'); ?></option><option value="365">1 <?php esc_html_e('Year', 'run-the-seas'); ?></option></select></label>
            <label><span><?php esc_html_e('Sort', 'run-the-seas'); ?></span><select data-rts-journey-sort><option value="desc"><?php esc_html_e('Newest First', 'run-the-seas'); ?></option><option value="asc"><?php esc_html_e('Oldest First', 'run-the-seas'); ?></option></select></label>
        </div>

        <div class="rts-journey__report"><h2><?php esc_html_e('Activity Report', 'run-the-seas'); ?></h2><div class="rts-journey__table-wrap"><table><thead><tr><th><?php esc_html_e('Date', 'run-the-seas'); ?></th><th><?php esc_html_e('Activity', 'run-the-seas'); ?></th><th><?php esc_html_e('Details', 'run-the-seas'); ?></th><th><?php esc_html_e('Result', 'run-the-seas'); ?></th></tr></thead><tbody data-rts-journey-rows>
        <?php if (!$timeline) : ?><tr class="rts-journey__empty"><td colspan="4"><?php esc_html_e('Your journey activity will appear here.', 'run-the-seas'); ?></td></tr><?php endif; ?>
        <?php foreach ($timeline as $entry) :
            $type = sanitize_key($entry->activity_type);
            $date_value = !empty($entry->activity_date) ? strtotime($entry->activity_date) : 0;
            $category = strpos($type, 'referral') !== false ? 'referral' : (strpos($type, 'trophy') !== false ? 'trophy' : (strpos($type, 'certificate') !== false ? 'certificate' : (strpos($type, 'verif') !== false ? 'verification' : 'other')));
        ?>
            <tr data-type="<?php echo esc_attr($category); ?>" data-time="<?php echo esc_attr($date_value); ?>"><td><?php echo esc_html($date_value ? wp_date('F j, Y', $date_value) : '—'); ?></td><td><?php echo esc_html(rts_journey_activity_label($type)); ?></td><td><?php echo esc_html($entry->activity_description); ?></td><td><span class="rts-journey__check">✓</span> <?php echo strpos($type, 'miles') !== false ? '+1K' : esc_html__('Completed', 'run-the-seas'); ?></td></tr>
        <?php endforeach; ?>
        </tbody></table></div></div>
        <footer class="rts-journey__footer"><span class="rts-journey__footer-message"><?php if ($footer_icon) : ?><img src="<?php echo esc_url($footer_icon); ?>" alt=""><?php else : ?><span aria-hidden="true">&#9875;</span><?php endif; ?> <?php esc_html_e('Report automatically updated from your member account.', 'run-the-seas'); ?></span><span><?php echo esc_html(sprintf(__('Last updated: %s', 'run-the-seas'), wp_date('F j, Y \a\t g:i A', $updated))); ?></span></footer>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_journey', 'rts_journey_shortcode');

/** Email the signed-in member's captured Journey report as an attachment. */
function rts_email_journey_report()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => __('Please sign in before emailing your Journey report.', 'run-the-seas')), 401);
    }

    check_ajax_referer('rts_email_journey_report', 'nonce');

    $recipient = sanitize_email(wp_unslash($_POST['recipient'] ?? ''));
    if (!$recipient || !is_email($recipient)) {
        wp_send_json_error(array('message' => __('Enter a valid recipient email address.', 'run-the-seas')), 400);
    }

    $participant = function_exists('rts_get_current_member_participant') ? rts_get_current_member_participant() : null;
    if (!$participant) {
        wp_send_json_error(array('message' => __('Your Journey report is not available yet.', 'run-the-seas')), 403);
    }

    if (empty($_FILES['report']) || !is_array($_FILES['report'])) {
        wp_send_json_error(array('message' => __('The Journey report attachment was not received.', 'run-the-seas')), 400);
    }

    $upload = $_FILES['report'];
    if (UPLOAD_ERR_OK !== (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE)) {
        wp_send_json_error(array('message' => __('The Journey report attachment could not be uploaded.', 'run-the-seas')), 400);
    }

    $size = (int) ($upload['size'] ?? 0);
    $uploaded_path = (string) ($upload['tmp_name'] ?? '');
    $image_info = $uploaded_path && is_uploaded_file($uploaded_path) ? @getimagesize($uploaded_path) : false;
    if (!$image_info || 'image/png' !== ($image_info['mime'] ?? '') || $size < 1 || $size > 15 * MB_IN_BYTES) {
        wp_send_json_error(array('message' => __('The Journey attachment must be a valid PNG image under 15 MB.', 'run-the-seas')), 400);
    }

    $certificate = sanitize_file_name((string) ($participant->certificate_number ?? 'report'));
    $attachment = trailingslashit(get_temp_dir()) . 'run-the-seas-journey-' . get_current_user_id() . '-' . $certificate . '-' . wp_generate_password(6, false, false) . '.png';
    if (!move_uploaded_file($uploaded_path, $attachment)) {
        wp_send_json_error(array('message' => __('The Journey attachment could not be prepared for email.', 'run-the-seas')), 500);
    }

    $full_name = trim((string) ($participant->first_name ?? '') . ' ' . (string) ($participant->last_name ?? ''));
    $full_name = $full_name ?: wp_get_current_user()->display_name;
    $subject = sprintf(__('%s shared a Run The Seas Journey report', 'run-the-seas'), $full_name);
    $message = '<p>' . esc_html(sprintf(__('%s has shared their Run The Seas Journey report with you.', 'run-the-seas'), $full_name)) . '</p>'
        . '<p>' . esc_html__('The complete report is attached to this email as a PNG image. No account or private page link is required to view it.', 'run-the-seas') . '</p>'
        . '<p><strong>' . esc_html__('Certificate:', 'run-the-seas') . '</strong> ' . esc_html((string) ($participant->certificate_number ?? '—')) . '</p>';

    $sent = wp_mail(
        $recipient,
        $subject,
        $message,
        array('Content-Type: text/html; charset=UTF-8'),
        array($attachment)
    );
    @unlink($attachment);

    if (!$sent) {
        wp_send_json_error(array('message' => __('The report could not be sent. Please check the website email configuration.', 'run-the-seas')), 500);
    }

    wp_send_json_success(array('message' => sprintf(__('Journey report sent to %s.', 'run-the-seas'), $recipient)));
}
add_action('wp_ajax_rts_email_journey_report', 'rts_email_journey_report');

/** Create the Elementor-editable Journey page when it does not exist yet. */
function rts_ensure_journey_page()
{
    if (wp_installing() || get_page_by_path('view-journey')) {
        return;
    }

    $page_id = wp_insert_post(array(
        'post_title' => __('View Journey', 'run-the-seas'),
        'post_name' => 'view-journey',
        'post_content' => '[rts_journey]',
        'post_status' => 'publish',
        'post_type' => 'page',
        'comment_status' => 'closed',
    ));

    if (!is_wp_error($page_id) && $page_id) {
        update_option('rts_journey_page_id', absint($page_id));
    }
}
add_action('init', 'rts_ensure_journey_page', 30);
