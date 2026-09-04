<?php

if (!defined('ABSPATH')) {
    exit;
}

/** Return one optional image from the Captain's Dashboard design settings. */
function rts_dashboard_design_image($key)
{
    $assets = get_option('rts_dashboard_design_assets', array());
    $assets = is_array($assets) ? $assets : array();

    return !empty($assets[$key]) ? esc_url((string) $assets[$key]) : '';
}

/** Sanitize a space-separated shortcode class attribute. */
function rts_dashboard_extra_classes($classes)
{
    $clean = array();
    foreach (preg_split('/\s+/', trim((string) $classes)) as $class) {
        $class = sanitize_html_class($class);
        if ('' !== $class) {
            $clean[] = $class;
        }
    }

    return implode(' ', array_unique($clean));
}

/** Count displayed trophies, including the virtual Founding Runner trophy. */
function rts_dashboard_trophy_count($participant)
{
    if (!$participant || empty($participant->id)) {
        return 0;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'rts_user_trophies';
    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT COUNT(*) AS trophy_count,
        MAX(CASE WHEN trophy_key IN ('founder', 'founding-runner', 'founding-runner-trophy')
            OR trophy_type IN ('founder', 'founding-runner', 'founding-runner-trophy') THEN 1 ELSE 0 END) AS has_founding_runner
        FROM {$table} WHERE participant_id = %d AND is_displayed = 1",
        $participant->id
    ));
    $count = $row ? (int) $row->trophy_count : 0;
    if ((int) ($participant->email_verified ?? 0) === 1 && (!$row || !(int) $row->has_founding_runner)) {
        ++$count;
    }

    return $count;
}

/** Render either uploaded artwork or the accessible built-in icon. */
function rts_dashboard_card_icon($asset_key, $fallback, $class)
{
    $image = rts_dashboard_design_image($asset_key);
    if ($image) {
        return '<span class="' . esc_attr($class) . '"><img src="' . esc_url($image)
            . '" alt="" loading="lazy" decoding="async"></span>';
    }

    return '<span class="' . esc_attr($class) . '" aria-hidden="true">' . esc_html($fallback) . '</span>';
}

/**
 * The four-card member status strip marked in the supplied dashboard artwork.
 *
 * Usage: [rts_captain_status]
 * Optional: [rts_captain_status target="42000" class="my-class"]
 */
function rts_captain_status_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'target' => 42000,
        'class'  => '',
    ), $atts, 'rts_captain_status');
    $target = rts_normalize_marathon_target($atts['target']);
    $participant = function_exists('rts_get_current_member_participant')
        ? rts_get_current_member_participant()
        : null;
    $referrals = $participant
        ? max(absint($participant->successful_referrals ?? 0), absint($participant->referral_count ?? 0))
        : 0;
    $miles = $participant ? max(0, (int) ($participant->total_captain_miles_earned ?? 0)) : 0;
    $percent = min(100, round(($miles / $target) * 100, 1));
    $trophy_count = rts_dashboard_trophy_count($participant);
    $next = $participant && function_exists('rts_get_current_member_next_trophy')
        ? rts_get_current_member_next_trophy()
        : null;
    $remaining = $next ? max(0, (int) $next['miles'] - $miles) : 0;
    $referrals_remaining = (int) ceil($remaining / 1000);
    $frame = rts_dashboard_design_image('status_frame_image');
    $classes = 'rts-captain-status';
    $extra_class = rts_dashboard_extra_classes($atts['class']);
    if ($extra_class) {
        $classes .= ' ' . $extra_class;
    }
    if ($frame) {
        $classes .= ' has-artwork-frame';
    }

    ob_start();
    ?>
    <section class="<?php echo esc_attr($classes); ?>" aria-label="<?php esc_attr_e('Your referral marathon status', 'run-the-seas'); ?>">
        <?php if ($frame) : ?><img class="rts-captain-status__frame" src="<?php echo esc_url($frame); ?>" alt="" aria-hidden="true"><?php endif; ?>
        <article class="rts-captain-status__card rts-captain-status__card--referrals">
            <?php echo rts_dashboard_card_icon('referrals_icon_image', '👥', 'rts-captain-status__icon'); ?>
            <div><span><?php esc_html_e('Your Referrals', 'run-the-seas'); ?></span><strong><?php echo esc_html(number_format_i18n($referrals)); ?></strong><small><?php esc_html_e('Keep going!', 'run-the-seas'); ?></small></div>
        </article>
        <article class="rts-captain-status__card rts-captain-status__card--progress">
            <?php echo rts_dashboard_card_icon('progress_icon_image', '🏃', 'rts-captain-status__icon'); ?>
            <div><span><?php esc_html_e('Your Progress', 'run-the-seas'); ?></span><strong><?php echo esc_html(rts_format_miles($miles)); ?> <em>/ <?php echo esc_html(42000 === $target ? rts_format_trophy_miles($target, '42k') : rts_format_miles($target)); ?></em></strong><i class="rts-captain-status__progress" role="progressbar" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($target); ?>" aria-valuenow="<?php echo esc_attr($miles); ?>"><b style="width:<?php echo esc_attr($percent); ?>%"></b></i><small><?php echo esc_html(sprintf(__('%s%% to the Marathon', 'run-the-seas'), $percent)); ?></small></div>
        </article>
        <article class="rts-captain-status__card rts-captain-status__card--trophies">
            <?php echo rts_dashboard_card_icon('trophies_icon_image', '🏆', 'rts-captain-status__icon'); ?>
            <div><span><?php esc_html_e('Trophies Unlocked', 'run-the-seas'); ?></span><strong><?php echo esc_html(number_format_i18n($trophy_count)); ?></strong><small><?php esc_html_e('Keep collecting!', 'run-the-seas'); ?></small></div>
        </article>
        <article class="rts-captain-status__card rts-captain-status__card--next">
            <?php echo rts_dashboard_card_icon('next_trophy_icon_image', '🏅', 'rts-captain-status__icon'); ?>
            <div><span><?php esc_html_e('Next Trophy', 'run-the-seas'); ?></span><strong><?php echo esc_html($next ? $next['name'] : __('All Unlocked', 'run-the-seas')); ?></strong><small><?php echo $next ? esc_html(sprintf(_n('%1$d verified referral needed (%2$s)', '%1$d verified referrals needed (%2$s)', $referrals_remaining, 'run-the-seas'), $referrals_remaining, rts_format_miles($remaining))) : esc_html__('Marathon complete!', 'run-the-seas'); ?></small></div>
        </article>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_captain_status', 'rts_captain_status_shortcode');

/** Get trophy counts for a group of participants in one query. */
function rts_dashboard_trophy_counts($participants)
{
    $participants_by_id = array();
    foreach ((array) $participants as $participant) {
        if (!empty($participant->id)) {
            $participants_by_id[(int) $participant->id] = $participant;
        }
    }
    if (!$participants_by_id) {
        return array();
    }

    global $wpdb;
    $ids = array_keys($participants_by_id);
    $placeholders = implode(',', array_fill(0, count($ids), '%d'));
    $sql = "SELECT participant_id, COUNT(*) AS trophy_count,
        MAX(CASE WHEN trophy_key IN ('founder', 'founding-runner', 'founding-runner-trophy')
            OR trophy_type IN ('founder', 'founding-runner', 'founding-runner-trophy') THEN 1 ELSE 0 END) AS has_founding_runner
        FROM {$wpdb->prefix}rts_user_trophies
        WHERE is_displayed = 1 AND participant_id IN ({$placeholders}) GROUP BY participant_id";
    $rows = $wpdb->get_results($wpdb->prepare($sql, $ids));
    $counts = array_fill_keys($ids, 0);
    $has_founding_runner = array_fill_keys($ids, false);
    foreach ($rows as $row) {
        $id = (int) $row->participant_id;
        $counts[$id] = (int) $row->trophy_count;
        $has_founding_runner[$id] = (bool) $row->has_founding_runner;
    }
    foreach ($participants_by_id as $id => $participant) {
        if ((int) ($participant->email_verified ?? 0) === 1 && !$has_founding_runner[$id]) {
            ++$counts[$id];
        }
    }

    return $counts;
}

/** Render the uploaded leaderboard trophy icons or their coloured fallbacks. */
function rts_dashboard_leaderboard_trophies($count, $style = 'default', $max_trophies = 7)
{
    $count = max(0, (int) $count);
    if (0 === $count) {
        return '<span class="rts-captain-leaderboard-card__no-trophy">—</span>';
    }

    $style = sanitize_key((string) $style);
    $default_image = rts_dashboard_design_image('leaderboard_trophy_icon_image');
    $marathon_colours = array('green', 'blue', 'purple', 'gold', 'orange', 'red', 'silver');
    $default_colours = array('1', '2', '3', '4', 'orange', 'red', 'silver');
    $max_trophies = min(7, max(1, absint($max_trophies)));
    $visible_count = min($max_trophies, $count);
    $output = '<span class="rts-captain-leaderboard-card__trophy-list" aria-label="' . esc_attr(sprintf(
        _n('%d trophy', '%d trophies', $count, 'run-the-seas'),
        $count
    )) . '">';
    for ($index = 0; $index < $visible_count; ++$index) {
        $colour = 'marathon' === $style ? $marathon_colours[$index] : $default_colours[$index];
        $colour_class = 'rts-trophy-colour--' . $colour;
        $uses_uploaded_marathon_icon = false;
        $image = 'marathon' === $style
            ? rts_dashboard_design_image('leaderboard_trophy_' . $marathon_colours[$index] . '_image')
            : $default_image;
        if ($image && 'marathon' === $style) {
            $uses_uploaded_marathon_icon = true;
        }
        if (!$image && 'marathon' === $style) {
            $image = $default_image;
        }
        $output .= $image
            ? '<img class="' . esc_attr($colour_class . ($uses_uploaded_marathon_icon ? ' is-uploaded-marathon-icon' : '')) . '" src="' . esc_url($image) . '" alt="" aria-hidden="true">'
            : '<b class="' . esc_attr($colour_class) . '" aria-hidden="true">&#127942;&#65038;</b>';
    }
    if ($count > $visible_count) {
        $output .= '<small>+' . esc_html(number_format_i18n($count - $visible_count)) . '</small>';
    }

    return $output . '</span>';
}

/**
 * The framed compact leaderboard marked in the supplied dashboard artwork.
 *
 * Usage: [rts_captain_leaderboard_card]
 * Optional: [rts_captain_leaderboard_card limit="8" trophy_style="marathon" max_trophies="7" class="my-class"]
 */
function rts_captain_leaderboard_card_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'limit'       => 8,
        'class'       => '',
        'trophy_style'=> 'default',
        'max_trophies'=> 7,
        'invite_text' => __('Invite more friends and climb the leaderboard!', 'run-the-seas'),
    ), $atts, 'rts_captain_leaderboard_card');
    $limit = min(20, max(3, absint($atts['limit'])));
    $trophy_style = 'marathon' === sanitize_key((string) $atts['trophy_style']) ? 'marathon' : 'default';
    $max_trophies = min(7, max(1, absint($atts['max_trophies'])));
    global $wpdb;
    $table = $wpdb->prefix . 'rts_participants';
    $referral_expression = 'GREATEST(COALESCE(successful_referrals, 0), COALESCE(referral_count, 0), FLOOR(COALESCE(total_captain_miles_earned, 0) / 1000))';
    $leaders = $wpdb->get_results($wpdb->prepare(
        "SELECT id, user_id, first_name, last_name, country, email_verified,
        captain_miles_balance, total_captain_miles_earned, {$referral_expression} AS referral_total
        FROM {$table} WHERE {$referral_expression} > 0
        ORDER BY referral_total DESC, total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC LIMIT %d",
        $limit
    ));
    $current = function_exists('rts_get_current_member_participant') ? rts_get_current_member_participant() : null;
    $current_id = $current ? (int) $current->id : 0;
    $current_is_visible = false;
    foreach ($leaders as $leader) {
        if ($current_id && $current_id === (int) $leader->id) {
            $current_is_visible = true;
            break;
        }
    }
    if ($current && !$current_is_visible) {
        $current_referrals = max(
            absint($current->successful_referrals ?? 0),
            absint($current->referral_count ?? 0),
            (int) floor(absint($current->total_captain_miles_earned ?? 0) / 1000)
        );
        if ($current_referrals > 0) {
            $current->referral_total = $current_referrals;
            $current->rts_dashboard_rank = 1 + (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$table}
                WHERE {$referral_expression} > %d
                OR ({$referral_expression} = %d AND id < %d)",
                $current_referrals,
                $current_referrals,
                $current->id
            ));
            $leaders[] = $current;
        }
    }

    $trophy_counts = rts_dashboard_trophy_counts($leaders);
    $frame = rts_dashboard_design_image('leaderboard_frame_image');
    $left_ornament = rts_dashboard_design_image('leaderboard_left_ornament_image');
    $right_ornament = rts_dashboard_design_image('leaderboard_right_ornament_image');
    $invite_icon = rts_dashboard_design_image('leaderboard_invite_icon_image');
    $classes = 'rts-captain-leaderboard-card';
    $extra_class = rts_dashboard_extra_classes($atts['class']);
    if ($extra_class) {
        $classes .= ' ' . $extra_class;
    }
    if ($frame) {
        $classes .= ' has-artwork-frame';
    }

    ob_start();
    ?>
    <section class="<?php echo esc_attr($classes); ?>" aria-label="<?php esc_attr_e('Referral leaderboard', 'run-the-seas'); ?>">
        <?php if ($frame) : ?><img class="rts-captain-leaderboard-card__frame" src="<?php echo esc_url($frame); ?>" alt="" aria-hidden="true"><?php endif; ?>
        <header class="rts-captain-leaderboard-card__header">
            <?php if ($left_ornament) : ?><img class="rts-captain-leaderboard-card__heading-ornament rts-captain-leaderboard-card__heading-ornament--left" src="<?php echo esc_url($left_ornament); ?>" alt="" aria-hidden="true"><?php else : ?><span aria-hidden="true">❧</span><?php endif; ?>
            <h2><?php esc_html_e('Leaderboard', 'run-the-seas'); ?></h2>
            <?php if ($right_ornament) : ?><img class="rts-captain-leaderboard-card__heading-ornament rts-captain-leaderboard-card__heading-ornament--right" src="<?php echo esc_url($right_ornament); ?>" alt="" aria-hidden="true"><?php else : ?><span aria-hidden="true">❧</span><?php endif; ?>
        </header>
        <div class="rts-captain-leaderboard-card__labels" aria-hidden="true"><span><?php esc_html_e('Rank', 'run-the-seas'); ?></span><span><?php esc_html_e('Runner', 'run-the-seas'); ?></span><span><?php esc_html_e('Referrals', 'run-the-seas'); ?></span><span><?php esc_html_e('Trophies', 'run-the-seas'); ?></span></div>
        <div class="rts-captain-leaderboard-card__rows" role="list">
            <?php if (!$leaders) : ?>
                <p class="rts-captain-leaderboard-card__empty"><?php esc_html_e('No verified referrals yet. Be the first Captain on the leaderboard!', 'run-the-seas'); ?></p>
            <?php endif; ?>
            <?php foreach ($leaders as $index => $leader) :
                $rank = isset($leader->rts_dashboard_rank) ? (int) $leader->rts_dashboard_rank : $index + 1;
                $is_current = $current_id && $current_id === (int) $leader->id;
                $name = trim((string) $leader->first_name . ' ' . (string) $leader->last_name) ?: __('Captain', 'run-the-seas');
                $count = $trophy_counts[(int) $leader->id] ?? 0;
                ?>
                <article class="rts-captain-leaderboard-card__row<?php echo $is_current ? ' is-current' : ''; ?>" role="listitem">
                    <strong class="rts-captain-leaderboard-card__rank rts-captain-leaderboard-card__rank--<?php echo esc_attr(min(4, $rank)); ?>"><?php echo esc_html(number_format_i18n($rank)); ?></strong>
                    <span class="rts-captain-leaderboard-card__runner"<?php echo $is_current ? ' aria-label="' . esc_attr(sprintf(__('%s — You. Keep going!', 'run-the-seas'), $name)) . '"' : ''; ?>><?php echo get_avatar((int) $leader->user_id, 44, '', '', array('class' => 'rts-captain-leaderboard-card__avatar')); ?><span><b><?php echo esc_html($is_current ? __('You', 'run-the-seas') : $name); ?></b><small><?php echo $is_current ? esc_html__('Keep going!', 'run-the-seas') : esc_html(rts_country_flag($leader->country) . ' ' . ($leader->country ?: __('International', 'run-the-seas'))); ?></small></span></span>
                    <strong class="rts-captain-leaderboard-card__referrals"><?php echo esc_html(number_format_i18n((int) $leader->referral_total)); ?></strong>
                    <?php echo rts_dashboard_leaderboard_trophies($count, $trophy_style, $max_trophies); ?>
                </article>
            <?php endforeach; ?>
        </div>
        <footer class="rts-captain-leaderboard-card__invite">
            <?php if ($invite_icon) : ?><img src="<?php echo esc_url($invite_icon); ?>" alt="" aria-hidden="true"><?php else : ?><span aria-hidden="true">👥</span><?php endif; ?>
            <?php echo esc_html(wp_strip_all_tags($atts['invite_text'])); ?>
        </footer>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('rts_captain_leaderboard_card', 'rts_captain_leaderboard_card_shortcode');
