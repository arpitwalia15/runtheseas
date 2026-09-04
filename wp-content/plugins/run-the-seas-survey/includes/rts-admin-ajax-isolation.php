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

    remove_all_actions('template_redirect');
}
add_action('admin_init', 'rts_isolate_core_inline_save_ajax', PHP_INT_MIN);
