<?php
/**
 * Wbcom Easy Setup Helper
 * 
 * One-liner integration for any Wbcom plugin
 * 
 * @package Wbcom_Shared
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * One-line integration function
 * 
 * Usage: wbcom_integrate_plugin(__FILE__);
 * 
 * @param string $plugin_file Main plugin file
 * @param array $custom_data Optional custom data
 */
function wbcom_integrate_plugin($plugin_file, $custom_data = array()) {
    // Only run in admin
    if (!is_admin()) {
        return false;
    }
    
    // Load the shared system
    $shared_path = dirname($plugin_file) . '/includes/shared-admin/';
    $loader_file = $shared_path . 'class-wbcom-shared-loader.php';
    
    if (!file_exists($loader_file)) {
        return false;
    }
    
    // Load shared system if not already loaded
    if (!class_exists('Wbcom_Shared_Loader')) {
        require_once $loader_file;
    }
    
    // Auto-register the plugin
    return Wbcom_Shared_Loader::quick_register($plugin_file, $custom_data);
}

/**
 * Alternative function for plugins that want more control
 */
function wbcom_register_plugin($plugin_file, $plugin_data) {
    return wbcom_integrate_plugin($plugin_file, $plugin_data);
}