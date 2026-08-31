<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use \Elementor\Controls_Manager as Controls_Manager;
use \Elementor\Frontend;
use \Elementor\Group_Control_Border as Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow as Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography as Group_Control_Typography;
use \Elementor\Utils as Utils;
use \Elementor\Widget_Base as Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Carousel
 *
 * Elementor widget for carousel
 *
 * @since 3.6.0
 */
class PostsCarousel extends Widget_Base {


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
		wp_register_style( 'wbcom-posts-revolutions', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wbcom-posts-revolutions.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_style( 'owlcarousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/owl.carousel.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'owltheme', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/owl.theme.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wbcom-appear', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/wbcom-appear.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wbcom-animate', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/wbcom-animate.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'owlcarousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/owl.carousel.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );

	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wbcom-appear', 'wbcom-animate', 'owlcarousel' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wbcom-posts-revolutions', 'wbcom-animations', 'owlcarousel', 'owltheme' );
	}
	/**
	 * Retrieve the widget name.
	 *
	 * @since 3.6.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'wbcom-posts-carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 3.6.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Posts Carousel', 'wbcom-essential' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 3.6.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-media-carousel';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 3.6.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'wbcom-elements' );
	}

	/**
	 * Get keywords.
	 */
	public function get_keywords() {
		return array( 'posts', 'carousel', 'slider', 'blog', 'articles' );
	}

	/**
	 * Get post type categories.
	 */
	private function grid_get_all_post_type_categories( $post_type ) {
		$options = array();

		if ( $post_type == 'post' ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $post_type;
		}

		if ( ! empty( $taxonomy ) ) {
			// Get categories for post type.
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term ) ) {
						if ( isset( $term->slug ) && isset( $term->name ) ) {
							$options[ $term->slug ] = $term->name;
						}
					}
				}
			}
		}

		return $options;
	}


	/**
	 * Get post type categories.
	 */
	private function grid_get_all_custom_post_types() {
		$options = array();

		$args       = array( '_builtin' => false );
		$post_types = get_post_types( $args, 'objects' );

		foreach ( $post_types as $post_type ) {
			if ( isset( $post_type ) ) {
					$options[ $post_type->name ] = $post_type->label;
			}
		}

		return $options;
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
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'wbcom_carousel_type',
			array(
				'label'   => esc_html__( 'Post Display Type', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'type1',
				'options' => array(
					'type1' => 'Type 1',
					'type2' => 'Type 2',
				),
			)
		);

		$this->add_control(
			'wbcom_carousel_excerpt',
			array(
				'label'   => esc_html__( 'Show Excerpt', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'true',
				'options' => array(
					'true'  => esc_html__( 'Show', 'wbcom-essential' ),
					'false' => esc_html__( 'Hidden', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'wbcom_carousel_excerpt_number',
			array(
				'label'     => esc_html__( 'Number Experpt', 'wbcom-essential' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '150',
				'condition' => array(
					'wbcom_carousel_excerpt' => 'true',
				),
			)
		);

		$this->add_control(
			'wbcom_carousel_date_format',
			array(
				'label'   => esc_html__( 'Date Format', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'F j, Y',
				'options' => array(
					'F j, Y g:i a'     => esc_html__( 'November 6, 2010 12:50 am', 'wbcom-essential' ),
					'F j, Y'           => esc_html__( 'November 6, 2010', 'wbcom-essential' ),
					'F, Y'             => esc_html__( 'November, 2010', 'wbcom-essential' ),
					'g:i a'            => esc_html__( '12:50 am', 'wbcom-essential' ),
					'g:i:s a'          => esc_html__( '12:50:48 am', 'wbcom-essential' ),
					'l, F jS, Y'       => esc_html__( 'Saturday, November 6th, 2010', 'wbcom-essential' ),
					'M j, Y @ G:i'     => esc_html__( 'Nov 6, 2010 @ 0:50', 'wbcom-essential' ),
					'Y/m/d \a\t g:i A' => esc_html__( '2010/11/06 at 12:50 AM', 'wbcom-essential' ),
					'Y/m/d \a\t g:ia'  => esc_html__( '2010/11/06 at 12:50am', 'wbcom-essential' ),
					'Y/m/d g:i:s A'    => esc_html__( '2010/11/06 12:50:48 AM', 'wbcom-essential' ),
					'Y/m/d'            => esc_html__( '2010/11/06', 'wbcom-essential' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_settings',
			array(
				'label' => esc_html__( 'Carousel Settings', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'wbcom_carousel_item',
			array(
				'label'   => esc_html__( 'Number Item Show', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
			)
		);

		$this->add_control(
			'wbcom_carousel_navigation',
			array(
				'label'   => esc_html__( 'Navigation', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'true',
				'options' => array(
					'true'  => esc_html__( 'Show', 'wbcom-essential' ),
					'false' => esc_html__( 'Hidden', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'wbcom_carousel_pagination',
			array(
				'label'   => esc_html__( 'Pagination', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'true',
				'options' => array(
					'true'  => esc_html__( 'Show', 'wbcom-essential' ),
					'false' => esc_html__( 'Hidden', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'wbcom_carousel_autoplay',
			array(
				'label'       => esc_html__( 'Autoplay (ms) - ex 1000 (Insert 0 if you want disable autoplay)', 'wbcom-essential' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '2000',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'QUERY', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'wbcom_query_source',
			array(
				'label'   => esc_html__( 'Source', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'wp_posts',
				'options' => array(
					'wp_posts'             => esc_html__( 'Wordpress Posts', 'wbcom-essential' ),
					'wp_custom_posts_type' => esc_html__( 'Custom Posts Type', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'wbcom_query_sticky_posts',
			array(
				'label'     => esc_html__( 'All Posts/Sticky posts', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'allposts',
				'options'   => array(
					'allposts'        => esc_html__( 'All Posts', 'wbcom-essential' ),
					'onlystickyposts' => esc_html__( 'Only Sticky Posts', 'wbcom-essential' ),
				),
				'condition' => array(
					'wbcom_query_source' => 'wp_posts',
				),
			)
		);

		$this->add_control(
			'wbcom_query_posts_type',
			array(
				'label'     => esc_html__( 'Select Post Type Source', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->grid_get_all_custom_post_types(),
				'condition' => array(
					'wbcom_query_source' => 'wp_custom_posts_type',
				),
			)
		);

		$this->add_control(
			'wbcom_query_categories',
			array(
				'label'     => esc_html__( 'Categories', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT2,
				'multiple'  => true,
				'options'   => $this->grid_get_all_post_type_categories( 'post' ),
				'condition' => array(
					'wbcom_query_source' => 'wp_posts',
				),
			)
		);

		$this->add_control(
			'wbcom_query_order',
			array(
				'label'   => esc_html__( 'Order', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => 'DESC',
					'ASC'  => 'ASC',
				),
			)
		);

		$this->add_control(
			'wbcom_query_orderby',
			array(
				'label'   => esc_html__( 'Order By', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'          => 'Date',
					'ID'            => 'ID',
					'author'        => 'Author',
					'title'         => 'Title',
					'name'          => 'Name',
					'modified'      => 'Modified',
					'parent'        => 'Parent',
					'rand'          => 'Rand',
					'comment_count' => 'Comments Count',
					'none'          => 'None',
				),
			)
		);

		$this->add_control(
			'wbcom_query_number',
			array(
				'label'   => esc_html__( 'Number Posts', 'wbcom-essential' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '5',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_animation',
			array(
				'label' => esc_html__( 'Animations', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'wbcom_animate',
			array(
				'label'   => esc_html__( 'Animate', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'off',
				'options' => array(
					'off' => 'Off',
					'on'  => 'On',
				),
			)
		);

		$this->add_control(
			'wbcom_animate_effect',
			array(
				'label'     => esc_html__( 'Animate Effects', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade-in',
				'options'   => array(
					'fade-in'           => 'Fade In',
					'fade-in-up'        => 'fade in up',
					'fade-in-down'      => 'fade in down',
					'fade-in-left'      => 'fade in Left',
					'fade-in-right'     => 'fade in Right',
					'fade-out'          => 'Fade In',
					'fade-out-up'       => 'Fade Out up',
					'fade-out-down'     => 'Fade Out down',
					'fade-out-left'     => 'Fade Out Left',
					'fade-out-right'    => 'Fade Out Right',
					'bounce-in'         => 'Bounce In',
					'bounce-in-up'      => 'Bounce in up',
					'bounce-in-down'    => 'Bounce in down',
					'bounce-in-left'    => 'Bounce in Left',
					'bounce-in-right'   => 'Bounce in Right',
					'bounce-out'        => 'Bounce In',
					'bounce-out-up'     => 'Bounce Out up',
					'bounce-out-down'   => 'Bounce Out down',
					'bounce-out-left'   => 'Bounce Out Left',
					'bounce-out-right'  => 'Bounce Out Right',
					'zoom-in'           => 'Zoom In',
					'zoom-in-up'        => 'Zoom in up',
					'zoom-in-down'      => 'Zoom in down',
					'zoom-in-left'      => 'Zoom in Left',
					'zoom-in-right'     => 'Zoom in Right',
					'zoom-out'          => 'Zoom In',
					'zoom-out-up'       => 'Zoom Out up',
					'zoom-out-down'     => 'Zoom Out down',
					'zoom-out-left'     => 'Zoom Out Left',
					'zoom-out-right'    => 'Zoom Out Right',
					'flash'             => 'Flash',
					'strobe'            => 'Strobe',
					'shake-x'           => 'Shake X',
					'shake-y'           => 'Shake Y',
					'bounce'            => 'Bounce',
					'tada'              => 'Tada',
					'rubber-band'       => 'Rubber Band',
					'swing'             => 'Swing',
					'spin'              => 'Spin',
					'spin-reverse'      => 'Spin Reverse',
					'slingshot'         => 'Slingshot',
					'slingshot-reverse' => 'Slingshot Reverse',
					'wobble'            => 'Wobble',
					'pulse'             => 'Pulse',
					'pulsate'           => 'Pulsate',
					'heartbeat'         => 'Heartbeat',
					'panic'             => 'Panic',
				),
				'condition' => array(
					'wbcom_animate' => 'on',
				),
			)
		);

		$this->add_control(
			'wbcom_delay',
			array(
				'label'     => esc_html__( 'Animate Delay (ms)', 'wbcom-essential' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '2000',
				'condition' => array(
					'wbcom_animate' => 'on',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'wbcom_custom_style',
			array(
				'label'   => esc_html__( 'Custom Style', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'off',
				'options' => array(
					'off' => 'Off',
					'on'  => 'On',
				),
			)
		);

		$this->add_control(
			'wbcom_main_color',
			array(
				'label'     => esc_html__( 'Main Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1d76da',
				'condition' => array(
					'wbcom_custom_style' => 'on',
				),
			)
		);

		$this->add_control(
			'wbcom_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1d76da',
				'condition' => array(
					'wbcom_custom_style' => 'on',
				),
			)
		);

		$this->add_control(
			'wbcom_heading_color',
			array(
				'label'     => esc_html__( 'Heading Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#000',
				'condition' => array(
					'wbcom_custom_style' => 'on',
				),
			)
		);

		$this->add_control(
			'wbcom_content_color',
			array(
				'label'     => esc_html__( 'Content Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#808291',
				'condition' => array(
					'wbcom_custom_style' => 'on',
				),
			)
		);

		$this->add_control(
			'wbcom_background_hover',
			array(
				'label'     => esc_html__( 'Backgound Hover Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFA94B',
				'condition' => array(
					'wbcom_custom_style' => 'on',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_spacing_style',
			[
				'label' => __( 'Spacing', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'      => __( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wbcom-essential-posts-revolution-elementor' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_margin',
			[
				'label'      => __( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wbcom-essential-posts-revolution-elementor' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
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
		static $instance = 0;
		$instance++;
		$settings = $this->get_settings_for_display();

		$wbcom_carousel_type           = esc_html( $settings['wbcom_carousel_type'] );
		$wbcom_carousel_excerpt        = esc_html( $settings['wbcom_carousel_excerpt'] );
		$wbcom_carousel_excerpt_number = esc_html( $settings['wbcom_carousel_excerpt_number'] );
		$wbcom_carousel_date_format    = esc_html( $settings['wbcom_carousel_date_format'] );

		$wbcom_carousel_item         = esc_html( $settings['wbcom_carousel_item'] );
		$wbcom_carousel_navigation   = esc_html( $settings['wbcom_carousel_navigation'] );
		$wbcom_carousel_pagination   = esc_html( $settings['wbcom_carousel_pagination'] );
		$wbcom_carousel_autoplay     = esc_html( $settings['wbcom_carousel_autoplay'] );
		$wbcom_carousel_item_padding = '';

		$wbcom_query_source       = esc_html( $settings['wbcom_query_source'] );
		$wbcom_query_sticky_posts = esc_html( $settings['wbcom_query_sticky_posts'] );
		$wbcom_query_posts_type   = esc_html( $settings['wbcom_query_posts_type'] );
		$wbcom_query_categories   = '';

		if ( ! empty( $settings['wbcom_query_categories'] ) ) {
			$num_cat = count( $settings['wbcom_query_categories'] );
			$i       = 1;
			foreach ( $settings['wbcom_query_categories'] as $element ) {
				$wbcom_query_categories .= $element;
				if ( $i != $num_cat ) {
					$wbcom_query_categories .= ',';
				}
				$i++;
			}
		}
		$wbcom_query_order           = esc_html( $settings['wbcom_query_order'] );
		$wbcom_query_orderby         = esc_html( $settings['wbcom_query_orderby'] );
		$wbcom_query_pagination      = '';
		$wbcom_query_pagination_type = '';
		$wbcom_query_number          = esc_html( $settings['wbcom_query_number'] );
		$wbcom_query_posts_for_page  = '';

		$wbcom_custom_style     = esc_html( $settings['wbcom_custom_style'] );
		$wbcom_main_color       = esc_html( $settings['wbcom_main_color'] );
		$wbcom_hover_color      = esc_html( $settings['wbcom_hover_color'] );
		$wbcom_heading_color    = esc_html( $settings['wbcom_heading_color'] );
		$wbcom_content_color    = esc_html( $settings['wbcom_content_color'] );
		$wbcom_background_hover = esc_html( $settings['wbcom_background_hover'] );

		$wbcom_animate        = esc_html( $settings['wbcom_animate'] );
		$wbcom_animate_effect = esc_html( $settings['wbcom_animate_effect'] );
		$wbcom_delay          = esc_html( $settings['wbcom_delay'] );

		/* CHECK VALUE EMPTY */
		if ( $wbcom_carousel_autoplay == '' || $wbcom_carousel_autoplay == '0' ) {
			$wbcom_carousel_autoplay = 'false'; }
		if ( $wbcom_carousel_excerpt_number == '' ) {
			$wbcom_carousel_excerpt_number = '50'; }

		$return = '<style type="text/css">';
		if ( $wbcom_custom_style == 'on' ) {
			if ( $wbcom_carousel_type == 'type1' ) {
				$return .= '#wb-carousel-' . $instance . ' .owl-item .item .wb-info-left {
							background:' . $wbcom_background_hover . '!important;
						}';
				$return .= '.wbcom-essential-posts-revolution-elementor .wb-title a {
							color:' . $wbcom_heading_color . '!important;
						}';
				$return .= '.wbcom-essential-posts-revolution-elementor .wb-content, .wbcom-essential-posts-revolution-elementor .wb-date, .wbcom-essential-posts-revolution-elementor .wb-author {
					color:' . $wbcom_content_color . '!important;
				}';
				$return .= '.wbcom-essential-posts-revolution-elementor.posts_carousel_type1.wb-selector-' . $instance . ' .wb-category a {
													color:' . $wbcom_main_color . '!important;
									}
									.wbcom-essential-posts-revolution-elementor.posts_carousel_type1.wb-selector-' . $instance . ' .wb-category a:hover {
										color:' . $wbcom_hover_color . '!important;
									}
									.wbcom-essential-posts-revolution-elementor.posts_carousel_type1.wb-selector-' . $instance . ' .wb-title a:hover {
										color:' . $wbcom_hover_color . '!important;
									}';
				$return .= '.owl-theme .owl-controls .owl-page span {
    								border-color:' . $wbcom_main_color . '!important;
									}
									.owl-theme .owl-controls .owl-page.active span, .owl-theme .owl-controls.clickable .owl-page:hover span {
    								background: ' . $wbcom_main_color . '!important;
									}
									.owl-theme .owl-controls .owl-buttons div {
										border-color:' . $wbcom_main_color . '!important;
										color:' . $wbcom_main_color . '!important;
									}
									.owl-theme .owl-controls.clickable .owl-buttons div:hover{
										background: ' . $wbcom_hover_color . '!important;
										color:#ffffff !important;
										border-color:' . $wbcom_hover_color . '!important;
									}
									.owl-theme .owl-controls.clickable .owl-page:hover span{
										background: ' . $wbcom_hover_color . '!important;
										color:#ffffff !important;
										border-color:' . $wbcom_hover_color . '!important;
									}';
			}

			if ( $wbcom_carousel_type == 'type2' ) {
				$return .= '#wb-carousel-' . $instance . ' .wbcom-essential-posts-revolution-elementor-thumbs-container:hover .wb-info-left {
							background:' . $wbcom_background_hover . '!important;
				}';
				$return .= '.wbcom-essential-posts-revolution-elementor .wb-title a {
							color:' . $wbcom_heading_color . '!important;
				}';
				$return .= '.wbcom-essential-posts-revolution-elementor .wb-content, .wbcom-essential-posts-revolution-elementor .wb-date, .wbcom-essential-posts-revolution-elementor .wb-author {
							color:' . $wbcom_content_color . '!important;
				}';
				$return .= '.wbcom-essential-posts-revolution-elementor.posts_carousel_type2.wb-selector-' . $instance . ' .wb-category a {
														color:' . $wbcom_main_color . '!important;
										}
										.wbcom-essential-posts-revolution-elementor.posts_carousel_type2.wb-selector-' . $instance . ' .wb-category a:hover {
											color:' . $wbcom_hover_color . '!important;
										}
										.wbcom-essential-posts-revolution-elementor.posts_carousel_type2.wb-selector-' . $instance . ' .wb-title a:hover {
											color:' . $wbcom_hover_color . '!important;
										}';
				$return .= '.owl-theme .owl-controls .owl-page span {
									border-color:' . $wbcom_main_color . '!important;
										}
										.owl-theme .owl-controls .owl-page.active span, .owl-theme .owl-controls.clickable .owl-page:hover span {
									background: ' . $wbcom_main_color . '!important;
										}
										.owl-theme .owl-controls .owl-buttons div {
											border-color:' . $wbcom_main_color . '!important;
											color:' . $wbcom_main_color . '!important;
										}
										.owl-theme .owl-controls.clickable .owl-buttons div:hover{
											background: ' . $wbcom_hover_color . '!important;
											color:#ffffff !important;
											border-color:' . $wbcom_hover_color . '!important;
										}
										.owl-theme .owl-controls.clickable .owl-page:hover span{
											background: ' . $wbcom_hover_color . '!important;
											color:#ffffff !important;
											border-color:' . $wbcom_hover_color . '!important;
										}';
			}
		}
		$return .= '</style>';

		if ( $wbcom_carousel_type == 'type3' ) {

			$return .= '<script type="text/javascript">
						  jQuery(document).ready(function($){
								$("#wb-carousel-' . $instance . '").owlCarousel({
									items : ' . $wbcom_carousel_item . ',
									navigation: ' . $wbcom_carousel_navigation . ',
									pagination: ' . $wbcom_carousel_pagination . ',
									autoPlay:	' . $wbcom_carousel_autoplay . ',
									navigationText : ["<i class=\'fa fa-arrow-left\'></i>","<i class=\'fa fa-arrow-right\'></i>"],
								});
							});
						</script>';

		} else {

			$return .= '<script type="text/javascript">
						  jQuery(document).ready(function($){
								$("#wb-carousel-' . $instance . '").owlCarousel({
									items : ' . $wbcom_carousel_item . ',
									navigation: ' . $wbcom_carousel_navigation . ',
									pagination: ' . $wbcom_carousel_pagination . ',
									autoPlay:	' . $wbcom_carousel_autoplay . '
								});
							});
						</script>';

		}

		// LOOP QUERY
		$query = wbcom_essential_posts_revolution_elementor_query(
			$wbcom_query_source,
			$wbcom_query_sticky_posts,
			$wbcom_query_posts_type,
			$wbcom_query_categories,
			$wbcom_query_order,
			$wbcom_query_orderby,
			'no',
			$wbcom_query_pagination_type,
			$wbcom_query_number,
			$wbcom_query_posts_for_page
		);

			$return .= '<div class="wbclear"></div>';

		if ( $wbcom_animate == 'on' ) { // ANIMATION ON
			$return .= '<div class="animate-in" data-anim-type="' . esc_attr($wbcom_animate_effect) . '" data-anim-delay="' . esc_attr($wbcom_delay) . '">';
		}

		/*
		 * TYPE 1
		 */

		if ( $wbcom_carousel_type == 'type1' ) {

			$return .= '<div id="wb-carousel-' . $instance . '" class="owl-carousel' . $instance . ' wbcom-essential-posts-revolution-elementor posts_carousel_type1 wb-selector-' . $instance . '">'; // OPEN MAIN DIV

			$count = 0;
			$loop  = new \WP_Query( $query );
			if ( $loop ) {
				while ( $loop->have_posts() ) :
					$loop->the_post();
					$link       = get_permalink();
					$categories = get_the_category();

					$return .= '<div class="item">';

							$return .= '<div class="wbcom-essential-posts-revolution-elementor-thumbs-container">' . wbcom_essential_posts_revolution_elementor_thumbs();
							$return .= '</div>';

							$return         .= '<div class="wb-info-left">';
									$return .= '<span class="wb-category">' . wbcom_essential_posts_revolution_elementor_category( $wbcom_query_source, $wbcom_query_posts_type ) . '</span>';
									$return .= '<span class="wb-title"><a href="' . esc_url($link) . '">' . esc_html(get_the_title()) . '</a></span>';
					if ( $wbcom_carousel_excerpt == 'true' ) {
						$return .= '<span class="wb-content">' . wbcom_essential_posts_revolution_elementor_excerpt( $wbcom_carousel_excerpt_number ) . '</span>';
					}
									$return .= '<span class="wb-author">' . esc_html(get_the_author()) . '</span>';
									$return .= '<span class="wb-date">' . esc_html(get_the_date( $wbcom_carousel_date_format )) . '</span>';
							$return         .= '</div>';

					$return .= '</div>';

					$count++;
			endwhile;
				wp_reset_postdata();
			}

			$return .= '</div>'; // CLOSE MAIN DIV

			/*
			 *TYPE 2
			 */

		} elseif ( $wbcom_carousel_type == 'type2' ) {

			$return .= '<div id="wb-carousel-' . $instance . '" class="owl-carousel' . $instance . ' wbcom-essential-posts-revolution-elementor posts_carousel_type2 wb-selector-' . $instance . '">'; // OPEN MAIN DIV

			$count = 0;
			$loop  = new \WP_Query( $query );
			if ( $loop ) {
				while ( $loop->have_posts() ) :
					$loop->the_post();
					$link       = get_permalink();
					$categories = get_the_category();

					$return .= '<div class="item">';

						$return .= '<div class="wbcom-essential-posts-revolution-elementor-thumbs-container">' . wbcom_essential_posts_revolution_elementor_thumbs();

									$return         .= '<div class="wb-info-left">';
											$return .= '<span class="wb-category">' . wbcom_essential_posts_revolution_elementor_category( $wbcom_query_source, $wbcom_query_posts_type ) . '</span>';
											$return .= '<span class="wb-title"><a href="' . esc_url($link) . '">' . esc_html(get_the_title()) . '</a></span>';
					if ( $wbcom_carousel_excerpt == 'true' ) {
						$return .= '<span class="wb-content">' . wbcom_essential_posts_revolution_elementor_excerpt( $wbcom_carousel_excerpt_number ) . '</span>';
					}
											$return .= '<span class="wb-author">' . esc_html( get_the_author() ) . '</span>';
											$return .= '<span class="wb-date">' . esc_html( get_the_date( $wbcom_carousel_date_format ) ) . '</span>';
									$return         .= '</div>';

						$return .= '</div>';// img

					$return .= '</div>';// item

					$count++;
			endwhile;
				wp_reset_postdata();
			}

			$return .= '</div>'; // CLOSE MAIN DIV

		}
		if ( $wbcom_animate == 'on' ) {

			$return .= '</div>';

		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $return contains pre-escaped HTML from WordPress functions
		echo $return;
	}

}
