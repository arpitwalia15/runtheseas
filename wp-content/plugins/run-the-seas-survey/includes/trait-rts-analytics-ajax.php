<?php

if (!defined('ABSPATH')) {
    exit;
}

trait RTS_Analytics_Ajax
{
    public function ajax_get_analytics_data()
    {
        if (class_exists('RTS_Analytics')) {
            $analytics = new RTS_Analytics($this->tracking);
            $analytics->ajax_get_analytics_data();
        } else {
            wp_send_json_error('Analytics class not found');
        }
    }

    public function ajax_export_analytics()
    {
        if (class_exists('RTS_Analytics')) {
            $analytics = new RTS_Analytics($this->tracking);
            $analytics->ajax_export_analytics();
        } else {
            wp_die('Analytics class not found');
        }
    }

    public function ajax_archive_analytics()
    {
        if (class_exists('RTS_Analytics')) {
            $analytics = new RTS_Analytics($this->tracking);
            $analytics->ajax_archive_analytics();
        } else {
            wp_send_json_error('Analytics class not found');
        }
    }

    // Add to run-the-seas-survey.php - create_race_tables() function
}
