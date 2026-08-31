<?php
/**
 * Header notification dropdown template.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress/header-bar/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$menu_link                 = trailingslashit( bp_loggedin_user_domain() . bp_get_notifications_slug() );
$notifications             = bp_notifications_get_unread_notification_count( bp_loggedin_user_id() );
$unread_notification_count = ! empty( $notifications ) ? $notifications : 0;

$notifications_icon = ( isset( $settings['notifications_icon']['value'] ) && '' !== $settings['notifications_icon']['value'] ) ? $settings['notifications_icon']['value'] : 'wbe-icon-bell';
?>
<div id="header-notifications-dropdown-elem" class="notification-wrap menu-item-has-children">
	<a href="<?php echo esc_url( $menu_link ); ?>"
		ref="notification_bell"
		class="notification-link">
		<span data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Notifications', 'wbcom-essential' ); ?>">
			<i class="<?php echo esc_attr( $notifications_icon ); ?>"></i>
			<?php if ( $unread_notification_count > 0 ) : ?>
				<span class="count"><?php echo esc_html( $unread_notification_count ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<section class="notification-dropdown">
		<header class="notification-header">
			<h2 class="title"><?php esc_html_e( 'Notifications', 'wbcom-essential' ); ?></h2>
			<a class="mark-read-all action-unread" data-notification-id="all" style="display: none;">
				<?php esc_html_e( 'Mark all as read', 'wbcom-essential' ); ?>
			</a>
		</header>

		<ul class="notification-list wbcom-essential-nouveau-list">
			<?php if ( bp_has_notifications( bp_ajax_querystring( 'notifications' ) . '&user_id=' . get_current_user_id() . '&is_new=1' ) ) : ?>
				<?php
				while ( bp_the_notifications() ) :
					bp_the_notification();
					?>
					<li class="read-item <?php echo isset( buddypress()->notifications->query_loop->notification->is_new ) && buddypress()->notifications->query_loop->notification->is_new ? 'unread' : ''; ?>">
						<span class="wbcom-essential--full-link">
							<?php bp_the_notification_description(); ?>
						</span>
						<div class="notification-avatar">
							<?php wbcom_essential_notification_avatar(); ?>
						</div>
						<div class="notification-content">
							<span class="wbcom-essential--full-link">
								<?php bp_the_notification_description(); ?>
							</span>
							<span><?php bp_the_notification_description(); ?></span>
							<span class="posted"><?php bp_the_notification_time_since(); ?></span>
						</div>						
					</li>
				<?php endwhile; ?>
			<?php else : ?>
				<li class="bs-item-wrap">
					<div class="notification-content"><?php esc_html_e( 'No new notifications', 'wbcom-essential' ); ?>!</div>
				</li>
			<?php endif; ?>

		</ul>

		<footer class="notification-footer">
			<a href="<?php echo esc_url( $menu_link ); ?>" class="delete-all">
				<?php esc_html_e( 'View Notifications', 'wbcom-essential' ); ?>
				<i class="wbcom-essential-icon-angle-right"></i>
			</a>
		</footer>
	</section>
</div>
