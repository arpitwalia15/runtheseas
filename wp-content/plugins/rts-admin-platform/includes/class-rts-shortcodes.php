<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class RTS_Shortcodes {

	public static function init() {
		add_shortcode( 'rts_survey', array( __CLASS__, 'render_survey' ) );
		add_shortcode( 'rts_unsubscribe', array( __CLASS__, 'render_unsubscribe' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	public static function enqueue_assets() {
		wp_register_script( 'rts-survey', RTSAP_PLUGIN_URL . 'assets/survey.js', array(), '1.9.0', true );
		wp_localize_script( 'rts-survey', 'rtsConfig', array(
			'apiUrl' => rest_url( 'rts/v1' ),
			'surveyId' => 1,
			'emailMode' => RTS_Production::get( 'email_mode' ),
		) );
		wp_register_style( 'rts-survey-style', RTSAP_PLUGIN_URL . 'assets/survey.css', array(), '1.9.0' );
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
