<?php
/**
 * Partial: Author Bio
 *
 * Displays author avatar, name, and bio. Only renders if bio exists.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/single-post/partials
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wbcom_author_bio = get_the_author_meta( 'description' );

if ( ! $wbcom_author_bio ) {
	return;
}

$wbcom_author_id   = get_the_author_meta( 'ID' );
$wbcom_author_name = get_the_author();
$wbcom_author_url  = get_author_posts_url( $wbcom_author_id );
?>
<div class="wbcom-sp-author-bio">
	<div class="wbcom-sp-author-bio__avatar">
		<a href="<?php echo esc_url( $wbcom_author_url ); ?>">
			<?php echo get_avatar( $wbcom_author_id, 80 ); ?>
		</a>
	</div>
	<div class="wbcom-sp-author-bio__content">
		<h4 class="wbcom-sp-author-bio__name">
			<a href="<?php echo esc_url( $wbcom_author_url ); ?>">
				<?php echo esc_html( $wbcom_author_name ); ?>
			</a>
		</h4>
		<p class="wbcom-sp-author-bio__text"><?php echo wp_kses_post( $wbcom_author_bio ); ?></p>
	</div>
</div>
