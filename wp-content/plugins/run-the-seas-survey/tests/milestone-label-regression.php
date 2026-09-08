<?php
/**
 * Offline checks for fixed race names versus descriptive kilometre values.
 * Run: php tests/milestone-label-regression.php
 * No WordPress bootstrap, database connection, network calls, or emails.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('ABSPATH', dirname(__DIR__) . '/');
define('RTS_PLUGIN_URL', 'https://example.test/survey/');
function add_action(...$args) {}
function remove_action(...$args) {}
function add_filter(...$args) {}
function add_shortcode(...$args) {}
function apply_filters($hook, $value, ...$args) { return $value; }
function get_option($key, $default = false) { return $default; }
function absint($value) { return abs((int) $value); }
function sanitize_key($value) { return preg_replace('/[^a-z0-9_\-]/', '', strtolower($value)); }
function __($value, $domain = '') { return $value; }
function esc_html($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function esc_attr($value) { return esc_html($value); }
function esc_html__($value, $domain = '') { return esc_html($value); }
function wp_strip_all_tags($value) { return strip_tags((string) $value); }
function shortcode_atts($defaults, $atts, $shortcode = '') { return array_merge($defaults, $atts); }
// Deliberately use comma decimals: fixed product names must not be localised.
function number_format_i18n($value, $decimals = 0) { return number_format($value, $decimals, ',', '.'); }
function current_time($format) { return '12:00'; }
function is_user_logged_in() { return true; }
function wp_get_current_user() { return (object) array('ID' => 123); }
function rts_normalize_marathon_target($target) { return 42200 === absint($target) ? 42000 : max(1, absint($target)); }
function rts_init_trophy_system() { return $GLOBALS['rts_test_trophies']; }

$rts_test_participant = (object) array('total_captain_miles_earned' => 0, 'captain_miles_balance' => 0);
$wpdb = new class {
    public $prefix = 'test_';
    public function prepare($query, ...$args) { return $query; }
    public function get_row($query) {
        if (strpos($query, 'SELECT * FROM test_rts_participants WHERE user_id') !== 0) {
            throw new RuntimeException('Unexpected query: ' . $query);
        }
        return $GLOBALS['rts_test_participant'];
    }
    public function __call($name, $args) { throw new RuntimeException('Unexpected database operation: ' . $name); }
};

foreach (array('rts-auth-registration', 'class-rts-registration', 'class-rts-trophy',
    'rts-member-shortcodes', 'rts-leaderboard-shortcodes', 'rts-marathon-challenge') as $file) {
    require dirname(__DIR__) . '/includes/' . $file . '.php';
}
$rts_test_trophies = new RTS_Trophy();
$definitions = $rts_test_trophies->get_all_trophy_definitions();
$checks = 0;
function rts_label_check($actual, $expected, $message) {
    if ($actual !== $expected) {
        throw new RuntimeException($message . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
    $GLOBALS['checks']++;
}

$races = array(
    '5k' => array(5000, '5K'), '10k' => array(10000, '10K'),
    '15k' => array(15000, '15K'), '20k' => array(20000, '20K'),
    '21k' => array(21000, '21.1K'), '25k' => array(25000, '25K'),
    '30k' => array(30000, '30K'), '35k' => array(35000, '35K'),
    '42k' => array(42000, '42.2K'),
);
foreach ($races as $key => [$threshold, $label]) {
    foreach (array('' => 0, 'm2-' => 42000) as $prefix => $offset) {
        $trophy_key = $prefix . $key;
        $name = ($prefix ? 'MARATHON 2 — ' : '') . $label . ' TROPHY';
        rts_label_check($definitions[$trophy_key]['name'], $name, $trophy_key . ' product name');
        rts_label_check($definitions[$trophy_key]['miles_required'], $offset + $threshold, $trophy_key . ' unchanged unlock threshold');
        rts_label_check(rts_format_trophy_miles($offset + $threshold, $trophy_key), $label, $trophy_key . ' trophy label');
        $rts_test_participant->total_captain_miles_earned = $offset + $threshold;
        rts_label_check(rts_member_current_trophy_shortcode(array('field' => 'distance')), $label, $trophy_key . ' current-trophy label');
        rts_label_check(rts_member_current_trophy_shortcode(array('field' => 'name')), $name, $trophy_key . ' current-trophy name');
    }
    rts_label_check(rts_marathon_challenge_distance($threshold), $label, $key . ' marathon label');
    rts_label_check(rts_marathon_challenge_map_distance($threshold), $label, $key . ' map label');
    $header = rts_leaderboard_header_shortcode(array('target' => $threshold));
    rts_label_check(strpos($header, '<strong>' . $label . '</strong>') !== false, true, $key . ' leaderboard title');
    rts_label_check(strpos($header, 'by 1 km toward') !== false, true, 'header descriptive progress');
}
rts_label_check(rts_marathon_challenge_distance(21100), '21.1K', 'legacy half-marathon label');
rts_label_check(rts_marathon_challenge_distance(42200), '42.2K', 'legacy marathon label');
foreach (array(0, 0.0, '0.0') as $zero) {
    rts_label_check(rts_format_trophy_miles($zero), '0K', 'zero trophy label has no decimal');
    rts_label_check(rts_marathon_challenge_distance($zero), '0K', 'zero marathon label has no decimal');
    rts_label_check(rts_marathon_challenge_map_distance($zero), '0K', 'zero map label has no decimal');
    rts_label_check(rts_format_miles($zero), '0 km', 'zero descriptive progress has no decimal');
}
$rts_test_participant->total_captain_miles_earned = 0;
rts_label_check(
    strip_tags(rts_member_distance_shortcode(array('target' => 42200, 'format' => 'progress'))),
    '0K of 42.2K',
    'compact marathon status uses matching K units'
);
$track_spots = rts_marathon_challenge_track_spots(
    array(5000, 10000, 15000, 20000, 21000, 25000, 30000, 35000, 42000),
    42000,
    20
);
rts_label_check(count($track_spots), 20, 'map is capped at twenty canonical track spots');
rts_label_check($track_spots[0], 0, 'track spots retain the start');
rts_label_check(end($track_spots), 42000, 'track spots retain the finish');
rts_label_check(rts_marathon_challenge_track_spots(range(1000, 41000, 1000), 42000, 2), array(0, 42000), 'minimum spot limit retains start and finish');
foreach (array(5000, 10000, 15000, 20000, 21000, 25000, 30000, 35000, 42000) as $milestone_distance) {
    rts_label_check(in_array($milestone_distance, $track_spots, true), true, $milestone_distance . ' trophy reserves a track spot');
}

$gap_runner = (object) array('id' => 1, 'lap' => array('distance' => 1000), 'progress_completed_at' => 400);
$exact_runner = (object) array('id' => 2, 'lap' => array('distance' => 5000), 'progress_completed_at' => 200);
$next_runner = (object) array('id' => 3, 'lap' => array('distance' => 6000), 'progress_completed_at' => 300);
$marathon_two_runner = (object) array('id' => 4, 'lap' => array('distance' => 4000), 'progress_completed_at' => 100);
$bucketed = rts_marathon_challenge_bucket_route_groups(
    array(
        1 => array(1000 => array($gap_runner), 5000 => array($exact_runner), 6000 => array($next_runner)),
        2 => array(4000 => array($marathon_two_runner)),
    ),
    array(0, 5000, 10000)
);
rts_label_check(array_map(static function ($participant) { return $participant->id; }, $bucketed[1][5000]), array(1, 2), 'gap runner rolls into the next spot list');
rts_label_check($bucketed[1][10000][0]->id, 3, 'runner after a spot rolls forward');
rts_label_check($bucketed[2][5000][0]->id, 4, 'Marathon 2 keeps its own spot list');
rts_marathon_challenge_sort_bucket_members($bucketed[1][5000], 5000);
rts_label_check(array_map(static function ($participant) { return $participant->id; }, $bucketed[1][5000]), array(2, 1), 'exact runner precedes a newer runner rolled forward from a gap');
$merged_spots = rts_marathon_challenge_merge_spot_marathons($bucketed);
rts_label_check(count($merged_spots), 2, 'coincident marathon groups produce one physical marker per spot');
rts_label_check($merged_spots[5000]['marathon'], 2, 'highest marathon supplies the shared marker');
rts_label_check(array_map(static function ($participant) { return $participant->id; }, $merged_spots[5000]['members']), array(4, 2, 1), 'Marathon 2 runners precede Marathon 1 runners in the shared list');
$earlier_finisher_with_recent_activity = (object) array(
    'id' => 5,
    'progress_completed_at' => 900,
    'milestone_completed_at' => array(42000 => 100),
    'milestone_completed_order' => array(42000 => 1),
);
$recent_finisher = (object) array(
    'id' => 6,
    'progress_completed_at' => 500,
    'milestone_completed_at' => array(42000 => 200),
    'milestone_completed_order' => array(42000 => 2),
);
$over_finishers = array($earlier_finisher_with_recent_activity, $recent_finisher);
rts_marathon_challenge_sort_recent($over_finishers, 42000);
rts_label_check(array_map(static function ($participant) { return $participant->id; }, $over_finishers), array(6, 5), 'over-target card uses the most recent finish crossing rather than later activity');
$around_participants = array();
for ($around_index = 0; $around_index <= 10; $around_index++) {
    $around_participants[] = (object) array(
        'id' => 100 + $around_index,
        'total_captain_miles_earned' => $around_index * 1000,
        'is_current' => 5 === $around_index,
    );
}
$middle_window = rts_marathon_challenge_around_window($around_participants, 105, 4);
rts_label_check(count($middle_window), 9, 'around-you window contains current user plus four on either side');
rts_label_check(array_search(105, array_column($middle_window, 'id'), true), 4, 'current user is centred when four users exist on either side');
$around_participants[5]->is_current = false;
$around_participants[10]->is_current = true;
$leader_window = rts_marathon_challenge_around_window($around_participants, 110, 4);
rts_label_check(count($leader_window), 5, 'around-you leader still sees four participants behind');
rts_label_check(array_column($leader_window, 'id'), array(110, 109, 108, 107, 106), 'around-you leader is followed by the nearest four participants');
$first_marathon_finisher = (object) array(
    'id' => 201,
    'lap' => array('distance' => 42000, 'marathon' => 1),
    'progress_completed_at' => 100,
    'milestone_completed_at' => array(42000 => 100),
);
$past_finisher = (object) array(
    'id' => 202,
    'lap' => array('distance' => 25000, 'marathon' => 2),
    'progress_completed_at' => 300,
);
$second_marathon_finisher = (object) array(
    'id' => 203,
    'lap' => array('distance' => 42000, 'marathon' => 2),
    'progress_completed_at' => 200,
    'milestone_completed_at' => array(42000 => 200),
);
$current_finish_members = rts_marathon_challenge_current_milestone_members(
    array($first_marathon_finisher, $past_finisher, $second_marathon_finisher),
    42000
);
rts_label_check(array_column($current_finish_members, 'id'), array(203, 201), 'milestone list contains only users currently at that lap milestone');
$five_k_band_members = rts_marathon_challenge_current_milestone_members(
    array(
        (object) array('id' => 204, 'lap' => array('distance' => 4000), 'milestone_completed_at' => array()),
        (object) array('id' => 205, 'lap' => array('distance' => 5000), 'progress_completed_at' => 300, 'milestone_completed_at' => array(5000 => 100)),
        (object) array('id' => 206, 'lap' => array('distance' => 6000), 'progress_completed_at' => 100, 'milestone_completed_at' => array(5000 => 200)),
        (object) array('id' => 207, 'lap' => array('distance' => 9000), 'progress_completed_at' => 200, 'milestone_completed_at' => array(5000 => 150)),
        (object) array('id' => 208, 'lap' => array('distance' => 10000), 'milestone_completed_at' => array(5000 => 250)),
    ),
    5000,
    10000
);
rts_label_check(array_column($five_k_band_members, 'id'), array(206, 207, 205), 'milestone band is ordered by milestone achievement time');
rts_label_check(rts_format_miles(14000), '14 km', 'descriptive progress units');
rts_label_check(rts_format_miles(21000), '21 km', 'actual progress is not the 21.1K product label');
rts_label_check(rts_format_miles(42000), '42 km', 'actual progress is not the 42.2K product label');
rts_label_check(rts_format_miles(21100), '21,1 km', 'descriptive progress retains localisation');

$expected_track = array('5K', '10K', '15K', '21.1K', '25K', '30K', '35K', '42.2K');
// Existing compact track omits 20K for spacing; do not change its layout.
foreach (array(0, 42000, 84000) as $progress) {
    rts_label_check(array_column(rts_leaderboard_track_milestones(42000, $progress), 'label'), $expected_track, 'track labels have exactly one K');
}
$cards = rts_trophy_milestones_shortcode();
foreach ($races as [$threshold, $label]) {
    rts_label_check(strpos($cards, '<strong>' . $label . '</strong>') !== false, true, $label . ' rendered milestone card');
}
$rts_test_participant->total_captain_miles_earned = 14000;
rts_label_check(rts_member_next_trophy_shortcode(array('field' => 'remaining_label')), '1 km to go to earn 15K TROPHY', 'next-trophy sentence separates units from name');

// Guard direct template labels, which do not call a formatter.
$journey = file_get_contents(dirname(__DIR__) . '/includes/rts-journey-shortcode.php');
rts_label_check(strpos($journey, 'esc_html($milestone); ?>K</b>') !== false, true, 'Journey milestone label');
rts_label_check(strpos($journey, 'esc_html($next_trophy); ?>K</strong>') !== false, true, 'Journey next-trophy label');
rts_label_check(strpos($journey, ' km OF 42.2 km</strong>') !== false, true, 'Journey measured progress unchanged');
$registration = file_get_contents(dirname(__DIR__) . '/includes/class-rts-registration.php');
rts_label_check(strpos($registration, "rts_format_trophy_miles(\$milestone) . ' milestone!'") !== false, true, 'achievement name uses K');
rts_label_check(strpos($registration, "rts_format_trophy_miles(\$milestone) . ' Milestone Medal'") !== false, true, 'medal name uses K');
foreach (glob(dirname(__DIR__) . '/includes/*.php') as $file) {
    rts_label_check(strpos(file_get_contents($file), '42.2 km Referral Marathon Challenge'), false, basename($file) . ' fixed challenge name');
}
echo 'PASS: ' . $checks . " fixed-name and descriptive-distance checks. No database writes or emails.\n";
