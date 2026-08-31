<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Tabs
 *
 * Elementor widget for Tabs
 *
 * @since 3.6.0
 */
class Tabs extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-tabs', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/tabs.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-lib-tabs', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/library/tabs.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wb-tabs', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/tabs.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-tabs';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Tabs', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-tabs';
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
		return array( 'tabs', 'content', 'navigation', 'switch', 'panels' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-tabs' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-lib-tabs', 'wb-tabs' );
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
			'title_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
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
			'image',
			array(
				'label' => esc_html__( 'Image', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
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

		$this->add_control(
			'list',
			array(
				'label'       => esc_html__( 'Tabs', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'title_icon' => '',
						'title'      => esc_html__( 'Title #1', 'wbcom-essential' ),
						'image'      => '',
						'text'       => esc_html__( 'Item content...', 'wbcom-essential' ),
					),
					array(
						'title_icon' => '',
						'title'      => esc_html__( 'Title #2', 'wbcom-essential' ),
						'image'      => '',
						'text'       => esc_html__( 'Item content...', 'wbcom-essential' ),
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->add_control(
			'tabs_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => esc_html__( 'Title', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',

				'selector' => '{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title',
			)
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_active',
			array(
				'label' => esc_html__( 'Active', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'title_active_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li.is-open,{{WRAPPER}} .wbaccordion-mobile-title.is-open' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_active_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li.is-open,{{WRAPPER}} .wbaccordion-mobile-title.is-open' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_active_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li.is-open,{{WRAPPER}} .wbaccordion-mobile-title.is-open' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'title_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'tab_layout',
			array(
				'label'   => esc_html__( 'Layout', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 't-horizontal',
				'options' => array(
					't-horizontal'       => esc_html__( 'Horizontal', 'wbcom-essential' ),
					't-vertical'         => esc_html__( 'Vertical', 'wbcom-essential' ),
					't-vertical-reverse' => esc_html__( 'Vertical Reverse', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'title_h_align',
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
					'{{WRAPPER}} .wbcom_tab_head' => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'tabs_layout' => 'horizontal',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'title_width',
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
					'{{WRAPPER}} .wbcom_tab_head li' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_align',
			array(
				'label'     => esc_html__( 'Text Alignment', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
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
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title' => 'text-align: {{VALUE}};',
				),
				'toggle'    => true,
			)
		);

		$this->add_control(
			'title_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'title_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom_tab_head li,{{WRAPPER}} .wbaccordion-mobile-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom_tab_head li svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .wbaccordion-mobile-title i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbaccordion-mobile-title svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icon_active_color',
			array(
				'label'     => esc_html__( 'Active Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_head li.is-open i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbaccordion-mobile-title.is-open i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom_tab_head li.is-open svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .wbaccordion-mobile-title.is-open svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Size', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom_tab_head li i'   => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbaccordion-mobile-title i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom_tab_head li svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .wbaccordion-mobile-title svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'icon_block',
			array(
				'label'   => esc_html__( 'Block', 'wbcom-essential' ),
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

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom_tab_head li i,{{WRAPPER}} .wbaccordion-mobile-title i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

				'selector' => '{{WRAPPER}} .wbaccordion__body,{{WRAPPER}} .wbaccordion__body p',
			)
		);

		$this->add_responsive_control(
			'v_align',
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
					'{{WRAPPER}} .wbaccordion-inner' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'content_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbaccordion__body,{{WRAPPER}} .wbaccordion__body p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_item.wbaccordion' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom_tab_item.wbaccordion' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom_tab_item.wbaccordion' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'content_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom_tab_item.wbaccordion,{{WRAPPER}} .wbaccordion-mobile-title',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom_tab_item.wbaccordion' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => esc_html__( 'Image', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_img_width',
			array(
				'label'      => esc_html__( 'Maximum Container Width', 'wbcom-essential' ),
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
				'default'    => array(
					'unit' => '%',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbaccordion-img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_img_max_width',
			array(
				'label'      => esc_html__( 'Maximum Image Width', 'wbcom-essential' ),
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
				'selectors'  => array(
					'{{WRAPPER}} .wbaccordion-img img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_img_h_align',
			array(
				'label'     => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'row'         => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'row-reverse' => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => 'row',
				'selectors' => array(
					'{{WRAPPER}} .wbaccordion-inner' => 'flex-direction: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'content_img_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbaccordion-img img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'content_img_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbaccordion-img img',
			)
		);

		$this->add_responsive_control(
			'content_img_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbaccordion-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$rand     = '-' . wp_rand();

		if ( ! empty( $settings['list'] ) ) { ?>
			<div class="wbcom-tabs <?php echo esc_attr( $settings['tab_layout'] ); ?>">
				<ul class="wbcom_tab_head <?php echo esc_attr( $settings['icon_block'] === 'true' ? 'wbcom-tabs-block-icon' : '' ); ?>">
					<?php
					$menu_count = 0;
					$tab_count  = 0;
					foreach ( $settings['list'] as $item ) {
						?>
						<li 
							class="<?php echo $menu_count === 0 ? 'is-open' : ''; ?>" 
							data-opentab="wbcom-<?php echo esc_attr( $item['_id'] . $rand ); ?>">
							<?php
							\Elementor\Icons_Manager::render_icon( $item['title_icon'], array( 'aria-hidden' => 'true' ) );
							echo esc_html( $item['title'] );
							?>
						</li>
						<?php
						++$menu_count;
					}
					?>
				</ul>
	
				<?php foreach ( $settings['list'] as $item ) { ?>	
					<div class="wbaccordion-mobile-title <?php echo esc_attr( $tab_count === 0 ? 'is-open' : '' ); ?>" data-opentab="wbcom-<?php echo esc_attr( $item['_id'] . $rand ); ?>">
						<?php
						\Elementor\Icons_Manager::render_icon( $item['title_icon'], array( 'aria-hidden' => 'true' ) );
						echo esc_html( $item['title'] );
						?>
					</div>	
	
					<div class="wbaccordion wbcom_tab_item <?php echo esc_attr( $tab_count === 0 ? 'is-open' : '' ); ?>" id="wbcom-<?php echo esc_attr( $item['_id'] . $rand ); ?>" <?php echo $settings['hash'] ? 'data-hash="#wbcom-' . esc_attr( $item['_id'] . $rand ) . '"' : ''; ?>>
						<div class="wbaccordion__body">
							<div class="wbaccordion-inner">
								<?php if ( ! empty( $item['image']['url'] ) ) { ?>
									<div class="wbaccordion-img">
										<img src="<?php echo esc_url( $item['image']['url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" />
									</div>
								<?php } ?>
								<div class="wbaccordion-text"><?php echo wp_kses_post( $item['text'] ); ?></div>
							</div>
						</div>
					</div>
					<?php
					++$tab_count;
				}
				?>
			</div>
			<?php
			$breakpoint = get_option( 'elementor_viewport_md' );
			if ( empty( $breakpoint ) ) {
				$breakpoint = '768';
			}
			?>
			<style>
				@media screen and (max-width: <?php echo esc_attr( $breakpoint ); ?>px) {
					.wbaccordion-mobile-title { display: block !important; }
					ul.wbcom_tab_head { display: none !important; }
					.wbcom_tab_item.wbaccordion { margin-bottom: -1px !important; }
					.wbcom-tabs.t-vertical, .wbcom-tabs.t-vertical-reverse { flex-direction: column !important; }
				}
			</style>
			<?php
		}
	}
}
