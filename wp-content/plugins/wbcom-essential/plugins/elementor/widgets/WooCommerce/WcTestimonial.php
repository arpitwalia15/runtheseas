<?php
/**
 * Elementor widget wc testimonial.
 *
 * @since      3.7.1
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/woocommerce
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

/**
 * Wc Testimonial.
 *
 * @since      3.7.1
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/woocommerce
 */
class WcTestimonial extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wbe-swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/swiper-base.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wb-wc-testimonial', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wc-testimonial.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wbcom-widgets', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wbcom-widgets.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/swiper.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wbcom-widgets-scripts', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/wbcom-widgets-active.js', array( 'jquery', 'swiper' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-wc-testimonial';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'WooCommerce Testimonial', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-review';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array(
			'wbe-swiper',
			'elementor-icons-shared-0-css',
			'elementor-icons-fa-brands',
			'elementor-icons-fa-regular',
			'elementor-icons-fa-solid',
			'wb-wc-testimonial',
			'wbcom-widgets',
		);
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array(
			'swiper',
			'wbcom-widgets-scripts',
		);
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
		return array( 'review', 'testimonial', 'product review', 'customer review', 'client say' );
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'testimonial_content',
			array(
				'label' => __( 'Testimonial', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'testimonial_layout',
				array(
					'label'   => __( 'Style', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1' => __( 'Style One', 'wbcom-essential' ),
						'2' => __( 'Style Two', 'wbcom-essential' ),
						'3' => __( 'Style Three', 'wbcom-essential' ),
						'4' => __( 'Style Four', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'testimonial_type',
				array(
					'label'   => __( 'Review Type', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'custom',
					'options' => array(
						'custom' => __( 'Custom', 'wbcom-essential' ),
					),
				)
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'client_name',
				array(
					'label'   => __( 'Name', 'wbcom-essential' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Anna Miller', 'wbcom-essential' ),
				)
			);

			$repeater->add_control(
				'client_designation',
				array(
					'label'   => __( 'Designation', 'wbcom-essential' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Designer', 'wbcom-essential' ),
				)
			);

			$repeater->add_control(
				'client_rating',
				array(
					'label' => __( 'Client Rating', 'wbcom-essential' ),
					'type'  => Controls_Manager::NUMBER,
					'min'   => 1,
					'max'   => 5,
					'step'  => 1,
				)
			);

			$repeater->add_control(
				'client_image',
				array(
					'label' => __( 'Image', 'wbcom-essential' ),
					'type'  => Controls_Manager::MEDIA,
				)
			);

			$repeater->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'client_imagesize',
					'default'   => 'full',
					'separator' => 'none',
				)
			);

			$repeater->add_control(
				'client_say',
				array(
					'label'   => __( 'Client Say', 'wbcom-essential' ),
					'type'    => Controls_Manager::TEXTAREA,
					'default' => __( '“ Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, laboris consequat. ”', 'wbcom-essential' ),
				)
			);

			$this->add_control(
				'testimonial_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'condition'   => array(
						'testimonial_type' => 'custom',
					),
					'fields'      => $repeater->get_controls(),
					'default'     => array(

						array(
							'client_name'        => __( 'Anna Miller', 'wbcom-essential' ),
							'client_designation' => __( 'Designer', 'wbcom-essential' ),
							'client_say'         => __( '“ Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, laboris consequat. ”', 'wbcom-essential' ),
						),

						array(
							'client_name'        => __( 'Kevin Walker', 'wbcom-essential' ),
							'client_designation' => __( 'Developer', 'wbcom-essential' ),
							'client_say'         => __( '“ Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore Lorem ipsum dolor sit amet, consectetur adipisicing elit ”', 'wbcom-essential' ),
						),

						array(
							'client_name'        => __( 'Ruth Pierce', 'wbcom-essential' ),
							'client_designation' => __( 'Customer', 'wbcom-essential' ),
							'client_say'         => __( '“ Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, laboris consequat. ”', 'wbcom-essential' ),
						),
					),
					'title_field' => '{{{ client_name }}}',
				)
			);

			$this->add_control(
				'slider_on',
				array(
					'label'        => __( 'Slider On', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'separator'    => 'before',
				)
			);

		$this->end_controls_section();

		// Column.
		$this->start_controls_section(
			'section_column_option',
			array(
				'label'     => __( 'Columns', 'wbcom-essential' ),
				'condition' => array(
					'slider_on!' => 'yes',
				),
			)
		);

			$this->add_responsive_control(
				'column',
				array(
					'label'        => esc_html__( 'Columns', 'wbcom-essential' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => '3',
					'options'      => array(
						'1'  => esc_html__( 'One', 'wbcom-essential' ),
						'2'  => esc_html__( 'Two', 'wbcom-essential' ),
						'3'  => esc_html__( 'Three', 'wbcom-essential' ),
						'4'  => esc_html__( 'Four', 'wbcom-essential' ),
						'5'  => esc_html__( 'Five', 'wbcom-essential' ),
						'6'  => esc_html__( 'Six', 'wbcom-essential' ),
						'7'  => esc_html__( 'Seven', 'wbcom-essential' ),
						'8'  => esc_html__( 'Eight', 'wbcom-essential' ),
						'9'  => esc_html__( 'Nine', 'wbcom-essential' ),
						'10' => esc_html__( 'Ten', 'wbcom-essential' ),
					),
					'label_block'  => true,
					'prefix_class' => 'wb-columns%s-',
				)
			);

			$this->add_control(
				'no_gutters',
				array(
					'label'        => esc_html__( 'No Gutters', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
					'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_responsive_control(
				'item_space',
				array(
					'label'      => esc_html__( 'Space', 'wbcom-essential' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'default'    => array(
						'unit' => 'px',
						'size' => 15,
					),
					'condition'  => array(
						'no_gutters!' => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} .wb-row > [class*="col-"]' => 'padding: 0  {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'item_bottom_space',
				array(
					'label'      => esc_html__( 'Bottom Space', 'wbcom-essential' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'default'    => array(
						'unit' => 'px',
						'size' => 30,
					),
					'condition'  => array(
						'no_gutters!' => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} .wb-row > [class*="col-"]' => 'margin-bottom:{{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		// Slider Option.
		$this->start_controls_section(
			'section_slider_option',
			array(
				'label'     => esc_html__( 'Slider Option', 'wbcom-essential' ),
				'condition' => array(
					'slider_on' => 'yes',
				),
			)
		);

			$this->add_control(
				'slitems',
				array(
					'label'   => esc_html__( 'Slider Items', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'step'    => 1,
					'default' => 2,
				)
			);

			$this->add_control(
				'slarrows',
				array(
					'label'        => esc_html__( 'Slider Arrow', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'sldots',
				array(
					'label'        => esc_html__( 'Slider dots', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'slpause_on_hover',
				array(
					'type'         => Controls_Manager::SWITCHER,
					'label_off'    => __( 'No', 'wbcom-essential' ),
					'label_on'     => __( 'Yes', 'wbcom-essential' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'label'        => __( 'Pause on Hover?', 'wbcom-essential' ),
				)
			);

			$this->add_control(
				'slautolay',
				array(
					'label'        => esc_html__( 'Slider autoplay', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'separator'    => 'before',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'slautoplay_speed',
				array(
					'label'     => __( 'Autoplay speed', 'wbcom-essential' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 3000,
					'condition' => array(
						'slautolay' => 'yes',
					),
				)
			);

			$this->add_control(
				'slanimation_speed',
				array(
					'label'     => __( 'Autoplay animation speed', 'wbcom-essential' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 300,
					'condition' => array(
						'slautolay' => 'yes',
					),
				)
			);

			$this->add_control(
				'slscroll_columns',
				array(
					'label'   => __( 'Slider item to scroll', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'step'    => 1,
					'default' => 2,
				)
			);

			$this->add_control(
				'heading_tablet',
				array(
					'label'     => __( 'Tablet', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'after',
				)
			);

			$this->add_control(
				'sltablet_display_columns',
				array(
					'label'   => __( 'Slider Items', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'step'    => 1,
					'default' => 2,
				)
			);

			$this->add_control(
				'sltablet_scroll_columns',
				array(
					'label'   => __( 'Slider item to scroll', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'step'    => 1,
					'default' => 2,
				)
			);

			$this->add_control(
				'sltablet_width',
				array(
					'label'       => __( 'Tablet Resolution', 'wbcom-essential' ),
					'description' => __( 'The resolution to the tablet.', 'wbcom-essential' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 750,
				)
			);

			$this->add_control(
				'heading_mobile',
				array(
					'label'     => __( 'Mobile Phone', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'after',
				)
			);

			$this->add_control(
				'slmobile_display_columns',
				array(
					'label'   => __( 'Slider Items', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'step'    => 1,
					'default' => 1,
				)
			);

			$this->add_control(
				'slmobile_scroll_columns',
				array(
					'label'   => __( 'Slider item to scroll', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'step'    => 1,
					'default' => 1,
				)
			);

			$this->add_control(
				'slmobile_width',
				array(
					'label'       => __( 'Mobile Resolution', 'wbcom-essential' ),
					'description' => __( 'The resolution to mobile.', 'wbcom-essential' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 480,
				)
			);

		$this->end_controls_section();

		// Style style start.
		$this->start_controls_section(
			'testimonial_area_style',
			array(
				'label' => __( 'Item', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'item_border',
					'label'    => __( 'Border', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wb-single-testimonial-wrap',
				)
			);

			$this->add_responsive_control(
				'item_border_radius',
				array(
					'label'      => __( 'Border Radius', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'item_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'item_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		// Style image style start.
		$this->start_controls_section(
			'testimonial_image_style',
			array(
				'label' => __( 'Image', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'testimonial_image_border',
					'label'    => __( 'Border', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] img',
				)
			);

			$this->add_responsive_control(
				'testimonial_image_border_radius',
				array(
					'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] img' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					),
				)
			);

			$this->add_control(
				'testimonial_image_area_border_color',
				array(
					'label'     => __( 'Image Area Border Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap .wb-client-info-wrap.wb-client-info-border' => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'testimonial_layout' => '3',
					),
				)
			);

		$this->end_controls_section(); // Style Testimonial image style end.

		// Style Testimonial name style start.
		$this->start_controls_section(
			'testimonial_name_style',
			array(
				'label' => __( 'Name', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'testimonial_name_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] h4' => 'color: {{VALUE}};',
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"]:before' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'testimonial_name_typography',
					'selector' => '{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] h4',
				)
			);

			$this->add_responsive_control(
				'testimonial_name_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] h4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_responsive_control(
				'testimonial_name_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] h4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section(); // Style Testimonial name style end.

		// Style Testimonial designation style start.
		$this->start_controls_section(
			'testimonial_designation_style',
			array(
				'label' => __( 'Designation', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'testimonial_designation_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] span' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'testimonial_designation_typography',
					'selector' => '{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] span',
				)
			);

			$this->add_responsive_control(
				'testimonial_designation_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_responsive_control(
				'testimonial_designation_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-info"] span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section(); // Style Testimonial designation style end.

		// Style Testimonial designation style start.
		$this->start_controls_section(
			'testimonial_clientsay_style',
			array(
				'label' => __( 'Client say', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'testimonial_clientsay_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-content"] p' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'testimonial_clientsay_typography',
					'selector' => '{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-content"] p',
				)
			);

			$this->add_responsive_control(
				'testimonial_clientsay_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-content"] p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_responsive_control(
				'testimonial_clientsay_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap [class*="wb-client-content"] p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section(); // Style Testimonial designation style end.

		// Style Testimonial designation style start.
		$this->start_controls_section(
			'testimonial_clientrating_style',
			array(
				'label' => __( 'Rating', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'testimonial_clientrating_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap .wb-client-rating ul li i' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'testimonial_clientrating_size',
				array(
					'label'      => __( 'Font Size', 'wbcom-essential' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap .wb-client-rating ul li i' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'testimonial_clientrating_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-single-testimonial-wrap .wb-client-rating ul' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section(); // Style Testimonial designation style end.

		// Slider Button style.
		$this->start_controls_section(
			'products-slider-controller-style',
			array(
				'label'     => esc_html__( 'Slider Controller Style', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'slider_on' => 'yes',
				),
			)
		);

			$this->start_controls_tabs( 'product_sliderbtn_style_tabs' );

				// Slider Button style Normal.
				$this->start_controls_tab(
					'product_sliderbtn_style_normal_tab',
					array(
						'label' => __( 'Normal', 'wbcom-essential' ),
					)
				);

					$this->add_control(
						'button_style_heading',
						array(
							'label' => __( 'Navigation Arrow', 'wbcom-essential' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_responsive_control(
						'nvigation_position',
						array(
							'label'      => __( 'Position', 'wbcom-essential' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', '%' ),
							'range'      => array(
								'px' => array(
									'min'  => 0,
									'max'  => 1000,
									'step' => 1,
								),
								'%'  => array(
									'min' => 0,
									'max' => 100,
								),
							),
							'selectors'  => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button' => 'top: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$this->add_control(
						'button_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'button_bg_color',
						array(
							'label'     => __( 'Background Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button' => 'background-color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'button_border',
							'label'    => __( 'Border', 'wbcom-essential' ),
							'selector' => '{{WRAPPER}} .product-slider .elementor-swiper-button',
						)
					);

					$this->add_responsive_control(
						'button_border_radius',
						array(
							'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
							'type'      => Controls_Manager::DIMENSIONS,
							'selectors' => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
							),
						)
					);

					$this->add_responsive_control(
						'button_padding',
						array(
							'label'      => __( 'Padding', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							),
						)
					);

					$this->add_control(
						'button_style_dots_heading',
						array(
							'label' => __( 'Navigation Dots', 'wbcom-essential' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

						$this->add_responsive_control(
							'dots_position',
							array(
								'label'      => __( 'Position', 'wbcom-essential' ),
								'type'       => Controls_Manager::SLIDER,
								'size_units' => array( 'px', '%' ),
								'range'      => array(
									'px' => array(
										'min'  => 0,
										'max'  => 1000,
										'step' => 1,
									),
									'%'  => array(
										'min' => 0,
										'max' => 100,
									),
								),
								'selectors'  => array(
									'{{WRAPPER}} .product-slider .swiper-pagination' => 'left: {{SIZE}}{{UNIT}};',
								),
							)
						);

						$this->add_control(
							'dots_bg_color',
							array(
								'label'     => __( 'Background Color', 'wbcom-essential' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => array(
									'{{WRAPPER}} .product-slider .swiper-pagination-bullet' => 'background-color: {{VALUE}} !important;',
								),
							)
						);

						$this->add_group_control(
							Group_Control_Border::get_type(),
							array(
								'name'     => 'dots_border',
								'label'    => __( 'Border', 'wbcom-essential' ),
								'selector' => '{{WRAPPER}} .product-slider .swiper-pagination-bullet',
							)
						);

						$this->add_responsive_control(
							'dots_border_radius',
							array(
								'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
								'type'      => Controls_Manager::DIMENSIONS,
								'selectors' => array(
									'{{WRAPPER}} .product-slider .swiper-pagination-bullet' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
								),
							)
						);

				$this->end_controls_tab();// Normal button style end.

				// Button style Hover.
				$this->start_controls_tab(
					'product_sliderbtn_style_hover_tab',
					array(
						'label' => __( 'Hover', 'wbcom-essential' ),
					)
				);

					$this->add_control(
						'button_style_arrow_heading',
						array(
							'label' => __( 'Navigation', 'wbcom-essential' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'button_hover_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button:hover' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'button_hover_bg_color',
						array(
							'label'     => __( 'Background', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'selectors' => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button:hover' => 'background-color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'button_hover_border',
							'label'    => __( 'Border', 'wbcom-essential' ),
							'selector' => '{{WRAPPER}} .product-slider .elementor-swiper-button:hover',
						)
					);

					$this->add_responsive_control(
						'button_hover_border_radius',
						array(
							'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
							'type'      => Controls_Manager::DIMENSIONS,
							'selectors' => array(
								'{{WRAPPER}} .product-slider .elementor-swiper-button:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
							),
						)
					);

					$this->add_control(
						'button_style_dotshov_heading',
						array(
							'label' => __( 'Navigation Dots', 'wbcom-essential' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

						$this->add_control(
							'dots_hover_bg_color',
							array(
								'label'     => __( 'Background Color', 'wbcom-essential' ),
								'type'      => Controls_Manager::COLOR,
								'selectors' => array(
									'{{WRAPPER}} .product-slider .swiper-pagination-bullet:hover, {{WRAPPER}} .product-slider .swiper-pagination-bullet-active' => 'background-color: {{VALUE}} !important;',
								),
							)
						);

						$this->add_group_control(
							Group_Control_Border::get_type(),
							array(
								'name'     => 'dots_border_hover',
								'label'    => __( 'Border', 'wbcom-essential' ),
								'selector' => '{{WRAPPER}} .product-slider .swiper-pagination-bullet:hover',
							)
						);

						$this->add_responsive_control(
							'dots_border_radius_hover',
							array(
								'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
								'type'      => Controls_Manager::DIMENSIONS,
								'selectors' => array(
									'{{WRAPPER}} .product-slider .swiper-pagination-bullet:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
								),
							)
						);

				$this->end_controls_tab();// Hover button style end.

			$this->end_controls_tabs();

		$this->end_controls_section(); // Tab option end.
	}

	protected function render( $instance = array() ) {

		$settings = $this->get_settings_for_display();
		$column   = $this->get_settings_for_display( 'column' );

		$collumval = 'wb-col-1';
		if ( $column != '' ) {
			$collumval = 'wb-col-' . $column;
		}

		// Generate review.
		$testimonial_list = array();
		if ( 'custom' === $settings['testimonial_type'] ) {
			foreach ( $settings['testimonial_list'] as $testimonial ) {
				$testimonial_list[] = array(
					'image'       => Group_Control_Image_Size::get_attachment_image_html( $testimonial, 'client_imagesize', 'client_image' ),
					'name'        => $testimonial['client_name'],
					'designation' => $testimonial['client_designation'],
					'ratting'     => $testimonial['client_rating'],
					'message'     => $testimonial['client_say'],
				);
			}
		}

		// Slider Options.
		$slider_main_div_style = '';
		if ( $settings['slider_on'] === 'yes' ) {

			$is_rtl          = is_rtl();
			$direction       = $is_rtl ? 'rtl' : 'ltr';
			$slider_settings = array(
				'arrows'          => ( 'yes' === $settings['slarrows'] ),
				'dots'            => ( 'yes' === $settings['sldots'] ),
				'autoplay'        => ( 'yes' === $settings['slautolay'] ),
				'autoplay_speed'  => absint( $settings['slautoplay_speed'] ),
				'animation_speed' => absint( $settings['slanimation_speed'] ),
				'pause_on_hover'  => ( 'yes' === $settings['slpause_on_hover'] ),
				'rtl'             => $is_rtl,
			);

			$slider_responsive_settings = array(
				'product_items'          => $settings['slitems'],
				'scroll_columns'         => $settings['slscroll_columns'],
				'tablet_width'           => $settings['sltablet_width'],
				'tablet_display_columns' => $settings['sltablet_display_columns'],
				'tablet_scroll_columns'  => $settings['sltablet_scroll_columns'],
				'mobile_width'           => $settings['slmobile_width'],
				'mobile_display_columns' => $settings['slmobile_display_columns'],
				'mobile_scroll_columns'  => $settings['slmobile_scroll_columns'],

			);
			$slider_settings       = array_merge( $slider_settings, $slider_responsive_settings );
			$slider_main_div_style = "style='display:none'";
		} else {
			$slider_settings = '';
		}

		$this->add_render_attribute( 'area_attr', 'class', 'wb-row wb-testimonial-style-' . $settings['testimonial_layout'] );

		if ( $settings['no_gutters'] === 'yes' ) {
			$this->add_render_attribute( 'area_attr', 'class', 'wb-gutters' );
		}
		if ( $settings['slider_on'] === 'yes' ) {
			$this->add_render_attribute( 'area_attr', 'class', 'product-slider swiper-container' );
			$this->add_render_attribute( 'area_attr', 'data-settings', wp_json_encode( $slider_settings ) );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor's get_render_attribute_string() handles escaping.
		echo '<div ' . $this->get_render_attribute_string( 'area_attr' ) . ' ' . $slider_main_div_style . '>';

		if ( $settings['slider_on'] === 'yes' ) {
			echo '<div class="swiper-wrapper">';
		}

		foreach ( $testimonial_list as $testimonial ) :
			$slide_class = ( $settings['slider_on'] === 'yes' ) ? $collumval . ' swiper-slide' : $collumval;
			?>
				<div class="<?php echo esc_attr( $slide_class ); ?>">
					<div class="wb-single-testimonial-wrap">

					<?php if ( $settings['testimonial_layout'] === '1' ) : ?>
							<?php
							if ( ! empty( $testimonial['message'] ) ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ratting() method returns properly escaped HTML.
								printf( '<div class="wb-client-content wb-client-content-border"><p>%1$s</p>%2$s</div>', esc_html( $testimonial['message'] ), $this->ratting( $testimonial['ratting'] ) );
							}
							?>
							<div class="wb-client-info">
								<?php
								if ( ! empty( $testimonial['image'] ) ) {
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML from Elementor's Group_Control_Image_Size is safe.
									echo $testimonial['image'];
								}

								if ( ! empty( $testimonial['name'] ) ) {
									echo '<h4>' . esc_html( $testimonial['name'] ) . '</h4>';
								}

								if ( ! empty( $testimonial['designation'] ) ) {
									echo '<span>' . esc_html( $testimonial['designation'] ) . '</span>';
								}
								?>
							</div>

						<?php elseif ( $settings['testimonial_layout'] === '2' ) : ?>
							<div class="wb-client-info-wrap-2">
								<?php
								if ( ! empty( $testimonial['image'] ) ) {
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML from Elementor's Group_Control_Image_Size is safe.
									printf( '<div class="wb-client-img-2">%1$s</div>', $testimonial['image'] );
								}
								?>
								<div class="wb-client-info-3">
									<?php
									if ( ! empty( $testimonial['name'] ) || ! empty( $testimonial['designation'] ) ) {
										printf( '<h4>%1$s<span>%2$s</span></h4>', esc_html( $testimonial['name'] ), esc_html( $testimonial['designation'] ) );
									}
									if ( ! empty( $testimonial['ratting'] ) ) {
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ratting() method returns properly escaped HTML.
										echo $this->ratting( $testimonial['ratting'] );
									}
									?>
								</div>
							</div>
							<?php
							if ( ! empty( $testimonial['message'] ) ) {
								printf( '<div class="wb-client-content"><p class="wb-width-dec">%1$s</p></div>', esc_html( $testimonial['message'] ) );
							}
							?>

						<?php elseif ( $settings['testimonial_layout'] === '3' ) : ?>
							<div class="wb-client-info-wrap wb-client-info-border">
								<?php
								if ( ! empty( $testimonial['image'] ) ) {
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML from Elementor's Group_Control_Image_Size is safe.
									printf( '<div class="wb-client-img">%1$s</div>', $testimonial['image'] );
								}
								?>
								<div class="wb-client-info-2">
									<?php
									if ( ! empty( $testimonial['name'] ) ) {
										echo '<h4>' . esc_html( $testimonial['name'] ) . '</h4>';
									}

									if ( ! empty( $testimonial['designation'] ) ) {
										echo '<span>' . esc_html( $testimonial['designation'] ) . '</span>';
									}

									if ( ! empty( $testimonial['ratting'] ) ) {
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ratting() method returns properly escaped HTML.
										echo $this->ratting( $testimonial['ratting'] );
									}
									?>
								</div>
							</div>
							<?php
							if ( ! empty( $testimonial['message'] ) ) {
								printf( '<div class="wb-client-content"><p>%1$s</p></div>', esc_html( $testimonial['message'] ) );
							}
							?>

						<?php else : ?>
							<div class="wb-client-info-wrap-2">
								<?php
								if ( ! empty( $testimonial['image'] ) ) {
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML from Elementor's Group_Control_Image_Size is safe.
									printf( '<div class="wb-client-img-2">%1$s</div>', $testimonial['image'] );
								}
								?>
								<div class="wb-client-info-3">
									<?php
									if ( ! empty( $testimonial['name'] ) || ! empty( $testimonial['designation'] ) ) {
										printf( '<h4>%1$s<span>%2$s</span></h4>', esc_html( $testimonial['name'] ), esc_html( $testimonial['designation'] ) );
									}

									if ( ! empty( $testimonial['ratting'] ) ) {
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ratting() method returns properly escaped HTML.
										echo $this->ratting( $testimonial['ratting'] );
									}
									?>
								</div>
							</div>
							<?php
							if ( ! empty( $testimonial['message'] ) ) {
								printf( '<div class="wb-client-content"><p>%1$s</p></div>', esc_html( $testimonial['message'] ) );
							}
							?>

						<?php endif; ?>

					</div>
				</div>
			<?php
			endforeach;

		if ( $settings['slider_on'] === 'yes' ) {
			echo '</div>'; // close swiper-wrapper
			echo '<div class="swiper-pagination"></div>';
			echo '<div class="elementor-swiper-button elementor-swiper-button-prev"><i class="wbe-icons wbe-icon-angle-left"></i></div>';
			echo '<div class="elementor-swiper-button elementor-swiper-button-next"><i class="wbe-icons wbe-icon-angle-right"></i></div>';
		}

		echo '</div>';
	}

	public function ratting( $ratting_num ) {
		if ( ! empty( $ratting_num ) ) {
			$rating          = $ratting_num;
			$rating_whole    = floor( $ratting_num );
			$rating_fraction = $rating - $rating_whole;
			$ratting_html    = '<div class="wb-client-rating"><ul>';
			for ( $i = 1; $i <= 5; $i++ ) {
				if ( $i <= $rating_whole ) {
					$ratting_html .= '<li><i class="fas fa-star"></i></li>';
				} elseif ( $rating_fraction != 0 ) {
					$ratting_html   .= '<li><i class="fas fa-star-half-alt"></i></li>';
					$rating_fraction = 0;
				} else {
					$ratting_html .= '<li><i class="far fa-star"></i></li>';
				}
			}
			$ratting_html .= '</ul></div>';

			return $ratting_html;
		}
	}
}
