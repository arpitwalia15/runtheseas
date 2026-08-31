<?php

/**
 * Wbcom Shared Dashboard - Universal Dashboard for All Plugins
 * 
 * @package Wbcom_Shared_Admin
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

class Wbcom_Shared_Dashboard
{

    private $registered_plugins = array();
    private $menu_created = false;

    /**
     * Constructor
     */
    public function __construct($plugins = array())
    {
        $this->registered_plugins = $plugins;
        $this->init();
    }

    /**
     * Initialize dashboard
     */
    private function init()
    {
        add_action('admin_menu', array($this, 'create_main_menu'), 5);
        add_action('admin_menu', array($this, 'add_plugin_submenus'), 10);
    }

    /**
     * Create main Wbcom Designs menu
     */
    public function create_main_menu()
    {
        if ($this->menu_created) return;

        add_menu_page(
            esc_html__('Wbcom Designs', 'wbcom-essential'),
            esc_html__('Wbcom Designs', 'wbcom-essential'),
            'manage_options',
            'wbcom-designs',
            array($this, 'render_dashboard'),
            $this->get_menu_icon(),
            58.5
        );

        // Add dashboard as first submenu
        add_submenu_page(
            'wbcom-designs',
            esc_html__('Dashboard', 'wbcom-essential'),
            esc_html__('Dashboard', 'wbcom-essential'),
            'manage_options',
            'wbcom-designs',
            array($this, 'render_dashboard')
        );

        $this->menu_created = true;
    }

    /**
     * Add submenu for each registered plugin
     */
    public function add_plugin_submenus()
    {
        foreach ($this->registered_plugins as $plugin) {
            if ($plugin['status'] !== 'active') continue;

            $menu_slug = $this->extract_menu_slug($plugin['settings_url']);

            if (empty($menu_slug)) continue;

            add_submenu_page(
                'wbcom-designs',
                $plugin['name'],
                $plugin['name'],
                'manage_options',
                $menu_slug,
                '__return_null' // Plugin handles its own rendering
            );
        }
    }

    /**
     * Render main dashboard
     */
    public function render_dashboard()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin dashboard tab parameter, no state change.
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'overview'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
?>
        <div class="wrap wbcom-shared-dashboard">
            <h1>
                🌟
                <?php esc_html_e('Wbcom Designs', 'wbcom-essential'); ?>
                <span class="wbcom-version">v<?php echo esc_html(Wbcom_Shared_Loader::VERSION); ?></span>
            </h1>

            <?php $this->render_admin_notices(); ?>

            <div class="wbcom-dashboard-content">
                <div class="wbcom-dashboard-main">
                    <?php $this->render_dashboard_tabs($active_tab); ?>
                </div>
                <div class="wbcom-dashboard-sidebar">
                    <?php $this->render_sidebar_widgets(); ?>
                </div>
            </div>
        </div>
    <?php
    }

    /**
     * Render dashboard tabs
     */
    private function render_dashboard_tabs($active_tab)
    {
        $tabs = array(
            'overview' => array(
                'title' => esc_html__('Overview', 'wbcom-essential'),
                'icon'  => 'dashicons-dashboard',
            ),
            'plugins' => array(
                'title' => esc_html__('Installed Plugins', 'wbcom-essential'),
                'icon'  => 'dashicons-admin-plugins',
            ),
            'premium' => array(
                'title' => esc_html__('Premium Plugins', 'wbcom-essential'),
                'icon'  => 'dashicons-star-filled',
            ),
            'themes' => array(
                'title' => esc_html__('Premium Themes', 'wbcom-essential'),
                'icon'  => 'dashicons-admin-appearance',
            ),
            'news' => array(
                'title' => esc_html__('News & Updates', 'wbcom-essential'),
                'icon'  => 'dashicons-rss',
            ),
        );
    ?>
        <div class="wbcom-dashboard-tabs">
            <nav class="nav-tab-wrapper">
                <?php foreach ($tabs as $tab_key => $tab_data) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wbcom-designs&tab=' . $tab_key)); ?>"
                        class="nav-tab <?php echo esc_attr( $active_tab === $tab_key ? 'nav-tab-active' : '' ); ?>">
                        <span class="dashicons <?php echo esc_attr($tab_data['icon']); ?>"></span>
                        <?php echo esc_html($tab_data['title']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="tab-content">
                <?php
                switch ($active_tab) {
                    case 'plugins':
                        $this->render_plugins_tab();
                        break;
                    case 'premium':
                        $this->render_premium_tab();
                        break;
                    case 'themes':
                        $this->render_themes_tab();
                        break;
                    case 'news':
                        $this->render_news_tab();
                        break;
                    case 'overview':
                    default:
                        $this->render_overview_tab();
                        break;
                }
                ?>
            </div>
        </div>
    <?php
    }

    /**
     * Render overview tab
     */
    private function render_overview_tab()
    {
    ?>
        <div class="wbcom-welcome-panel" style="background: #fff; border: 1px solid #e1e5e9; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
            <h2 style="color: #153045; font-size: 24px; font-weight: 700; margin: 0 0 16px 0;"><?php esc_html_e('Welcome to Wbcom Designs Dashboard', 'wbcom-essential'); ?></h2>
            <p class="about-description" style="color: #515b67; font-size: 15px; line-height: 1.6; margin-bottom: 20px;">
                <?php esc_html_e('Your central hub for managing premium WordPress and BuddyPress solutions. At Wbcom Designs, we specialize in creating powerful community plugins, custom development services, and comprehensive support solutions. Our Care Plan ensures your site stays optimized and secure with priority support, regular updates, and expert maintenance.', 'wbcom-essential'); ?>
            </p>
            <div class="wbcom-care-plan-notice" style="background: linear-gradient(135deg, #2c5282 0%, #1e3a5f 100%); color: white; padding: 30px; border-radius: 12px; margin: 25px 0; box-shadow: 0 8px 25px rgba(44, 82, 130, 0.25); border: 1px solid rgba(255,255,255,0.1); position: relative; overflow: hidden;">
                <!-- Security Shield Background Pattern -->
                <div style="position: absolute; top: -20px; right: -20px; opacity: 0.1; font-size: 120px; transform: rotate(15deg);">🛡️</div>

                <div style="display: flex; align-items: flex-start; gap: 20px;">
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.15); border-radius: 50%; padding: 15px; border: 2px solid rgba(255,255,255,0.2);">
                        <span style="font-size: 28px; display: block; line-height: 1;">⚠️</span>
                    </div>

                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 12px 0; font-size: 22px; font-weight: 700; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            <?php esc_html_e('Don\'t Risk Your Website\'s Success', 'wbcom-essential'); ?>
                        </h3>
                        <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: rgba(255,255,255,0.95);">
                            <?php esc_html_e('WordPress updates can break your site, security vulnerabilities expose your data, and performance issues drive visitors away. Our Care Plan ensures all updates are tested before deployment, security is monitored 24/7, and your site stays optimized. Stop worrying about crashes and focus on growing your business.', 'wbcom-essential'); ?>
                        </p>

                        <div style="display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
                            <a href="https://wbcomdesigns.com/start-a-project/" target="_blank"
                                style="background: #fff; color: #2c5282; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 16px; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease; border: none;"
                                onmouseover="this.style.background='#f8f9fa'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)'"
                                onmouseout="this.style.background='#fff'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'">
                                <span style="background: linear-gradient(135deg, #2c5282, #1e3a5f); color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px;">📞</span>
                                <?php esc_html_e('Schedule Free Consultation', 'wbcom-essential'); ?>
                            </a>

                            <div style="background: rgba(255,255,255,0.1); padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.9); border: 1px solid rgba(255,255,255,0.2);">
                                ✓ No Commitment Required
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="wbcom-welcome-panel-columns" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 24px;">
                <div class="wbcom-welcome-panel-column">
                    <h3 style="color: #153045; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #f0f0f1;"><?php esc_html_e('Our Services', 'wbcom-essential'); ?></h3>
                    <div class="wbcom-action-list" style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="https://wbcomdesigns.com/downloads/wordpress-care-plans/" target="_blank" style="display: block; background: linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%); color: #ffffff; padding: 12px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; text-align: center; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(44, 82, 130, 0.2);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(44, 82, 130, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(44, 82, 130, 0.2)';"><?php esc_html_e('Get Care Plan', 'wbcom-essential'); ?></a>
                        <a href="https://wbcomdesigns.com/plugins/" target="_blank" style="display: block; background: #fff; border: 1px solid #2c5282; color: #2c5282; padding: 12px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; text-align: center; transition: all 0.3s ease;" onmouseover="this.style.background='#f0f4ff'; this.style.borderColor='#1e3a5f'; this.style.color='#1e3a5f';" onmouseout="this.style.background='#fff'; this.style.borderColor='#2c5282'; this.style.color='#2c5282';"><?php esc_html_e('Premium Plugins', 'wbcom-essential'); ?></a>
                        <a href="https://wbcomdesigns.com/start-a-project/" target="_blank" style="display: block; background: #fff; border: 1px solid #2c5282; color: #2c5282; padding: 12px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; text-align: center; transition: all 0.3s ease;" onmouseover="this.style.background='#f0f4ff'; this.style.borderColor='#1e3a5f'; this.style.color='#1e3a5f';" onmouseout="this.style.background='#fff'; this.style.borderColor='#2c5282'; this.style.color='#2c5282';"><?php esc_html_e('Custom Development', 'wbcom-essential'); ?></a>
                        <a href="https://wbcomdesigns.com/support/" target="_blank" style="display: block; background: #fff; border: 1px solid #2c5282; color: #2c5282; padding: 12px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; text-align: center; transition: all 0.3s ease;" onmouseover="this.style.background='#f0f4ff'; this.style.borderColor='#1e3a5f'; this.style.color='#1e3a5f';" onmouseout="this.style.background='#fff'; this.style.borderColor='#2c5282'; this.style.color='#2c5282';"><?php esc_html_e('Get Support', 'wbcom-essential'); ?></a>
                    </div>
                </div>

                <div class="wbcom-welcome-panel-column">
                    <h3 style="color: #153045; font-size: 18px; font-weight: 600; margin: 0 0 20px 0; padding-bottom: 12px; border-bottom: 2px solid #f0f0f1;"><?php esc_html_e('System Status', 'wbcom-essential'); ?></h3>
                    <div class="wbcom-system-status" style="display: flex; flex-direction: column; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                            <span class="status-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo esc_attr( version_compare(get_bloginfo('version'), '5.0', '>=') ? '#2c5282' : '#e74c3c' ); ?>; flex-shrink: 0;"></span>
                            <span style="font-size: 14px; color: #153045; font-weight: 500;"><?php esc_html_e('WordPress Version', 'wbcom-essential'); ?></span>
                            <span style="font-size: 13px; color: #515b67; margin-left: auto;"><?php echo esc_html( get_bloginfo('version') ); ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                            <span class="status-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo esc_attr( function_exists('buddypress') ? '#2c5282' : '#e74c3c' ); ?>; flex-shrink: 0;"></span>
                            <span style="font-size: 14px; color: #153045; font-weight: 500;"><?php esc_html_e('BuddyPress', 'wbcom-essential'); ?></span>
                            <span style="font-size: 13px; color: #515b67; margin-left: auto;"><?php echo function_exists('buddypress') ? esc_html__('Active', 'wbcom-essential') : esc_html__('Inactive', 'wbcom-essential'); ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                            <span class="status-indicator" style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo esc_attr( defined('WP_DEBUG') && WP_DEBUG ? '#e74c3c' : '#2c5282' ); ?>; flex-shrink: 0;"></span>
                            <span style="font-size: 14px; color: #153045; font-weight: 500;"><?php esc_html_e('Production Mode', 'wbcom-essential'); ?></span>
                            <span style="font-size: 13px; color: #515b67; margin-left: auto;"><?php echo defined('WP_DEBUG') && WP_DEBUG ? esc_html__('Debug On', 'wbcom-essential') : esc_html__('Active', 'wbcom-essential'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    /**
     * Render plugins tab
     */
    private function render_plugins_tab()
    {
    ?>
        <div class="wbcom-plugins-header" style="background: #fff; border: 1px solid #e1e5e9; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
            <h2 style="color: #153045; font-size: 24px; font-weight: 700; margin: 0 0 16px 0;"><?php esc_html_e('Installed Wbcom Plugins', 'wbcom-essential'); ?></h2>
        </div>

        <div class="wbcom-plugins-grid">
            <?php if (empty($this->registered_plugins)) : ?>
                <div class="wbcom-no-plugins" style="background: #fff; border: 1px solid #e1e5e9; border-radius: 12px; padding: 60px 20px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
                    <div class="no-plugins-icon">
                        <span class="dashicons dashicons-admin-plugins" style="font-size: 64px; color: #2c5282; margin-bottom: 20px;"></span>
                    </div>
                    <h3 style="color: #153045; font-size: 20px; font-weight: 600; margin: 0 0 12px 0;"><?php esc_html_e('No Wbcom Plugins Found', 'wbcom-essential'); ?></h3>
                    <p style="color: #515b67; font-size: 15px; line-height: 1.6; margin: 0 0 24px 0;"><?php esc_html_e('Looks like you haven\'t installed any Wbcom Designs plugins yet.', 'wbcom-essential'); ?></p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wbcom-designs&tab=premium')); ?>" style="display: inline-block; background: linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%); color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(44, 82, 130, 0.3);" onmouseover="this.style.background='linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(44, 82, 130, 0.4)';" onmouseout="this.style.background='linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(44, 82, 130, 0.3)';">
                        <?php esc_html_e('Browse Premium Plugins', 'wbcom-essential'); ?>
                    </a>
                </div>
            <?php else : ?>
                <?php foreach ($this->registered_plugins as $plugin) : ?>
                    <div class="wbcom-plugin-card plugin-status-<?php echo esc_attr($plugin['status']); ?>">
                        <div class="plugin-card-top">
                            <div class="plugin-card-header">
                                <h3><?php echo esc_html($plugin['name']); ?></h3>
                                <div class="plugin-status-badge <?php echo esc_attr($plugin['status']); ?>">
                                    <?php echo esc_html(ucfirst($plugin['status'])); ?>
                                </div>
                            </div>
                            <p class="plugin-description"><?php echo esc_html($plugin['description']); ?></p>
                            <div class="plugin-version">
                                <span class="version-label"><?php esc_html_e('Version:', 'wbcom-essential'); ?></span>
                                <span class="version-number"><?php echo esc_html($plugin['version']); ?></span>
                            </div>
                        </div>
                        <div class="plugin-card-bottom">
                            <div class="plugin-actions">
                                <?php if ($plugin['status'] === 'active' && !empty($plugin['settings_url'])) : ?>
                                    <a href="<?php echo esc_url($plugin['settings_url']); ?>" class="button button-primary">
                                        <span class="dashicons dashicons-admin-generic"></span>
                                        <?php esc_html_e('Settings', 'wbcom-essential'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php
    }

    /**
     * Render premium plugins tab
     */
    private function render_premium_tab()
    {
        $premium_plugins = $this->get_premium_plugins();

    ?>
        <div class="wbcom-premium-section">
            <div class="wbcom-premium-header">
                <h2><?php esc_html_e('Premium BuddyPress Plugins', 'wbcom-essential'); ?></h2>
                <p><?php esc_html_e('Enhance your community with these powerful premium plugins designed specifically for BuddyPress.', 'wbcom-essential'); ?></p>
            </div>

            <div class="premium-plugins-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 30px; margin-top: 30px;">
                <?php foreach ($premium_plugins as $plugin) : ?>
                    <div class="premium-plugin-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 0; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); transition: all 0.3s ease; position: relative;">


                        <div class="plugin-card-content" style="background: #fff; margin: 3px; border-radius: 13px; padding: 30px;">

                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 22px; font-weight: 700; color: #153045; margin: 0 0 6px 0; line-height: 1.2;">
                                        <?php echo esc_html($plugin['name']); ?>
                                    </h3>
                                    <?php if (isset($plugin['tagline'])) : ?>
                                        <p class="plugin-tagline" style="color: #515b67; font-weight: 400; font-size: 14px; margin: 0; line-height: 1.4;">
                                            <?php echo esc_html($plugin['tagline']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div style="flex-shrink: 0; margin-left: 20px;">
                                    <span class="price-amount" style="font-size: 24px; font-weight: 800; color: #2c5282; background: linear-gradient(135deg, #faf9ff 0%, #f0f4ff 100%); padding: 8px 16px; border-radius: 8px; border: 2px solid #2c5282; display: inline-block; min-width: 80px; text-align: center;">
                                        <?php echo esc_html($plugin['price']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="plugin-description" style="margin-bottom: 20px;">
                                <p style="color: #515b67; font-size: 15px; line-height: 1.6; margin: 0;">
                                    <?php echo esc_html($plugin['description']); ?>
                                </p>
                            </div>

                            <div class="plugin-features" style="margin-bottom: 24px;">
                                <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 8px;">
                                    <?php foreach ($plugin['features'] as $feature) : ?>
                                        <li style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #153045; padding: 6px 0;">
                                            <span class="dashicons dashicons-yes" style="color: #2c5282; font-size: 16px; flex-shrink: 0;"></span>
                                            <span><?php echo esc_html($feature); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <?php if (isset($plugin['highlight'])) : ?>
                                <div class="plugin-highlight" style="background: linear-gradient(135deg, #faf9ff 0%, #f0f4ff 100%); color: #153045; padding: 16px; border-radius: 10px; margin-bottom: 20px; text-align: center; border: 2px solid #2c5282;">
                                    <p style="margin: 0; font-size: 14px; font-weight: 600; line-height: 1.4;">
                                        ⭐ <?php echo esc_html($plugin['highlight']); ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <div class="plugin-actions" style="margin-top: 20px;">
                                <a href="<?php echo esc_url($plugin['url']); ?>" target="_blank" rel="noopener"
                                    style="display: block; width: 100%; background: linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%); color: #ffffff; padding: 14px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(44, 82, 130, 0.3);"
                                    onmouseover="this.style.background='linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(44, 82, 130, 0.4)';"
                                    onmouseout="this.style.background='linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(44, 82, 130, 0.3)';">
                                    Get <?php echo esc_html($plugin['name']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="premium-footer">
                <p class="center-text">
                    <a href="https://wbcomdesigns.com/plugins/" target="_blank" rel="noopener"
                        style="display: inline-block; background: linear-gradient(135deg, rgb(29, 118, 218) 0%, rgb(60, 140, 230) 100%); color: rgb(255, 255, 255); padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: rgba(44, 82, 130, 0.3) 0px 4px 12px; transform: translateY(0px);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(44, 82, 130, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(44, 82, 130, 0.3)'">
                        <?php esc_html_e('Browse All Premium Plugins', 'wbcom-essential'); ?>
                        <span class="dashicons dashicons-external" style="vertical-align: middle; margin-left: 5px;"></span>
                    </a>
                </p>
            </div>
        </div>
    <?php
    }

    /**
     * Render themes tab
     */
    private function render_themes_tab()
    {
        $premium_themes = $this->get_premium_themes();

    ?>
        <div class="wbcom-themes-section">
            <div class="wbcom-themes-header">
                <h2><?php esc_html_e('Premium Community Themes', 'wbcom-essential'); ?></h2>
                <p><?php esc_html_e('Transform your vision with these powerful multi-purpose themes designed to create any type of community platform.', 'wbcom-essential'); ?></p>
            </div>

            <div class="premium-themes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 30px;">
                <?php foreach ($premium_themes as $theme) : ?>
                    <div class="premium-theme-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 16px; padding: 0; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); transition: all 0.3s ease; position: relative;">

                        <div class="theme-card-content" style="background: #fff; margin: 3px; border-radius: 13px; padding: 30px;">

                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                                <div style="flex: 1;">
                                    <h3 style="font-size: 22px; font-weight: 700; color: #153045; margin: 0 0 6px 0; line-height: 1.2;">
                                        <?php echo esc_html($theme['name']); ?>
                                    </h3>
                                    <?php if (isset($theme['tagline'])) : ?>
                                        <p class="theme-tagline" style="color: #515b67; font-weight: 400; font-size: 14px; margin: 0; line-height: 1.4;">
                                            <?php echo esc_html($theme['tagline']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div style="flex-shrink: 0; margin-left: 20px;">
                                    <span class="price-amount" style="font-size: 24px; font-weight: 800; color: #2c5282; background: linear-gradient(135deg, #faf9ff 0%, #f0f4ff 100%); padding: 8px 16px; border-radius: 8px; border: 2px solid #2c5282; display: inline-block; min-width: 80px; text-align: center;">
                                        <?php echo esc_html($theme['price']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="theme-description" style="margin-bottom: 24px;">
                                <p style="color: #5a6c7d; font-size: 15px; line-height: 1.6; margin: 0;">
                                    <?php echo esc_html($theme['description']); ?>
                                </p>
                            </div>

                            <div class="theme-features" style="margin-bottom: 24px;">
                                <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 8px;">
                                    <?php foreach ($theme['features'] as $feature) : ?>
                                        <li style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #153045; padding: 6px 0;">
                                            <span class="dashicons dashicons-yes" style="color: #2c5282; font-size: 16px; flex-shrink: 0;"></span>
                                            <span><?php echo esc_html($feature); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="theme-actions" style="display: flex; flex-direction: column; gap: 8px; margin-top: 20px;">
                                <a href="<?php echo esc_url($theme['url']); ?>" target="_blank" rel="noopener"
                                    style="width: 100%; background: linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%); color: white; padding: 14px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center; transition: all 0.3s ease; box-sizing: border-box;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(44, 82, 130, 0.4)';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                    View Theme
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="themes-footer">
                <p class="center-text">
                    <a href="https://wbcomdesigns.com/downloads/category/themes/" target="_blank" rel="noopener"
                        style="display: inline-block; background: linear-gradient(135deg, rgb(29, 118, 218) 0%, rgb(60, 140, 230) 100%); color: rgb(255, 255, 255); padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: rgba(44, 82, 130, 0.3) 0px 4px 12px; transform: translateY(0px);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(44, 82, 130, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(44, 82, 130, 0.3)'">
                        <?php esc_html_e('Browse All Premium Themes', 'wbcom-essential'); ?>
                        <span class="dashicons dashicons-external" style="vertical-align: middle; margin-left: 5px;"></span>
                    </a>
                </p>
            </div>
        </div>
    <?php
    }

    /**
     * Render news tab
     */
    private function render_news_tab()
    {
    ?>
        <div class="wbcom-news-section" style="background: #fff; border: 1px solid #e1e5e9; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
            <div class="wbcom-news-header" style="margin-bottom: 24px;">
                <h2 style="color: #153045; font-size: 24px; font-weight: 700; margin: 0 0 12px 0;"><?php esc_html_e('Latest News from Wbcom Designs', 'wbcom-essential'); ?></h2>
                <p style="color: #515b67; font-size: 15px; line-height: 1.6; margin: 0;"><?php esc_html_e('Stay updated with the latest plugin releases, updates, and WordPress community news.', 'wbcom-essential'); ?></p>
            </div>

            <div id="wbcom-news-feed" class="wbcom-news-feed" style="border: 1px solid #f0f0f1; border-radius: 8px; padding: 20px; background: #faf9ff;">
                <div class="news-loading" style="text-align: center; padding: 40px; color: #515b67;">
                    <span class="spinner is-active" style="margin-bottom: 15px;"></span>
                    <p style="margin: 0; font-size: 14px;"><?php esc_html_e('Loading latest news...', 'wbcom-essential'); ?></p>
                </div>
            </div>

            <div class="news-footer">
                <p class="center-text">
                    <a href="https://wbcomdesigns.com/blog/" target="_blank" rel="noopener"
                        style="display: inline-block; background: linear-gradient(135deg, rgb(29, 118, 218) 0%, rgb(60, 140, 230) 100%); color: rgb(255, 255, 255); padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s ease; box-shadow: rgba(44, 82, 130, 0.3) 0px 4px 12px; transform: translateY(0px);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(44, 82, 130, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(44, 82, 130, 0.3)'">
                        <?php esc_html_e('Visit Our Blog', 'wbcom-essential'); ?>
                        <span class="dashicons dashicons-external" style="vertical-align: middle; margin-left: 5px;"></span>
                    </a>
                </p>
            </div>
        </div>
    <?php
    }

    /**
     * Render sidebar widgets
     */
    private function render_sidebar_widgets()
    {
    ?>
        <!-- Care Plan Block -->
        <div class="wbcom-sidebar-widget wbcom-care-plan-widget">
            <div class="service-header">
                <h3>🛡️ WordPress Care Plan</h3>
                <div class="service-badge">ESSENTIAL</div>
            </div>

            <div class="service-pricing">
                <span class="price">$149</span>
                <span class="period">/month per site</span>
            </div>

            <div class="service-description">
                <p><strong>WordPress updates breaking your site?</strong> We test everything before deployment. <strong>Worried about security breaches?</strong> We monitor and protect 24/7. <strong>Site running slow?</strong> We optimize performance continuously.</p>
            </div>

            <div class="service-features">
                <ul>
                    <li><span class="dashicons dashicons-yes"></span> No More Broken Sites from Updates</li>
                    <li><span class="dashicons dashicons-yes"></span> Protected from Security Threats</li>
                    <li><span class="dashicons dashicons-yes"></span> Always Fast & Optimized Performance</li>
                    <li><span class="dashicons dashicons-yes"></span> Automatic Daily Backups</li>
                    <li><span class="dashicons dashicons-yes"></span> Expert Support When You Need It</li>
                    <li><span class="dashicons dashicons-yes"></span> Peace of Mind - Focus on Business</li>
                </ul>
            </div>

            <div class="service-actions">
                <a href="https://wbcomdesigns.com/downloads/wordpress-care-plans/" target="_blank" class="service-btn primary" style="margin-bottom: 8px;">
                    Get Care Plan
                </a>
                <a href="https://wbcomdesigns.com/start-a-project/" target="_blank" class="service-btn outline">
                    Schedule Free Call
                </a>
            </div>
        </div>

        <!-- Custom Development Block -->
        <div class="wbcom-sidebar-widget wbcom-development-widget">
            <div class="service-header">
                <h3>⚙️ Custom Development</h3>
                <div class="service-badge">PAY-AS-YOU-GO</div>
            </div>

            <div class="service-pricing">
                <span class="price">Flexible</span>
                <span class="period">Hours</span>
            </div>

            <div class="service-description">
                <p>Professional WordPress development services with flexible engagement model. Expert developers available for custom projects with transparent pricing and no hidden costs.</p>
            </div>

            <div class="service-features">
                <ul>
                    <li><span class="dashicons dashicons-yes"></span> Fully Customizable Project Scope</li>
                    <li><span class="dashicons dashicons-yes"></span> Flexible Development Hours</li>
                    <li><span class="dashicons dashicons-yes"></span> Transparent Pay-Per-Need Pricing</li>
                    <li><span class="dashicons dashicons-yes"></span> No Hidden Costs or Surprises</li>
                    <li><span class="dashicons dashicons-yes"></span> Direct Developer Consultation</li>
                    <li><span class="dashicons dashicons-yes"></span> Specialized Custom Solutions</li>
                </ul>
            </div>

            <div class="service-actions">
                <a href="https://wbcomdesigns.com/start-a-project/" target="_blank" class="service-btn primary" style="margin-bottom: 8px;">
                    Start Project
                </a>
                <a href="https://wbcomdesigns.com/support/" target="_blank" class="service-btn outline">
                    Book Consultation
                </a>
            </div>
        </div>

        <style>
            /* Sidebar Service Blocks */
            .wbcom-sidebar-widget {
                background: #fff;
                border-radius: 6px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border: 1px solid #c3c4c7;
                transition: all 0.2s ease;
            }

            .wbcom-sidebar-widget:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transform: translateY(-2px);
                border-color: #2c5282;
            }

            .service-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f1;
            }

            .service-header h3 {
                margin: 0;
                font-size: 20px;
                font-weight: 600;
                color: #153045;
                line-height: 1.3;
                flex: 1;
            }

            .service-badge {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                padding: 3px 8px;
                border-radius: 12px;
                letter-spacing: 0.5px;
                background: linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%);
                color: #ffffff;
                border: 1px solid #2c5282;
                flex-shrink: 0;
                margin-left: 15px;
            }

            .service-pricing {
                margin-bottom: 20px;
                text-align: center;
                padding: 15px;
                background: linear-gradient(135deg, #faf9ff 0%, #f0f4ff 100%);
                border-radius: 8px;
                border: 2px solid #2c5282;
            }

            .service-pricing .price {
                font-size: 24px;
                font-weight: 700;
                color: #2c5282;
                display: block;
                min-width: 80px;
            }

            .service-pricing .period {
                font-size: 13px;
                color: #646970;
                font-weight: 500;
            }

            .service-description {
                margin-bottom: 20px;
            }

            .service-description p {
                margin: 0;
                color: #515b67;
                line-height: 1.6;
                font-size: 14px;
            }

            .service-features ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .service-features li {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                color: #153045;
                padding: 4px 0;
            }

            .service-actions {
                display: flex;
                flex-direction: column;
                gap: 8px;
                padding-top: 15px;
                border-top: 1px solid #f0f0f1;
                margin-top: 20px;
            }

            .service-btn {
                display: block;
                width: 100% !important;
                text-decoration: none;
                font-weight: 500;
                padding: 12px 16px;
                border-radius: 6px;
                font-size: 14px;
                border: 1px solid;
                cursor: pointer;
                transition: all 0.2s ease;
                text-align: center;
                box-sizing: border-box;
            }

            .service-btn.primary {
                background: linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%);
                border-color: #2c5282;
                color: #fff;
            }

            .service-btn.primary:hover {
                background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
                border-color: #1e3a5f;
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(44, 82, 130, 0.3);
            }

            .service-btn.secondary {
                background: linear-gradient(135deg, #2c5282 0%, #4a6fa1 100%);
                border-color: #2c5282;
                color: #fff;
            }

            .service-btn.secondary:hover {
                background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
                border-color: #1e3a5f;
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(44, 82, 130, 0.3);
            }

            .service-btn.outline {
                background: #fff;
                border-color: #c3c4c7;
                color: #646970;
            }

            .service-btn.outline:hover {
                background: #f6f7f7;
                border-color: #8c8f94;
                color: #23282d;
            }

            @media (max-width: 782px) {
                .wbcom-sidebar-widget {
                    padding: 15px;
                    margin-bottom: 15px;
                }

                .service-header h3 {
                    font-size: 18px;
                }

                .service-pricing .price {
                    font-size: 20px;
                }

                .service-actions {
                    flex-direction: column;
                }

                .service-btn {
                    width: 100%;
                }
            }
        </style>
        <?php
    }

    /**
     * Helper methods
     */
    private function get_dashboard_stats()
    {
        return array(
            'total_plugins'  => count($this->registered_plugins),
            'active_plugins' => count($this->get_active_plugins()),
            'wp_version'     => get_bloginfo('version'),
            'bp_version'     => function_exists('buddypress') ? buddypress()->version : __('Not Active', 'wbcom-essential'),
        );
    }

    private function get_active_plugins()
    {
        return array_filter($this->registered_plugins, function ($plugin) {
            return $plugin['status'] === 'active';
        });
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

    private function render_admin_notices()
    {
        $active_count = count($this->get_active_plugins());

        if ($active_count === 0) {
        ?>
            <div class="notice notice-warning">
                <p>
                    <strong><?php esc_html_e('Welcome to Wbcom Designs!', 'wbcom-essential'); ?></strong>
                    <?php esc_html_e('No Wbcom plugins are currently active. Activate plugins to see them here.', 'wbcom-essential'); ?>
                </p>
            </div>
<?php
        }
    }

    private function get_premium_plugins()
    {
        return array(
            array(
                'name'        => 'Community Bundle',
                'tagline'     => 'Complete BuddyPress Community Solution - 25+ Plugins',
                'description' => 'Complete BuddyPress community solution with over 25 essential plugins for building a thriving online community.',
                'price'       => '$249',
                'url'         => 'https://wbcomdesigns.com/downloads/buddypress-community-bundle/',
                'features'    => array(
                    'All BuddyPress premium plugins included',
                    'Activity feeds enhancement',
                    'Advanced member management',
                    'Community engagement tools',
                    'Professional support included',
                    'Regular updates and new features'
                ),
            ),
            array(
                'name'        => 'Woo Sell Services',
                'tagline'     => 'Service Booking & Management Platform',
                'description' => 'Transform your WooCommerce store to sell services with booking, appointments, and service management features.',
                'price'       => '$59',
                'url'         => 'https://wbcomdesigns.com/downloads/woo-sell-services/',
                'demo_url'    => 'https://app.instawp.io/launch?t=woo-sell-services&d=v1',
                'features'    => array(
                    'Service booking and appointments',
                    'Staff and resource management',
                    'Service packages and pricing',
                    'Calendar integration',
                    'Customer booking management',
                    'Payment and invoice handling'
                ),
            ),
            array(
                'name'        => 'LearnDash Dashboard',
                'tagline'     => 'Advanced Learning Analytics & Management',
                'description' => 'Advanced dashboard for LearnDash with comprehensive analytics, reporting, and student management tools.',
                'price'       => '$79',
                'url'         => 'https://wbcomdesigns.com/downloads/learndash-dashboard/',
                'demo_url'    => 'https://app.instawp.io/launch?t=learndash-dashboard&d=v1',
                'features'    => array(
                    'Advanced course analytics',
                    'Student progress tracking',
                    'Custom reporting system',
                    'Instructor dashboard',
                    'Revenue and enrollment insights',
                    'Export and data visualization'
                ),
            ),
        );
    }

    private function get_premium_themes()
    {
        return array(
            array(
                'name'        => 'Reign Bundle',
                'tagline'     => 'Reign Theme + All Reign Addons',
                'description' => 'The ultimate all-in-one package. Get Reign theme plus all premium addons for building any type of community - social networks, learning platforms, marketplaces, or directories.',
                'price'       => '$179',
                'url'         => 'https://wbcomdesigns.com/downloads/reign-addons-bundle/',
                'features'    => array(
                    'Complete multi-purpose solution for any community type',
                    'Works seamlessly with BuddyPress & BuddyBoss',
                    'Built-in monetization & membership capabilities',
                    'Professional templates for every industry',
                    'Advanced branding & white-label options',
                    'Priority support with lifetime updates'
                ),
            ),
            array(
                'name'        => 'Reign Theme',
                'tagline'     => 'Multi-Purpose Community Powerhouse',
                'description' => 'One theme, unlimited possibilities. Transform your site into any type of community - social networks, learning platforms, marketplaces, or professional directories with BuddyPress & BuddyBoss compatibility.',
                'price'       => '$99',
                'url'         => 'https://wbcomdesigns.com/downloads/reign-buddypress-theme/',
                'features'    => array(
                    'Multi-platform support (BuddyPress, BuddyBoss, PeepSo)',
                    'Transform into social network, LMS, or marketplace',
                    'Advanced customization without coding',
                    'Mobile-first responsive design',
                    'Built-in SEO optimization & performance',
                    'Integrates with all major plugins'
                ),
            ),
            array(
                'name'        => 'BuddyX Pro',
                'tagline'     => 'Trusted by 6000+ Successful Communities',
                'description' => 'Join thousands of thriving communities worldwide. Create Facebook-like social experiences, integrate learning platforms, build marketplaces, or launch membership sites with complete customization.',
                'price'       => '$79',
                'url'         => 'https://wbcomdesigns.com/downloads/buddyx-pro-theme/',
                'features'    => array(
                    'Facebook-style social networking experience',
                    'Multi-LMS support (LearnDash, LearnPress, LifterLMS)',
                    'WooCommerce multi-vendor marketplace ready',
                    'Membership & subscription monetization',
                    'Elementor page builder integration',
                    'Dark/light modes with custom branding'
                ),
            ),
            array(
                'name'        => 'BuddyX Free',
                'tagline'     => 'Professional Community Foundation',
                'description' => 'Start building your community with our powerful free foundation. Perfect for testing and small communities, with a clear upgrade path to Pro features when ready to scale.',
                'price'       => 'Free',
                'url'         => 'https://wbcomdesigns.com/downloads/buddyx-theme/',
                'features'    => array(
                    'Complete community foundation at zero cost',
                    'Modern, mobile-responsive design',
                    'Essential social networking features',
                    'Compatible with popular plugins',
                    'Upgrade path to Pro when ready',
                    'Active community support'
                ),
            ),
        );
    }
}
