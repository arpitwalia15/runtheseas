<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Countdown
 *
 * Elementor widget for Countdown
 *
 * @since 3.6.0
 */
class Countdown extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-countdown', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/countdown.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-countdown', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/countdown.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-countdown';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Countdown', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-countdown';
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
		return array( 'countdown', 'timer', 'clock', 'date', 'event' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-countdown' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-countdown' );
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
			'countdown_content',
			array(
				'label' => esc_html__( 'Countdown', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'due_date',
			array(
				'label'       => esc_html__( 'Due Date', 'wbcom-essential' ),
				/* translators: %s: Timezone string */
				'description' => sprintf( esc_html__( 'Date set according to your timezone: %s.', 'wbcom-essential' ), Utils::get_timezone_string() ),
				'default'     => gmdate( 'Y-m-d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
			)
		);

		$this->add_control(
			'days_switcher',
			array(
				'label'        => esc_html__( 'Days', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'days',
			array(
				'label'       => esc_html__( 'Days', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Days', 'wbcom-essential' ),
				'condition'   => array( 'days_switcher' => 'yes' ),
				'show_label'  => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'hours_switcher',
			array(
				'label'        => esc_html__( 'Hours', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hours',
			array(
				'label'       => esc_html__( 'Hours', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Hours', 'wbcom-essential' ),
				'condition'   => array( 'hours_switcher' => 'yes' ),
				'show_label'  => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'minutes_switcher',
			array(
				'label'        => esc_html__( 'Minutes', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'minutes',
			array(
				'label'       => esc_html__( 'Minutes', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Minutes', 'wbcom-essential' ),
				'condition'   => array( 'minutes_switcher' => 'yes' ),
				'show_label'  => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'seconds_switcher',
			array(
				'label'        => esc_html__( 'Seconds', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'seconds',
			array(
				'label'       => esc_html__( 'Seconds', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Seconds', 'wbcom-essential' ),
				'condition'   => array( 'seconds_switcher' => 'yes' ),
				'show_label'  => false,
				'label_block' => true,
			)
		);

		$this->add_control(
			'msg_expire',
			array(
				'label' => esc_html__( 'Message After Expire', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::TEXTAREA,
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_boxes_style',
			array(
				'label' => esc_html__( 'Boxes', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'box_width',
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
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-countdown > div' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_width_2',
			array(
				'label'      => esc_html__( 'Width (%)', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-countdown > div' => 'flex: 0 {{SIZE}}%;',
				),
			)
		);

		$this->add_responsive_control(
			'box_height',
			array(
				'label'      => esc_html__( 'Height', 'wbcom-essential' ),
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
					'{{WRAPPER}} .wbcom-countdown > div' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'boxes_h_align',
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
					'{{WRAPPER}} .wbcom-countdown' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'boxes_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div',
			)
		);

		$this->add_control(
			'boxes_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div',
			)
		);

		$this->add_responsive_control(
			'box_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-countdown > div' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div',
			)
		);

		$this->add_control(
			'boxes_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-countdown > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-countdown > div' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_layout',
			array(
				'label'   => esc_html__( 'Layout', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'v-layout',
				'options' => array(
					'h-layout'         => esc_html__( 'Horizontal', 'wbcom-essential' ),
					'h-layout-reverse' => esc_html__( 'Horizontal Reverse', 'wbcom-essential' ),
					'v-layout'         => esc_html__( 'Vertical', 'wbcom-essential' ),
					'v-layout-reverse' => esc_html__( 'Vertical Reverse', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'horizontal_h_align',
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
				'default'    => 'center',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'content_layout',
							'value' => 'h-layout',
						),
						array(
							'name'  => 'content_layout',
							'value' => 'h-layout-reverse',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-countdown > div' => 'justify-content: {{VALUE}};',
				),
				'toggle'     => false,
			)
		);

		$this->add_responsive_control(
			'vertical_h_align',
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
				'default'    => 'center',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'content_layout',
							'value' => 'v-layout',
						),
						array(
							'name'  => 'content_layout',
							'value' => 'v-layout-reverse',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-countdown > div' => 'align-items: {{VALUE}};',
				),
				'toggle'     => false,
			)
		);

		$this->add_control(
			'digits_heading',
			array(
				'label'     => esc_html__( 'Digits', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'digit_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} span.wbcom-countdown-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'digit_typography',

				'selector' => '{{WRAPPER}} span.wbcom-countdown-value',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'digit_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} span.wbcom-countdown-value',
			)
		);

		$this->add_control(
			'labels_heading',
			array(
				'label'     => esc_html__( 'Labels', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} span.wbcom-countdown-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',

				'selector' => '{{WRAPPER}} span.wbcom-countdown-label',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'label_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} span.wbcom-countdown-label',
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} span.wbcom-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'msg_heading',
			array(
				'label'     => esc_html__( 'Message After Expire', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'msg_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown-msg' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'msg_typography',

				'selector' => '{{WRAPPER}} .wbcom-countdown-msg',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'msg_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown-msg',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_days_style',
			array(
				'label' => esc_html__( 'Days', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'days_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-days',
			)
		);

		$this->add_control(
			'days_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'days_digits',
			array(
				'label' => esc_html__( 'Digits', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'days_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-days > span.wbcom-countdown-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'days_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-days span.wbcom-countdown-value',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'days_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-days > span.wbcom-countdown-value',
			)
		);

		$this->add_control(
			'days_labels',
			array(
				'label'     => esc_html__( 'Labels', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'days_label_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-days > span.wbcom-countdown-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'days_label_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-days span.wbcom-countdown-label',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'days_label_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-days > span.wbcom-countdown-label',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_hours_style',
			array(
				'label' => esc_html__( 'Hours', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'hours_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-hours',
			)
		);

		$this->add_control(
			'hours_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'hours_digits',
			array(
				'label' => esc_html__( 'Digits', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'hours_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-hours > span.wbcom-countdown-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hours_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-hours span.wbcom-countdown-value',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'hours_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-hours > span.wbcom-countdown-value',
			)
		);

		$this->add_control(
			'hours_labels',
			array(
				'label'     => esc_html__( 'Labels', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hours_label_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-hours > span.wbcom-countdown-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hours_label_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-hours span.wbcom-countdown-label',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'hours_label_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-hours > span.wbcom-countdown-label',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_minutes_style',
			array(
				'label' => esc_html__( 'Minutes', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'minutes_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-minutes',
			)
		);

		$this->add_control(
			'minutes_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'minutes_digits',
			array(
				'label' => esc_html__( 'Digits', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'minutes_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-minutes > span.wbcom-countdown-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'minutes_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-minutes span.wbcom-countdown-value',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'minutes_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-minutes > span.wbcom-countdown-value',
			)
		);

		$this->add_control(
			'minutes_labels',
			array(
				'label'     => esc_html__( 'Labels', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'minutes_label_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-minutes > span.wbcom-countdown-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'minutes_label_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-minutes span.wbcom-countdown-label',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'minutes_label_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-minutes > span.wbcom-countdown-label',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_seconds_style',
			array(
				'label' => esc_html__( 'Seconds', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'seconds_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-seconds',
			)
		);

		$this->add_control(
			'seconds_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'seconds_digits',
			array(
				'label' => esc_html__( 'Digits', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'seconds_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-seconds > span.wbcom-countdown-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'seconds_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-seconds span.wbcom-countdown-value',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'seconds_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-seconds > span.wbcom-countdown-value',
			)
		);

		$this->add_control(
			'seconds_labels',
			array(
				'label'     => esc_html__( 'Labels', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'seconds_label_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-seconds > span.wbcom-countdown-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'seconds_label_typography',

				'selector' => '{{WRAPPER}} div.wbcom-countdown-seconds span.wbcom-countdown-label',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'seconds_label_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-countdown > div.wbcom-countdown-seconds > span.wbcom-countdown-label',
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
		?>
		<div class="wbcom-countdown <?php echo esc_attr( $settings['content_layout'] ); ?>" data-duedate="<?php echo esc_attr( $settings['due_date'] ); ?>">
			<?php if ( $settings['days_switcher'] ) { ?>
			<div class="wbcom-countdown-days">
				<span class="wbcom-countdown-value">00</span><span class="wbcom-countdown-label"><?php echo esc_html( $settings['days'] ); ?></span>
			</div>
			<?php } ?>
			<?php if ( $settings['hours_switcher'] ) { ?>
			<div class="wbcom-countdown-hours">
				<span class="wbcom-countdown-value">00</span><span class="wbcom-countdown-label"><?php echo esc_html( $settings['hours'] ); ?></span>
			</div>
			<?php } ?>
			<?php if ( $settings['minutes_switcher'] ) { ?>
			<div class="wbcom-countdown-minutes">
				<span class="wbcom-countdown-value">00</span><span class="wbcom-countdown-label"><?php echo esc_html( $settings['minutes'] ); ?></span>
			</div>
			<?php } ?>
			<?php if ( $settings['seconds_switcher'] ) { ?>
			<div class="wbcom-countdown-seconds">
				<span class="wbcom-countdown-value">00</span><span class="wbcom-countdown-label"><?php echo esc_html( $settings['seconds'] ); ?></span>
			</div>
			<?php } ?>
		</div>
		<?php if ( $settings['msg_expire'] ) { ?>
		<div class="wbcom-countdown-msg" style="display:none;"><?php echo wp_kses_post( $settings['msg_expire'] ); ?></div>
		<?php } ?>
		<?php
	}
}
