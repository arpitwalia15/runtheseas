<?php
/**
 * WC cart Side Drawer.
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

$cart_icon = ( isset( $settings['cart_icon']['value'] ) && '' !== $settings['cart_icon']['value'] ) ? $settings['cart_icon']['value'] : 'wbe-icon-shopping-cart';
?>

<div class="notification-wrap header-cart-link-wrap cart-wrap">
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="header-cart-link notification-link header-cart-drawer-trigger">
		<span data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Cart', 'wbcom-essential' ); ?>">
			<i class="<?php echo esc_attr( $cart_icon ); ?>"></i>
			<?php
			if ( is_object( WC()->cart ) ) {
				$wc_cart_count = wc()->cart->get_cart_contents_count();
				if ( 0 !== $wc_cart_count ) {
					?>
					<span class="count header-cart-count"><?php echo esc_html( $wc_cart_count ); ?></span>
					<?php
				}
			}
			?>
		</span>
	</a>
</div>

<!-- Side Drawer for Cart -->
<div class="header-cart-drawer" aria-hidden="true">
	<div class="header-cart-drawer__overlay"></div>
	<div class="header-cart-drawer__content">
		<div class="header-cart-drawer__header">
			<h3 class="header-cart-drawer__title"><?php esc_html_e( 'Shopping Cart', 'wbcom-essential' ); ?></h3>
			<button type="button" class="header-cart-drawer__close" aria-label="<?php esc_attr_e( 'Close cart', 'wbcom-essential' ); ?>">
				<span class="widget-close-text"><?php esc_html_e( 'Close', 'wbcom-essential' ); ?></span>
				<span class="widget-close-icon">&mdash;</span>
			</button>
		</div>
		<div class="header-cart-drawer__body">
			<div class="widget_shopping_cart_content">
				<?php
				if ( is_object( WC()->cart ) ) {
					woocommerce_mini_cart();
				}
				?>
			</div>
		</div>
	</div>
</div>
