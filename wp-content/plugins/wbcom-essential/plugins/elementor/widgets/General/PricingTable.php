<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor PricingTable
 *
 * Elementor widget for PricingTable
 *
 * @since 3.6.0
 */
class PricingTable extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-price-table', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/price-table.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-pricing-table';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Pricing Table', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-price-table';
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
		return array( 'pricing', 'table', 'price', 'plan', 'subscription' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-price-table' );
	}

	/**
	 * Get button skins.
	 */
	public function get_btn_skins() {
		$output_skins = apply_filters(
			'wbcom_btn_skins',
			array(
				''            => esc_html__( 'None', 'wbcom-essential' ),
				'wbcom-btn-1' => esc_html__( 'Animation 1', 'wbcom-essential' ),
				'wbcom-btn-2' => esc_html__( 'Animation 2', 'wbcom-essential' ),
				'wbcom-btn-3' => esc_html__( 'Animation 3', 'wbcom-essential' ),
				'wbcom-btn-4' => esc_html__( 'Animation 4', 'wbcom-essential' ),
				'wbcom-btn-5' => esc_html__( 'Animation 5', 'wbcom-essential' ),
				'wbcom-btn-6' => esc_html__( 'Animation 6', 'wbcom-essential' ),
				'wbcom-btn-7' => esc_html__( 'Animation 7', 'wbcom-essential' ),
				'wbcom-btn-8' => esc_html__( 'Animation 8', 'wbcom-essential' ),

			)
		);
		return $output_skins;
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
			'header_content',
			array(
				'label' => esc_html__( 'Header', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'header_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Enter your title', 'wbcom-essential' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'desc',
			array(
				'label'       => esc_html__( 'Description', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Enter your description', 'wbcom-essential' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'Heading Tag', 'wbcom-essential' ),
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
				'default' => 'h3',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'ribbon_content',
			array(
				'label' => esc_html__( 'Ribbon', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_ribbon',
			array(
				'label'        => esc_html__( 'Show Ribbon', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'ribbon_text',
			array(
				'label'       => esc_html__( 'Text', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'POPULAR', 'wbcom-essential' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'ribbon_style',
			array(
				'label'   => esc_html__( 'Style', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'wbcom-price-table-ribbon',
				'options' => array(
					'wbcom-price-table-ribbon'     => esc_html__( 'Ribbon', 'wbcom-essential' ),
					'wbcom-price-table-vertical'   => esc_html__( 'Vertical Text', 'wbcom-essential' ),
					'wbcom-price-table-horizontal' => esc_html__( 'Horizontal Text', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'ribbon_align',
			array(
				'label'      => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
					'v-left'  => array(
						'title' => esc_html__( 'Left', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'v-right' => array(
						'title' => esc_html__( 'Right', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-vertical',
						),
					),
				),
				'default'    => 'v-right',
				'toggle'     => false,
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'pricing_content',
			array(
				'label' => esc_html__( 'Pricing', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'price_prefix',
			array(
				'label'       => esc_html__( 'Price Prefix', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( '$', 'wbcom-essential' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'price',
			array(
				'label'       => esc_html__( 'Price', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( '49', 'wbcom-essential' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'price_suffix',
			array(
				'label'       => esc_html__( 'Price Suffix', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( '.99', 'wbcom-essential' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'original_price',
			array(
				'label'       => esc_html__( 'Original Price', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'period',
			array(
				'label'       => esc_html__( 'Period', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'features_content',
			array(
				'label' => esc_html__( 'Features', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'item_text',
			array(
				'label'       => esc_html__( 'Text', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'label_block' => true,
			)
		);

		$default_icon = array(
			'value'   => 'far fa-check-circle',
			'library' => 'fa-regular',
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label'   => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'    => Controls_Manager::ICONS,
				'default' => $default_icon,
			)
		);

		$repeater->add_control(
			'item_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} i'   => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'list',
			array(
				'label'       => esc_html__( 'List Items', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'item_text' => esc_html__( 'List Item #1', 'wbcom-essential' ),
						'item_icon' => $default_icon,
					),
					array(
						'item_text' => esc_html__( 'List Item #2', 'wbcom-essential' ),
						'item_icon' => $default_icon,
					),
				),
				'title_field' => '{{{ item_text }}}',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'footer_content',
			array(
				'label' => esc_html__( 'Footer', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'btn_text',
			array(
				'label'   => esc_html__( 'Button Text', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'BUY NOW', 'wbcom-essential' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'btn_link',
			array(
				'label'         => esc_html__( 'Link to', 'wbcom-essential' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'wbcom-essential' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'dynamic'       => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'btn_size',
			array(
				'label'   => esc_html__( 'Button Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'wbcom-btn-md',
				'options' => array(
					'wbcom-btn-md' => esc_html__( 'Normal', 'wbcom-essential' ),
					'wbcom-btn-lg' => esc_html__( 'Large', 'wbcom-essential' ),
					'wbcom-btn-sm' => esc_html__( 'Small', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'btn_id',
			array(
				'label'       => esc_html__( 'Button ID', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows A-Z 0-9  & underscore chars without spaces.', 'wbcom-essential' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'footer_info',
			array(
				'label'       => esc_html__( 'Additional Info', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_container_style',
			array(
				'label' => esc_html__( 'Container', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'container_zoom',
			array(
				'label'     => esc_html__( 'Zoom', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0.5,
				'max'       => 2.0,
				'step'      => 0.1,
				'default'   => 1.0,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table' => 'transform:scale({{VALUE}});',
				),
			)
		);

		$this->add_responsive_control(
			'text_align',
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
					'{{WRAPPER}} .wbcom-price-table-footer' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .wbcom-price-table-subheader' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} ul.wbcom-price-table-features' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_width',
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
					'{{WRAPPER}} .wbcom-price-table' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'container_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table',
			)
		);

		$this->add_responsive_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table',
			)
		);

		$this->add_control(
			'container_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => esc_html__( 'Header', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'header_text_align',
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
					'{{WRAPPER}} .wbcom-price-table-header' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'header_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-header',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'header_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-header',
			)
		);

		$this->add_responsive_control(
			'header_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-header' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_title_heading',
			array(
				'label'     => esc_html__( 'Title', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_title_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'header_title_text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-price-table-title',
			)
		);

		$this->add_responsive_control(
			'header_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_desc_heading',
			array(
				'label'     => esc_html__( 'Description', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'header_desc_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-desc' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'header_desc_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-desc',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'header_desc_text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-price-table-desc',
			)
		);

		$this->add_responsive_control(
			'header_desc_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_header_icon_style',
			array(
				'label' => esc_html__( 'Header Icon', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'header_icon_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .wbcom-price-table-header-icon svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'header_icon_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'header_icon_size',
			array(
				'label'      => esc_html__( 'Size', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-price-table-header-icon svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_icon_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'header_icon_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-header-icon',
			)
		);

		$this->add_responsive_control(
			'header_icon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'header_icon_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-header-icon',
			)
		);

		$this->add_control(
			'header_icon_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'header_icon_width',
			array(
				'label'     => esc_html__( 'Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'header_icon_height',
			array(
				'label'     => esc_html__( 'Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon' => 'height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-price-table-header-icon i' => 'line-height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-price-table-header-icon svg' => 'line-height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'header_icon_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_icon_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-header-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_ribbon_style',
			array(
				'label' => esc_html__( 'Ribbon', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ribbon_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-ribbon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .wbcom-price-table-vertical' => 'color: {{VALUE}}',
					'{{WRAPPER}} .wbcom-price-table-horizontal' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ribbon_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-ribbon' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .wbcom-price-table-vertical' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .wbcom-price-table-horizontal' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ribbon_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-ribbon,{{WRAPPER}} .wbcom-price-table-vertical,{{WRAPPER}} .wbcom-price-table-horizontal',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'ribbon_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-ribbon,{{WRAPPER}} .wbcom-price-table-vertical,{{WRAPPER}} .wbcom-price-table-horizontal',
			)
		);

		$this->add_control(
			'hr_ribbon_1',
			array(
				'type'       => \Elementor\Controls_Manager::DIVIDER,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-vertical',
						),
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-horizontal',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'ribbon_text_align',
			array(
				'label'      => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-vertical',
						),
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-horizontal',
						),
					),
				),
				'default'    => '',
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-vertical' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .wbcom-price-table-horizontal' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'ribbon_height',
			array(
				'label'      => esc_html__( 'Min Width (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-vertical',
						),
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-horizontal',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-vertical' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ribbon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-vertical',
						),
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-horizontal',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-vertical' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-price-table-horizontal' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ribbon_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-vertical',
						),
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-horizontal',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-vertical' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-price-table-horizontal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ribbon_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-vertical',
						),
						array(
							'name'  => 'ribbon_style',
							'value' => 'wbcom-price-table-horizontal',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-vertical' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-price-table-horizontal' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_pricing_style',
			array(
				'label' => esc_html__( 'Pricing', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pricing_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-subheader',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pricing_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-subheader',
			)
		);

		$this->add_responsive_control(
			'pricing_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-subheader' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pricing_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-subheader' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pricing_spacing',
			array(
				'label'      => esc_html__( 'Inner Spacing', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem' ),
				'range'      => array(
					'%'   => array(
						'min' => 0,
						'max' => 100,
					),
					'px'  => array(
						'min' => 0,
						'max' => 50,
					),
					'rem' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 3,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-original-price' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-price-table-price-value' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pricing_prefix',
			array(
				'label'     => esc_html__( 'Price Prefix', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pricing_prefix_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-price-prefix' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pricing_prefix_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-price-prefix',
			)
		);

		$this->add_responsive_control(
			'pricing_prefix_v_align',
			array(
				'label'     => esc_html__( 'Vertical Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-price-prefix' => 'align-self: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'pricing_price',
			array(
				'label'     => esc_html__( 'Price', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pricing_price_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-price-value' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pricing_price_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-price-value',
			)
		);

		$this->add_responsive_control(
			'pricing_price_v_align',
			array(
				'label'     => esc_html__( 'Vertical Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-price-value' => 'align-self: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'pricing_price_align',
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
					'{{WRAPPER}} .wbcom-price-table-price' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pricing_suffix',
			array(
				'label'     => esc_html__( 'Price Suffix', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pricing_suffix_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-price-suffix' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pricing_suffix_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-price-suffix',
			)
		);

		$this->add_responsive_control(
			'pricing_suffix_v_align',
			array(
				'label'     => esc_html__( 'Vertical Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'flex-end',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-price-suffix' => 'align-self: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'pricing_original',
			array(
				'label'     => esc_html__( 'Original Price', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pricing_original_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-original-price' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pricing_original_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-original-price',
			)
		);

		$this->add_responsive_control(
			'pricing_original_v_align',
			array(
				'label'     => esc_html__( 'Vertical Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'flex-end',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-original-price' => 'align-self: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'pricing_period',
			array(
				'label'     => esc_html__( 'Period', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'pricing_period_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-period' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pricing_period_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-period',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_features_style',
			array(
				'label' => esc_html__( 'Features', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'features_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'features_typography',

				'selector' => '{{WRAPPER}} ul.wbcom-price-table-features li span',
			)
		);

		$this->add_control(
			'hr_features_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'features_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ul.wbcom-price-table-features li svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'features_icon_padding',
			array(
				'label'      => esc_html__( 'Icon Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li i,{{WRAPPER}} ul.wbcom-price-table-features li svg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hr_features_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'features_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-content',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'features_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-content',
			)
		);

		$this->add_responsive_control(
			'features_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-content' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hr_features_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'features_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'features_item_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'features_item_margin',
			array(
				'label'      => esc_html__( 'Item Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'features_divider',
			array(
				'label'     => esc_html__( 'Divider', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'features_divider_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'features_divider_width',
			array(
				'label'      => esc_html__( 'Height', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'features_divider_style',
			array(
				'label'     => esc_html__( 'Style', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'solid'  => esc_html__( 'Solid', 'wbcom-essential' ),
					'dashed' => esc_html__( 'Dashed', 'wbcom-essential' ),
					'dotted' => esc_html__( 'Dotted', 'wbcom-essential' ),
					'double' => esc_html__( 'Double', 'wbcom-essential' ),
				),
				'default'   => 'solid',
				'selectors' => array(
					'{{WRAPPER}} ul.wbcom-price-table-features li' => 'border-bottom-style: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_btn_style',
			array(
				'label' => esc_html__( 'Button', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typography',

				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'btn_text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a',
			)
		);

		$this->add_control(
			'btn_skin',
			array(
				'label'   => esc_html__( 'Animation', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_btn_skins(),
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
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-btn-wrapper a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-btn-wrapper a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a',
			)
		);

		$this->add_responsive_control(
			'btn_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-btn-wrapper a,{{WRAPPER}} .wbcom-btn-wrapper a:before' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a',
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
					'{{WRAPPER}} .wbcom-btn-wrapper a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-btn-wrapper a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_animation_color',
			array(
				'label'     => esc_html__( 'Animation Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-btn-wrapper a:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a:hover',
			)
		);

		$this->add_responsive_control(
			'btn_border_hover_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-btn-wrapper a:hover,{{WRAPPER}} .wbcom-btn-wrapper a:hover:before' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_border_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a:hover',
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
					'{{WRAPPER}} .wbcom-btn-wrapper a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wbcom-btn-wrapper a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wbcom-btn-wrapper a' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_footer_style',
			array(
				'label' => esc_html__( 'Footer', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'footer_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-price-table-footer-desc' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'footer_typography',

				'selector' => '{{WRAPPER}} .wbcom-price-table-footer-desc',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'footer_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-footer',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'footer_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-price-table-footer',
			)
		);

		$this->add_responsive_control(
			'footer_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-footer' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'footer_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-price-table-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$target   = $settings['btn_link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['btn_link']['nofollow'] ? ' rel="nofollow"' : '';
		?>    
		<div class="wbcom-price-table" <?php echo $settings['show_ribbon'] ? 'style="overflow:hidden;"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $settings['show_ribbon'] ) : ?>
				<div class="wbcom-price-table-ribbon-wrapper">
					<div class="<?php echo esc_attr( $settings['ribbon_style'] ); ?> <?php echo esc_attr( $settings['ribbon_align'] ); ?>">
						<?php echo esc_html( $settings['ribbon_text'] ); ?>
					</div>
				</div>
			<?php endif; ?>
			
			<div class="wbcom-price-table-header">
				<div class="wbcom-price-table-header-icon">
					<?php \Elementor\Icons_Manager::render_icon( $settings['header_icon'], array( 'aria-hidden' => 'true' ) ); ?>
				</div>
	
				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<<?php echo esc_attr( $settings['html_tag'] ); ?> class="wbcom-price-table-title">
						<?php echo esc_html( $settings['title'] ); ?>
					</<?php echo esc_attr( $settings['html_tag'] ); ?>>
				<?php endif; ?>
	
				<?php if ( ! empty( $settings['desc'] ) ) : ?>
					<span class="wbcom-price-table-desc"><?php echo esc_html( $settings['desc'] ); ?></span>
				<?php endif; ?>
			</div>
	
			<div class="wbcom-price-table-subheader">
				<div class="wbcom-price-table-price">
					<?php if ( ! empty( $settings['original_price'] ) ) : ?>
						<div class="wbcom-price-table-original-price"><del><?php echo esc_html( $settings['original_price'] ); ?></del></div>
					<?php endif; ?>
	
					<?php if ( ! empty( $settings['price_prefix'] ) ) : ?>
						<div class="wbcom-price-table-price-prefix"><?php echo esc_html( $settings['price_prefix'] ); ?></div>
					<?php endif; ?>
	
					<?php if ( isset( $settings['price'] ) ) : ?>
						<div class="wbcom-price-table-price-value"><?php echo esc_html( $settings['price'] ); ?></div>
					<?php endif; ?>
	
					<?php if ( ! empty( $settings['price_suffix'] ) ) : ?>
						<div class="wbcom-price-table-price-suffix"><?php echo esc_html( $settings['price_suffix'] ); ?></div>
					<?php endif; ?>
				</div>
	
				<?php if ( ! empty( $settings['period'] ) ) : ?>
					<div class="wbcom-price-table-period">
						<span><?php echo esc_html( $settings['period'] ); ?></span>
					</div>
				<?php endif; ?>
			</div>
	
			<div class="wbcom-price-table-content">
				<ul class="wbcom-price-table-features">
					<?php if ( ! empty( $settings['list'] ) && is_array( $settings['list'] ) ) : ?>
						<?php foreach ( $settings['list'] as $item ) : ?>
							<li class="elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
								<?php \Elementor\Icons_Manager::render_icon( $item['item_icon'], array( 'aria-hidden' => 'true' ) ); ?>
								<span><?php echo wp_kses_post( $item['item_text'] ); ?></span>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
	
			<div class="wbcom-price-table-footer">
				<div class="wbcom-btn-wrapper">
					<a id="<?php echo esc_attr( $settings['btn_id'] ); ?>"
						class="<?php echo esc_attr( $settings['btn_size'] ); ?> <?php echo esc_attr( $settings['btn_skin'] ); ?>"
						href="<?php echo esc_url( $settings['btn_link']['url'] ); ?>" <?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo $nofollow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $settings['btn_text'] ); ?>
					</a>
				</div>
	
				<?php if ( ! empty( $settings['footer_info'] ) ) : ?>
					<span class="wbcom-price-table-footer-desc"><?php echo wp_kses_post( $settings['footer_info'] ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	
		<?php
	}
}
