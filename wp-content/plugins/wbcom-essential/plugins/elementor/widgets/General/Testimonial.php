<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Testimonial
 *
 * Elementor widget for Testimonial
 *
 * @since 3.6.0
 */
class Testimonial extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-testimonial', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/testimonial.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-testimonial';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Testimonial', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial';
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
		return array( 'testimonial', 'review', 'quote', 'feedback', 'customer' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-testimonial' );
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
			'settings_section',
			array(
				'label' => esc_html__( 'Testimonials', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Name', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'John Doe', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Info', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Web Designer', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label' => esc_html__( 'Thumbnail', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'content',
			array(
				'label'       => esc_html__( 'Content', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'label_block' => true,
				'default'     => esc_html__( 'Enim ad commodo do est proident excepteur nulla enim pariatur. Proident et laborum reprehenderit voluptate velit Lorem culpa ullamco.', 'wbcom-essential' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item',
			array(
				'label' => esc_html__( 'Testimonial', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'item_direction',
			array(
				'label'     => esc_html__( 'Flex Direction', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'column',
				'options'   => array(
					'column'         => esc_html__( 'Column', 'wbcom-essential' ),
					'column-reverse' => esc_html__( 'Column Reverse', 'wbcom-essential' ),
					'row'            => esc_html__( 'Row', 'wbcom-essential' ),
					'row-reverse'    => esc_html__( 'Row Reverse', 'wbcom-essential' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-item' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_width',
			array(
				'label'      => esc_html__( 'Max Width (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-item' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'item_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-item',
			)
		);

		$this->add_responsive_control(
			'item_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-item' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'label'    => esc_html__( 'Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-item',
			)
		);

		$this->add_control(
			'item_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'item_margin',
			array(
				'label'      => esc_html__( 'Item Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_info',
			array(
				'label' => esc_html__( 'Author Info', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'info_direction',
			array(
				'label'     => esc_html__( 'Flex Direction', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'row',
				'options'   => array(
					'column'         => esc_html__( 'Column', 'wbcom-essential' ),
					'column-reverse' => esc_html__( 'Column Reverse', 'wbcom-essential' ),
					'row'            => esc_html__( 'Row', 'wbcom-essential' ),
					'row-reverse'    => esc_html__( 'Row Reverse', 'wbcom-essential' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'info_min_width',
			array(
				'label'      => esc_html__( 'Min Width (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 500,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 150,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'item_direction',
							'operator' => '==',
							'value'    => 'row',
						),
						array(
							'name'     => 'item_direction',
							'operator' => '==',
							'value'    => 'row-reverse',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'info_horizontal_align_column',
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
							'name'     => 'info_direction',
							'operator' => '==',
							'value'    => 'column',
						),
						array(
							'name'     => 'info_direction',
							'operator' => '==',
							'value'    => 'column-reverse',
						),
					),
				),
				'default'    => 'center',
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'align-items: {{VALUE}};',
				),
				'toggle'     => false,
			)
		);

		$this->add_responsive_control(
			'info_vertical_align_column',
			array(
				'label'      => esc_html__( 'Vertical Align', 'wbcom-essential' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'info_direction',
									'operator' => '==',
									'value'    => 'column',
								),
								array(
									'name'     => 'info_direction',
									'operator' => '==',
									'value'    => 'column-reverse',
								),
							),
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'item_direction',
									'operator' => '==',
									'value'    => 'row',
								),
								array(
									'name'     => 'item_direction',
									'operator' => '==',
									'value'    => 'row-reverse',
								),
							),
						),
					),
				),
				'default'    => 'center',
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'justify-content: {{VALUE}};',
				),
				'toggle'     => false,
			)
		);

		$this->add_responsive_control(
			'info_horizontal_align_row',
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
							'name'     => 'info_direction',
							'operator' => '==',
							'value'    => 'row',
						),
						array(
							'name'     => 'info_direction',
							'operator' => '==',
							'value'    => 'row-reverse',
						),
					),
				),
				'default'    => 'flex-start',
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'justify-content: {{VALUE}};',
				),
				'toggle'     => false,
			)
		);

		$this->add_responsive_control(
			'info_vertical_align_row',
			array(
				'label'      => esc_html__( 'Vertical Align', 'wbcom-essential' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => array(
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
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'info_direction',
							'operator' => '==',
							'value'    => 'row',
						),
						array(
							'name'     => 'info_direction',
							'operator' => '==',
							'value'    => 'row-reverse',
						),
					),
				),
				'default'    => 'center',
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'align-items: {{VALUE}};',
				),
				'toggle'     => false,
			)
		);

		$this->add_responsive_control(
			'info_text_align',
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
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'info_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'info_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'info_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-person' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_font_color',
			array(
				'label'     => esc_html__( 'Font Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-content' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-content p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_font_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-testimonials-content,{{WRAPPER}} .wbcom-testimonials-content p',
			)
		);

		$this->add_control(
			'content_heading_color',
			array(
				'label'     => esc_html__( 'Heading Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-content h1' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-content h2' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-content h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-content h4' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-content h5' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-content h6' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_heading_typography',
				'label'    => esc_html__( 'Heading Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-testimonials-content h1,{{WRAPPER}} .wbcom-testimonials-content h2,{{WRAPPER}} .wbcom-testimonials-content h3,{{WRAPPER}} .wbcom-testimonials-content h4,{{WRAPPER}} .wbcom-testimonials-content h5,{{WRAPPER}} .wbcom-testimonials-content h6',
			)
		);

		$this->add_responsive_control(
			'content_text_align',
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
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-content' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-content p' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'content_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'content_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-content' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'content_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-content',
			)
		);

		$this->add_responsive_control(
			'content_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-content' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'content_shadow',
				'label'    => esc_html__( 'Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-content',
			)
		);

		$this->add_control(
			'content_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_arrow',
			array(
				'label' => esc_html__( 'Content Arrow', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'content_arrow',
			array(
				'label'   => esc_html__( 'Arrow', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'no-arrow',
				'options' => array(
					'no-arrow'     => esc_html__( 'None', 'wbcom-essential' ),
					'top-arrow'    => esc_html__( 'Top', 'wbcom-essential' ),
					'bottom-arrow' => esc_html__( 'Bottom', 'wbcom-essential' ),
					'left-arrow'   => esc_html__( 'Left', 'wbcom-essential' ),
					'right-arrow'  => esc_html__( 'Right', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'content_arrow_top',
			array(
				'label'      => esc_html__( 'Top Spacing', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
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
							'name'     => 'content_arrow',
							'operator' => '==',
							'value'    => 'left-arrow',
						),
						array(
							'name'     => 'content_arrow',
							'operator' => '==',
							'value'    => 'right-arrow',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-content.left-arrow:after' => 'top: {{SIZE}}{{UNIT}};transform:translateY(0) translateX(-100%);',
					'{{WRAPPER}} .wbcom-testimonials-content.right-arrow:after' => 'top: {{SIZE}}{{UNIT}};transform:translateY(0) translateX(100%);',
				),
			)
		);

		$this->add_responsive_control(
			'content_arrow_left',
			array(
				'label'      => esc_html__( 'Left Spacing', 'wbcom-essential' ),
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
							'name'     => 'content_arrow',
							'operator' => '==',
							'value'    => 'top-arrow',
						),
						array(
							'name'     => 'content_arrow',
							'operator' => '==',
							'value'    => 'bottom-arrow',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-content.top-arrow:after' => 'left: {{SIZE}}{{UNIT}};transform:none;',
					'{{WRAPPER}} .wbcom-testimonials-content.bottom-arrow:after' => 'left: {{SIZE}}{{UNIT}};transform:none;',
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
					'{{WRAPPER}} .wbcom-testimonials-content:after' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'     => esc_html__( 'Arrow Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 200,
				'step'      => 1,
				'default'   => 15,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-content:after' => 'border-width: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title',
			array(
				'label' => esc_html__( 'Name', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-testimonials-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-title',
			)
		);

		$this->add_control(
			'title_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wbcom-testimonials-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_subtitle',
			array(
				'label' => esc_html__( 'Info', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-testimonials-subtitle',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'subtitle_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-subtitle',
			)
		);

		$this->add_control(
			'subtitle_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'subtitle_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wbcom-testimonials-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_thumbnail',
			array(
				'label' => esc_html__( 'Thumbnail', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'img_size',
			array(
				'label'   => esc_html__( 'Thumbnail Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'thumbnail',
				'options' => wba_get_image_sizes(),
			)
		);

		$this->add_responsive_control(
			'thumb_width',
			array(
				'label'     => esc_html__( 'Max Width (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 500,
				'step'      => 1,
				'default'   => 70,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-thumb' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'thumb_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'thumb_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-thumb img',
			)
		);

		$this->add_responsive_control(
			'thumb_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-thumb img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'thumb_shadow',
				'label'    => esc_html__( 'Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-testimonials-thumb img',
			)
		);

		$this->add_control(
			'thumb_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'thumb_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-thumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		?>
		<?php
		$img_url = wp_get_attachment_image_url( $settings['image']['id'], $settings['img_size'] );
		?>
		<div class="wbcom-testimonials-item">
			<div class="wbcom-testimonials-content <?php echo esc_attr( $settings['content_arrow'] ); ?>">
				<?php echo wp_kses_post( $settings['content'] ); ?>
			</div>
			<div class="wbcom-testimonials-person">
				<?php if ( $img_url ) { ?>
				<div class="wbcom-testimonials-thumb"><img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $settings['title'] ); ?>" /></div>
				<?php } ?>
				<div class="wbcom-testimonials-info">
					<?php if ( $settings['title'] ) { ?>
					<span class="wbcom-testimonials-title"><?php echo wp_kses_post( $settings['title'] ); ?></span>
					<?php } ?>
					<?php if ( $settings['subtitle'] ) { ?>
					<span class="wbcom-testimonials-subtitle"><?php echo wp_kses_post( $settings['subtitle'] ); ?></span>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
}
