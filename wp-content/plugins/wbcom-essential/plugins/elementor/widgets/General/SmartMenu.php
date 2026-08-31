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
 * Elementor Smart Menu
 *
 * Elementor widget for Smart Menu
 *
 * @since 3.6.0
 */
class SmartMenu extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		// phpcs:ignore PluginCheck.CodeAnalysis.EnqueuedResourceOffloading.OffloadedContent -- Font Awesome is bundled locally in the plugin.
		if ( ! wp_style_is( 'font-awesome-5', 'enqueued' ) ) {
			wp_register_style(
				'font-awesome-5',
				WBCOM_ESSENTIAL_URL . 'assets/vendor/font-awesome/css/all.min.css',
				array(),
				'5.15.4'
			);
		}

		wp_register_style( 'wbcom-animations', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/animations.min.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wb-smart-menu', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/smart-menu.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-smart-menu', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/smart-menu.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-smart-menu';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Smart Menu', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-nav-menu';
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
		return array( 'smart', 'menu', 'navigation', 'nav', 'header' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wbcom-animations', 'font-awesome-5', 'wb-smart-menu' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-smart-menu' );
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
			'section_settings',
			array(
				'label' => esc_html__( 'Smart Menu', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'menu',
			array(
				'label'   => esc_html__( 'Select a Menu', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => wba_get_menus(),
			)
		);

		$this->add_control(
			'menu_layout',
			array(
				'label'   => esc_html__( 'Menu Layout', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'sm-horizontal'           => esc_html__( 'Horizontal', 'wbcom-essential' ),
					'sm-horizontal-justified' => esc_html__( 'Horizontal-Justified', 'wbcom-essential' ),
					'sm-vertical'             => esc_html__( 'Vertical', 'wbcom-essential' ),
				),
				'default' => 'sm-horizontal',
			)
		);

		$this->add_control(
			'menu_h_align',
			array(
				'label'      => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'menu_layout',
							'value' => 'sm-horizontal',
						),
						array(
							'name'  => 'menu_layout',
							'value' => 'sm-vertical',
						),
					),
				),
				'default'    => 'flex-start',
				'selectors'  => array(
					'{{WRAPPER}} .wba-smart-menu-desktop.wba-smart-menu-wrapper' => 'justify-content: {{VALUE}};',
				),
				'toggle'     => false,
			)
		);

		$this->add_control(
			'vertical_menu_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'em' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'condition'  => array(
					'menu_layout' => 'sm-vertical',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-smart-menu-desktop.wba-smart-menu-wrapper .wba-sm-skin.sm-vertical' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'smart_menu_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'menu_breakpoint',
			array(
				'label'   => esc_html__( 'Mobile Breakpoint', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => get_option( 'elementor_viewport_lg', true ),
			)
		);

		$this->add_control(
			'menu_toggle',
			array(
				'label'        => esc_html__( 'Mobile Menu Toggle', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'menu_toggle_text',
			array(
				'label'     => esc_html__( 'Mobile Menu Toggle Text', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'MENU', 'wbcom-essential' ),
				'condition' => array(
					'menu_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'menu_toggle_text_h_align',
			array(
				'label'     => esc_html__( 'Mobile Menu Toggle Alignment', 'wbcom-essential' ),
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
				'condition' => array(
					'menu_toggle' => 'yes',
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .wba-smart-menu-toggle-container' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'menu_collapsible_behavior',
			array(
				'label'   => esc_html__( 'Collapsible Behavior', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default'          => esc_html__( 'Default', 'wbcom-essential' ),
					'toggle'           => esc_html__( 'Toggle', 'wbcom-essential' ),
					'link'             => esc_html__( 'Link', 'wbcom-essential' ),
					'accordion'        => esc_html__( 'Accordion', 'wbcom-essential' ),
					'accordion-toggle' => esc_html__( 'Accordion-Toggle', 'wbcom-essential' ),
					'accordion-link'   => esc_html__( 'Accordion-Link', 'wbcom-essential' ),
				),
				'default' => 'link',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_main_menu_container',
			array(
				'label' => esc_html__( 'Menu Container', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'main_menu_background',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'main_menu_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin',
			)
		);

		$this->add_responsive_control(
			'main_menu_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-sm-skin' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'main_menu_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin',
			)
		);

		$this->add_control(
			'main_menu_transition_duration',
			array(
				'label'     => esc_html__( 'Transition Duration (ms)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 10,
				'step'      => 0.1,
				'default'   => 0.2,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin a' => 'transition: all {{VALUE}}s ease-in-out;',
				),
			)
		);

		$this->add_control(
			'main_menu_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'main_menu_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-sm-skin' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'main_menu_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-sm-skin' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_main_menu_items',
			array(
				'label' => esc_html__( 'Main Menu Items', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'main_menu_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wba-sm-skin > li > a',
			)
		);

		$this->add_control(
			'main_menu_icon',
			array(
				'label'   => esc_html__( 'Dropdown Menu Icon', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'caret'          => esc_html__( 'Caret', 'wbcom-essential' ),
					'caret-square'   => esc_html__( 'Caret-Square', 'wbcom-essential' ),
					'chevron'        => esc_html__( 'Chevron', 'wbcom-essential' ),
					'chevron-circle' => esc_html__( 'Chevron-Circle', 'wbcom-essential' ),
					'plus'           => esc_html__( 'Plus', 'wbcom-essential' ),
					'plus-circle'    => esc_html__( 'Plus-Circle', 'wbcom-essential' ),
				),
				'default' => 'caret',
			)
		);

		$this->add_responsive_control(
			'main_menu_icon_size',
			array(
				'label'     => esc_html__( 'Dropdown Menu Icon Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin a .sub-arrow:before' => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'main_menu_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color (Mobile)', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin a .sub-arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_menu_icon_bg',
			array(
				'label'     => esc_html__( 'Icon Background Color (Mobile)', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin a .sub-arrow' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_menu_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'main_menu_link_style' );

		$this->start_controls_tab(
			'main_menu_link_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'main_menu_item_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin > li > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_menu_item_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin > li > a' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'main_menu_item_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin > li > a',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'main_menu_link_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'main_menu_item_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin > li > a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li > a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li > a:active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_menu_item_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin > li > a:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li > a:focus' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li > a:active' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'main_menu_item_border_hover',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin > li > a:hover,{{WRAPPER}} .wba-sm-skin > li > a:focus,{{WRAPPER}} .wba-sm-skin > li > a:active',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'main_menu_link_active',
			array(
				'label' => esc_html__( 'Active', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'main_menu_item_color_active',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a.highlighted' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_menu_item_bg_active',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a.highlighted' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:focus' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:active' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'main_menu_item_border_active',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a,{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a.highlighted,{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:hover,{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:focus,{{WRAPPER}} .wba-sm-skin > li.current-menu-item > a:active',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'main_menu_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'main_menu_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-sm-skin > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wba-sm-skin > li > a > .sub-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sub_menus',
			array(
				'label' => esc_html__( 'Sub Menus', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'menu_sub_menu_animation',
			array(
				'label' => esc_html__( 'Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ANIMATION,
			)
		);

		$this->add_control(
			'menu_sub_menu_max_width',
			array(
				'label'      => esc_html__( 'Maximum Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'em' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'em',
					'size' => 20,
				),
			)
		);

		$this->add_control(
			'menu_sub_menu_min_width',
			array(
				'label'      => esc_html__( 'Minimum Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'em' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'em',
					'size' => 10,
				),
			)
		);

		$this->add_control(
			'mainMenuSubOffsetX',
			array(
				'label'   => esc_html__( 'First-level Offset X', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$this->add_control(
			'mainMenuSubOffsetY',
			array(
				'label'   => esc_html__( 'First-level Offset Y', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$this->add_control(
			'subMenusSubOffsetX',
			array(
				'label'   => esc_html__( 'Second-level Offset X', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$this->add_control(
			'subMenusSubOffsetY',
			array(
				'label'   => esc_html__( 'Second-level Offset Y', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 0,
			)
		);

		$this->add_control(
			'menu_rtl_sub_menus',
			array(
				'label'        => esc_html__( 'Right to Left', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'sub_menu_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'sub_menu_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin ul' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'sub_menu_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-smart-menu-desktop .wba-sm-skin ul',
			)
		);

		$this->add_control(
			'sub_menu_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-smart-menu-desktop .wba-sm-skin ul' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'sub_menu_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-smart-menu-desktop .wba-sm-skin ul',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sub_menu_items',
			array(
				'label' => esc_html__( 'Sub Menu Items', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'main_sub_menu_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wba-sm-skin li ul li a',
			)
		);

		$this->start_controls_tabs( 'main_sub_menu_link_style' );

		$this->start_controls_tab(
			'main_sub_menu_link_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'main_sub_menu_item_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin li ul li a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_sub_menu_item_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin li ul li a' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'main_sub_menu_item_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin li ul li a',
			)
		);

		$this->add_control(
			'main_sub_menu_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-sm-skin li ul li a' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'main_sub_menu_link_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'main_sub_menu_item_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin li ul li a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin li ul li a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin li ul li a:active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_sub_menu_item_bg_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin li ul li a:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin li ul li a:focus' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin li ul li a:active' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'main_sub_menu_item_border_hover',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin li ul li a:hover,{{WRAPPER}} .wba-sm-skin li ul li a:focus,{{WRAPPER}} .wba-sm-skin li ul li a:active',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'main_sub_menu_link_active',
			array(
				'label' => esc_html__( 'Active', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'main_sub_menu_item_color_active',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a.highlighted' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'main_sub_menu_item_bg_active',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a.highlighted' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:hover' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:focus' => 'background: {{VALUE}};',
					'{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:active' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'main_sub_menu_item_border_active',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a,{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a.highlighted,{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:hover,{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:focus,{{WRAPPER}} .wba-sm-skin ul li.current-menu-item > a:active',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'sub_menu_item_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'main_sub_menu_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-sm-skin ul li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wba-sm-skin ul li a .sub-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_mobile_menu',
			array(
				'label' => esc_html__( 'Mobile Menu Toggle', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'mobile_menu_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-smart-menu-toggle',
			)
		);

		$this->add_control(
			'mobile_menu_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-smart-menu-toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'mobile_menu_background',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-smart-menu-toggle',
			)
		);

		$this->add_control(
			'mobile_menu_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'em' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-smart-menu-toggle' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'mobile_menu_text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-smart-menu-toggle' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_menu_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'mobile_menu_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-smart-menu-toggle',
			)
		);

		$this->add_responsive_control(
			'mobile_menu_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-smart-menu-toggle' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'mobile_menu_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-smart-menu-toggle',
			)
		);

		$this->add_control(
			'mobile_menu_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'mobile_menu_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-smart-menu-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'mobile_menu_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-smart-menu-toggle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$settings = $this->get_settings_for_display();
		$menu_id  = $this->get_id();
		$rtl      = '';
		if ( is_rtl() ) {
			$rtl = 'sm-rtl';
		}
		?>
		<div style="display:none;" class="wba-smart-menu-container
		<?php
		if ( $settings['menu_rtl_sub_menus'] ) {
			echo esc_attr( 'wba-smart-menu-rtl-submenu' ); }
		?>
		" data-animin="<?php echo esc_attr($settings['menu_sub_menu_animation']); ?>" data-animout="<?php echo esc_attr(wba_get_anim_exits( $settings['menu_sub_menu_animation'] )); ?>" data-collapsiblebehavior="<?php echo esc_attr( $settings['menu_collapsible_behavior'] ); ?>" data-mainmenusuboffsetx="<?php echo esc_attr( $settings['mainMenuSubOffsetX'] ); ?>" data-mainmenusuboffsety="<?php echo esc_attr( $settings['mainMenuSubOffsetY'] ); ?>" data-submenussuboffsetx="<?php echo esc_attr( $settings['subMenusSubOffsetX'] ); ?>" data-submenussuboffsety="<?php echo esc_attr( $settings['subMenusSubOffsetY'] ); ?>" data-submenumin="<?php echo esc_attr( $settings['menu_sub_menu_min_width']['size'] . $settings['menu_sub_menu_min_width']['unit'] ); ?>"  data-submenumax="<?php echo esc_attr( $settings['menu_sub_menu_max_width']['size'] . $settings['menu_sub_menu_max_width']['unit'] ); ?>" data-rtlsubmenu="<?php echo esc_attr( $settings['menu_rtl_sub_menus'] ); ?>" data-mtoggle="<?php echo esc_attr( $settings['menu_toggle'] ); ?>" data-bpoint="<?php echo esc_attr( $settings['menu_breakpoint'] ); ?>">
		<?php if ( $settings['menu_toggle'] ) { ?>
			<div class="wba-smart-menu-toggle-container">
				<div class="wbcom-smart-menu-toggle">
					<i class="fas fa-bars"></i> <span><?php echo esc_html($settings['menu_toggle_text']); ?></span>
				</div>
			</div>
		<?php } ?>
			<?php
			wp_nav_menu(
				array(
					'menu'            => $settings['menu'],
					'container'       => 'nav',
					'container_id'    => 'wba-smart-menu-wrapper-' . esc_attr($menu_id),
					'container_class' => 'wba-smart-menu-wrapper',
					'menu_id'         => 'wba-smart-menu-' . esc_attr($menu_id),
					'menu_class'      => 'wba-smart-menu sm wba-sm-skin animated ' . esc_attr($settings['menu_layout']) . ' ' . esc_attr($settings['main_menu_icon']) . ' ' . esc_attr($rtl),
					'depth'           => 99,
				)
			);
			?>
		</div>
		<?php
	}
}
