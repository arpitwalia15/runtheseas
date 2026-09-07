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

/** Keep the verification certificate at a true 490px minimum in saved templates. */
function rts_enforce_verification_certificate_width($html)
{
    $html = preg_replace_callback(
        '/<img\b(?=[^>]*\balt\s*=\s*(["\'])Preview of your Founding Runner Cruise Credit\1)[^>]*\/?\s*>/i',
        function ($matches) {
            $tag = $matches[0];
            if (preg_match('/\bwidth\s*=\s*(["\']).*?\1/i', $tag)) {
                $tag = preg_replace('/\bwidth\s*=\s*(["\']).*?\1/i', 'width="490"', $tag, 1);
            } else {
                $tag = preg_replace('/\s*\/?>$/', ' width="490">', $tag, 1);
            }

            $required_style = 'display:block;width:100%;min-width:490px;max-width:490px;height:auto;';
            if (preg_match('/\bstyle\s*=\s*(["\'])(.*?)\1/i', $tag)) {
                $tag = preg_replace_callback(
                    '/\bstyle\s*=\s*(["\'])(.*?)\1/i',
                    function ($style_matches) use ($required_style) {
                        $style = preg_replace(
                            '/(?:display|min-width|max-width|(?<!-)width|height)\s*:[^;]*;?/i',
                            '',
                            $style_matches[2]
                        );
                        return 'style=' . $style_matches[1] . $required_style . ltrim($style) . $style_matches[1];
                    },
                    $tag,
                    1
                );
            } else {
                $tag = preg_replace('/\s*\/?>$/', ' style="' . $required_style . '">', $tag, 1);
            }
            return $tag;
        },
        (string) $html
    );

    // The content area is 700px wide; a 30/70 split gives the certificate
    // column the full 490px required by the image.
    $html = preg_replace('/\bwidth\s*=\s*(["\'])(?:35%|32%)\1/i', 'width="30%"', $html);
    $html = preg_replace('/\bwidth\s*=\s*(["\'])(?:65%|68%)\1/i', 'width="70%"', $html);
    return $html;
}

/**
 * Render certificate personalization over the blank backplate without GD.
 *
 * The ordinary path sends a baked PNG. This table/VML version is only used
 * when the live server returns the untouched artwork because image processing
 * is unavailable. VML supplies the background in desktop Outlook, while the
 * table background covers Gmail, Apple Mail, and web clients.
 */
function rts_render_email_certificate_html_fallback($backplate_url, array $context = array())
{
    $backplate_url = esc_url((string) $backplate_url);
    if ($backplate_url === '') {
        return '';
    }

    $name = trim((string) ($context['full_name'] ?? ''));
    if ($name === '') {
        $name = trim((string) ($context['first_name'] ?? '') . ' ' . (string) ($context['last_name'] ?? ''));
    }
    $name = $name !== '' ? $name : __('Founding Runner', 'run-the-seas');
    $runner_number = trim((string) ($context['founding_runner_number'] ?? ''));
    $certificate_number = trim((string) ($context['certificate_number'] ?? ''));
    $status = strtoupper(trim((string) ($context['certificate_status'] ?? 'PENDING')));
    $issued_date = trim((string) ($context['certificate_issued_date'] ?? ''));

    $runner_number = $runner_number !== '' ? $runner_number : __('Pending', 'run-the-seas');
    $certificate_number = $certificate_number !== '' ? $certificate_number : __('Pending', 'run-the-seas');
    $status = $status !== '' ? $status : 'PENDING';
    $issued_date = $issued_date !== '' ? $issued_date : __('Pending verification', 'run-the-seas');
    $status_colour = 'APPROVED' === $status ? '#2d995b' : ('VOID' === $status ? '#9b2c2c' : '#be7e12');

    $content = '<table role="presentation" width="490" height="366" cellspacing="0" cellpadding="0" border="0" background="' . $backplate_url . '" style="width:490px;height:366px;border-collapse:collapse;background-color:#f2e5cf;background-image:url(\'' . $backplate_url . '\');background-position:center center;background-repeat:no-repeat;background-size:490px 366px;">'
        . '<tr><td height="140" style="height:140px;font-size:0;line-height:0;">&nbsp;</td></tr>'
        . '<tr><td height="30" align="center" valign="middle" style="height:30px;padding:0 72px;color:#071b3b;font-family:Georgia,\'Times New Roman\',serif;font-size:20px;font-style:italic;font-weight:bold;line-height:22px;white-space:nowrap;">' . esc_html($name) . '</td></tr>'
        . '<tr><td height="86" style="height:86px;font-size:0;line-height:0;">&nbsp;</td></tr>'
        . '<tr><td height="49" valign="top" style="height:49px;padding:0 0 0 32px;color:#071b3b;font-family:Arial,Helvetica,sans-serif;text-align:left;">'
        . '<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;color:#071b3b;font-family:Arial,Helvetica,sans-serif;text-align:left;">'
        . '<tr><td colspan="2" style="padding:0;font-size:7px;line-height:8px;color:#2c4156;">FOUNDING RUNNER NUMBER</td></tr>'
        . '<tr><td colspan="2" style="padding:1px 0 3px;font-size:12px;line-height:13px;font-weight:bold;">' . esc_html($runner_number) . '</td></tr>'
        . '<tr><td style="padding:0 6px 0 0;font-size:7px;line-height:9px;color:#2c4156;">CERTIFICATE NO.</td><td style="padding:0;font-size:7px;line-height:9px;font-weight:bold;white-space:nowrap;">' . esc_html($certificate_number) . '&nbsp;&nbsp;<span style="display:inline-block;padding:1px 4px;background:' . esc_attr($status_colour) . ';color:#ffffff;font-size:6px;line-height:8px;font-weight:bold;">' . esc_html($status) . '</span></td></tr>'
        . '<tr><td style="padding:1px 6px 0 0;font-size:7px;line-height:9px;color:#2c4156;">ISSUED:</td><td style="padding:1px 0 0;font-size:7px;line-height:9px;font-weight:bold;white-space:nowrap;">' . esc_html($issued_date) . '</td></tr>'
        . '</table></td></tr>'
        . '<tr><td height="61" style="height:61px;font-size:0;line-height:0;">&nbsp;</td></tr>'
        . '</table>';

    return '<!--[if gte mso 9]><v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:490px;height:366px;"><v:fill type="frame" src="' . $backplate_url . '" color="#f2e5cf"/><v:textbox inset="0,0,0,0"><![endif]-->'
        . $content
        . '<!--[if gte mso 9]></v:textbox></v:rect><![endif]-->';
}

/** Identify certificate slots in saved transactional templates, including Media Library artwork. */
function rts_is_email_certificate_image($source, $alt, $action_key, $preview_url = '')
{
    if (!in_array($action_key, array('email_verification', 'founding_runner_certificate'), true)) {
        return false;
    }
    $source = html_entity_decode(trim((string) $source), ENT_QUOTES, 'UTF-8');
    if ('{certificate_preview_url}' === $source || ($preview_url !== '' && $source === $preview_url)) {
        return true;
    }
    if (in_array(trim((string) $alt), array('Preview of your Founding Runner Cruise Credit', 'Your Founding Runner Gift Certificate'), true)) {
        return true;
    }
    foreach (array('rts_verification_email_design_assets', 'rts_certificate_email_design_assets') as $option) {
        $assets = get_option($option, array());
        if (is_array($assets) && !empty($assets['certificate_preview_image']) && $source === $assets['certificate_preview_image']) {
            return true;
        }
    }

    $path = rawurldecode((string) wp_parse_url($source, PHP_URL_PATH));
    $filename = str_replace('_', '-', strtolower(basename($path)));
    // Match the approved named backplates, including WordPress thumbnail,
    // duplicate-upload, and scaled suffixes. Do not match arbitrary certificates
    // or certificate-related button/icon images.
    return (bool) preg_match('~^(?:runtheseas-)?certificate-(?:backplate|template|confirmation-preview)(?:-v\d+)?(?:-\d+|-(?:\d+x\d+)|-scaled|-old|--*|[ -]*\(\d+\))*\.(?:jpe?g|png|webp)$~i', $filename)
        || 'register-page-sample-certificate.png' === $filename
        || (bool) preg_match('~/rts-certificate-previews/verification-preview-\d+-[a-f0-9]+\.png$~i', $path)
        || false !== strpos($source, 'rts_certificate_preview=1');
}

/**
 * Keep the selected backplate separate from the personalized image merge field.
 * The same normalization runs when editing, saving, and sending a template, so
 * existing static Media Library images are repaired without a manual HTML edit.
 */
function rts_normalize_email_certificate_template($html, $action_key, $preview_url = '')
{
    $result = array('html_body' => (string) $html, 'backplate_url' => '', 'sources' => array());
    if (!in_array($action_key, array('email_verification', 'founding_runner_certificate'), true)) {
        return $result;
    }
    $tags = new WP_HTML_Tag_Processor((string) $html);
    while ($tags->next_tag('IMG')) {
        $source = (string) $tags->get_attribute('src');
        $backplate = (string) $tags->get_attribute('data-rts-certificate-backplate');
        if ($backplate === '' && !rts_is_email_certificate_image($source, $tags->get_attribute('alt'), $action_key, $preview_url)) {
            continue;
        }
        $result['sources'][] = $source;
        if ($backplate === '' && $source !== '{certificate_preview_url}' && $source !== $preview_url
            && false === strpos($source, '/rts-certificate-previews/') && false === strpos($source, 'rts_certificate_preview=')) {
            $backplate = esc_url_raw($source);
        }
        if ($backplate !== '') {
            $backplate = esc_url_raw($backplate);
            $result['backplate_url'] = $backplate;
            $tags->set_attribute('data-rts-certificate-backplate', $backplate);
        }
        // Tag Processor URL-escapes src values, which strips merge-field braces.
        // Use a reserved URL during parsing and restore the token afterwards.
        $tags->set_attribute('src', 'https://rts-template.invalid/certificate-preview-url');
        $tags->set_attribute('alt', 'email_verification' === $action_key
            ? 'Preview of your Founding Runner Cruise Credit' : 'Your Founding Runner Gift Certificate');
        // A Media Library srcset or lazy-load URL can override the personalized
        // src in supporting clients. Keep just the recipient-aware source.
        foreach (array('srcset', 'sizes', 'data-src', 'data-srcset', 'data-lazy-src', 'data-mce-src') as $attribute) {
            $tags->remove_attribute($attribute);
        }
    }
    $result['html_body'] = str_replace('src="https://rts-template.invalid/certificate-preview-url"',
        'src="{certificate_preview_url}"', $tags->get_updated_html());
    $links = new WP_HTML_Tag_Processor($result['html_body']);
    while ($links->next_tag('A')) {
        if (in_array($links->get_attribute('href'), $result['sources'], true)) {
            $links->set_attribute('href', 'https://rts-template.invalid/certificate-preview-url');
        }
    }
    $result['html_body'] = str_replace('href="https://rts-template.invalid/certificate-preview-url"',
        'href="{certificate_preview_url}"', $links->get_updated_html());
    return $result;
}

/** Replace the certificate image by source, regardless of editor-altered alt text. */
function rts_replace_email_certificate_image($html, $preview_url, $replacement)
{
    $preview_url = html_entity_decode((string) $preview_url, ENT_QUOTES, 'UTF-8');
    $replaced = false;

    return preg_replace_callback(
        '/<img\b[^>]*\/?\s*>/i',
        function ($matches) use ($preview_url, $replacement, &$replaced) {
            if ($replaced || !preg_match('/\bsrc\s*=\s*(["\'])(.*?)\1/i', $matches[0], $source_match)) {
                return $matches[0];
            }

            $image_source = html_entity_decode((string) $source_match[2], ENT_QUOTES, 'UTF-8');
            $is_certificate = '{certificate_preview_url}' === $image_source
                || untrailingslashit($image_source) === untrailingslashit($preview_url);
            if (!$is_certificate) {
                return $matches[0];
            }

            $replaced = true;
            return (string) $replacement;
        },
        (string) $html
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
            'certificate_status' => '',
            'certificate_issued_date' => '',
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

    // A certificate image selected in the no-code template editor must remain
    // recipient-aware. Older saves could replace the merge field with the raw,
    // blank certificate artwork; restore the dynamic field at send time.
    if (
        in_array($action_key, array('email_verification', 'founding_runner_certificate'), true)
        && $context['certificate_preview_url'] !== ''
    ) {
        $certificate_template = rts_normalize_email_certificate_template($html, $action_key, $context['certificate_preview_url']);
        $html = $certificate_template['html_body'];
        $asset_option = 'founding_runner_certificate' === $action_key
            ? 'rts_certificate_email_design_assets'
            : 'rts_verification_email_design_assets';

        // Template Images can select different artwork from the email-design
        // settings. Render that actual backplate with the current recipient;
        // merely swapping a list of known URLs leaves these uploads untouched.
        $backplate = $certificate_template['backplate_url'];
        if ($backplate !== '') {
            $context['certificate_preview_url'] = $backplate;
            if (!empty($context['_certificate_participant']) && class_exists('RTS_Registration')) {
                $registration = (new ReflectionClass('RTS_Registration'))->newInstanceWithoutConstructor();
                $context['certificate_preview_url'] = $registration->get_email_certificate_preview_url(
                    $context['_certificate_participant'], $asset_option, $backplate
                );
            }
        }

        // Detect an unrendered image using both the template-specific artwork
        // and shared settings. This also works when GD is absent on the host.
        $raw_previews = array($backplate, RTS_PLUGIN_URL . 'assets/certificate-template.jpg');
        foreach (array('rts_verification_email_design_assets', 'rts_certificate_email_design_assets') as $option) {
            $assets = get_option($option, array());
            if (is_array($assets) && !empty($assets['certificate_preview_image'])) {
                $raw_previews[] = $assets['certificate_preview_image'];
            }
        }
        $preview_url = html_entity_decode((string) $context['certificate_preview_url'], ENT_QUOTES, 'UTF-8');
        $uses_raw_backplate = false;
        foreach (array_filter($raw_previews) as $raw_preview) {
            if (untrailingslashit($preview_url) === untrailingslashit(html_entity_decode((string) $raw_preview, ENT_QUOTES, 'UTF-8'))) {
                $uses_raw_backplate = true;
                break;
            }
        }
        if ($uses_raw_backplate) {
            $fallback = rts_render_email_certificate_html_fallback($preview_url, $context);
            if ($fallback !== '') {
                $html = rts_replace_email_certificate_image($html, $preview_url, $fallback);
            }
        }
    }

    // Upgrade previously stored verification templates at render time. This
    // handles both compact original HTML and TinyMCE's space-normalized HTML.
    if ('email_verification' === $action_key) {
        $html = rts_enforce_verification_certificate_width($html);
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

function rts_get_transactional_email_editor_preview_context($action_key = '', $template_html = '')
{
    global $wpdb;

    $action_key = sanitize_key((string) $action_key);
    $asset_option = 'founding_runner_certificate' === $action_key
        ? 'rts_certificate_email_design_assets'
        : 'rts_verification_email_design_assets';
    $assets = get_option($asset_option, array());
    $assets = is_array($assets) ? $assets : array();
    $certificate_template = rts_normalize_email_certificate_template($template_html, $action_key);
    $template_backplate = $certificate_template['backplate_url'];
    $certificate_preview_url = $template_backplate !== '' ? $template_backplate : (!empty($assets['certificate_preview_image'])
        ? esc_url_raw($assets['certificate_preview_image'])
        : esc_url_raw(RTS_PLUGIN_URL . 'assets/certificate-template.jpg'));

    // Use the approved confirmation sample for verification emails and a
    // personalised render of the shared backplate for certificate emails.
    if (
        in_array($action_key, array('email_verification', 'founding_runner_certificate'), true)
        && function_exists('rts_init')
    ) {
        $participant = $wpdb->get_row(
            "SELECT * FROM {$wpdb->prefix}rts_participants
            WHERE first_name <> '' OR last_name <> ''
            ORDER BY id DESC
            LIMIT 1"
        );
        $plugin = rts_init();
        if (
            $participant
            && $plugin
            && !empty($plugin->registration)
            && is_callable(array($plugin->registration, 'get_email_certificate_preview_url'))
        ) {
            $personalised_preview_url = $plugin->registration->get_email_certificate_preview_url(
                $participant,
                $asset_option,
                $template_backplate
            );
            if ($personalised_preview_url !== '') {
                $certificate_preview_url = $personalised_preview_url;
            }
        }
    }

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
