<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add solid-color fallbacks to the password reset button for Outlook.
 *
 * Outlook's Word-based renderer ignores CSS gradients. Older password-reset
 * templates saved in the admin platform therefore rendered dark button text
 * directly on the dark email card until external content was enabled. This is
 * applied at send time so existing stored templates are fixed immediately.
 */
function rts_make_transactional_email_outlook_safe($html, $action_key = '')
{
    if ('password_reset' !== sanitize_key((string) $action_key) || false === stripos($html, 'Reset My Passcode')) {
        return $html;
    }

    return preg_replace_callback(
        '/<td\b([^>]*)>(?=\s*<a\b[^>]*>[^<]*Reset\s+My\s+Passcode)/i',
        function ($matches) {
            $attributes = $matches[1];

            if (preg_match('/\bbgcolor\s*=\s*(["\']).*?\1/i', $attributes)) {
                $attributes = preg_replace(
                    '/\bbgcolor\s*=\s*(["\']).*?\1/i',
                    'bgcolor="#E4C77A"',
                    $attributes,
                    1
                );
            } else {
                $attributes .= ' bgcolor="#E4C77A"';
            }

            $outlook_styles = 'background-color:#E4C77A;background-image:linear-gradient(180deg,#E4C77A 0%,#C9A24B 100%);border:1px solid #C9A24B;mso-padding-alt:15px 42px;';
            if (preg_match('/\bstyle\s*=\s*(["\'])(.*?)\1/i', $attributes)) {
                $attributes = preg_replace_callback(
                    '/\bstyle\s*=\s*(["\'])(.*?)\1/i',
                    function ($style_matches) use ($outlook_styles) {
                        // Remove the old background shorthand because clients
                        // that block CSS images also discard its color layer.
                        $style = preg_replace(
                            '/(?:background(?:-color|-image)?|border|mso-padding-alt)\s*:[^;]*;?/i',
                            '',
                            $style_matches[2]
                        );
                        $style = $outlook_styles . ltrim($style);

                        return 'style=' . $style_matches[1] . $style . $style_matches[1];
                    },
                    $attributes,
                    1
                );
            } else {
                $attributes .= ' style="' . $outlook_styles . '"';
            }

            return '<td' . $attributes . '>';
        },
        (string) $html,
        1
    );
}

/**
 * Resolve an assigned admin-platform template for a transactional email.
 *
 * An assigned template may leave its body empty to inherit the complete
 * built-in survey design. This keeps the survey plugin independent and gives
 * every email a safe fallback when the admin platform is inactive or no
 * template is assigned.
 */
function rts_resolve_transactional_email_template($action_key, $default_subject, $default_html, array $context = array())
{
    global $wpdb;

    $template = null;
    $table = $wpdb->prefix . 'rts_email_templates';
    static $template_table_ready = null;

    if (null === $template_table_ready) {
        $table_exists = $table === $wpdb->get_var(
            $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table))
        );
        $template_table_ready = $table_exists
            && (bool) $wpdb->get_var(
                $wpdb->prepare("SHOW COLUMNS FROM `{$table}` LIKE %s", 'action_key')
            );
    }

    if ($template_table_ready) {
        $template = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id, subject, html_body FROM `{$table}` WHERE action_key = %s LIMIT 1",
                sanitize_key($action_key)
            )
        );
    }

    $subject = $template && trim((string) $template->subject) !== ''
        ? (string) $template->subject
        : (string) $default_subject;
    $html = $template && trim((string) $template->html_body) !== ''
        ? (string) $template->html_body
        : (string) $default_html;

    $design_context = function_exists('rts_get_transactional_email_design_merge_context')
        ? rts_get_transactional_email_design_merge_context($action_key)
        : array();
    $context = array_merge(
        array(
            'first_name' => '',
            'last_name' => '',
            'full_name' => '',
            'name' => '',
            'email' => '',
            'password_reset_url' => '',
            'reset_url' => '',
            'verification_url' => '',
            'verify_url' => '',
            'certificate_number' => '',
            'founding_runner_number' => '',
            'certificate_preview_url' => '',
            'captains_suite_url' => '',
            'login_url' => '',
            'account_url' => '',
            'logo_url' => '',
            'support_email' => 'support@runtheseas.com',
            'site_name' => wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES),
            'site_url' => home_url('/'),
        ),
        $design_context,
        $context
    );

    if ($context['full_name'] === '') {
        $context['full_name'] = trim((string) $context['first_name'] . ' ' . (string) $context['last_name']);
    }
    if ($context['name'] === '') {
        $context['name'] = $context['full_name'];
    }
    if ($context['reset_url'] === '') {
        $context['reset_url'] = $context['password_reset_url'];
    }
    if ($context['verify_url'] === '') {
        $context['verify_url'] = $context['verification_url'];
    }
    if ($context['founding_runner_number'] === '') {
        $context['founding_runner_number'] = $context['certificate_number'];
    }

    $subject_replacements = array();
    $html_replacements = array();
    foreach ($context as $key => $value) {
        if (!is_scalar($value) && null !== $value) {
            continue;
        }
        $token = '{' . $key . '}';
        $subject_replacements[$token] = wp_strip_all_tags((string) $value);
        $html_replacements[$token] = esc_html((string) $value);
    }

    $resolved = array(
        'subject' => sanitize_text_field(strtr($subject, $subject_replacements)),
        'html_body' => rts_make_transactional_email_outlook_safe(
            strtr($html, $html_replacements),
            $action_key
        ),
        'template_id' => $template ? (int) $template->id : 0,
        'action_key' => sanitize_key($action_key),
        'uses_builtin_body' => !$template || trim((string) $template->html_body) === '',
    );

    return apply_filters('rts_resolved_transactional_email_template', $resolved, $context);
}

/**
 * Return real image URLs for the admin template editor preview.
 *
 * The stored template deliberately keeps merge fields so certificate artwork
 * can be personalised for each recipient. The editor uses these URLs only for
 * its on-screen preview and converts them back to merge fields when saving.
 */
function rts_get_transactional_email_design_merge_context($action_key = '')
{
    $action_key = sanitize_key((string) $action_key);
    $is_certificate = 'founding_runner_certificate' === $action_key;
    $asset_option = $is_certificate ? 'rts_certificate_email_design_assets' : 'rts_verification_email_design_assets';
    $prefix = $is_certificate ? 'certificate_' : 'verification_';
    $assets = get_option($asset_option, array());
    $assets = is_array($assets) ? $assets : array();

    $context = array();
    foreach ($assets as $key => $url) {
        if ('certificate_preview_image' === $key || empty($url)) {
            continue;
        }
        $context[$prefix . sanitize_key($key)] = esc_url_raw($url);
    }
    return $context;
}

function rts_get_transactional_email_editor_preview_context($action_key = '')
{
    $action_key = sanitize_key((string) $action_key);
    $asset_option = 'founding_runner_certificate' === $action_key
        ? 'rts_certificate_email_design_assets'
        : 'rts_verification_email_design_assets';
    $assets = get_option($asset_option, array());
    $assets = is_array($assets) ? $assets : array();

    $certificate_preview_url = !empty($assets['certificate_preview_image'])
        ? esc_url_raw($assets['certificate_preview_image'])
        : esc_url_raw(RTS_PLUGIN_URL . 'assets/certificate-template.png');

    return array_merge(rts_get_transactional_email_design_merge_context($action_key), array(
        'logo_url' => function_exists('rts_password_email_logo_url')
            ? esc_url_raw(rts_password_email_logo_url())
            : '',
        'certificate_preview_url' => $certificate_preview_url,
    ));
}

/**
 * Export editable defaults from the same production renderers used to send
 * Run The Seas transactional emails. Only recipient data becomes merge fields.
 */
function rts_get_production_transactional_email_templates()
{
    if (
        !function_exists('rts_render_password_email_template')
        || !class_exists('RTS_Registration')
        || !method_exists('RTS_Registration', 'get_verification_email_template_definition')
    ) {
        return array();
    }

    $reset_url = 'https://rts-template.invalid/password-reset-url';
    $logo_url = 'https://rts-template.invalid/logo-url';
    $site_url = 'https://rts-template.invalid/site-url';
    $support_email = 'rts-template-support@example.invalid';
    $password_html = rts_render_password_email_template(
        'password-reset',
        array(
            'first_name' => 'RTS_FIRST_NAME',
            'logo_url' => $logo_url,
            'reset_link' => $reset_url,
            'site_url' => $site_url,
            'support_email' => $support_email,
        )
    );
    $password_html = str_replace(
        array('RTS_FIRST_NAME', $logo_url, $reset_url, $site_url, $support_email),
        array('{first_name}', '{logo_url}', '{password_reset_url}', '{site_url}', '{support_email}'),
        $password_html
    );

    $reflection = new ReflectionClass('RTS_Registration');
    $registration = $reflection->newInstanceWithoutConstructor();

    return array(
        array(
            'template_key' => 'default_password_reset',
            'action_key' => 'password_reset',
            'name' => 'Password Reset',
            'subject' => 'Captain’s Suite Passcode Reset',
            'html_body' => $password_html,
        ),
        $registration->get_verification_email_template_definition(),
        $registration->get_certificate_email_template_definition(),
    );
}
