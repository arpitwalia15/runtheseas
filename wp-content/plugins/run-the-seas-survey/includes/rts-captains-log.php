<?php

if (!defined('ABSPATH')) {
    exit;
}

/** Human-readable label for the audience saved on an admin broadcast. */
function rts_captains_log_audience_label($audience)
{
    $labels = array(
        'all'              => __('All Members', 'run-the-seas'),
        'verified_only'    => __('Verified Members', 'run-the-seas'),
        'runners_only'     => __('Runners', 'run-the-seas'),
        'non_runners_only' => __('Non-Runners', 'run-the-seas'),
    );

    return $labels[$audience] ?? __('Selected Members', 'run-the-seas');
}

/**
 * Return only admin broadcasts actually addressed to this participant.
 *
 * The admin platform writes one immutable email_outbox row per recipient.
 * Its JSON metadata contains both participant_id and draft_id, which lets the
 * log avoid recalculating an audience that may have changed since send time.
 */
function rts_get_captains_log_messages($participant)
{
    if (!$participant || empty($participant->id) || empty($participant->email)) {
        return array();
    }

    global $wpdb;
    $outbox_table = $wpdb->prefix . 'rts_email_outbox';
    $drafts_table = $wpdb->prefix . 'rts_email_drafts';
    $outbox_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $outbox_table));
    if ($outbox_table !== $outbox_exists) {
        return array();
    }

    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT id, subject, body_html, meta, created_at
         FROM {$outbox_table}
         WHERE to_email = %s AND kind = 'marketing'
         ORDER BY created_at DESC, id DESC",
        $participant->email
    ));
    if (!$rows) {
        return array();
    }

    $messages = array();
    $draft_ids = array();
    foreach ($rows as $row) {
        $meta = json_decode((string) $row->meta, true);
        if (!is_array($meta) || empty($meta['draft_id']) || !empty($meta['test'])) {
            continue;
        }
        if (!empty($meta['participant_id']) && (int) $meta['participant_id'] !== (int) $participant->id) {
            continue;
        }

        $row->draft_id = absint($meta['draft_id']);
        $messages[] = $row;
        $draft_ids[$row->draft_id] = $row->draft_id;
    }
    if (!$messages) {
        return array();
    }

    $drafts = array();
    $drafts_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $drafts_table));
    if ($drafts_table === $drafts_exists && $draft_ids) {
        $placeholders = implode(',', array_fill(0, count($draft_ids), '%d'));
        $query = $wpdb->prepare(
            "SELECT id, audience_filter, category, created_by, bulk_sent, bulk_sent_at
             FROM {$drafts_table} WHERE id IN ({$placeholders})",
            array_values($draft_ids)
        );
        foreach ((array) $wpdb->get_results($query) as $draft) {
            $drafts[(int) $draft->id] = $draft;
        }
    }

    foreach ($messages as $message) {
        $draft = $drafts[(int) $message->draft_id] ?? null;
        $message->audience_label = rts_captains_log_audience_label($draft->audience_filter ?? 'selected');
        $message->category = $draft->category ?? 'general';
        $message->sent_by = $draft->created_by ?? '';
    }

    return $messages;
}

/** Render the private Captain's Log message center. Usage: [rts_captains_log]. */
function rts_captains_log_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'suite_page'  => 'captains-suite',
        'scene_image' => '',
    ), $atts, 'rts_captains_log');

    if (!is_user_logged_in()) {
        $destination = is_singular() ? get_permalink(get_queried_object_id()) : rts_get_member_page_url('captains-log');
        return '<section class="rts-captains-log-notice"><p>'
            . sprintf(
                wp_kses_post(__('Please <a href="%s">log in</a> to open your Captain\'s Log.', 'run-the-seas')),
                esc_url(rts_get_member_login_url($destination))
            )
            . '</p></section>';
    }

    $participant = rts_get_current_member_participant();
    if (!$participant) {
        return '<section class="rts-captains-log-notice"><p>'
            . esc_html__('Your member record could not be found.', 'run-the-seas')
            . '</p></section>';
    }

    $all_messages = rts_get_captains_log_messages($participant);
    $requested_id = absint(wp_unslash($_GET['rts_message'] ?? 0));
    $requested_per_page = absint(wp_unslash($_GET['rts_log_per_page'] ?? 5));
    $messages_per_page = 2 === $requested_per_page ? 2 : 5;
    $total_messages = count($all_messages);
    $total_pages = max(1, (int) ceil($total_messages / $messages_per_page));
    $current_page = max(1, absint(wp_unslash($_GET['rts_log_page'] ?? 1)));
    $current_page = min($current_page, $total_pages);
    $selected = null;

    if ($requested_id) {
        foreach ($all_messages as $message_index => $message) {
            if ((int) $message->id === $requested_id) {
                $selected = $message;
                $current_page = ((int) floor($message_index / $messages_per_page)) + 1;
                break;
            }
        }
    }

    $messages = array_slice($all_messages, ($current_page - 1) * $messages_per_page, $messages_per_page);
    if (!$selected && $messages) {
        $selected = $messages[0];
    }

    $design_assets = get_option('rts_captains_log_design_assets', array());
    $design_assets = is_array($design_assets) ? $design_assets : array();
    $asset = static function ($key) use ($design_assets) {
        return isset($design_assets[$key]) ? esc_url_raw((string) $design_assets[$key]) : '';
    };

    // A shortcode scene_image remains available as a per-page override.
    $complete_background_image = $asset('background_image');
    $scene_image = esc_url_raw((string) $atts['scene_image']);
    if (!$scene_image) {
        $scene_image = $complete_background_image;
    }

    $button_image = $asset('button_image');
    $messages_left_art = $asset('messages_left_art_image');
    $messages_right_art = $asset('messages_right_art_image');
    $logo_image = $asset('logo_image');
    $message_center_left_art = $asset('message_center_left_art_image');
    $message_center_right_art = $asset('message_center_right_art_image');
    $message_center_below_art = $asset('message_center_below_art_image');
    $column_center_art = $asset('column_center_art_image');
    $message_below_art = $asset('message_below_art_image');
    $selected_message_art = $asset('selected_message_art_image');
    $header_top_spacing = isset($design_assets['header_top_spacing'])
        ? min(160, absint($design_assets['header_top_spacing']))
        : 40;

    $scene_classes = array('rts-captains-log');
    $scene_styles = array();
    if ($scene_image) {
        $scene_classes[] = 'rts-captains-log--has-scene';
        $scene_styles[] = '--rts-captains-log-scene:url("' . str_replace('"', '%22', $scene_image) . '")';
    }
    if ($complete_background_image && $scene_image === $complete_background_image) {
        $scene_classes[] = 'rts-captains-log--has-complete-background';
        $scene_styles[] = '--rts-captains-log-header-top:' . $header_top_spacing . 'px';
    }
    if ($selected_message_art) {
        $scene_classes[] = 'rts-captains-log--has-selected-art';
    }
    if ($message_below_art) {
        $scene_classes[] = 'rts-captains-log--has-message-divider-art';
    }
    if ($column_center_art) {
        $scene_classes[] = 'rts-captains-log--has-column-art';
    }

    $back_url = rts_get_member_page_url($atts['suite_page']);
    $page_url = is_singular() ? get_permalink(get_queried_object_id()) : rts_get_member_page_url('captains-log');

    ob_start();
    ?>
    <section class="<?php echo esc_attr(implode(' ', $scene_classes)); ?>"<?php echo $scene_styles ? ' style="' . esc_attr(implode(';', $scene_styles)) . '"' : ''; ?> data-messages-per-page="<?php echo esc_attr($messages_per_page); ?>" aria-labelledby="rts-captains-log-title">
        <div class="rts-captains-log__book">
            <header class="rts-captains-log__header">
                <?php if ($logo_image) : ?>
                    <img class="rts-captains-log__logo" src="<?php echo esc_url($logo_image); ?>" alt="">
                <?php else : ?>
                    <span class="rts-captains-log__crest" aria-hidden="true">&#9875;</span>
                <?php endif; ?>
                <h1 id="rts-captains-log-title"><?php esc_html_e("Captain's Log", 'run-the-seas'); ?></h1>
                <p<?php echo ($message_center_left_art || $message_center_right_art) ? ' class="has-custom-art"' : ''; ?>>
                    <?php if ($message_center_left_art) : ?><img class="rts-captains-log__heading-art" src="<?php echo esc_url($message_center_left_art); ?>" alt=""><?php endif; ?>
                    <span><?php esc_html_e('Message Center', 'run-the-seas'); ?></span>
                    <?php if ($message_center_right_art) : ?><img class="rts-captains-log__heading-art" src="<?php echo esc_url($message_center_right_art); ?>" alt=""><?php endif; ?>
                </p>
                <?php if ($message_center_below_art) : ?>
                    <img class="rts-captains-log__header-below-art" src="<?php echo esc_url($message_center_below_art); ?>" alt="">
                <?php endif; ?>
            </header>

            <div class="rts-captains-log__pages">
                <?php if ($column_center_art) : ?>
                    <img class="rts-captains-log__column-art" src="<?php echo esc_url($column_center_art); ?>" alt="">
                <?php endif; ?>
                <aside class="rts-captains-log__index" aria-label="<?php esc_attr_e('Messages', 'run-the-seas'); ?>">
                    <h2<?php echo ($messages_left_art || $messages_right_art) ? ' class="has-custom-art"' : ''; ?>>
                        <?php if ($messages_left_art) : ?><img class="rts-captains-log__heading-art" src="<?php echo esc_url($messages_left_art); ?>" alt=""><?php endif; ?>
                        <span><?php esc_html_e('Messages', 'run-the-seas'); ?></span>
                        <?php if ($messages_right_art) : ?><img class="rts-captains-log__heading-art" src="<?php echo esc_url($messages_right_art); ?>" alt=""><?php endif; ?>
                    </h2>
                    <?php if ($messages) : ?>
                        <ol>
                            <?php foreach ($messages as $message) :
                                $is_selected = $selected && (int) $selected->id === (int) $message->id;
                                $date = mysql2date(get_option('date_format'), $message->created_at);
                                $time = mysql2date(get_option('time_format'), $message->created_at);
                                $message_url = add_query_arg(array(
                                    'rts_message'  => (int) $message->id,
                                    'rts_log_page' => $current_page,
                                    'rts_log_per_page' => $messages_per_page,
                                ), $page_url);
                                ?>
                                <li<?php echo $is_selected ? ' class="is-active"' : ''; ?>>
                                    <?php if ($selected_message_art) : ?>
                                        <img class="rts-captains-log__row-frame" src="<?php echo esc_url($selected_message_art); ?>" alt="">
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url($message_url); ?>"<?php echo $is_selected ? ' class="is-active" aria-current="page"' : ''; ?>>
                                        <span class="rts-captains-log__list-subject"><?php echo esc_html($message->subject ?: __('Captain’s Message', 'run-the-seas')); ?></span>
                                        <span class="rts-captains-log__list-date"><?php echo esc_html($date); ?><small><?php echo esc_html($time); ?></small></span>
                                        <span class="rts-captains-log__list-audience"><?php echo esc_html(sprintf(__('To: %s', 'run-the-seas'), $message->audience_label)); ?></span>
                                    </a>
                                    <?php if ($message_below_art) : ?>
                                        <span class="rts-captains-log__row-divider" aria-hidden="true"><img src="<?php echo esc_url($message_below_art); ?>" alt=""></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                        <?php if ($total_pages > 1) : ?>
                            <nav class="rts-captains-log__pagination" aria-label="<?php esc_attr_e('Message pages', 'run-the-seas'); ?>">
                                <?php if ($current_page > 1) : ?>
                                    <a class="prev page-numbers" href="<?php echo esc_url(add_query_arg(array('rts_log_page' => $current_page - 1, 'rts_log_per_page' => $messages_per_page), $page_url)); ?>" aria-label="<?php esc_attr_e('Previous message page', 'run-the-seas'); ?>">&#8249;</a>
                                <?php endif; ?>
                                <?php
                                $ellipsis_rendered = false;
                                for ($page_number = 1; $page_number <= $total_pages; $page_number++) :
                                    $show_page = 1 === $page_number
                                        || $total_pages === $page_number
                                        || abs($page_number - $current_page) <= 1;
                                    if (!$show_page) {
                                        if (!$ellipsis_rendered) {
                                            echo '<span class="page-numbers dots" aria-hidden="true">&hellip;</span>';
                                            $ellipsis_rendered = true;
                                        }
                                        continue;
                                    }
                                    $ellipsis_rendered = false;
                                    if ($page_number === $current_page) :
                                        ?>
                                        <span class="page-numbers current" aria-current="page"><?php echo esc_html($page_number); ?></span>
                                    <?php else : ?>
                                        <a class="page-numbers" href="<?php echo esc_url(add_query_arg(array('rts_log_page' => $page_number, 'rts_log_per_page' => $messages_per_page), $page_url)); ?>"><?php echo esc_html($page_number); ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <?php if ($current_page < $total_pages) : ?>
                                    <a class="next page-numbers" href="<?php echo esc_url(add_query_arg(array('rts_log_page' => $current_page + 1, 'rts_log_per_page' => $messages_per_page), $page_url)); ?>" aria-label="<?php esc_attr_e('Next message page', 'run-the-seas'); ?>">&#8250;</a>
                                <?php endif; ?>
                            </nav>
                        <?php endif; ?>
                    <?php else : ?>
                        <p class="rts-captains-log__empty-index"><?php esc_html_e('No messages yet.', 'run-the-seas'); ?></p>
                    <?php endif; ?>
                </aside>

                <article class="rts-captains-log__message" aria-live="polite">
                    <?php if ($selected) :
                        $selected_date = mysql2date(get_option('date_format'), $selected->created_at);
                        $selected_time = mysql2date(get_option('time_format'), $selected->created_at);
                        ?>
                        <header>
                            <h2><?php echo esc_html($selected->subject ?: __('Captain’s Message', 'run-the-seas')); ?></h2>
                            <div class="rts-captains-log__meta">
                                <span><?php echo esc_html(sprintf(__('To: %s', 'run-the-seas'), $selected->audience_label)); ?></span>
                                <time datetime="<?php echo esc_attr(mysql2date('c', $selected->created_at)); ?>"><?php echo esc_html($selected_date . ', ' . $selected_time); ?></time>
                            </div>
                        </header>
                        <div class="rts-captains-log__body"><?php echo wp_kses_post($selected->body_html); ?></div>
                    <?php else : ?>
                        <div class="rts-captains-log__empty-message">
                            <span aria-hidden="true">&#9993;</span>
                            <h2><?php esc_html_e('Your Log Is Ready', 'run-the-seas'); ?></h2>
                            <p><?php esc_html_e('Broadcasts addressed to you will appear here.', 'run-the-seas'); ?></p>
                        </div>
                    <?php endif; ?>
                </article>
            </div>
        </div>

        <a class="rts-captains-log__back<?php echo $button_image ? ' has-custom-art' : ''; ?>" href="<?php echo esc_url($back_url); ?>">
            <?php if ($button_image) : ?>
                <img src="<?php echo esc_url($button_image); ?>" alt="<?php esc_attr_e("Back to Captain's Suite", 'run-the-seas'); ?>">
            <?php else : ?>
                &#8592; <?php esc_html_e("Back to Captain's Suite", 'run-the-seas'); ?>
            <?php endif; ?>
        </a>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_captains_log', 'rts_captains_log_shortcode');
