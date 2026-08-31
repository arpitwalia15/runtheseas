<?php
/**
 * WBE_CSS -- Server-side CSS generation for dynamic blocks.
 *
 * @package WBcom_Essential
 */

namespace WBCOM_ESSENTIAL\Gutenberg;

defined( 'ABSPATH' ) || exit;

class WBE_CSS {

	/**
	 * Collected CSS rules per page load.
	 *
	 * @var array
	 */
	private static $styles = array();

	/**
	 * Generate scoped CSS for a block instance.
	 *
	 * @param string $unique_id Block unique ID.
	 * @param array  $attrs     Block attributes.
	 * @return string CSS string.
	 */
	public static function generate( $unique_id, $attrs ) {
		if ( empty( $unique_id ) ) {
			return '';
		}

		$selector = '.wbe-block-' . sanitize_html_class( $unique_id );
		$desktop  = array();
		$tablet   = array();
		$mobile   = array();

		// Padding.
		if ( ! empty( $attrs['padding'] ) && is_array( $attrs['padding'] ) ) {
			$unit      = ! empty( $attrs['paddingUnit'] ) ? $attrs['paddingUnit'] : 'px';
			$p         = $attrs['padding'];
			$desktop[] = sprintf(
				'padding: %s%s %s%s %s%s %s%s;',
				absint( $p['top'] ?? 0 ), $unit,
				absint( $p['right'] ?? 0 ), $unit,
				absint( $p['bottom'] ?? 0 ), $unit,
				absint( $p['left'] ?? 0 ), $unit
			);
		}

		if ( ! empty( $attrs['paddingTablet'] ) && is_array( $attrs['paddingTablet'] ) ) {
			$unit     = ! empty( $attrs['paddingUnit'] ) ? $attrs['paddingUnit'] : 'px';
			$p        = $attrs['paddingTablet'];
			$tablet[] = sprintf(
				'padding: %s%s %s%s %s%s %s%s;',
				absint( $p['top'] ?? 0 ), $unit,
				absint( $p['right'] ?? 0 ), $unit,
				absint( $p['bottom'] ?? 0 ), $unit,
				absint( $p['left'] ?? 0 ), $unit
			);
		}

		if ( ! empty( $attrs['paddingMobile'] ) && is_array( $attrs['paddingMobile'] ) ) {
			$unit     = ! empty( $attrs['paddingUnit'] ) ? $attrs['paddingUnit'] : 'px';
			$p        = $attrs['paddingMobile'];
			$mobile[] = sprintf(
				'padding: %s%s %s%s %s%s %s%s;',
				absint( $p['top'] ?? 0 ), $unit,
				absint( $p['right'] ?? 0 ), $unit,
				absint( $p['bottom'] ?? 0 ), $unit,
				absint( $p['left'] ?? 0 ), $unit
			);
		}

		// Margin.
		if ( ! empty( $attrs['margin'] ) && is_array( $attrs['margin'] ) ) {
			$unit      = ! empty( $attrs['marginUnit'] ) ? $attrs['marginUnit'] : 'px';
			$m         = $attrs['margin'];
			$desktop[] = sprintf(
				'margin: %s%s %s%s %s%s %s%s;',
				intval( $m['top'] ?? 0 ), $unit,
				intval( $m['right'] ?? 0 ), $unit,
				intval( $m['bottom'] ?? 0 ), $unit,
				intval( $m['left'] ?? 0 ), $unit
			);
		}

		if ( ! empty( $attrs['marginTablet'] ) && is_array( $attrs['marginTablet'] ) ) {
			$unit     = ! empty( $attrs['marginUnit'] ) ? $attrs['marginUnit'] : 'px';
			$m        = $attrs['marginTablet'];
			$tablet[] = sprintf(
				'margin: %s%s %s%s %s%s %s%s;',
				intval( $m['top'] ?? 0 ), $unit,
				intval( $m['right'] ?? 0 ), $unit,
				intval( $m['bottom'] ?? 0 ), $unit,
				intval( $m['left'] ?? 0 ), $unit
			);
		}

		if ( ! empty( $attrs['marginMobile'] ) && is_array( $attrs['marginMobile'] ) ) {
			$unit     = ! empty( $attrs['marginUnit'] ) ? $attrs['marginUnit'] : 'px';
			$m        = $attrs['marginMobile'];
			$mobile[] = sprintf(
				'margin: %s%s %s%s %s%s %s%s;',
				intval( $m['top'] ?? 0 ), $unit,
				intval( $m['right'] ?? 0 ), $unit,
				intval( $m['bottom'] ?? 0 ), $unit,
				intval( $m['left'] ?? 0 ), $unit
			);
		}

		// Border radius.
		if ( ! empty( $attrs['borderRadius'] ) && is_array( $attrs['borderRadius'] ) ) {
			$unit      = ! empty( $attrs['borderRadiusUnit'] ) ? $attrs['borderRadiusUnit'] : 'px';
			$r         = $attrs['borderRadius'];
			$desktop[] = sprintf(
				'border-radius: %s%s %s%s %s%s %s%s;',
				absint( $r['top'] ?? 0 ), $unit,
				absint( $r['right'] ?? 0 ), $unit,
				absint( $r['bottom'] ?? 0 ), $unit,
				absint( $r['left'] ?? 0 ), $unit
			);
		}

		// Box shadow.
		if ( ! empty( $attrs['boxShadow'] ) ) {
			$h = intval( $attrs['shadowHorizontal'] ?? 0 );
			$v = intval( $attrs['shadowVertical'] ?? 4 );
			$b = absint( $attrs['shadowBlur'] ?? 8 );
			$s = intval( $attrs['shadowSpread'] ?? 0 );
			$c = sanitize_text_field( $attrs['shadowColor'] ?? 'rgba(0,0,0,0.12)' );

			$desktop[] = sprintf( 'box-shadow: %dpx %dpx %dpx %dpx %s;', $h, $v, $b, $s, $c );
		}

		// Font size (responsive).
		if ( isset( $attrs['fontSize'] ) && '' !== $attrs['fontSize'] ) {
			$unit      = ! empty( $attrs['fontSizeUnit'] ) ? $attrs['fontSizeUnit'] : 'px';
			$desktop[] = sprintf( 'font-size: %s%s;', floatval( $attrs['fontSize'] ), $unit );
		}
		if ( isset( $attrs['fontSizeTablet'] ) && '' !== $attrs['fontSizeTablet'] ) {
			$unit     = ! empty( $attrs['fontSizeUnit'] ) ? $attrs['fontSizeUnit'] : 'px';
			$tablet[] = sprintf( 'font-size: %s%s;', floatval( $attrs['fontSizeTablet'] ), $unit );
		}
		if ( isset( $attrs['fontSizeMobile'] ) && '' !== $attrs['fontSizeMobile'] ) {
			$unit     = ! empty( $attrs['fontSizeUnit'] ) ? $attrs['fontSizeUnit'] : 'px';
			$mobile[] = sprintf( 'font-size: %s%s;', floatval( $attrs['fontSizeMobile'] ), $unit );
		}

		// Font family.
		if ( ! empty( $attrs['fontFamily'] ) ) {
			$desktop[] = sprintf( 'font-family: %s;', sanitize_text_field( $attrs['fontFamily'] ) );
		}

		// Font weight.
		if ( ! empty( $attrs['fontWeight'] ) ) {
			$desktop[] = sprintf( 'font-weight: %s;', sanitize_text_field( $attrs['fontWeight'] ) );
		}

		// Line height.
		if ( isset( $attrs['lineHeight'] ) && '' !== $attrs['lineHeight'] ) {
			$unit      = ! empty( $attrs['lineHeightUnit'] ) ? $attrs['lineHeightUnit'] : '';
			$desktop[] = sprintf( 'line-height: %s%s;', floatval( $attrs['lineHeight'] ), $unit );
		}

		// Letter spacing.
		if ( isset( $attrs['letterSpacing'] ) && '' !== $attrs['letterSpacing'] ) {
			$desktop[] = sprintf( 'letter-spacing: %spx;', floatval( $attrs['letterSpacing'] ) );
		}

		// Text transform.
		if ( ! empty( $attrs['textTransform'] ) ) {
			$desktop[] = sprintf( 'text-transform: %s;', sanitize_text_field( $attrs['textTransform'] ) );
		}

		// Build CSS.
		$css = '';

		if ( ! empty( $desktop ) ) {
			$css .= $selector . " {\n  " . implode( "\n  ", $desktop ) . "\n}\n";
		}

		if ( ! empty( $tablet ) ) {
			$css .= "@media (max-width: 1024px) {\n  " . $selector . " {\n    " . implode( "\n    ", $tablet ) . "\n  }\n}\n";
		}

		if ( ! empty( $mobile ) ) {
			$css .= "@media (max-width: 767px) {\n  " . $selector . " {\n    " . implode( "\n    ", $mobile ) . "\n  }\n}\n";
		}

		return $css;
	}

	/**
	 * Add block CSS to the page collection.
	 *
	 * @param string $unique_id Block unique ID.
	 * @param array  $attrs     Block attributes.
	 */
	public static function add( $unique_id, $attrs ) {
		$css = self::generate( $unique_id, $attrs );
		if ( $css ) {
			self::$styles[ $unique_id ] = $css;
		}
	}

	/**
	 * Output all collected CSS in the footer.
	 */
	public static function output() {
		if ( empty( self::$styles ) ) {
			return;
		}

		echo '<style id="wbe-block-styles">' . "\n";
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS is generated internally.
		echo implode( "\n", self::$styles );
		echo "\n</style>\n";
	}

	/**
	 * Get visibility CSS classes for a block.
	 *
	 * @param array $attrs Block attributes.
	 * @return string CSS classes string.
	 */
	public static function get_visibility_classes( $attrs ) {
		$classes = array();

		if ( ! empty( $attrs['hideOnDesktop'] ) ) {
			$classes[] = 'wbe-hide-desktop';
		}
		if ( ! empty( $attrs['hideOnTablet'] ) ) {
			$classes[] = 'wbe-hide-tablet';
		}
		if ( ! empty( $attrs['hideOnMobile'] ) ) {
			$classes[] = 'wbe-hide-mobile';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Initialize -- hook CSS output to footer.
	 */
	public static function init() {
		add_action( 'wp_footer', array( __CLASS__, 'output' ) );
	}
}
