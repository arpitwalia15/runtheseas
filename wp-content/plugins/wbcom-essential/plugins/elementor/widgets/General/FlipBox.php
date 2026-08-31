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
 * Elementor FlipBox
 *
 * Elementor widget for FlipBox
 *
 * @since 3.6.0
 */
class FlipBox extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-flip-box', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/flip-box.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-flip-box';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Flip Box', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-flip-box';
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
		return array( 'flip', 'box', 'card', 'hover', 'animation' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-flip-box' );
	}

	/**
	 * Get buttons skins.
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
		$this->start_controls_section(
			'front_section',
			array(
				'label' => esc_html__( 'Front', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'front_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'front_title_html_tag',
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
					'span' => 'span',
				),
				'default' => 'h5',
			)
		);

		$this->add_control(
			'front_title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Title Here...', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'front_content',
			array(
				'label'   => esc_html__( 'Content', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Content Here...', 'wbcom-essential' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'back_section',
			array(
				'label' => esc_html__( 'Back', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'back_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'back_title_html_tag',
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
					'span' => 'span',
				),
				'default' => 'h5',
			)
		);

		$this->add_control(
			'back_title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Title Here...', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'back_content',
			array(
				'label'   => esc_html__( 'Content', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Content Here...', 'wbcom-essential' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_content',
			array(
				'label' => esc_html__( 'Button', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'btn_text',
			array(
				'label'   => esc_html__( 'Text', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'BUY NOW', 'wbcom-essential' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'btn_website_link',
			array(
				'label'         => esc_html__( 'Link to', 'wbcom-essential' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://wbcomdesigns.com/', 'wbcom-essential' ),
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
				'label'   => esc_html__( 'Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'wbcom-btn-md',
				'options' => array(
					'wbcom-btn-md' => esc_html__( 'Normal', 'wbcom-essential' ),
					'wbcom-btn-lg' => esc_html__( 'Large', 'wbcom-essential' ),
					'wbcom-btn-sm' => esc_html__( 'Small', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'btn_text_align',
			array(
				'label'     => esc_html__( 'Alignment', 'wbcom-essential' ),
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
					'{{WRAPPER}} .wbcom-btn-wrapper' => 'text-align: {{VALUE}};',
				),
				'toggle'    => true,
			)
		);

		$this->add_control(
			'btn_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'btn_icon_position',
			array(
				'label'   => esc_html__( 'Icon Position', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'before',
				'options' => array(
					'after'  => esc_html__( 'After', 'wbcom-essential' ),
					'before' => esc_html__( 'Before', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'btn_icon_spacing',
			array(
				'label'      => esc_html__( 'Icon Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-btn-wrapper a i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'settings_section',
			array(
				'label' => esc_html__( 'Animation', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'       => esc_html__( 'Animation Direction', 'wbcom-essential' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'flip-right'                   => esc_html__( 'Flip Right', 'wbcom-essential' ),
					'flip-left'                    => esc_html__( 'Flip Left', 'wbcom-essential' ),
					'flip-up'                      => esc_html__( 'Flip Up', 'wbcom-essential' ),
					'flip-down'                    => esc_html__( 'Flip Down', 'wbcom-essential' ),
					'flip-diagonal-right'          => esc_html__( 'Flip Diagonal Right', 'wbcom-essential' ),
					'flip-diagonal-left'           => esc_html__( 'Flip Diagonal Left', 'wbcom-essential' ),
					'flip-inverted-diagonal-right' => esc_html__( 'Flip Inverted Diagonal Right', 'wbcom-essential' ),
					'flip-inverted-diagonal-left'  => esc_html__( 'Flip Inverted Diagonal Left', 'wbcom-essential' ),
				),
				'label_block' => true,
				'default'     => 'flip-right',
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'     => esc_html__( 'Animation Duration', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 10,
				'step'      => 0.1,
				'default'   => 0.4,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-inner' => 'transition-duration: {{VALUE}}s;',
				),
			)
		);

		$this->add_control(
			'timing',
			array(
				'label'       => esc_html__( 'Animation Timing', 'wbcom-essential' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'linear'      => esc_html__( 'Linear', 'wbcom-essential' ),
					'ease'        => esc_html__( 'Ease', 'wbcom-essential' ),
					'ease-in'     => esc_html__( 'Ease In', 'wbcom-essential' ),
					'ease-out'    => esc_html__( 'Ease Out', 'wbcom-essential' ),
					'ease-in-out' => esc_html__( 'Ease In Out', 'wbcom-essential' ),
					'custom'      => esc_html__( 'Custom', 'wbcom-essential' ),
				),
				'label_block' => true,
				'default'     => 'linear',
			)
		);

		$this->add_control(
			'custom_timing',
			array(
				'label'       => esc_html__( 'Custom Timing', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => 'cubic-bezier(0.75, 0, 0.85, 1)',
				'label_block' => true,
				'condition'   => array( 'timing' => 'custom' ),
			)
		);

		$this->end_controls_section();

		  // section start.
		$this->start_controls_section(
			'box_style_section',
			array(
				'label' => esc_html__( 'Box', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'box_align',
			array(
				'label'     => esc_html__( 'Box Align', 'wbcom-essential' ),
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
					'{{WRAPPER}} .wba-flip-card-outer' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'box_max_width',
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
					'unit' => 'px',
					'size' => 300,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wba-flip-card-inner'   => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_min_height',
			array(
				'label'      => esc_html__( 'Minimum Height', 'wbcom-essential' ),
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
					'unit' => 'px',
					'size' => 400,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wba-flip-card-inner'   => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'front_style_section',
			array(
				'label' => esc_html__( 'Front', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'front_horizontal_align',
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
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'front_vertical_align',
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
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'front_text_align',
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
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'front_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'front_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front',
			)
		);

		$this->add_control(
			'front_bg_overlay',
			array(
				'label'     => esc_html__( 'Background Overlay Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'front_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'front_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front',
			)
		);

		$this->add_responsive_control(
			'front_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'front_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_front_style' );

		$this->start_controls_tab(
			'tab_front_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'front_icon_width',
			array(
				'label'     => esc_html__( 'Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'front_icon_height',
			array(
				'label'     => esc_html__( 'Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon' => 'height: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'front_icon_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'front_icon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'front_icon_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon',
			)
		);

		$this->add_control(
			'front_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'rem' => array(
						'min' => 0,
						'max' => 50,
					),
					'px'  => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_icon_svg_width',
			array(
				'label'     => esc_html__( 'SVG Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'default'   => 100,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon svg' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'front_icon_svg_height',
			array(
				'label'     => esc_html__( 'SVG Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'default'   => 100,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-icon svg' => 'height: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_front_title',
			array(
				'label' => esc_html__( 'Title', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'front_title_typography',

				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-title',
			)
		);

		$this->add_control(
			'front_title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'front_title_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_front_content',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'front_content_typography',

				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-content',
			)
		);

		$this->add_control(
			'front_content_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'front_content_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-content' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .front .wba-flip-card-front-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		 // section start.
		$this->start_controls_section(
			'back_style_section',
			array(
				'label' => esc_html__( 'Back', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'back_horizontal_align',
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
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'back_vertical_align',
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
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'back_text_align',
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
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'back_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'back_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back',
			)
		);

		$this->add_control(
			'back_bg_overlay',
			array(
				'label'     => esc_html__( 'Background Overlay Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'back_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'back_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back',
			)
		);

		$this->add_responsive_control(
			'back_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'back_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_back_style' );

		$this->start_controls_tab(
			'tab_back_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'back_icon_width',
			array(
				'label'     => esc_html__( 'Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'back_icon_height',
			array(
				'label'     => esc_html__( 'Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon' => 'height: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'back_icon_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'back_icon_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'back_icon_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon',
			)
		);

		$this->add_control(
			'back_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'rem' => array(
						'min' => 0,
						'max' => 50,
					),
					'px'  => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_icon_svg_width',
			array(
				'label'     => esc_html__( 'SVG Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'default'   => 100,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon svg' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'back_icon_svg_height',
			array(
				'label'     => esc_html__( 'SVG Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'default'   => 100,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-icon svg' => 'height: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_back_title',
			array(
				'label' => esc_html__( 'Title', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'back_title_typography',

				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-title',
			)
		);

		$this->add_control(
			'back_title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'back_title_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_back_content',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'back_content_typography',

				'selector' => '{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-content',
			)
		);

		$this->add_control(
			'back_content_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'back_content_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-content' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-flip-card-wrapper .wba-flip-card-inner .back .wba-flip-card-back-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'btn_style_section',
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
			Group_Control_Background::get_type(),
			array(
				'name'     => 'bg_color_gradient',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a',
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'bg_color_hover_gradient',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-btn-wrapper a:hover',
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
		$target        = $settings['btn_website_link']['is_external'] ? ' target="' . esc_attr( '_blank' ) . '"' : '';
		$nofollow      = $settings['btn_website_link']['nofollow'] ? ' rel="' . esc_attr( 'nofollow' ) . '"' : '';
		$icon_position = $settings['btn_icon_position'];		
		?>
		<div class="wba-flip-card-outer" style="display:flex;">
			<div class="wba-flip-card-wrapper <?php echo esc_attr( $settings['direction'] ); ?>">
				<div class="wba-flip-card-inner" style="transition-timing-function: <?php if ( $settings['custom_timing'] ) { echo esc_attr( $settings['custom_timing'] ); } else { echo esc_attr( $settings['timing'] ); } ?>;">
					<div class="front">
						<div class="wba-flip-card-overlay"></div>
						<div class="wba-flip-card-front-icon">
							<?php \Elementor\Icons_Manager::render_icon( $settings['front_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</div>
						<?php
						if ( $settings['front_title'] ) {							
							echo '<' . esc_html( $settings['front_title_html_tag'] ) . ' class="wba-flip-card-front-title">' . esc_html( $settings['front_title'] ) . '</' . esc_html( $settings['front_title_html_tag'] ) . '>';
						}
						if ( $settings['front_content'] ) {
							echo '<div class="wba-flip-card-front-content">' . wp_kses_post( $settings['front_content'] ) . '</div>';
						}
						?>
					</div>
					<div class="back">
						<div class="wba-flip-card-overlay"></div>
						<div class="wba-flip-card-back-icon">
							<?php \Elementor\Icons_Manager::render_icon( $settings['back_icon'], array( 'aria-hidden' => 'true' ) ); ?>
						</div>
						<?php
						if ( $settings['back_title'] ) {
							echo '<' . esc_html( $settings['back_title_html_tag'] ) . ' class="wba-flip-card-back-title">' . esc_html( $settings['back_title'] ) . '</' .  esc_html( $settings['back_title_html_tag'] ) . '>';
						}
						if ( $settings['back_content'] ) {
							echo '<div class="wba-flip-card-back-content">' . wp_kses_post( $settings['back_content'] ) . '</div>';
						}
						if ( $settings['btn_website_link']['url'] ) {
							?>
						<div class="wbcom-btn-wrapper">
							<a id="<?php echo esc_attr( $settings['btn_id'] ); ?>" class="<?php echo esc_attr( $settings['btn_size'] ); ?> <?php echo esc_attr( $settings['btn_skin'] ); ?>" href="<?php echo esc_url( $settings['btn_website_link']['url'] ); ?>" <?php echo esc_attr( $target ); ?> <?php echo esc_attr( $nofollow ); ?>>
								<?php
								if ( $icon_position == 'before' ) {
									\Elementor\Icons_Manager::render_icon( $settings['btn_icon'], array( 'aria-hidden' => 'true' ) );
									echo esc_html( $settings['btn_text'] );
								} else {
									echo esc_html( $settings['btn_text'] );
									\Elementor\Icons_Manager::render_icon( $settings['btn_icon'], array( 'aria-hidden' => 'true' ) );
								}
								?>
							</a>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
