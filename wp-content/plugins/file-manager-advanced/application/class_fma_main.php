<?php
/**
 * File Manager Advanced Main Class
 *
 * @package: File Manager Advanced
 * @Class: fma_main
 */

defined('ABSPATH') || exit;

if (class_exists('class_fma_main')) {
	return;
}

/**
 * Main Class
 */
class class_fma_main
{
	/**
	 * Settings
	 *
	 * @var false|mixed|null $settings Plugin settings.
	 */
	public $settings;

	/**
	 * SMTP Recommendation instance
	 */
	public $recommend_smtp;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		add_action('admin_menu', array(&$this, 'fma_menus'));
		add_action('admin_enqueue_scripts', array(&$this, 'fma_scripts'));
		add_action('wp_ajax_fma_load_fma_ui', array(&$this, 'fma_load_fma_ui'));
		add_action('wp_ajax_fma_review_ajax', array($this, 'fma_review_ajax'));
		add_action('wp_ajax_fma_save_php_file', array($this, 'fma_save_php_file'));
		add_action('wp_ajax_fma_debug_php', array($this, 'fma_debug_php'));
		add_action('admin_footer', array($this, 'maybe_inject_review_header_line'));
		$this->settings = get_option('fmaoptions');

		add_action('admin_init', array($this, 'admin_init'));

		// Initialize SMTP recommendation
		$this->init_smtp_recommendation();
	}

	/**
	 * Load Menus
	 */
	public function fma_menus()
	{
		include 'class_fma_admin_menus.php';
		$fma_menus = new class_fma_admin_menus();
		$fma_menus->load_menus();
	}

	/**
	 * Load File Manager UI
	 */
	public function fma_load_fma_ui()
	{
		// Clear any buffered output so file/download streams stay binary-clean.
		while (ob_get_level()) {
			ob_end_clean();
		}

		class_fma_permissions::verify_ajax_access();

		$fmakey = isset($_REQUEST['_fmakey']) ? sanitize_text_field(wp_unslash($_REQUEST['_fmakey'])) : '';
		if ('' === $fmakey || !wp_verify_nonce($fmakey, 'fmaskey')) {
			wp_die(esc_html__('Security check failed', 'file-manager-advanced'), esc_html__('Forbidden', 'file-manager-advanced'), array('response' => 403));
		}

		include 'class_fma_connector.php';
		$fma_connector = new class_fma_connector();
		$fma_connector->fma_local_file_system();
	}

	/**
	 * Load Scripts
	 *
	 * @param string $hook The current admin page.
	 */
	public function fma_scripts($hook)
	{
		$locale = isset($this->settings['fma_locale']) ? sanitize_file_name($this->settings['fma_locale']) : 'en';
		$display_ui_options = isset($this->settings['display_ui_options']) ? $this->settings['display_ui_options'] : FMA_UI;
		$cm_theme = isset($this->settings['fma_cm_theme']) ? $this->settings['fma_cm_theme'] : 'default';
		$library_url = FMA_PLUGIN_URL . 'application/library/';
		$hide_path = false;
		if (isset($this->settings['hide_path']) && 1 === absint($this->settings['hide_path'])) {
			$hide_path = true;
		}

		if ('toplevel_page_file_manager_advanced_ui' === $hook) {
			if (isset($_GET['page']) && 'file_manager_advanced_ui' === sanitize_text_field(wp_unslash($_GET['page']))) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				wp_enqueue_style('elfinder.jquery-ui', $library_url . 'jquery/jquery-ui.min.css', array(), FMA_VERSION, 'all');
				wp_enqueue_style('elfinder', $library_url . 'css/elfinder.min.css', array(), FMA_VERSION, 'all');
				wp_enqueue_style('elfinder.theme', $library_url . 'css/theme.css', array(), FMA_VERSION, 'all');
				wp_enqueue_style('codemirror', $library_url . 'codemirror/lib/codemirror.css', array(), FMA_VERSION, 'all');

				if (isset($this->settings['fma_theme']) && in_array($this->settings['fma_theme'], array('dark', 'grey', 'windows10', 'bootstrap', 'mono', 'm-light', 'moono'), true)) {
					wp_enqueue_style('elfinder.preview', $library_url . 'themes/' . $this->settings['fma_theme'] . '/css/theme.css', array(), FMA_VERSION, 'all');
				}

				wp_enqueue_style('elfinder.styles', FMA_PLUGIN_URL . 'application/assets/css/custom_style_filemanager_advanced.css', array(), FMA_VERSION, 'all');

				wp_enqueue_script('elfinder', $library_url . 'js/elfinder.min.js', array('jquery', 'jquery-ui-core', 'jquery-ui-selectable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-resizable', 'jquery-ui-dialog', 'jquery-ui-slider', 'jquery-ui-tabs'), FMA_VERSION, true);
				wp_enqueue_script('fma-elfinder-security', FMA_PLUGIN_URL . 'application/assets/js/fma-elfinder-security.js', array('jquery', 'elfinder'), FMA_VERSION, true);
				wp_enqueue_script('codemirror', $library_url . 'codemirror/lib/codemirror.js', array(), FMA_VERSION, true);
				wp_enqueue_script('codemirror.htmlmixed', $library_url . 'codemirror/mode/htmlmixed/htmlmixed.js', array(), FMA_VERSION, true);
				wp_enqueue_script('codemirror.xml', $library_url . 'codemirror/mode/xml/xml.js', array(), FMA_VERSION, true);
				wp_enqueue_script('codemirror.css', $library_url . 'codemirror/mode/css/css.js', array(), FMA_VERSION, true);
				wp_enqueue_script('codemirror.javascript', $library_url . 'codemirror/mode/javascript/javascript.js', array(), FMA_VERSION, true);
				wp_enqueue_script('codemirror.clike', $library_url . 'codemirror/mode/clike/clike.js', array(), FMA_VERSION, true);
				wp_enqueue_script('codemirror.php', $library_url . 'codemirror/mode/php/php.js', array(), FMA_VERSION, true);

				if ('en' !== $locale) {
					wp_enqueue_script('elfinder.language', $library_url . sprintf('js/i18n/elfinder.%s.js', $locale), array('elfinder'), FMA_VERSION, true);
				}

				if ('default' !== $cm_theme) {
					wp_enqueue_style('codemirror.theme', $library_url . 'codemirror/theme/' . $cm_theme . '.css', array(), FMA_VERSION, 'all');
				}

				wp_enqueue_script('fma-elfinder-commands', FMA_PLUGIN_URL . 'application/assets/js/fma-elfinder-commands.js', array('jquery', 'elfinder', 'fma-elfinder-security'), FMA_VERSION, true);
				wp_enqueue_script('elfinder.script', FMA_PLUGIN_URL . 'application/assets/js/elfinder_script.js', array('jquery', 'fma-elfinder-security', 'fma-elfinder-commands'), FMA_VERSION, true);
				wp_localize_script(
					'elfinder.script',
					'afm_object',
					array(
						'ajaxurl' => admin_url('admin-ajax.php'),
						'nonce' => wp_create_nonce('fmaskey'),
						'locale' => $locale,
						'ui' => $display_ui_options,
						'cm_theme' => $cm_theme,
						'hide_path' => $hide_path,
						'plugin_url' => FMA_PLUGIN_URL,
						'debug_enabled' => isset($this->settings['fma_debug_enabled']) ? $this->settings['fma_debug_enabled'] : '0',
					)
				);
			}
		}

		wp_register_style('afm-jquery.select2', FMA_PLUGIN_URL . 'application/assets/css/select2/jquery.select2.min.css', array(), FMA_VERSION, 'all');
		wp_register_script('afm-jquery.select2', FMA_PLUGIN_URL . 'application/assets/js/select2/jquery.select2.min.js', array('jquery'), FMA_VERSION, true);

		if (in_array($hook, array('file-manager_page_file_manager_advanced_controls', 'file-manager_page_file_manager_advanced_shortcodes', 'file-manager_page_afmp-adminer', 'file-manager_page_afmp-dropbox', 'file-manager_page_afmp-googledrive', 'file-manager_page_afmp-googlecloud', 'file-manager_page_afmp-github', 'toplevel_page_file_manager_advanced_ui', 'file-manager_page_afmp-file-logs', 'file-manager_page_afmp-onedrive', 'file-manager_page_afmp-aws', 'file-manager_page_afm-integrations-pro'), true)) {
			wp_enqueue_style('afm-admin', FMA_PLUGIN_URL . 'application/assets/css/afm-styles.css', array('afm-jquery.select2'), FMA_VERSION, 'all');
			wp_enqueue_script('afm-admin', FMA_PLUGIN_URL . 'application/assets/js/afm-scripts.js', array('afm-jquery.select2'), FMA_VERSION, true);
			wp_localize_script(
				'afm-admin',
				'afmAdmin',
				array(
					'assetsURL' => FMA_PLUGIN_URL . 'application/assets/',
					'jsonURL' => rest_url(),
				),
			);
			// Enqueue SMTP scripts if we are on the settings page
			if ($hook === 'file-manager_page_file_manager_advanced_controls' && $this->recommend_smtp) {
				$this->recommend_smtp->admin_enqueue_scripts(); 
			}
		}
	}

	/**
	 * Code Mirror Themes
	 */
	public static function cm_themes()
	{
		$cm_themes_dir = FMA_CM_THEMES_PATH;
		$cm_themes = [];
		$cm_themes['default'] = array(
			'title' => 'default',
			'pro' => false,
		);

		$free_themes = array('3024-day', '3024-night', 'base16-dark', 'base16-light', 'downtown-light');
		foreach (glob($cm_themes_dir . '/*.css') as $file) {
			$bn = basename($file, ".css");
			$args = array(
				'title' => $bn,
				'pro' => true,
			);
			if (in_array($bn, $free_themes, true)) {
				$args['pro'] = false;
			}
			$cm_themes[$bn] = $args;
		}

		usort(
			$cm_themes,
			function ($a, $b) {
				if ($a['pro'] === $b['pro']) {
					return 0;
				}
				return $a['pro'] ? 1 : -1;
			}
		);

		return $cm_themes;
	}

	/**
	 * Thank-you / Rate Us line used in AFM admin headers.
	 *
	 * @return string
	 */
	public static function review_header_line_html() {
		$img = plugins_url( 'images/5stars.png', FMAFILEPATH . 'application/pages/main.php' );
		return sprintf(
			'<span id="thankyou" class="fma-header-review description">%s<a class="fma-header-review-rate" href="%s" target="_blank" rel="noopener noreferrer">%s <img src="%s" alt=""></a></span>',
			wp_kses_post( __( 'Thank you for using <a href="https://wordpress.org/plugins/file-manager-advanced/">File Manager Advanced</a>. If happy then ', 'file-manager-advanced' ) ),
			esc_url( 'https://wordpress.org/support/plugin/file-manager-advanced/reviews/?filter=5' ),
			esc_html__( ' Rate Us', 'file-manager-advanced' ),
			esc_url( $img )
		);
	}

	/**
	 * Show the Rate Us line in the header on AFM pages except the main File Manager screen.
	 */
	public function maybe_inject_review_header_line() {
		if ( ! is_admin() ) {
			return;
		}

		$page      = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';

		if ( 'file_manager_advanced_ui' === $page ) {
			return;
		}

		$is_afm = false;
		if ( '' !== $page && (
			0 === strpos( $page, 'file_manager_advanced' )
			|| 0 === strpos( $page, 'afmp-' )
			|| 0 === strpos( $page, 'fma_shortcode' )
			|| 'afm-integrations-pro' === $page
		) ) {
			$is_afm = true;
		}
		if ( 'fma_shortcode' === $post_type ) {
			$is_afm = true;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && isset( $screen->post_type ) && 'fma_shortcode' === $screen->post_type ) {
			$is_afm = true;
		}

		if ( ! $is_afm ) {
			return;
		}

		$html = self::review_header_line_html();
		?>
		<style>
			.wrap .fma-header-review {
				float: right;
				margin: 0;
				padding: 6px 0 0;
				line-height: 1.4;
				white-space: nowrap;
			}
			.wrap .fma-header-review a {
				text-decoration: none;
			}
			.wrap .fma-header-review .fma-header-review-rate {
				display: inline-flex;
				align-items: center;
				gap: 4px;
				vertical-align: middle;
			}
			.wrap .fma-header-review img {
				width: 100px;
				height: auto;
				vertical-align: middle;
			}
		</style>
		<script>
		jQuery(function ($) {
			if ($('.fma-header-review').length) {
				return;
			}
			var $wrap = $('.wrap').first();
			var $h = $wrap.children('h1').first();
			if (!$h.length) {
				$h = $wrap.children('h2').first();
			}
			if (!$h.length) {
				return;
			}
			$h.addClass('wp-heading-inline');
			var html = <?php echo wp_json_encode( $html ); ?>;
			var $lastAction = $h.nextAll('.page-title-action').last();
			if ($lastAction.length) {
				$lastAction.after(html);
			} else {
				$h.after(html);
			}
			if (!$h.nextAll('hr.wp-header-end').length) {
				$wrap.find('.fma-header-review').first().after('<hr class="wp-header-end">');
			}
		});
		</script>
		<?php
	}

	/**
	 * Review Ajax
	 */
	public function fma_review_ajax()
	{
		$nonce = $_REQUEST['nonce'];
		if (!wp_verify_nonce($nonce, 'afm_review')) {
			die(__('Security check', 'file-manager-advanced'));
		} else {
			$task = sanitize_text_field($_POST['task']);
			$done = update_option('fma_hide_review_section', $task);
			if ($done) {
				echo '1';
			} else {
				echo '0';
			}
			die;
		}
	}

	/**
	 * Admin Init
	 *
	 * @since 3.3.1
	 */
	public function admin_init()
	{
		$is_pro_version = get_option('active_plugins', array());
		$is_pro_active = false;
		foreach ($is_pro_version as $plugin_file) {
			if (is_string($plugin_file) && preg_match('#/file-manager-advanced-shortcode\.php$#', $plugin_file)) {
				$is_pro_active = true;
				break;
			}
		}
		if ($is_pro_active) {
			require_once FMAFILEPATH . 'application/logs/class-filelogs.php';
		}
	}





	/**
	 * PHP Debug Analysis Ajax
	 */
	public function fma_debug_php()
	{
		class_fma_permissions::verify_ajax_access();

		// Check nonce for security
		if (!wp_verify_nonce($_POST['nonce'], 'fmaskey')) {
			wp_die(__('Security check failed', 'file-manager-advanced'));
		}

		// Get the PHP code from POST data
		$php_code = wp_unslash($_POST['php_code']);
		$filename = sanitize_text_field($_POST['filename']);

		// Load the debug analyzer
		require_once FMAFILEPATH . 'application/library/php-parser/src/FMA_PhpDebugAnalyzer.php';

		// Analyze PHP code for debug information
		$debug_result = FMA_PhpDebugAnalyzer::analyze($php_code, $filename);

		// Return JSON response
		wp_send_json($debug_result);
	}

	/**
	 * Save PHP file with proper unescaping
	 */
	public function fma_save_php_file()
	{
		if (!class_fma_permissions::user_has_file_manager_access()) {
			wp_send_json_error(array('message' => __('You do not have permission to access the file manager.', 'file-manager-advanced')));
			return;
		}

		// Check nonce for security
		if (!wp_verify_nonce($_POST['nonce'], 'fmaskey')) {
			wp_send_json_error(array('message' => __('Security check failed', 'file-manager-advanced')));
			return;
		}

		// Get the PHP code and file info from POST data.
		// Keep php_code slashed here; connector applies wp_unslash once on put content.
		$php_code = isset($_POST['php_code']) ? $_POST['php_code'] : '';
		$file_hash = sanitize_text_field($_POST['file_hash']);
		$filename = sanitize_text_field(wp_unslash($_POST['filename']));

		// Skip validation since this is called when user chooses "Save Anyway"
		// The validation was already done and user explicitly chose to save with errors

		try {
			// Store original POST data
			$original_post = $_POST;

			// Set up POST data for elFinder save operation
			$_POST = array(
				'cmd' => 'put',
				'target' => $file_hash,
				'content' => $php_code,
				'action' => 'fma_load_fma_ui',
				'_fmakey' => wp_create_nonce('fmaskey')
			);

			// Use elFinder connector to save the file
			if (!class_exists('class_fma_connector')) {
				include_once 'class_fma_connector.php';
			}

			if (class_exists('class_fma_connector')) {
				$fma_connector = new class_fma_connector();

				// Capture elFinder output
				ob_start();
				$fma_connector->fma_local_file_system();
				$elfinder_response = ob_get_clean();

				// Restore original POST data
				$_POST = $original_post;

				// Parse elFinder response
				$response_data = json_decode($elfinder_response, true);

				if ($response_data && isset($response_data['changed']) && !empty($response_data['changed'])) {
					wp_send_json_success(array(
						'message' => __('File saved successfully', 'file-manager-advanced'),
						'elfinder_response' => $response_data
					));
				} else if ($response_data && !isset($response_data['error'])) {
					// Sometimes elFinder doesn't return 'changed' but save is successful
					wp_send_json_success(array(
						'message' => __('File saved successfully', 'file-manager-advanced'),
						'elfinder_response' => $response_data
					));
				} else {
					$error_message = '';
					if ($response_data && isset($response_data['error'])) {
						$error_message = $response_data['error'];
					} else {
						$error_message = __('Failed to save file through elFinder', 'file-manager-advanced');
					}

					wp_send_json_error(array(
						'message' => $error_message,
						'elfinder_response' => $elfinder_response
					));
				}
			} else {
				// Restore original POST data
				$_POST = $original_post;

				wp_send_json_error(array(
					'message' => __('elFinder connector class not found', 'file-manager-advanced')
				));
			}

		} catch (Exception $e) {
			// Restore original POST data in case of exception
			$_POST = $original_post;

			wp_send_json_error(array(
				'message' => sprintf(__('Error saving file: %s', 'file-manager-advanced'), $e->getMessage()),
				'exception' => $e->getMessage()
			));
		}
	}

	/**
	 * Initialize SMTP recommendation using universal system
	 * @since 6.7.3
	 */
	private function init_smtp_recommendation()
	{
		require_once FMAFILEPATH . 'application/post-smtp-notice/recommend-post-smtp-loader.php';

		// Initialize SMTP recommendation without parent menu (to hide submenu)
		$this->recommend_smtp = recommend_smtp_loader(
			'fma',                    // Unique plugin identifier
			'file-manager-advanced',  // Plugin slug
			true,                     // Show admin notice
			false,                   // Parent menu (false = no submenu)
			'png'                     // Format
		);

		if (!function_exists('is_plugin_active')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$smtp_plugins = array(
			'post-smtp/postman-smtp.php',
			'wp-mail-smtp/wp_mail_smtp.php',
			'wp-mail-smtp-pro/wp_mail_smtp_pro.php',
			'easy-wp-smtp/easy-wp-smtp.php',
			'fluent-smtp/fluent-smtp.php',
			'gosmtp/gosmtp.php',
			'smtp-mailer/smtp-mailer.php',
			'suremails/suremails.php',
			'mailin/mailin.php',
			'site-mailer/site-mailer.php',
			'wp-smtp/wp-smtp.php'
		);

		$smtp_active = false;
		foreach ($smtp_plugins as $plugin) {
			if (is_plugin_active($plugin)) {
				$smtp_active = true;
				break;
			}
		}

		// Only add SMTP tab if no SMTP plugin is active
		if (!$smtp_active) {
			// Add SMTP tab to settings with a high priority to ensure it's always last
			add_filter('fma__settings_tabs', array($this, 'fma_settings_smtp_tab'), 999);
			// Add content for SMTP tab
			add_action('fma__settings_tab_smtp_content', array($this, 'fma_settings_smtp_content'));
		}
	}

	/**
	 * Add SMTP tab to settings
	 * @param array $tabs
	 * @return array
	 */
	public function fma_settings_smtp_tab($tabs)
	{
		$tabs['smtp'] = array(
			'title' => __('SMTP', 'file-manager-advanced') . ' <span style="background: #d63638; color: #fff; font-size: 9px; padding: 2px 6px; border-radius: 10px; margin-left: 5px; vertical-align: middle;">FREE</span>',
			'slug' => 'smtp',
			'icon' => '<i class="dashicons dashicons-email-alt"></i>',
		);
		return $tabs;
	}

	/**
	 * Display SMTP tab content
	 */
	public function fma_settings_smtp_content()
	{
		if ($this->recommend_smtp) {
			$this->recommend_smtp->recommend_post_smtp_submenu();
		}
	}

	public static function has_pro()
	{
		$has_pro = apply_filters('fma__has_pro', false);
		return $has_pro;
	}
}