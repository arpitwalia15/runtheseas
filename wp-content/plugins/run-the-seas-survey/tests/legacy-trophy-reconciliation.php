<?php
/**
 * Offline regression checks for legacy trophy reconciliation.
 * Run: php tests/legacy-trophy-reconciliation.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('ABSPATH', dirname(__DIR__) . '/');
define('RTS_PLUGIN_URL', 'https://example.test/survey/');
function add_action(...$args) {}
function add_filter(...$args) {}
function add_shortcode(...$args) {}
function apply_filters($hook, $value, ...$args) { return $value; }
function get_option($key, $default = false) { return $default; }
function absint($value) { return abs((int) $value); }
function sanitize_key($value) { return preg_replace('/[^a-z0-9_\-]/', '', strtolower((string) $value)); }
function sanitize_email($value) { return strtolower(trim((string) $value)); }
function esc_url_raw($value) { return (string) $value; }
function current_time($format) { return '2026-09-11 12:00:00'; }
function get_user_meta(...$args) { return array(); }
function update_user_meta(...$args) { return true; }
function delete_user_meta(...$args) { return true; }

class RTS_Registration {
    public function get_participant($participant_id) {
        $participant = $GLOBALS['rts_legacy_test_db']->participant;
        return (int) $participant->id === (int) $participant_id ? $participant : null;
    }
    public function add_achievement(...$args) { return true; }
    public function log_timeline(...$args) { return true; }
}

class RTS_Legacy_Trophy_Test_DB {
    public $prefix = 'test_';
    public $participant;
    public $trophies = array();
    public $referrals = array();
    public $insert_id = 0;
    public $consent_cutoff = '2026-06-01 00:00:00';

    public function prepare($query, ...$args) {
        foreach ($args as $arg) {
            $replacement = is_numeric($arg) ? (string) $arg : "'" . str_replace("'", "''", (string) $arg) . "'";
            $query = preg_replace('/%[dfs]/', $replacement, $query, 1);
        }
        return $query;
    }

    public function get_var($query) {
        if (strpos($query, 'SELECT MIN(registration_date)') !== false) {
            return $this->consent_cutoff;
        }
        if (strpos($query, 'rts_survey_tracking') !== false) {
            return 1;
        }
        if (strpos($query, 'COUNT(DISTINCT participant_id)') !== false) {
            preg_match("/trophy_key = '([^']+)'/", $query, $match);
            return count(array_filter($this->trophies, static function ($record) use ($match) {
                return $record->trophy_key === ($match[1] ?? '');
            }));
        }
        if (strpos($query, 'rts_user_trophies') !== false) {
            preg_match('/participant_id = (\d+)/', $query, $participant_match);
            preg_match("/trophy_key = '([^']+)'/", $query, $key_match);
            foreach ($this->trophies as $record) {
                if ((int) $record->participant_id !== (int) ($participant_match[1] ?? 0)) {
                    continue;
                }
                if (!empty($key_match[1]) && $record->trophy_key !== $key_match[1]) {
                    continue;
                }
                return $record->id;
            }
        }
        return null;
    }

    public function get_row($query) {
        preg_match('/participant_id = (\d+)/', $query, $participant_match);
        preg_match("/trophy_key = '([^']+)'/", $query, $key_match);
        foreach ($this->trophies as $record) {
            if (
                (int) $record->participant_id === (int) ($participant_match[1] ?? 0)
                && $record->trophy_key === ($key_match[1] ?? '')
            ) {
                return $record;
            }
        }
        return null;
    }

    public function get_results($query) {
        if (strpos($query, 'rts_referrals') !== false) {
            return $this->referrals;
        }
        if (strpos($query, 'rts_user_trophies') !== false) {
            preg_match('/participant_id = (\d+)/', $query, $match);
            return array_values(array_filter($this->trophies, static function ($record) use ($match) {
                return (int) $record->participant_id === (int) ($match[1] ?? 0);
            }));
        }
        return array();
    }

    public function insert($table, $data) {
        if (strpos($table, 'rts_user_trophies') === false) {
            return 1;
        }
        $data['id'] = ++$this->insert_id;
        $this->trophies[$data['id']] = (object) $data;
        return 1;
    }

    public function update($table, $data, $where, ...$args) {
        foreach ($this->trophies as $id => $record) {
            $matches = isset($where['id'])
                ? (int) $record->id === (int) $where['id']
                : (!isset($where['trophy_key']) || $record->trophy_key === $where['trophy_key']);
            if ($matches) {
                foreach ($data as $key => $value) {
                    $this->trophies[$id]->{$key} = $value;
                }
            }
        }
        return 1;
    }
}

$wpdb = new RTS_Legacy_Trophy_Test_DB();
$GLOBALS['rts_legacy_test_db'] = $wpdb;
$wpdb->participant = (object) array(
    'id' => 15,
    'user_id' => 115,
    'email' => 'legacy@example.test',
    'registration_date' => '2026-01-01 09:00:00',
    'email_verified' => 1,
    'email_verification_date' => '2026-01-02 09:00:00',
    'age_consent_confirmed_at' => null,
    'survey_tracking_id' => 55,
    'total_captain_miles_earned' => 5000,
);
foreach (array('2026-01-03', '2026-01-04', '2026-01-05', '2026-01-06', '2026-01-08') as $date) {
    $wpdb->referrals[] = (object) array(
        'bonus_earned' => 1000,
        'completed_date' => $date . ' 10:00:00',
    );
}

require dirname(__DIR__) . '/includes/class-rts-trophy.php';
$trophies = new RTS_Trophy();
$added = $trophies->reconcile_participant_trophies(15, false);

function rts_legacy_check($condition, $message) {
    if (!$condition) {
        throw new RuntimeException('FAIL: ' . $message);
    }
    echo 'PASS: ' . $message . PHP_EOL;
}

rts_legacy_check(2 === $added, 'legacy participant receives both implied trophies');
rts_legacy_check(isset($wpdb->trophies[1]) && 'founding-runner' === $wpdb->trophies[1]->trophy_key, 'Founding Runner row is stored');
rts_legacy_check(isset($wpdb->trophies[2]) && '5k' === $wpdb->trophies[2]->trophy_key, '5K row is stored');
rts_legacy_check('2026-01-02 09:00:00' === $wpdb->trophies[1]->earned_date, 'Founding Runner uses verified journey start');
rts_legacy_check('2026-01-08 10:00:00' === $wpdb->trophies[2]->earned_date, '5K uses fifth completed referral date');
rts_legacy_check(6 === $wpdb->trophies[2]->split_days && 6 === $wpdb->trophies[2]->total_days, '5K day statistics are persisted from historical dates');
rts_legacy_check(null === $wpdb->participant->age_consent_confirmed_at, 'legacy eligibility does not fabricate consent');

$wpdb->participant = (object) array(
    'id' => 16,
    'user_id' => 116,
    'email' => 'invalid-current@example.test',
    'registration_date' => '2026-07-01 09:00:00',
    'email_verified' => 1,
    'email_verification_date' => '2026-07-02 09:00:00',
    'age_consent_confirmed_at' => null,
    'survey_tracking_id' => 56,
    'total_captain_miles_earned' => 5000,
);
rts_legacy_check(0 === $trophies->reconcile_participant_trophies(16, false), 'post-cutoff record without consent remains ineligible');
