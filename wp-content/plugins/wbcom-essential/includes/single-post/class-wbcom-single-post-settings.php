<?php
/**
 * Single Post Template Settings.
 *
 * Registers the global option, per-post meta box, and admin settings section.
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/includes/single-post
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Single_Post_Settings
 */
class Wbcom_Single_Post_Settings {

	/**
	 * Template choices for dropdowns.
	 *
	 * @var array
	 */
	private $template_choices = array();

	/**
	 * Initialize hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_post', array( $this, 'save_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_meta_box_styles' ) );
	}

	/**
	 * Get template choices (lazy-loaded to avoid early textdomain calls).
	 *
	 * @return array
	 */
	public function get_template_choices() {
		if ( empty( $this->template_choices ) ) {
			$this->template_choices = array(
				''         => __( 'None (Use Theme Default)', 'wbcom-essential' ),
				'classic'  => __( 'Classic (Content + Sidebar)', 'wbcom-essential' ),
				'magazine' => __( 'Magazine (Full-Width Hero)', 'wbcom-essential' ),
				'minimal'  => __( 'Minimal (Centered, Clean)', 'wbcom-essential' ),
				'modern'   => __( 'Modern (Progress Bar + ToC)', 'wbcom-essential' ),
			);
		}
		return $this->template_choices;
	}

	/**
	 * Register global settings.
	 */
	public function register_settings() {
		register_setting(
			'wbcom_essential_settings',
			'wbcom_essential_single_post_template',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_template_choice' ),
				'default'           => '',
			)
		);

		add_settings_section(
			'wbcom_essential_single_post_section',
			__( 'Post Templates', 'wbcom-essential' ),
			array( $this, 'render_section_description' ),
			'wbcom-essential'
		);

		add_settings_field(
			'wbcom_essential_single_post_template',
			__( 'Default Single Post Template', 'wbcom-essential' ),
			array( $this, 'render_template_field' ),
			'wbcom-essential',
			'wbcom_essential_single_post_section'
		);
	}

	/**
	 * Render section description.
	 */
	public function render_section_description() {
		echo '<p>' . esc_html__( 'Choose a default template for single blog posts. Individual posts can override this in their edit screen.', 'wbcom-essential' ) . '</p>';
	}

	/**
	 * Render the template dropdown field.
	 */
	public function render_template_field() {
		$current = get_option( 'wbcom_essential_single_post_template', '' );
		echo '<select name="wbcom_essential_single_post_template" id="wbcom_essential_single_post_template">';
		foreach ( $this->get_template_choices() as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '"' . selected( $current, $value, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
		echo '<p class="description">' . esc_html__( 'This applies to all single posts unless overridden per-post.', 'wbcom-essential' ) . '</p>';
	}

	/**
	 * Enqueue meta box styles on post edit screens.
	 *
	 * @param string $hook_suffix Current admin page.
	 */
	public function enqueue_meta_box_styles( $hook_suffix ) {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		if ( 'post' !== get_post_type() ) {
			return;
		}

		wp_add_inline_style( 'wp-admin', $this->get_meta_box_css() );
	}

	/**
	 * Return meta box CSS.
	 *
	 * @return string
	 */
	private function get_meta_box_css() {
		return '
			.wbcom-spt-options { margin: 0; padding: 0; }
			.wbcom-spt-option { display: flex; align-items: flex-start; gap: 8px; padding: 10px 12px; margin: 0 -12px; border-radius: 4px; cursor: pointer; transition: background 0.15s; }
			.wbcom-spt-option:hover { background: #f0f0f1; }
			.wbcom-spt-option input[type="radio"] { margin-top: 2px; flex-shrink: 0; }
			.wbcom-spt-option--active { background: #f0f6fc; }
			.wbcom-spt-option--active:hover { background: #e1ecf7; }
			.wbcom-spt-label { font-weight: 500; color: #1e1e1e; line-height: 1.4; display: block; }
			.wbcom-spt-desc { font-size: 12px; color: #757575; line-height: 1.4; margin-top: 1px; display: block; }
			.wbcom-spt-global-note { font-size: 12px; color: #757575; margin: 10px 0 0; padding: 8px 10px; background: #f0f0f1; border-radius: 3px; border-left: 3px solid #3858e9; }
		';
	}

	/**
	 * Add meta box to post edit screen.
	 */
	public function add_meta_box() {
		add_meta_box(
			'wbcom_single_post_template',
			__( 'Post Template', 'wbcom-essential' ),
			array( $this, 'render_meta_box' ),
			'post',
			'side',
			'default'
		);
	}

	/**
	 * Render the meta box.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'wbcom_sp_template_nonce', 'wbcom_sp_template_nonce' );

		$current = get_post_meta( $post->ID, '_wbcom_single_post_template', true );

		$per_post_choices = array(
			''         => array(
				'label' => __( 'Global Default', 'wbcom-essential' ),
				'desc'  => __( 'Inherits the site-wide setting', 'wbcom-essential' ),
			),
			'classic'  => array(
				'label' => __( 'Classic', 'wbcom-essential' ),
				'desc'  => __( 'Content with right sidebar', 'wbcom-essential' ),
			),
			'magazine' => array(
				'label' => __( 'Magazine', 'wbcom-essential' ),
				'desc'  => __( 'Full-width hero with overlay', 'wbcom-essential' ),
			),
			'minimal'  => array(
				'label' => __( 'Minimal', 'wbcom-essential' ),
				'desc'  => __( 'Centered, clean typography', 'wbcom-essential' ),
			),
			'modern'   => array(
				'label' => __( 'Modern', 'wbcom-essential' ),
				'desc'  => __( 'Progress bar, ToC sidebar, share bar', 'wbcom-essential' ),
			),
			'none'     => array(
				'label' => __( 'None', 'wbcom-essential' ),
				'desc'  => __( 'Use theme default template', 'wbcom-essential' ),
			),
		);

		echo '<div class="wbcom-spt-options">';
		foreach ( $per_post_choices as $value => $option ) {
			$checked    = checked( $current, $value, false );
			$active_cls = ( $current === $value ) ? ' wbcom-spt-option--active' : '';

			printf(
				'<label class="wbcom-spt-option%s">
					<input type="radio" name="wbcom_sp_template" value="%s"%s>
					<span>
						<span class="wbcom-spt-label">%s</span>
						<span class="wbcom-spt-desc">%s</span>
					</span>
				</label>',
				esc_attr( $active_cls ),
				esc_attr( $value ),
				$checked, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- checked() returns a safe attribute string.
				esc_html( $option['label'] ),
				esc_html( $option['desc'] )
			);
		}
		echo '</div>';

		$global = get_option( 'wbcom_essential_single_post_template', '' );
		$choices = $this->get_template_choices();
		if ( $global && isset( $choices[ $global ] ) ) {
			echo '<p class="wbcom-spt-global-note">';
			printf(
				/* translators: %s: template name */
				esc_html__( 'Site default: %s', 'wbcom-essential' ),
				'<strong>' . esc_html( $choices[ $global ] ) . '</strong>'
			);
			echo '</p>';
		}
	}

	/**
	 * Save meta box value.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['wbcom_sp_template_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wbcom_sp_template_nonce'] ) ), 'wbcom_sp_template_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$valid_values = array( '', 'classic', 'magazine', 'minimal', 'modern', 'none' );

		if ( isset( $_POST['wbcom_sp_template'] ) ) {
			$value = sanitize_text_field( wp_unslash( $_POST['wbcom_sp_template'] ) );

			if ( in_array( $value, $valid_values, true ) ) {
				if ( '' === $value ) {
					delete_post_meta( $post_id, '_wbcom_single_post_template' );
				} else {
					update_post_meta( $post_id, '_wbcom_single_post_template', $value );
				}
			}
		}
	}

	/**
	 * Sanitize template choice for the global option.
	 *
	 * @param string $value Input value.
	 * @return string Sanitized value.
	 */
	public function sanitize_template_choice( $value ) {
		$valid = array( '', 'classic', 'magazine', 'minimal', 'modern' );
		return in_array( $value, $valid, true ) ? $value : '';
	}
}
