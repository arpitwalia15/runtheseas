<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the three-item sidebar navigation for the Captain's Suite template.
 * The page slugs can be changed per site without editing this plugin.
 */
function rts_captains_suite_navigation_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'suite_page'        => 'captains-suite',
        'log_page'          => 'captains-log',
        'certificates_page' => 'certificates',
        'member_lounge' => 'member-lounge',
        'profile_settings' => 'profile-settings',
        'support' => 'support'
    ), $atts, 'rts_captains_suite_navigation');

    $icon_url = RTS_PLUGIN_URL . 'assets/images/icons/';

    $links = array(
        array(
            'page'  => $atts['suite_page'],
            'label' => __("Captain's Suite", 'run-the-seas'),
            'icon'  => $icon_url . 'home1.svg',
        ),
        array(
            'page'  => $atts['log_page'],
            'label' => __("Captain's Log", 'run-the-seas'),
            'icon'  => $icon_url . 'captain-log1.svg',
        ),
        array(
            'page'  => $atts['certificates_page'],
            'label' => __('View Certificate', 'run-the-seas'),
            'icon'  => $icon_url . 'view-certificate1.svg',
        ),
        array(
            'page'  => $atts['member_lounge'],
            'label' => __('Member Lounge', 'run-the-seas'),
            'icon'  => $icon_url . 'member1.svg',
        ),
        // array(
        //     'page'  => $atts['profile_settings'],
        //     'url'   => rts_get_buddynext_profile_edit_url(),
        //     'label' => __('Profile & Settings', 'run-the-seas'),
        //     'icon'  => $icon_url . 'settings1.svg',
        // ),
         array(
            'page'  => $atts['profile_settings'],            
            'label' => __('Profile & Settings', 'run-the-seas'),
            'icon'  => $icon_url . 'settings1.svg',
        ),
        array(
            'page'  => $atts['support'],
            'label' => __('Support', 'run-the-seas'),
            'icon'  => $icon_url . 'support1.svg',
        ),
    );

    $output = '<nav class="rts-captains-suite-navigation" aria-label="' . esc_attr__('Captain\'s Suite', 'run-the-seas') . '">';
    foreach ($links as $link) {
        $page = get_page_by_path(sanitize_title($link['page']));
        $url = !empty($link['url']) ? $link['url'] : rts_get_member_page_url($link['page']);
        $current_path = untrailingslashit((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH));
        $target_path = untrailingslashit((string) wp_parse_url($url, PHP_URL_PATH));
        $active = ($page && is_page($page->ID)) || ($target_path && $current_path === $target_path) ? ' is-active' : '';
        $output .= '<a class="rts-captains-suite-navigation__link' . esc_attr($active) . '" href="'
            . esc_url($url) . '">';

        $output .= '<img class="rts-captains-suite-navigation__icon" src="'
            . esc_url($link['icon']) . '" alt="" aria-hidden="true">';

        $output .= '<span class="rts-captains-suite-navigation__label">'
            . esc_html($link['label'])
            . '</span>';

        $output .= '</a>';
    }
    $output .= '</nav>';

    return $output;
}
add_shortcode('rts_captains_suite_navigation', 'rts_captains_suite_navigation_shortcode');

/** Render a back link for log, certificate, and other inner Captain pages. */
function rts_captains_suite_back_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'label' => __("Back to Captain's Suite", 'run-the-seas'),
    ), $atts, 'rts_captains_suite_back');

    return '<a class="rts-captains-suite-back" href="' . esc_url(rts_get_captains_suite_url()) . '">&#8592; '
        . esc_html($atts['label']) . '</a>';
}
add_shortcode('rts_captains_suite_back', 'rts_captains_suite_back_shortcode');

/** Build a signed, member-only link for a certificate action. */
function rts_member_certificate_action_url($action, $participant_id, $nonce_action)
{
    $url = add_query_arg('action', sanitize_key($action), admin_url('admin-post.php'));
    return wp_nonce_url($url, $nonce_action . '_' . absint($participant_id));
}

/** Get the certificate owner for a protected certificate request. */
function rts_get_member_certificate_participant($nonce_action)
{
    if (!is_user_logged_in()) {
        wp_die(esc_html__('Please log in to access your certificate.', 'run-the-seas'), '', array('response' => 403));
    }

    $participant = rts_get_current_member_participant();
    if (!$participant || empty($participant->certificate_number) || (int) $participant->email_verified !== 1) {
        wp_die(esc_html__('Your certificate is not available yet.', 'run-the-seas'), '', array('response' => 404));
    }

    check_admin_referer($nonce_action . '_' . absint($participant->id));
    return $participant;
}

/** Stream the logged-in member's current certificate as an inline PDF or download. */
function rts_stream_member_certificate($download = false)
{
    $participant = rts_get_member_certificate_participant('rts_certificate_file');
    $plugin = rts_init();
    $certificate = $plugin->registration->generate_certificate_pdf($participant->id);

    if (is_wp_error($certificate) || !is_readable($certificate)) {
        $message = is_wp_error($certificate)
            ? $certificate->get_error_message()
            : __('Your certificate could not be prepared. Please try again.', 'run-the-seas');
        wp_die(esc_html($message), '', array('response' => 500));
    }

    $filename = 'run-the-seas-certificate-' . sanitize_file_name($participant->certificate_number) . '.pdf';
    nocache_headers();
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($certificate));
    header('Content-Disposition: ' . ($download ? 'attachment' : 'inline') . '; filename="' . $filename . '"');
    header('X-Content-Type-Options: nosniff');
    readfile($certificate);
    exit;
}

function rts_view_member_certificate()
{
    rts_stream_member_certificate(false);
}
add_action('admin_post_rts_view_member_certificate', 'rts_view_member_certificate');

function rts_download_member_certificate()
{
    rts_stream_member_certificate(true);
}
add_action('admin_post_rts_download_member_certificate', 'rts_download_member_certificate');

/** Resend the certificate only to the logged-in certificate owner. */
function rts_resend_member_certificate()
{
    $participant = rts_get_member_certificate_participant('rts_resend_certificate');
    $sent = rts_init()->registration->send_certificate($participant->id, get_current_user_id());
    $destination = wp_get_referer() ?: rts_get_member_page_url('certificates');
    $status = is_wp_error($sent) ? 'failed' : 'sent';

    wp_safe_redirect(add_query_arg('rts_certificate_email', $status, $destination));
    exit;
}
add_action('admin_post_rts_resend_member_certificate', 'rts_resend_member_certificate');

/**
 * Render the member certificate page.
 * Usage: [rts_certificate_page] or [rts_certificate]
 */
function rts_certificate_page_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'suite_page'  => 'captains-suite',
        'scene_image' => '',
        'certificate_top' => '21.5',
        'certificate_width' => '63.8',
    ), $atts, 'rts_certificate_page');
    $scene_image = esc_url_raw($atts['scene_image']);
    $has_scene_image = !empty($scene_image);
    $certificate_top = min(35, max(0, (float) $atts['certificate_top']));
    $certificate_width = min(75, max(45, (float) $atts['certificate_width']));
    $certificate_left = (100 - $certificate_width) / 2;
    $scene_style = $has_scene_image
        ? sprintf('--rts-certificate-top: %.2f%%; --rts-certificate-width: %.2f%%; --rts-certificate-left: %.2f%%;', $certificate_top, $certificate_width, $certificate_left)
        : '';

    if (!is_user_logged_in()) {
        return '<section class="rts-certificate-page rts-certificate-page--notice"><p>'
            . sprintf(
                wp_kses_post(__('Please <a href="%s">log in</a> to view your certificate.', 'run-the-seas')),
                esc_url(rts_get_member_login_url(rts_get_member_page_url('certificates')))
            )
            . '</p></section>';
    }

    $participant = rts_get_current_member_participant();
    if (!$participant || empty($participant->certificate_number) || (int) $participant->email_verified !== 1) {
        return '<section class="rts-certificate-page rts-certificate-page--notice"><div class="rts-certificate-page__notice-card">'
            . '<span class="rts-certificate-page__notice-icon" aria-hidden="true">&#9875;</span>'
            . '<h1>' . esc_html__('Your Certificate Is Not Available Yet', 'run-the-seas') . '</h1>'
            . '<p>' . esc_html__('Verify your email to activate your Captain\'s Suite and receive your Founding Runner certificate.', 'run-the-seas') . '</p>'
            . '<a class="rts-certificate-page__button" href="' . esc_url(rts_get_captains_suite_url()) . '">'
            . esc_html__('Back to Captain\'s Suite', 'run-the-seas') . '</a></div></section>';
    }

    $view_url = rts_member_certificate_action_url('rts_view_member_certificate', $participant->id, 'rts_certificate_file');
    $resend_url = rts_member_certificate_action_url('rts_resend_member_certificate', $participant->id, 'rts_resend_certificate');
    $back_url = rts_get_member_page_url($atts['suite_page']);
    $email_status = sanitize_key($_GET['rts_certificate_email'] ?? '');

    ob_start();
    ?>
    <section class="rts-certificate-page<?php echo $has_scene_image ? ' rts-certificate-page--scene' : ''; ?>" style="<?php echo esc_attr($scene_style); ?>" aria-label="<?php esc_attr_e('Founding Runner certificate', 'run-the-seas'); ?>">
        <?php if ($has_scene_image) : ?>
            <div class="rts-certificate-page__scene-stage">
                <img class="rts-certificate-page__scene-image" src="<?php echo esc_url($scene_image); ?>" alt="" aria-hidden="true">
        <?php endif; ?>
        <!--<header class="rts-certificate-page__header">-->
        <!--    <div class="rts-certificate-page__brand" aria-label="Run The Seas Captain's Suite">-->
        <!--        <span class="rts-certificate-page__brand-mark" aria-hidden="true">&#9875;</span>-->
        <!--        <span><strong>Run The Seas</strong><small>Captain's Suite</small></span>-->
        <!--    </div>-->
        <!--    <div class="rts-certificate-page__heading">-->
                <!--<span class="rts-certificate-page__ornament" aria-hidden="true">&#10022; &#9875; &#10022;</span>-->
        <!--        <h1 id="rts-certificate-page-title"><?php esc_html_e('View / Print Certificate', 'run-the-seas'); ?></h1>-->
        <!--        <p><?php esc_html_e('This is a duplicate copy of your Founding Runner Certificate. You can view, email, or print it below.', 'run-the-seas'); ?></p>-->
        <!--    </div>-->
        <!--</header>-->

        <?php if ('sent' === $email_status) : ?>
            <p class="rts-certificate-page__status rts-certificate-page__status--success"><?php esc_html_e('Your certificate has been emailed to you.', 'run-the-seas'); ?></p>
        <?php elseif ('failed' === $email_status) : ?>
            <p class="rts-certificate-page__status rts-certificate-page__status--error"><?php esc_html_e('We could not email your certificate. Please try again.', 'run-the-seas'); ?></p>
        <?php endif; ?>

        <div class="rts-certificate-page__viewer">
            <iframe title="<?php esc_attr_e('Your Founding Runner certificate', 'run-the-seas'); ?>" src="<?php echo esc_url($view_url); ?>#toolbar=0&amp;navpanes=0" loading="lazy"></iframe>
        </div>

        <div class="rts-certificate-page__actions" aria-label="<?php esc_attr_e('Certificate actions', 'run-the-seas'); ?>">
            <a class="rts-certificate-page__button" href="<?php echo esc_url($resend_url); ?>"><span aria-hidden="true">&#9993;</span><?php esc_html_e('Email Certificate', 'run-the-seas'); ?></a>
            <a class="rts-certificate-page__button" href="<?php echo esc_url($view_url); ?>" target="_blank" rel="noopener"><span aria-hidden="true">&#128438;</span><?php esc_html_e('Print Certificate', 'run-the-seas'); ?></a>
        </div>
        <a class="rts-certificate-page__back" href="<?php echo esc_url($back_url); ?>">&#8592; <?php esc_html_e('Back to Captain\'s Suite', 'run-the-seas'); ?></a>
        <?php if ($has_scene_image) : ?>
            </div>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_certificate_page', 'rts_certificate_page_shortcode');
add_shortcode('rts_certificate', 'rts_certificate_page_shortcode');

/** Create the Captain's Suite certificate page when the site does not have it yet. */
function rts_ensure_certificates_page()
{
    if (get_page_by_path('certificates')) {
        return;
    }

    wp_insert_post(array(
        'post_title'   => __('Certificates', 'run-the-seas'),
        'post_name'    => 'certificates',
        'post_content' => '[rts_certificate_page]',
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ));
}
add_action('init', 'rts_ensure_certificates_page', 30);

/** Route a member's empty BuddyPress profile URL to the Elementor Suite page. */
function rts_redirect_buddypress_member_home_to_captains_suite()
{
    if (is_admin() || !is_user_logged_in() || current_user_can(RTS_MANAGE_CAPABILITY)
        || !function_exists('bp_is_my_profile') || !bp_is_my_profile()
        || !function_exists('bp_is_user') || !bp_is_user()
        || !function_exists('bp_current_action') || bp_current_action()) {
        return;
    }

    $component = function_exists('bp_current_component') ? bp_current_component() : '';
    if ($component && 'members' !== $component) {
        return;
    }

    wp_safe_redirect(rts_get_captains_suite_url());
    exit;
}
add_action('template_redirect', 'rts_redirect_buddypress_member_home_to_captains_suite', 20);

function rts_ensure_member_profile_page()
{
    if (wp_installing()) {
        return;
    }
    $page_id = absint(get_option('rts_member_profile_page_id'));
    if (!$page_id || !get_post_status($page_id)) {
        $existing = get_page_by_path('my-details');
        if ($existing) {
            update_option('rts_member_profile_page_id', $existing->ID);
        } else {
            $page_id = wp_insert_post(array(
                'post_title'   => __('My Details', 'run-the-seas'),
                'post_name'    => 'my-details',
                'post_content' => '[rts_member_profile]',
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ));
            if ($page_id && !is_wp_error($page_id)) {
                update_option('rts_member_profile_page_id', $page_id);
            }
        }
    }

    $qr_page = get_page_by_path('my-qr-code');
    if ($qr_page) {
        update_option('rts_member_qr_page_id', $qr_page->ID);
    } elseif (!get_option('rts_member_qr_page_id')) {
        $qr_page_id = wp_insert_post(array(
            'post_title'   => __('My QR Code', 'run-the-seas'),
            'post_name'    => 'my-qr-code',
            'post_content' => '[rts_member_qr]',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));
        if ($qr_page_id && !is_wp_error($qr_page_id)) {
            update_option('rts_member_qr_page_id', $qr_page_id);
        }
    }
}
add_action('init', 'rts_ensure_member_profile_page', 30);

function rts_get_member_profile_url()
{
    $page_id = absint(get_option('rts_member_profile_page_id'));
    return $page_id && get_post_status($page_id) ? get_permalink($page_id) : home_url('/my-details/');
}

/** Return the logged-in member's native BuddyNext profile-edit URL. */
function rts_get_buddynext_profile_edit_url()
{
    $user_id = get_current_user_id();
    if (
        $user_id > 0
        && class_exists('\\BuddyNext\\Core\\PageRouter')
        && method_exists('\\BuddyNext\\Core\\PageRouter', 'edit_profile_url')
    ) {
        return \BuddyNext\Core\PageRouter::edit_profile_url($user_id);
    }

    return rts_get_member_profile_url();
}

function rts_get_member_qr_url()
{
    $page_id = absint(get_option('rts_member_qr_page_id'));
    return $page_id && get_post_status($page_id) ? get_permalink($page_id) : home_url('/my-qr-code/');
}

/** Customer-only account links, displayed on the Run The Seas website. */
function rts_render_customer_account_links()
{
    if (is_admin() || !is_user_logged_in() || current_user_can(RTS_MANAGE_CAPABILITY)) {
        return;
    }
    echo '<nav class="rts-customer-account-links" aria-label="My Run The Seas account">'
        . '<a href="' . esc_url(rts_get_member_profile_url()) . '">My Details</a>'
        . '<a href="' . esc_url(rts_get_member_qr_url()) . '">My QR Code</a>'
        . '</nav>';
}
add_action('init', 'rts_upgrade_participant_benefit_columns', 1);
