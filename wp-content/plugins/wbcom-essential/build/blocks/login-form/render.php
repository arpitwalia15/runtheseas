<?php
/**
 * Server-side render for Login Form block.
 *
 * @package WBCOM_Essential
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$unique_id   = ! empty( $attributes['uniqueId'] ) ? $attributes['uniqueId'] : '';
$vis_classes = \WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::get_visibility_classes( $attributes );
\WBCOM_ESSENTIAL\Gutenberg\WBE_CSS::add( $unique_id, $attributes );

// Colour attributes with fallbacks.
$form_bg      = ! empty( $attributes['formBg'] ) ? $attributes['formBg'] : '#ffffff';
$button_bg    = ! empty( $attributes['buttonBg'] ) ? $attributes['buttonBg'] : '#667eea';
$button_color = ! empty( $attributes['buttonColor'] ) ? $attributes['buttonColor'] : '#ffffff';
$label_color  = ! empty( $attributes['labelColor'] ) ? $attributes['labelColor'] : '#1e1e2e';
$link_color   = ! empty( $attributes['linkColor'] ) ? $attributes['linkColor'] : '#667eea';

// Content attributes.
$redirect_url      = ! empty( $attributes['redirectUrl'] ) ? esc_url( $attributes['redirectUrl'] ) : '';
$show_remember     = ! empty( $attributes['showRememberMe'] );
$show_register     = ! empty( $attributes['showRegisterLink'] );
$show_lost_pass    = ! empty( $attributes['showLostPassword'] );
$button_text       = ! empty( $attributes['buttonText'] ) ? $attributes['buttonText'] : __( 'Log In', 'wbcom-essential' );
$logged_in_message = ! empty( $attributes['loggedInMessage'] ) ? $attributes['loggedInMessage'] : __( 'You are already logged in.', 'wbcom-essential' );

// Build wrapper CSS custom properties for colour tokens.
$token_style = sprintf(
	'--wbe-lf-form-bg:%s;--wbe-lf-btn-bg:%s;--wbe-lf-btn-color:%s;--wbe-lf-label-color:%s;--wbe-lf-link-color:%s;',
	esc_attr( $form_bg ),
	esc_attr( $button_bg ),
	esc_attr( $button_color ),
	esc_attr( $label_color ),
	esc_attr( $link_color )
);

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => trim( 'wbe-block-' . esc_attr( $unique_id ) . ' wbe-login-form ' . $vis_classes ),
		'style' => $token_style,
	)
);
?>

<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes() ?>>

	<?php if ( is_user_logged_in() ) : ?>

		<div class="wbe-login-form__logged-in">
			<p class="wbe-login-form__logged-in-message">
				<?php echo esc_html( $logged_in_message ); ?>
			</p>
			<?php
			$current_user = wp_get_current_user();
			$profile_url  = get_author_posts_url( $current_user->ID );
			?>
			<a class="wbe-login-form__profile-link" href="<?php echo esc_url( $profile_url ); ?>">
				<?php esc_html_e( 'View your profile', 'wbcom-essential' ); ?>
			</a>
		</div>

	<?php else : ?>

		<div class="wbe-login-form__inner">
			<?php
			$login_form_args = array(
				'echo'           => true,
				'remember'       => $show_remember,
				'redirect'       => $redirect_url ? $redirect_url : esc_url_raw( home_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ) ) ),
				'form_id'        => 'wbe-login-form-' . esc_attr( $unique_id ),
				'label_username' => __( 'Username or Email Address', 'wbcom-essential' ),
				'label_password' => __( 'Password', 'wbcom-essential' ),
				'label_remember' => __( 'Remember Me', 'wbcom-essential' ),
				'label_log_in'   => esc_html( $button_text ),
				'id_submit'      => 'wbe-login-submit-' . esc_attr( $unique_id ),
			);

			wp_login_form( $login_form_args );
			?>

			<?php if ( $show_lost_pass || $show_register ) : ?>
				<div class="wbe-login-form__links">
					<?php if ( $show_lost_pass ) : ?>
						<a class="wbe-login-form__link wbe-login-form__link--lost" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
							<?php esc_html_e( 'Lost your password?', 'wbcom-essential' ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $show_register && get_option( 'users_can_register' ) ) : ?>
						<a class="wbe-login-form__link wbe-login-form__link--register" href="<?php echo esc_url( wp_registration_url() ); ?>">
							<?php esc_html_e( 'Register', 'wbcom-essential' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

	<?php endif; ?>

</div>
