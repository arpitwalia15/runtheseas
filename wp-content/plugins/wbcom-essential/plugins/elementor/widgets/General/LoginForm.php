<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor LoginForm
 *
 * Elementor widget for LoginForm
 *
 * @since 3.6.0
 */
class LoginForm extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-login-form', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/login-form.css', array(), WBCOM_ESSENTIAL_VERSION );

		if ( ! defined( 'PMPRO_VERSION' ) ) {
			wp_register_script( 'wbcom-ajax-login', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/wbcom-ajax-login.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );

			wp_localize_script(
				'wbcom-ajax-login',
				'wbcom_ajax_login_params',
				array(
					'ajax_url'     => admin_url( 'admin-ajax.php' ),
					'redirect_url' => home_url(),
					'security'     => wp_create_nonce( 'wbcom-ajax-login-nonce' ),
				)
			);
		}
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-login-form';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Login Form', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-lock-user';
	}

	/**
	 * Get categories.
	 */
	public function get_categories() {
		return array( 'wbcom-elements' );
	}

	/**
	 * Get keywords.
	 */
	public function get_keywords() {
		return array( 'login', 'form', 'signin', 'authentication', 'user' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wbcom-ajax-login' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-login-form' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.6.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		// section start.
		$this->start_controls_section(
			'form_labels',
			array(
				'label' => esc_html__( 'Login Form', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo',
			array(
				'label' => esc_html__( 'Logo', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'hr_title_1',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'title',
			array(
				'label' => esc_html__( 'Title', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => esc_html__( 'Title HTML Tag', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h2',
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label' => esc_html__( 'Sub Title', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'subtitle_html_tag',
			array(
				'label'   => esc_html__( 'Sub Title HTML Tag', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'p',
			)
		);

		$this->add_control(
			'hr_title_2',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'label_username',
			array(
				'label'   => esc_html__( 'Username', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Username or Email Address', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'label_password',
			array(
				'label'   => esc_html__( 'Password', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Password', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'label_remember',
			array(
				'label'   => esc_html__( 'Remember Me', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Remember Me', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'label_register',
			array(
				'label'   => esc_html__( 'Register', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Register', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'links_seperator',
			array(
				'label' => esc_html__( 'Link Separator', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'label_lost_password',
			array(
				'label'   => esc_html__( 'Lost Password', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Lost Password', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'label_log_in',
			array(
				'label'   => esc_html__( 'Button Text', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Log In', 'wbcom-essential' ),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'form_additional_options',
			array(
				'label' => esc_html__( 'Additional Options', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'redirect_link_switcher',
			array(
				'label'        => esc_html__( 'Redirect After Login', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Off', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'redirect_link',
			array(
				'label'         => esc_html__( 'Redirect After Login', 'wbcom-essential' ),
				'description'   => esc_html__( 'Note: Because of security reasons, you can ONLY use your current domain here.', 'wbcom-essential' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'condition'     => array( 'redirect_link_switcher' => 'yes' ),
				'placeholder'   => esc_html__( 'https://your-link.com', 'wbcom-essential' ),
				'show_external' => false,
				'show_label'    => false,
			)
		);

		$this->add_control(
			'lost_password_switcher',
			array(
				'label'        => esc_html__( 'Lost Password', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'register_switcher',
			array(
				'label'        => esc_html__( 'Register', 'wbcom-essential' ),
				'description'  => esc_html__( 'You must enable "membership" from WordPress settings.', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'remember_switcher',
			array(
				'label'        => esc_html__( 'Remember Me', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'loggedin_msg_switcher',
			array(
				'label'        => esc_html__( 'Logged in Message', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'loggedin_msg',
			array(
				'label'       => esc_html__( 'Logged in Message', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'You are logged in as ', 'wbcom-essential' ),
				'condition'   => array( 'loggedin_msg_switcher' => 'yes' ),
				'show_label'  => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'test_mode',
			array(
				'label'        => esc_html__( 'Test Mode', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Off', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_form_container_style',
			array(
				'label' => esc_html__( 'Form Container', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'form_width',
			array(
				'label'      => esc_html__( 'Maximum Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem' ),
				'range'      => array(
					'%'   => array(
						'min' => 0,
						'max' => 100,
					),
					'px'  => array(
						'min' => 0,
						'max' => 1000,
					),
					'rem' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-inner' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_align',
			array(
				'label'     => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-login-form-wrapper' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label'      => esc_html__( 'Rows Gap', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-inner p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'form_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-login-form-inner',
			)
		);

		$this->add_control(
			'form_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'form_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-login-form-inner',
			)
		);

		$this->add_responsive_control(
			'form_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-inner' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'form_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-login-form-inner',
			)
		);

		$this->add_control(
			'form_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'form_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_logo_style',
			array(
				'label' => esc_html__( 'Logo', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'Maximum Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem' ),
				'range'      => array(
					'%'   => array(
						'min' => 0,
						'max' => 100,
					),
					'px'  => array(
						'min' => 0,
						'max' => 1000,
					),
					'rem' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-logo' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_h_align',
			array(
				'label'     => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-login-form-logo-container' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'logo_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'logo_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-login-form-logo',
			)
		);

		$this->add_responsive_control(
			'logo_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-logo' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'logo_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-login-form-logo',
			)
		);

		$this->add_control(
			'logo_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'logo_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-logo-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => esc_html__( 'Title', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-login-form-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',

				'selector' => '{{WRAPPER}} .wbcom-login-form-title',
			)
		);

		$this->add_responsive_control(
			'title_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-login-form-title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hr_title',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_subtitle_style',
			array(
				'label' => esc_html__( 'Sub Title', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-login-form-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',

				'selector' => '{{WRAPPER}} .wbcom-login-form-subtitle',
			)
		);

		$this->add_responsive_control(
			'subtitle_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-login-form-subtitle' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hr_subtitle',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'subtitle_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtitle_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-login-form-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_labels_style',
			array(
				'label' => esc_html__( 'Labels', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',

				'selector' => '{{WRAPPER}} #wb_login_form label',
			)
		);

		$this->add_responsive_control(
			'label_text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form label' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'label_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wb_login_form label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wb_login_form label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_input_style',
			array(
				'label' => esc_html__( 'Input & Textarea', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'input_typography',

				'selector' => '{{WRAPPER}} #wb_login_form input:not(.button),{{WRAPPER}} #wb_login_form textarea',
			)
		);

		$this->add_responsive_control(
			'input_text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form input:not(.button)' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} #wb_login_form textarea' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'input_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem' ),
				'range'      => array(
					'%'   => array(
						'min' => 0,
						'max' => 100,
					),
					'px'  => array(
						'min' => 0,
						'max' => 1000,
					),
					'rem' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} input[type="text"]'     => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} input[type="email"]'    => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} input[type="tel"]'      => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} input[type="password"]' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} textarea'               => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'input_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'form_input_style' );

		$this->start_controls_tab(
			'form_input_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form input:not(.button)' => 'color: {{VALUE}};',
					'{{WRAPPER}} #wb_login_form textarea' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'placeholder_color',
			array(
				'label'     => esc_html__( 'Placeholder Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form input:not(.button)::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} #wb_login_form textarea::placeholder' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form input:not(.button)' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} #wb_login_form textarea' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wb_login_form input:not(.button),{{WRAPPER}} #wb_login_form textarea',
			)
		);

		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wb_login_form input:not(.button)' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #wb_login_form textarea' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wb_login_form input:not(.button),{{WRAPPER}} #wb_login_form textarea',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'form_input_hover',
			array(
				'label' => esc_html__( 'Focus', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form input:not(.button):focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} #wb_login_form textarea:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wb_login_form input:not(.button):focus' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} #wb_login_form textarea:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wb_login_form input:not(.button):focus,{{WRAPPER}} #wb_login_form textarea:focus',
			)
		);

		$this->add_responsive_control(
			'border_hover_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wb_login_form input:not(.wpcf7-submit):focus' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #wb_login_form textarea:focus' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'border_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wb_login_form input:not(.button):focus,{{WRAPPER}} #wb_login_form textarea:focus',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'input_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'input_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wb_login_form input:not(.button)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #wb_login_form textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wb_login_form input:not(.button)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #wb_login_form textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_btn_style',
			array(
				'label' => esc_html__( 'Submit Button', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typography',

				'selector' => '{{WRAPPER}} #wp-submit',
			)
		);

		$this->add_responsive_control(
			'btn_text_align',
			array(
				'label'     => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} #wp-submit' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'btn_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'btn_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wp-submit' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wp-submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wp-submit',
			)
		);

		$this->add_responsive_control(
			'btn_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wp-submit' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wp-submit',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'btn_text_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wp-submit:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #wp-submit:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wp-submit:hover',
			)
		);

		$this->add_responsive_control(
			'btn_border_hover_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wp-submit:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_border_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} #wp-submit:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'btn_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'btn_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} #wp-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}}  #wp-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_width',
			array(
				'label'      => esc_html__( 'Button Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem' ),
				'range'      => array(
					'%'   => array(
						'min' => 0,
						'max' => 100,
					),
					'px'  => array(
						'min' => 0,
						'max' => 1000,
					),
					'rem' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} #wp-submit' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_links_style',
			array(
				'label' => esc_html__( 'Links', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.wbcom-login-form-links li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.wbcom-login-form-links li' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-loggedout-msg a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label'     => esc_html__( 'Link Hover Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.wbcom-login-form-links li a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-loggedout-msg a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'links_typography',

				'selector' => '{{WRAPPER}} ul.wbcom-login-form-links li',
			)
		);

		$this->add_responsive_control(
			'links_align',
			array(
				'label'     => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'flex-end',
				'selectors' => array(
					'{{WRAPPER}} ul.wbcom-login-form-links' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'links_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'links_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.wbcom-login-form-links li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'links_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} ul.wbcom-login-form-links',
			)
		);

		$this->add_responsive_control(
			'links_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.wbcom-login-form-links' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_logged_in_msg_style',
			array(
				'label'     => esc_html__( 'Logged in Message', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'loggedin_msg_switcher' => 'yes' ),
			)
		);

		$this->add_control(
			'logged_in_msg_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-loggedout-msg'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-loggedout-msg a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-loggedout-msg a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'logged_in_msg_typography',

				'selector' => '{{WRAPPER}} .wbcom-loggedout-msg',
			)
		);

		$this->add_responsive_control(
			'logged_in_msg_text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-loggedout-msg' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'logged_in_msg_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-loggedout-msg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 3.6.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings      = $this->get_settings_for_display();
		$logo_url      = wp_get_attachment_image_url( $settings['logo']['id'], 'full' );
		$redirect_link = isset( $settings['redirect_link']['url'] ) ? esc_url( $settings['redirect_link']['url'] ) : home_url();
		$remember      = ! empty( $settings['remember_switcher'] ) ? true : false;

		if ( defined( 'PMPRO_VERSION' ) ) {
			$login_form_args = array(
				'echo'           => true,
				'redirect'       => $redirect_link,
				'form_id'        => 'wb_login_form',
				'label_username' => $settings['label_username'],
				'label_password' => $settings['label_password'],
				'label_remember' => $settings['label_remember'],
				'label_log_in'   => $settings['label_log_in'],
				'remember'       => $remember,
				'value_username' => '',
				// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
				'value_remember' => false,
			);
		}

		?>
		
		<?php if ( ( ! is_user_logged_in() ) || ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) || ( $settings['test_mode'] ) ) { ?>
			<div class="wbcom-login-form-wrapper">
				<div class="wbcom-login-form-inner">
					<?php if ( $logo_url ) { ?>
						<div class="wbcom-login-form-logo-container">
							<img class="wbcom-login-form-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $settings['title'] ); ?>" />
						</div>
					<?php } ?>
	
					<?php if ( $settings['title'] ) { ?>
						<<?php echo esc_attr( $settings['title_html_tag'] ); ?> class="wbcom-login-form-title">
							<?php echo esc_html( $settings['title'] ); ?>
						</<?php echo esc_attr( $settings['title_html_tag'] ); ?>>
					<?php } ?>
	
					<?php if ( $settings['subtitle'] ) { ?>
						<<?php echo esc_attr( $settings['subtitle_html_tag'] ); ?> class="wbcom-login-form-subtitle">
							<?php echo esc_html( $settings['subtitle'] ); ?>
						</<?php echo esc_attr( $settings['subtitle_html_tag'] ); ?>>
					<?php } ?>
	
					<?php if ( defined( 'PMPRO_VERSION' ) ) { ?>
						<?php wp_login_form( $login_form_args ); ?>
					<?php } else { ?>
						<?php $widget_id = $this->get_id(); ?>
						<div id="wbcom-login-error-<?php echo esc_attr( $widget_id ); ?>" class="wbcom-login-error" style="display:none; color:red; margin-bottom:10px;"></div>

						<form id="wb_login_form_<?php echo esc_attr( $widget_id ); ?>" class="wb_login_form" data-widget-id="<?php echo esc_attr( $widget_id ); ?>">
							<p>
								<label><?php echo esc_html( $settings['label_username'] ); ?></label>
								<input type="text" name="log" required>
							</p>
							<p>
								<label><?php echo esc_html( $settings['label_password'] ); ?></label>
								<input type="password" name="pwd" required>
							</p>
							<?php if ( $remember ) { ?>
								<p>
									<label>
										<input type="checkbox" name="rememberme" value="forever"> <?php echo esc_html( $settings['label_remember'] ); ?>
									</label>
								</p>
							<?php } ?>

							<p>
							<?php do_action( 'wbcom_recaptcha_login_form' ); ?>
							</p>

							<p>
								<button id="wp-submit-<?php echo esc_attr( $widget_id ); ?>" class="wp-submit" type="submit"><?php echo esc_html( $settings['label_log_in'] ); ?></button>
							</p>
							<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_link ); ?>">
							<input type="hidden" name="action" value="wbcom_ajax_login">
							<?php wp_nonce_field( 'wbcom-ajax-login-nonce', 'security' ); ?>
						</form>

					<?php } ?>
	
					<ul class="wbcom-login-form-links">
						<?php if ( get_option( 'users_can_register' ) == '1' && $settings['register_switcher'] ) { ?>
							<li><a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php echo esc_html( $settings['label_register'] ); ?></a></li>
						<?php } ?>
						<?php if ( $settings['links_seperator'] ) { ?>
							<li><?php echo esc_html( $settings['links_seperator'] ); ?></li>
						<?php } ?>
						<?php if ( $settings['lost_password_switcher'] ) { ?>
							<li><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php echo esc_html( $settings['label_lost_password'] ); ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php
		} elseif ( $settings['loggedin_msg_switcher'] ) {
			$current_user = wp_get_current_user();
			?>
			<div class="wbcom-loggedout-msg"><?php echo esc_html( $settings['loggedin_msg'] ) . ' ' . esc_html( $current_user->user_login ); ?>
				<a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>"><?php esc_html_e( '(Logout)', 'wbcom-essential' ); ?></a>
			</div>
			<?php
		}
	}
}
