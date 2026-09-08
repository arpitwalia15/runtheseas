<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Shortcodes {

	public static function init() {
		add_shortcode( 'rts_survey', array( __CLASS__, 'render_survey' ) );
		add_shortcode( 'rts_unsubscribe', array( __CLASS__, 'render_unsubscribe' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_assets' ) );
		add_action( 'admin_notices', array( __CLASS__, 'render_admin_brand_bar' ), 1 );
		add_filter( 'admin_body_class', array( __CLASS__, 'admin_body_class' ) );
	}

	public static function enqueue_assets() {
		wp_register_script( 'rts-survey', RTSAP_PLUGIN_URL . 'assets/survey.js', array(), RTSAP_VERSION, true );
		wp_localize_script( 'rts-survey', 'rtsConfig', array(
			'apiUrl' => rest_url( 'rts/v1' ),
			'surveyId' => 1,
			'emailMode' => RTS_Production::get( 'email_mode' ),
		) );
		wp_register_style( 'rts-survey-style', RTSAP_PLUGIN_URL . 'assets/survey.css', array(), RTSAP_VERSION );
	}

	private static function is_admin_platform_screen() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		return 'rts-admin' === $page || str_starts_with( $page, 'rts-' );
	}

	public static function admin_enqueue_assets() {
		if ( ! self::is_admin_platform_screen() ) {
			return;
		}

		wp_enqueue_style( 'rts-admin-platform', RTSAP_PLUGIN_URL . 'assets/admin-survey.css', array(), RTSAP_VERSION );
	}

	public static function admin_body_class( $classes ) {
		return self::is_admin_platform_screen() ? $classes . ' rtsap-admin-page' : $classes;
	}

	/** Add the always-visible branded context bar from the approved wireframe. */
	public static function render_admin_brand_bar() {
		if ( ! self::is_admin_platform_screen() ) {
			return;
		}

		$backup_url = add_query_arg( 'page', 'rts-backup', admin_url( 'admin.php' ) );
		echo '<div class="rtsap-brand-bar" role="banner">'
			. '<div class="rtsap-brand-bar__identity"><span class="rtsap-brand-bar__mark" aria-hidden="true">⚓</span>'
			. '<span><b>' . esc_html__( 'RUN THE SEAS — Admin Platform', 'run-the-seas' ) . '</b>'
			. '<small>' . esc_html__( 'Live operations, insights and administration', 'run-the-seas' ) . '</small></span></div>'
			. '<a class="rtsap-brand-bar__control" href="' . esc_url( $backup_url ) . '">'
			. esc_html__( 'System & Site Controls', 'run-the-seas' ) . '</a></div>';
	}

	public static function render_survey() {
		wp_enqueue_script( 'rts-survey' );
		wp_enqueue_style( 'rts-survey-style' );
		return '<div id="rts-survey-app">Loading survey…</div>';
	}

	public static function render_unsubscribe() {
		wp_enqueue_script( 'rts-survey' );
		wp_enqueue_style( 'rts-survey-style' );
		return '<div id="rts-unsubscribe-app">Loading…</div>';
	}
}
