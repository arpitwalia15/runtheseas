<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor TeamCarousel
 *
 * Elementor widget for TeamCarousel
 *
 * @since 3.6.0
 */
class TeamCarousel extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wbcom-animations', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/animations.min.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wb-lib-lightbox', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/library/featherlight.min.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wbe-swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/swiper-base.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wb-team-carousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/team-carousel.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/swiper.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wb-lib-lightbox', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/library/featherlight.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wb-team-carousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/team-carousel.js', array( 'jquery', 'swiper', 'wb-lib-lightbox' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-team-carousel';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Team Carousel', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-person';
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
		return array( 'team', 'carousel', 'members', 'staff', 'slider' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-lib-lightbox', 'wb-team-carousel' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'elementor-icons-fa-solid', 'wbcom-animations', 'wb-lib-lightbox', 'wb-team-carousel' );
	}

	/**
	 * Get animation.
	 */
	function get_anim_exits( $animation ) {
		if ( $animation ) {
			$animation_array = array(
				'bounce'            => 'fadeOut',
				'flash'             => 'fadeOut',
				'pulse'             => 'fadeOut',
				'rubberBand'        => 'fadeOut',
				'shake'             => 'fadeOut',
				'swing'             => 'fadeOut',
				'tada'              => 'fadeOut',
				'wobble'            => 'fadeOut',
				'jello'             => 'fadeOut',
				'heartBeat'         => 'fadeOut',
				'bounceIn'          => 'bounceOut',
				'bounceInDown'      => 'bounceOutUp',
				'bounceInLeft'      => 'bounceOutLeft',
				'bounceInRight'     => 'bounceOutRight',
				'bounceInUp'        => 'bounceOutDown',
				'fadeIn'            => 'fadeOut',
				'fadeInDown'        => 'fadeOutUp',
				'fadeInDownBig'     => 'fadeOutUpBig',
				'fadeInLeft'        => 'fadeOutLeft',
				'fadeInLeftBig'     => 'fadeOutLeftBig',
				'fadeInRight'       => 'fadeOutRight',
				'fadeInRightBig'    => 'fadeOutRightBig',
				'fadeInUp'          => 'fadeOutDown',
				'fadeInUpBig'       => 'fadeOutDownBig',
				'flip'              => 'fadeOut',
				'flipInX'           => 'flipOutX',
				'flipInY'           => 'flipOutY',
				'lightSpeedIn'      => 'lightSpeedOut',
				'rotateIn'          => 'rotateOut',
				'rotateInDownLeft'  => 'rotateOutUpLeft',
				'rotateInDownRight' => 'rotateOutUpRight',
				'rotateInUpLeft'    => 'rotateOutDownLeft',
				'rotateInUpRight'   => 'rotateOutDownRight',
				'slideInUp'         => 'slideOutDown',
				'slideInDown'       => 'slideOutUp',
				'slideInLeft'       => 'slideOutLeft',
				'slideInRight'      => 'slideOutRight',
				'zoomIn'            => 'zoomOut',
				'zoomInDown'        => 'zoomOutUp',
				'zoomInLeft'        => 'zoomOutLeft',
				'zoomInRight'       => 'zoomOutRight',
				'zoomInUp'          => 'zoomOutDown',
				'hinge'             => 'fadeOut',
				'jackInTheBox'      => 'fadeOut',
				'rollIn'            => 'fadeOut',
			);
			$animation       = $animation_array[ $animation ];
			return $animation;
		}
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
			'settings_section',
			array(
				'label' => esc_html__( 'Team', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'      => esc_html__( 'Thumbnail', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'show_label' => false,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Name', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'John Doe', 'wbcom-essential' ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Info', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Web Designer', 'wbcom-essential' ),
			)
		);

		$repeater->add_control(
			'heading_lightbox',
			array(
				'label'     => esc_html__( 'Link to', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'lightbox_style',
			array(
				'label'       => esc_html__( 'Link to', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'none',
				'options'     => array(
					'none'     => esc_html__( 'No link', 'wbcom-essential' ),
					'external' => esc_html__( 'External Url', 'wbcom-essential' ),
					'img'      => esc_html__( 'Image', 'wbcom-essential' ),
					'video'    => esc_html__( 'Video', 'wbcom-essential' ),
				),
				'label_block' => true,
				'show_label'  => false,
			)
		);

		$repeater->add_control(
			'external_link',
			array(
				'label'         => esc_html__( 'Destination Url', 'wbcom-essential' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'wbcom-essential' ),
				'show_external' => true,
				'condition'     => array( 'lightbox_style' => 'external' ),
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

		$repeater->add_control(
			'lightbox_image',
			array(
				'label'      => esc_html__( 'Lightbox Image', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'condition'  => array( 'lightbox_style' => 'img' ),
				'show_label' => false,
			)
		);

		$repeater->add_control(
			'oembed',
			array(
				'label'       => esc_html__( 'Lightbox Video URL', 'wbcom-essential' ),
				'description' => esc_html__( 'For example: https://www.youtube.com/watch?v=8AZ8GqW5iak', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array( 'lightbox_style' => 'video' ),
				'input_type'  => 'url',
				'show_label'  => false,
			)
		);

		$repeater->add_control(
			'lightbox_content',
			array(
				'label'      => esc_html__( 'Lightbox Content', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::HEADING,
				'separator'  => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'lightbox_style',
							'value' => 'img',
						),
						array(
							'name'  => 'lightbox_style',
							'value' => 'video',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'box_content',
			array(
				'label'       => esc_html__( 'Lightbox Content', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => '',
				'label_block' => true,
				'show_label'  => false,
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'lightbox_style',
							'value' => 'img',
						),
						array(
							'name'  => 'lightbox_style',
							'value' => 'video',
						),
					),
				),
			)
		);

		$this->add_control(
			'gallery',
			array(
				'label'       => esc_html__( 'Team Members', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'title'            => esc_html__( 'Title #1', 'wbcom-essential' ),
						'subtitle'         => esc_html__( 'Subtitle #1', 'wbcom-essential' ),
						'image'            => \Elementor\Utils::get_placeholder_image_src(),
						'lightbox_image'   => '',
						'oembed'           => '',
						'lightbox_content' => '',
					),
					array(
						'title'            => esc_html__( 'Title #2', 'wbcom-essential' ),
						'subtitle'         => esc_html__( 'Subtitle #2', 'wbcom-essential' ),
						'image'            => \Elementor\Utils::get_placeholder_image_src(),
						'lightbox_image'   => '',
						'oembed'           => '',
						'lightbox_content' => '',
					),
					array(
						'title'            => esc_html__( 'Title #3', 'wbcom-essential' ),
						'subtitle'         => esc_html__( 'Subtitle #3', 'wbcom-essential' ),
						'image'            => \Elementor\Utils::get_placeholder_image_src(),
						'lightbox_image'   => '',
						'oembed'           => '',
						'lightbox_content' => '',
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
				'default'      => 'yes',
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
				'default'      => '',
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

		// section start.
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
				'default' => 'large',
				'options' => wba_get_image_sizes(),
			)
		);

		$this->add_responsive_control(
			'max_img_size',
			array(
				'label'     => esc_html__( 'Max. Thumb Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 100,
				'max'       => 2000,
				'step'      => 10,
				'default'   => 600,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-member' => 'max-width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'img_h_align',
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
					'{{WRAPPER}} .wbcom-team-member-wrapper' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'thumbnail_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-member a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thumbnail_opacity_duration',
			array(
				'label'     => esc_html__( 'Opacity Animation Duration', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 10,
				'step'      => 0.1,
				'default'   => 0.2,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-member img' => 'transition-duration: {{VALUE}}s;',
				),
			)
		);

		$this->add_control(
			'thumb_hr_1',
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

		$this->add_control(
			'thumbnail_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-member img' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'thumbnail_css_filter',
				'label'    => esc_html__( 'CSS Filters', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-member img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbnail_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-member a:hover img' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'thumbnail_hover_css_filter',
				'label'    => esc_html__( 'CSS Filters', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-member a:hover img',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'thumb_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'thumb_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-member',
			)
		);

		$this->add_responsive_control(
			'thumb_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-member a'   => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-team-member img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-team-member'     => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'thumb_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-member',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_carousel',
			array(
				'label' => esc_html__( 'Carousel', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .swiper-slide'   => 'margin-left: {{LEFT}}{{UNIT}};margin-right: {{RIGHT}}{{UNIT}};margin-top: {{TOP}}{{UNIT}};margin-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .swiper-wrapper' => 'margin-left: -{{LEFT}}{{UNIT}};margin-right: -{{RIGHT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_caption',
			array(
				'label' => esc_html__( 'Box Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'caption_placement',
			array(
				'label'   => esc_html__( 'Placement', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'in-img',
				'options' => array(
					'in-img'    => esc_html__( 'On the image', 'wbcom-essential' ),
					'below-img' => esc_html__( 'Below the image', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'caption_style',
			array(
				'label'   => esc_html__( 'Show', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'name',
				'options' => array(
					'name' => esc_html__( 'Name & Info', 'wbcom-essential' ),
					'icon' => esc_html__( 'Icon', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'caption_align',
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
					'{{WRAPPER}} .wbcom-team-overlay' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'caption_valign',
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
					'{{WRAPPER}} .wbcom-team-overlay' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'caption_text_align',
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
					'{{WRAPPER}} .wbcom-team-texts' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'box_content_animation',
			array(
				'label' => esc_html__( 'Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->add_control(
			'overflow_hidden',
			array(
				'label'        => esc_html__( 'Overflow', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Hidden', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'Auto', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'box_content_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'box_content_style' );

		$this->start_controls_tab(
			'box_content_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'box_content_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-overlay' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_content_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-team-overlay',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_content_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-overlay',
			)
		);

		$this->add_responsive_control(
			'box_content_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-overlay' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'box_content_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'box_content_hover_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-overlay:hover' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'box_content_hover_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-team-overlay:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_content_hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-overlay:hover',
			)
		);

		$this->add_responsive_control(
			'box_content_hover_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-overlay:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'box_content_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'box_content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_title',
			array(
				'label'     => esc_html__( 'Title', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'caption_style' => 'name' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-team-overlay .wbcom-team-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-title',
			)
		);

		$this->add_control(
			'title_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-title span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'title_entrance_animation',
			array(
				'label' => esc_html__( 'Entrance Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ANIMATION,
			)
		);

		$this->add_control(
			'title_entrance_animation_duration',
			array(
				'label'       => esc_html__( 'Entrance Animation Duration', 'wbcom-essential' ),
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

		$this->add_control(
			'title_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-title span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_subtitle',
			array(
				'label'     => esc_html__( 'Subtitle', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'caption_style' => 'name' ),
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-team-overlay .wbcom-team-subtitle',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'subtitle_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-subtitle',
			)
		);

		$this->add_control(
			'subtitle_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-subtitle span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subtitle_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'subtitle_entrance_animation',
			array(
				'label' => esc_html__( 'Entrance Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ANIMATION,
			)
		);

		$this->add_control(
			'subtitle_entrance_animation_duration',
			array(
				'label'       => esc_html__( 'Entrance Animation Duration', 'wbcom-essential' ),
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

		$this->add_control(
			'subtitle_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'subtitle_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-subtitle span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'subtitle_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-overlay .wbcom-team-texts .wbcom-team-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_icon',
			array(
				'label'     => esc_html__( 'Icon', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'caption_style' => 'icon' ),
			)
		);

		$this->add_control(
			'thumb_icon',
			array(
				'label'   => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-search',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
			'icon_animation',
			array(
				'label' => esc_html__( 'Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'     => esc_html__( 'Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 100,
				'step'      => 1,
				'default'   => 30,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-icon i' => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-icon i' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'icon_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-icon i',
			)
		);

		$this->add_control(
			'icon_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'icon_width',
			array(
				'label'     => esc_html__( 'Width (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 300,
				'step'      => 1,
				'default'   => 30,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-icon' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'icon_height',
			array(
				'label'     => esc_html__( 'Height (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 300,
				'step'      => 1,
				'default'   => 30,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-icon'   => 'height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-team-icon i' => 'line-height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-icon' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-team-icon i',
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-team-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_lightbox',
			array(
				'label' => esc_html__( 'Lightbox', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'lightbox_bg_color',
			array(
				'label'   => esc_html__( 'Content Background Color', 'wbcom-essential' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#ffffff',
			)
		);

		$this->add_control(
			'box_width',
			array(
				'label'   => esc_html__( 'Maximum Lightbox Width (px)', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 10,
				'default' => 800,
			)
		);

		$this->add_control(
			'lightbox_spacing',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-lightbox-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next' => 'background-color: {{VALUE}};',
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
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev' => 'font-size: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next' => 'font-size: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-next' => 'right: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-team-carousel .elementor-swiper-button-prev' => 'left: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-team-carousel .swiper-pagination-bullet' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-team-carousel .swiper-pagination-bullet' => 'font-size: {{VALUE}}px;line-height: {{VALUE}}px;width: {{VALUE}}px;height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-team-carousel .swiper-pagination-bullet' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-team-carousel .swiper-pagination-bullet' => 'margin-left: {{VALUE}}px !important;margin-right: {{VALUE}}px !important;',
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
				'default'   => 20,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-team-carousel .swiper-pagination' => 'bottom: {{VALUE}}px;',
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

		if ( $settings['gallery'] ) {
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
        <div id="wbcom-team-carousel-<?php echo esc_attr( $widget_id ); ?>" class="wbcom-team-carousel swiper-container <?php echo esc_attr( $settings['carousel_overflow_hidden'] ); ?>" <?php if ( is_rtl() ) { echo 'dir="rtl"'; } ?> data-settings="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>">
			<div class="swiper-wrapper">
        <?php foreach ( $settings['gallery'] as $item ) { ?>
            <?php
            $img_url = wp_get_attachment_image_url( $item['image']['id'], $settings['img_size'] );
            if (!$img_url) {
            $img_url = $item['image']['url'];
            }
            ?>
            <div class="swiper-slide">
            <div class="wbcom-carousel-item">
                <div class="wbcom-team-member-wrapper">
                    <div class="wbcom-team-member <?php echo esc_attr($settings['caption_placement']); ?>">
                        <?php if ($item['lightbox_style'] == 'external') { ?>
                        <?php
                        $target = $item['external_link']['is_external'] ? ' target="_blank"' : '';
                        $nofollow = $item['external_link']['nofollow'] ? ' rel="nofollow"' : '';
                        ?>
                        <a href="<?php echo esc_url($item['external_link']['url']); ?>" <?php echo esc_attr( $target ); ?> <?php echo esc_attr( $nofollow ); ?> data-elementor-open-lightbox="no" class="<?php if ($settings['overflow_hidden']) { echo 'no-overlay'; } ?>">
                        <?php } elseif ($item['lightbox_style'] != 'none') { ?>
                        <a href="#wbcom-lightbox-<?php echo esc_attr($item['_id']); ?>" data-elementor-open-lightbox="no" class="has-lightbox <?php if ($settings['overflow_hidden']) { echo 'no-overlay'; } ?>">
                        <?php } ?>
                            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                            <?php if (($settings['caption_style'] == 'name') && (($item['title']) || ($item['subtitle']))) { ?>
                            <div class="wbcom-team-overlay elementor-animation-<?php echo esc_attr($settings['box_content_animation']); ?>  <?php if ($settings['overflow_hidden']) { echo 'no-overlay'; } ?>">
                                <div class="wbcom-team-texts">
                                    <?php if ($item['title']) { ?>
                                    <div class="wbcom-team-title <?php if (($settings['title_entrance_animation']) && ($settings['title_entrance_animation'] != 'none')) { ?>animated wbcom-hide<?php } ?> <?php echo esc_attr( $settings['title_entrance_animation_duration'] ); ?>" data-animation="<?php echo esc_attr( $settings['title_entrance_animation'] ); ?>" data-exit="<?php echo esc_attr( $this->get_anim_exits($settings['title_entrance_animation']) ); ?>">
                                        <span><?php echo esc_html($item['title']); ?></span>
                                    </div>
                                    <?php } ?>
                                    <?php if ($item['subtitle']) { ?>
                                    <div class="wbcom-team-subtitle <?php if (($settings['subtitle_entrance_animation']) && ($settings['subtitle_entrance_animation'] != 'none')) { ?>animated wbcom-hide<?php } ?> <?php echo esc_attr( $settings['subtitle_entrance_animation_duration'] ); ?>" data-animation="<?php echo esc_attr( $settings['subtitle_entrance_animation'] ); ?>" data-exit="<?php echo esc_attr( $this->get_anim_exits($settings['subtitle_entrance_animation']) ); ?>">
                                        <span><?php echo esc_html($item['subtitle']); ?></span>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } elseif ($settings['caption_style'] == 'icon') { ?>
                            <div class="wbcom-team-overlay elementor-animation-<?php echo esc_attr($settings['icon_animation']); ?> <?php if ($settings['overflow_hidden']) { echo 'no-overlay'; } ?>">
                                <div class="wbcom-team-icon"><?php \Elementor\Icons_Manager::render_icon( $settings['thumb_icon'], [ 'aria-hidden' => 'true' ] ); ?></div>
                            </div>
                            <?php } ?>
                        <?php if ($item['lightbox_style'] != 'none') { ?>
                        </a>
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
        <?php foreach ( $settings['gallery'] as $item ) { ?>
        <?php 
        if (($item['lightbox_style'] == 'img') || ($item['lightbox_style'] == 'video')) {
        /**
        * Lightbox content
        */
        $lightbox_image = $item['lightbox_image'];    
        $video_url = $item['oembed']; 
        $box_content = $item['box_content'];
        ?>
        <div id="wbcom-lightbox-<?php echo esc_attr($item['_id']); ?>" class="wbcom-lightbox-oembed">
            <?php if (($video_url) && ($item['lightbox_style'] == 'video')) { ?>
            <div class="wbcom-lightbox-iframe">
            <?php
            $args = array(
                'width' => $settings['box_width']
            );
            ?>
            <?php $oembed = wp_oembed_get( $item['oembed'], $args ); ?>
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_oembed_get returns safe HTML from WordPress oEmbed
            echo ( $oembed ) ? $oembed : esc_url( $settings['oembed'] ); ?>
            </div>
            <?php } elseif (($item['lightbox_image']['url']) && ($item['lightbox_style'] == 'img')) { ?>
            <?php $lightbox_image_url = wp_get_attachment_image_url( $item['lightbox_image']['id'], 'full' );  ?>
            <div class="wbcom-lightbox-image" style="max-width:<?php echo esc_attr($settings['box_width']); ?>px;">
                <img src="<?php echo esc_url($lightbox_image_url); ?>" alt="" />
            </div>
            <?php } ?>
            <?php if ($box_content) { ?>
            <div class="wbcom-lightbox-content" style="max-width:<?php echo esc_attr($settings['box_width']); ?>px;background-color:<?php echo esc_attr($settings['lightbox_bg_color']); ?>;padding:<?php echo esc_attr($settings['lightbox_spacing']['top'] . $settings['lightbox_spacing']['unit']); ?> <?php echo esc_attr($settings['lightbox_spacing']['right'] . $settings['lightbox_spacing']['unit']); ?> <?php echo esc_attr($settings['lightbox_spacing']['bottom'] . $settings['lightbox_spacing']['unit']); ?> <?php echo esc_attr($settings['lightbox_spacing']['left'] . $settings['lightbox_spacing']['unit']); ?>">
                <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_shortcode() output is trusted shortcode content.
                echo do_shortcode($box_content); ?>
            </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php } ?>
    <?php }
	}
}
