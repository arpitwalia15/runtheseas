<?php

/**
 * Wbcom Shared Admin System - Main Coordinator (Enhanced for Easy Integration)
 * 
 * @package Wbcom_Shared_Admin
 * @version 2.2.0
 */

if (!defined('ABSPATH')) exit;

class Wbcom_Shared_Loader
{

    const VERSION = '2.2.0';
    const GLOBAL_KEY = 'wbcom_shared_system';

    private static $instance = null;
    private $registered_plugins = array();
    private $is_primary_loader = false;
    private $loaded_from_plugin = '';
    private $shared_path = '';

    /**
     * NEW: Super simple registration - auto-detects everything
     * 
     * @param string $plugin_file Main plugin file path
     * @param array $overrides Optional data to override auto-detection
     * @return bool Success status
     */
    public static function quick_register($plugin_file, $overrides = array())
    {
        // Auto-detect plugin data
        $plugin_data = self::get_plugin_info($plugin_file, $overrides);

        // Register using existing method
        return self::register_plugin($plugin_data);
    }

    /**
     * NEW: Auto-detect plugin information from file and headers
     */
    private static function get_plugin_info($plugin_file, $overrides = array())
    {
        $plugin_dir = dirname($plugin_file);
        $plugin_url = plugin_dir_url($plugin_file);

        // Don't read plugin headers before init hook to avoid translation warnings
        $plugin_header = array(
            'Name' => '',
            'Version' => '',
            'Description' => ''
        );

        // Only get plugin data after init hook
        if (did_action('init')) {
            // Get plugin header data
            if (!function_exists('get_plugin_data')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $plugin_header = get_plugin_data($plugin_file);
        }

        // Extract slug from filename
        $slug = sanitize_key(basename($plugin_file, '.php'));

        // Generate clean settings page slug
        $settings_slug = self::generate_settings_slug($slug);

        // Auto-detect admin class name
        $admin_class = self::find_admin_class($slug);

        // Use override name if available, otherwise empty string
        $name_for_icon = isset($overrides['name']) ? $overrides['name'] : (isset($overrides['menu_title']) ? $overrides['menu_title'] : $plugin_header['Name']);

        // Auto-detect icon based on plugin name/slug
        $icon = self::pick_plugin_icon($name_for_icon, $slug);

        $auto_data = array(
            'slug'         => isset($overrides['slug']) ? $overrides['slug'] : $slug,
            'name'         => isset($overrides['name']) ? $overrides['name'] : (isset($overrides['menu_title']) ? $overrides['menu_title'] : $plugin_header['Name']),
            'version'      => isset($overrides['version']) ? $overrides['version'] : $plugin_header['Version'],
            'description'  => isset($overrides['description']) ? $overrides['description'] : $plugin_header['Description'],
            'settings_url' => isset($overrides['settings_url']) ? $overrides['settings_url'] : admin_url('admin.php?page=wbcom-' . $settings_slug),
            'icon'         => isset($overrides['icon']) ? $overrides['icon'] : $icon,
            'priority'     => isset($overrides['priority']) ? $overrides['priority'] : 10,
            'status'       => isset($overrides['status']) ? $overrides['status'] : 'active',
            'has_premium'  => isset($overrides['has_premium']) ? $overrides['has_premium'] : false,
            'docs_url'     => isset($overrides['docs_url']) ? $overrides['docs_url'] : 'https://docs.wbcomdesigns.com/' . $settings_slug . '/',
            'support_url'  => isset($overrides['support_url']) ? $overrides['support_url'] : 'https://wbcomdesigns.com/support/',
            'shared_path'  => $plugin_dir . '/includes/shared-admin/',
            'admin_class'  => $admin_class,
            'plugin_file'  => $plugin_file,
            'plugin_dir'   => $plugin_dir,
            'plugin_url'   => $plugin_url,
        );

        // Merge any additional overrides that weren't handled above
        foreach ($overrides as $key => $value) {
            if (!isset($auto_data[$key])) {
                $auto_data[$key] = $value;
            }
        }

        return $auto_data;
    }

    /**
     * NEW: Generate clean settings page slug
     */
    private static function generate_settings_slug($slug)
    {
        // Handle common BuddyPress plugin patterns
        if (strpos($slug, 'buddypress-') === 0) {
            // buddypress-activity-filter -> activity-filter
            return substr($slug, 11);
        }

        if (strpos($slug, 'bp-') === 0) {
            // bp-activity-filter -> activity-filter  
            return substr($slug, 3);
        }

        // For other plugins, just use the slug as-is
        return $slug;
    }

    /**
     * NEW: Find admin class name based on common patterns
     */
    private static function find_admin_class($slug)
    {
        $patterns = array(
            // buddypress-activity-filter -> BP_Activity_Filter_Admin
            self::slug_to_bp_class($slug) . '_Admin',
            // general-plugin-name -> General_Plugin_Name_Admin  
            self::slug_to_class($slug) . '_Admin',
            // plugin-name -> Plugin_Name_Admin
            str_replace(' ', '_', ucwords(str_replace('-', ' ', $slug))) . '_Admin',
        );

        foreach ($patterns as $class_name) {
            if (class_exists($class_name)) {
                return $class_name;
            }
        }

        return false;
    }

    /**
     * NEW: Convert slug to BuddyPress class naming convention
     */
    private static function slug_to_bp_class($slug)
    {
        if (strpos($slug, 'buddypress-') === 0) {
            return 'BP_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', substr($slug, 11))));
        }
        if (strpos($slug, 'bp-') === 0) {
            return 'BP_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', substr($slug, 3))));
        }
        return self::slug_to_class($slug);
    }

    /**
     * NEW: Convert slug to standard class naming
     */
    private static function slug_to_class($slug)
    {
        return str_replace(' ', '_', ucwords(str_replace('-', ' ', $slug)));
    }

    /**
     * NEW: Pick appropriate icon based on plugin name/type
     */
    private static function pick_plugin_icon($name, $slug)
    {
        $name_lower = strtolower($name . ' ' . $slug);

        if (strpos($name_lower, 'activity') !== false) return 'dashicons-admin-comments';
        if (strpos($name_lower, 'member') !== false) return 'dashicons-admin-users';
        if (strpos($name_lower, 'group') !== false) return 'dashicons-groups';
        if (strpos($name_lower, 'message') !== false) return 'dashicons-email';
        if (strpos($name_lower, 'notification') !== false) return 'dashicons-bell';
        if (strpos($name_lower, 'profile') !== false) return 'dashicons-admin-users';
        if (strpos($name_lower, 'media') !== false) return 'dashicons-admin-media';
        if (strpos($name_lower, 'event') !== false) return 'dashicons-calendar';
        if (strpos($name_lower, 'poll') !== false) return 'dashicons-chart-bar';
        if (strpos($name_lower, 'quote') !== false) return 'dashicons-format-quote';
        if (strpos($name_lower, 'hashtag') !== false) return 'dashicons-tag';
        if (strpos($name_lower, 'filter') !== false) return 'dashicons-filter';
        if (strpos($name_lower, 'social') !== false) return 'dashicons-share';
        if (strpos($name_lower, 'buddypress') !== false) return 'dashicons-groups';

        return 'dashicons-admin-generic';
    }

    // ==== EXISTING CODE BELOW - NO CHANGES ====

    /**
     * Easy registration method - call this from plugin main file
     * 
     * @param array $plugin_data Plugin information
     * @return bool Success status
     */
    public static function register_plugin($plugin_data)
    {
        // Validate required plugin data
        $plugin_data = self::validate_plugin_data($plugin_data);
        if (!$plugin_data) {
            return false;
        }

        // Get or create shared system instance
        $shared_system = self::get_shared_instance($plugin_data);

        // Register the plugin
        $shared_system->add_plugin($plugin_data);

        return true;
    }

    /**
     * Get shared system instance (singleton across all plugins)
     */
    private static function get_shared_instance($plugin_data)
    {
        // Check if instance already exists globally
        if (isset($GLOBALS[self::GLOBAL_KEY])) {
            return $GLOBALS[self::GLOBAL_KEY];
        }

        // Create new instance
        $instance = new self();
        $instance->is_primary_loader = true;
        $instance->loaded_from_plugin = $plugin_data['slug'];
        $instance->shared_path = $plugin_data['shared_path'];
        $instance->init_shared_system();

        // Store globally for other plugins
        $GLOBALS[self::GLOBAL_KEY] = $instance;

        return $instance;
    }

    /**
     * Validate and sanitize plugin data
     */
    private static function validate_plugin_data($data)
    {
        $defaults = array(
            'slug'         => '',
            'name'         => '',
            'version'      => '1.0.0',
            'settings_url' => '',
            'icon'         => 'dashicons-admin-generic',
            'priority'     => 10,
            'description'  => '',
            'status'       => 'active',
            'has_premium'  => false,
            'docs_url'     => '',
            'support_url'  => '',
            'shared_path'  => '', // Path to shared-admin folder
        );

        $plugin_data = wp_parse_args($data, $defaults);

        // Validate required fields
        if (empty($plugin_data['slug']) || empty($plugin_data['name'])) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
            }
            return false;
        }

        // Auto-detect shared path if not provided
        if (empty($plugin_data['shared_path'])) {
            $plugin_data['shared_path'] = self::detect_shared_path();
        }

        // Sanitize data
        $plugin_data['slug'] = sanitize_key($plugin_data['slug']);
        $plugin_data['name'] = sanitize_text_field($plugin_data['name']);
        $plugin_data['version'] = sanitize_text_field($plugin_data['version']);
        $plugin_data['description'] = sanitize_text_field($plugin_data['description']);
        $plugin_data['priority'] = absint($plugin_data['priority']);
        $plugin_data['has_premium'] = (bool) $plugin_data['has_premium'];

        return $plugin_data;
    }

    /**
     * Auto-detect shared folder path
     */
    private static function detect_shared_path()
    {
        // Get calling file path
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace -- Used for path auto-detection, not debugging.
        $backtrace = debug_backtrace();
        $calling_file = '';

        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], 'shared-admin') === false) {
                $calling_file = $trace['file'];
                break;
            }
        }

        if (empty($calling_file)) {
            return '';
        }

        // Look for shared-admin folder
        $plugin_dir = dirname($calling_file);
        $possible_paths = array(
            $plugin_dir . '/includes/shared-admin/',
            $plugin_dir . '/shared-admin/',
            $plugin_dir . '/admin/shared-admin/',
        );

        foreach ($possible_paths as $path) {
            if (file_exists($path . 'class-wbcom-shared-loader.php')) {
                return $path;
            }
        }

        return '';
    }

    /**
     * Add plugin to registry
     */
    public function add_plugin($plugin_data)
    {
        $this->registered_plugins[$plugin_data['slug']] = $plugin_data;

        // Sort by priority
        uasort($this->registered_plugins, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });
    }

    /**
     * Initialize the shared admin system
     */
    private function init_shared_system()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
        }

        if (!$this->is_primary_loader) return;

        if (defined('WP_DEBUG') && WP_DEBUG) {
        }

        // Load required classes
        $this->load_shared_classes();

        // Initialize main menu and dashboard
        if (did_action('admin_menu')) {
            // admin_menu hook has already fired, create menu immediately
            if (defined('WP_DEBUG') && WP_DEBUG) {
            }
            $this->create_main_menu();
            $this->add_plugin_submenus();
        } else {
            // admin_menu hook hasn't fired yet, add hooks
            add_action('admin_menu', array($this, 'create_main_menu'), 5);
            add_action('admin_menu', array($this, 'add_plugin_submenus'), 10);
            if (defined('WP_DEBUG') && WP_DEBUG) {
            }
        }

        // Enqueue shared assets for all Wbcom pages - use priority 1 to load early
        add_action('admin_enqueue_scripts', array($this, 'enqueue_shared_assets'), 1);

        // Version check and conflict resolution
        add_action('admin_init', array($this, 'check_version_conflicts'));
    }

    /**
     * Load shared system classes
     */
    private function load_shared_classes()
    {
        $base_path = $this->shared_path;

        if (empty($base_path) || !is_dir($base_path)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
            }
            return;
        }

        $classes = array(
            'class-wbcom-shared-dashboard.php'
        );

        foreach ($classes as $class_file) {
            $file_path = $base_path . $class_file;
            if (file_exists($file_path)) {
                require_once $file_path;
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                }
            }
        }
    }

    /**
     * Create main Wbcom Designs menu
     */
    public function create_main_menu()
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
        }

        // Check if menu already exists
        if ($this->menu_exists()) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
            }
            return;
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
        }

        add_menu_page(
            'Wbcom Designs',
            'Wbcom Designs',
            'manage_options',
            'wbcom-designs',
            array($this, 'show_dashboard'),
            $this->get_menu_icon(),
            58.5
        );

        if (defined('WP_DEBUG') && WP_DEBUG) {
        }

        // Add dashboard as first submenu
        add_submenu_page(
            'wbcom-designs',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'wbcom-designs',
            array($this, 'show_dashboard')
        );

        if (defined('WP_DEBUG') && WP_DEBUG) {
        }
    }

    /**
     * Add submenu for registered plugins
     */
    public function add_plugin_submenus()
    {
        foreach ($this->registered_plugins as $plugin) {
            if ($plugin['status'] !== 'active' || empty($plugin['settings_url'])) {
                continue;
            }

            $menu_slug = $this->extract_menu_slug($plugin['settings_url']);

            if (empty($menu_slug)) {
                continue;
            }

            // Only add if submenu doesn't already exist
            if (!$this->submenu_exists($menu_slug)) {
                // Apply filter to allow customization of menu label
                $menu_label = apply_filters('wbcom_submenu_label', $plugin['name'], $plugin['slug'], $plugin);

                add_submenu_page(
                    'wbcom-designs',
                    $plugin['name'],  // Page title
                    $menu_label,      // Menu label (filtered)
                    'manage_options',
                    $menu_slug,
                    array($this, 'show_plugin_page')
                );
            }
        }
    }

    /**
     * Show plugin page - Routes to the appropriate plugin callback
     */
    public function show_plugin_page()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET page parameter for routing, no state change.
        $current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

        // Find the plugin that matches this page
        foreach ($this->registered_plugins as $plugin) {
            $plugin_slug = $this->extract_menu_slug($plugin['settings_url']);

            if ($plugin_slug === $current_page) {
                // Try to call the plugin's admin method
                $this->load_plugin_admin($plugin);
                return;
            }
        }

        // Fallback if no plugin found
        $this->show_not_found_page();
    }

    /**
     * NEW: Load and display the plugin's admin interface
     */
    private function load_plugin_admin($plugin)
    {
        // Check if plugin has a callback first
        if (isset($plugin['callback']) && is_callable($plugin['callback'])) {
            call_user_func($plugin['callback']);
            return;
        }

        $admin_class = isset($plugin['admin_class']) ? $plugin['admin_class'] : false;

        // Try the detected admin class first
        if ($admin_class && class_exists($admin_class)) {
            $this->get_admin_instance($admin_class, $plugin);
            return;
        }

        // Try the main plugin class method
        $plugin_slug = str_replace('-', '_', $plugin['slug']);
        $main_function = $plugin_slug;

        if (function_exists($main_function)) {
            $instance = call_user_func($main_function);
            if ($instance && method_exists($instance, 'render_admin_page')) {
                $instance->render_admin_page();
                return;
            }
        }

        // Show basic fallback page
        $this->show_basic_page($plugin);
    }

    /**
     * NEW: Get admin class instance and call settings page
     */
    private function get_admin_instance($admin_class, $plugin)
    {
        $methods = array(
            'render_settings_page',
            'settings_page',
            'show_settings_page',
            'admin_page',
            'show_admin_page'
        );

        // First try to get instance using common singleton patterns
        $instance = null;

        if (method_exists($admin_class, 'instance')) {
            $instance = call_user_func(array($admin_class, 'instance'));
        } elseif (method_exists($admin_class, 'get_instance')) {
            $instance = call_user_func(array($admin_class, 'get_instance'));
        } elseif (method_exists($admin_class, 'getInstance')) {
            $instance = call_user_func(array($admin_class, 'getInstance'));
        }

        // Try instance methods first if we have an instance
        if ($instance) {
            foreach ($methods as $method) {
                if (method_exists($instance, $method)) {
                    $instance->$method();
                    return;
                }
            }
        }

        // Fallback: try static methods
        foreach ($methods as $method) {
            if (method_exists($admin_class, $method)) {
                // Check if method is actually static
                $reflection = new ReflectionMethod($admin_class, $method);
                if ($reflection->isStatic()) {
                    call_user_func(array($admin_class, $method));
                    return;
                }
            }
        }

        // Show basic page if nothing else works
        $this->show_basic_page($plugin);
    }

    /**
     * Show basic plugin page
     */
    private function show_basic_page($plugin)
    {
?>
        <div class="wrap">
            <h1><?php echo esc_html($plugin['name']); ?></h1>
            <div class="notice notice-info">
                <p>
                    <strong>Plugin Loaded Successfully!</strong><br>
                    The plugin is active but the admin interface is loading. Please check that all plugin files are properly installed.
                </p>
            </div>

            <div class="card">
                <h2>Plugin Information</h2>
                <table class="form-table">
                    <tr>
                        <th>Version:</th>
                        <td><?php echo esc_html($plugin['version']); ?></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td><span style="color: #00a32a;">✓ Active</span></td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td><?php echo esc_html($plugin['description']); ?></td>
                    </tr>
                </table>

                <?php if (!empty($plugin['docs_url']) || !empty($plugin['support_url'])) : ?>
                    <p>
                        <?php if (!empty($plugin['docs_url'])) : ?>
                            <a href="<?php echo esc_url($plugin['docs_url']); ?>" target="_blank" class="button button-secondary">
                                <span class="dashicons dashicons-book"></span>
                                Documentation
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($plugin['support_url'])) : ?>
                            <a href="<?php echo esc_url($plugin['support_url']); ?>" target="_blank" class="button button-secondary">
                                <span class="dashicons dashicons-sos"></span>
                                Get Support
                            </a>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php
    }

    /**
     * Show page when plugin not found
     */
    private function show_not_found_page()
    {
    ?>
        <div class="wrap">
            <h1>Plugin Not Found</h1>
            <div class="notice notice-error">
                <p>The requested plugin page could not be found or is not properly registered.</p>
            </div>
            <p><a href="<?php echo esc_url(admin_url('admin.php?page=wbcom-designs')); ?>" class="button button-primary">← Back to Dashboard</a></p>
        </div>
    <?php
    }

    /**
     * Show dashboard
     */
    public function show_dashboard()
    {
        try {
            if (class_exists('Wbcom_Shared_Dashboard')) {
                $dashboard = new Wbcom_Shared_Dashboard($this->registered_plugins);
                $dashboard->render_dashboard();
            } else {
                $this->show_fallback_dashboard();
            }
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
            }
            $this->show_fallback_dashboard();
        }
    }

    /**
     * Show fallback dashboard if main dashboard fails
     */
    private function show_fallback_dashboard()
    {
    ?>
        <div class="wrap">
            <h1>🌟 Wbcom Designs</h1>

            <div class="notice notice-info">
                <p><strong>Welcome to Wbcom Designs!</strong> Your plugins are being loaded...</p>
            </div>

            <div class="card">
                <h2>Installed Wbcom Plugins</h2>
                <?php if (!empty($this->registered_plugins)) : ?>
                    <ul>
                        <?php foreach ($this->registered_plugins as $plugin) : ?>
                            <li>
                                <strong><?php echo esc_html($plugin['name']); ?></strong>
                                (v<?php echo esc_html($plugin['version']); ?>)
                                <?php if (!empty($plugin['settings_url'])) : ?>
                                    - <a href="<?php echo esc_url($plugin['settings_url']); ?>">Settings</a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>No plugins registered yet.</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Quick Links</h2>
                <p>
                    <a href="https://wbcomdesigns.com/support/" target="_blank" class="button button-secondary">Get Support</a>
                    <a href="https://wbcomdesigns.com/plugins/" target="_blank" class="button button-secondary">Browse Premium Plugins</a>
                    <a href="https://docs.wbcomdesigns.com/" target="_blank" class="button button-secondary">Documentation</a>
                </p>
            </div>
        </div>

        <style>
            .card {
                background: #fff;
                border: 1px solid #c3c4c7;
                padding: 20px;
                margin: 20px 0;
                border-radius: 4px;
            }

            .card h2 {
                margin-top: 0;
            }
        </style>
    <?php
    }

    /**
     * Check for version conflicts between shared systems
     */
    public function check_version_conflicts()
    {
        // Check if multiple versions are loaded
        if (defined('WBCOM_SHARED_VERSION') && WBCOM_SHARED_VERSION !== self::VERSION) {
            add_action('admin_notices', array($this, 'version_conflict_notice'));
        } else {
            define('WBCOM_SHARED_VERSION', self::VERSION);
        }
    }

    /**
     * Display version conflict notice
     */
    public function version_conflict_notice()
    {
    ?>
        <div class="notice notice-warning">
            <p>
                <strong>Wbcom Shared System Conflict</strong><br>
                Multiple versions of the Wbcom shared system detected. Please update all Wbcom plugins to ensure compatibility.
            </p>
        </div>
<?php
    }

    /**
     * NEW: Enqueue shared CSS and JS assets for Wbcom pages ONLY
     */
    public function enqueue_shared_assets($hook_suffix)
    {
        // Only load on Wbcom admin pages
        if (!$this->is_wbcom_admin_page($hook_suffix)) {
            return;
        }

        // Check if already enqueued to prevent duplicates
        if (wp_style_is('wbcom-shared-admin', 'enqueued') || wp_style_is('wbcom-shared-admin', 'done')) {
            return;
        }

        $assets_url = $this->get_shared_assets_url();
        $version = self::VERSION;

        // Verify files exist before enqueueing
        $css_file = $this->shared_path . 'wbcom-shared-admin.css';
        $js_file = $this->shared_path . 'wbcom-shared-admin.js';

        // Enqueue CSS
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'wbcom-shared-admin',
                $assets_url . 'wbcom-shared-admin.css',
                array(),
                $version
            );
        }

        // Enqueue JS
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'wbcom-shared-admin',
                $assets_url . 'wbcom-shared-admin.js',
                array('jquery'),
                $version,
                true
            );

            // Localize script with minimal data
            wp_localize_script('wbcom-shared-admin', 'wbcomShared', array(
                'version' => $version,
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wbcom_shared_nonce'),
                'pluginCount' => count($this->registered_plugins),
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Admin page param for localization, read-only.
                'currentPage' => isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '',
                'strings' => array(
                    'loading' => __('Loading...', 'wbcom-essential'),
                    'error' => __('Error loading content.', 'wbcom-essential'),
                    'success' => __('Settings saved successfully.', 'wbcom-essential'),
                )
            ));
        }
    }

    /**
     * NEW: Check if current page is a Wbcom admin page (STRICT checking)
     * Only loads shared assets on the main dashboard, NOT on individual plugin pages
     */
    private function is_wbcom_admin_page($hook_suffix)
    {
        // Get current page parameter
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET page for admin page check, read-only.
        $current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

        // STRICT CHECK: Only load shared assets on main dashboard pages

        // 1. Main Wbcom dashboard
        if ($current_page === 'wbcom-designs') {
            return true;
        }

        // 2. Hook suffix patterns for main Wbcom dashboard only
        $wbcom_hook_patterns = array(
            'toplevel_page_wbcom-designs',
        );

        foreach ($wbcom_hook_patterns as $pattern) {
            if (strpos($hook_suffix, $pattern) === 0) {
                return true;
            }
        }

        // DO NOT load shared assets on individual plugin pages - they should load their own CSS/JS
        return false;
    }

    /**
     * NEW: Get shared assets URL
     */
    private function get_shared_assets_url()
    {
        // Try to determine the URL from the shared path
        $plugin_url = '';

        // Find which plugin loaded this shared system
        foreach ($this->registered_plugins as $plugin) {
            if (isset($plugin['plugin_url']) && isset($plugin['shared_path'])) {
                if ($plugin['shared_path'] === $this->shared_path) {
                    $plugin_url = $plugin['plugin_url'];
                    break;
                }
            }
        }

        if (!empty($plugin_url)) {
            return $plugin_url . 'includes/shared-admin/';
        }

        // Fallback: try to determine from shared path
        $wp_content_dir = wp_normalize_path(WP_CONTENT_DIR);
        $shared_path_normalized = wp_normalize_path($this->shared_path);

        if (strpos($shared_path_normalized, $wp_content_dir) === 0) {
            $relative_path = str_replace($wp_content_dir, '', $shared_path_normalized);
            return content_url($relative_path);
        }

        // Final fallback
        return plugins_url('includes/shared-admin/', $this->loaded_from_plugin);
    }
    private function menu_exists()
    {
        global $menu;

        if (!is_array($menu)) {
            return false;
        }

        foreach ($menu as $item) {
            if (isset($item[2]) && $item[2] === 'wbcom-designs') {
                return true;
            }
        }

        return false;
    }

    private function submenu_exists($menu_slug)
    {
        global $submenu;

        if (!isset($submenu['wbcom-designs'])) {
            return false;
        }

        foreach ($submenu['wbcom-designs'] as $item) {
            if (isset($item[2]) && $item[2] === $menu_slug) {
                return true;
            }
        }

        return false;
    }

    private function extract_menu_slug($settings_url)
    {
        $parsed = wp_parse_url($settings_url);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $params);
            return isset($params['page']) ? $params['page'] : '';
        }
        return '';
    }

    private function get_menu_icon()
    {
        $svg = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 2L13.09 8.26L20 9L14 12L15 20L10 17L5 20L6 12L0 9L6.91 8.26L10 2Z" fill="#a7aaad"/>
        </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function get_registered_plugins()
    {
        return $this->registered_plugins;
    }

    public function get_active_plugins()
    {
        return array_filter($this->registered_plugins, function ($plugin) {
            return $plugin['status'] === 'active';
        });
    }

    public static function get_instance()
    {
        return isset($GLOBALS[self::GLOBAL_KEY]) ? $GLOBALS[self::GLOBAL_KEY] : null;
    }

    public function get_debug_info()
    {
        return array(
            'version' => self::VERSION,
            'is_primary_loader' => $this->is_primary_loader,
            'loaded_from_plugin' => $this->loaded_from_plugin,
            'shared_path' => $this->shared_path,
            'shared_path_exists' => is_dir($this->shared_path),
            'css_file_exists' => file_exists($this->shared_path . 'wbcom-shared-admin.css'),
            'js_file_exists' => file_exists($this->shared_path . 'wbcom-shared-admin.js'),
            'registered_plugins_count' => count($this->registered_plugins),
            'registered_plugins' => array_keys($this->registered_plugins),
            'dashboard_class_exists' => class_exists('Wbcom_Shared_Dashboard'),
            'current_hook' => isset($GLOBALS['hook_suffix']) ? $GLOBALS['hook_suffix'] : 'unknown',
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Debug info, read-only.
            'current_page' => isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : 'none',
            'assets_url' => $this->get_shared_assets_url(),
        );
    }

    /**
     * NEW: Get list of pages where assets should load (for debugging)
     */
    public function get_asset_pages()
    {
        $pages = array('wbcom-designs'); // Dashboard

        // Add all registered plugin pages
        foreach ($this->registered_plugins as $plugin) {
            $plugin_page = $this->extract_menu_slug($plugin['settings_url']);
            if (!empty($plugin_page)) {
                $pages[] = $plugin_page;
            }
        }

        return $pages;
    }
}
