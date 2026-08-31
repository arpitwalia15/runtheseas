<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Heading
 *
 * Elementor widget for Heading
 *
 * @since 3.6.0
 */
class Heading extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wbcom-heading', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wbcom-heading.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-heading';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Heading', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-t-letter';
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
		return array( 'heading', 'title', 'text', 'headline', 'typography' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wbcom-heading' );
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
			'heading_content',
			array(
				'label' => esc_html__( 'Heading', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'heading_text',
			array(
				'label'       => esc_html__( 'Heading Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your title', 'wbcom-essential' ),
				'default'     => esc_html__( 'Add Your Heading Text Here', 'wbcom-essential' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'wbcom-essential' ),
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
			'link',
			array(
				'label'     => esc_html__( 'Link', 'wbcom-essential' ),
				'type'      => Controls_Manager::URL,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => array(
					'url' => '',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => esc_html__( 'Heading', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading, {{WRAPPER}} .wbcom-heading a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',

				'selector' => '{{WRAPPER}} .wbcom-heading',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-heading',
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label'     => esc_html__( 'Blend Mode', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''            => esc_html__( 'Normal', 'wbcom-essential' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'difference'  => 'Difference',
					'exclusion'   => 'Exclusion',
					'hue'         => 'Hue',
					'luminosity'  => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading' => 'mix-blend-mode: {{VALUE}}',
				),
				'condition' => array(
					'gradient_heading' => '',
					'rotate_switch'    => '',
				),
				'separator' => 'none',
			)
		);

		$this->add_control(
			'flex_direction',
			array(
				'label'     => esc_html__( 'Before/After Layout', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'column' => esc_html__( 'Vertical', 'wbcom-essential' ),
					'row'    => esc_html__( 'Horizontal', 'wbcom-essential' ),
				),
				'default'   => 'column',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading' => 'flex-direction: {{VALUE}}',
				),
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'justify',
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
				'condition' => array( 'flex_direction' => 'row' ),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'align',
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
				'condition' => array( 'flex_direction' => 'column' ),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hr_heading_1',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'max_width',
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
					'{{WRAPPER}} .wbcom-heading' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hr_heading_2',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'gradient_heading',
			array(
				'label'        => esc_html__( 'Gradient Heading', 'wbcom-essential' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Off', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'gradient_heading_bg',
				'label'     => esc_html__( 'Background', 'wbcom-essential' ),
				'types'     => array( 'gradient' ),
				'selector'  => '{{WRAPPER}} .wbcom-heading',
				'condition' => array( 'gradient_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'hr_heading_3',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'rotate_switch',
			array(
				'label'        => esc_html__( 'Rotate Text', 'wbcom-essential' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Off', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'text_rotate',
			array(
				'label'      => esc_html__( 'Rotate', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'condition'  => array( 'rotate_switch' => 'yes' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 360,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 180,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading' => 'writing-mode: vertical-rl;transform: rotate({{SIZE}}deg);',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_line1_style',
			array(
				'label' => esc_html__( 'Before', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'line1_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:before' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'line1_height',
			array(
				'label'      => esc_html__( 'Height', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:before' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'line1_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading:before' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'line1_align',
			array(
				'label'     => esc_html__( 'Alignment', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'flex-start' => esc_html__( 'Start', 'wbcom-essential' ),
					'center'     => esc_html__( 'Center', 'wbcom-essential' ),
					'flex-end'   => esc_html__( 'End', 'wbcom-essential' ),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading:before' => 'align-self: {{VALUE}}',
				),
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'line1_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'line1_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:before' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'line1_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-heading:before',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_line2_style',
			array(
				'label' => esc_html__( 'After', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'line2_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:after' => 'width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'line2_height',
			array(
				'label'      => esc_html__( 'Height', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:after' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'line2_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading:after' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'line2_align',
			array(
				'label'     => esc_html__( 'Alignment', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'flex-start' => esc_html__( 'Start', 'wbcom-essential' ),
					'center'     => esc_html__( 'Center', 'wbcom-essential' ),
					'flex-end'   => esc_html__( 'End', 'wbcom-essential' ),
				),
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-heading:after' => 'align-self: {{VALUE}}',
				),
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'line2_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'line2_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-heading:after' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'line2_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-heading:after',
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
		if ( '' === $settings['heading_text'] ) {
			return;
		}

		$heading_output = esc_html( $settings['heading_text'] );
		$this->add_render_attribute( 'heading_text', 'class', 'wbcom-heading' );

		if ( $settings['gradient_heading'] === 'yes' ) {
			$this->add_render_attribute( 'heading_text', 'class', 'wbcom-gradient-heading' );
		}

		// link.
		if ( ! empty( $settings['link']['url'] ) ) {

			$this->add_render_attribute( 'url', 'href', esc_url( $settings['link']['url'] ) );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'url', 'target', '_blank' );
			}

			if ( ! empty( $settings['link']['nofollow'] ) ) {
				$this->add_render_attribute( 'url', 'rel', 'nofollow' );
			}

			$heading_output = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $heading_output );
		}

		// heading tag.
		printf( '<%1$s %2$s><span>%3$s</span></%1$s>', esc_attr( $settings['html_tag'] ), $this->get_render_attribute_string( 'heading_text' ), $heading_output ); //phpcs:ignore

	}

}
