<?php
/**
 * Wbcom Essential Admin Page
 *
 * @package WBCOM_Essential
 */

namespace WBCOM_ESSENTIAL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin page class
 */
class Wbcom_Essential_Widget_Showcase {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		if ( ! $this->should_use_wrapper() ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * Check if we should use the Wbcom wrapper
	 */
	private function should_use_wrapper() {
		if ( class_exists( 'Wbcom_Shared_Loader' ) ) {
			return true;
		}

		$shared_loader_file = WBCOM_ESSENTIAL_PATH . '/includes/shared-admin/class-wbcom-shared-loader.php';
		if ( file_exists( $shared_loader_file ) ) {
			require_once $shared_loader_file;
			if ( class_exists( 'Wbcom_Shared_Loader' ) ) {
				return true;
			}
		}

		if ( function_exists( 'wbcom_integrate_plugin' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Static method to render admin page (for wrapper callback)
	 */
	public static function render_admin_page() {
		$instance = new self();
		$instance->render_page();
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			esc_html__( 'Wbcom Designs', 'wbcom-essential' ),
			esc_html__( 'Wbcom Designs', 'wbcom-essential' ),
			'manage_options',
			'wbcom-designs',
			array( $this, 'render_page' ),
			$this->get_menu_icon(),
			58.5
		);

		add_submenu_page(
			'wbcom-designs',
			esc_html__( 'Essential', 'wbcom-essential' ),
			esc_html__( 'Essential', 'wbcom-essential' ),
			'manage_options',
			'wbcom-essential',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Get menu icon
	 */
	private function get_menu_icon() {
		$svg = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M10 2L13.09 8.26L20 9L14 12L15 20L10 17L5 20L6 12L0 9L6.91 8.26L10 2Z" fill="#a7aaad"/>
		</svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook The current admin page hook suffix.
	 */
	public function enqueue_admin_styles( $hook ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( 'wbcom-essential' !== $current_page && 'wbcom-designs' !== $current_page ) {
			return;
		}

		// Shared tab styles.
		$shared_tabs_css = WBCOM_ESSENTIAL_PATH . 'includes/shared-admin/wbcom-shared-tabs.css';
		if ( file_exists( $shared_tabs_css ) ) {
			wp_enqueue_style(
				'wbcom-shared-tabs',
				WBCOM_ESSENTIAL_URL . 'includes/shared-admin/wbcom-shared-tabs.css',
				array(),
				WBCOM_ESSENTIAL_VERSION
			);
		}

		wp_enqueue_style(
			'wbcom-essential-admin',
			plugin_dir_url( __FILE__ ) . 'css/admin.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);
	}

	/**
	 * Get the current admin tab.
	 *
	 * @return string
	 */
	private function get_current_tab() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'overview';
	}

	/**
	 * Render the admin page
	 */
	public function render_page() {
		$stats       = $this->get_stats();
		$current_tab = $this->get_current_tab();
		$base_url    = admin_url( 'admin.php?page=wbcom-essential' );
		?>
		<div class="wbcom-essential-wrap">
			<!-- Header -->
			<header class="wbcom-header">
				<div class="wbcom-header-content">
					<h1><?php esc_html_e( 'Wbcom Essential', 'wbcom-essential' ); ?></h1>
					<span class="wbcom-version">v<?php echo esc_html( WBCOM_ESSENTIAL_VERSION ); ?></span>
				</div>
				<p class="wbcom-tagline"><?php esc_html_e( 'Free companion plugin for BuddyX, BuddyX Pro, and Reign themes', 'wbcom-essential' ); ?></p>
			</header>

			<!-- Tabs -->
			<div class="wbcom-tab-wrapper" style="grid-column: 1 / -1;">
				<div class="wbcom-nav-tab-wrapper">
					<a href="<?php echo esc_url( $base_url ); ?>" class="wbcom-nav-tab <?php echo 'overview' === $current_tab ? 'nav-tab-active' : ''; ?>">
						<span class="dashicons dashicons-screenoptions"></span>
						<?php esc_html_e( 'Overview', 'wbcom-essential' ); ?>
					</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings', $base_url ) ); ?>" class="wbcom-nav-tab <?php echo 'settings' === $current_tab ? 'nav-tab-active' : ''; ?>">
						<span class="dashicons dashicons-admin-generic"></span>
						<?php esc_html_e( 'Settings', 'wbcom-essential' ); ?>
					</a>
				</div>

				<div class="wbcom-tab-content">
					<?php if ( 'overview' === $current_tab ) : ?>
						<?php $this->render_overview_tab( $stats ); ?>
					<?php elseif ( 'settings' === $current_tab ) : ?>
						<?php $this->render_settings_tab(); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Overview tab content.
	 *
	 * @param array $stats Plugin stats.
	 */
	private function render_overview_tab( $stats ) {
		?>
		<!-- Stats Cards -->
		<div class="wbcom-stats">
			<div class="wbcom-stat-card wbcom-stat-blocks">
				<span class="wbcom-stat-icon dashicons dashicons-block-default"></span>
				<div class="wbcom-stat-info">
					<span class="wbcom-stat-number"><?php echo esc_html( $stats['blocks'] ); ?></span>
					<span class="wbcom-stat-label"><?php esc_html_e( 'Gutenberg Blocks', 'wbcom-essential' ); ?></span>
				</div>
			</div>
			<div class="wbcom-stat-card wbcom-stat-widgets">
				<span class="wbcom-stat-icon dashicons dashicons-admin-customizer"></span>
				<div class="wbcom-stat-info">
					<span class="wbcom-stat-number"><?php echo esc_html( $stats['widgets'] ); ?></span>
					<span class="wbcom-stat-label"><?php esc_html_e( 'Elementor Widgets', 'wbcom-essential' ); ?></span>
				</div>
			</div>
			<div class="wbcom-stat-card wbcom-stat-price">
				<span class="wbcom-stat-icon dashicons dashicons-heart"></span>
				<div class="wbcom-stat-info">
					<span class="wbcom-stat-number">$0</span>
					<span class="wbcom-stat-label"><?php esc_html_e( 'Free Forever', 'wbcom-essential' ); ?></span>
				</div>
			</div>
		</div>

		<!-- What's Included -->
		<div class="wbcom-features" style="margin-top: 24px;">
			<div class="wbcom-feature">
				<span class="dashicons dashicons-block-default"></span>
				<h3><?php esc_html_e( 'Gutenberg Blocks', 'wbcom-essential' ); ?></h3>
				<p><?php esc_html_e( 'Works natively with the WordPress Block Editor. No page builder required.', 'wbcom-essential' ); ?></p>
				<ul>
					<li><?php esc_html_e( '17 Marketing, Content & Design blocks', 'wbcom-essential' ); ?></li>
					<li><?php esc_html_e( '5 BuddyPress blocks (Members, Groups, Activity)', 'wbcom-essential' ); ?></li>
					<li><?php esc_html_e( '7 Blog & WooCommerce blocks', 'wbcom-essential' ); ?></li>
					<li><?php esc_html_e( '3 EDD blocks (Account, Checkout, Catalog)', 'wbcom-essential' ); ?></li>
				</ul>
			</div>
			<div class="wbcom-feature">
				<span class="dashicons dashicons-admin-customizer"></span>
				<h3><?php esc_html_e( 'Elementor Widgets', 'wbcom-essential' ); ?></h3>
				<p><?php esc_html_e( 'Premium widgets for Elementor with advanced styling options.', 'wbcom-essential' ); ?></p>
				<ul>
					<li><?php esc_html_e( '27 General widgets', 'wbcom-essential' ); ?></li>
					<li><?php esc_html_e( '11 BuddyPress widgets', 'wbcom-essential' ); ?></li>
					<li><?php esc_html_e( '5 WooCommerce widgets', 'wbcom-essential' ); ?></li>
				</ul>
			</div>
		</div>

		<!-- Requirements -->
		<div class="wbcom-req-grid" style="margin-top: 24px;">
			<div class="wbcom-req-item">
				<span class="dashicons dashicons-yes-alt"></span>
				<span><?php esc_html_e( 'WordPress 6.0+', 'wbcom-essential' ); ?></span>
			</div>
			<div class="wbcom-req-item">
				<span class="dashicons dashicons-yes-alt"></span>
				<span><?php esc_html_e( 'PHP 8.0+', 'wbcom-essential' ); ?></span>
			</div>
			<div class="wbcom-req-item wbcom-req-optional">
				<span class="dashicons dashicons-info"></span>
				<span><?php esc_html_e( 'Elementor (optional, for widgets)', 'wbcom-essential' ); ?></span>
			</div>
			<div class="wbcom-req-item wbcom-req-optional">
				<span class="dashicons dashicons-info"></span>
				<span><?php esc_html_e( 'BuddyPress (for community blocks)', 'wbcom-essential' ); ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Settings tab content.
	 */
	private function render_settings_tab() {
		?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'wbcom_essential_settings' );
			do_settings_sections( 'wbcom-essential' );
			submit_button();
			?>
		</form>
		<?php
	}

	/**
	 * Get plugin stats
	 */
	private function get_stats() {
		return array(
			'blocks'  => 32,
			'widgets' => 43,
		);
	}
}
