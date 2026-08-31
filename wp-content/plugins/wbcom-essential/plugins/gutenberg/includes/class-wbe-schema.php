<?php
/**
 * WBE_Schema -- Structured data (JSON-LD) collector for blocks.
 *
 * @package WBcom_Essential
 */

namespace WBCOM_ESSENTIAL\Gutenberg;

defined( 'ABSPATH' ) || exit;

class WBE_Schema {

	/**
	 * Collected schema items.
	 *
	 * @var array
	 */
	private static $items = array();

	/**
	 * Add a FAQ item (question + answer).
	 *
	 * @param string $question The question text.
	 * @param string $answer   The answer text/HTML.
	 */
	public static function add_faq( $question, $answer ) {
		if ( ! isset( self::$items['faq'] ) ) {
			self::$items['faq'] = array();
		}

		self::$items['faq'][] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $question ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_kses_post( $answer ),
			),
		);
	}

	/**
	 * Add a HowTo step.
	 *
	 * @param string $name Step name.
	 * @param string $text Step description.
	 */
	public static function add_howto_step( $name, $text ) {
		if ( ! isset( self::$items['howto'] ) ) {
			self::$items['howto'] = array();
		}

		self::$items['howto'][] = array(
			'@type' => 'HowToStep',
			'name'  => wp_strip_all_tags( $name ),
			'text'  => wp_kses_post( $text ),
		);
	}

	/**
	 * Output JSON-LD in the footer.
	 */
	public static function output() {
		if ( empty( self::$items ) ) {
			return;
		}

		$schemas = array();

		// FAQ schema.
		if ( ! empty( self::$items['faq'] ) ) {
			$schemas[] = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => self::$items['faq'],
			);
		}

		// HowTo schema.
		if ( ! empty( self::$items['howto'] ) ) {
			$schemas[] = array(
				'@context' => 'https://schema.org',
				'@type'    => 'HowTo',
				'step'     => self::$items['howto'],
			);
		}

		foreach ( $schemas as $schema ) {
			echo '<script type="application/ld+json">';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD is built from sanitized data.
			echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			echo "</script>\n";
		}
	}

	/**
	 * Initialize -- hook output to footer.
	 */
	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'output' ), 20 );
	}
}
