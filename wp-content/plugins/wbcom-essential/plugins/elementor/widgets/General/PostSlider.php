<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Post Slider
 *
 * Elementor widget for Post Slider
 *
 * @since 3.6.0
 */
class PostSlider extends \Elementor\Widget_Base {

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
		wp_register_style( 'wb-slider', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/slider.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/swiper.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wb-post-slider', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/post-slider.js', array( 'jquery', 'swiper' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-post-slider';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Post Slider', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-post-slider';
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
		return array( 'post', 'slider', 'slideshow', 'blog', 'featured' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-post-slider' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-slider', 'elementor-icons-fa-solid', 'elementor-icons-fa-regular' );
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
				'label' => esc_html__( 'Posts', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'   => esc_html__( 'Post Type', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post',
				'options' => wba_get_post_types(),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'Descending', 'wbcom-essential' ),
					'ASC'  => esc_html__( 'Ascending', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order By', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => array(
					'post_date'     => esc_html__( 'Date', 'wbcom-essential' ),
					'title'         => esc_html__( 'Title', 'wbcom-essential' ),
					'rand'          => esc_html__( 'Random', 'wbcom-essential' ),
					'comment_count' => esc_html__( 'Comment Count', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'taxonomy',
			array(
				'label'       => esc_html__( 'Categories', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => 'true',
				'multiple'    => true,
				'default'     => '',
				'options'     => wba_get_categories(),
				'condition'   => array( 'post_type' => 'post' ),
			)
		);

		$this->add_control(
			'tags',
			array(
				'label'       => esc_html__( 'Tags', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => 'true',
				'multiple'    => true,
				'default'     => '',
				'options'     => wba_get_tags(),
				'condition'   => array( 'post_type' => 'post' ),
			)
		);

		$this->add_control(
			'authors',
			array(
				'label'       => esc_html__( 'Authors', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => 'true',
				'multiple'    => true,
				'default'     => '',
				'options'     => wba_get_authors(),
				'condition'   => array( 'post_type' => 'post' ),
			)
		);

		$this->add_control(
			'max',
			array(
				'label'   => esc_html__( 'Maximum number of posts', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 99,
				'step'    => 1,
				'default' => 6,
			)
		);

		$this->add_control(
			'include',
			array(
				'label'       => esc_html__( 'Include posts by ID', 'wbcom-essential' ),
				'description' => esc_html__( 'To include multiple posts, add comma between IDs.', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
			)
		);

		$this->add_control(
			'exclude',
			array(
				'label'       => esc_html__( 'Exclude posts by ID', 'wbcom-essential' ),
				'description' => esc_html__( 'To exclude multiple posts, add comma between IDs.', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
			)
		);

		$this->add_control(
			'display_date',
			array(
				'label'        => esc_html__( 'Display date', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'display_btn',
			array(
				'label'        => esc_html__( 'Display button', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'btn_txt',
			array(
				'label'     => esc_html__( 'Button Text', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Read More', 'wbcom-essential' ),
				'condition' => array( 'display_btn' => 'yes' ),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'   => esc_html__( 'Excerpt length (To remove excerpt, enter "0")', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 1000,
				'step'    => 1,
				'default' => 140,
			)
		);

		$this->add_control(
			'posts_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'bg_entrance_animation',
			array(
				'label'       => esc_html__( 'Background Image Animation', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'none',
				'label_block' => 'true',
				'options'     => array(
					'none'              => esc_html__( 'None', 'wbcom-essential' ),
					'zoom'              => esc_html__( 'Zoom', 'wbcom-essential' ),
					'zoom-top'          => esc_html__( 'Zoom Top-Center', 'wbcom-essential' ),
					'zoom-top-right'    => esc_html__( 'Zoom Top-Right', 'wbcom-essential' ),
					'zoom-top-left'     => esc_html__( 'Zoom Top-Left', 'wbcom-essential' ),
					'zoom-bottom'       => esc_html__( 'Zoom Bottom-Center', 'wbcom-essential' ),
					'zoom-bottom-right' => esc_html__( 'Zoom Bottom-Right', 'wbcom-essential' ),
					'zoom-bottom-left'  => esc_html__( 'Zoom Bottom-Left', 'wbcom-essential' ),
					'zoom-left'         => esc_html__( 'Zoom Left', 'wbcom-essential' ),
					'zoom-right'        => esc_html__( 'Zoom Right', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'bg_entrance_animation_duration',
			array(
				'label'   => esc_html__( 'Animation Duration (second)', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0.1,
				'max'     => 10,
				'step'    => 0.1,
				'default' => 1,
			)
		);

		$this->add_control(
			'entrance_animation',
			array(
				'label' => esc_html__( 'Text Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ANIMATION,
			)
		);

		$this->add_control(
			'entrance_animation_duration',
			array(
				'label'       => esc_html__( 'Text Animation Duration', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'label_block' => 'true',
				'options'     => array(
					''       => esc_html__( 'Default', 'wbcom-essential' ),
					'fast'   => esc_html__( 'Fast', 'wbcom-essential' ),
					'faster' => esc_html__( 'Faster', 'wbcom-essential' ),
					'slow'   => esc_html__( 'Slow', 'wbcom-essential' ),
					'slower' => esc_html__( 'Slower', 'wbcom-essential' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'slider_section',
			array(
				'label' => esc_html__( 'Slider Settings', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'default_image',
			array(
				'label'   => esc_html__( 'Default Image', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_responsive_control(
			'slider_height',
			array(
				'label'      => esc_html__( 'Slider Height', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min'  => 10,
						'max'  => 1400,
						'step' => 5,
					),
					'vh' => array(
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 700,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider-inner'        => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-slide'              => 'min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-slider-text-wrapper' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-slider-loader'       => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'wbcom-essential' ),
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

		$this->add_control(
			'slide_anim',
			array(
				'label'       => esc_html__( 'Slide Transition', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'true',
				'label_block' => 'true',
				'options'     => array(
					'true'  => esc_html__( 'Fade', 'wbcom-essential' ),
					'false' => esc_html__( 'Slide', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'slide_anim_duration',
			array(
				'label'   => esc_html__( 'Slide Transition Duration (ms)', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 10,
				'default' => 300,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'slider_nav',
			array(
				'label' => esc_html__( 'Slider Navigation', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'hide_nav',
			array(
				'label'        => esc_html__( 'Show Navigation only on Hover', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_dots_title',
			array(
				'label'     => esc_html__( 'Navigation Dots', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'nav_dots',
			array(
				'label'        => esc_html__( 'Enable', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_thumbnails',
			array(
				'label'        => esc_html__( 'Enable Thumbnail Mode', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
				'condition'    => array( 'nav_dots' => 'yes' ),
			)
		);

		$this->add_control(
			'nav_dots_desktop',
			array(
				'label'        => esc_html__( 'Hide On Desktop', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hide', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Show', 'wbcom-essential' ),
				'return_value' => 'hide',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_dots_tablet',
			array(
				'label'        => esc_html__( 'Hide On Tablet', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hide', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Show', 'wbcom-essential' ),
				'return_value' => 'hide',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_dots_mobile',
			array(
				'label'        => esc_html__( 'Hide On Mobile', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hide', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Show', 'wbcom-essential' ),
				'return_value' => 'hide',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_arrows_title',
			array(
				'label'     => esc_html__( 'Navigation Arrows', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'nav_arrows',
			array(
				'label'        => esc_html__( 'Enable', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_arrows_desktop',
			array(
				'label'        => esc_html__( 'Hide On Desktop', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hide', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Show', 'wbcom-essential' ),
				'return_value' => 'hide',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_arrows_tablet',
			array(
				'label'        => esc_html__( 'Hide On Tablet', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hide', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Show', 'wbcom-essential' ),
				'return_value' => 'hide',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'nav_arrows_mobile',
			array(
				'label'        => esc_html__( 'Hide On Mobile', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hide', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Show', 'wbcom-essential' ),
				'return_value' => 'hide',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides',
			array(
				'label' => esc_html__( 'Slide', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'slide_bg_color',
			array(
				'label'     => esc_html__( 'Slide Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0073aa',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-inner'   => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-slider-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slide_overlay',
			array(
				'label'     => esc_html__( 'Overlay Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_position',
			array(
				'label'   => esc_html__( 'Background Image Position', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => array(
					'top left'      => esc_html__( 'Top Left', 'wbcom-essential' ),
					'top center'    => esc_html__( 'Top Center', 'wbcom-essential' ),
					'top right'     => esc_html__( 'Top Right', 'wbcom-essential' ),
					'center left'   => esc_html__( 'Center Left', 'wbcom-essential' ),
					'center center' => esc_html__( 'Center Center', 'wbcom-essential' ),
					'center right'  => esc_html__( 'Center Right', 'wbcom-essential' ),
					'bottom left'   => esc_html__( 'Bottom Left', 'wbcom-essential' ),
					'bottom center' => esc_html__( 'Bottom Center', 'wbcom-essential' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'image_repeat',
			array(
				'label'   => esc_html__( 'Background Image Repeat', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => array(
					'no-repeat' => esc_html__( 'No Repeat', 'wbcom-essential' ),
					'repeat'    => esc_html__( 'Repeat', 'wbcom-essential' ),
					'repeat-x'  => esc_html__( 'Repeat-x', 'wbcom-essential' ),
					'repeat-y'  => esc_html__( 'Repeat-y', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'image_bg_size',
			array(
				'label'   => esc_html__( 'Background Image Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => array(
					'cover'   => esc_html__( 'Cover', 'wbcom-essential' ),
					'contain' => esc_html__( 'Contain', 'wbcom-essential' ),
					'auto'    => esc_html__( 'Auto (Not recommended)', 'wbcom-essential' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box',
			array(
				'label' => esc_html__( 'Text Box', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'text_max_width',
			array(
				'label'      => esc_html__( 'Text Box Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 200,
						'max'  => 1400,
						'step' => 5,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 600,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider-text-box' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-text-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'slider_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'text_padding',
			array(
				'label'      => esc_html__( 'Text Box Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider-text-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_margin',
			array(
				'label'      => esc_html__( 'Text Box Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider-text-box' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'slider_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'text_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-slider-text-box',
			)
		);

		$this->add_responsive_control(
			'text_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider-text-box' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-slider-text-box',
			)
		);

		$this->add_control(
			'slider_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'wbcom-essential' ),
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
				'default' => 'h1',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-slider-title',
			)
		);

		$this->add_control(
			'desc_color',
			array(
				'label'     => esc_html__( 'Content Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-text-box .wbcom-slider-desc p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typography',
				'label'    => esc_html__( 'Content Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-slider-text-box .wbcom-slider-desc p',
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => esc_html__( 'Date Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-text-box .wbcom-slider-date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typography',
				'label'    => esc_html__( 'Date Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-slider-text-box .wbcom-slider-date',
			)
		);

		$this->add_control(
			'posts_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'text_box_align',
			array(
				'label'     => esc_html__( 'Horizontal Alignment', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .wbcom-slider-text-wrapper' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'text_box_valign',
			array(
				'label'     => esc_html__( 'Vertical Alignment', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .wbcom-slider-text-wrapper' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'text_align',
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
					'{{WRAPPER}} .wbcom-slider-text-wrapper .wbcom-slider-text-box' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button',
			array(
				'label'     => esc_html__( 'Button', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_btn' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcombtn-primary',
			)
		);

		$this->start_controls_tabs( 'tabs_btn_primary_style' );

		$this->start_controls_tab(
			'tab_btn_primary_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'btn_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcombtn-primary' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => array(
					'{{WRAPPER}} .wbcombtn-primary' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcombtn-primary',
			)
		);

		$this->add_responsive_control(
			'btn_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcombtn-primary' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcombtn-primary',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_btn_primary_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'btn_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcombtn-primary:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcombtn-primary:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcombtn-primary:hover',
			)
		);

		$this->add_responsive_control(
			'btn_hover_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcombtn-primary:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'btn_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcombtn-primary:hover',
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
			'btn_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'default'    => array(
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcombtn-primary' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'default'    => array(
					'top'      => '15',
					'right'    => '20',
					'bottom'   => '15',
					'left'     => '20',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcombtn-primary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_divider',
			array(
				'label' => esc_html__( 'Divider', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'divider_hide',
			array(
				'label'        => esc_html__( 'Hide', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0073aa',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-divider' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 5,
						'max'  => 1000,
						'step' => 5,
					),
					'%'  => array(
						'min'  => 5,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 60,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider-divider' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'divider_height',
			array(
				'label'     => esc_html__( 'Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 40,
				'step'      => 1,
				'default'   => 5,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-divider' => 'height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'divider_h_align',
			array(
				'label'     => esc_html__( 'Horizontal Alignment', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
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
					'{{WRAPPER}} .wbcom-slider-divider-wrapper' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'divider_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'default'    => array(
					'top'      => '20',
					'right'    => '0',
					'bottom'   => '20',
					'left'     => '0',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider-divider-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			array(
				'label'     => esc_html__( 'Navigation Arrows', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'nav_arrows' => 'yes' ),
			)
		);

		$this->add_control(
			'arrow_next_icon',
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
			'arrow_prev_icon',
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
			'arrow_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next' => 'background-color: {{VALUE}};',
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
			'arrow_hover_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Hover Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'arrow_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'     => esc_html__( 'Icon Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 100,
				'step'      => 1,
				'default'   => 30,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev' => 'font-size: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next' => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_box_size',
			array(
				'label'     => esc_html__( 'Box Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 200,
				'step'      => 1,
				'default'   => 60,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'arrow_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next,{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev',
			)
		);

		$this->add_responsive_control(
			'arrow_radius',
			array(
				'label'      => esc_html__( 'Box Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_box_margin',
			array(
				'label'     => esc_html__( 'Box Right/Left Margin (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => -100,
				'max'       => 100,
				'step'      => 1,
				'default'   => 0,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-next' => 'right: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-slider .elementor-swiper-button-prev' => 'left: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation_thumbnails',
			array(
				'label'     => esc_html__( 'Navigation Thumbnails', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'nav_dots' => 'yes' ),
			)
		);

		$this->add_control(
			'nav_thumbnails_position',
			array(
				'label'   => esc_html__( 'Position', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'wbcom-dots-inside',
				'options' => array(
					'wbcom-dots-inside'  => esc_html__( 'Inside', 'wbcom-essential' ),
					'wbcom-dots-outside' => esc_html__( 'Outside', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'nav_thumbnails_margin',
			array(
				'label'      => esc_html__( 'Container Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_thumbnails_padding',
			array(
				'label'      => esc_html__( 'Container Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_margin',
			array(
				'label'      => esc_html__( 'Thumbnail Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_padding',
			array(
				'label'      => esc_html__( 'Thumbnail Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'nav_thumbnail_size',
			array(
				'label'   => esc_html__( 'Thumbnail Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'thumbnail',
				'options' => array(
					'thumbnail' => esc_html__( 'Thumbnail', 'wbcom-essential' ),
					'medium'    => esc_html__( 'Medium', 'wbcom-essential' ),
					'large'     => esc_html__( 'Large', 'wbcom-essential' ),
					'full'      => esc_html__( 'Full (Not recommended)', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_align',
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
					'{{WRAPPER}} .wbcom-thumbnail-dots' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_width',
			array(
				'label'      => esc_html__( 'Thumbnail Max. Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', '%' ),
				'range'      => array(
					'px'  => array(
						'min'  => 10,
						'max'  => 1400,
						'step' => 5,
					),
					'rem' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
					'%'   => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'nav_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_thumbnail_style' );

		$this->start_controls_tab(
			'tab_thumbnail_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_opacity',
			array(
				'label'     => esc_html__( 'Thumbnail Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_thumbnail_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img',
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nav_thumbnail_shadow',
				'label'    => esc_html__( 'Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbnail_hover',
			array(
				'label' => esc_html__( 'Active', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_hover_opacity',
			array(
				'label'     => esc_html__( 'Thumbnail Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide-thumb-active img' => 'opacity: {{VALUE}};',
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img:hover' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'nav_thumbnail_border_hover',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide-thumb-active img,{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img:hover',
			)
		);

		$this->add_responsive_control(
			'nav_thumbnail_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide-thumb-active img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'nav_thumbnail_shadow_hover',
				'label'    => esc_html__( 'Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide-thumb-active img,{{WRAPPER}} .wbcom-thumbnail-dots .swiper-slide img:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation_dots',
			array(
				'label'     => esc_html__( 'Navigation Dots', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'nav_dots' => 'yes' ),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-slider .swiper-pagination-bullet' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-slider .swiper-pagination-bullet' => 'margin-left: {{VALUE}}px !important;margin-right: {{VALUE}}px !important;',
				),
			)
		);

		$this->add_responsive_control(
			'dots_bottom_margin',
			array(
				'label'     => esc_html__( 'Dots Bottom Margin (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 100,
				'step'      => 1,
				'default'   => 20,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider .swiper-pagination' => 'bottom: {{VALUE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_loader',
			array(
				'label' => esc_html__( 'Loader', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'loader_bg_color',
			array(
				'label'     => esc_html__( 'Container Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0073aa',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-loader' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'loader_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-css3-loader.wbcom-slider-loader:before' => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'css_loader_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.1)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-css3-loader.wbcom-slider-loader:before' => 'border-left-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'loader_thickness',
			array(
				'label'     => esc_html__( 'Thickness', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 5,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-css3-loader.wbcom-slider-loader:before' => 'border-width: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'loader_size',
			array(
				'label'     => esc_html__( 'Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 20,
				'max'       => 200,
				'step'      => 1,
				'default'   => 50,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-css3-loader.wbcom-slider-loader:before' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'loader_duration',
			array(
				'label'     => esc_html__( 'Animation Duration (seconds)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-css3-loader.wbcom-slider-loader:before' => 'animation-duration: {{VALUE}}s;',
				),
			)
		);

		$this->add_control(
			'loader_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'loader_image',
			array(
				'label' => esc_html__( 'Custom Loading Image', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'loader_image_size',
			array(
				'label'     => esc_html__( 'Loading Image Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 500,
				'step'      => 1,
				'default'   => 60,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-slider-loader' => 'background-size: {{VALUE}}px;',
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
		$wbcomslider_slider_id = $this->get_id();
		$settings            = $this->get_settings_for_display();
		$postype             = $settings['post_type'];
		$order               = $settings['order'];
		$orderby             = $settings['orderby'];
		$max                 = $settings['max'];
		$authors             = $settings['authors'];
		$categories          = $settings['taxonomy'];
		$tags                = $settings['tags'];

		$terms = array();
		if ( empty( $authors ) ) {
			$authors = array();
		}

		if ( $categories && $tags ) {
			$terms = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $categories,
				),
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => $tags,
				),
			);
		} elseif ( $categories ) {
			$terms = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $categories,
				),
			);
		} elseif ( $tags ) {
			$terms = array(
				array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => $tags,
				),
			);
		}

		if ( $settings['exclude'] ) {
			$exclude = explode( ',', $settings['exclude'] );
		} else {
			$exclude = array();
		}

		if ( $settings['include'] ) {
			$include = explode( ',', $settings['include'] );
		} else {
			$include = array();
		}

		$custom_query = new WP_Query(
			array(
				'post_type'           => $postype,
				'post_status'         => 'publish',
				'posts_per_page'      => $max,
				'order'               => $order,
				'orderby'             => $orderby,
				'post__in'            => $include,
				'post__not_in'        => $exclude,
				'author__in'          => $authors,
				'ignore_sticky_posts' => true,
				'tax_query'           => $terms,
			)
		);

		if ( $custom_query->have_posts() ) {
			// Build Swiper settings JSON.
			$swiper_settings = array(
				'slide_anim'        => $settings['slide_anim'],
				'speed'             => (int) $settings['slide_anim_duration'],
				'autoplay'          => ! empty( $settings['autoplay'] ),
				'autoplay_duration' => (int) $settings['autoplay_duration'] * 1000,
				'nav_arrows'        => ! empty( $settings['nav_arrows'] ),
				'nav_dots'          => ! empty( $settings['nav_dots'] ),
				'nav_thumbnails'    => $settings['nav_thumbnails'],
				'rtl'               => is_rtl(),
			);

			// Collect thumbnail data for building the thumbnail Swiper.
			$slides_data = array();
			while ( $custom_query->have_posts() ) {
				$custom_query->the_post();
				if ( has_post_thumbnail() ) {
					$image_id        = get_post_thumbnail_id();
					$thumb_url_array = wp_get_attachment_image_src( $image_id, $settings['nav_thumbnail_size'], true );
					$thumb_url       = $thumb_url_array[0];
					$image_url_array = wp_get_attachment_image_src( $image_id, 'full', true );
					$image_url       = $image_url_array[0];
				} else {
					$thumb_url = wp_get_attachment_image_url( $settings['default_image']['id'], 'nav_thumbnail_size' );
					$image_url = wp_get_attachment_image_url( $settings['default_image']['id'], 'full' );
				}
				// Pre-compute the trimmed excerpt while in the loop context.
				$trimmed_excerpt = '';
				if ( get_the_excerpt() && ! empty( $settings['excerpt_length'] ) && 0 !== (int) $settings['excerpt_length'] ) {
					$trimmed_excerpt = wba_excerpt( $settings['excerpt_length'] );
				}
				$slides_data[] = array(
					'thumb_url'        => $thumb_url,
					'image_url'        => $image_url,
					'title'            => get_the_title(),
					'title_attr'       => get_the_title(),
					'permalink'        => get_the_permalink(),
					'date'             => get_the_time( get_option( 'date_format' ) ),
					'excerpt'          => get_the_excerpt(),
					'trimmed_excerpt'  => $trimmed_excerpt,
				);
			}
			wp_reset_postdata();

			$dir_attr = is_rtl() ? ' dir="rtl"' : '';
			?>
			<div class="wbcom-slider-wrapper <?php echo ! empty( $settings['hide_nav'] ) ? esc_attr( 'hide-nav' ) : ''; ?>">
				<div class="wbcom-slider-loader <?php echo empty( $settings['loader_image']['url'] ) ? 'wbcom-css3-loader' : ''; ?>" style="<?php echo ! empty( $settings['loader_image']['url'] ) ? 'background-image:url(' . esc_url( $settings['loader_image']['url'] ) . ');' : ''; ?>"></div>
				<div id="wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?>" class="wbcom-slider swiper-container swiper"<?php echo $dir_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-settings="<?php echo esc_attr( wp_json_encode( $swiper_settings ) ); ?>">
					<div class="swiper-wrapper">
						<?php foreach ( $slides_data as $slide ) : ?>
						<div class="swiper-slide">
							<div class="wbcom-slider-inner animated none <?php echo esc_attr( $settings['bg_entrance_animation'] ); ?>" style="background-image:url(<?php echo esc_url( $slide['image_url'] ); ?>);background-position:<?php echo esc_attr( $settings['image_position'] ); ?>;background-repeat:<?php echo esc_attr( $settings['image_repeat'] ); ?>;background-size:<?php echo esc_attr( $settings['image_bg_size'] ); ?>;transition-duration:<?php echo esc_attr( $settings['bg_entrance_animation_duration'] ); ?>s;"></div>
							<?php if ( 'yes' !== $settings['display_btn'] ) { ?>
							<a class="wbcom-slider-url" href="<?php echo esc_url( $slide['permalink'] ); ?>"></a>
							<?php } ?>
							<div class="wbcom-slider-overlay"></div>
							<div class="wbcom-slider-text-wrapper">
								<div class="wbcom-slider-text-box noanim animated <?php echo esc_attr( $settings['entrance_animation_duration'] ); ?> <?php echo esc_attr( $settings['entrance_animation'] ); ?>">
								<?php if ( 'yes' === $settings['display_date'] ) { ?>
								<div class="wbcom-slider-date"><?php echo esc_html( $slide['date'] ); ?></div>
								<?php } ?>
								<?php
								if ( $slide['title'] ) {
									echo '<' . esc_attr( $settings['title_html_tag'] ) . ' class="wbcom-slider-title">' . esc_html( $slide['title'] ) . '</' . esc_attr( $settings['title_html_tag'] ) . '>';
								}
								?>
								<?php if ( 'yes' !== $settings['divider_hide'] ) { ?>
								<div class="wbcom-slider-divider-wrapper">
									<div class="wbcom-slider-divider"></div>
								</div>
								<?php } ?>
								<?php
								if ( ! empty( $slide['trimmed_excerpt'] ) ) {
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wba_excerpt output is safe
									echo '<div class="wbcom-slider-desc"><p>' . $slide['trimmed_excerpt'] . '</p></div>';
								}
								if ( ! empty( $settings['display_btn'] ) ) {
									echo '<a href="' . esc_url( $slide['permalink'] ) . '" class="wbcombtn wbcombtn-primary">' . esc_html( $settings['btn_txt'] ) . '</a>';
								}
								?>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
					<?php if ( ! empty( $settings['nav_dots'] ) && empty( $settings['nav_thumbnails'] ) ) : ?>
					<div class="swiper-pagination"></div>
					<?php endif; ?>
					<?php if ( ! empty( $settings['nav_arrows'] ) ) : ?>
					<div class="elementor-swiper-button-prev" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Previous slide', 'wbcom-essential' ); ?>">
						<?php if ( ! empty( $settings['arrow_prev_icon']['value'] ) ) : ?>
							<i class="<?php echo esc_attr( $settings['arrow_prev_icon']['value'] ); ?>"></i>
						<?php else : ?>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 44" width="1em" height="1em"><path d="M0,22L22,0l2.1,2.1L4.2,22l19.9,19.9L22,44L0,22L0,22z" fill="currentColor"></path></svg>
						<?php endif; ?>
					</div>
					<div class="elementor-swiper-button-next" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Next slide', 'wbcom-essential' ); ?>">
						<?php if ( ! empty( $settings['arrow_next_icon']['value'] ) ) : ?>
							<i class="<?php echo esc_attr( $settings['arrow_next_icon']['value'] ); ?>"></i>
						<?php else : ?>
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 44" width="1em" height="1em"><path d="M27,22L27,22L5,44l-2.1-2.1L22.8,22L2.9,2.1L5,0L27,22L27,22z" fill="currentColor"></path></svg>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $settings['nav_dots'] ) && ! empty( $settings['nav_thumbnails'] ) ) : ?>
				<div id="wbcom-slider-thumbnails-<?php echo esc_attr( $wbcomslider_slider_id ); ?>" class="wbcom-slider-thumbnails wbcom-thumbnail-dots swiper-container swiper <?php echo esc_attr( $settings['nav_thumbnails_position'] ); ?>"<?php echo $dir_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div class="swiper-wrapper">
						<?php foreach ( $slides_data as $slide ) : ?>
						<div class="swiper-slide">
							<img src="<?php echo esc_url( $slide['thumb_url'] ); ?>" alt="<?php echo esc_attr( $slide['title_attr'] ); ?>">
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		<style type="text/css">
			<?php
			$viewport_lg = 1024;
			$viewport_md = 767;
			?>
			@media screen and (min-width: <?php echo esc_attr( $viewport_lg + 1 ) . 'px'; ?>) {
				<?php if ( $settings['nav_arrows_desktop'] ) { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-prev,
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-next {
					display: none !important;
				}
				<?php } else { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-prev,
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-next {
					display: flex !important;
				}
				<?php } ?>
				<?php if ( $settings['nav_dots_desktop'] ) { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .swiper-pagination,
				#wbcom-slider-thumbnails-<?php echo esc_attr( $wbcomslider_slider_id ); ?> {
					display: none !important;
				}
				<?php } else { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .swiper-pagination,
				#wbcom-slider-thumbnails-<?php echo esc_attr( $wbcomslider_slider_id ); ?> {
					display: flex !important;
				}
				<?php } ?>
			}
			@media only screen and (max-width: <?php echo esc_attr( $viewport_lg ) . 'px'; ?>) {
				<?php if ( $settings['nav_arrows_tablet'] ) { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-prev,
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-next {
					display: none !important;
				}
				<?php } else { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-prev,
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-next {
					display: flex !important;
				}
				<?php } ?>
				<?php if ( $settings['nav_dots_tablet'] ) { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .swiper-pagination,
				#wbcom-slider-thumbnails-<?php echo esc_attr( $wbcomslider_slider_id ); ?> {
					display: none !important;
				}
				<?php } else { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .swiper-pagination,
				#wbcom-slider-thumbnails-<?php echo esc_attr( $wbcomslider_slider_id ); ?> {
					display: flex !important;
				}
				<?php } ?>
			}
			@media screen and (max-width: <?php echo esc_attr( $viewport_md ) . 'px'; ?>) {
				<?php if ( $settings['nav_arrows_mobile'] ) { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-prev,
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-next {
					display: none !important;
				}
				<?php } else { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-prev,
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .elementor-swiper-button-next {
					display: flex !important;
				}
				<?php } ?>
				<?php if ( $settings['nav_dots_mobile'] ) { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .swiper-pagination,
				#wbcom-slider-thumbnails-<?php echo esc_attr( $wbcomslider_slider_id ); ?> {
					display: none !important;
				}
				<?php } else { ?>
				#wbcom-slider-<?php echo esc_attr( $wbcomslider_slider_id ); ?> .swiper-pagination,
				#wbcom-slider-thumbnails-<?php echo esc_attr( $wbcomslider_slider_id ); ?> {
					display: flex !important;
				}
				<?php } ?>
			}
	</style>
	<?php } else { ?>
		<div class="wbcom-danger"><?php esc_html_e( 'No post found!', 'wbcom-essential' ); ?></div>
			<?php
	}
	}
}