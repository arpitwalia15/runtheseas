<?php
/**
 * Header bar template.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress/header-bar/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


$settings_align = $settings['content_align'];

$settings_search_ico         = $settings['search_icon']['value'];
$settings_messages_icon      = ( function_exists( 'bp_is_active' ) && bp_is_active( 'messages' ) ) ? $settings['messages_icon']['value'] : '';
$settings_friends_icon       = ( function_exists( 'bp_is_active' ) && bp_is_active( 'friends' ) ) ? $settings['friends_icon']['value'] : '';
$settings_notifications_icon = ( function_exists( 'bp_is_active' ) && bp_is_active( 'notifications' ) ) ? $settings['notifications_icon']['value'] : '';
$settings_cart_icon          = ( class_exists( 'WooCommerce' ) ) ? $settings['cart_icon']['value'] : '';
$settings_dark_icon          = ( class_exists( 'SFWD_LMS' ) ) ? $settings['dark_icon']['value'] : '';
$settings_sidebartoggle_icon = ( class_exists( 'SFWD_LMS' ) ) ? $settings['sidebartoggle_icon']['value'] : '';
$settings_avatar_border      = isset( $settings['avatar_border_style'] ) ? $settings['avatar_border_style'] : '';

$this->add_render_attribute( 'site-header', 'class', 'site-header site-header--elementor icon-fill-in' );
$this->add_render_attribute( 'site-header', 'class', 'site-header--align-' . esc_attr( $settings_align ) . '' );
$this->add_render_attribute( 'site-header', 'class', 'avatar-' . esc_attr( $settings_avatar_border ) . '' );
$this->add_render_attribute( 'site-header', 'data-search-icon', esc_attr( $settings_search_ico ) );
$this->add_render_attribute( 'site-header', 'data-messages-icon', esc_attr( $settings_messages_icon ) );
$this->add_render_attribute( 'site-header', 'data-friends-icon', esc_attr( $settings_friends_icon ) );
$this->add_render_attribute( 'site-header', 'data-notifications-icon', esc_attr( $settings_notifications_icon ) );
$this->add_render_attribute( 'site-header', 'data-cart-icon', esc_attr( $settings_cart_icon ) );
$this->add_render_attribute( 'site-header', 'data-dark-icon', esc_attr( $settings_dark_icon ) );
$this->add_render_attribute( 'site-header', 'data-sidebartoggle-icon', esc_attr( $settings_sidebartoggle_icon ) );

$this->add_render_attribute( 'site-header-container', 'class', 'container site-header-container flex default-header' );
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $this->get_render_attribute_string() is a safe Elementor method that returns escaped HTML
$container = '<div ' . $this->get_render_attribute_string( 'site-header-container' ) . '>';

$elem = ( is_front_page() && is_home() ) ? 'h1' : 'div';

?>

<div <?php $this->print_render_attribute_string( 'site-header' ); ?>>
	<?php
	$nheader_aside_template_path = WBCOM_ESSENTIAL_ELEMENTOR_WIDGET_PATH . '/Buddypress/header-bar/templates/header-aside.php';

	if ( file_exists( $nheader_aside_template_path ) ) {
		require $nheader_aside_template_path;
	}
	?>
	<div class="header-search-wrap header-search-wrap--elementor">
		<div class="container">
			<?php get_search_form(); ?>
			<a href="#" class="close-search" aria-label="<?php esc_attr_e( 'Close search', 'wbcom-essential' ); ?>"><i class="eicon-close-circle" aria-hidden="true"></i></a>
		</div>
	</div>
</div>
