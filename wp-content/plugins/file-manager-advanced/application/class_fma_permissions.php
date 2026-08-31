<?php
/**
 * File Manager Advanced permission helpers.
 *
 * @package File Manager Advanced
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'class_fma_permissions' ) ) {
	return;
}

/**
 * Centralized access control for the file manager.
 */
class class_fma_permissions {

	/**
	 * Whether the current user may use the file manager.
	 *
	 * @return bool
	 */
	public static function user_has_file_manager_access() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$capability = self::get_required_capability();

		if ( ! current_user_can( $capability ) ) {
			return false;
		}

		// The `read` capability is granted to every logged-in user; require an allowed role too.
		if ( 'read' === $capability && ! self::user_role_is_allowed() ) {
			return false;
		}

		return true;
	}

	/**
	 * Capability required to render and use the file manager UI.
	 *
	 * @return string
	 */
	public static function get_required_capability() {
		if ( is_multisite() && ! is_network_admin() ) {
			return self::get_network_capability();
		}

		return self::get_fma_capability();
	}

	/**
	 * Single-site capability logic (mirrors class_fma_admin_menus::fmaPer).
	 *
	 * @return string
	 */
	public static function get_fma_capability() {
		$settings               = get_option( 'fmaoptions' );
		$user                   = wp_get_current_user();
		$allowed_fma_user_roles = isset( $settings['fma_user_roles'] ) ? $settings['fma_user_roles'] : array( 'administrator' );

		if ( ! in_array( 'administrator', $allowed_fma_user_roles, true ) ) {
			$fma_user_roles = array_merge( array( 'administrator' ), $allowed_fma_user_roles );
		} else {
			$fma_user_roles = $allowed_fma_user_roles;
		}

		$check_user_role_existence = array_intersect( $fma_user_roles, $user->roles );

		if ( count( $check_user_role_existence ) > 0 && ! in_array( 'administrator', $check_user_role_existence, true ) ) {
			return 'read';
		}

		return 'manage_options';
	}

	/**
	 * Multisite capability logic (mirrors class_fma_admin_menus::networkPer).
	 *
	 * @return string
	 */
	public static function get_network_capability() {
		$settings               = get_option( 'fmaoptions' );
		$user                   = wp_get_current_user();
		$allowed_fma_user_roles = isset( $settings['fma_user_roles'] ) ? $settings['fma_user_roles'] : array();

		$check_user_role_existence = array_intersect( $allowed_fma_user_roles, $user->roles );

		if ( count( $check_user_role_existence ) > 0 ) {
			if ( ! in_array( 'administrator', $check_user_role_existence, true ) ) {
				return 'read';
			}

			return 'manage_options';
		}

		return 'manage_network';
	}

	/**
	 * Whether the current user's role is explicitly allowed in plugin settings.
	 *
	 * @return bool
	 */
	public static function user_role_is_allowed() {
		$settings = get_option( 'fmaoptions' );
		$user     = wp_get_current_user();

		if ( in_array( 'administrator', $user->roles, true ) ) {
			return true;
		}

		$allowed_fma_user_roles = isset( $settings['fma_user_roles'] ) ? $settings['fma_user_roles'] : array( 'administrator' );

		return ! empty( array_intersect( $allowed_fma_user_roles, $user->roles ) );
	}

	/**
	 * Whether the current user has unrestricted filesystem access.
	 *
	 * @return bool
	 */
	public static function has_unrestricted_filesystem_access() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Root directory used to sandbox non-administrator users (uploads).
	 * Keeps granted roles out of ABSPATH / wp-admin / wp-includes / plugins.
	 *
	 * @return string
	 */
	public static function get_restricted_root_path() {
		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['basedir'] ) ) {
			$path = wp_normalize_path( $upload_dir['basedir'] );
		} else {
			$path = wp_normalize_path( WP_CONTENT_DIR . '/uploads' );
		}

		if ( ! is_dir( $path ) ) {
			wp_mkdir_p( $path );
		}

		return $path;
	}

	/**
	 * Public URL for the uploads-based restricted root directory.
	 *
	 * @return string
	 */
	public static function get_restricted_root_url() {
		$upload_dir = wp_upload_dir();

		if ( ! empty( $upload_dir['baseurl'] ) ) {
			return $upload_dir['baseurl'];
		}

		return content_url( 'uploads' );
	}

	/**
	 * MIME types denied for non-administrator upload and overwrite operations.
	 *
	 * @return array
	 */
	public static function get_restricted_upload_deny_mimes() {
		return array(
			'text/x-php',
			'application/x-httpd-php',
			'application/x-php',
			'text/javascript',
			'application/javascript',
			'application/x-javascript',
			'text/css',
			'application/x-executable',
			'text/html',
			'application/xhtml+xml',
		);
	}

	/**
	 * Volume attribute rules blocking sensitive files for non-administrators.
	 *
	 * @return array
	 */
	public static function get_restricted_file_attributes() {
		return array(
			array(
				// Covers .php, .php.bak, .php~, etc.
				'pattern' => '/\.php(\.|$)/i',
				'read'    => false,
				'write'   => false,
				'hidden'  => true,
				'locked'  => true,
			),
			array(
				'pattern' => '/\.phtml(\.|$)/i',
				'read'    => false,
				'write'   => false,
				'hidden'  => true,
				'locked'  => true,
			),
			array(
				'pattern' => '/\.js(\.|$)/i',
				'read'    => false,
				'write'   => false,
				'hidden'  => true,
				'locked'  => true,
			),
			array(
				'pattern' => '/\.css(\.|$)/i',
				'read'    => false,
				'write'   => false,
				'hidden'  => true,
				'locked'  => true,
			),
			array(
				'pattern' => '/\.htaccess$/i',
				'read'    => false,
				'write'   => false,
				'hidden'  => true,
				'locked'  => true,
			),
			array(
				'pattern' => '/wp-config(\.|$)/i',
				'read'    => false,
				'write'   => false,
				'hidden'  => true,
				'locked'  => true,
			),
			array(
				'pattern' => '/\.(html?|xhtml|shtml)$/i',
				'read'    => false,
				'write'   => false,
				'hidden'  => true,
				'locked'  => true,
			),
		);
	}

	/**
	 * Abort AJAX requests from users without file manager access.
	 *
	 * @return void
	 */
	public static function verify_ajax_access() {
		if ( ! self::user_has_file_manager_access() ) {
			wp_die( esc_html__( 'You do not have permission to access the file manager.', 'file-manager-advanced' ), esc_html__( 'Forbidden', 'file-manager-advanced' ), array( 'response' => 403 ) );
		}
	}

	/**
	 * Whether a filename is allowed for non-administrator write operations.
	 *
	 * @param string $name File name.
	 * @return bool
	 */
	public static function is_restricted_write_filename_allowed( $name ) {
		if ( empty( $name ) ) {
			return false;
		}

		return (bool) afm_plugin_file_validName( $name );
	}
}
