<?php
/**
 * Partial: Post Navigation
 *
 * Simple previous/next text links. No fancy styling.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/single-post/partials
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wbcom_prev = get_previous_post();
$wbcom_next = get_next_post();

if ( ! $wbcom_prev && ! $wbcom_next ) {
	return;
}
?>
<nav class="wbcom-sp-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'wbcom-essential' ); ?>">
	<div class="wbcom-sp-nav__links">
		<?php if ( $wbcom_prev ) : ?>
			<a href="<?php echo esc_url( get_permalink( $wbcom_prev ) ); ?>" class="wbcom-sp-nav__prev" rel="prev">
				<span class="wbcom-sp-nav__arrow">&larr;</span>
				<span class="wbcom-sp-nav__text"><?php echo esc_html( get_the_title( $wbcom_prev ) ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( $wbcom_next ) : ?>
			<a href="<?php echo esc_url( get_permalink( $wbcom_next ) ); ?>" class="wbcom-sp-nav__next" rel="next">
				<span class="wbcom-sp-nav__text"><?php echo esc_html( get_the_title( $wbcom_next ) ); ?></span>
				<span class="wbcom-sp-nav__arrow">&rarr;</span>
			</a>
		<?php endif; ?>
	</div>
</nav>
