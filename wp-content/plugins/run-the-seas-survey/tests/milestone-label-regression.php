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
