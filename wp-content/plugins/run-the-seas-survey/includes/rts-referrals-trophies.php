<?php

if (!defined('ABSPATH')) {
    exit;
}

function rts_add_referral_columns()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'rts_participants';

    $columns_to_add = array(
        'referred_by' => "ALTER TABLE $table_name ADD COLUMN referred_by bigint(20) DEFAULT NULL",
        'referral_completed' => "ALTER TABLE $table_name ADD COLUMN referral_completed tinyint(1) DEFAULT 0",
        'referral_completed_date' => "ALTER TABLE $table_name ADD COLUMN referral_completed_date datetime DEFAULT NULL"
    );

    foreach ($columns_to_add as $column => $sql) {
        $column_exists = $wpdb->get_var(
            "SHOW COLUMNS FROM $table_name LIKE '$column'"
        );
        if (!$column_exists) {
            $wpdb->query($sql);
            error_log("RTS: Added column $column to $table_name");
        }
    }
}
add_action('init', 'rts_add_referral_columns');

function rts_add_referrer_id_column()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'rts_survey_tracking';

    $column_exists = $wpdb->get_var(
        "SHOW COLUMNS FROM $table_name LIKE 'referrer_participant_id'"
    );

    if (!$column_exists) {
        $wpdb->query(
            "ALTER TABLE $table_name ADD COLUMN referrer_participant_id bigint(20) DEFAULT NULL"
        );
        error_log('RTS: Added referrer_participant_id column to tracking table');
    }
}
add_action('init', 'rts_add_referrer_id_column');

function rts_referral_stats_shortcode($atts)
{
    $tracking = function_exists('rts_init') ? rts_init()->tracking : null;
    $registration = new RTS_Registration($tracking);
    $page = new RTS_Registration_Page($tracking, $registration);
    return $page->render_referral_stats($atts);
}
add_shortcode('rts_referral_stats', 'rts_referral_stats_shortcode');

function rts_add_unique_referral_constraint()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'rts_referrals';

    // First, clean up any duplicates
    $wpdb->query(
        "DELETE r1 FROM $table_name r1
         INNER JOIN $table_name r2 
         WHERE r1.referred_email = r2.referred_email 
         AND r1.referrer_id = r2.referrer_id
         AND r1.id > r2.id"
    );

    // Then add unique constraint
    $index_exists = $wpdb->get_var(
        "SHOW INDEX FROM $table_name WHERE Key_name = 'unique_referral_per_user'"
    );

    if (!$index_exists) {
        $wpdb->query(
            "ALTER TABLE $table_name ADD UNIQUE INDEX unique_referral_per_user (referrer_id, referred_email)"
        );
        error_log('RTS: Added unique constraint to referrals table');
    }
}
add_action('init', 'rts_add_unique_referral_constraint');

////////
/**
 * Initialize the trophy system
 */
function rts_init_trophy_system()
{
    global $rts_trophy_instance;

    // Only initialize if class exists and not already initialized
    if (!isset($rts_trophy_instance) && class_exists('RTS_Trophy')) {
        try {
            $rts_trophy_instance = new RTS_Trophy();
            error_log('RTS: Trophy system initialized successfully');
        } catch (Exception $e) {
            error_log('RTS: Failed to initialize trophy system: ' . $e->getMessage());
        }
    }
    return $rts_trophy_instance;
}
// Load early
add_action('init', 'rts_init_trophy_system', 5);
// Also load on plugins_loaded as fallback
add_action('plugins_loaded', 'rts_init_trophy_system', 20);

/**
 * Register trophy shortcodes - DIRECT REGISTRATION
 * This ensures shortcodes are always available
 */
function rts_register_trophy_shortcodes()
{
    global $rts_trophy_instance;

    // First try to use existing instance
    if (isset($rts_trophy_instance) && is_object($rts_trophy_instance)) {
        add_shortcode('rts_trophy_case', array($rts_trophy_instance, 'render_trophy_case'));
        add_shortcode('rts_marathon_one_trophy_case', array($rts_trophy_instance, 'render_marathon_one_trophy_case'));
        add_shortcode('rts_trophy_case_marathon_1', array($rts_trophy_instance, 'render_marathon_one_trophy_case'));
        add_shortcode('rts_single_trophy', array($rts_trophy_instance, 'render_single_trophy'));
        add_shortcode('rts_trophy_room', array($rts_trophy_instance, 'render_trophy_room'));
        error_log('RTS: Trophy shortcodes registered via instance');
        return;
    }

    // Fallback: Create a new instance just for shortcodes
    if (class_exists('RTS_Trophy')) {
        try {
            $trophy = new RTS_Trophy();
            add_shortcode('rts_trophy_case', array($trophy, 'render_trophy_case'));
            add_shortcode('rts_marathon_one_trophy_case', array($trophy, 'render_marathon_one_trophy_case'));
            add_shortcode('rts_trophy_case_marathon_1', array($trophy, 'render_marathon_one_trophy_case'));
            add_shortcode('rts_single_trophy', array($trophy, 'render_single_trophy'));
            add_shortcode('rts_trophy_room', array($trophy, 'render_trophy_room'));
            error_log('RTS: Trophy shortcodes registered via fallback');
        } catch (Exception $e) {
            error_log('RTS: Failed to register trophy shortcodes: ' . $e->getMessage());
        }
    }
}
// Register shortcodes on init with high priority
add_action('init', 'rts_register_trophy_shortcodes', 1);

/**
 * Debug function to check if shortcodes are registered
 */
function rts_debug_shortcodes()
{
    global $shortcode_tags;
    $trophy_shortcodes = array();
    foreach ($shortcode_tags as $tag => $callback) {
        if (strpos($tag, 'rts_trophy') !== false || strpos($tag, 'trophy') !== false) {
            $trophy_shortcodes[] = $tag;
        }
    }
    if (!empty($trophy_shortcodes)) {
        error_log('RTS: Registered trophy shortcodes: ' . implode(', ', $trophy_shortcodes));
    } else {
        error_log('RTS: No trophy shortcodes found!');
    }
}
add_action('init', 'rts_debug_shortcodes', 20);

/**
 * Ensure trophy table has all required columns
 */
function rts_ensure_trophy_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'rts_user_trophies';

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        error_log('RTS: Trophy table does not exist, creating...');
        $plugin = RunTheSeasPlugin::get_instance();
        $plugin->create_race_tables();
        return;
    }

    // Add missing columns
    $columns_to_add = array(
        'trophy_key' => "ALTER TABLE $table_name ADD COLUMN trophy_key varchar(50) DEFAULT NULL",
        'split_days' => "ALTER TABLE $table_name ADD COLUMN split_days int(11) DEFAULT 0",
        'total_days' => "ALTER TABLE $table_name ADD COLUMN total_days int(11) DEFAULT 0",
        'crew_members' => "ALTER TABLE $table_name ADD COLUMN crew_members int(11) DEFAULT 0",
        'miles_required' => "ALTER TABLE $table_name ADD COLUMN miles_required int(11) DEFAULT 0"
    );

    foreach ($columns_to_add as $column => $sql) {
        $column_exists = $wpdb->get_var(
            "SHOW COLUMNS FROM $table_name LIKE '$column'"
        );
        if (!$column_exists) {
            $wpdb->query($sql);
            error_log("RTS: Added column $column to $table_name");
        }
    }
}
add_action('init', 'rts_ensure_trophy_table');
add_filter('rts_trophy_definitions', function ($trophies) {
    $upload_dir = wp_get_upload_dir();
    $trophy_image_base_url = trailingslashit($upload_dir['baseurl']) . '2026/07/';

    $trophies['5k'] = array(
        'name'           => '5K Trophy',
        'miles_required' => 5000,
        'crew_members'   => 5,
        'trophy_type'    => '5k',
        'rank'           => 1,
        'description'    => 'Founding Runner Marathon - 5K',
        'image_url'      => $trophy_image_base_url . 'run-the-sea-gold.png',
        //'icon_url'       => $trophy_image_base_url . 'run-the-sea-gold.png',
    );
    
    $trophies['10k'] = array(
        'name'           => '10K Trophy',
        'miles_required' => 10000,
        'crew_members'   => 5,
        'trophy_type'    => '10k',
        'rank'           => 2,
        'description'    => 'Founding Runner Marathon - 10K',
        'image_url'      => $trophy_image_base_url . '10k.png',
        //'icon_url'       => $trophy_image_base_url . '10k.png',
    );
    
    
    

    return $trophies;
});


add_filter('rts_trophy_definitions', function ($trophies) {
    $upload_dir = wp_get_upload_dir();
    $image_url = trailingslashit($upload_dir['baseurl']) . '2026/07/10k.png';

    $trophies['10k']['icon_url']  = $image_url;
    $trophies['10k']['image_url'] = $image_url;

    return $trophies;
});
