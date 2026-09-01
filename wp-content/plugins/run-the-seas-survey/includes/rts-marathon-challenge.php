<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return a participant's position within the repeating 42.2K challenge.
 *
 * A member stays at the finish line when their lifetime distance is an exact
 * multiple of the target. Their next referral starts the following marathon.
 */
function rts_marathon_challenge_lap($total_miles, $target = 42000)
{
    $target = rts_normalize_marathon_target($target);
    $total_miles = max(0, absint($total_miles));

    if ($total_miles <= $target) {
        return array(
            'distance'  => $total_miles,
            'marathon'  => 1,
            'completed' => $total_miles === $target ? 1 : 0,
        );
    }

    $completed = (int) floor($total_miles / $target);
    $distance = $total_miles % $target;
    $marathon = $completed + 1;

    if (0 === $distance) {
        $distance = $target;
        $marathon = $completed;
    }

    return array(
        'distance'  => $distance,
        'marathon'  => $marathon,
        'completed' => $completed,
    );
}

/**
 * Build deterministic, request-only participants for visual testing.
 * Nothing returned here is written to WordPress or the participants table.
 */
function rts_marathon_challenge_demo_participants($target = 42000)
{
    $target = rts_normalize_marathon_target($target);
    $first_names = array('Ava', 'Noah', 'Mia', 'Leo', 'Zoe', 'Eli', 'Ivy', 'Kai', 'Nina', 'Owen', 'Lena', 'Theo');
    $last_names = array('Reed', 'Stone', 'Brooks', 'Lane', 'Cole', 'Hart', 'Wells', 'Shaw', 'Cross', 'Blake');
    $colors = array('#087fdb', '#1aa36f', '#e78216', '#b34fbc', '#c44848', '#488d24', '#177e89', '#8658d4');
    $participants = array();
    $sequence = 1;

    $make_participant = static function ($distance, $label = '') use (&$sequence, $first_names, $last_names, $colors) {
        $first = $first_names[($sequence - 1) % count($first_names)];
        $last = $last_names[(int) floor(($sequence - 1) / count($first_names)) % count($last_names)];
        $participant = (object) array(
            'id'                         => 900000 + $sequence,
            'user_id'                    => 0,
            'first_name'                 => $first,
            'last_name'                  => trim($last . ' ' . $label),
            'country'                    => 'Demo',
            'captain_miles_balance'      => absint($distance),
            'total_captain_miles_earned' => absint($distance),
            'is_demo'                    => true,
            'demo_initials'              => strtoupper(substr($first, 0, 1) . substr($last, 0, 1)),
            'demo_color'                 => $colors[($sequence - 1) % count($colors)],
        );
        $sequence++;

        return $participant;
    };

    $finisher_names = array(
        array('Sophia', 'Martinez', 'SM'),
        array('Daniel', 'Kim', 'DK'),
        array('Aisha', 'Patel', 'AP'),
        array('James', 'Wilson', 'JW'),
    );
    foreach ($finisher_names as $finisher_name) {
        $finisher = $make_participant($target);
        $finisher->first_name = $finisher_name[0];
        $finisher->last_name = $finisher_name[1];
        $finisher->demo_initials = $finisher_name[2];
        $participants[] = $finisher;
    }

    $participants[] = $make_participant($target + 1000, 'M2 Voyager');

    for ($distance = 0; $distance < $target; $distance += 1000) {
        $kilometre = (int) floor($distance / 1000);
        $count = 0 === $kilometre % 3 ? 3 : 2;
        for ($member = 1; $member <= $count; $member++) {
            $participants[] = $make_participant($distance, $kilometre . 'K-' . $member);
        }
    }

    return $participants;
}

/** Convert a stored WordPress/MySQL date into a sortable timestamp. */
function rts_marathon_challenge_recency_timestamp($value)
{
    $timestamp = $value ? strtotime((string) $value) : false;

    return false === $timestamp ? 0 : absint($timestamp);
}

/**
 * Attach progress and milestone-completion times to each participant.
 *
 * New Captain Miles timeline rows contain the exact lifetime totals before and
 * after an award. Older rows only contain the resulting balance, so they are
 * still supported. Participants that predate timeline logging fall back to
 * their last updated/registration date and estimated 1K steps.
 */
function rts_marathon_challenge_add_recency($participants, $timeline_rows, $milestone_distances)
{
    $events_by_participant = array();
    foreach ((array) $timeline_rows as $event) {
        $participant_id = absint($event->participant_id ?? 0);
        if (!$participant_id) {
            continue;
        }
        if (!isset($events_by_participant[$participant_id])) {
            $events_by_participant[$participant_id] = array();
        }
        $events_by_participant[$participant_id][] = $event;
    }

    foreach ($participants as $participant) {
        $participant_id = absint($participant->id ?? 0);
        $total = absint($participant->total_captain_miles_earned ?? 0);
        $fallback_at = rts_marathon_challenge_recency_timestamp($participant->updated_at ?? '')
            ?: rts_marathon_challenge_recency_timestamp($participant->registration_date ?? '')
            ?: $participant_id;

        $participant->progress_completed_at = $fallback_at;
        $participant->progress_completed_order = 0;
        $participant->milestone_completed_at = array();
        $participant->milestone_completed_order = array();

        // Keep the request-only demo deterministic while allowing different
        // recent captains to lead different milestones and route points.
        if (!empty($participant->is_demo)) {
            $participant->progress_completed_at = 1700000000 + (($participant_id * 7919) % 1000000);
            foreach ($milestone_distances as $distance) {
                $distance = absint($distance);
                if ($total >= $distance) {
                    $demo_completion_order = (int) sprintf('%u', crc32($participant_id . ':' . $distance));
                    $participant->milestone_completed_at[$distance] = 1700000000
                        + ($demo_completion_order % 1000000);
                }
            }
            continue;
        }

        foreach ($events_by_participant[$participant_id] ?? array() as $event) {
            $data = json_decode((string) ($event->activity_data ?? ''), true);
            $data = is_array($data) ? $data : array();
            $miles = absint($data['miles'] ?? 0);
            $after = isset($data['new_total_earned'])
                ? absint($data['new_total_earned'])
                : (isset($data['new_balance']) ? absint($data['new_balance']) : 0);
            if (!$after && $miles) {
                $after = $miles;
            }
            $before = isset($data['previous_total_earned'])
                ? absint($data['previous_total_earned'])
                : max(0, $after - $miles);
            $event_at = rts_marathon_challenge_recency_timestamp($event->activity_date ?? '')
                ?: rts_marathon_challenge_recency_timestamp($event->created_at ?? '')
                ?: $fallback_at;
            $event_order = absint($event->id ?? 0);

            if ($event_at > $participant->progress_completed_at
                || ($event_at === $participant->progress_completed_at && $event_order > $participant->progress_completed_order)) {
                $participant->progress_completed_at = $event_at;
                $participant->progress_completed_order = $event_order;
            }

            foreach ($milestone_distances as $distance) {
                $distance = absint($distance);
                if ($before < $distance && $after >= $distance) {
                    $participant->milestone_completed_at[$distance] = $event_at;
                    $participant->milestone_completed_order[$distance] = $event_order;
                }
            }
        }

        foreach ($milestone_distances as $distance) {
            $distance = absint($distance);
            if ($total < $distance || isset($participant->milestone_completed_at[$distance])) {
                continue;
            }
            $steps_since_milestone = (int) ceil(max(0, $total - $distance) / 1000);
            $participant->milestone_completed_at[$distance] = max(1, $participant->progress_completed_at - $steps_since_milestone);
            $participant->milestone_completed_order[$distance] = 0;
        }
    }

    return $participants;
}

/** Sort participants newest-first by their current point or a milestone crossing. */
function rts_marathon_challenge_sort_recent(&$participants, $milestone_distance = null)
{
    usort($participants, static function ($a, $b) use ($milestone_distance) {
        if (null === $milestone_distance) {
            $a_time = absint($a->progress_completed_at ?? 0);
            $b_time = absint($b->progress_completed_at ?? 0);
            $a_order = absint($a->progress_completed_order ?? 0);
            $b_order = absint($b->progress_completed_order ?? 0);
        } else {
            $distance = absint($milestone_distance);
            $a_time = absint($a->milestone_completed_at[$distance] ?? 0);
            $b_time = absint($b->milestone_completed_at[$distance] ?? 0);
            $a_order = absint($a->milestone_completed_order[$distance] ?? 0);
            $b_order = absint($b->milestone_completed_order[$distance] ?? 0);
        }

        if ($a_time !== $b_time) {
            return $b_time <=> $a_time;
        }
        if ($a_order !== $b_order) {
            return $b_order <=> $a_order;
        }

        return absint($b->id ?? 0) <=> absint($a->id ?? 0);
    });
}

/** Format a stored Captain's Miles value as a race distance. */
function rts_marathon_challenge_distance($miles)
{
    $miles = absint($miles);
    if (0 === $miles) {
        return '0K';
    }
    if (in_array($miles, array(21000, 21100), true)) {
        return '21.1K';
    }
    if (in_array($miles, array(42000, 42200), true)) {
        return '42.2K';
    }

    if ($miles >= 1000 && function_exists('rts_format_miles')) {
        return rts_format_miles(absint($miles));
    }

    if ($miles >= 1000) {
        return rtrim(rtrim(number_format_i18n($miles / 1000, 1), '0'), '.') . 'K';
    }

    return rtrim(rtrim(number_format_i18n($miles / 1000, 1), '0'), '.') . 'K';
}

/** Use the internationally recognised 21.1K label for the half-marathon stop. */
function rts_marathon_challenge_milestone_distance($milestone)
{
    $key = sanitize_key($milestone['key'] ?? '');
    if ('21k' === $key) {
        return 21100;
    }
    if ('42k' === $key) {
        return 42200;
    }

    return absint($milestone['miles'] ?? 0);
}

/** Keep the map's half-marathon point aligned with the supplied 21.1K design. */
function rts_marathon_challenge_map_distance($distance)
{
    $distance = absint($distance);

    return in_array($distance, array(21000, 21100), true)
        ? rts_marathon_challenge_distance(21100)
        : rts_marathon_challenge_distance($distance);
}

/** Return configured trophy artwork, falling back to the bundled milestone badge. */
function rts_marathon_challenge_trophy_url($milestone, $design_assets = array())
{
    $key = sanitize_key($milestone['key'] ?? '');
    $custom_url = !empty($design_assets['trophy_' . $key . '_image'])
        ? esc_url_raw($design_assets['trophy_' . $key . '_image'])
        : '';
    if ($custom_url) {
        return $custom_url;
    }

    if (!empty($milestone['icon_url'])) {
        return esc_url_raw($milestone['icon_url']);
    }

    $bundled_path = RTS_PLUGIN_PATH . 'assets/images/trophies/' . $key . '.png';
    if ($key && file_exists($bundled_path)) {
        return RTS_PLUGIN_URL . 'assets/images/trophies/' . $key . '.png';
    }

    return '';
}

/** Get a compact, non-empty display name for a participant. */
function rts_marathon_challenge_name($participant)
{
    $name = trim((string) ($participant->first_name ?? '') . ' ' . (string) ($participant->last_name ?? ''));

    return $name ?: __('Captain', 'run-the-seas');
}

/** Render a participant avatar using the same WordPress avatar source as the existing leaderboards. */
function rts_marathon_challenge_avatar($participant, $size = 48, $class = '')
{
    $class = trim('rts-mc-avatar ' . $class);
    $name = rts_marathon_challenge_name($participant);

    if (!empty($participant->is_demo)) {
        return '<span class="' . esc_attr($class . ' rts-mc-avatar--demo') . '" style="--rts-demo-avatar:'
            . esc_attr($participant->demo_color ?? '#087fdb') . '" role="img" aria-label="' . esc_attr($name) . '">'
            . esc_html($participant->demo_initials ?? 'DU') . '</span>';
    }

    return get_avatar(
        absint($participant->user_id ?? 0),
        max(24, absint($size)),
        '',
        $name,
        array('class' => $class, 'loading' => 'lazy')
    );
}

/** Anonymous starting avatar used on the public state. */
function rts_marathon_challenge_dummy_avatar($class = '')
{
    return '<span class="rts-mc-avatar rts-mc-avatar--dummy ' . esc_attr($class) . '" aria-hidden="true">'
        . '<svg viewBox="0 0 64 64" focusable="false"><circle cx="32" cy="23" r="13"/><path d="M10 58c2-15 10-22 22-22s20 7 22 22z"/></svg></span>';
}

/**
 * Map progress onto the visual route by travelled path length.
 *
 * The polyline has more points around bends, but distance is measured across the
 * complete line before interpolation. Therefore every 1K consumes the same
 * visible amount of track instead of inheriting uneven milestone spacing.
 */
function rts_marathon_challenge_position($distance, $target = 42000)
{
    // Traced from the centre of the orange route in the 1466x1073 map art.
    // Dense points around bends keep interpolated 1K and staggered lap markers
    // on the route instead of cutting across the inside of a curve.
    $path = array(
        array(20.46, 64.21),
        array(18.96, 62.81),
        array(19.10, 59.18),
        array(17.53, 56.85),
        array(15.35, 54.24),
        array(15.14, 51.72),
        array(16.71, 47.53),
        array(18.55, 43.43),
        array(20.46, 39.79),
        array(20.87, 35.97),
        array(22.92, 33.08),
        array(23.12, 30.38),
        array(21.62, 28.52),
        array(19.17, 26.28),
        array(18.83, 24.51),
        array(16.85, 22.74),
        array(12.96, 21.62),
        array(16.03, 19.29),
        array(19.99, 17.80),
        array(24.49, 15.84),
        array(29.26, 13.33),
        array(33.56, 9.60),
        array(37.93, 10.25),
        array(41.00, 7.74),
        array(45.84, 7.08),
        array(50.34, 7.08),
        array(54.50, 8.48),
        array(57.71, 8.29),
        array(60.03, 9.41),
        array(63.57, 10.53),
        array(68.76, 13.33),
        array(70.74, 15.94),
        array(71.62, 19.38),
        array(73.67, 22.46),
        array(76.06, 26.10),
        array(78.10, 28.42),
        array(79.60, 31.59),
        array(80.42, 35.69),
        array(80.01, 40.54),
        array(80.97, 45.01),
        array(79.47, 51.07),
        array(79.67, 54.99),
        array(78.10, 59.37),
        array(76.06, 61.88),
        array(77.22, 64.40),
        array(79.67, 67.01),
        array(80.42, 69.99),
        array(79.26, 72.32),
        array(77.42, 73.90),
        array(73.53, 76.79),
    );

    $target = rts_normalize_marathon_target($target);
    $distance = min(max(0, absint($distance)), $target);
    $segments = array();
    $path_length = 0.0;
    $horizontal_scale = 1.37; // Compensate for the landscape map aspect ratio.

    for ($index = 1, $count = count($path); $index < $count; $index++) {
        $from = $path[$index - 1];
        $to = $path[$index];
        $segment_length = sqrt(
            pow(($to[0] - $from[0]) * $horizontal_scale, 2)
            + pow($to[1] - $from[1], 2)
        );
        $segments[] = array($from, $to, $segment_length);
        $path_length += $segment_length;
    }

    $travelled = $path_length * ($distance / $target);
    foreach ($segments as $segment) {
        if ($travelled <= $segment[2]) {
            $ratio = $segment[2] > 0 ? $travelled / $segment[2] : 0;

            $x = $segment[0][0] + (($segment[1][0] - $segment[0][0]) * $ratio);
            $y = $segment[0][1] + (($segment[1][1] - $segment[0][1]) * $ratio);

            // Optional per-distance map calibration: array(X, Y), in map
            // percentages. Negative X moves left, positive X moves right;
            // negative Y moves up, positive Y moves down. Keep a zero anchor
            // before and after a corrected section for a smooth return to the
            // traced route. Intermediate/M2 positions are interpolated too.
            $route_adjustments = array(
                1000 => array(0.00, 0.20),
                2000 => array(0.00, 0.20),
                3000 => array(0.20, 0.50),
                4000 => array(0.50, 0.50),
                5000 => array(0.50, 0.50),
                6000 => array(0.20, 0.00),
                7000 => array(0.20, 0.40),
                8000 => array(0.00, 0.70),
                9000 => array(0.00, 1.00),
                10000 => array(0.00, 0.80),
                11000 => array(0.00, 0.50),
                12000 => array(0.00, 0.50),
                13000 => array(0.00, 0.70),
                14000 => array(0.00, 0.80),
                15000 => array(0.00, 1.00),
                16000 => array(0.00, 1.00),
                17000 => array(0.00, 3.00),
                18000 => array(0.00, 1.00),
                19000 => array(0.00, 1.20),
                20000 => array(0.00, 0.40),
                21000 => array(0.00, 0.20),
                22000 => array(0.00, 0.70),
                23000 => array(0.00, 0.00),
                24000 => array(0.00, 0.50),
                25000 => array(0.00, 1.50),
                26000 => array(0.00, 1.80),
                27000 => array(0.00, 1.50),
                28000 => array(-0.15, 0.00),
                29000 => array(0.00, 0.00),
                30000 => array(0.00, 1.00),
                31000 => array(-0.15, 0.50),
                32000 => array(0.50, 0.00),
                33000 => array(-0.60, 0.00),
                34000 => array(-0.46, 0.00),
                35000 => array(-1.16, 0.00),
                36000 => array(0.20, 0.00),
                37000 => array(0.50, 0.00),
                38000 => array(0.50, 0.00),
                39000 => array(0.00, 0.20),
                40000 => array(0.00, 0.00),
                41000 => array(0.00, 0.10),
                42000 => array(0.00, 0.00),
            );
            $adjustment_distances = array_keys($route_adjustments);
            for ($adjustment_index = 1, $adjustment_count = count($adjustment_distances); $adjustment_index < $adjustment_count; $adjustment_index++) {
                $adjustment_from = $adjustment_distances[$adjustment_index - 1];
                $adjustment_to = $adjustment_distances[$adjustment_index];
                if ($distance < $adjustment_from || $distance > $adjustment_to) {
                    continue;
                }
                $adjustment_ratio = ($distance - $adjustment_from) / max(1, $adjustment_to - $adjustment_from);
                $from_adjustment = $route_adjustments[$adjustment_from];
                $to_adjustment = $route_adjustments[$adjustment_to];
                $x += $from_adjustment[0] + (($to_adjustment[0] - $from_adjustment[0]) * $adjustment_ratio);
                $y += $from_adjustment[1] + (($to_adjustment[1] - $from_adjustment[1]) * $adjustment_ratio);
                break;
            }

            return array(round($x, 2), round($y, 2));
        }
        $travelled -= $segment[2];
    }

    return end($path);
}

/**
 * Keep coincident lap groups on the route while giving each marathon its own
 * clickable marker. With two occupied laps this produces half-kilometre map
 * spacing, which remains readable when the user zooms in.
 */
function rts_marathon_challenge_lap_group_position($distance, $target, $marathon, $occupied_marathons)
{
    $occupied_marathons = array_values(array_unique(array_map('absint', (array) $occupied_marathons)));
    sort($occupied_marathons, SORT_NUMERIC);
    $position_index = array_search(absint($marathon), $occupied_marathons, true);
    if (false === $position_index || count($occupied_marathons) < 2) {
        return rts_marathon_challenge_position($distance, $target);
    }

    $spacing = 500;
    $span = (count($occupied_marathons) - 1) * $spacing;
    $start = max(0, min(max(0, absint($target) - $span), absint($distance) - ($span / 2)));
    $placed_distance = $start + ($position_index * $spacing);

    return rts_marathon_challenge_position($placed_distance, $target);
}

/**
 * Place the current captain beside the route instead of directly on it.
 *
 * Pulling the route point toward the island's visual centre naturally places
 * early progress to the right of the left-hand track, the 20K section near the
 * middle, and later progress to the left of the right-hand track.
 */
function rts_marathon_challenge_current_position($distance, $target = 42000)
{
    // Exact current-user positions, expressed as map percentages: array(X, Y).
    // Increase X to move right or Y to move down. Unlisted distances continue
    // to use the automatic beside-the-route calculation below.
    $manual_positions = array(
        1000  => array(27.0, 60.4),
        2000  => array(25.0, 54.4),
        3000  => array(25.0, 50.4),
        4000  => array(25.0, 48.4),
        5000  => array(25.0, 42.4),
        6000  => array(25.0, 38.4),
        7000  => array(27.0, 34.4),
        8000  => array(27.0, 30.4),
        9000  => array(27.0, 24.4),
        10000 => array(27.0, 24.4),
        11000  => array(27.0, 24.4),
        12000  => array(27.0, 24.4),
        13000  => array(27.0, 24.4),
        14000  => array(27.0, 24.4),
        15000 => array(27.0, 24.4),
        16000  => array(30.0, 24.4),
        17000  => array(33.0, 24.4),
        18000  => array(36.0, 24.4),
        19000  => array(40.0, 24.4),
        20000 => array(44.0, 22.4),
        21000 => array(48.0, 22.4),
        22000 => array(51.0, 22.4),
        23000  => array(54.0, 20.4),
        24000  => array(58.0, 20.4),
        25000  => array(62.0, 20.4),
        26000 => array(65.0, 20.4),
        27000  => array(65.0, 20.4),
        28000  => array(65.0, 20.4),
        29000  => array(63.0, 24.4),
        30000 => array(68.0, 26.4),
        31000  => array(72.0, 30.4),
        32000  => array(74.0, 33.4),
        33000  => array(74.0, 37.4),
        34000  => array(74.0, 42.4),
        35000 => array(74.0, 47.4),
        36000  => array(74.0, 52.4),
        37000  => array(72.0, 56.4),
        38000  => array(72.0, 60.4),
        39000  => array(72.0, 65.4),
        40000  => array(72.0, 69.4),
        41000  => array(72.0, 69.4),
        42000 => array(72.0, 69.4),
        
    );

    $distance = absint($distance);
    if (isset($manual_positions[$distance])) {
        return $manual_positions[$distance];
    }

    $route_point = rts_marathon_challenge_position($distance, $target);
    $island_centre = array(50.0, 43.0);

    $x = $route_point[0] + (($island_centre[0] - $route_point[0]) * 0.35);
    $y = $route_point[1] + (($island_centre[1] - $route_point[1]) * 0.40);

    return array(
        round(max(8, min(92, $x)), 2),
        round(max(10, min(90, $y)), 2),
    );
}

/** Select a representative spread while always retaining occupied trophy stops. */
function rts_marathon_challenge_sample_groups($groups, $limit, $current_distance = null, $required_distances = array())
{
    ksort($groups, SORT_NUMERIC);
    if (count($groups) <= $limit) {
        return $groups;
    }

    $keys = array_keys($groups);
    $selected = array();
    foreach ($required_distances as $required_distance) {
        $required_distance = absint($required_distance);
        if (isset($groups[$required_distance])) {
            $selected[$required_distance] = $groups[$required_distance];
        }
    }

    $remaining = max(0, $limit - count($selected));
    while ($remaining > 0) {
        $available_keys = array_values(array_filter($keys, static function ($key) use ($selected) {
            return !isset($selected[$key]);
        }));
        if (!$available_keys) {
            break;
        }

        $best_key = null;
        $best_gap = -1;
        foreach ($available_keys as $candidate_key) {
            $nearest_gap = PHP_INT_MAX;
            foreach (array_keys($selected) as $selected_key) {
                $nearest_gap = min($nearest_gap, abs($candidate_key - $selected_key));
            }
            if ($nearest_gap > $best_gap) {
                $best_gap = $nearest_gap;
                $best_key = $candidate_key;
            }
        }

        if (null === $best_key) {
            break;
        }
        $selected[$best_key] = $groups[$best_key];
        $remaining--;
    }

    if (null !== $current_distance && isset($groups[$current_distance])) {
        $selected[$current_distance] = $groups[$current_distance];
    }

    ksort($selected, SORT_NUMERIC);
    return $selected;
}

/** Render a participant row shared by the side panels and popovers. */
function rts_marathon_challenge_person_row($participant, $distance, $prefix = '', $is_current = false, $rank = null)
{
    $name = $is_current ? __('You', 'run-the-seas') : rts_marathon_challenge_name($participant);
    $lap = rts_marathon_challenge_lap($participant->total_captain_miles_earned ?? 0);
    ?>
    <span class="rts-mc-person<?php echo $is_current ? ' is-you' : ''; ?><?php echo null !== $rank ? ' has-rank' : ''; ?>">
        <?php if (null !== $rank) : ?><i class="rts-mc-person__rank"><?php echo esc_html(absint($rank)); ?></i><?php endif; ?>
        <?php echo rts_marathon_challenge_avatar($participant, 42); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <span class="rts-mc-person__name"><?php echo esc_html($prefix . $name); ?></span>
        <strong><?php echo esc_html(rts_marathon_challenge_distance($distance)); ?></strong>
        <?php if ($lap['marathon'] > 1) : ?><small>M<?php echo esc_html($lap['marathon']); ?></small><?php endif; ?>
    </span>
    <?php
}

/** Render a popup containing everyone represented by a map or milestone group. */
function rts_marathon_challenge_popup($id, $title, $participants, $distance, $use_total = false, $frame_url = '', $variant = 'compact', $icon_url = '', $right_icon_url = '', $badge_label = '')
{
    $frame_style = $frame_url
        ? '--rts-mc-popup-frame:url("' . esc_url_raw($frame_url) . '");'
        : '';
    $is_group = 'group' === $variant;
    $display_limit = $is_group ? 10 : 50;
    ?>
    <span class="rts-mc-popover<?php echo $frame_url ? ' has-artwork-frame' : ''; ?><?php echo $is_group ? ' rts-mc-popover--group' : ''; ?>" id="<?php echo esc_attr($id); ?>" role="tooltip"<?php if ($frame_style) : ?> style="<?php echo esc_attr($frame_style); ?>"<?php endif; ?>>
        <?php if ($is_group) : ?>
            <span class="rts-mc-popover__group-head">
                <span class="rts-mc-popover__group-icon"><?php if ($icon_url) : ?><img src="<?php echo esc_url($icon_url); ?>" alt="" aria-hidden="true"><?php else : ?>🏆<?php endif; ?></span>
                <span><strong><?php echo esc_html($title); ?></strong><small><?php esc_html_e('Top 10 members', 'run-the-seas'); ?></small></span>
                <i class="rts-mc-popover__right-icon" aria-hidden="true"><?php if ($right_icon_url) : ?><img src="<?php echo esc_url($right_icon_url); ?>" alt=""><?php else : ?>⚓<?php endif; ?></i>
            </span>
        <?php else : ?>
            <strong class="rts-mc-popover__title has-header-controls">
                <span class="rts-mc-popover__compact-badge<?php echo $icon_url ? ' has-artwork' : ''; ?>"><?php if ($icon_url) : ?><img src="<?php echo esc_url($icon_url); ?>" alt="" aria-hidden="true"><?php endif; ?><b><?php echo esc_html($badge_label ?: $title); ?></b></span>
                <span class="rts-mc-popover__title-label"><?php echo esc_html($title); ?></span>
                <i class="rts-mc-popover__right-icon" aria-hidden="true"><?php if ($right_icon_url) : ?><img src="<?php echo esc_url($right_icon_url); ?>" alt=""><?php else : ?>⌃<?php endif; ?></i>
            </strong>
        <?php endif; ?>
        <span class="rts-mc-popover__list">
            <?php foreach (array_slice($participants, 0, $display_limit) as $participant_index => $participant) : ?>
                <?php
                $row_distance = $use_total
                    ? absint($participant->total_captain_miles_earned ?? 0)
                    : $distance;
                rts_marathon_challenge_person_row($participant, $row_distance, '', !empty($participant->is_current), $is_group ? $participant_index + 1 : null);
                ?>
            <?php endforeach; ?>
        </span>
        <?php if (count($participants) > $display_limit) : ?>
            <span class="rts-mc-popover__more"><?php echo esc_html(sprintf(__('%d more captains', 'run-the-seas'), count($participants) - $display_limit)); ?></span>
        <?php endif; ?>
    </span>
    <?php
}

/**
 * New, separate 42.2K map. The existing [rts_virtual_marathon] shortcode is
 * intentionally left untouched.
 *
 * Usage: [rts_marathon_challenge]
 */
function rts_marathon_challenge_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'target'    => 42000,
        'map_image' => '',
    ), $atts, 'rts_marathon_challenge');
    $target = rts_normalize_marathon_target($atts['target']);
    $design_assets = get_option('rts_marathon_challenge_design_assets', array());
    $design_assets = is_array($design_assets) ? $design_assets : array();
    $asset = static function ($key) use ($design_assets) {
        return esc_url($design_assets[$key] ?? '');
    };
    $saved_map_url = esc_url($design_assets['map_image'] ?? '');
    $default_map_path = RTS_PLUGIN_PATH . 'assets/images/marathon-challenge-island.png';
    $default_map_url = file_exists($default_map_path)
        ? RTS_PLUGIN_URL . 'assets/images/marathon-challenge-island.png'
        : '';
    $map_url = $atts['map_image']
        ? esc_url($atts['map_image'])
        : ($saved_map_url ?: $default_map_url);
    $current_user_frame_url = $asset('current_user_avatar_frame_image') ?: $asset('guest_avatar_frame_image');
    $position_marker_url = $asset('user_position_marker_image');
    $position_marker_selected_url = $asset('user_position_marker_selected_image');
    // Marathon 2 uses its own normal milestone pin, the standard M1 selected
    // pin on hover, a persistent M2 badge beside the avatar, and a separate
    // artwork frame layered over the avatar.
    $marathon2_position_marker_url = $asset('marathon2_position_marker_image')
        ?: $position_marker_url;
    $marathon2_map_badge_url = $asset('marathon2_position_marker_selected_image');
    $marathon2_avatar_frame_url = $asset('marathon2_badge_image');
    $shared_heading_divider_url = $asset('panel_heading_divider_image');
    $top_four_divider_url = $asset('top_four_icon_image') ?: $shared_heading_divider_url;
    $around_you_divider_url = $asset('around_you_icon_image') ?: $shared_heading_divider_url;
    $milestones_divider_url = $asset('milestones_icon_image') ?: $shared_heading_divider_url;
    $over_target_divider_url = $asset('over_target_icon_image') ?: $shared_heading_divider_url;
    $around_you_current_frame_url = $asset('around_you_current_frame_image');
    $milestone_active_frame_url = $asset('milestone_active_frame_image');
    $popup_frame_url = $asset('user_list_popup_frame_image');
    $user_list_header_right_icon_url = $asset('user_list_header_right_icon_image');
    $milestone_group_header_right_icon_url = $asset('milestone_group_header_right_icon_image');
    $finisher_avatar_frame_url = $asset('finisher_avatar_frame_image');
    $finisher_rank_icon_url = $asset('finisher_rank_icon_image');
    $around_you_arrow_urls = array(
        'up'    => $asset('around_you_up_arrow_image'),
        'down'  => $asset('around_you_down_arrow_image'),
        'right' => $asset('around_you_right_arrow_image'),
    );

    wp_enqueue_style(
        'rts-marathon-challenge',
        RTS_PLUGIN_URL . 'assets/css/marathon-challenge.css',
        array(),
        RTS_VERSION . '.' . filemtime(RTS_PLUGIN_PATH . 'assets/css/marathon-challenge.css')
    );
    // Keep the grid stable even when a PWA/service-worker cache temporarily
    // serves an older stylesheet that still gives the map an intrinsic ratio.
    wp_add_inline_style(
        'rts-marathon-challenge',
        '.rts-mc-map{width:100%!important;min-width:0!important;aspect-ratio:auto!important;}'
        . '.rts-mc-map__viewport{transform:none!important;will-change:auto!important;}'
        . '.rts-mc-map__art{transform:translate3d(var(--rts-map-pan-x,0px),var(--rts-map-pan-y,0px),0) scale(var(--rts-map-zoom,1))!important;transform-origin:50% 50%!important;}'
        . '.rts-mc-marker--milestone-empty{z-index:3;pointer-events:none;}'
        . '.rts-mc-marker--milestone-empty>.rts-mc-marker__distance{display:grid;}'
        . '.rts-mc-marker--milestone-empty>.rts-mc-marker__distance.has-artwork{place-items:start center!important;}'
        . '.rts-mc-marker--milestone-empty>.rts-mc-marker__distance:not(.has-artwork){place-items:center!important;}'
        . '.rts-mc-marker{transform:translate(calc(-50% + var(--rts-marker-offset-x,0px)),calc(var(--rts-marker-offset-y,0px) + var(--rts-marker-track-anchor-y,0px)))!important;transform-origin:50% 0!important;}'
        . '.rts-mc-marker--route-user{--rts-marker-track-anchor-y:-35px!important;}'
        . '.rts-mc-marker--route-user.rts-mc-marker--artwork-pin{--rts-marker-track-anchor-y:-47px!important;}'
        . '.rts-mc-marker.is-before-half-marathon,.rts-mc-marker.is-half-marathon{--rts-marker-nudge-x:0px!important;--rts-marker-nudge-y:0px!important;}'
        . '.rts-mc-marker--you{transform:translate(-50%,-50%)!important;transform-origin:50% 50%!important;}'
        . '.rts-mc-marker>.rts-mc-marker__distance,.rts-mc-marker>button>.rts-mc-marker__distance{transform:translateY(-50%)!important;}'
        . '.rts-mc-marker__distance,.rts-mc-marker__badge{position:relative!important;z-index:7!important;}'
        . '.rts-mc-marker__badge{position:absolute!important;}'
        . '.rts-mc-marker>button:after{display:none!important;}'
        . '.rts-mc-marker>button>.rts-mc-marker__distance.has-artwork+.rts-mc-map-avatar{margin-top:-15px!important;}'
        . '.rts-mc-marker>button>.rts-mc-marker__distance:not(.has-artwork)+.rts-mc-map-avatar{margin-top:-3px!important;}'
        . '.rts-mc-marker .rts-mc-avatar{width:clamp(28px,2.7vw,42px)!important;height:clamp(28px,2.7vw,42px)!important;}'
        . '.rts-mc-marker--you .rts-mc-avatar{width:clamp(48px,4.4vw,58px)!important;height:clamp(48px,4.4vw,58px)!important;}'
        . '.rts-mc-current-avatar{width:72px!important;height:72px!important;}'
        . '.rts-mc-marker--start .rts-mc-current-avatar{width:60px!important;height:60px!important;}'
        . '.rts-mc-marker--you .rts-mc-current-avatar.has-artwork-frame .rts-mc-avatar{width:74%!important;height:74%!important;}'
        . '.rts-mc-marker__you{min-width:72px!important;margin-top:4px!important;padding:3px 7px!important;font-size:clamp(10px,1.15vw,16px)!important;}'
        . '.rts-mc-marker__distance.has-artwork{width:44px!important;height:51px!important;margin-bottom:-6px!important;padding:11px 4px 0!important;}'
        . '.rts-mc-marker__name{max-width:84px!important;margin-top:0!important;font-size:clamp(7px,.7vw,10px)!important;line-height:1.05!important;}'
        . '.rts-mc-map-avatar>.rts-mc-avatar{position:relative!important;z-index:1!important;}'
        . '.rts-mc-map-avatar.has-artwork-frame{width:42px!important;height:42px!important;}'
        . '.rts-mc-map-avatar.has-artwork-frame>.rts-mc-avatar{z-index:3!important;width:33px!important;height:33px!important;transform:translateY(-4px)!important;border:0!important;box-shadow:none!important;}'
        . '.rts-mc-map-avatar__badge{z-index:7!important;}'
        . '.rts-mc-map-avatar__frame{position:absolute!important;z-index:2!important;top:50%!important;left:50%!important;width:56px!important;max-width:none!important;height:46px!important;transform:translate(-50%,-50%)!important;object-fit:contain!important;pointer-events:none!important;}'
        . '.rts-mc-marker>button>.rts-mc-map-avatar:before{content:""!important;position:absolute!important;z-index:0!important;top:-9px!important;left:50%!important;width:2px!important;height:10px!important;transform:translateX(-50%)!important;background:#fff!important;box-shadow:0 0 0 1px rgba(0,15,27,.92),0 0 4px rgba(255,255,255,.9)!important;}'
        . '.rts-mc-popover--group{width:min(286px,72vw)!important;max-height:430px!important;overflow:hidden!important;border-radius:2px!important;}'
        . '.rts-mc-popover__group-head{min-height:66px!important;display:grid!important;grid-template-columns:58px minmax(0,1fr) 24px!important;align-items:center!important;gap:7px!important;padding:6px 8px!important;border-bottom:1px solid var(--mc-gold)!important;background:#00111e!important;}'
        . '.rts-mc-popover__group-icon{width:54px!important;height:54px!important;display:grid!important;place-items:center!important;overflow:hidden!important;border:1px solid var(--mc-gold)!important;border-radius:50%!important;}'
        . '.rts-mc-popover__group-icon>img{width:100%!important;height:100%!important;display:block!important;object-fit:contain!important;}'
        . '.rts-mc-popover__group-head>span:nth-child(2){min-width:0!important;}.rts-mc-popover__group-head strong,.rts-mc-popover__group-head small{display:block!important;text-transform:uppercase!important;}'
        . '.rts-mc-popover__group-head strong{color:var(--mc-gold-light)!important;font:500 25px/1 var(--mc-number-font)!important;}.rts-mc-popover__group-head small{margin-top:4px!important;color:var(--mc-cream)!important;font-size:10px!important;}'
        . '.rts-mc-popover__group-head>i{color:var(--mc-gold-light)!important;font-size:22px!important;font-style:normal!important;}'
        . '.rts-mc-popover__right-icon{width:18px!important;height:18px!important;display:grid!important;flex:0 0 auto!important;place-items:center!important;overflow:hidden!important;color:var(--mc-gold-light)!important;font-style:normal!important;}.rts-mc-popover__right-icon>img{width:100%!important;height:100%!important;display:block!important;object-fit:contain!important;}'
        . '.rts-mc-popover--group .rts-mc-popover__list{max-height:326px!important;padding:4px 7px 6px!important;overflow-y:auto!important;}'
        . '.rts-mc-popover--group .rts-mc-person.has-rank{position:relative!important;min-height:31px!important;grid-template-columns:16px 27px minmax(0,1fr) auto!important;gap:5px!important;padding:2px 1px!important;}'
        . '.rts-mc-popover--group .rts-mc-person__rank{color:var(--mc-gold-light)!important;font:12px/1 var(--mc-number-font)!important;font-style:normal!important;text-align:center!important;}'
        . '.rts-mc-popover--group .rts-mc-person .rts-mc-avatar{width:27px!important;height:27px!important;border-width:1px!important;box-shadow:none!important;}'
        . '.rts-mc-popover--group .rts-mc-person__name,.rts-mc-popover--group .rts-mc-person>strong{font-size:11px!important;}.rts-mc-popover--group .rts-mc-person>small{position:absolute!important;z-index:2!important;bottom:0!important;left:32px!important;min-width:14px!important;min-height:12px!important;margin:0!important;font-size:7px!important;}'
        . '.rts-mc-milestone .rts-mc-popover--group{right:calc(100% + 12px)!important;}'
        . '.rts-mc-marker>.rts-mc-popover{top:-24px!important;right:calc(50% + 30px)!important;left:auto!important;width:min(154px,60vw)!important;max-height:none!important;transform:none!important;overflow:visible!important;}'
        . '.rts-mc-marker.is-popover-overlap>.rts-mc-popover{right:auto!important;left:calc(50% + 30px)!important;}'
        . '.rts-mc-marker>.rts-mc-popover:after{content:""!important;position:absolute!important;top:48px!important;left:100%!important;width:10px!important;height:2px!important;background:var(--mc-gold-light)!important;box-shadow:0 0 0 1px rgba(0,15,27,.9)!important;}'
        . '.rts-mc-marker.is-popover-overlap>.rts-mc-popover:after{right:100%!important;left:auto!important;}'
        . '.rts-mc-marker>.rts-mc-popover .rts-mc-popover__title{min-height:24px!important;padding:5px 24px 4px 7px!important;font-size:11px!important;line-height:1.1!important;text-align:left!important;}'
        . '.rts-mc-marker>.rts-mc-popover .rts-mc-popover__title.has-header-controls{min-height:33px!important;display:flex!important;align-items:center!important;gap:5px!important;padding:2px 4px!important;}.rts-mc-marker>.rts-mc-popover .rts-mc-popover__title.has-header-controls:after{display:none!important;}'
        . '.rts-mc-popover__compact-badge{position:relative!important;width:25px!important;height:29px!important;display:grid!important;flex:0 0 25px!important;place-items:center!important;}.rts-mc-popover__compact-badge>img{position:absolute!important;inset:0!important;width:100%!important;height:100%!important;display:block!important;object-fit:contain!important;}.rts-mc-popover__compact-badge>b{position:relative!important;z-index:1!important;top:-2px!important;color:#191006!important;font:700 7px/1 var(--mc-number-font)!important;white-space:nowrap!important;}'
        . '.rts-mc-popover__compact-badge:not(.has-artwork){height:25px!important;border:1px solid #7f4f06!important;border-radius:50%!important;background:radial-gradient(circle at 35% 25%,#ffe7a1,#d89a2f)!important;}.rts-mc-popover__compact-badge:not(.has-artwork)>b{top:0!important;}'
        . '.rts-mc-popover__title-label{min-width:0!important;flex:1 1 auto!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important;}'
        . '.rts-mc-marker>.rts-mc-popover .rts-mc-popover__list{max-height:166px!important;padding:3px 4px 4px!important;overflow-y:auto!important;}'
        . '.rts-mc-marker>.rts-mc-popover .rts-mc-person{min-height:21px!important;grid-template-columns:18px minmax(0,1fr) auto!important;gap:4px!important;padding:1px 2px!important;}'
        . '.rts-mc-marker>.rts-mc-popover .rts-mc-person .rts-mc-avatar{width:18px!important;height:18px!important;border-width:1px!important;font-size:7px!important;box-shadow:none!important;}'
        . '.rts-mc-marker>.rts-mc-popover .rts-mc-person__name,.rts-mc-marker>.rts-mc-popover .rts-mc-person>strong{font-size:9px!important;line-height:1!important;}'
        . '.rts-mc-marker>.rts-mc-popover .rts-mc-person>small{min-width:14px!important;min-height:12px!important;margin-top:-9px!important;font-size:7px!important;}'
        . '.rts-mc-km-tick{transform:translate(-50%,-50%)!important;}'
        . '.rts-mc-finishers{width:min(70%,264px)!important;transform:translateX(-50%)!important;}'
        . '.rts-mc-finishers>span{gap:3px!important;}'
        . '.rts-mc-finisher{width:calc((100% - 9px)/4)!important;min-width:0!important;padding:0!important;border:0!important;border-radius:0!important;background:transparent!important;}'
        . '.rts-mc-finisher .rts-mc-avatar{width:52px!important;height:52px!important;}'
        . '.rts-mc-finisher__portrait{width:58px!important;height:58px!important;}'
        . '.rts-mc-finisher>b{display:-webkit-box!important;margin-top:8px!important;padding:4px 3px 0!important;border:1px solid rgba(227,155,8,.75)!important;border-bottom:0!important;border-radius:4px 4px 0 0!important;background:rgba(0,12,22,.9)!important;font-size:9px!important;}'
        . '.rts-mc-finisher>strong{width:100%!important;padding:1px 3px 4px!important;border:1px solid rgba(227,155,8,.75)!important;border-top:0!important;border-radius:0 0 4px 4px!important;background:rgba(0,12,22,.9)!important;}'
    );
    wp_enqueue_script(
        'rts-marathon-challenge',
        RTS_PLUGIN_URL . 'assets/js/marathon-challenge.js',
        array(),
        RTS_VERSION . '.' . filemtime(RTS_PLUGIN_PATH . 'assets/js/marathon-challenge.js'),
        true
    );

    global $wpdb;
    $participants_table = $wpdb->prefix . 'rts_participants';
    $participants = $wpdb->get_results(
        "SELECT id, user_id, first_name, last_name, country, captain_miles_balance, total_captain_miles_earned, registration_date, updated_at
        FROM {$participants_table}
        ORDER BY total_captain_miles_earned DESC, captain_miles_balance DESC, id ASC
        LIMIT 500"
    );
    $participants = is_array($participants) ? $participants : array();
    $current = function_exists('rts_get_current_member_participant') ? rts_get_current_member_participant() : null;
    // Demo participants are deterministic, request-only, and never written to
    // the database, so the preview URL is safe to render without an admin cap.
    $demo_mode = isset($_GET['rts_marathon_demo'])
        && '1' === sanitize_text_field(wp_unslash($_GET['rts_marathon_demo']));
    $demo_state = isset($_GET['rts_marathon_demo_state'])
        ? sanitize_key(wp_unslash($_GET['rts_marathon_demo_state']))
        : (isset($_GET['rts_marathon_demo_progress']) ? 'member' : 'public');
    $demo_progress = isset($_GET['rts_marathon_demo_progress'])
        ? min(absint(wp_unslash($_GET['rts_marathon_demo_progress'])), $target * 3)
        : 0;

    if ($demo_mode) {
        if (!headers_sent()) {
            nocache_headers();
        }
        $participants = rts_marathon_challenge_demo_participants($target);
        if ('public' === $demo_state) {
            $current = null;
        } else {
            $current = (object) array(
                'id'                         => 999999,
                'user_id'                    => 0,
                'first_name'                 => 'Demo',
                'last_name'                  => 'You',
                'country'                    => 'Demo',
                'captain_miles_balance'      => $demo_progress,
                'total_captain_miles_earned' => $demo_progress,
                'is_demo'                    => true,
                'demo_initials'              => 'YOU',
                'demo_color'                 => '#087fdb',
            );
        }
    }
    $current_id = $current ? absint($current->id) : 0;
    $is_logged_in = ($demo_mode && $current) || (is_user_logged_in() && $current);
    $current_lap = rts_marathon_challenge_lap($current->total_captain_miles_earned ?? 0, $target);

    foreach ($participants as $participant) {
        $participant->lap = rts_marathon_challenge_lap($participant->total_captain_miles_earned ?? 0, $target);
        $participant->is_current = $current_id && $current_id === absint($participant->id);
    }

    if ($current && !wp_list_filter($participants, array('id' => $current_id))) {
        $current->lap = $current_lap;
        $current->is_current = true;
        $participants[] = $current;
    }

    $milestones = array_values(array_filter(rts_get_captains_milestones(), function ($milestone) use ($target) {
        return !empty($milestone['miles']) && absint($milestone['miles']) <= $target;
    }));
    $milestone_distances = array_values(array_unique(array_map(static function ($milestone) {
        return absint($milestone['miles'] ?? 0);
    }, $milestones)));
    $timeline_rows = array();
    if (!$demo_mode && $participants) {
        $participant_ids = array_values(array_unique(array_filter(array_map(static function ($participant) {
            return absint($participant->id ?? 0);
        }, $participants))));
        if ($participant_ids) {
            $timeline_table = $wpdb->prefix . 'rts_timeline';
            $timeline_rows = $wpdb->get_results(
                "SELECT id, participant_id, activity_data, activity_date, created_at
                FROM {$timeline_table}
                WHERE activity_type = 'captain_miles_earned'
                AND participant_id IN (" . implode(',', $participant_ids) . ")
                ORDER BY participant_id ASC, activity_date ASC, id ASC"
            );
        }
    }
    $participants = rts_marathon_challenge_add_recency($participants, $timeline_rows, $milestone_distances);

    $top_four = array_slice(array_values(array_filter($participants, function ($participant) {
        return absint($participant->total_captain_miles_earned ?? 0) > 0;
    })), 0, 4);
    $finishers = array_values(array_filter($participants, function ($participant) use ($target) {
        return absint($participant->total_captain_miles_earned ?? 0) >= $target;
    }));
    rts_marathon_challenge_sort_recent($finishers, $target);
    $finishers = array_slice($finishers, 0, 4);

    $around = array();
    $around_marathon = $is_logged_in ? absint($current_lap['marathon']) : 1;
    $around_pool = array_values(array_filter($participants, function ($participant) use ($around_marathon) {
        return absint($participant->lap['marathon'] ?? 1) === $around_marathon;
    }));
    if ($is_logged_in) {
        $by_lap = $around_pool;
        usort($by_lap, function ($a, $b) {
            if ($a->lap['distance'] === $b->lap['distance']) {
                return absint($a->id) <=> absint($b->id);
            }
            return $b->lap['distance'] <=> $a->lap['distance'];
        });
        $position = 0;
        foreach ($by_lap as $index => $participant) {
            if (!empty($participant->is_current)) {
                $position = $index;
                break;
            }
        }
        $start = max(0, min(count($by_lap) - 9, $position - 4));
        $around = array_slice($by_lap, $start, 9);
    } else {
        $around = $around_pool;
        usort($around, function ($a, $b) {
            if ($a->lap['distance'] === $b->lap['distance']) {
                return absint($a->id) <=> absint($b->id);
            }
            return $a->lap['distance'] <=> $b->lap['distance'];
        });
        $around = array_slice($around, 0, 9);
    }

    $groups = array();
    $groups_by_marathon = array();
    foreach ($participants as $participant) {
        $distance = absint($participant->lap['distance']);
        $marathon = max(1, absint($participant->lap['marathon'] ?? 1));
        if (!isset($groups[$distance])) {
            $groups[$distance] = array();
        }
        $groups[$distance][] = $participant;
        if (!isset($groups_by_marathon[$marathon])) {
            $groups_by_marathon[$marathon] = array();
        }
        if (!isset($groups_by_marathon[$marathon][$distance])) {
            $groups_by_marathon[$marathon][$distance] = array();
        }
        $groups_by_marathon[$marathon][$distance][] = $participant;
    }
    foreach ($groups as &$group_members) {
        rts_marathon_challenge_sort_recent($group_members);
    }
    unset($group_members);
    foreach ($groups_by_marathon as &$marathon_groups) {
        foreach ($marathon_groups as &$marathon_group_members) {
            rts_marathon_challenge_sort_recent($marathon_group_members);
        }
        unset($marathon_group_members);
    }
    unset($marathon_groups);

    // The current captain is rendered once as the larger YOU marker below.
    // Keep everyone else in the normal route groups so peers at the same
    // distance still remain visible and available in their group popup.
    $route_groups_by_marathon = array();
    foreach ($groups_by_marathon as $marathon => $marathon_groups) {
        foreach ($marathon_groups as $distance => $members) {
            $route_members = array_values(array_filter($members, static function ($participant) {
                return empty($participant->is_current);
            }));
            if ($route_members) {
                $route_groups_by_marathon[$marathon][$distance] = $route_members;
            }
        }
    }

    $zero_members = array_values(array_filter($groups[0] ?? array(), function ($participant) {
        return empty($participant->is_current);
    }));
    $show_zero_start_markers = !$is_logged_in || 0 === absint($current_lap['distance']);
    $zero_top = $show_zero_start_markers ? array_slice($zero_members, 0, 3) : array();
    $zero_remaining = $show_zero_start_markers ? array_slice($zero_members, 3) : array();
    $map_groups = array();
    foreach ($route_groups_by_marathon as $marathon => $marathon_groups) {
        foreach ($marathon_groups as $distance => $members) {
            if (0 === absint($distance)) {
                continue;
            }
            $map_groups[] = array(
                'marathon' => absint($marathon),
                'distance' => absint($distance),
                'members'  => $members,
            );
        }
    }
    // Every occupied route point is rendered. Only trophy milestones receive
    // badge-only fallbacks when no captain is currently at that exact point.
    $milestone_groups = array();
    foreach ($milestones as $milestone) {
        $distance = absint($milestone['miles']);
        // Milestone groups represent everyone who has achieved the threshold,
        // not only captains whose current position is exactly on the marker.
        $milestone_groups[$distance] = array_values(array_filter($participants, static function ($participant) use ($distance) {
            return absint($participant->total_captain_miles_earned ?? 0) >= $distance;
        }));
        rts_marathon_challenge_sort_recent($milestone_groups[$distance], $distance);
    }
    $over_target = array_values(array_filter($participants, function ($participant) use ($target) {
        return absint($participant->total_captain_miles_earned ?? 0) > $target;
    }));
    rts_marathon_challenge_sort_recent($over_target);

    $logo_id = absint(get_theme_mod('custom_logo'));
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';

    ob_start();
    ?>
    <section class="rts-marathon-challenge" data-rts-marathon-challenge aria-labelledby="rts-mc-title">
        <?php if ($demo_mode) : ?><div class="rts-mc-demo-banner" role="status"><strong><?php esc_html_e('Demo data', 'run-the-seas'); ?></strong> — <?php esc_html_e('temporary and not saved', 'run-the-seas'); ?> <a href="<?php echo esc_url(remove_query_arg(array('rts_marathon_demo', 'rts_marathon_demo_state', 'rts_marathon_demo_progress'))); ?>"><?php esc_html_e('Exit demo', 'run-the-seas'); ?></a></div><?php endif; ?>
        <nav class="rts-mc-nav" aria-label="<?php esc_attr_e('Marathon challenge navigation', 'run-the-seas'); ?>">
            <a class="rts-mc-brand" href="<?php echo esc_url(home_url('/')); ?>">
                <?php if ($logo_url) : ?><img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>"><?php else : ?><span aria-hidden="true">⚓</span><b>Run The Seas<small>Yacht Club</small></b><?php endif; ?>
            </a>
            <span class="rts-mc-nav__links">
                <a href="<?php echo esc_url(home_url('/race-rules/')); ?>"><?php esc_html_e('Race Rules', 'run-the-seas'); ?></a>
                <button type="button" data-rts-mc-print><?php esc_html_e('Email / Print', 'run-the-seas'); ?></button>
                <a href="<?php echo esc_url(home_url('/leaderboard/')); ?>"><?php esc_html_e('Leaderboard', 'run-the-seas'); ?></a>
                <a href="<?php echo esc_url(home_url('/captains-suite/')); ?>"><?php esc_html_e("Captain's Suite", 'run-the-seas'); ?></a>
                <a href="<?php echo esc_url(home_url('/faq/')); ?>"><?php esc_html_e('FAQ', 'run-the-seas'); ?></a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'run-the-seas'); ?></a>
            </span>
            <span class="rts-mc-nav__actions">
                <a class="is-gold" href="<?php echo esc_url(home_url('/survey/')); ?>">⚓ <?php esc_html_e('Take the Survey', 'run-the-seas'); ?></a>
                <a href="#rts-mc-map">▶ <?php esc_html_e('Play', 'run-the-seas'); ?></a>
            </span>
        </nav>

        <header class="rts-mc-header<?php echo $asset('header_frame_image') ? ' has-artwork-frame' : ''; ?>">
            <?php if ($asset('header_frame_image')) : ?><img class="rts-mc-header__frame" src="<?php echo esc_url($asset('header_frame_image')); ?>" alt="" aria-hidden="true"><?php endif; ?>
            <h1 id="rts-mc-title"><?php esc_html_e('The 42.2K Referral Marathon Challenge', 'run-the-seas'); ?></h1>
        </header>

        <div class="rts-mc-layout">
            <aside class="rts-mc-sidebar rts-mc-sidebar--left">
                <section class="rts-mc-panel<?php echo $asset('top_four_frame_image') ? ' has-artwork-frame' : ''; ?><?php echo $top_four_divider_url ? ' has-heading-divider' : ''; ?>">
                    <?php if ($asset('top_four_frame_image')) : ?><img class="rts-mc-panel__frame" src="<?php echo esc_url($asset('top_four_frame_image')); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <h2><?php esc_html_e('Top 4', 'run-the-seas'); ?></h2>
                    <?php if ($top_four_divider_url) : ?><img class="rts-mc-heading-divider" src="<?php echo esc_url($top_four_divider_url); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <div class="rts-mc-panel__body rts-mc-top-four">
                        <?php if ($top_four) : foreach ($top_four as $index => $participant) : ?>
                            <span class="rts-mc-ranked"><i><?php echo esc_html($index + 1); ?></i><?php rts_marathon_challenge_person_row($participant, absint($participant->total_captain_miles_earned ?? 0), '', !empty($participant->is_current)); ?></span>
                        <?php endforeach; else : ?><em class="rts-mc-empty"><?php esc_html_e('The starting line is waiting.', 'run-the-seas'); ?></em><?php endif; ?>
                    </div>
                </section>

                <section class="rts-mc-panel rts-mc-around<?php echo $asset('around_you_frame_image') ? ' has-artwork-frame' : ''; ?><?php echo $around_you_divider_url ? ' has-heading-divider' : ''; ?>">
                    <?php if ($asset('around_you_frame_image')) : ?><img class="rts-mc-panel__frame" src="<?php echo esc_url($asset('around_you_frame_image')); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <h2><?php esc_html_e('Around You', 'run-the-seas'); ?></h2>
                    <?php if ($around_you_divider_url) : ?><img class="rts-mc-heading-divider" src="<?php echo esc_url($around_you_divider_url); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <div class="rts-mc-panel__body">
                        <?php if (!$is_logged_in) : ?>
                            <span class="rts-mc-around__row is-you<?php echo $around_you_current_frame_url ? ' has-artwork-frame' : ''; ?>"><?php if ($around_you_current_frame_url) : ?><img class="rts-mc-around__row-frame" src="<?php echo esc_url($around_you_current_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?><i><?php if ($around_you_arrow_urls['right']) : ?><img src="<?php echo esc_url($around_you_arrow_urls['right']); ?>" alt="" aria-hidden="true"><?php else : ?>➜<?php endif; ?></i><?php echo rts_marathon_challenge_dummy_avatar(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><b><?php esc_html_e('You Are Here', 'run-the-seas'); ?></b><strong>0K</strong></span>
                        <?php endif; ?>
                        <?php foreach ($around as $participant) : ?>
                            <?php
                            $is_current_row = !empty($participant->is_current);
                            $distance = absint($participant->lap['distance']);
                            $trend_key = $is_current_row || $distance === absint($current_lap['distance'])
                                ? 'right'
                                : ($distance > absint($current_lap['distance']) ? 'up' : 'down');
                            $trend_fallback = array('up' => '▲', 'down' => '▼', 'right' => '➜');
                            ?>
                            <span class="rts-mc-around__row<?php echo $is_current_row ? ' is-you' : ''; ?><?php echo $is_current_row && $around_you_current_frame_url ? ' has-artwork-frame' : ''; ?>"><?php if ($is_current_row && $around_you_current_frame_url) : ?><img class="rts-mc-around__row-frame" src="<?php echo esc_url($around_you_current_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?><i><?php if ($around_you_arrow_urls[$trend_key]) : ?><img src="<?php echo esc_url($around_you_arrow_urls[$trend_key]); ?>" alt="" aria-hidden="true"><?php else : ?><?php echo esc_html($trend_fallback[$trend_key]); ?><?php endif; ?></i><?php echo rts_marathon_challenge_avatar($participant, 42); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><b><?php echo esc_html($is_current_row ? __('You', 'run-the-seas') : rts_marathon_challenge_name($participant)); ?></b><strong><?php echo esc_html(rts_marathon_challenge_distance($distance)); ?></strong><?php if ($participant->lap['marathon'] > 1) : ?><small>M<?php echo esc_html($participant->lap['marathon']); ?></small><?php endif; ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <div class="rts-mc-motto"><?php if ($asset('footer_icon_image')) : ?><img class="rts-mc-footer-icon" src="<?php echo esc_url($asset('footer_icon_image')); ?>" alt="" aria-hidden="true"><?php else : ?><span aria-hidden="true">☸</span><?php endif; ?><p><?php esc_html_e('Every referral moves us forward.', 'run-the-seas'); ?><br><?php esc_html_e('Together, we Run The Seas!', 'run-the-seas'); ?></p></div>
            </aside>

            <main class="rts-mc-map" id="rts-mc-map" data-rts-mc-map tabindex="0" role="region" aria-label="<?php esc_attr_e('Interactive marathon milestone map', 'run-the-seas'); ?>">
                <div class="rts-mc-map__viewport" data-rts-mc-map-viewport>
                <?php if ($map_url) : ?><img class="rts-mc-map__art" src="<?php echo esc_url($map_url); ?>" alt="<?php esc_attr_e('Tropical island with a 42.2K marathon route', 'run-the-seas'); ?>"><?php endif; ?>

                <?php if ($finishers) : ?>
                    <section class="rts-mc-finishers" aria-label="<?php esc_attr_e('Marathon finishers', 'run-the-seas'); ?>">
                        <h2<?php echo $asset('finishers_frame_image') ? ' class="has-artwork"' : ''; ?>><?php if ($asset('finishers_frame_image')) : ?><img src="<?php echo esc_url($asset('finishers_frame_image')); ?>" alt="" aria-hidden="true"><?php endif; ?><span><?php esc_html_e('Marathon Finishers', 'run-the-seas'); ?></span></h2>
                        <span><?php foreach ($finishers as $index => $participant) : ?><span class="rts-mc-finisher"><span class="rts-mc-finisher__portrait<?php echo $finisher_avatar_frame_url ? ' has-artwork-frame' : ''; ?>"><?php echo rts_marathon_challenge_avatar($participant, 58); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ($finisher_avatar_frame_url) : ?><img class="rts-mc-finisher__avatar-frame" src="<?php echo esc_url($finisher_avatar_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?><i class="rts-mc-finisher__rank<?php echo $finisher_rank_icon_url ? ' has-artwork' : ''; ?>"><?php if ($finisher_rank_icon_url) : ?><img src="<?php echo esc_url($finisher_rank_icon_url); ?>" alt="" aria-hidden="true"><?php endif; ?><b><?php echo esc_html($index + 1); ?></b></i></span><b><?php echo esc_html(rts_marathon_challenge_name($participant)); ?></b><strong><?php echo esc_html(rts_marathon_challenge_distance($participant->total_captain_miles_earned)); ?></strong></span><?php endforeach; ?></span>
                    </section>
                <?php endif; ?>

                <?php foreach ($milestones as $milestone) : ?>
                    <?php
                    $stored_milestone_distance = absint($milestone['miles']);
                    $milestone_distance = rts_marathon_challenge_milestone_distance($milestone);
                    $milestone_key = sanitize_key($milestone['key'] ?? '');
                    $milestone_label = rts_marathon_challenge_map_distance($milestone_distance);
                    $milestone_lap_groups = array();
                    foreach ($route_groups_by_marathon as $group_marathon => $marathon_distance_groups) {
                        $lap_members = $marathon_distance_groups[$stored_milestone_distance] ?? array();
                        if ($lap_members) {
                            $milestone_lap_groups[absint($group_marathon)] = $lap_members;
                        }
                    }
                    $occupied_milestone_marathons = array_keys($milestone_lap_groups);
                    if (!$milestone_lap_groups) {
                        $milestone_lap_groups = array(1 => array());
                    }
                    $is_before_half_marathon = '20k' === $milestone_key;
                    $is_half_marathon = '21k' === $milestone_key;
                    ?>
                    <?php foreach ($milestone_lap_groups as $milestone_marathon => $milestone_members) : ?>
                        <?php
                        $milestone_point = rts_marathon_challenge_lap_group_position($milestone_distance, $target, $milestone_marathon, $occupied_milestone_marathons);
                        $milestone_representative = $milestone_members ? $milestone_members[0] : null;
                        $milestone_is_marathon_two = 2 === absint($milestone_marathon);
                        $milestone_marker_image = $milestone_is_marathon_two ? $marathon2_position_marker_url : $position_marker_url;
                        $milestone_selected_marker_image = $position_marker_selected_url;
                        $milestone_popover_id = 'rts-mc-map-milestone-' . sanitize_html_class($milestone_key) . '-m' . absint($milestone_marathon);
                        $milestone_popup_title = (absint($milestone_marathon) > 1 ? 'M' . absint($milestone_marathon) . ' ' : '') . $milestone_label;
                        ?>
                        <span class="rts-mc-marker rts-mc-marker--milestone rts-mc-marker--lap-<?php echo esc_attr(absint($milestone_marathon)); ?> rts-mc-marker--milestone-<?php echo esc_attr(sanitize_html_class($milestone_key)); ?><?php echo $milestone_members ? ' has-members rts-mc-marker--route-user' : ' rts-mc-marker--milestone-empty'; ?><?php echo $milestone_members && $milestone_marker_image ? ' rts-mc-marker--artwork-pin' : ''; ?><?php echo $milestone_point[0] <= 14 ? ' is-popover-overlap' : ''; ?><?php echo $is_before_half_marathon ? ' is-before-half-marathon' : ''; ?><?php echo $is_half_marathon ? ' is-half-marathon' : ''; ?>" style="--rts-x:<?php echo esc_attr($milestone_point[0]); ?>%;--rts-y:<?php echo esc_attr($milestone_point[1]); ?>%" title="<?php echo esc_attr(sprintf(__('%s milestone', 'run-the-seas'), $milestone_popup_title)); ?>"<?php echo $milestone_members ? ' data-rts-mc-popover' : ''; ?>>
                            <?php if ($milestone_representative) : ?><button type="button" aria-describedby="<?php echo esc_attr($milestone_popover_id); ?>" aria-expanded="false"><?php endif; ?>
                            <span class="rts-mc-marker__distance<?php echo $milestone_marker_image ? ' has-artwork' : ''; ?><?php echo $milestone_marker_image && $milestone_selected_marker_image ? ' has-selected-artwork' : ''; ?>"><?php if ($milestone_marker_image) : ?><img class="rts-mc-position-pin rts-mc-position-pin--default" src="<?php echo esc_url($milestone_marker_image); ?>" alt="" aria-hidden="true"><?php endif; ?><?php if ($milestone_marker_image && $milestone_selected_marker_image) : ?><img class="rts-mc-position-pin rts-mc-position-pin--selected" src="<?php echo esc_url($milestone_selected_marker_image); ?>" alt="" aria-hidden="true"><?php endif; ?><b><?php echo esc_html($milestone_label); ?></b></span>
                            <?php if ($milestone_representative) : ?>
                                <span class="rts-mc-map-avatar<?php echo $milestone_is_marathon_two && $marathon2_avatar_frame_url ? ' has-artwork-frame' : ''; ?>"><?php echo rts_marathon_challenge_avatar($milestone_representative, 50); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ($milestone_is_marathon_two && $marathon2_avatar_frame_url) : ?><img class="rts-mc-map-avatar__frame" src="<?php echo esc_url($marathon2_avatar_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?><?php if (absint($milestone_marathon) > 1) : ?><span class="rts-mc-map-avatar__badge<?php echo $milestone_is_marathon_two && $marathon2_map_badge_url ? ' has-artwork' : ''; ?>"><?php if ($milestone_is_marathon_two && $marathon2_map_badge_url) : ?><img src="<?php echo esc_url($marathon2_map_badge_url); ?>" alt="M2"><?php else : ?>M<?php echo esc_html(absint($milestone_marathon)); ?><?php endif; ?></span><?php endif; ?></span>
                                <span class="rts-mc-marker__name"><?php echo esc_html(rts_marathon_challenge_name($milestone_representative)); ?></span>
                            </button>
                            <?php rts_marathon_challenge_popup($milestone_popover_id, $milestone_popup_title, $milestone_members, $stored_milestone_distance, false, $popup_frame_url, 'compact', $milestone_marker_image, $user_list_header_right_icon_url, $milestone_label); ?>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <?php for ($kilometre = 1; $kilometre <= 42; $kilometre++) : ?>
                    <?php $tick_point = rts_marathon_challenge_position($kilometre * 1000, $target); ?>
                    <span class="rts-mc-km-tick<?php echo 0 === $kilometre % 5 ? ' is-major' : ''; ?>" style="--rts-x:<?php echo esc_attr($tick_point[0]); ?>%;--rts-y:<?php echo esc_attr($tick_point[1]); ?>%" aria-hidden="true"></span>
                <?php endfor; ?>

                <?php
                $zero_positions = array(
                    array(28.0, 73.5),
                    array(34.0, 77.0),
                    array(40.0, 73.5),
                );
                ?>
                <?php foreach ($zero_top as $zero_index => $participant) : ?>
                    <?php
                    $point = $zero_positions[$zero_index];
                    $popover_id = 'rts-mc-zero-point-' . absint($zero_index);
                    $has_zero_popup = !empty($zero_remaining);
                    ?>
                    <span class="rts-mc-marker rts-mc-marker--zero-top" style="--rts-x:<?php echo esc_attr($point[0]); ?>%;--rts-y:<?php echo esc_attr($point[1]); ?>%"<?php echo $has_zero_popup ? ' data-rts-mc-popover' : ''; ?>>
                        <button type="button"<?php if ($has_zero_popup) : ?> aria-describedby="<?php echo esc_attr($popover_id); ?>" aria-expanded="false"<?php endif; ?>>
                            <span class="rts-mc-marker__distance<?php echo $position_marker_url ? ' has-artwork' : ''; ?><?php echo $position_marker_url && $position_marker_selected_url ? ' has-selected-artwork' : ''; ?>"><?php if ($position_marker_url) : ?><img class="rts-mc-position-pin rts-mc-position-pin--default" src="<?php echo esc_url($position_marker_url); ?>" alt="" aria-hidden="true"><?php endif; ?><?php if ($position_marker_url && $position_marker_selected_url) : ?><img class="rts-mc-position-pin rts-mc-position-pin--selected" src="<?php echo esc_url($position_marker_selected_url); ?>" alt="" aria-hidden="true"><?php endif; ?><b>0K</b></span>
                            <span class="rts-mc-map-avatar"><?php echo rts_marathon_challenge_avatar($participant, 50); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                            <span class="rts-mc-marker__name"><?php echo esc_html(rts_marathon_challenge_name($participant)); ?></span>
                        </button>
                        <?php if ($has_zero_popup) : ?><?php rts_marathon_challenge_popup($popover_id, '0K', $zero_remaining, 0, false, $popup_frame_url, 'compact', $position_marker_url, $user_list_header_right_icon_url, '0K'); ?><?php endif; ?>
                    </span>
                <?php endforeach; ?>

                <?php foreach ($map_groups as $map_group) : ?>
                    <?php
                    $distance = absint($map_group['distance']);
                    $members = $map_group['members'];
                    $group_marathon = max(1, absint($map_group['marathon']));
                    if (0 === absint($distance)) {
                        continue;
                    }
                    if (in_array(absint($distance), $milestone_distances, true)) {
                        continue;
                    }
                    $occupied_marathons = array();
                    foreach ($route_groups_by_marathon as $occupied_marathon => $marathon_distance_groups) {
                        if (!empty($marathon_distance_groups[$distance])) {
                            $occupied_marathons[] = absint($occupied_marathon);
                        }
                    }
                    $point = rts_marathon_challenge_lap_group_position($distance, $target, $group_marathon, $occupied_marathons);
                    $representative = $members[0];
                    $representative_marathon = $group_marathon;
                    $is_marathon_two = 2 === $representative_marathon;
                    $is_before_half_marathon = 20000 === absint($distance)
                        && (isset($groups[21000]) || isset($groups[21100]));
                    $is_half_marathon = in_array(absint($distance), array(21000, 21100), true)
                        && isset($groups[20000]);
                    $marker_image = $is_marathon_two
                        ? $marathon2_position_marker_url
                        : $position_marker_url;
                    $selected_marker_image = $position_marker_selected_url;
                    $popover_id = 'rts-mc-point-' . absint($distance) . '-m' . $representative_marathon;
                    $map_group_title = ($representative_marathon > 1 ? 'M' . $representative_marathon . ' ' : '') . rts_marathon_challenge_map_distance($distance);
                    ?>
                    <span class="rts-mc-marker rts-mc-marker--route-user<?php echo $marker_image ? ' rts-mc-marker--artwork-pin' : ''; ?> rts-mc-marker--lap-<?php echo esc_attr($representative_marathon); ?><?php echo $point[0] <= 14 ? ' is-popover-overlap' : ''; ?><?php echo $is_before_half_marathon ? ' is-before-half-marathon' : ''; ?><?php echo $is_half_marathon ? ' is-half-marathon' : ''; ?>" style="--rts-x:<?php echo esc_attr($point[0]); ?>%;--rts-y:<?php echo esc_attr($point[1]); ?>%" data-rts-mc-popover>
                        <button type="button" aria-describedby="<?php echo esc_attr($popover_id); ?>" aria-expanded="false">
                            <span class="rts-mc-marker__distance<?php echo $marker_image ? ' has-artwork' : ''; ?><?php echo $marker_image && $selected_marker_image ? ' has-selected-artwork' : ''; ?>"><?php if ($marker_image) : ?><img class="rts-mc-position-pin rts-mc-position-pin--default" src="<?php echo esc_url($marker_image); ?>" alt="" aria-hidden="true"><?php endif; ?><?php if ($marker_image && $selected_marker_image) : ?><img class="rts-mc-position-pin rts-mc-position-pin--selected" src="<?php echo esc_url($selected_marker_image); ?>" alt="" aria-hidden="true"><?php endif; ?><b><?php echo esc_html(rts_marathon_challenge_map_distance($distance)); ?></b></span>
                            <span class="rts-mc-map-avatar<?php echo $is_marathon_two && $marathon2_avatar_frame_url ? ' has-artwork-frame' : ''; ?>"><?php echo rts_marathon_challenge_avatar($representative, 50); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ($is_marathon_two && $marathon2_avatar_frame_url) : ?><img class="rts-mc-map-avatar__frame" src="<?php echo esc_url($marathon2_avatar_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?><?php if ($representative_marathon > 1) : ?><span class="rts-mc-map-avatar__badge<?php echo $is_marathon_two && $marathon2_map_badge_url ? ' has-artwork' : ''; ?>"><?php if ($is_marathon_two && $marathon2_map_badge_url) : ?><img src="<?php echo esc_url($marathon2_map_badge_url); ?>" alt="M2"><?php else : ?>M<?php echo esc_html($representative_marathon); ?><?php endif; ?></span><?php endif; ?></span>
                            <span class="rts-mc-marker__name"><?php echo esc_html(rts_marathon_challenge_name($representative)); ?></span>
                        </button>
                        <?php rts_marathon_challenge_popup($popover_id, $map_group_title, $members, $distance, false, $popup_frame_url, 'compact', $marker_image, $user_list_header_right_icon_url, rts_marathon_challenge_map_distance($distance)); ?>
                    </span>
                <?php endforeach; ?>

                <?php
                $you_distance = $is_logged_in ? absint($current_lap['distance']) : 0;
                $is_start_state = !$is_logged_in || 0 === $you_distance;
                if ($is_start_state) {
                    // Keep both guest and signed-in 0K callouts in the clear water
                    // beside Start instead of placing either inside the 0K pack.
                    $you_point = array(35.5, 55.0);
                } else {
                    $you_point = rts_marathon_challenge_current_position($you_distance, $target);
                }
                $show_you_popover = !$is_logged_in || $you_distance > 0;
                $you_marathon = $is_logged_in ? max(1, absint($current_lap['marathon'] ?? 1)) : 1;
                $you_members = $is_logged_in && $you_distance > 0
                    ? ($groups_by_marathon[$you_marathon][$you_distance] ?? array())
                    : array();
                ?>
                <span class="rts-mc-marker rts-mc-marker--you<?php echo $is_start_state ? ' rts-mc-marker--start' : ''; ?><?php echo !$is_logged_in ? ' rts-mc-marker--guest' : ''; ?><?php echo $you_point[0] <= 25 ? ' is-popover-overlap' : ''; ?>" style="--rts-x:<?php echo esc_attr($you_point[0]); ?>%;--rts-y:<?php echo esc_attr($you_point[1]); ?>%"<?php echo $show_you_popover ? ' data-rts-mc-popover' : ''; ?>>
                    <button type="button"<?php if ($show_you_popover) : ?> aria-describedby="rts-mc-you-point" aria-expanded="false"<?php endif; ?>>
                        <?php if ($is_logged_in) : ?><span class="rts-mc-current-avatar<?php echo $current_user_frame_url ? ' has-artwork-frame' : ''; ?>"><?php echo rts_marathon_challenge_avatar($current, 72); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ($current_user_frame_url) : ?><img class="rts-mc-current-avatar__frame" src="<?php echo esc_url($current_user_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?></span><?php else : ?><span class="rts-mc-guest-avatar"><?php echo rts_marathon_challenge_dummy_avatar(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ($asset('guest_avatar_frame_image')) : ?><img class="rts-mc-guest-avatar__frame" src="<?php echo esc_url($asset('guest_avatar_frame_image')); ?>" alt="" aria-hidden="true"><?php endif; ?></span><?php endif; ?>
                        <?php if ($is_logged_in && $current_lap['marathon'] > 1) : ?><span class="rts-mc-marker__badge<?php echo 2 === absint($current_lap['marathon']) && $marathon2_map_badge_url ? ' has-artwork' : ''; ?>"><?php if (2 === absint($current_lap['marathon']) && $marathon2_map_badge_url) : ?><img src="<?php echo esc_url($marathon2_map_badge_url); ?>" alt="M2"><?php else : ?>M<?php echo esc_html($current_lap['marathon']); ?><?php endif; ?></span><?php endif; ?>
                        <span class="rts-mc-marker__you"><?php if ($is_logged_in) : ?><?php esc_html_e('You', 'run-the-seas'); ?><strong><?php echo esc_html(rts_marathon_challenge_distance($you_distance)); ?></strong><?php else : ?><?php esc_html_e('You Are Here,', 'run-the-seas'); ?><small><?php esc_html_e('Start Your Race', 'run-the-seas'); ?></small><?php endif; ?></span>
                    </button>
                    <?php if ($show_you_popover && $you_members) : ?><?php rts_marathon_challenge_popup('rts-mc-you-point', sprintf(__('%s point', 'run-the-seas'), rts_marathon_challenge_distance($you_distance)), $you_members, $you_distance, false, $popup_frame_url, 'compact', $position_marker_url, $user_list_header_right_icon_url, rts_marathon_challenge_distance($you_distance)); ?><?php elseif ($show_you_popover && !$is_logged_in) : ?><span class="rts-mc-popover<?php echo $popup_frame_url ? ' has-artwork-frame' : ''; ?>" id="rts-mc-you-point" role="tooltip"<?php if ($popup_frame_url) : ?> style="--rts-mc-popup-frame:url(&quot;<?php echo esc_url($popup_frame_url); ?>&quot;);"<?php endif; ?>><strong class="rts-mc-popover__title"><?php esc_html_e('You are at the starting line', 'run-the-seas'); ?></strong><span class="rts-mc-popover__more"><?php esc_html_e('Sign in and share your referral link to begin.', 'run-the-seas'); ?></span></span><?php endif; ?>
                </span>
                </div>
                <div class="rts-mc-map-controls" aria-label="<?php esc_attr_e('Map zoom controls', 'run-the-seas'); ?>">
                    <button type="button" data-rts-mc-zoom-in aria-label="<?php esc_attr_e('Zoom in', 'run-the-seas'); ?>">+</button>
                    <button type="button" data-rts-mc-zoom-out aria-label="<?php esc_attr_e('Zoom out', 'run-the-seas'); ?>">−</button>
                    <button type="button" data-rts-mc-zoom-reset aria-label="<?php esc_attr_e('Reset map view', 'run-the-seas'); ?>">⌂</button>
                </div>
            </main>

            <aside class="rts-mc-sidebar rts-mc-sidebar--right">
                <section class="rts-mc-panel rts-mc-milestones<?php echo $asset('milestones_frame_image') ? ' has-artwork-frame' : ''; ?><?php echo $milestones_divider_url ? ' has-heading-divider' : ''; ?>">
                    <?php if ($asset('milestones_frame_image')) : ?><img class="rts-mc-panel__frame" src="<?php echo esc_url($asset('milestones_frame_image')); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <h2><?php esc_html_e('Milestone Groups', 'run-the-seas'); ?></h2>
                    <?php if ($milestones_divider_url) : ?><img class="rts-mc-heading-divider" src="<?php echo esc_url($milestones_divider_url); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <div class="rts-mc-panel__body">
                        <?php foreach ($milestones as $milestone) : ?>
                            <?php
                            $distance = absint($milestone['miles']);
                            $members = $milestone_groups[$distance];
                            $representative = $members ? $members[0] : null;
                            $popover_id = 'rts-mc-milestone-' . sanitize_html_class($milestone['key']);
                            $custom_trophy = rts_marathon_challenge_trophy_url($milestone, $design_assets);
                            ?>
                            <span class="rts-mc-milestone<?php echo $members ? ' has-members' : ' is-empty'; ?><?php echo $milestone_active_frame_url ? ' has-row-frame' : ''; ?>" data-rts-mc-popover>
                                <button type="button" aria-describedby="<?php echo esc_attr($popover_id); ?>" aria-expanded="false">
                                    <?php if ($milestone_active_frame_url) : ?><img class="rts-mc-milestone__row-frame" src="<?php echo esc_url($milestone_active_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?>
                                    <?php if ($custom_trophy) : ?><span class="rts-trophy-icon rts-mc-milestone__icon"><img src="<?php echo esc_url($custom_trophy); ?>" alt="" aria-hidden="true"></span><?php else : ?><?php echo rts_render_trophy_milestone_icon($milestone, 'rts-mc-milestone__icon'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php endif; ?>
                                    <strong><?php echo esc_html(rts_marathon_challenge_distance(rts_marathon_challenge_milestone_distance($milestone))); ?></strong>
                                    <?php if ($representative) : ?><?php echo rts_marathon_challenge_avatar($representative, 42); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html(rts_marathon_challenge_name($representative)); ?></span><?php else : ?><span class="rts-mc-milestone__empty-label"><?php esc_html_e('Be the first', 'run-the-seas'); ?></span><?php endif; ?>
                                    <i class="rts-mc-list-toggle<?php echo $asset('list_open_icon_image') || $asset('list_close_icon_image') ? ' has-artwork' : ''; ?>" aria-hidden="true"><span class="rts-mc-list-toggle__open"><?php if ($asset('list_open_icon_image')) : ?><img src="<?php echo esc_url($asset('list_open_icon_image')); ?>" alt=""><?php else : ?>⌄<?php endif; ?></span><span class="rts-mc-list-toggle__close"><?php if ($asset('list_close_icon_image')) : ?><img src="<?php echo esc_url($asset('list_close_icon_image')); ?>" alt=""><?php else : ?>⌃<?php endif; ?></span></i>
                                </button>
                                <?php if ($members) : ?><?php rts_marathon_challenge_popup($popover_id, rts_marathon_challenge_distance(rts_marathon_challenge_milestone_distance($milestone)), $members, $distance, false, $popup_frame_url, 'group', $custom_trophy, $milestone_group_header_right_icon_url); ?><?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="rts-mc-panel rts-mc-over<?php echo $asset('over_target_frame_image') ? ' has-artwork-frame' : ''; ?><?php echo $over_target_divider_url ? ' has-heading-divider' : ''; ?>">
                    <?php if ($asset('over_target_frame_image')) : ?><img class="rts-mc-panel__frame" src="<?php echo esc_url($asset('over_target_frame_image')); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <h2><?php echo esc_html(sprintf(__('Over %s', 'run-the-seas'), rts_marathon_challenge_distance($target))); ?></h2>
                    <?php if ($over_target_divider_url) : ?><img class="rts-mc-heading-divider" src="<?php echo esc_url($over_target_divider_url); ?>" alt="" aria-hidden="true"><?php endif; ?>
                    <div class="rts-mc-panel__body">
                        <?php if ($over_target) : ?>
                            <span class="rts-mc-milestone has-members<?php echo $milestone_active_frame_url ? ' has-row-frame' : ''; ?>" data-rts-mc-popover>
                                <button type="button" aria-describedby="rts-mc-over-list" aria-expanded="false"><?php if ($milestone_active_frame_url) : ?><img class="rts-mc-milestone__row-frame" src="<?php echo esc_url($milestone_active_frame_url); ?>" alt="" aria-hidden="true"><?php endif; ?><span class="rts-mc-over__icon"><?php if ($asset('trophy_over_image')) : ?><img src="<?php echo esc_url($asset('trophy_over_image')); ?>" alt="" aria-hidden="true"><?php else : ?>🏆<?php endif; ?></span><strong><?php echo esc_html(rts_marathon_challenge_distance($over_target[0]->total_captain_miles_earned)); ?></strong><?php echo rts_marathon_challenge_avatar($over_target[0], 42); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html(rts_marathon_challenge_name($over_target[0])); ?></span><i class="rts-mc-list-toggle<?php echo $asset('list_open_icon_image') || $asset('list_close_icon_image') ? ' has-artwork' : ''; ?>" aria-hidden="true"><span class="rts-mc-list-toggle__open"><?php if ($asset('list_open_icon_image')) : ?><img src="<?php echo esc_url($asset('list_open_icon_image')); ?>" alt=""><?php else : ?>⌄<?php endif; ?></span><span class="rts-mc-list-toggle__close"><?php if ($asset('list_close_icon_image')) : ?><img src="<?php echo esc_url($asset('list_close_icon_image')); ?>" alt=""><?php else : ?>⌃<?php endif; ?></span></i></button>
                                <?php rts_marathon_challenge_popup('rts-mc-over-list', sprintf(__('Over %s', 'run-the-seas'), rts_marathon_challenge_distance($target)), $over_target, $target, true, $popup_frame_url); ?>
                            </span>
                        <?php else : ?><em class="rts-mc-empty"><?php esc_html_e('The next voyage awaits its first captain.', 'run-the-seas'); ?></em><?php endif; ?>
                    </div>
                </section>
            </aside>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('rts_marathon_challenge', 'rts_marathon_challenge_shortcode');

/** Create the new challenge page without replacing or editing the old page. */
function rts_ensure_marathon_challenge_page()
{
    if (wp_installing()) {
        return;
    }

    $existing = get_page_by_path('marathon-challenge');
    if ($existing) {
        if (
            defined('ELEMENTOR_VERSION')
            && '[rts_marathon_challenge]' === trim((string) $existing->post_content)
            && 'elementor_canvas' !== get_post_meta($existing->ID, '_wp_page_template', true)
        ) {
            update_post_meta($existing->ID, '_wp_page_template', 'elementor_canvas');
        }
        return;
    }

    // $page_id = wp_insert_post(array(
    //     'post_title'   => __('Marathon Challenge', 'run-the-seas'),
    //     'post_name'    => 'marathon-challenge',
    //     'post_content' => '[rts_marathon_challenge]',
    //     'post_status'  => 'publish',
    //     'post_type'    => 'page',
    // ));

    // if ($page_id && !is_wp_error($page_id)) {
    //     update_option('rts_marathon_challenge_page_id', absint($page_id));
    //     if (defined('ELEMENTOR_VERSION')) {
    //         update_post_meta($page_id, '_wp_page_template', 'elementor_canvas');
    //     }
    // }
}
add_action('init', 'rts_ensure_marathon_challenge_page', 30);
