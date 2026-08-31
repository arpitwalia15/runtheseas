<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Accordion
 *
 * Elementor widget for Accordion
 *
 * @since 3.6.0
 */
class Accordion extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-accordion', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/accordion.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-lib-tabs', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/library/tabs.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wb-accordion', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/accordion.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-accordion';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Accordion', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-accordion';
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
		return array( 'accordion', 'toggle', 'faq', 'collapse', 'expand' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-lib-tabs', 'wb-accordion' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-accordion' );
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
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon_txt_switcher',
			array(
				'label'   => esc_html__( 'Prefix', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'icon' => array(
						'title' => esc_html__( 'Icon', 'wbcom-essential' ),
						'icon'  => 'eicon-star',
					),
					'text' => array(
						'title' => esc_html__( 'Text', 'wbcom-essential' ),
						'icon'  => 'eicon-heading',
					),
				),
				'default' => 'icon',
				'toggle'  => true,
			)
		);

		$repeater->add_control(
			'title_icon',
			array(
				'label'     => esc_html__( 'Icon', 'wbcom-essential' ),
				'condition' => array( 'icon_txt_switcher' => 'icon' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'title_icon_txt',
			array(
				'label'     => esc_html__( 'Text', 'wbcom-essential' ),
				'condition' => array( 'icon_txt_switcher' => 'text' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '1',
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'      => esc_html__( 'Content', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::WYSIWYG,
				'default'    => '',
				'show_label' => false,
			)
		);

		$repeater->add_control(
			'status',
			array(
				'label'   => esc_html__( 'Opened by default', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'is-open'    => array(
						'title' => esc_html__( 'On', 'wbcom-essential' ),
						'icon'  => 'fas fa-check',
					),
					'is-default' => array(
						'title' => esc_html__( 'Off', 'wbcom-essential' ),
						'icon'  => 'fas fa-times',
					),
				),
				'default' => 'is-default',
				'toggle'  => false,
			)
		);

		$repeater->add_control(
			'self_block',
			array(
				'label'   => esc_html__( 'Block close event on click', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => array(
					'true'  => array(
						'title' => esc_html__( 'On', 'wbcom-essential' ),
						'icon'  => 'fas fa-check',
					),
					'false' => array(
						'title' => esc_html__( 'Off', 'wbcom-essential' ),
						'icon'  => 'fas fa-times',
					),
				),
				'default' => 'false',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'list',
			array(
				'label'       => esc_html__( 'Accordion Items', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'title_icon' => '',
						'title'      => esc_html__( 'Title #1', 'wbcom-essential' ),
						'text'       => esc_html__( 'Item content...', 'wbcom-essential' ),
						'status'     => 'is-default',
						'self_block' => 'false',
					),
					array(
						'title_icon' => '',
						'title'      => esc_html__( 'Title #2', 'wbcom-essential' ),
						'text'       => esc_html__( 'Item content...', 'wbcom-essential' ),
						'status'     => 'is-default',
						'self_block' => 'false',
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_settings',
			array(
				'label' => esc_html__( 'Settings', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'Title HTML Tag', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
					'p'   => 'p',
				),
				'default' => 'h5',
			)
		);

		$this->add_control(
			'hash',
			array(
				'label'        => esc_html__( 'Url Sharing', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Off', 'wbcom-essential' ),
				'return_value' => 'on',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'open_single',
			array(
				'label'       => esc_html__( 'Open Single', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'options'     => array(
					'true'  => array(
						'title' => esc_html__( 'On', 'wbcom-essential' ),
						'icon'  => 'fas fa-check',
					),
					'false' => array(
						'title' => esc_html__( 'Off', 'wbcom-essential' ),
						'icon'  => 'fas fa-times',
					),
				),
				'default'     => 'false',
				'description' => esc_html__( 'Open just one accordion at once.', 'wbcom-essential' ),
				'toggle'      => false,
			)
		);
		
		$this->add_control(
			'faq_schema',
			[
				'label' => esc_html__( 'Enable FAQ Schema', 'wbcom-essential' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Generate FAQ schema for SEO optimization.', 'wbcom-essential' ),
				'default' => '',
			]
		);
		
		$this->add_control(
			'self_close',
			array(
				'label'       => esc_html__( 'Self Close', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'options'     => array(
					'true'  => array(
						'title' => esc_html__( 'On', 'wbcom-essential' ),
						'icon'  => 'fas fa-check',
					),
					'false' => array(
						'title' => esc_html__( 'Off', 'wbcom-essential' ),
						'icon'  => 'fas fa-times',
					),
				),
				'default'     => 'false',
				'description' => esc_html__( 'Close accordion on click outside.', 'wbcom-essential' ),
				'toggle'      => false,
			)
		);

		$this->add_control(
			'scroll',
			array(
				'label'       => esc_html__( 'Auto Scroll', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'options'     => array(
					'true'  => array(
						'title' => esc_html__( 'On', 'wbcom-essential' ),
						'icon'  => 'fas fa-check',
					),
					'false' => array(
						'title' => esc_html__( 'Off', 'wbcom-essential' ),
						'icon'  => 'fas fa-times',
					),
				),
				'default'     => 'false',
				'description' => esc_html__( 'Scroll to accordion on open.', 'wbcom-essential' ),
				'toggle'      => false,
			)
		);

		$this->add_control(
			'scroll_offset',
			array(
				'label'   => esc_html__( 'Scroll Offset', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 2000,
				'step'    => 10,
				'default' => 0,
			)
		);

		$this->add_control(
			'scroll_speed',
			array(
				'label'   => esc_html__( 'Scroll Speed', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 10000,
				'step'    => 10,
				'default' => 400,
			)
		);

		$this->add_control(
			'open_speed',
			array(
				'label'   => esc_html__( 'Open Speed', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 2000,
				'step'    => 10,
				'default' => 200,
			)
		);

		$this->add_control(
			'close_speed',
			array(
				'label'   => esc_html__( 'Close Speed', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 2000,
				'step'    => 10,
				'default' => 200,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_accordion_style',
			array(
				'label' => esc_html__( 'Accordion', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_accordion_style' );

		$this->start_controls_tab(
			'tab_accordion_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'accordion_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbaccordion',
			)
		);

		$this->add_control(
			'accordion_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'accordion_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'accordion_background',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_accordion_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'accordion_hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion:hover,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open',
			)
		);

		$this->add_control(
			'accordion_hover_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'accordion_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion:hover,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'accordion_hover_background',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion:hover,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'hr_accordion_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'accordion_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => esc_html__( 'Title', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		// Add Title Typography setting.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Title Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion__head',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);



		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'     => esc_html__( 'Arrow Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 100,
				'step'      => 1,
				'default'   => 4,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head button::after' => 'padding: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_thickness',
			array(
				'label'     => esc_html__( 'Arrow Thickness', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 100,
				'step'      => 1,
				'default'   => 4,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head button::after' => 'border-width: 0 {{VALUE}}px {{VALUE}}px 0;',
				),
			)
		);

		$this->add_control(
			'hr_accordion_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head button:after' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'accordion_title_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion__head',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head:hover,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button:hover,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hover_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head:hover,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button:hover,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_hover_color',
			array(
				'label'     => esc_html__( 'Arrow Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head  button:hover:after,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head  button:after' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'accordion_title_hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'hr_accordion_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head > button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head::after' => 'right: {{RIGHT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => esc_html__( 'Prefix', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'     => esc_html__( 'Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 100,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button .wbaccordion-prefix' => 'font-size: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head svg' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_width',
			array(
				'label'     => esc_html__( 'Container Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 100,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head svg,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button .wbaccordion-prefix' => 'width: {{VALUE}}px;min-width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_height',
			array(
				'label'     => esc_html__( 'Container Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 2,
				'max'       => 100,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head svg,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button .wbaccordion-prefix' => 'height: {{VALUE}}px;line-height: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'hr_icon_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_icon_style' );

		$this->start_controls_tab(
			'tab_icon_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button .wbaccordion-prefix' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button .wbaccordion-prefix',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head:hover i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button:hover i,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head button i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head:hover .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button:hover .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head button .wbaccordion-prefix' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_bg_hover',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion__head:hover i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button:hover i,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head button i',
				'{{WRAPPER}} .wbcom-accordions .wbaccordion__head:hover .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button:hover .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion.is-open .wbaccordion__head button .wbaccordion-prefix' => 'color: {{VALUE}};',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'hr_icon_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head .wbaccordion-prefix,{{WRAPPER}} .wbcom-accordions .wbaccordion__head button .wbaccordion-prefix' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__head i,{{WRAPPER}} .wbcom-accordions .wbaccordion__head .wbaccordion-prefix' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typography',

				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion__body,{{WRAPPER}} .wbaccordion__body p',
			)
		);

		$this->add_control(
			'content_color',
			[
				'label' => esc_html__( 'Content Color', 'wbcom-essential' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000000',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__body, {{WRAPPER}} .wbaccordion__body p' => 'color: {{VALUE}};',
				],
			]
		);		

		$this->add_control(
			'content_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__body,{{WRAPPER}} .wbaccordion__body p' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'content_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-accordions .wbaccordion__body',
			)
		);

		$this->add_control(
			'hr_accordion_4',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-accordions .wbaccordion__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$rand     = '-' . wp_rand();

		if ( ! empty( $settings['list'] ) ) { ?>
			<div class="wbcom-accordions" 
				data-selfclose="<?php echo esc_attr( $settings['self_close'] ); ?>" 
				data-opensingle="<?php echo esc_attr( $settings['open_single'] ); ?>" 
				data-openspeed="<?php echo absint( $settings['open_speed'] ); ?>" 
				data-closespeed="<?php echo absint( $settings['close_speed'] ); ?>" 
				data-autoscroll="<?php echo esc_attr( $settings['scroll'] ); ?>" 
				data-scrollspeed="<?php echo absint( $settings['scroll_speed'] ); ?>" 
				data-scrolloffset="<?php echo absint( $settings['scroll_offset'] ); ?>">
				<?php
				if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
					$json = [
						'@context' => 'https://schema.org',
						'@type' => 'FAQPage',
						'mainEntity' => [],
					];

					foreach ( $settings['list'] as $item ) {
						$json['mainEntity'][] = [
							'@type' => 'Question',
							'name' => wp_strip_all_tags( $item['title'] ),
							'acceptedAnswer' => [
								'@type' => 'Answer',
								'text' => wp_kses_post( $item['text'] ),
							],
						];
					}
					printf(
						'<script type="application/ld+json">%s</script>',
						wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
					);
				}
				?>

				<?php foreach ( $settings['list'] as $item ) { ?>
					<div id="wbcom-<?php echo esc_attr( $item['_id'] ) . esc_attr( $rand ); ?>" 
						<?php if ( ! empty( $settings['hash'] ) ) { ?>
							data-hash="#wbcom-<?php echo esc_attr( $item['_id'] ) . esc_attr( $rand ); ?>"
						<?php } ?>
						class="wbaccordion <?php echo esc_attr( $item['status'] ); ?>" 
						data-wbaccordion-options='{"selfBlock": <?php echo esc_attr( $item['self_block'] ); ?>}'>
						<?php
						echo '<' . esc_html( $settings['html_tag'] ) . ' class="wbaccordion__head">';

						// Render the icon and capture the output.
						ob_start();
						\Elementor\Icons_Manager::render_icon( $item['title_icon'], array( 'aria-hidden' => 'true' ) );
						$icon_html = ob_get_clean();

						// Modify the icon HTML to include width and height.
						$icon_html = str_replace( '<svg', '<svg width="18" height="18"', $icon_html );

						echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						if ( ! empty( $item['title_icon_txt'] ) ) {
							echo '<span class="wbaccordion-prefix">' . esc_html( $item['title_icon_txt'] ) . '</span>';
						}

						echo esc_html( $item['title'] );
						echo '</' . esc_html( $settings['html_tag'] ) . '>';
						?>
						<div class="wbaccordion__body">
							<?php echo wp_kses_post( $item['text'] ); ?>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php
		}
	}
}
