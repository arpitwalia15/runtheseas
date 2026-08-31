<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor PostCarousel
 *
 * Elementor widget for PostCarousel
 *
 * @since 3.6.0
 */
class PostCarousel extends \Elementor\Widget_Base {

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
		wp_register_style( 'wb-post-carousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/post-carousel.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/swiper.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wb-post-carousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/post-carousel.js', array( 'jquery', 'swiper' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-post-carousel';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Post Carousel', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-posts-carousel';
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
		return array( 'post', 'carousel', 'slider', 'blog', 'content' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-post-carousel' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-post-carousel' );
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
			'section_posts',
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
			'section_posts_hr',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'display_only_thumbnail',
			array(
				'label'        => esc_html__( 'Display only posts with thumbnail', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'display_thumbnail',
			array(
				'label'        => esc_html__( 'Display post thumbnail', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'display_category',
			array(
				'label'        => esc_html__( 'Display categories', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
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
			'display_author_name',
			array(
				'label'        => esc_html__( 'Display author name', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'display_author_avatar',
			array(
				'label'        => esc_html__( 'Display author avatar', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
				'condition'    => array( 'display_author_name' => 'yes' ),
			)
		);

		$this->add_control(
			'display_author_url',
			array(
				'label'        => esc_html__( 'Enable author url', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
				'condition'    => array( 'display_author_name' => 'yes' ),
			)
		);

		$this->add_control(
			'add_classes',
			array(
				'label'        => esc_html__( 'Add default classes', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_masonry',
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
			'img_size',
			array(
				'label'   => esc_html__( 'Thumbnail Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'large',
				'options' => wba_get_image_sizes(),
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
				'description'  => esc_html__( 'Infinite should be on.', 'wbcom-essential' ),
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
			'adaptive_height',
			array(
				'label'        => esc_html__( 'Adaptive Height', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'true',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_responsive',
			array(
				'label' => esc_html__( 'Responsive', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'responsive_arrows_title',
			array(
				'label'     => esc_html__( 'Navigation Arrows', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
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

		$this->add_control(
			'responsive_dots_title',
			array(
				'label'     => esc_html__( 'Navigation Dots', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
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
			'responsive_cat_title',
			array(
				'label'     => esc_html__( 'Categories', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cats_desktop',
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
			'cats_tablet',
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
			'cats_mobile',
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
			'responsive_excerpt_title',
			array(
				'label'     => esc_html__( 'Excerpt', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'excerpt_desktop',
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
			'excerpt_tablet',
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
			'excerpt_mobile',
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
			'responsive_footer_title',
			array(
				'label'     => esc_html__( 'Card Footer', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'footer_desktop',
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
			'footer_tablet',
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
			'footer_mobile',
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
			'section_card',
			array(
				'label' => esc_html__( 'Card', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_layout',
			array(
				'label'   => esc_html__( 'Layout', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => array(
					'vertical'           => esc_html__( 'Vertical', 'wbcom-essential' ),
					'horizontal'         => esc_html__( 'Horizontal', 'wbcom-essential' ),
					'horizontal-reverse' => esc_html__( 'Horizontal Reverse', 'wbcom-essential' ),
					'bg-img'             => esc_html__( 'BG Image (Featured image is required)', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'card_img_width',
			array(
				'label'      => esc_html__( 'Image ratio (%)', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 40,
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'card_layout',
							'value' => 'horizontal',
						),
						array(
							'name'  => 'card_layout',
							'value' => 'horizontal-reverse',
						),
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-horizontal .wbcom-posts-card-img-wrapper' => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .wbcom-posts-horizontal .wbcom-posts-card-body-wrapper' => 'width: calc(100% - {{SIZE}}%)',
					'{{WRAPPER}} .wbcom-posts-horizontal-reverse .wbcom-posts-card-img-wrapper' => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .wbcom-posts-horizontal-reverse .wbcom-posts-card-body-wrapper' => 'width: calc(100% - {{SIZE}}%)',
				),
			)
		);

		$this->add_responsive_control(
			'card_align',
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
					'{{WRAPPER}} .wbcom-posts-card-body-wrapper' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .wbcom-posts-card-footer' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'card_valign',
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
				'condition' => array( 'card_layout' => 'bg-img' ),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-body-wrapper' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'card_text_align',
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
					'{{WRAPPER}} .wbcom-posts-card-body-wrapper' => 'text-align: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'card_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card',
			)
		);

		$this->add_control(
			'card_hr',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card',
			)
		);

		$this->add_control(
			'card_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'overflow_hidden',
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
			'card_spacing',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_img',
			array(
				'label' => esc_html__( 'Card Image', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_img_overflow',
			array(
				'label'        => esc_html__( 'Overflow Hidden', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'overflowhidden',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'card_img_animation',
			array(
				'label' => esc_html__( 'Hover Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->add_responsive_control(
			'thumbnail_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-featured-img img' => 'opacity: {{VALUE}};',
					'{{WRAPPER}} .wbcom-posts-horizontal .wbcom-posts-card-img-wrapper' => 'opacity: {{VALUE}};',
					'{{WRAPPER}} .wbcom-posts-horizontal-reverse .wbcom-posts-card-img-wrapper' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thumbnail_hover_opacity',
			array(
				'label'     => esc_html__( 'Hover Opacity', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.1,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-featured-img:hover img' => 'opacity: {{VALUE}};',
					'{{WRAPPER}} .wbcom-posts-horizontal .wbcom-posts-card-img-wrapper:hover' => 'opacity: {{VALUE}};',
					'{{WRAPPER}} .wbcom-posts-horizontal-reverse .wbcom-posts-card-img-wrapper:hover' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_img_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-featured-img' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_thumbnail_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_img_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card-featured-img img',
			)
		);

		$this->add_responsive_control(
			'card_img_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-featured-img img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_img_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card-featured-img img',
			)
		);

		$this->add_control(
			'card_thumbnail_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'card_img_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-featured-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_img_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-featured-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_body',
			array(
				'label' => esc_html__( 'Card Body', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_card_body_style' );

		$this->start_controls_tab(
			'tab_card_body_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'card_body_bg_color',
				'label'    => esc_html__( 'Background Color', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card-body-wrapper',
			)
		);

		$this->add_control(
			'card_body_inner_bg_color',
			array(
				'label'     => esc_html__( 'Inner Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-body' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_card_body_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'card_body_bg_color_hover',
				'label'    => esc_html__( 'Background Color', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card-body-wrapper:hover',
			)
		);

		$this->add_control(
			'card_body_inner_bg_color_hover',
			array(
				'label'     => esc_html__( 'Inner Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-body-wrapper:hover .wbcom-posts-card-body' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'card_body_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_body_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card-body',
			)
		);

		$this->add_control(
			'card_body_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'card_body_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_body_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-body' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_footer',
			array(
				'label' => esc_html__( 'Card Footer', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_footer_layout',
			array(
				'label'   => esc_html__( 'Layout', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'card-footer-block',
				'options' => array(
					'card-footer-block'  => esc_html__( 'Block', 'wbcom-essential' ),
					'card-footer-inline' => esc_html__( 'Inline', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'card_body_footer_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-footer' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_footer_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card-footer',
			)
		);

		$this->add_responsive_control(
			'card_footer_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_categories',
			array(
				'label'     => esc_html__( 'Categories', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_category' => 'yes' ),
			)
		);

		$this->add_control(
			'card_category_style',
			array(
				'label'   => esc_html__( 'Style', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'list',
				'options' => array(
					'list'  => esc_html__( 'List', 'wbcom-essential' ),
					'badge' => esc_html__( 'Badge', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'card_category_position',
			array(
				'label'   => esc_html__( 'Position', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'wbcom-essential' ),
					'bottom' => esc_html__( 'Bottom', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'card_category_padding',
			array(
				'label'      => esc_html__( 'Container Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-cats' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_category_margin',
			array(
				'label'      => esc_html__( 'Container Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-cats' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_badge_list_hr',
			array(
				'label'     => esc_html__( 'List and Badge', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_category_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-cats'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-posts-card-cats a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_category_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-cats a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_category_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-posts-card-cats,{{WRAPPER}} .wbcom-posts-card-cats a',
			)
		);

		$this->add_control(
			'card_badge_hr',
			array(
				'label'     => esc_html__( 'Badge', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'card_badge_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-masonry-badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_badge_border',
				'label'    => esc_html__( 'Badge Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-masonry-badge',
			)
		);

		$this->add_responsive_control(
			'card_badge_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-masonry-badge' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_badge_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-masonry-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_badge_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-masonry-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_title',
			array(
				'label' => esc_html__( 'Title', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_title_html',
			array(
				'label'   => esc_html__( 'HTML Tag', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h1'   => esc_html__( 'H1', 'wbcom-essential' ),
					'h2'   => esc_html__( 'H2', 'wbcom-essential' ),
					'h3'   => esc_html__( 'H3', 'wbcom-essential' ),
					'h4'   => esc_html__( 'H4', 'wbcom-essential' ),
					'h5'   => esc_html__( 'H5', 'wbcom-essential' ),
					'h6'   => esc_html__( 'H6', 'wbcom-essential' ),
					'div'  => esc_html__( 'div', 'wbcom-essential' ),
					'span' => esc_html__( 'span', 'wbcom-essential' ),
					'p'    => esc_html__( 'p', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'card_title_ellipsis',
			array(
				'label'        => esc_html__( 'Ellipsis', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'wbcom-ellipsis',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'card_title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-title'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-posts-card-title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_title_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-posts-card-title,{{WRAPPER}} .wbcom-posts-card-title a',
			)
		);

		$this->add_control(
			'card_title_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'card_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_excerpt',
			array(
				'label' => esc_html__( 'Excerpt', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_excerpt_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-excerpt p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_excerpt_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-posts-excerpt p',
			)
		);

		$this->add_responsive_control(
			'card_excerpt_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-excerpt p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_excerpt_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-excerpt p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_author',
			array(
				'label'     => esc_html__( 'Author', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_author_name' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'     => esc_html__( 'Avatar Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 5,
				'max'       => 150,
				'step'      => 1,
				'default'   => 40,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-author-img img.avatar' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-posts-card-author-img img' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_radius',
			array(
				'label'      => esc_html__( 'Avatar Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-author-img img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'avatar_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-posts-card-author-img img',
			)
		);

		$this->add_control(
			'section_author_hr',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'card_author_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-author-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_author_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-author-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_author_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-posts-card-author-link',
			)
		);

		$this->add_control(
			'card_author_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'card_author_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-author-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_author_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-author-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_author_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'card_author_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$this->add_responsive_control(
			'card_author_icon_padding',
			array(
				'label'      => esc_html__( 'Icon Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-author-link i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_date',
			array(
				'label'     => esc_html__( 'Date', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'display_date' => 'yes' ),
			)
		);

		$this->add_control(
			'card_date_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-date-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_date_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-date-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_date_typography',
				'label'    => esc_html__( 'Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wbcom-posts-card-date-link',
			)
		);

		$this->add_control(
			'card_date_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'card_date_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-date-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_date_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-date-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'card_date_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'card_date_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			'card_date_icon_size',
			array(
				'label'     => esc_html__( 'Icon Size (px)', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 50,
				'step'      => 1,
				'default'   => 16,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-posts-card-date-link svg' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-posts-card-date-link i' => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'card_date_icon_padding',
			array(
				'label'      => esc_html__( 'Icon Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-posts-card-date-link i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev' => 'font-size: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next' => 'font-size: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next' => 'height: {{VALUE}}px;width: {{VALUE}}px;line-height: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-next' => 'right: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-post-carousel .elementor-swiper-button-prev' => 'left: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-post-carousel .swiper-pagination-bullet' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .wbcom-post-carousel .swiper-pagination-bullet' => 'font-size: {{VALUE}}px;line-height: {{VALUE}}px;width: {{VALUE}}px;height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-post-carousel .swiper-pagination-bullet' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
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
					'{{WRAPPER}} .wbcom-post-carousel .swiper-pagination-bullet' => 'margin-left: {{VALUE}}px !important;margin-right: {{VALUE}}px !important;',
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
					'{{WRAPPER}} .wbcom-post-carousel .swiper-pagination' => 'bottom: {{VALUE}}px;',
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
		$widget_id  = $this->get_id();
		$settings   = $this->get_settings_for_display();
		$postype    = $settings['post_type'];
		$order      = $settings['order'];
		$orderby    = $settings['orderby'];
		$max        = $settings['max'];
		$authors    = $settings['authors'];
		$categories = $settings['taxonomy'];
		$tags       = $settings['tags'];

		$terms = array();
		if ( empty( $authors ) ) {
			$authors = array();
		}

		if ( $settings['display_only_thumbnail'] ) {
			$metakey = '_thumbnail_id';
		} else {
			$metakey = false;
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
				'meta_key'            => $metakey,
				'post__in'            => $include,
				'post__not_in'        => $exclude,
				'author__in'          => $authors,
				'ignore_sticky_posts' => true,
				'tax_query'           => $terms,
			)
		);

        if ($custom_query->have_posts()) {
			$columns_map = array( 'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5 );
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
        <div id="wbcom-post-carousel-<?php echo esc_attr($widget_id); ?>" class="wbcom-post-carousel swiper-container <?php echo esc_attr($settings['overflow_hidden']); ?>" <?php if ( is_rtl() ) { echo 'dir="rtl"'; } ?> data-settings="<?php echo esc_attr( wp_json_encode( $swiper_options ) ); ?>">
			<div class="swiper-wrapper">
			<?php while($custom_query->have_posts()) : $custom_query->the_post(); ?>
			<div class="swiper-slide">
			<div <?php if ($settings['add_classes']) { post_class('wbcom-carousel-item'); } else { echo 'class="wbcom-carousel-item"'; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML attribute string. */ } ?>>
				<div class="wbcom-posts-card wbcom-posts-<?php echo esc_attr($settings['card_layout']); ?>">
				<?php if ((has_post_thumbnail()) && ($settings['display_thumbnail'])) { ?>
				<?php
				$tmeposts_thumb_id = get_post_thumbnail_id();
				$tmeposts_thumb_url_array = wp_get_attachment_image_src($tmeposts_thumb_id, $settings['img_size'], true);
				$tmeposts_thumb_url = $tmeposts_thumb_url_array[0];
				?>
					
				<?php if (($settings['card_layout'] == 'horizontal') || ($settings['card_layout'] == 'horizontal-reverse')) { ?> 
				<div class="wbcom-posts-card-img-wrapper <?php echo esc_attr($settings['card_img_overflow']); ?>" style="background-image:url('<?php echo esc_url($tmeposts_thumb_url); ?>');">  
					<a class="wbcom-posts-card-featured-img elementor-animation-<?php echo esc_attr($settings['card_img_animation']); ?>" href="<?php the_permalink(); ?>"></a>    
				</div>    
				<?php } else { ?> 
				<div class="wbcom-posts-card-img-wrapper <?php echo esc_attr($settings['card_img_overflow']); ?>">  
					<a class="wbcom-posts-card-featured-img elementor-animation-<?php echo esc_attr($settings['card_img_animation']); ?>" href="<?php the_permalink(); ?>">
						<img src="<?php echo esc_url($tmeposts_thumb_url); ?>" alt="<?php the_title_attribute(); ?>" />   
					</a>    
				</div>     
				<?php } ?>    
						
				<?php } ?>
					<div class="wbcom-posts-card-body-wrapper">
					<div class="wbcom-posts-card-body-wrapper-inner">
					<?php if ($settings['card_layout'] == 'bg-img') { ?>
						<a class="wbcom-posts-card-body-url" href="<?php the_permalink(); ?>"></a>
					<?php } ?>    
					<div class="wbcom-posts-card-body">
						<?php if (( has_category() ) && ($settings['display_category']) && ($settings['card_category_position'] == 'top')) { ?> 
						<div class="wbcom-posts-card-cats">
							<?php if ($settings['card_category_style'] == 'badge') { ?>
							<span class="wbcom-masonry-badge"><?php the_category('</span><span class="wbcom-masonry-badge">'); ?></span>
							<?php } else { ?>
							<span><?php the_category(',</span> <span>'); ?></span>
							<?php } ?>
						</div>
						<?php } ?>
						<<?php echo esc_attr($settings['card_title_html']); ?> class="wbcom-posts-card-title <?php echo esc_attr($settings['card_title_ellipsis']); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo esc_attr($settings['card_title_html']); ?>>
						<?php
						if ((get_the_excerpt()) && (!empty($settings['excerpt_length'])) && ($settings['excerpt_length'] != 0)) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wpautop is a WordPress function that returns safe HTML
							echo '<div class="wbcom-posts-excerpt">' . wpautop(wba_excerpt($settings['excerpt_length'])) . '</div>';
						}
						?>
						<?php if (( has_category() ) && ($settings['display_category']) && ($settings['card_category_position'] == 'bottom')) { ?> 
						<div class="wbcom-posts-card-cats">
							<?php if ($settings['card_category_style'] == 'badge') { ?>
							<span class="wbcom-masonry-badge"><?php the_category('</span><span class="wbcom-masonry-badge">'); ?></span>
							<?php } else { ?>
							<span><?php the_category(',</span> <span>'); ?></span>
							<?php } ?>
						</div>
						<?php } ?>    
					</div>
					<div class="wbcom-posts-card-footer">
						<?php if ($settings['display_author_avatar']) { ?>
						<div class="wbcom-posts-card-author-img">
							<?php if ($settings['display_author_url']) { ?>
							<?php $avatar_size = ( ! empty( $settings['avatar_size'] ) ) ? esc_attr( $settings['avatar_size'] ) : ''; ?>
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo get_avatar( get_the_author_meta( 'ID' ), $avatar_size ); ?></a>
							<?php } else { ?>
							<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() is a WordPress core function that returns escaped HTML. ?>
						<?php echo get_avatar( get_the_author_meta( 'ID' ), $settings['avatar_size'] ); ?>
							<?php } ?>
						</div>
						<?php } ?>
						<div class="wbcom-posts-card-date <?php echo esc_attr($settings['card_footer_layout']) ?>">
							<?php if ($settings['display_author_name']) { ?>
							<?php if ($settings['display_author_url']) { ?>
							<a class="wbcom-posts-card-author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
								<?php \Elementor\Icons_Manager::render_icon( $settings['card_author_icon'], [ 'aria-hidden' => 'true' ] ); ?><?php the_author(); ?>
							</a>
							<?php } else { ?>
							<span class="wbcom-posts-card-author-link">
								<?php \Elementor\Icons_Manager::render_icon( $settings['card_author_icon'], [ 'aria-hidden' => 'true' ] ); ?><?php the_author(); ?>
							</span>
							<?php } ?>
							<?php } ?>
							<?php if ($settings['display_date']) { ?>
							<a class="wbcom-posts-card-date-link" href="<?php esc_url(the_permalink()); ?>">
								<?php \Elementor\Icons_Manager::render_icon( $settings['card_date_icon'], [ 'aria-hidden' => 'true' ] ); ?><?php the_time(get_option('date_format')); ?>
							</a>
							<?php } ?>
						</div>
					</div>
					</div>
					</div>
					</div>
				</div>
			</div>
			<?php endwhile; ?>
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
        <div class="wbcom-clear"></div>   
        <?php wp_reset_postdata(); ?>
			<style type="text/css">
				<?php
				$viewport_lg = '';
				if (empty($viewport_lg)) {
					$viewport_lg = 1024;
				}                              
				$viewport_md = '';
				if (empty($viewport_md)) {
					$viewport_md = 767;
				}
				?>
				@media screen and (min-width: <?php echo esc_attr( $viewport_lg + 1 ) . 'px'; ?>) {
					<?php if ($settings['nav_arrows_desktop']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-prev,
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-next {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-prev,
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-next {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['nav_dots_desktop']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .swiper-pagination {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .swiper-pagination {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['cats_desktop']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-cats {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-cats {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['excerpt_desktop']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-excerpt {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-excerpt {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['footer_desktop']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-footer {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-footer {
						display: flex !important;
					}
					<?php } ?>
				}
				@media only screen and (max-width: <?php echo esc_attr($viewport_lg) . 'px'; ?>) {
					<?php if ($settings['nav_arrows_tablet']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-prev,
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-next {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-prev,
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-next {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['nav_dots_tablet']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .swiper-pagination {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .swiper-pagination {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['cats_tablet']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-cats {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-cats {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['excerpt_tablet']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-excerpt {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-excerpt {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['footer_tablet']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-footer {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-footer {
						display: flex !important;
					}
					<?php } ?>
				}
				@media screen and (max-width: <?php echo esc_attr($viewport_md) . 'px'; ?>) {
					<?php if ($settings['nav_arrows_mobile']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-prev,
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-next {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-prev,
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .elementor-swiper-button-next {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['nav_dots_mobile']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .swiper-pagination {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .swiper-pagination {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['cats_mobile']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-cats {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-cats {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['excerpt_mobile']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-excerpt {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_js($widget_id); ?> .wbcom-posts-excerpt {
						display: block !important;
					}
					<?php } ?>
					<?php if ($settings['footer_mobile']) { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-footer {
						display: none !important;
					}
					<?php } else { ?>
					#wbcom-post-carousel-<?php echo esc_attr($widget_id); ?> .wbcom-posts-card-footer {
						display: flex !important;
					}
					<?php } ?>
				}
			</style>
		<?php } else { ?>
			<div class="wbcom-danger"><?php esc_html_e( 'Nothing was found!', 'wbcom-essential' ); ?></div>         
			<?php
		}
	}
}
