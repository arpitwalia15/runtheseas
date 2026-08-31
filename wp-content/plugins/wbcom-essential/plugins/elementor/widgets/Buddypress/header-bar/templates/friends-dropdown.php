<?php
/**
 * Header friend requests dropdown template.
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

$menu_link            = wbcom_essential_get_friend_requests_url();
$friend_requests      = wbcom_essential_get_friend_requests( get_current_user_id(), 10 );
$friend_requests_count = wbcom_essential_get_friend_requests_count( get_current_user_id() );
$friends_icon         = ( isset( $settings['friends_icon']['value'] ) && '' !== $settings['friends_icon']['value'] ) ? $settings['friends_icon']['value'] : 'wbe-icon-user-plus';
?>
<div id="header-friends-dropdown-elem" class="notification-wrap friends-wrap menu-item-has-children">
	<a href="<?php echo esc_url( $menu_link ); ?>"
		ref="friend_requests"
		class="notification-link">
		<span data-balloon-pos="down" data-balloon="<?php esc_attr_e( 'Connections', 'wbcom-essential' ); ?>">
			<i class="<?php echo esc_attr( $friends_icon ); ?>"></i>
			<?php if ( $friend_requests_count > 0 ) : ?>
				<span class="count"><?php echo esc_html( wbcom_essential_get_header_counter_label( $friend_requests_count ) ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<section class="notification-dropdown">
		<header class="notification-header">
			<h2 class="title"><?php esc_html_e( 'Connection Requests', 'wbcom-essential' ); ?></h2>
		</header>

		<ul class="notification-list wbcom-essential-friends-list">
			<?php if ( ! empty( $friend_requests ) ) : ?>
				<?php foreach ( $friend_requests as $friend_request ) : ?>
					<li class="unread">
						<div class="notification-avatar">
							<a href="<?php echo esc_url( $friend_request['link'] ); ?>">
								<?php echo wp_kses_post( $friend_request['avatar_html'] ); ?>
							</a>
						</div>
						<div class="notification-content">
							<a class="wbcom-essential-friend-name" href="<?php echo esc_url( $friend_request['link'] ); ?>">
								<?php echo esc_html( $friend_request['name'] ); ?>
							</a>
							<?php if ( ! empty( $friend_request['last_active'] ) ) : ?>
								<span class="posted"><?php echo esc_html( $friend_request['last_active'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="notification-actions">
							<?php if ( ! empty( $friend_request['accept_url'] ) ) : ?>
								<a class="wbcom-essential-friend-action is-accept" href="<?php echo esc_url( $friend_request['accept_url'] ); ?>" aria-label="<?php esc_attr_e( 'Accept request', 'wbcom-essential' ); ?>">
									<i class="wbe-icon-check" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
							<?php if ( ! empty( $friend_request['reject_url'] ) ) : ?>
								<a class="wbcom-essential-friend-action is-reject" href="<?php echo esc_url( $friend_request['reject_url'] ); ?>" aria-label="<?php esc_attr_e( 'Reject request', 'wbcom-essential' ); ?>">
									<i class="wbe-icon-xmark" aria-hidden="true"></i>
								</a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			<?php else : ?>
				<li class="bs-item-wrap">
					<div class="notification-content"><?php esc_html_e( 'No connection requests found.', 'wbcom-essential' ); ?></div>
				</li>
			<?php endif; ?>
		</ul>

		<footer class="notification-footer">
			<a href="<?php echo esc_url( $menu_link ); ?>" class="delete-all">
				<?php esc_html_e( 'View Requests', 'wbcom-essential' ); ?>
				<i class="wbcom-essential-icon-angle-right"></i>
			</a>
		</footer>
	</section>
</div>
