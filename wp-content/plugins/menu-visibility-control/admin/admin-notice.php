<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Show admin notice on Appearance → Menus once per day
 */
add_action( 'admin_notices', 'mvc_show_menu_admin_notice' );
function mvc_show_menu_admin_notice() {

	$screen = get_current_screen();
	if ( ! $screen || 'nav-menus' !== $screen->id ) {
		return;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return;
	}

	$last_dismissed = get_user_meta( $user_id, '_mvc_notice_dismissed', true );

	// Show again after 24 hours
	if ( $last_dismissed && ( time() - (int) $last_dismissed ) < DAY_IN_SECONDS ) {
		return;
	}

	$nonce = wp_create_nonce( 'mvc_dismiss_notice' );

	// External icon (intentional & stable)
	$icon = 'https://knowledge.buzz/assets/mvc-icon-128x128.png';
	?>
	<div class="notice mvc-admin-notice is-dismissible" data-nonce="<?php echo esc_attr( $nonce ); ?>">
		<div class="mvc-notice-inner">

			<div class="mvc-notice-header">
				<img
					src="<?php echo esc_url( $icon ); ?>"
					alt="<?php esc_attr_e( 'Menu Visibility Control', 'menu-visibility-control' ); ?>"
					class="mvc-notice-icon"
					width="48"
					height="48"
				/>
				<h3><?php esc_html_e( 'Menu Visibility Control', 'menu-visibility-control' ); ?></h3>
			</div>

			<p>
				<?php esc_html_e(
					'Control who sees each menu item by user role, login status, device type, or specific pages — directly from this menu screen.',
					'menu-visibility-control'
				); ?>
			</p>

			<p class="mvc-links">
				<a href="https://knowledge.buzz/menu-visibility-control" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( '📘 Documentation', 'menu-visibility-control' ); ?>
				</a>

				<a href="https://wordpress.org/support/plugin/menu-visibility-control/reviews/#new-post" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( '⭐ Leave a Review', 'menu-visibility-control' ); ?>
				</a>

				<a href="https://knowledge.buzz/donate" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( '❤️ Support Development', 'menu-visibility-control' ); ?>
				</a>
			</p>

		</div>
	</div>
	<?php
}

/**
 * Handle notice dismissal (AJAX)
 */
add_action( 'wp_ajax_mvc_dismiss_notice', 'mvc_dismiss_menu_notice' );
function mvc_dismiss_menu_notice() {

	check_ajax_referer( 'mvc_dismiss_notice', 'nonce' );

	$user_id = get_current_user_id();
	if ( $user_id ) {
		update_user_meta( $user_id, '_mvc_notice_dismissed', time() );
	}

	wp_send_json_success();
}

/**
 * Admin notice styles & scripts
 */
add_action( 'admin_enqueue_scripts', 'mvc_admin_notice_assets' );
function mvc_admin_notice_assets( $hook ) {

	if ( 'nav-menus.php' !== $hook ) {
		return;
	}

	// Inline CSS (WordPress.org reviewer-safe)
	wp_add_inline_style(
		'wp-admin',
		'
		.mvc-admin-notice {
			border: none;
			background: transparent;
			padding: 0;
		}
		.mvc-notice-inner {
			background: linear-gradient(145deg, #ffffff, #f3f4f6);
			border-radius: 14px;
			padding: 18px 22px;
			box-shadow:
				0 10px 24px rgba(0,0,0,0.08),
				inset 0 1px 0 rgba(255,255,255,0.6);
		}
		.mvc-notice-header {
			display: flex;
			align-items: center;
			gap: 14px;
			margin-bottom: 6px;
		}
		.mvc-notice-icon {
			border-radius: 12px;
			background: #fff;
			box-shadow:
				0 4px 10px rgba(0,0,0,0.12),
				inset 0 1px 0 rgba(255,255,255,0.5);
		}
		.mvc-notice-inner h3 {
			margin: 0;
			font-size: 16px;
			line-height: 1.4;
		}
		.mvc-links a {
			margin-right: 16px;
			text-decoration: none;
			font-weight: 500;
		}
		'
	);

	// Inline JS (dismiss handler)
	wp_add_inline_script(
		'jquery-core',
		'
		jQuery(document).on("click", ".mvc-admin-notice .notice-dismiss", function () {
			const notice = jQuery(this).closest(".mvc-admin-notice");
			jQuery.post(ajaxurl, {
				action: "mvc_dismiss_notice",
				nonce: notice.data("nonce")
			});
		});
	'
	);
}
