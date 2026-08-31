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
 * Elementor TestimonialCarousel
 *
 * Elementor widget for TestimonialCarousel
 *
 * @since 3.6.0
 */
class TestimonialCarousel extends \Elementor\Widget_Base {

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
		wp_register_style( 'wb-testimonial', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/testimonial.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/swiper.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wb-testimonial-carousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/testimonial-carousel.js', array( 'jquery', 'swiper' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-testimonial-carousel';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Testimonial Carousel', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
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
		return array( 'testimonial', 'carousel', 'reviews', 'slider', 'quotes' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-testimonial-carousel' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-testimonial', 'elementor-icons-fa-solid', 'elementor-icons-fa-regular' );
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

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Subtitle', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'Thumbnail', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'content',
			array(
				'label'       => esc_html__( 'Content', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pugnant Stoici cum Peripateticis. Et quidem, inquit, vehementer errat; Quamquam id quidem, infinitum est in hac urbe; Duo Reges: constructio interrete.</p>',
				'label_block' => true,
			)
		);

		$this->add_control(
			'testimonials',
			array(
				'label'       => esc_html__( 'Testimonials', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'title'    => esc_html__( 'Title #1', 'wbcom-essential' ),
						'subtitle' => esc_html__( 'Subtitle #1', 'wbcom-essential' ),
						'image'    => '',
						'content'  => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pugnant Stoici cum Peripateticis. Et quidem, inquit, vehementer errat; Quamquam id quidem, infinitum est in hac urbe; Duo Reges: constructio interrete.</p>',
					),
					array(
						'title'    => esc_html__( 'Title #2', 'wbcom-essential' ),
						'subtitle' => esc_html__( 'Subtitle #2', 'wbcom-essential' ),
						'image'    => '',
						'content'  => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pugnant Stoici cum Peripateticis. Et quidem, inquit, vehementer errat; Quamquam id quidem, infinitum est in hac urbe; Duo Reges: constructio interrete.</p>',
					),
					array(
						'title'    => esc_html__( 'Title #3', 'wbcom-essential' ),
						'subtitle' => esc_html__( 'Subtitle #3', 'wbcom-essential' ),
						'image'    => '',
						'content'  => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pugnant Stoici cum Peripateticis. Et quidem, inquit, vehementer errat; Quamquam id quidem, infinitum est in hac urbe; Duo Reges: constructio interrete.</p>',
					),
					array(
						'title'    => esc_html__( 'Title #4', 'wbcom-essential' ),
						'subtitle' => esc_html__( 'Subtitle #4', 'wbcom-essential' ),
						'image'    => '',
						'content'  => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pugnant Stoici cum Peripateticis. Et quidem, inquit, vehementer errat; Quamquam id quidem, infinitum est in hac urbe; Duo Reges: constructio interrete.</p>',
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'carousel_settings',
			array(
				'label' => esc_html__( 'Carousel Settings', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => esc_html__( 'Columns', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'three',
				'options' => array(
					'one'   => esc_html__( '1 Column', 'wbcom-essential' ),
					'two'   => esc_html__( '2 Column', 'wbcom-essential' ),
					'three' => esc_html__( '3 Column', 'wbcom-essential' ),
					'four'  => esc_html__( '4 Column', 'wbcom-essential' ),
					'five'  => esc_html__( '5 Column', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'display_nav',
			array(
				'label'        => esc_html__( 'Display Navigation Arrows', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'display_dots',
			array(
				'label'        => esc_html__( 'Display Navigation Dots', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => esc_html__( 'Infinite Loop', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'wbcom-essential' ),
				'description'  => esc_html__( 'Infinite loop should be on.', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'autoplay_duration',
			array(
				'label'   => esc_html__( 'Autoplay Duration (Second)', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 120,
				'step'    => 1,
				'default' => 5,
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

		$this->add_control(
			'carousel_overflow_hidden',
			array(
				'label'        => esc_html__( 'Overflow Hidden', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'wbcom-overflow-hidden',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_responsive_control(
			'carousel_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-slide' => 'margin-left: {{LEFT}}{{UNIT}};margin-right: {{RIGHT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .swiper-wrapper'  => 'margin-left: -{{LEFT}}{{UNIT}};margin-right: -{{RIGHT}}{{UNIT}};',
				),
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

		$this->start_controls_section(
			'section_navigation',
			array(
				'label'     => esc_html__( 'Navigation Arrows', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_nav' => 'yes' ),
			)
		);

		$this->add_control(
			'nav_arrow_next_icon',
			array(
				'label'   => esc_html__( 'Next Icon', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
			'nav_arrow_prev_icon',
			array(
				'label'   => esc_html__( 'Previous Icon', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-left',
					'library' => 'solid',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_arrow_style' );

		$this->start_controls_tab(
			'tab_arrow_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'nav_arrow_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_arrow_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrow_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'nav_arrow_hover_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_arrow_bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Hover Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'nav_arrow_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'nav_arrow_size',
			array(
				'label'     => esc_html__( 'Icon Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 100,
				'step'      => 1,
				'default'   => 30,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev' => 'font-size: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next' => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'nav_arrow_box_size',
			array(
				'label'     => esc_html__( 'Box Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 200,
				'step'      => 1,
				'default'   => 60,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'nav_arrow_radius',
			array(
				'label'      => esc_html__( 'Box Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_arrow_box_margin',
			array(
				'label'     => esc_html__( 'Box Right/Left Margin (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => -100,
				'max'       => 100,
				'step'      => 1,
				'default'   => 0,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-next' => 'right: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-testimonials-carousel .elementor-swiper-button-prev' => 'left: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation_dots',
			array(
				'label'     => esc_html__( 'Navigation Dots', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_dots' => 'yes' ),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'dots_size',
			array(
				'label'     => esc_html__( 'Dot Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 100,
				'step'      => 1,
				'default'   => 20,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .swiper-pagination-bullet' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'dot_margin',
			array(
				'label'     => esc_html__( 'Dot Right/Left Padding (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 10,
				'step'      => 1,
				'default'   => 2,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .swiper-pagination-bullet' => 'margin-left: {{VALUE}}px !important;margin-right: {{VALUE}}px !important;',
				),
			)
		);

		$this->add_responsive_control(
			'dots_bottom_margin',
			array(
				'label'     => esc_html__( 'Dots Bottom Margin (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => -100,
				'max'       => 100,
				'step'      => 1,
				'default'   => -40,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-testimonials-carousel .swiper-pagination' => 'bottom: {{VALUE}}px;',
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
		$settings  = $this->get_settings_for_display();
		$widget_id = $this->get_id();

		$columns_map    = array( 'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5 );
		$slides_to_show = isset( $columns_map[ $settings['columns'] ] ) ? $columns_map[ $settings['columns'] ] : 3;

		$show_nav  = ! empty( $settings['display_nav'] );
		$show_dots = ! empty( $settings['display_dots'] );
		if ( $show_nav && $show_dots ) {
			$navigation = 'both';
		} elseif ( $show_nav ) {
			$navigation = 'arrows';
		} elseif ( $show_dots ) {
			$navigation = 'dots';
		} else {
			$navigation = 'none';
		}

		$swiper_options = array(
			'slides_to_show' => $slides_to_show,
			'autoplay'       => ! empty( $settings['autoplay'] ) ? 'yes' : 'no',
			'autoplay_speed' => intval( $settings['autoplay_duration'] ) * 1000,
			'infinite'       => ! empty( $settings['infinite'] ) ? 'yes' : 'no',
			'navigation'     => $navigation,
			'speed'          => 300,
			'pause_on_hover' => 'yes',
		);
		?>
		<div id="wbcom-testimonials-carousel-<?php echo esc_attr( $widget_id ); ?>" class="wbcom-testimonials-carousel swiper-container <?php echo esc_attr( $settings['carousel_overflow_hidden'] ); ?>" <?php if ( is_rtl() ) { echo 'dir="rtl"'; } ?> data-settings="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>">
			<div class="swiper-wrapper">
			<?php foreach ( $settings['testimonials'] as $item ) { ?>
				<?php $img_url = wp_get_attachment_image_url( $item['image']['id'], $settings['img_size'] ); ?>
				<div class="swiper-slide">
					<div class="wbcom-testimonials-item">
						<div class="wbcom-testimonials-content <?php echo esc_attr( $settings['content_arrow'] ); ?>">
							<?php echo wp_kses_post( $item['content'] ); ?>
						</div>
						<div class="wbcom-testimonials-person">
							<?php if ( $img_url ) { ?>
							<div class="wbcom-testimonials-thumb"><img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" /></div>
							<?php } ?>
							<div class="wbcom-testimonials-info">
								<?php if ( $item['title'] ) { ?>
								<span class="wbcom-testimonials-title"><?php echo wp_kses_post( $item['title'] ); ?></span>
								<?php } ?>
								<?php if ( $item['subtitle'] ) { ?>
								<span class="wbcom-testimonials-subtitle"><?php echo wp_kses_post( $item['subtitle'] ); ?></span>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
			<?php if ( $show_dots ) : ?>
			<div class="swiper-pagination"></div>
			<?php endif; ?>
			<?php if ( $show_nav ) : ?>
			<div class="elementor-swiper-button elementor-swiper-button-prev">
				<svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-left" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"><path d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z"></path></svg>
				<span class="elementor-screen-only"><?php esc_html_e( 'Previous', 'wbcom-essential' ); ?></span>
			</div>
			<div class="elementor-swiper-button elementor-swiper-button-next">
				<svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-right" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"><path d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z"></path></svg>
				<span class="elementor-screen-only"><?php esc_html_e( 'Next', 'wbcom-essential' ); ?></span>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
