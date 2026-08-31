<?php
/**
 * Display BuddyPress Groups.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/templates/reign/buddypress/nouveau/groups
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="groups-dir-list" class="groups dir-list">
<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) . $query_string ) ) : ?>
	<ul id="groups-list" class="rg-group-list item-list groups-list bp-list grid <?php echo esc_attr( $column_class . ' ' . $group_directory_type ); ?>">

		<?php
		while ( bp_groups() ) :
			bp_the_group();
			?>

			<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups">
				<div class="list-wrap">

					<?php
					/**
					 * Fires inside the listing of an individual group listing item.
					 * Added by Reign Theme
					 *
					 * @since 1.0.7
					 */
					// do_action( 'wbtm_before_group_avatar_group_directory' );
					if ( 'wbtm-group-directory-type-1' !== $group_directory_type ) {
						$args = array(
							'object_dir' => 'groups',
							'item_id'    => $group_id = bp_get_group_id(),
							'type'       => 'cover-image',
						);
						$cover_img_url = bp_attachments_get_attachment( 'url', $args );
						if ( empty( $cover_img_url ) ) {
							global $wbtm_reign_settings;
							$cover_img_url = isset( $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] ) ? $wbtm_reign_settings['reign_buddyextender']['default_group_cover_image_url'] : REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
							if ( empty( $cover_img_url ) ) {
								$cover_img_url = REIGN_INC_DIR_URI . 'reign-settings/imgs/default-cover.jpg';
							}
						}
						echo '<div class="wbtm-group-cover-img"><img src="' . esc_url( $cover_img_url ) . '" alt="" /></div>';
					}
					?>

					<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
						<div class="item-avatar">
							<?php
							if ( 'wbtm-group-directory-type-4' === $group_directory_type ) {
								echo '<figure class="img-dynamic aspect-ratio avatar">';
							}
							?>
							<?php if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) : ?>
								<a class="item-avatar-group <?php echo esc_attr( $img_class ); ?>" href="<?php bp_group_url(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
							<?php else : ?>
								<a class="item-avatar-group <?php echo esc_attr( $img_class ); ?>" href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
							<?php endif; ?>
								<?php
								if ( 'wbtm-group-directory-type-4' === $group_directory_type ) {
									echo '</figure>';
								}
								?>
						</div>
					<?php endif; ?>

					<div class="group-content-wrap">
						<div class="item">

							<div class="item-block">

								<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>

								<?php if ( bp_nouveau_group_has_meta() ) : ?>

									<p class="item-meta group-details">
										<?php
										if ( function_exists( 'bp_nouveau_the_group_meta' ) ) {
											bp_nouveau_the_group_meta();
										} elseif ( function_exists( 'bp_nouveau_group_meta' ) ) {
											bp_nouveau_group_meta();
										}
										?>
									</p>

								<?php endif; ?>

								<p class="last-activity item-meta">
									<?php
									printf(
									/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
										esc_html__( 'active %s', 'wbcom-essential' ),
										esc_attr( bp_get_group_last_active() )
									);
									?>
								</p>

							</div>

							<div class="group-desc"><p><?php bp_group_description_excerpt(); ?></p></div>

							<?php bp_nouveau_groups_loop_item(); ?>

							<?php // bp_nouveau_groups_loop_buttons(); ?>

						</div>

						<?php do_action( 'wbtm_bp_directory_groups_data' ); ?>

						<div class="group-admins-wrap">
							<?php reign_bp_group_list_admins(); ?>
						</div>

						<!-- Added action buttons here -->
						<?php
						if ( 'wbtm-group-directory-type-3' === $group_directory_type ) {
							echo '<div class="action-wrap"><i class="fa fa-plus-circle"></i>';
						}
						bp_nouveau_groups_loop_buttons();
						if ( 'wbtm-group-directory-type-3' === $group_directory_type ) {
							echo '</div>';
						}
						?>
					</div>
				</div>
			</li>

		<?php endwhile; ?>

	</ul>
<?php else : ?>

	<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

<?php endif; ?>
</div>
