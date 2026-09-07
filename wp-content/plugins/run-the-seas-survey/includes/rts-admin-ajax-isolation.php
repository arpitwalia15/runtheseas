<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Keep WordPress core admin-AJAX saves isolated from front-end page routing.
 *
 * Quick Edit expects the `inline-save` request to contain only the updated
 * table row. If a theme or community router runs `template_redirect` during
 * that request, a complete front-end page can be returned instead and
 * WordPress displays the stripped page source as an error below Quick Edit.
 *
 * Core's admin-ajax endpoint does not use `template_redirect`, so removing its
 * callbacks for these core row-save requests cannot affect the save itself.
 */
function rts_isolate_core_inline_save_ajax()
{
    if (!wp_doing_ajax()) {
        return;
    }

    $action = isset($_REQUEST['action'])
        ? sanitize_key(wp_unslash($_REQUEST['action']))
        : '';

    if (!in_array($action, array('inline-save', 'inline-save-tax'), true)) {
        return;
    }

    /*
     * A BuddyNext backing page can contain a hub shortcode such as
     * [buddynext_auth]. Some admin list-table integrations inspect/render the
     * saved content while WordPress is building Quick Edit's AJAX response.
     * BuddyNext's auth shortcode redirects a logged-in viewer to /activity/;
     * when it runs here that redirect replaces the <tr> response with a 302
     * and the complete Activity page.
     *
     * Short-circuit only BuddyNext's front-end hub shortcodes, and only for
     * WordPress's two core inline-save actions. The post is still saved by
     * core and normal front-end rendering is untouched.
     */
    add_filter('pre_do_shortcode_tag', 'rts_skip_buddynext_hub_shortcode_during_inline_save', PHP_INT_MIN, 4);

    remove_all_actions('template_redirect');
}
add_action('admin_init', 'rts_isolate_core_inline_save_ajax', PHP_INT_MIN);

/**
 * Prevent front-end BuddyNext hub rendering inside a core Quick Edit request.
 *
 * @param false|string $return Short-circuit value. False runs the shortcode.
 * @param string       $tag    Shortcode tag currently being evaluated.
 * @return false|string
 */
function rts_skip_buddynext_hub_shortcode_during_inline_save($return, $tag)
{
    $hub_shortcodes = array(
        'buddynext_activity',
        'buddynext_people',
        'buddynext_spaces',
        'buddynext_messages',
        'buddynext_notifications',
        'buddynext_auth',
        'buddynext_community_admin',
        'buddynext_search',
    );

    return in_array($tag, $hub_shortcodes, true) ? '' : $return;
}
