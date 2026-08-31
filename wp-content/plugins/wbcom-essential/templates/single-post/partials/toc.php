<?php
/**
 * Partial: Table of Contents
 *
 * Parses post content for H2/H3 headings and builds a sticky ToC nav.
 * Adds anchor IDs to headings via content filter.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/single-post/partials
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wbcom_toc_content = get_the_content();
$wbcom_toc_content = apply_filters( 'the_content', $wbcom_toc_content );

preg_match_all( '/<h([23])[^>]*>(.*?)<\/h[23]>/i', $wbcom_toc_content, $wbcom_toc_matches, PREG_SET_ORDER );

if ( empty( $wbcom_toc_matches ) ) {
	return;
}

$wbcom_toc_items = array();
foreach ( $wbcom_toc_matches as $wbcom_match ) {
	$wbcom_text                    = wp_strip_all_tags( $wbcom_match[2] );
	$wbcom_id                      = sanitize_title( $wbcom_text );
	$wbcom_toc_items[] = array(
		'level' => (int) $wbcom_match[1],
		'text'  => $wbcom_text,
		'id'    => $wbcom_id,
	);
}

if ( empty( $wbcom_toc_items ) ) {
	return;
}
?>
<nav class="wbcom-sp-toc" aria-label="<?php esc_attr_e( 'Table of Contents', 'wbcom-essential' ); ?>">
	<h4 class="wbcom-sp-toc__title"><?php esc_html_e( 'Contents', 'wbcom-essential' ); ?></h4>
	<ul class="wbcom-sp-toc__list">
		<?php foreach ( $wbcom_toc_items as $wbcom_item ) : ?>
			<li class="wbcom-sp-toc__item wbcom-sp-toc__item--h<?php echo esc_attr( $wbcom_item['level'] ); ?>">
				<a href="#<?php echo esc_attr( $wbcom_item['id'] ); ?>" class="wbcom-sp-toc__link">
					<?php echo esc_html( $wbcom_item['text'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
