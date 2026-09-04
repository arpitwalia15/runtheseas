<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render a luxury survey shell around an existing Fluent Forms multi-step form.
 * Each Fluent Forms step remains one question; section_ends maps those questions
 * to the visual tabs displayed in the sidebar.
 *
 * Example:
 * [rts_luxury_survey form_id="3"
 *   tabs="Cruise Experience|Running Experience|Itinerary Preferences|Amenities & Activities|Cabin Preferences|Food & Beverage|Final Thoughts"
 *   section_ends="4,8,11,14,17,20,24"
 *   section_images="101|102|103|104|105|106|107"
 *   question_images="301|302|303|..."
 *   frame_background_image="201"
 *   form_background_image="202"
 *   question_background_image="203"]
 * Image attributes accept Media Library attachment IDs or URLs. Attachment IDs
 * are preferred because they continue to work after the site is migrated.
 */
function rts_luxury_survey_shortcode($atts)
{
    $default_tabs = array(
        __('Cruise Experience', 'run-the-seas'),
        __('Running Experience', 'run-the-seas'),
        __('Itinerary Preferences', 'run-the-seas'),
        __('Amenities & Activities', 'run-the-seas'),
        __('Cabin Preferences', 'run-the-seas'),
        __('Food & Beverage', 'run-the-seas'),
        __('Final Thoughts', 'run-the-seas'),
    );
    $default_descriptions = array(
        __('Help us create the ultimate running cruise experience.', 'run-the-seas'),
        __('Tell us about your running journey and goals.', 'run-the-seas'),
        __('Choose the destinations and voyage style that inspire you.', 'run-the-seas'),
        __('Help shape the activities and amenities offered on board.', 'run-the-seas'),
        __('Share what makes your ideal cabin comfortable.', 'run-the-seas'),
        __('Tell us about your preferred dining and beverage experiences.', 'run-the-seas'),
        __('Complete your voyage with your final thoughts.', 'run-the-seas'),
    );

    $atts = shortcode_atts(array(
        'form_id'             => 0,
        'tabs'                => implode('|', $default_tabs),
        'section_ends'        => '4,8,11,14,17,20,24',
        'descriptions'        => implode('|', $default_descriptions),
        'section_images'      => '',
        'question_images'     => '',
        'hero_image'          => '',
        'header_left_image'   => '',
        'header_right_image'  => '',
        'progress_divider_image' => '',
        'background_image'    => '',
        'frame_background_image'    => '',
        'form_background_image'     => '',
        'question_background_image' => '',
        'sidebar_background_image'  => '',
        'brand'               => __('Run The Seas', 'run-the-seas'),
        'title'               => __('Survey Questions', 'run-the-seas'),
        'subtitle'            => __('Your voyage begins here', 'run-the-seas'),
        'progress_title'      => __('Your Progress', 'run-the-seas'),
        'sections_title'      => __('Survey Sections', 'run-the-seas'),
        'footer_message'      => __('Your feedback is helping create the inaugural Run The Seas voyage.', 'run-the-seas'),
    ), $atts, 'rts_luxury_survey');

    $form_id = absint($atts['form_id']);
    if (!$form_id) {
        return '<div class="rts-luxury-survey__notice">'
            . esc_html__('Add a Fluent Forms ID to the shortcode, for example: [rts_luxury_survey form_id="3"]', 'run-the-seas')
            . '</div>';
    }

    $parse_pipe_list = static function ($value) {
        $items = array_map('trim', explode('|', (string) $value));
        return array_values(array_filter($items, static function ($item) {
            return '' !== $item;
        }));
    };

    // Keep media-library URLs portable when a database backup is restored on
    // another domain. External image URLs are intentionally left unchanged.
    $normalize_image_url = static function ($value) {
        $value = trim((string) $value);
        if ('' === $value) {
            return '';
        }

        if (ctype_digit($value)) {
            return (string) wp_get_attachment_image_url(absint($value), 'full');
        }

        if (0 === strpos($value, '/')) {
            return esc_url_raw(home_url($value));
        }

        $url = esc_url_raw($value);
        $path = (string) wp_parse_url($url, PHP_URL_PATH);
        $content_path = trailingslashit((string) wp_parse_url(content_url('/'), PHP_URL_PATH));

        if ($path && $content_path && 0 === strpos($path, $content_path)) {
            $relative_path = ltrim(substr($path, strlen($content_path)), '/');
            return esc_url_raw(content_url($relative_path));
        }

        return $url;
    };

    $tabs = $parse_pipe_list($atts['tabs']);
    if (!$tabs) {
        $tabs = $default_tabs;
    }

    $section_ends = array_values(array_filter(array_map('absint', explode(',', (string) $atts['section_ends']))));
    if (count($section_ends) !== count($tabs)) {
        $section_ends = array_slice(array(4, 8, 11, 14, 17, 20, 24), 0, count($tabs));
    }
    if (count($section_ends) !== count($tabs)) {
        $section_ends = array();
        foreach ($tabs as $index => $unused) {
            $section_ends[] = ($index + 1) * 4;
        }
    }

    $descriptions = $parse_pipe_list($atts['descriptions']);
    $images = array_map($normalize_image_url, $parse_pipe_list($atts['section_images']));
    if (1 === count($images) && count($tabs) > 1) {
        $images = array_fill(0, count($tabs), $images[0]);
    }
    $sections = array();
    foreach ($tabs as $index => $tab) {
        $sections[] = array(
            'name'        => sanitize_text_field($tab),
            'end'         => $section_ends[$index],
            'description' => isset($descriptions[$index]) ? sanitize_text_field($descriptions[$index]) : '',
            'image'       => isset($images[$index]) ? $images[$index] : '',
        );
    }

    $background_variables = array(
        'background_image'          => '--rts-survey-background',
        'frame_background_image'    => '--rts-survey-frame-background',
        'form_background_image'     => '--rts-survey-form-background',
        'question_background_image' => '--rts-survey-question-background',
        'sidebar_background_image'  => '--rts-survey-sidebar-background',
    );
    $background_styles = array();
    foreach ($background_variables as $attribute => $variable) {
        if (!empty($atts[$attribute])) {
            $image_url = $normalize_image_url($atts[$attribute]);
            if ($image_url) {
                $background_styles[] = $variable . ':url("' . esc_url($image_url) . '")';
            }
        }
    }

    $design_assets = get_option('rts_survey_design_assets', array());
    $design_assets = is_array($design_assets) ? $design_assets : array();

    // `question_images` accepts one Media Library ID or URL per Fluent Forms
    // step. When it is empty, the section image (then the static hero image)
    // remains the banner fallback.
    // A shortcode value takes priority, while the Survey Design screen provides
    // the convenient site-wide default for this survey.
    $question_image_source = $atts['question_images'] ?: ($design_assets['question_images'] ?? '');
    $question_images = array_map($normalize_image_url, $parse_pipe_list($question_image_source));
    $header_left_image = $normalize_image_url($atts['header_left_image'] ?: ($design_assets['header_left_image'] ?? ''));
    $header_right_image = $normalize_image_url($atts['header_right_image'] ?: ($design_assets['header_right_image'] ?? ''));

    // The Captain's Suite layout uses one static banner instead of section-by-section artwork.
    // For existing shortcodes, the first former section image remains a useful fallback.
    $hero_image = $normalize_image_url($atts['hero_image'] ?: ($design_assets['hero_image'] ?? ''));
    if (!$hero_image && !empty($images[0])) {
        $hero_image = $images[0];
    }
    $hero_style = $hero_image
        ? "background-image:linear-gradient(rgba(0, 38, 82, .08), rgba(0, 38, 82, .08)),url('" . esc_url($hero_image) . "');"
        : '';
    $progress_divider_image = $normalize_image_url($atts['progress_divider_image'] ?: ($design_assets['progress_divider_image'] ?? ''));
    if ($progress_divider_image) {
        $background_styles[] = '--rts-survey-progress-divider:url("' . esc_url($progress_divider_image) . '")';
        $background_styles[] = '--rts-survey-progress-divider-fallback:none';
    }
    $progress_divider_class = $progress_divider_image ? ' rts-luxury-survey__progress-divider--image' : '';
    $background_style = $background_styles ? implode(';', $background_styles) . ';' : '';

    $form_shortcode = sprintf('[fluentform id="%d"]', $form_id);
    $form_markup = shortcode_exists('fluentform')
        ? do_shortcode($form_shortcode)
        : '<p class="rts-luxury-survey__form-error">' . esc_html__('Fluent Forms is not active.', 'run-the-seas') . '</p>';

    ob_start();
    ?>
    <section class="rts-luxury-survey rts-luxury-survey--captains-layout" data-rts-luxury-survey
        data-form-id="<?php echo esc_attr($form_id); ?>"
        data-sections="<?php echo esc_attr(wp_json_encode($sections)); ?>"
        data-question-images="<?php echo esc_attr(wp_json_encode($question_images)); ?>"
        style="<?php echo esc_attr($background_style); ?>">
        <div class="rts-luxury-survey__layout">
            <header class="rts-luxury-survey__brand" aria-label="<?php echo esc_attr($atts['brand'] . ' ' . $atts['title']); ?>">
                <?php if ($header_left_image) : ?><img class="rts-luxury-survey__brand-image rts-luxury-survey__brand-image--left" src="<?php echo esc_url($header_left_image); ?>" alt=""><?php else : ?><span class="rts-luxury-survey__brand-anchor" aria-hidden="true">&#9875;</span><?php endif; ?>
                <strong><?php echo esc_html($atts['brand']); ?></strong>
                <em><?php echo esc_html($atts['title']); ?></em>
                <?php if ($header_right_image) : ?><img class="rts-luxury-survey__brand-image rts-luxury-survey__brand-image--right" src="<?php echo esc_url($header_right_image); ?>" alt=""><?php endif; ?>
            </header>
            <aside class="rts-luxury-survey__sidebar">
                <section class="rts-luxury-survey__progress" aria-labelledby="rts-survey-progress-<?php echo esc_attr($form_id); ?>">
                    <div class="rts-luxury-survey__progress-meter">
                        <div class="rts-luxury-survey__progress-ring" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <strong data-rts-progress-percent>0%</strong>
                            <span><?php esc_html_e('Complete', 'run-the-seas'); ?></span>
                        </div>
                    </div>
                    <p id="rts-survey-progress-<?php echo esc_attr($form_id); ?>"><strong data-rts-question-count>0</strong> <?php esc_html_e('questions', 'run-the-seas'); ?></p>
                    <span class="rts-luxury-survey__progress-divider<?php echo esc_attr($progress_divider_class); ?>" aria-hidden="true"></span>
                </section>
                <p class="rts-luxury-survey__message">
                    <?php if (trim($atts['footer_message']) === __('Your feedback is helping create the inaugural Run The Seas voyage.', 'run-the-seas')) : ?>
                        <span><?php esc_html_e('Your feedback is', 'run-the-seas'); ?></span>
                        <span><?php esc_html_e('helping create the', 'run-the-seas'); ?></span>
                        <strong><?php esc_html_e('inaugural', 'run-the-seas'); ?></strong>
                        <span><?php esc_html_e('Run The Seas voyage.', 'run-the-seas'); ?></span>
                    <?php else : ?>
                        <?php echo esc_html($atts['footer_message']); ?>
                    <?php endif; ?>
                </p>
            </aside>

            <main class="rts-luxury-survey__content">
                <div class="rts-luxury-survey__section-hero" data-rts-section-hero data-rts-default-image="<?php echo esc_url($hero_image); ?>" role="img" aria-label="<?php echo esc_attr__('Run The Seas voyage', 'run-the-seas'); ?>" style="<?php echo esc_attr($hero_style); ?>"></div>

                <div class="rts-luxury-survey__form">
                    <?php echo $form_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </main>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_luxury_survey', 'rts_luxury_survey_shortcode');

/**
 * Render the complete  page for an Elementor
 * Shortcode widget. The island is decorative; all labels and values are live.
 *
 * Usage: [rts_virtual_marathon]
 */
function rts_virtual_marathon_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'limit'     => 12,
        'target'    => 42000,
        'map_image' => '',
    ), $atts, 'rts_virtual_marathon');

    $limit = min(12, max(3, absint($atts['limit'])));
    $target = rts_normalize_marathon_target($atts['target']);
    $map_url = $atts['map_image']
        ? esc_url($atts['map_image'])
        : RTS_PLUGIN_URL . 'assets/images/virtual-marathon-island.png';

    global $wpdb;
    $participants_table = $wpdb->prefix . 'rts_participants';
    $leaders = $wpdb->get_results($wpdb->prepare(
        "SELECT id, user_id, first_name, last_name, country, captain_miles_balance, total_captain_miles_earned
        FROM {$participants_table}
        WHERE total_captain_miles_earned > 0
        ORDER BY total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC
        LIMIT %d",
        $limit
    ));
    $current = rts_get_current_member_participant();
    $current_id = $current ? (int) $current->id : 0;
    $current_miles = $current ? max(0, (int) $current->total_captain_miles_earned) : 0;
    $current_rank = 0;
    if ($current && $current_miles > 0) {
        $current_rank = 1 + (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$participants_table}
            WHERE total_captain_miles_earned > %d
            OR (total_captain_miles_earned = %d AND captain_miles_balance > %d)
            OR (total_captain_miles_earned = %d AND captain_miles_balance = %d AND id < %d)",
            $current_miles,
            $current_miles,
            $current->captain_miles_balance,
            $current_miles,
            $current->captain_miles_balance,
            $current->id
        ));
    }

    $trophy_count = $current ? (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}rts_user_trophies
        WHERE participant_id = %d AND is_displayed = 1",
        $current->id
    )) : 0;
    $next_trophy = $current ? rts_get_current_member_next_trophy() : null;
    $remaining = $next_trophy ? max(0, (int) $next_trophy['miles'] - $current_miles) : 0;
    $percent = min(100, round($current_miles / $target * 100, 1));

    $activities = $wpdb->get_results(
        "SELECT t.activity_description, t.activity_date, p.first_name, p.last_name
        FROM {$wpdb->prefix}rts_timeline t
        INNER JOIN {$participants_table} p ON p.id = t.participant_id
        ORDER BY t.activity_date DESC, t.id DESC LIMIT 5"
    );

    $marker_positions = array(
        array(54, 12), array(41, 18), array(34, 31), array(31, 47),
        array(37, 63), array(49, 73), array(63, 72), array(72, 58),
        array(72, 39), array(65, 25), array(57, 20), array(45, 82),
    );
    $current_on_map = false;

    ob_start();
    ?>
    <section class="rts-virtual-marathon" aria-label="<?php esc_attr_e('42.2 km Referral Marathon Challenge', 'run-the-seas'); ?>">
        <header class="rts-virtual-marathon__header">
            <span><?php esc_html_e('Run The Seas', 'run-the-seas'); ?></span>
            <h1><?php esc_html_e('42.2 km Referral Marathon Challenge', 'run-the-seas'); ?></h1>
            <p><?php esc_html_e('Every kilometre counts. Every step moves you forward.', 'run-the-seas'); ?></p>
        </header>

        <div class="rts-virtual-marathon__grid">
            <aside class="rts-virtual-marathon__left">
                <section class="rts-vm-panel rts-vm-how">
                    <h2><?php esc_html_e('How it works', 'run-the-seas'); ?></h2>
                    <ul>
                        <li><b aria-hidden="true">&#127939;</b><?php esc_html_e('Earn 1 KM for every verified referral survey.', 'run-the-seas'); ?></li>
                        <li><b aria-hidden="true">&#127942;</b><?php esc_html_e('Unlock trophies at every milestone.', 'run-the-seas'); ?></li>
                        <li><b aria-hidden="true">&#128101;</b><?php esc_html_e('Climb the leaderboard and see your rank.', 'run-the-seas'); ?></li>
                        <li><b aria-hidden="true">&#9875;</b><?php esc_html_e('Every kilometre brings you closer to your next trophy.', 'run-the-seas'); ?></li>
                    </ul>
                </section>

                <section class="rts-vm-panel rts-vm-milestones">
                    <h2><?php esc_html_e('Milestone trophies', 'run-the-seas'); ?></h2>
                    <p><?php esc_html_e('Hit these milestones to unlock your trophies!', 'run-the-seas'); ?></p>
                    <ul>
                        <?php foreach (rts_get_captains_milestones() as $milestone) : ?>
                            <?php if (empty($milestone['miles'])) { continue; } ?>
                            <li class="<?php echo $current_miles >= $milestone['miles'] ? 'is-earned' : ''; ?>">
                                <span aria-hidden="true">&#127942;</span><strong><?php echo esc_html(rts_format_trophy_miles($milestone['miles'], $milestone['key'] ?? '')); ?></strong>
                                <small><?php echo esc_html(preg_replace('/\s+trophy$/i', '', $milestone['name'])); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </aside>

            <div class="rts-virtual-marathon__main">
                <div class="rts-vm-map" style="background-image:url('<?php echo esc_url($map_url); ?>')">
                    <section class="rts-vm-progress-card">
                        <h2><?php esc_html_e('Your progress', 'run-the-seas'); ?></h2>
                        <strong><?php echo esc_html(rts_format_miles($current_miles)); ?></strong>
                        <span><?php echo esc_html($current_rank ? sprintf(__('Rank #%d', 'run-the-seas'), $current_rank) : __('Begin your voyage', 'run-the-seas')); ?></span>
                        <b><?php echo esc_html(number_format_i18n($trophy_count)); ?> <?php esc_html_e('trophies earned', 'run-the-seas'); ?></b>
                        <?php if ($next_trophy) : ?>
                            <em><?php echo esc_html(sprintf(__('Next: %1$s — %2$s to go', 'run-the-seas'), $next_trophy['name'], rts_format_miles($remaining))); ?></em>
                        <?php endif; ?>
                        <i><u style="width:<?php echo esc_attr($percent); ?>%"></u></i>
                    </section>

                    <?php foreach ($leaders as $index => $leader) : ?>
                        <?php
                        $position = $marker_positions[$index] ?? $marker_positions[count($marker_positions) - 1];
                        $is_current = $current_id && $current_id === (int) $leader->id;
                        $current_on_map = $current_on_map || $is_current;
                        $name = rts_leaderboard_winner_short_name($leader->first_name, $leader->last_name, __('Captain', 'run-the-seas'));
                        ?>
                        <span class="rts-vm-marker<?php echo $is_current ? ' is-you' : ''; ?>" style="left:<?php echo esc_attr($position[0]); ?>%;top:<?php echo esc_attr($position[1]); ?>%">
                            <b><?php echo esc_html($index + 1); ?></b><em><?php echo $is_current ? esc_html__('You', 'run-the-seas') : esc_html($name); ?></em>
                            <small><?php echo esc_html(rts_format_miles((int) $leader->total_captain_miles_earned)); ?></small>
                        </span>
                    <?php endforeach; ?>

                    <?php if ($current && !$current_on_map) : ?>
                        <span class="rts-vm-marker is-you" style="left:45%;top:82%"><b><?php echo esc_html($current_rank ?: '—'); ?></b><em><?php esc_html_e('You', 'run-the-seas'); ?></em><small><?php echo esc_html(rts_format_miles($current_miles)); ?></small></span>
                    <?php endif; ?>

                    <p class="rts-vm-map__message"><?php esc_html_e('Your journey. Your pace. Your legacy.', 'run-the-seas'); ?></p>
                    <span class="rts-vm-map__start"><?php esc_html_e('Start / Finish', 'run-the-seas'); ?></span>
                </div>

                <div class="rts-vm-features">
                    <article><b>&#128101;</b><span><strong><?php esc_html_e('Share & earn', 'run-the-seas'); ?></strong><?php esc_html_e('Share your link and earn kilometres.', 'run-the-seas'); ?></span></article>
                    <article><b>&#128202;</b><span><strong><?php esc_html_e('Track progress', 'run-the-seas'); ?></strong><?php esc_html_e('Watch your rank rise.', 'run-the-seas'); ?></span></article>
                    <article><b>&#127942;</b><span><strong><?php esc_html_e('Unlock trophies', 'run-the-seas'); ?></strong><?php esc_html_e('Build your Trophy Room legacy.', 'run-the-seas'); ?></span></article>
                    <article><b>&#9875;</b><span><strong><?php esc_html_e('Celebrate', 'run-the-seas'); ?></strong><?php esc_html_e('Every captain matters.', 'run-the-seas'); ?></span></article>
                </div>
            </div>

            <aside class="rts-virtual-marathon__right">
                <section class="rts-vm-panel rts-vm-leaderboard">
                    <h2><?php esc_html_e('Leaderboard', 'run-the-seas'); ?></h2>
                    <p><?php esc_html_e('All captains', 'run-the-seas'); ?></p>
                    <?php echo rts_captains_leaderboard_shortcode(array('limit' => $limit)); ?>
                </section>

                <section class="rts-vm-panel rts-vm-activity">
                    <h2><?php esc_html_e('Recent activity', 'run-the-seas'); ?></h2>
                    <?php if ($activities) : ?>
                        <ul>
                            <?php foreach ($activities as $activity) : ?>
                                <li><span><b>&#9875;</b><?php echo esc_html(rts_leaderboard_winner_short_name($activity->first_name, $activity->last_name, __('Captain', 'run-the-seas'))); ?></span><small><?php echo esc_html(human_time_diff(strtotime($activity->activity_date), current_time('timestamp')) . ' ' . __('ago', 'run-the-seas')); ?></small></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p><?php esc_html_e('No recent activity yet.', 'run-the-seas'); ?></p>
                    <?php endif; ?>
                </section>
            </aside>
        </div>

        <footer class="rts-virtual-marathon__footer">
            <strong><?php esc_html_e('Every KM. Every captain. Every milestone.', 'run-the-seas'); ?></strong>
            <span><?php esc_html_e('Your journey. Your pace. Your legacy.', 'run-the-seas'); ?></span>
        </footer>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_virtual_marathon', 'rts_virtual_marathon_shortcode');
