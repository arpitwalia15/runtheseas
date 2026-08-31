<?php

if (!defined('ABSPATH')) {
    exit;
}

/** Convert a saved participant country into a flag emoji for the leaderboard. */
function rts_country_flag($country)
{
    $country = strtoupper(trim(wp_strip_all_tags($country)));
    $country_codes = array(
        'USA' => 'US', 'UNITED STATES' => 'US', 'UNITED STATES OF AMERICA' => 'US',
        'UK' => 'GB', 'UNITED KINGDOM' => 'GB', 'GREAT BRITAIN' => 'GB', 'ENGLAND' => 'GB',
        'CANADA' => 'CA', 'INDIA' => 'IN', 'AUSTRALIA' => 'AU', 'NEW ZEALAND' => 'NZ',
        'IRELAND' => 'IE', 'SCOTLAND' => 'GB', 'WALES' => 'GB',
        'FRANCE' => 'FR', 'GERMANY' => 'DE', 'ITALY' => 'IT', 'SPAIN' => 'ES',
        'PORTUGAL' => 'PT', 'NETHERLANDS' => 'NL', 'BELGIUM' => 'BE', 'SWITZERLAND' => 'CH',
        'AUSTRIA' => 'AT', 'SWEDEN' => 'SE', 'NORWAY' => 'NO', 'DENMARK' => 'DK',
        'FINLAND' => 'FI', 'POLAND' => 'PL', 'GREECE' => 'GR', 'TURKEY' => 'TR',
        'SOUTH AFRICA' => 'ZA', 'NIGERIA' => 'NG', 'KENYA' => 'KE', 'EGYPT' => 'EG',
        'BRAZIL' => 'BR', 'MEXICO' => 'MX', 'ARGENTINA' => 'AR', 'CHILE' => 'CL',
        'COLOMBIA' => 'CO', 'JAPAN' => 'JP', 'CHINA' => 'CN', 'SOUTH KOREA' => 'KR',
        'SINGAPORE' => 'SG', 'MALAYSIA' => 'MY', 'PHILIPPINES' => 'PH', 'INDONESIA' => 'ID',
        'THAILAND' => 'TH', 'UNITED ARAB EMIRATES' => 'AE', 'UAE' => 'AE',
    );
    $code = isset($country_codes[$country]) ? $country_codes[$country] : $country;
    if (!preg_match('/^[A-Z]{2}$/', $code)) {
        return '🌐';
    }

    return html_entity_decode(sprintf(
        '&#x%X;&#x%X;',
        0x1F1E6 + ord($code[0]) - ord('A'),
        0x1F1E6 + ord($code[1]) - ord('A')
    ), ENT_NOQUOTES, 'UTF-8');
}

/** Render the public Captain's Miles leaderboard as compact sidebar rows. */
function rts_captains_leaderboard_shortcode($atts)
{
    $atts = shortcode_atts(array('limit' => 5), $atts, 'rts_captains_leaderboard');
    $limit = min(20, max(1, absint($atts['limit'])));
    global $wpdb;
    $table = $wpdb->prefix . 'rts_participants';
    $leaders = $wpdb->get_results($wpdb->prepare(
        "SELECT id, first_name, last_name, country, captain_miles_balance, total_captain_miles_earned
         FROM {$table}
         WHERE total_captain_miles_earned > 0
         ORDER BY total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC
         LIMIT %d",
        $limit
    ));

    if (empty($leaders)) {
        return '<p class="rts-captains-leaderboard__empty">' . esc_html__('No Captain’s Miles have been earned yet.', 'run-the-seas') . '</p>';
    }

    $current = rts_get_current_member_participant();
    $current_id = $current ? (int) $current->id : 0;
    $current_in_list = false;
    $output = '<div class="rts-captains-leaderboard" role="list"><div class="rts-captains-leaderboard__heading">'
        . '<span>' . esc_html__('Rank', 'run-the-seas') . '</span><span>' . esc_html__('Captain', 'run-the-seas') . '</span>'
        . '<span>' . esc_html__('KM', 'run-the-seas') . '</span></div>';

    foreach ($leaders as $index => $leader) {
        $is_current = $current_id && $current_id === (int) $leader->id;
        $current_in_list = $current_in_list || $is_current;
        $name = trim($leader->first_name . ' ' . $leader->last_name) ?: __('Captain', 'run-the-seas');
        $output .= '<div class="rts-captains-leaderboard__row' . ($is_current ? ' rts-captains-leaderboard__you' : '') . '" role="listitem">'
            . '<span class="rts-captains-leaderboard__rank">' . esc_html(number_format_i18n($index + 1)) . '</span>'
            . '<span class="rts-captains-leaderboard__captain"><span class="rts-captains-leaderboard__flag" role="img" aria-label="'
            . esc_attr($leader->country ?: __('Country unavailable', 'run-the-seas')) . '">' . esc_html(rts_country_flag($leader->country))
            . '</span>' . esc_html($name) . ($is_current ? ' ' . esc_html__('(You)', 'run-the-seas') : '') . '</span>'
            . '<span class="rts-captains-leaderboard__miles">' . esc_html(rts_format_miles((int) $leader->total_captain_miles_earned)) . '</span></div>';
    }

    if ($current && !$current_in_list && (int) $current->total_captain_miles_earned > 0) {
        $rank = 1 + (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table}
             WHERE total_captain_miles_earned > %d
             OR (total_captain_miles_earned = %d AND captain_miles_balance > %d)
             OR (total_captain_miles_earned = %d AND captain_miles_balance = %d AND id < %d)",
            $current->total_captain_miles_earned,
            $current->total_captain_miles_earned,
            $current->captain_miles_balance,
            $current->total_captain_miles_earned,
            $current->captain_miles_balance,
            $current->id
        ));
        $name = trim($current->first_name . ' ' . $current->last_name) ?: __('Captain', 'run-the-seas');
        $output .= '<div class="rts-captains-leaderboard__row rts-captains-leaderboard__you" role="listitem">'
            . '<span class="rts-captains-leaderboard__rank">' . esc_html(number_format_i18n($rank)) . '</span>'
            . '<span class="rts-captains-leaderboard__captain"><span class="rts-captains-leaderboard__flag" role="img" aria-label="'
            . esc_attr($current->country ?: __('Country unavailable', 'run-the-seas')) . '">' . esc_html(rts_country_flag($current->country))
            . '</span>' . esc_html($name) . ' ' . esc_html__('(You)', 'run-the-seas') . '</span>'
            . '<span class="rts-captains-leaderboard__miles">' . esc_html(rts_format_miles((int) $current->total_captain_miles_earned)) . '</span></div>';
    }

    return $output . '</div>';
}
add_shortcode('rts_captains_leaderboard', 'rts_captains_leaderboard_shortcode');

/** A standalone live title/subtitle block for an Elementor leaderboard hero. */
function rts_leaderboard_header_shortcode($atts)
{
    $atts = shortcode_atts(array('target' => 42200), $atts, 'rts_leaderboard_header');
    $target = max(1, absint($atts['target']));

    return '<div class="rts-leaderboard-header"><p class="rts-leaderboard-header__live"><span></span>'
        . esc_html__('Live Leaderboard', 'run-the-seas') . '</p><h2>'
        . esc_html__('The', 'run-the-seas') . ' <strong>' . esc_html(rts_format_miles($target)) . '</strong> '
        . esc_html__('Referral Marathon Challenge', 'run-the-seas') . '</h2><p>'
        . esc_html__('Every verified referral moves you 1K closer to the finish line.', 'run-the-seas') . '</p><small>'
        . esc_html(sprintf(__('Last updated: %s', 'run-the-seas'), current_time(get_option('time_format')))) . '</small></div>';
}
add_shortcode('rts_leaderboard_header', 'rts_leaderboard_header_shortcode');

/** The explanatory left-side panel for an Elementor leaderboard template. */
function rts_leaderboard_how_it_works_shortcode()
{
    $items = array(
        __('Every verified referral earns 1K Captain’s Miles.', 'run-the-seas'),
        __('Your kilometres accumulate automatically.', 'run-the-seas'),
        __('Reach milestones to unlock Run The Seas trophies.', 'run-the-seas'),
        __('Standings update as verified referrals are recorded.', 'run-the-seas'),
    );
    $output = '<div class="rts-leaderboard-how-it-works"><ul>';
    foreach ($items as $item) {
        $output .= '<li>' . esc_html($item) . '</li>';
    }
    return $output . '</ul></div>';
}
add_shortcode('rts_leaderboard_how_it_works', 'rts_leaderboard_how_it_works_shortcode');

/** The top-three Captain podium for an Elementor leaderboard template. */
function rts_leaderboard_podium_shortcode()
{
    global $wpdb;
    $table = $wpdb->prefix . 'rts_participants';
    $leaders = $wpdb->get_results(
        "SELECT id, user_id, first_name, last_name, country, total_captain_miles_earned
         FROM {$table} WHERE total_captain_miles_earned > 0
         ORDER BY total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC LIMIT 3"
    );
    if (empty($leaders)) {
        return '<p class="rts-leaderboard-empty">' . esc_html__('No Captain’s Miles have been earned yet.', 'run-the-seas') . '</p>';
    }

    $output = '<div class="rts-leaderboard-podium">';
    foreach (array(1, 0, 2) as $index) {
        if (!isset($leaders[$index])) {
            continue;
        }
        $leader = $leaders[$index];
        $rank = $index + 1;
        $name = trim($leader->first_name . ' ' . $leader->last_name) ?: __('Captain', 'run-the-seas');
        $output .= '<article class="rts-leaderboard-podium__captain rts-leaderboard-podium__captain--' . esc_attr($rank) . '">'
            . '<span class="rts-leaderboard-podium__rank">' . esc_html($rank) . '</span>'
            . get_avatar((int) $leader->user_id, 88, '', '', array('class' => 'rts-leaderboard-podium__avatar'))
            . '<strong>' . esc_html($name) . '</strong><span>' . esc_html(rts_country_flag($leader->country)) . ' '
            . esc_html(rts_format_miles((int) $leader->total_captain_miles_earned)) . '</span></article>';
    }
    return $output . '</div>';
}
add_shortcode('rts_leaderboard_podium', 'rts_leaderboard_podium_shortcode');

/** Get one ranked Captain for individually designed Elementor podium areas. */
function rts_get_leaderboard_ranked_captain($rank)
{
    $rank = max(1, absint($rank));
    global $wpdb;
    $table = $wpdb->prefix . 'rts_participants';

    return $wpdb->get_row($wpdb->prepare(
        "SELECT id, user_id, first_name, last_name, country, total_captain_miles_earned
         FROM {$table} WHERE total_captain_miles_earned > 0
         ORDER BY total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC
         LIMIT %d, 1",
        $rank - 1
    ));
}

/**
 * Shorten a leaderboard name for the winner-trophy pedestal.
 *
 * Examples: "Sarah Miller" becomes "Sarah M." and "Developer New" becomes
 * "Developer N.". A single-word name is kept, but capped so it still fits.
 */
function rts_leaderboard_winner_short_name($first_name, $last_name, $fallback = '')
{
    $first_name = trim(wp_strip_all_tags((string) $first_name));
    $last_name = trim(wp_strip_all_tags((string) $last_name));

    if ('' !== $first_name && '' !== $last_name) {
        $first_length = function_exists('mb_strlen') ? mb_strlen($first_name, 'UTF-8') : strlen($first_name);
        $first_initial = function_exists('mb_substr') ? mb_substr($first_name, 0, 1, 'UTF-8') : substr($first_name, 0, 1);
        $last_initial = function_exists('mb_substr') ? mb_substr($last_name, 0, 1, 'UTF-8') : substr($last_name, 0, 1);

        // Keep familiar short names, but turn unusually long names into
        // initials so they do not overflow the trophy pedestal.
        if ($first_length > 10) {
            return strtoupper($first_initial) . '. ' . $last_name;
        }

        return $first_name . ' ' . strtoupper($last_initial) . '.';
    }

    $name = $first_name ?: ($last_name ?: trim(wp_strip_all_tags((string) $fallback)));
    return function_exists('mb_strimwidth')
        ? mb_strimwidth($name, 0, 18, '…', 'UTF-8')
        : substr($name, 0, 18);
}

/** Return the compact milestone label that is engraved on the winner's cup. */
function rts_leaderboard_winner_trophy_label($trophy)
{
    $key = sanitize_key($trophy->trophy_key ?? ($trophy->trophy_type ?? ''));
    $labels = array(
        'founder'                => 'FR',
        'founding-runner'        => 'FR',
        'founding-runner-trophy' => 'FR',
        '5k'  => '5K',
        '10k' => '10K',
        '15k' => '15K',
        '20k' => '20K',
        '21k' => '21K',
        '25k' => '25K',
        '30k' => '30K',
        '35k' => '35K',
        '42k' => '42K',
    );
    if (isset($labels[$key])) {
        return $labels[$key];
    }

    $name = trim(preg_replace('/\\s+trophy\\s*$/i', '', (string) ($trophy->trophy_name ?? '')));
    return '' !== $name ? $name : 'FR';
}

/**
 * Render a personalised trophy image for the Captain's Miles rank-one leader.
 *
 * Usage: [rts_leaderboard_winner_trophy]
 * Optional attributes: size="520", class="my-extra-class", empty="".
 */
function rts_leaderboard_winner_trophy_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'size'  => 520,
        'class' => '',
        'empty' => '',
    ), $atts, 'rts_leaderboard_winner_trophy');

    $leader = rts_get_leaderboard_ranked_captain(1);
    if (!$leader) {
        return esc_html($atts['empty']);
    }

    global $wpdb;
    $trophy = $wpdb->get_row($wpdb->prepare(
        "SELECT trophy_name, trophy_key, trophy_type
        FROM {$wpdb->prefix}rts_user_trophies
        WHERE participant_id = %d AND is_displayed = 1
        ORDER BY miles_required DESC, earned_date DESC, id DESC
        LIMIT 1",
        $leader->id
    ));

    $fallback_name = '';
    if (!empty($leader->user_id)) {
        $user = get_userdata((int) $leader->user_id);
        $fallback_name = $user ? $user->display_name : '';
    }
    $member_name = rts_leaderboard_winner_short_name($leader->first_name, $leader->last_name, $fallback_name);
    $trophy_label = rts_leaderboard_winner_trophy_label($trophy ?: (object) array());
    $size = min(800, max(240, absint($atts['size'])));
    $classes = 'rts-leaderboard-winner-trophy';
    if ('' !== $atts['class']) {
        $classes .= ' ' . sanitize_html_class($atts['class']);
    }
    $image_url = RTS_PLUGIN_URL . 'assets/images/leaderboard-winner-trophy.png';
    $alt = sprintf(__('%1$s trophy awarded to leaderboard leader %2$s', 'run-the-seas'), $trophy_label, $member_name);

    return '<figure class="' . esc_attr($classes) . '" style="--rts-leaderboard-winner-trophy-size:' . esc_attr($size) . 'px">'
        . '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async">'
        . '<figcaption><span class="rts-leaderboard-winner-trophy__cup" aria-label="' . esc_attr($trophy_label) . '">'
        . esc_html($trophy_label) . '</span><span class="rts-leaderboard-winner-trophy__name">'
        . esc_html($member_name) . '</span></figcaption></figure>';
}
add_shortcode('rts_leaderboard_winner_trophy', 'rts_leaderboard_winner_trophy_shortcode');

/**
 * Render an individual podium Captain or one of their fields.
 * field="card" (default), "name", "flag", "country", "distance", "avatar", or "rank".
 */
function rts_leaderboard_rank_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'rank'  => 1,
        'field' => 'card',
        'size'  => 88,
    ), $atts, 'rts_leaderboard_rank');
    $rank = max(1, min(3, absint($atts['rank'])));
    $leader = rts_get_leaderboard_ranked_captain($rank);
    if (!$leader) {
        return '';
    }

    $field = sanitize_key($atts['field']);
    // $name = trim($leader->first_name . ' ' . $leader->last_name) ?: __('Captain', 'run-the-seas');
     $name = trim($leader->first_name) ?: __('Captain', 'run-the-seas');
    $flag = rts_country_flag($leader->country);
    $distance = rts_format_miles((int) $leader->total_captain_miles_earned);
    $avatar = get_avatar((int) $leader->user_id, max(24, absint($atts['size'])), '', '', array('class' => 'rts-leaderboard-rank__avatar'));

    if ('name' === $field) {
        return esc_html($name);
    }
    if ('flag' === $field) {
        return '<span class="rts-leaderboard-rank__flag" role="img" aria-label="' . esc_attr($leader->country ?: __('Country unavailable', 'run-the-seas')) . '">' . esc_html($flag) . '</span>';
    }
    if ('country' === $field) {
        return esc_html($leader->country);
    }
    if ('distance' === $field) {
        return esc_html($distance);
    }
    if ('avatar' === $field) {
        return $avatar;
    }
    if ('rank' === $field) {
        return esc_html((string) $rank);
    }

    return '<div class="rts-leaderboard-rank rts-leaderboard-rank--' . esc_attr($rank) . '">' . $avatar . '<div class="rts-leaderboard-rank_group"><div class="rts-leaderboard-rank_name"><strong>' . esc_html($name) . '</strong><span>'
        . esc_html($flag) . ' </span></div><span class="rts-leaderboard-rank_distance">' . esc_html($distance) . '</span></div></div>';
}
add_shortcode('rts_leaderboard_rank', 'rts_leaderboard_rank_shortcode');

function rts_leaderboard_first_shortcode($atts) { $atts['rank'] = 1; return rts_leaderboard_rank_shortcode($atts); }
function rts_leaderboard_second_shortcode($atts) { $atts['rank'] = 2; return rts_leaderboard_rank_shortcode($atts); }
function rts_leaderboard_third_shortcode($atts) { $atts['rank'] = 3; return rts_leaderboard_rank_shortcode($atts); }
add_shortcode('rts_leaderboard_first', 'rts_leaderboard_first_shortcode');
add_shortcode('rts_leaderboard_second', 'rts_leaderboard_second_shortcode');
add_shortcode('rts_leaderboard_third', 'rts_leaderboard_third_shortcode');

/** Progress bar with the same journey milestone markers used by the leaderboard. */
function rts_leaderboard_progress_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'target' => 42200,
        'source' => 'earned',
        'value'  => '',
        'show_milestones' => 'yes',
    ), $atts, 'rts_leaderboard_progress');
    $target = max(1, absint($atts['target']));
    $miles = is_numeric($atts['value']) ? max(0, (int) $atts['value']) : rts_get_current_member_miles($atts['source']);
    $percent = min(100, round($miles / $target * 100, 1));
    $milestones = array();
    foreach (rts_get_captains_milestones() as $milestone) {
        if (0 === $milestone['miles']) {
            continue;
        }
        $milestones[] = array(
            'name'     => $milestone['name'],
            'position' => min(100, $milestone['miles'] / $target * 100),
            'earned'   => $miles >= $milestone['miles'],
            'label'    => rts_format_miles($milestone['miles']),
        );
    }

    $output = '<div class="rts-leaderboard-progress" role="progressbar" aria-valuemin="0" aria-valuemax="'
        . esc_attr($target) . '" aria-valuenow="' . esc_attr($miles) . '">';
    if ('no' !== strtolower((string) $atts['show_milestones'])) {
        $output .= '<div class="rts-leaderboard-progress__milestones" aria-hidden="true">';
        foreach ($milestones as $milestone) {
            $output .= '<span style="left:' . esc_attr($milestone['position']) . '%">' . esc_html($milestone['label']) . '</span>';
        }
        $output .= '</div>';
    }
    $output .= '<div class="rts-leaderboard-progress__track"><b style="width:' . esc_attr($percent) . '%"></b>';

    foreach ($milestones as $milestone) {
        $state = $milestone['earned'] ? ' is-earned' : '';
        $output .= '<i class="rts-leaderboard-progress__marker' . esc_attr($state) . '" style="left:' . esc_attr($milestone['position'])
            . '%" title="' . esc_attr($milestone['name']) . '"></i>';
    }

    $output .= '<i class="rts-leaderboard-progress__current" style="left:' . esc_attr($percent) . '%" aria-hidden="true"></i>';

    return $output . '</div><span class="rts-leaderboard-progress__caption">' . esc_html(sprintf(
        __('%1$s of %2$s', 'run-the-seas'),
        rts_format_miles($miles),
        rts_format_miles($target)
    )) . '</span></div>';
}
add_shortcode('rts_leaderboard_progress', 'rts_leaderboard_progress_shortcode');

/** Flagged, avatar-led standings rows for the central Elementor section. */
function rts_leaderboard_standings_shortcode($atts)
{
    $atts = shortcode_atts(array('limit' => 10, 'target' => 42200), $atts, 'rts_leaderboard_standings');
    $limit = min(50, max(1, absint($atts['limit'])));
    $target = max(1, absint($atts['target']));
    global $wpdb;
    $table = $wpdb->prefix . 'rts_participants';
    $leaders = $wpdb->get_results($wpdb->prepare(
        "SELECT id, user_id, first_name, last_name, country, email_verified, captain_miles_balance, total_captain_miles_earned
         FROM {$table} WHERE total_captain_miles_earned > 0
         ORDER BY total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC LIMIT %d",
        $limit
    ));

    $current = rts_get_current_member_participant();
    if (empty($leaders) && !$current) {
        return '<p class="rts-leaderboard-empty">' . esc_html__('No Captain’s Miles have been earned yet.', 'run-the-seas') . '</p>';
    }

    // Keep the member in their real ranked position. Their personal row is
    // additionally repeated at the bottom as the highlighted "YOU" row.
    $ranked_leaders = array();
    foreach ($leaders as $index => $leader) {
        $leader->leaderboard_rank = $index + 1;
        $ranked_leaders[] = $leader;
    }

    $participant_ids = array_map('absint', wp_list_pluck($ranked_leaders, 'id'));
    if ($current) {
        $participant_ids[] = absint($current->id);
    }
    $trophy_counts = array();
    $has_founding_runner = array();
    if (!empty($participant_ids)) {
        $participant_ids = array_values(array_unique($participant_ids));
        $placeholders = implode(', ', array_fill(0, count($participant_ids), '%d'));
        $trophy_rows = $wpdb->get_results($wpdb->prepare(
            "SELECT participant_id, COUNT(*) AS trophy_count,
                MAX(CASE WHEN trophy_key IN ('founder', 'founding-runner', 'founding-runner-trophy')
                              OR trophy_type IN ('founder', 'founding-runner', 'founding-runner-trophy')
                         THEN 1 ELSE 0 END) AS has_founding_runner
            FROM {$wpdb->prefix}rts_user_trophies
            WHERE is_displayed = 1 AND participant_id IN ({$placeholders})
            GROUP BY participant_id",
            ...$participant_ids
        ));
        foreach ($trophy_rows as $trophy_row) {
            $trophy_counts[(int) $trophy_row->participant_id] = (int) $trophy_row->trophy_count;
            $has_founding_runner[(int) $trophy_row->participant_id] = (bool) $trophy_row->has_founding_runner;
        }
    }

    $standings_members = $ranked_leaders;
    if ($current) {
        $standings_members[] = $current;
    }
    $founding_runner_counted = array();
    foreach ($standings_members as $member) {
        $participant_id = (int) $member->id;
        if (
            !isset($founding_runner_counted[$participant_id])
            && (int) $member->email_verified === 1
            && empty($has_founding_runner[$participant_id])
        ) {
            $trophy_counts[$participant_id] = ($trophy_counts[$participant_id] ?? 0) + 1;
            $founding_runner_counted[$participant_id] = true;
        }
    }

    $render_row = static function ($leader, $rank, $is_you_row) use ($target, $trophy_counts) {
        $miles = max(0, (int) $leader->total_captain_miles_earned);
        $name = trim((string) $leader->first_name . ' ' . (string) $leader->last_name) ?: __('Captain', 'run-the-seas');
        $trophy_count = $trophy_counts[(int) $leader->id] ?? 0;
        $trophy_label = sprintf(
            _n('%d Trophy', '%d Trophies', $trophy_count, 'run-the-seas'),
            $trophy_count
        );
        $rank_class = $is_you_row
            ? ' is-you'
            : ((int) $rank <= 3 ? ' rts-leaderboard-standings__rank--' . (int) $rank : '');
        $rank_label = $is_you_row ? __('You', 'run-the-seas') : (string) $rank;
        $percent = min(100, round($miles / $target * 100));

        return '<article class="rts-leaderboard-standings__row' . ($is_you_row ? ' is-current' : '') . '"><strong class="rts-leaderboard-standings__rank'
            . esc_attr($rank_class) . '">' . esc_html($rank_label) . '</strong><span class="rts-leaderboard-standings__country">'
            . esc_html(rts_country_flag($leader->country)) . ' ' . esc_html($leader->country ?: '—') . '</span><span class="rts-leaderboard-standings__captain">'
            . get_avatar((int) $leader->user_id, 42, '', '', array('class' => 'rts-leaderboard-standings__avatar'))
            . '<span class="rts-leaderboard__name">' . esc_html($name) . '</span></span><span class="rts-leaderboard-standings__distance">'
            . esc_html(rts_format_miles($miles)) . '</span><span class="rts-leaderboard-standings__percent">'
            . esc_html($percent . '%') . '</span><span class="rts-leaderboard-standings__trophies">'
            . esc_html($trophy_label) . '</span><span class="rts-leaderboard-standings__progress">'
            . rts_leaderboard_progress_shortcode(array('target' => $target, 'value' => $miles, 'show_milestones' => 'no')) . '</span></article>';
    };

    $milestone_header = '<span class="rts-leaderboard-standings__milestone-header" aria-label="'
        . esc_attr__('Trophy milestones', 'run-the-seas') . '">';
    foreach (rts_get_captains_milestones() as $milestone) {
        if (0 === $milestone['miles']) {
            continue;
        }
        $position = min(100, $milestone['miles'] / $target * 100);
        $milestone_header .= '<i style="left:' . esc_attr($position) . '%">'
            . esc_html(rts_format_miles($milestone['miles'])) . '</i>';
    }
    $milestone_header .= '</span>';

    $output = '<div class="rts-leaderboard-standings"><div class="rts-leaderboard-standings__labels"><span>'
        . esc_html__('Rank', 'run-the-seas') . '</span><span>' . esc_html__('Country', 'run-the-seas') . '</span><span>'
        . esc_html__('Participant', 'run-the-seas') . '</span><span>' . esc_html__('Kilometres Completed', 'run-the-seas') . '</span><span>'
        . esc_html__('Progress', 'run-the-seas') . '</span><span>' . esc_html__('Trophies', 'run-the-seas') . '</span>'
        . $milestone_header . '</div>';
    foreach ($ranked_leaders as $leader) {
        $output .= $render_row($leader, $leader->leaderboard_rank, false);
    }
    if ($current) {
        $output .= '<div class="rts-leaderboard-standings__you-row">' . $render_row($current, 0, true) . '</div>';
    }
    return $output . '</div>';
}
add_shortcode('rts_leaderboard_standings', 'rts_leaderboard_standings_shortcode');

/** Print the current leaderboard page through the browser's native print dialog. */
function rts_leaderboard_print_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'label' => __('Print Leaderboard', 'run-the-seas'),
    ), $atts, 'rts_leaderboard_print');

    return '<div class="rts-leaderboard-print-action">'
        . '<button type="button" class="rts-leaderboard-print-action__button" data-rts-leaderboard-export="print">'
        . esc_html(wp_strip_all_tags($atts['label'])) . '</button>'
        . '<span class="rts-leaderboard-print-action__status" role="status" aria-live="polite"></span></div>';
}
add_shortcode('rts_leaderboard_print', 'rts_leaderboard_print_shortcode');

/** Ranked milestone cards with individually coloured trophy icons. */
function rts_trophy_milestones_shortcode()
{
    $participant = rts_get_current_member_participant();
    $miles = $participant ? rts_get_current_member_miles() : 0;
    $output = '<div class="rts-trophy-milestones">';
    foreach (rts_get_captains_milestones() as $milestone) {
        $earned = $participant && $miles >= $milestone['miles'] ? ' is-earned' : '';
        //$rank_label = 0 === $milestone['rank'] ? __('Founder', 'run-the-seas') : '#' . $milestone['rank'];
        // $output .= '<article class="rts-trophy-milestones__item rts-trophy-milestones__item--' . esc_attr($milestone['key'])
        //     . esc_attr($earned) . '">' . rts_render_trophy_milestone_icon($milestone, 'rts-trophy-milestones__icon')
        //     . '<span class="rts-trophy-milestones__rank"></span><span class="rts-trophy-milestones__title">' . esc_html($milestone['name'])
        //     . '</span><strong>' . esc_html(0 === $milestone['miles'] ? __('Founder', 'run-the-seas') : rts_format_miles($milestone['miles']))
        //     . '</strong></article>';
        $output .= '<article class="rts-trophy-milestones__item rts-trophy-milestones__item--' . esc_attr($milestone['key'])
            . esc_attr($earned) . '">' . '🏆'
            . '<span class="rts-trophy-milestones__rank"></span><span class="rts-trophy-milestones__title">' . esc_html($milestone['name'])
            . '</span><strong>' . esc_html(0 === $milestone['miles'] ? __('Founder', 'run-the-seas') : rts_format_miles($milestone['miles']))
            . '</strong></article>';
            
    
    }
    return $output . '</div>';
}
add_shortcode('rts_trophy_milestones', 'rts_trophy_milestones_shortcode');

/** Logged-in member's position, completed distance, and earned trophy total. */
function rts_member_leaderboard_summary_shortcode()
{
    $current = rts_get_current_member_participant();
    if (!$current) {
        return '';
    }

    global $wpdb;
    $table = $wpdb->prefix . 'rts_participants';
    $rank = 0;
    if ((int) $current->total_captain_miles_earned > 0) {
        $rank = 1 + (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table}
             WHERE total_captain_miles_earned > %d
             OR (total_captain_miles_earned = %d AND captain_miles_balance > %d)
             OR (total_captain_miles_earned = %d AND captain_miles_balance = %d AND id < %d)",
            $current->total_captain_miles_earned,
            $current->total_captain_miles_earned,
            $current->captain_miles_balance,
            $current->total_captain_miles_earned,
            $current->captain_miles_balance,
            $current->id
        ));
    }
    $trophies_table = $wpdb->prefix . 'rts_user_trophies';
    $trophy_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$trophies_table} WHERE participant_id = %d AND is_displayed = 1",
        $current->id
    ));
    $has_founding_runner = (bool) $wpdb->get_var($wpdb->prepare(
        "SELECT 1 FROM {$trophies_table}
        WHERE participant_id = %d
        AND (trophy_key IN ('founder', 'founding-runner', 'founding-runner-trophy')
             OR trophy_type IN ('founder', 'founding-runner', 'founding-runner-trophy'))
        LIMIT 1",
        $current->id
    ));
    if ((int) $current->email_verified === 1 && !$has_founding_runner) {
        ++$trophy_count;
    }

    return '<div class="rts-member-leaderboard-summary"><span>' . esc_html__('Your current position', 'run-the-seas')
        . '<strong>' . esc_html($rank ?: '—') . '</strong></span><span>' . esc_html__('Distance completed', 'run-the-seas')
        . '<strong>' . esc_html(rts_format_miles((int) $current->total_captain_miles_earned)) . '</strong></span><span>'
        . esc_html__('Trophies earned', 'run-the-seas') . '<strong>' . esc_html(number_format_i18n($trophy_count))
        . '</strong></span></div>';
}
add_shortcode('rts_member_leaderboard_summary', 'rts_member_leaderboard_summary_shortcode');

/** Render the full visual leaderboard page used in its own Elementor page. */
function rts_live_leaderboard_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'limit'  => 10,
        'target' => 42200,
    ), $atts, 'rts_live_leaderboard');
    $limit = min(50, max(3, absint($atts['limit'])));
    $target = max(1, absint($atts['target']));
    global $wpdb;
    $table = $wpdb->prefix . 'rts_participants';
    $leaders = $wpdb->get_results($wpdb->prepare(
        "SELECT id, user_id, first_name, last_name, country, captain_miles_balance, total_captain_miles_earned
         FROM {$table}
         WHERE total_captain_miles_earned > 0
         ORDER BY total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC
         LIMIT %d",
        $limit
    ));
    $current = rts_get_current_member_participant();
    $current_id = $current ? (int) $current->id : 0;
    $current_rank = 0;
    if ($current && (int) $current->total_captain_miles_earned > 0) {
        $current_rank = 1 + (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table}
             WHERE total_captain_miles_earned > %d
             OR (total_captain_miles_earned = %d AND captain_miles_balance > %d)
             OR (total_captain_miles_earned = %d AND captain_miles_balance = %d AND id < %d)",
            $current->total_captain_miles_earned,
            $current->total_captain_miles_earned,
            $current->captain_miles_balance,
            $current->total_captain_miles_earned,
            $current->captain_miles_balance,
            $current->id
        ));
    }
    $trophy_count = $current ? (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}rts_user_trophies WHERE participant_id = %d",
        $current->id
    )) : 0;
    $updated = current_time(get_option('time_format'));

    ob_start();
    ?>
    <section class="rts-live-leaderboard" aria-label="<?php esc_attr_e('Live leaderboard', 'run-the-seas'); ?>">
        <header class="rts-live-leaderboard__hero">
            <p class="rts-live-leaderboard__live"><span aria-hidden="true"></span><?php esc_html_e('Live Leaderboard', 'run-the-seas'); ?></p>
            <h1><?php esc_html_e('The', 'run-the-seas'); ?> <strong><?php echo esc_html(rts_format_miles($target)); ?></strong> <?php esc_html_e('Referral Marathon Challenge', 'run-the-seas'); ?></h1>
            <p><?php esc_html_e('Every verified referral moves you 1K closer to the finish line.', 'run-the-seas'); ?></p>
            <small><?php echo esc_html(sprintf(__('Last updated: %s', 'run-the-seas'), $updated)); ?></small>
        </header>

        <?php if (count($leaders) >= 3) : ?>
            <div class="rts-live-leaderboard__podium" aria-label="<?php esc_attr_e('Top three Captains', 'run-the-seas'); ?>">
                <?php foreach (array(1, 0, 2) as $index) : $leader = $leaders[$index]; $rank = $index + 1; ?>
                    <article class="rts-live-leaderboard__podium-card rts-live-leaderboard__podium-card--<?php echo esc_attr($rank); ?>">
                        <span class="rts-live-leaderboard__podium-rank">#<?php echo esc_html($rank); ?></span>
                        <?php echo get_avatar((int) $leader->user_id, 88, '', '', array('class' => 'rts-live-leaderboard__avatar')); ?>
                        <strong><?php echo esc_html(trim($leader->first_name . ' ' . $leader->last_name)); ?></strong>
                        <span><?php echo esc_html(rts_country_flag($leader->country)); ?> <?php echo esc_html(rts_format_miles((int) $leader->total_captain_miles_earned)); ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="rts-live-leaderboard__content">
            <aside class="rts-live-leaderboard__how-it-works">
                <h2><?php esc_html_e('How the leaderboard works', 'run-the-seas'); ?></h2>
                <ul>
                    <li><?php esc_html_e('Every verified referral earns 1K Captain’s Miles.', 'run-the-seas'); ?></li>
                    <li><?php esc_html_e('Your kilometres accumulate automatically.', 'run-the-seas'); ?></li>
                    <li><?php esc_html_e('Reach milestones to unlock Run The Seas trophies.', 'run-the-seas'); ?></li>
                    <li><?php esc_html_e('Standings update as verified referrals are recorded.', 'run-the-seas'); ?></li>
                </ul>
            </aside>

            <div class="rts-live-leaderboard__standings">
                <h2><?php esc_html_e('Current standings', 'run-the-seas'); ?></h2>
                <?php if (empty($leaders)) : ?>
                    <p><?php esc_html_e('No Captain’s Miles have been earned yet.', 'run-the-seas'); ?></p>
                <?php else : ?>
                    <div class="rts-live-leaderboard__labels" aria-hidden="true"><span><?php esc_html_e('Rank', 'run-the-seas'); ?></span><span><?php esc_html_e('Participant', 'run-the-seas'); ?></span><span><?php esc_html_e('Country', 'run-the-seas'); ?></span><span><?php esc_html_e('Distance', 'run-the-seas'); ?></span><span><?php esc_html_e('Progress', 'run-the-seas'); ?></span></div>
                    <?php foreach ($leaders as $index => $leader) :
                        $rank = $index + 1;
                        $is_current = $current_id && $current_id === (int) $leader->id;
                        $miles = (int) $leader->total_captain_miles_earned;
                        $percent = min(100, round($miles / $target * 100));
                        $name = trim($leader->first_name . ' ' . $leader->last_name) ?: __('Captain', 'run-the-seas');
                    ?>
                        <article class="rts-live-leaderboard__standing<?php echo $is_current ? ' is-current' : ''; ?>">
                            <strong class="rts-live-leaderboard__standing-rank"><?php echo esc_html($rank); ?></strong>
                            <span class="rts-live-leaderboard__standing-captain"><?php echo get_avatar((int) $leader->user_id, 42, '', '', array('class' => 'rts-live-leaderboard__avatar')); ?><?php echo esc_html($name); ?></span>
                            <span class="rts-live-leaderboard__standing-country"><?php echo esc_html(rts_country_flag($leader->country)); ?> <?php echo esc_html($leader->country ?: '—'); ?></span>
                            <span class="rts-live-leaderboard__standing-distance"><?php echo esc_html(rts_format_miles($miles)); ?></span>
                            <span class="rts-live-leaderboard__standing-progress"><i><b style="width:<?php echo esc_attr($percent); ?>%"></b></i><em><?php echo esc_html($percent); ?>%</em></span>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($current && $current_rank > $limit) : $percent = min(100, round((int) $current->total_captain_miles_earned / $target * 100)); ?>
                    <article class="rts-live-leaderboard__standing is-current rts-live-leaderboard__standing--you">
                        <strong class="rts-live-leaderboard__standing-rank"><?php echo esc_html($current_rank); ?></strong>
                        <span class="rts-live-leaderboard__standing-captain"><?php echo get_avatar((int) $current->user_id, 42, '', '', array('class' => 'rts-live-leaderboard__avatar')); ?><?php echo esc_html(trim($current->first_name . ' ' . $current->last_name)); ?> (<?php esc_html_e('You', 'run-the-seas'); ?>)</span>
                        <span class="rts-live-leaderboard__standing-country"><?php echo esc_html(rts_country_flag($current->country)); ?> <?php echo esc_html($current->country ?: '—'); ?></span>
                        <span class="rts-live-leaderboard__standing-distance"><?php echo esc_html(rts_format_miles((int) $current->total_captain_miles_earned)); ?></span>
                        <span class="rts-live-leaderboard__standing-progress"><i><b style="width:<?php echo esc_attr($percent); ?>%"></b></i><em><?php echo esc_html($percent); ?>%</em></span>
                    </article>
                <?php endif; ?>
            </div>

            <aside class="rts-live-leaderboard__milestones">
                <h2><?php esc_html_e('Trophy milestones', 'run-the-seas'); ?></h2>
                <ul>
                    <?php foreach (rts_get_captains_milestones() as $milestone) : ?>
                        <li><span>🏆 <?php echo esc_html($milestone['name']); ?></span><strong><?php echo esc_html(rts_format_miles($milestone['miles'])); ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </div>

        <?php if ($current) : ?>
            <footer class="rts-live-leaderboard__summary">
                <span><?php esc_html_e('Your current position', 'run-the-seas'); ?><strong><?php echo esc_html($current_rank ?: '—'); ?></strong></span>
                <span><?php esc_html_e('Kilometres completed', 'run-the-seas'); ?><strong><?php echo esc_html(rts_format_miles((int) $current->total_captain_miles_earned)); ?></strong></span>
                <span><?php esc_html_e('Trophies earned', 'run-the-seas'); ?><strong><?php echo esc_html(number_format_i18n($trophy_count)); ?></strong></span>
            </footer>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_live_leaderboard', 'rts_live_leaderboard_shortcode');
