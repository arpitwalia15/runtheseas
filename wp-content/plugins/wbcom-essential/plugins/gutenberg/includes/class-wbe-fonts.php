<?php
/**
 * WBE_Fonts -- Google Fonts enqueue for blocks.
 *
 * @package WBcom_Essential
 */

namespace WBCOM_ESSENTIAL\Gutenberg;

defined( 'ABSPATH' ) || exit;

class WBE_Fonts {

	/**
	 * Collected font families.
	 *
	 * @var array Family => weights array.
	 */
	private static $fonts = array();

	/**
	 * Register a font family + weight for enqueue.
	 *
	 * @param string $family Font family name (e.g., "Inter").
	 * @param string $weight Font weight (e.g., "400", "700"). Default "400".
	 */
	public static function add( $family, $weight = '400' ) {
		if ( empty( $family ) ) {
			return;
		}

		// Skip system fonts.
		$system_fonts = array( 'system-ui', 'inherit', 'sans-serif', 'serif', 'monospace' );
		$family_lower = strtolower( trim( $family ) );
		foreach ( $system_fonts as $sys ) {
			if ( false !== strpos( $family_lower, $sys ) ) {
				return;
			}
		}

		// Clean the family name (remove quotes and fallbacks).
		$clean = preg_replace( "/['\"]|,.*$/", '', $family );
		$clean = trim( $clean );

		if ( empty( $clean ) ) {
			return;
		}

		if ( ! isset( self::$fonts[ $clean ] ) ) {
			self::$fonts[ $clean ] = array();
		}

		self::$fonts[ $clean ][ $weight ] = true;
	}

	/**
	 * Enqueue collected Google Fonts.
	 */
	public static function enqueue() {
		if ( empty( self::$fonts ) ) {
			return;
		}

		$families = array();
		foreach ( self::$fonts as $family => $weights ) {
			$weight_list = implode( ';', array_keys( $weights ) );
			$families[]  = 'family=' . rawurlencode( $family ) . ':wght@' . $weight_list;
		}

		$url = 'https://fonts.googleapis.com/css2?' . implode( '&', $families ) . '&display=swap';

		wp_enqueue_style(
			'wbe-google-fonts',
			$url,
			array(),
			null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- External resource.
		);
	}

	/**
	 * Initialize -- hook enqueue to wp_enqueue_scripts.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ), 99 );
	}
}
