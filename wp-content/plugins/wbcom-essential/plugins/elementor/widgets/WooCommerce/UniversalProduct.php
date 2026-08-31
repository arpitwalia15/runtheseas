<?php
/**
 * Elementor widget universal product.
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
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

/**
 * Universal Product.
 *
 * @since      3.7.1
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/woocommerce
 */
class UniversalProduct extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wbflexboxgrid', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wbflexboxgrid.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wbe-swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/swiper-base.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wbcom-widgets', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wbcom-widgets.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/swiper.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
		wp_register_script( 'wbcom-widgets-scripts', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/wbcom-widgets-active.js', array( 'jquery', 'swiper' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-universal-product';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Universal Product', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-cart-light';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array(
			'wbflexboxgrid',
			'wbe-swiper',
			'elementor-icons-shared-0-css',
			'elementor-icons-fa-brands',
			'elementor-icons-fa-regular',
			'elementor-icons-fa-solid',
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
		return array( 'slider', 'product', 'universal', 'universal product', 'universal layout' );
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {
		// Product Content.
		$this->start_controls_section(
			'wbcom-products-layout-setting',
			array(
				'label' => esc_html__( 'Layout Settings', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'product_layout_style',
				array(
					'label'   => __( 'Layout', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'default',
					'options' => array(
						'slider'  => __( 'Slider', 'wbcom-essential' ),
						'tab'     => __( 'Tab', 'wbcom-essential' ),
						'default' => __( 'Default', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'same_height_box',
				array(
					'label'        => __( 'Same Height Box ?', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wbcom-essential' ),
					'label_off'    => __( 'No', 'wbcom-essential' ),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'wbcom_product_grid_column',
				array(
					'label'     => esc_html__( 'Columns', 'wbcom-essential' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '3',
					'options'   => array(
						'1' => esc_html__( '1', 'wbcom-essential' ),
						'2' => esc_html__( '2', 'wbcom-essential' ),
						'3' => esc_html__( '3', 'wbcom-essential' ),
						'4' => esc_html__( '4', 'wbcom-essential' ),
						'5' => esc_html__( '5', 'wbcom-essential' ),
						'6' => esc_html__( '6', 'wbcom-essential' ),
					),
					'condition' => array(
						'product_layout_style!' => 'slider',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'wbcom-products',
			array(
				'label' => esc_html__( 'Query Settings', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'wbcom_product_grid_product_filter',
				array(
					'label'   => esc_html__( 'Filter By', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'recent',
					'options' => array(
						'recent'             => esc_html__( 'Recent Products', 'wbcom-essential' ),
						'featured'           => esc_html__( 'Featured Products', 'wbcom-essential' ),
						'best_selling'       => esc_html__( 'Best Selling Products', 'wbcom-essential' ),
						'sale'               => esc_html__( 'Sale Products', 'wbcom-essential' ),
						'top_rated'          => esc_html__( 'Top Rated Products', 'wbcom-essential' ),
						'mixed_order'        => esc_html__( 'Random Products', 'wbcom-essential' ),
						'show_byid'          => esc_html__( 'Show By ID', 'wbcom-essential' ),
						'show_byid_manually' => esc_html__( 'Add ID Manually', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'wbcom_product_id',
				array(
					'label'       => __( 'Select Product', 'wbcom-essential' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => wbcom_post_name( 'product' ),
					'condition'   => array(
						'wbcom_product_grid_product_filter' => 'show_byid',
					),
				)
			);

			$this->add_control(
				'wbcom_product_ids_manually',
				array(
					'label'       => __( 'Product IDs', 'wbcom-essential' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'condition'   => array(
						'wbcom_product_grid_product_filter' => 'show_byid_manually',
					),
				)
			);

			$this->add_control(
				'wbcom_product_grid_products_count',
				array(
					'label'   => __( 'Product Limit', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 3,
					'step'    => 1,
				)
			);

			$this->add_control(
				'wbcom_product_grid_categories',
				array(
					'label'       => esc_html__( 'Product Categories', 'wbcom-essential' ),
					'type'        => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple'    => true,
					'options'     => wbcom_taxonomy_list(),
					'condition'   => array(
						'wbcom_product_grid_product_filter!' => 'show_byid',
					),
				)
			);

			$this->add_control(
				'hidden_outofstock',
				array(
					'label'        => esc_html__( 'Exclude Out Of Stock Item', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);
			$this->add_control(
				'hidden_item',
				array(
					'label'        => esc_html__( 'Exclude Hidden Item', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'wbcom_custom_order',
				array(
					'label'        => esc_html__( 'Custom Order', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_control(
				'orderby',
				array(
					'label'     => esc_html__( 'Order by', 'wbcom-essential' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'none',
					'options'   => array(
						'none'          => esc_html__( 'None', 'wbcom-essential' ),
						'ID'            => esc_html__( 'ID', 'wbcom-essential' ),
						'date'          => esc_html__( 'Date', 'wbcom-essential' ),
						'name'          => esc_html__( 'Name', 'wbcom-essential' ),
						'title'         => esc_html__( 'Title', 'wbcom-essential' ),
						'comment_count' => esc_html__( 'Comment count', 'wbcom-essential' ),
						'rand'          => esc_html__( 'Random', 'wbcom-essential' ),
					),
					'condition' => array(
						'wbcom_custom_order' => 'yes',
					),
				)
			);

			$this->add_control(
				'order',
				array(
					'label'     => esc_html__( 'Order', 'wbcom-essential' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'DESC',
					'options'   => array(
						'DESC' => esc_html__( 'Descending', 'wbcom-essential' ),
						'ASC'  => esc_html__( 'Ascending', 'wbcom-essential' ),
					),
					'condition' => array(
						'wbcom_custom_order' => 'yes',
					),
				)
			);

		$this->end_controls_section();

		// Product Content.
		$this->start_controls_section(
			'wbcom-products-content-setting',
			array(
				'label' => esc_html__( 'Content Settings', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'product_content_style',
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
				'product_title_html_tag',
				array(
					'label'   => __( 'Title HTML Tag', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'options' => wbcom_html_tag_lists(),
					'default' => 'h4',
				)
			);

			$this->add_control(
				'hide_product_title',
				array(
					'label'     => __( 'Hide Title', 'wbcom-essential' ),
					'type'      => Controls_Manager::SWITCHER,
					'selectors' => array(
						'{{WRAPPER}} .wb-product-inner .wb-product-title' => 'display: none !important;',
					),
				)
			);

			$this->add_control(
				'hide_product_price',
				array(
					'label'     => __( 'Hide Price', 'wbcom-essential' ),
					'type'      => Controls_Manager::SWITCHER,
					'selectors' => array(
						'{{WRAPPER}} .wb-product-inner .wb-product-price' => 'display: none !important;',
					),
				)
			);

			$this->add_control(
				'hide_product_category',
				array(
					'label'     => __( 'Hide Category', 'wbcom-essential' ),
					'type'      => Controls_Manager::SWITCHER,
					'selectors' => array(
						'{{WRAPPER}} .wb-product-inner .wb-product-categories' => 'display: none !important;',
					),
				)
			);

			$this->add_control(
				'hide_category_before_border',
				array(
					'label'     => __( 'Hide category before border', 'wbcom-essential' ),
					'type'      => Controls_Manager::SWITCHER,
					'selectors' => array(
						'{{WRAPPER}} .wb-product-inner .wb-product-categories::before' => 'display: none !important;',
						'{{WRAPPER}} .wb-product-inner .wb-product-categories' => 'padding-left: 0 !important;',
					),
				)
			);

			$this->add_control(
				'hide_product_ratting',
				array(
					'label'     => __( 'Hide Rating', 'wbcom-essential' ),
					'type'      => Controls_Manager::SWITCHER,
					'selectors' => array(
						'{{WRAPPER}} .wb-product-inner .wb-product-ratting-wrap' => 'display: none !important;',
					),
				)
			);

		$this->end_controls_section();

		// Product Action Button.
		$this->start_controls_section(
			'wbcom-products-action-button',
			array(
				'label' => esc_html__( 'Action Button Settings', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'show_action_button',
				array(
					'label'        => __( 'Action Button', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'wbcom-essential' ),
					'label_off'    => __( 'Hide', 'wbcom-essential' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'action_button_style',
				array(
					'label'     => __( 'Style', 'wbcom-essential' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '1',
					'options'   => array(
						'1' => __( 'Style One', 'wbcom-essential' ),
						'2' => __( 'Style Two', 'wbcom-essential' ),
						'3' => __( 'Style Three', 'wbcom-essential' ),
					),
					'condition' => array(
						'show_action_button' => 'yes',
					),
				)
			);

			$this->add_control(
				'action_button_show_on',
				array(
					'label'     => __( 'Show on', 'wbcom-essential' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'normal',
					'options'   => array(
						'hover'  => __( 'Hover', 'wbcom-essential' ),
						'normal' => __( 'Normal', 'wbcom-essential' ),
					),
					'condition' => array(
						'show_action_button' => 'yes',
					),
				)
			);

			$this->add_control(
				'action_button_position',
				array(
					'label'     => __( 'Position', 'wbcom-essential' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'          => array(
							'title' => __( 'Left', 'wbcom-essential' ),
							'icon'  => 'eicon-h-align-left',
						),
						'right'         => array(
							'title' => __( 'Right', 'wbcom-essential' ),
							'icon'  => 'eicon-h-align-right',
						),
						'middle'        => array(
							'title' => __( 'Middle', 'wbcom-essential' ),
							'icon'  => 'eicon-v-align-middle',
						),
						'bottom'        => array(
							'title' => __( 'Bottom', 'wbcom-essential' ),
							'icon'  => 'eicon-v-align-bottom',
						),
						'contentbottom' => array(
							'title' => __( 'Content Bottom', 'wbcom-essential' ),
							'icon'  => 'eicon-v-align-bottom',
						),
					),
					'default'   => 'middle',
					'toggle'    => false,
					'condition' => array(
						'show_action_button' => 'yes',
					),
				)
			);

			$this->add_control(
				'addtocart_button_txt',
				array(
					'label' => __( 'Show Add to Cart Button Text', 'wbcom-essential' ),
					'type'  => Controls_Manager::SWITCHER,
				)
			);

		$this->end_controls_section();

		// Product Image Setting.
		$this->start_controls_section(
			'wbcom-products-thumbnails-setting',
			array(
				'label' => esc_html__( 'Image Settings', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'thumbnails_style',
				array(
					'label'   => __( 'Thumbnails Style', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1' => __( 'Single Image', 'wbcom-essential' ),
						'2' => __( 'Image Slider', 'wbcom-essential' ),
						'3' => __( 'Gallery Tab', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'image_navigation_bg_color',
				array(
					'label'     => __( 'Arrows Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#444444',
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-image .wb-product-image-slider .elementor-swiper-button' => 'color: {{VALUE}} !important;',
					),
					'condition' => array(
						'thumbnails_style' => '2',
					),
				)
			);

			$this->add_control(
				'image_dots_normal_bg_color',
				array(
					'label'     => __( 'Dots Background Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#cccccc',
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-image .wb-product-image-slider .swiper-pagination-bullet' => 'background-color: {{VALUE}} !important;',
					),
					'condition' => array(
						'thumbnails_style' => '2',
					),
				)
			);

			$this->add_control(
				'image_dots_hover_bg_color',
				array(
					'label'     => __( 'Dots Active Background Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'condition' => array(
						'thumbnails_style' => '2',
					),
					'default'   => '#666666',
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-image .wb-product-image-slider .swiper-pagination-bullet-active' => 'background-color: {{VALUE}} !important;',
					),
				)
			);

			$this->add_control(
				'image_tab_menu_border_color',
				array(
					'label'     => __( 'Border Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#737373',
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-image .wb-product-cus-tab-links li a' => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'thumbnails_style' => '3',
					),
				)
			);

			$this->add_control(
				'image_tab_menu_active_border_color',
				array(
					'label'     => __( 'Active Border Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#1d76da',
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-image .wb-product-cus-tab-links li a.htactive' => 'border-color: {{VALUE}} !important;',
					),
					'condition' => array(
						'thumbnails_style' => '3',
					),
				)
			);

		$this->end_controls_section();

		// Product slider setting.
		$this->start_controls_section(
			'wbcom-products-slider',
			array(
				'label'     => esc_html__( 'Slider Option', 'wbcom-essential' ),
				'condition' => array(
					'product_layout_style' => 'slider',
				),
			)
		);

			$this->add_control(
				'slitems',
				array(
					'label'   => esc_html__( 'Slider Items', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 10,
					'step'    => 1,
					'default' => 3,
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
					'max'     => 10,
					'step'    => 1,
					'default' => 3,
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
					'max'     => 8,
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
					'max'     => 8,
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
					'max'     => 4,
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
					'max'     => 4,
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

		$this->end_controls_section(); // Slider Option end.

		// Style Default tab section.
		$this->start_controls_section(
			'universal_product_style_section',
			array(
				'label' => __( 'Style', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'product_inner_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .woocommerce div.product.mb-30' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'product_inner_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .woocommerce div.product.mb-30' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'product_inner_border_color',
				array(
					'label'     => __( 'Border Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#f1f1f1',
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner' => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'product_inner_box_shadow',
					'label'    => __( 'Hover Box Shadow', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner:hover',
				)
			);

			$this->add_control(
				'product_content_area_heading',
				array(
					'label'     => __( 'Content area', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'product_content_area_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'product_content_area_bg_color',
				array(
					'label'     => __( 'Background Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'content_area_bg', 'wbcom_style_tabs', '#ffffff' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'product_content_area_border',
					'label'    => __( 'Border', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content',
				)
			);

			$this->add_control(
				'product_badge_heading',
				array(
					'label'     => __( 'Product Badge', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'product_badge_color',
				array(
					'label'     => __( 'Badge Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'badge_color', 'wbcom_style_tabs', '#444444' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-label' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'product_outofstock_badge_color',
				array(
					'label'     => __( 'Out of Stock Badge Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-label.wb-stockout' => 'color: {{VALUE}} !important;',
					),
				)
			);

			$this->add_control(
				'product_badge_bg_color',
				array(
					'label'     => __( 'Badge Background Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-label' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'product_badge_typography',
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-label',
				)
			);

			$this->add_responsive_control(
				'product_badge_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-image-wrap .wb-product-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			// Product Category.
			$this->add_control(
				'product_category_heading',
				array(
					'label'     => __( 'Product Category', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'product_category_typography',
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-categories a',
				)
			);

			$this->add_control(
				'product_category_color',
				array(
					'label'     => __( 'Category Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'category_color', 'wbcom_style_tabs', '#444444' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-categories a' => 'color: {{VALUE}};',
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-categories::before' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'product_category_hover_color',
				array(
					'label'     => __( 'Category Hover Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'category_hover_color', 'wbcom_style_tabs', '#1d76da' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-categories a:hover' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'product_category_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-categories' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			// Product Title.
			$this->add_control(
				'product_title_heading',
				array(
					'label'     => __( 'Product Title', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'product_title_typography',
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-title a',
				)
			);

			$this->add_control(
				'product_title_color',
				array(
					'label'     => __( 'Title Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'title_color', 'wbcom_style_tabs', '#444444' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-title a' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'product_title_hover_color',
				array(
					'label'     => __( 'Title Hover Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'title_hover_color', 'wbcom_style_tabs', '#1d76da' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-title a:hover' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'product_title_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			// Product Price.
			$this->add_control(
				'product_price_heading',
				array(
					'label'     => __( 'Product Price', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'product_sale_price_color',
				array(
					'label'     => __( 'Sale Price Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'sale_price_color', 'wbcom_style_tabs', '#444444' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-price span' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'product_sale_price_typography',
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-price span',
				)
			);

			$this->add_control(
				'product_regular_price_color',
				array(
					'label'     => __( 'Regular Price Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'separator' => 'before',
					'default'   => wbcom_get_option( 'regular_price_color', 'wbcom_style_tabs', '#444444' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-price span del span,{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-price span del' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'product_regular_price_typography',
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-price span del span',
				)
			);

			$this->add_responsive_control(
				'product_price_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			// Product Rating.
			$this->add_control(
				'product_rating_heading',
				array(
					'label'     => __( 'Product Rating', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'product_rating_color',
				array(
					'label'     => __( 'Empty Rating Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'empty_rating_color', 'wbcom_style_tabs', '#aaaaaa' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-ratting-wrap .wb-product-ratting .wb-product-user-ratting i.empty' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'product_rating_give_color',
				array(
					'label'     => __( 'Rating Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => wbcom_get_option( 'rating_color', 'wbcom_style_tabs', '#1d76da' ),
					'selectors' => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-ratting-wrap .wb-product-ratting .wb-product-user-ratting i' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'product_rating_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-content .wb-product-content-inner .wb-product-ratting-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section(); // Style Default End.

		// Style Action Button tab section.
		$this->start_controls_section(
			'universal_product_action_button_style_section',
			array(
				'label' => __( 'Action Button Style', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				array(
					'name'     => 'product_action_button_background_color',
					'label'    => __( 'Background', 'wbcom-essential' ),
					'types'    => array( 'classic', 'gradient' ),
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'product_action_button_box_shadow',
					'label'    => __( 'Box Shadow', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul',
				)
			);

			$this->add_control(
				'product_tooltip_heading',
				array(
					'label'     => __( 'Tooltip', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

				$this->add_control(
					'product_tooltip_color',
					array(
						'label'     => __( 'Tooltip Color', 'wbcom-essential' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => wbcom_get_option( 'tooltip_color', 'wbcom_style_tabs', '#ffffff' ),
						'selectors' => array(
							'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li a .wb-product-action-tooltip,{{WRAPPER}} span.wbcom-tip' => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					array(
						'name'     => 'product_action_button_tooltip_background_color',
						'label'    => __( 'Background', 'wbcom-essential' ),
						'types'    => array( 'classic', 'gradient' ),
						'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li a .wb-product-action-tooltip,{{WRAPPER}} span.wbcom-tip',
					)
				);

			$this->start_controls_tabs( 'product_action_button_style_tabs' );

				// Normal.
				$this->start_controls_tab(
					'product_action_button_style_normal_tab',
					array(
						'label' => __( 'Normal', 'wbcom-essential' ),
					)
				);

					$this->add_control(
						'product_action_button_normal_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => wbcom_get_option( 'btn_color', 'wbcom_style_tabs', '#000000' ),
							'selectors' => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li a' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_responsive_control(
						'product_action_button_font_size',
						array(
							'label'      => __( 'Font Size', 'wbcom-essential' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', '%' ),
							'range'      => array(
								'px' => array(
									'min'  => 0,
									'max'  => 200,
									'step' => 1,
								),
								'%'  => array(
									'min' => 0,
									'max' => 100,
								),
							),
							'default'    => array(
								'unit' => 'px',
								'size' => 20,
							),
							'selectors'  => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li a i' => 'font-size: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .wb-product-action ul li.wbcom-cart a::before' => 'font-size: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$this->add_responsive_control(
						'product_action_button_line_height',
						array(
							'label'      => __( 'Line Height', 'wbcom-essential' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', '%' ),
							'range'      => array(
								'px' => array(
									'min'  => 0,
									'max'  => 200,
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
							'selectors'  => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li a i' => 'line-height: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .wb-product-action ul li.wbcom-cart a,{{WRAPPER}} .wb-product-action ul li.wbcom-cart a::before' => 'line-height: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'     => 'product_action_button_normal_background_color',
							'label'    => __( 'Background', 'wbcom-essential' ),
							'types'    => array( 'classic', 'gradient' ),
							'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li',
						)
					);

					$this->add_responsive_control(
						'product_action_button_normal_padding',
						array(
							'label'      => __( 'Padding', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_responsive_control(
						'product_action_button_normal_margin',
						array(
							'label'      => __( 'Margin', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'product_action_button_normal_button_border',
							'label'    => __( 'Border', 'wbcom-essential' ),
							'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li',
						)
					);

					$this->add_responsive_control(
						'product_action_button_border_radius',
						array(
							'label'      => __( 'Border Radius', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							),
						)
					);

					$this->add_responsive_control(
						'product_action_button_width',
						array(
							'label'      => __( 'Width', 'wbcom-essential' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', '%' ),
							'range'      => array(
								'px' => array(
									'min'  => 0,
									'max'  => 200,
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
							'selectors'  => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li a' => 'width: {{SIZE}}{{UNIT}};',
							),
						)
					);

					$this->add_responsive_control(
						'product_action_button_height',
						array(
							'label'      => __( 'Height', 'wbcom-essential' ),
							'type'       => Controls_Manager::SLIDER,
							'size_units' => array( 'px', '%' ),
							'range'      => array(
								'px' => array(
									'min'  => 0,
									'max'  => 200,
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
							'selectors'  => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li a' => 'height: {{SIZE}}{{UNIT}};',
							),
						)
					);

				$this->end_controls_tab();

				// Hover.
				$this->start_controls_tab(
					'product_action_button_style_hover_tab',
					array(
						'label' => __( 'Hover', 'wbcom-essential' ),
					)
				);

					$this->add_control(
						'product_action_button_hover_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => wbcom_get_option( 'btn_hover_color', 'wbcom_style_tabs', '#1d76da' ),
							'selectors' => array(
								'{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li:hover a' => 'color: {{VALUE}};',
								'{{WRAPPER}} .wb-product-action .yith-wcwl-wishlistaddedbrowse a, .wb-product-action .yith-wcwl-wishlistexistsbrowse a' => 'color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						array(
							'name'     => 'product_action_button_hover_background_color',
							'label'    => __( 'Background', 'wbcom-essential' ),
							'types'    => array( 'classic', 'gradient' ),
							'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li:hover',
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'product_action_button_hover_button_border',
							'label'    => __( 'Border', 'wbcom-essential' ),
							'selector' => '{{WRAPPER}} .wb-products .wb-product .wb-product-inner .wb-product-action ul li:hover',
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		// Slider Button style.
		$this->start_controls_section(
			'products-slider-controller-style',
			array(
				'label'     => esc_html__( 'Slider Controller Style', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'product_layout_style' => 'slider',
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
									'step' => 5,
								),
								'%'  => array(
									'min' => 0,
									'max' => 100,
								),
							),
							'default'    => array(
								'unit' => '%',
								'size' => 50,
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
							'default'   => '#dddddd',
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
							'default'   => '#ffffff',
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
										'step' => 5,
									),
									'%'  => array(
										'min' => 0,
										'max' => 100,
									),
								),
								'default'    => array(
									'unit' => '%',
									'size' => 0,
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
								'default'   => '#ffffff',
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
							'default'   => '#23252a',
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
							'default'   => '#ffffff',
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
								'default'   => '#282828',
								'selectors' => array(
									'{{WRAPPER}} .product-slider .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}} !important;',
									'{{WRAPPER}} .product-slider .swiper-pagination-bullet-active' => 'background-color: {{VALUE}} !important;',
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

		// Product Tab menu setting.
		$this->start_controls_section(
			'wbcom-products-tab-menu',
			array(
				'label'     => esc_html__( 'Tab Menu Style', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'product_layout_style' => 'tab',
				),
			)
		);

			$this->add_responsive_control(
				'wbcom-tab-menu-align',
				array(
					'label'     => __( 'Alignment', 'wbcom-essential' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'    => array(
							'title' => __( 'Left', 'wbcom-essential' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'  => array(
							'title' => __( 'Center', 'wbcom-essential' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'   => array(
							'title' => __( 'Right', 'wbcom-essential' ),
							'icon'  => 'eicon-text-align-right',
						),
						'justify' => array(
							'title' => __( 'Justified', 'wbcom-essential' ),
							'icon'  => 'eicon-text-align-justify',
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .product-tab-list.wb-text-center' => 'text-align: {{VALUE}};',
					),
					'default'   => 'center',
					'separator' => 'after',
				)
			);

			$this->add_responsive_control(
				'product_tab_menu_area_margin',
				array(
					'label'      => __( 'Tab Menu Area Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-tab-menus' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'product_tab_style_tabs' );

				// Tab menu style Normal.
				$this->start_controls_tab(
					'product_tab_style_normal_tab',
					array(
						'label' => __( 'Normal', 'wbcom-essential' ),
					)
				);

					$this->add_group_control(
						Group_Control_Typography::get_type(),
						array(
							'name'     => 'tabmenutypography',
							'selector' => '{{WRAPPER}} .wb-tab-menus li a',
						)
					);

					$this->add_control(
						'tab_menu_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#23252a',
							'selectors' => array(
								'{{WRAPPER}} .wb-tab-menus li a' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'product_tab_menu_bg_color',
						array(
							'label'     => __( 'Product tab menu background', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#ffffff',
							'selectors' => array(
								'{{WRAPPER}} .wb-tab-menus li a' => 'background-color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'tabmenu_border',
							'label'    => __( 'Border', 'wbcom-essential' ),
							'selector' => '{{WRAPPER}} .wb-tab-menus li a',
						)
					);

					$this->add_responsive_control(
						'tabmenu_border_radius',
						array(
							'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
							'type'      => Controls_Manager::DIMENSIONS,
							'selectors' => array(
								'{{WRAPPER}} .wb-tab-menus li a' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
							),
						)
					);

					$this->add_responsive_control(
						'product_tab_menu_padding',
						array(
							'label'      => __( 'Tab Menu padding', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .wb-tab-menus li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							),
						)
					);

					$this->add_responsive_control(
						'product_tab_menu_margin',
						array(
							'label'      => __( 'Tab Menu margin', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .wb-tab-menus li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							),
						)
					);

				$this->end_controls_tab();// Normal tab menu style end.

				// Tab menu style Hover.
				$this->start_controls_tab(
					'product_tab_style_hover_tab',
					array(
						'label' => __( 'Hover', 'wbcom-essential' ),
					)
				);

					$this->add_control(
						'tab_menu_hover_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#23252a',
							'selectors' => array(
								'{{WRAPPER}} .wb-tab-menus li a:hover' => 'color: {{VALUE}};',
								'{{WRAPPER}} .wb-tab-menus li a.htactive' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'product_tab_menu_hover_bg_color',
						array(
							'label'     => __( 'Product tab menu background', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#ffffff',
							'selectors' => array(
								'{{WRAPPER}} .wb-tab-menus li a:hover' => 'background-color: {{VALUE}} !important;',
								'{{WRAPPER}} .wb-tab-menus li a.htactive' => 'background-color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'tabmenu_hover_border',
							'label'    => __( 'Border', 'wbcom-essential' ),
							'selector' => '{{WRAPPER}} .wb-tab-menus li a:hover',
							'selector' => '{{WRAPPER}} .wb-tab-menus li a.htactive',
						)
					);

					$this->add_responsive_control(
						'tabmenu_hover_border_radius',
						array(
							'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
							'type'      => Controls_Manager::DIMENSIONS,
							'selectors' => array(
								'{{WRAPPER}} .wb-tab-menus li a:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
								'{{WRAPPER}} .wb-tab-menus li a.htactive' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
							),
						)
					);

				$this->end_controls_tab();// Hover tab menu style end.

			$this->end_controls_tabs();

		$this->end_controls_section(); // Tab option end.
	}

	protected function render( $instance = array() ) {

		// Check if WooCommerce is active before rendering.
		if ( ! class_exists( 'WooCommerce' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<p>' . esc_html__( 'WooCommerce is required for this widget.', 'wbcom-essential' ) . '</p>';
			}
			return;
		}

		$settings        = $this->get_settings_for_display();
		$product_type    = $this->get_settings_for_display( 'wbcom_product_grid_product_filter' );
		$per_page        = $this->get_settings_for_display( 'wbcom_product_grid_products_count' );
		$custom_order_ck = $this->get_settings_for_display( 'wbcom_custom_order' );
		$orderby         = $this->get_settings_for_display( 'orderby' );
		$order           = $this->get_settings_for_display( 'order' );
		$tabuniqid       = $this->get_id();
		$columns         = $this->get_settings_for_display( 'wbcom_product_grid_column' );
		$same_height_box = $this->get_settings_for_display( 'same_height_box' );

		// Query Argument.
		$query_args = array(
			'per_page'     => $per_page,
			'product_type' => $product_type,
			'product_ids'  => $product_type === 'show_byid' && isset( $settings['wbcom_product_id'] ) ? $settings['wbcom_product_id'] : array(),
		);

		// Category Wise.
		$product_cats = isset( $settings['wbcom_product_grid_categories'] ) ? $settings['wbcom_product_grid_categories'] : array();
		if ( is_array( $product_cats ) && count( $product_cats ) > 0 ) {
			$query_args['categories'] = $product_cats;
		}

		/**
		 * Show by IDs.
		 */
		if ( 'show_byid' == $product_type && isset( $settings['wbcom_product_id'] ) ) {
			$query_args['product_ids'] = $settings['wbcom_product_id'];
		} elseif ( 'show_byid_manually' == $product_type && isset( $settings['wbcom_product_ids_manually'] ) ) {
			$query_args['product_ids'] = explode( ',', $settings['wbcom_product_ids_manually'] );
		} else {
			$query_args['product_ids'] = array();
		}

		// Custom Order.
		if ( $custom_order_ck == 'yes' ) {
			$query_args['custom_order'] = array(
				'orderby' => $orderby,
				'order'   => $order,
			);
		}

		$query_args['hidden']            = ( 'yes' === $settings['hidden_item'] );
		$query_args['hide_out_of_stock'] = ( 'yes' === $settings['hidden_outofstock'] );

		$args = wbcom_product_query( $query_args );

		$products = new \WP_Query( $args );

		// Calculate Column.
		$collumval = ( $settings['product_layout_style'] == 'slider' ) ? 'swiper-slide wb-product mb-30 product wb-col-xs-12' : 'wb-product wb-col-lg-4 wb-col-md-6 wb-col-sm-6 wb-col-xs-12 mb-30 product';
		if ( $columns != '' ) {
			if ( $columns == 5 ) {
				$collumval = 'wb-product cus-col-5 wb-col-md-6 wb-col-sm-6 wb-col-xs-12 mb-30 product';
			} else {
				$colwidth  = round( 12 / $columns );
				$collumval = 'wb-product wb-col-lg-' . $colwidth . ' wb-col-md-6 wb-col-sm-6 wb-col-xs-12 mb-30 product';
			}
		}

		// Action Button Style.
		if ( $settings['action_button_style'] == 2 ) {
			$collumval .= ' wb-product-action-style-2';
		} elseif ( $settings['action_button_style'] == 3 ) {
			$collumval .= ' wb-product-action-style-2 wb-product-action-round';
		} else {
			$collumval = $collumval;
		}

		// Position Action Button.
		if ( $settings['action_button_position'] == 'right' ) {
			$collumval .= ' wb-product-action-right';
		} elseif ( $settings['action_button_position'] == 'bottom' ) {
			$collumval .= ' wb-product-action-bottom';
		} elseif ( $settings['action_button_position'] == 'middle' ) {
			$collumval .= ' wb-product-action-middle';
		} elseif ( $settings['action_button_position'] == 'contentbottom' ) {
			$collumval .= ' wb-product-action-bottom-content';
		} else {
			$collumval = $collumval;
		}

		// Show Action.
		if ( $settings['action_button_show_on'] == 'hover' ) {
			$collumval .= ' wb-product-action-on-hover';
		}

		// Content Style.
		if ( $settings['product_content_style'] == 2 ) {
			$collumval .= ' wb-product-category-right-bottom';
		} elseif ( $settings['product_content_style'] == 3 ) {
			$collumval .= ' wb-product-ratting-top-right';
		} elseif ( $settings['product_content_style'] == 4 ) {
			$collumval .= ' wb-product-content-allcenter';
		} else {
			$collumval = $collumval;
		}

		// Slider Options.
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
		$slider_settings = array_merge( $slider_settings, $slider_responsive_settings );

		// Action Button.
		$this->add_render_attribute( 'action_btn_attr', 'class', 'wbcom-action-btn-area' );

		if ( $settings['addtocart_button_txt'] == 'yes' ) {
			$this->add_render_attribute( 'action_btn_attr', 'class', 'wbcom-btn-text-cart' );
		}

		$title_html_tag = wbcom_validate_html_tag( $settings['product_title_html_tag'] );

		?>
			<?php if ( $settings['product_layout_style'] == 'tab' ) { ?>
				<div class="product-tab-list wb-text-center">
					<ul class="wb-tab-menus">
						<?php
							$m = 0;
						if ( is_array( $product_cats ) && count( $product_cats ) > 0 ) {

							// Category retrive.
							$prod_categories = get_terms(
								array(
									'taxonomy'   => 'product_cat',
									'orderby'    => 'name',
									'order'      => 'ASC',
									'hide_empty' => true,
									'slug'       => $product_cats,
								)
							);

							foreach ( $prod_categories as $prod_cats ) {
								++$m;

								$field_name        = is_numeric( $product_cats[0] ) ? 'term_id' : 'slug';
								$args['tax_query'] = array(
									array(
										'taxonomy'         => 'product_cat',
										'terms'            => $prod_cats,
										'field'            => $field_name,
										'include_children' => false,
									),
								);
								if ( 'featured' == $product_type ) {
									$args['tax_query'][] = array(
										'taxonomy' => 'product_visibility',
										'field'    => 'name',
										'terms'    => 'featured',
										'operator' => 'IN',
									);
								}
								$fetchproduct = new \WP_Query( $args );

								if ( $fetchproduct->have_posts() ) {
									?>
											<li><a class="
											<?php
											if ( $m == 1 ) {
												echo esc_attr( 'htactive' );}
											?>
											" href="#wbcomtab<?php echo esc_attr( $tabuniqid . $m ); ?>">
											<?php echo esc_html( $prod_cats->name ); ?>
											</a></li>
										<?php
								}
							}
						}
						?>
					</ul>
				</div>
			<?php } ?>

			<?php if ( is_array( $product_cats ) && ( count( $product_cats ) > 0 ) && ( $settings['product_layout_style'] == 'tab' ) ) : ?>
				<div class="<?php echo esc_attr( $same_height_box == 'yes' ? 'wbcom-product-same-height' : '' ); ?> wb-products woocommerce">
					
					<?php
					$z                      = 0;
					$tabcatargs             = array(
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => true,
						'slug'       => $product_cats,
					);
					$tabcatargs['taxonomy'] = 'product_cat';
					$tabcat_fach            = get_terms( $tabcatargs );
					foreach ( $tabcat_fach as $cats ) :
						++$z;
						$field_name        = is_numeric( $product_cats[0] ) ? 'term_id' : 'slug';
						$args['tax_query'] = array(
							array(
								'taxonomy'         => 'product_cat',
								'terms'            => $cats,
								'field'            => $field_name,
								'include_children' => false,
							),
						);
						if ( 'featured' == $product_type ) {
							$args['tax_query'][] = array(
								'taxonomy' => 'product_visibility',
								'field'    => 'name',
								'terms'    => 'featured',
								'operator' => 'IN',
							);
						}
						$products = new \WP_Query( $args );

						if ( $products->have_posts() ) :
							?>
						<div class="wb-tab-pane 
							<?php
							if ( $z == 1 ) {
								echo esc_attr( 'htactive' ); }
							?>
							" id="<?php echo esc_attr( 'wbcomtab' . $tabuniqid . $z ); ?>">
							<div class="wb-row">

								<?php
								while ( $products->have_posts() ) :
									$products->the_post();

									// Sale Schedule.
									$offer_start_date_timestamp = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
									$offer_start_date           = $offer_start_date_timestamp ? date_i18n( 'Y/m/d', $offer_start_date_timestamp ) : '';
									$offer_end_date_timestamp   = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
									$offer_end_date             = $offer_end_date_timestamp ? date_i18n( 'Y/m/d', $offer_end_date_timestamp ) : '';

									// Gallery Image.
									global $product;
									$gallery_images_ids = $product->get_gallery_image_ids() ? $product->get_gallery_image_ids() : array();
									if ( has_post_thumbnail() ) {
										array_unshift( $gallery_images_ids, $product->get_image_id() );
									}

									?>

									<!--Product Start-->
									<div class="<?php echo esc_attr( $collumval ); ?>">
										<div class="wb-product-inner">

											<div class="wb-product-image-wrap">
												<?php
												if ( class_exists( 'WooCommerce' ) ) {
													wbcom_custom_product_badge();
													wbcom_sale_flash();
												}
												?>
												<div class="wb-product-image">
													<?php if ( $settings['thumbnails_style'] == 2 && $gallery_images_ids ) : ?>
														<div class="wb-product-image-slider swiper-container wb-product-image-thumbnaisl-<?php echo esc_attr( $tabuniqid ); ?>">
															<div class="swiper-wrapper">
															<?php
															foreach ( $gallery_images_ids as $gallery_attachment_id ) {
																echo '<div class="swiper-slide"><a href="' . esc_url( get_the_permalink() ) . '" class="item">' . wp_get_attachment_image( $gallery_attachment_id, 'woocommerce_thumbnail' ) . '</a></div>';
															}
															?>
															</div>
															<div class="swiper-pagination"></div>
															<div class="elementor-swiper-button elementor-swiper-button-prev"><i class="wbe-icons wbe-icon-angle-left"></i></div>
															<div class="elementor-swiper-button elementor-swiper-button-next"><i class="wbe-icons wbe-icon-angle-right"></i></div>
														</div>

														<?php
													elseif ( $settings['thumbnails_style'] == 3 && $gallery_images_ids ) :
														$tabactive = '';
														?>
														<div class="wb-product-cus-tab">
															<?php
																$i = 0;
															foreach ( $gallery_images_ids as $gallery_attachment_id ) {
																++$i;
																if ( $i == 1 ) {
																	$tabactive = 'htactive';
																} else {
																	$tabactive = ' '; }
																echo '<div class="wb-product-cus-tab-pane ' . esc_attr( $tabactive ) . '" id="image-' . esc_attr( $i . get_the_ID() ) . '"><a href="' . esc_url( get_the_permalink() ) . '">' . wp_get_attachment_image( $gallery_attachment_id, 'woocommerce_thumbnail' ) . '</a></div>';
															}
															?>
														</div>
														<ul class="wb-product-cus-tab-links">
															<?php
																$j = 0;
															foreach ( $gallery_images_ids as $gallery_attachment_id ) {
																++$j;
																if ( $j == 1 ) {
																	$tabactive = 'htactive';
																} else {
																	$tabactive = ' '; }
																echo '<li><a href="#image-' . esc_attr( $j . get_the_ID() ) . '" class="' . esc_attr( $tabactive ) . '">' . wp_get_attachment_image( $gallery_attachment_id, 'woocommerce_gallery_thumbnail' ) . '</a></li>';
															}
															?>
														</ul>

													<?php else : ?>
														<a href="<?php the_permalink(); ?>">
															<?php woocommerce_template_loop_product_thumbnail(); ?>
														</a>
													<?php endif; ?>

												</div>

												<?php
												if ( $settings['show_action_button'] == 'yes' ) {
													if ( $settings['action_button_position'] != 'contentbottom' ) :
														?>
													<div class="wb-product-action">
																											<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor method ?>
														<ul <?php echo $this->get_render_attribute_string( 'action_btn_attr' ); ?>>
																												<?php
																												if ( function_exists( 'wbcom_compare_button' ) && true === wbcom_exist_compare_plugin() ) {
																													echo '<li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																														wbcom_compare_button(
																															array(
																																'style' => 2,
																																'btn_text' => '<i class="wbe-icons wbe-icon-spin5"></i>',
																																'btn_added_txt' => '<i class="wbe-icons wbe-icon-check-circle"></i>',
																															)
																														);
																													echo '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																												}
																												?>
															<li class="wbcom-cart"><?php woocommerce_template_loop_add_to_cart(); ?></li>
														</ul>
													</div>
																									<?php endif; } ?>

											</div>

											<div class="wb-product-content">
												<div class="wb-product-content-inner">
													<div class="wb-product-categories"><?php wbcom_get_product_category_list(); ?></div>
													<?php do_action( 'wbcom_universal_before_title' ); ?>
													<?php echo wp_kses_post( sprintf( "<%s class='wb-product-title'><a href='%s'>%s</a></%s>", esc_html( $title_html_tag ), esc_url( get_the_permalink() ), esc_html( get_the_title() ), esc_html( $title_html_tag ) ) ); ?>
													<?php do_action( 'wbcom_universal_after_title' ); ?>
													<?php do_action( 'wbcom_universal_before_price' ); ?>
													<div class="wb-product-price"><?php woocommerce_template_loop_price(); ?></div>
													<?php do_action( 'wbcom_universal_after_price' ); ?>
													<div class="wb-product-ratting-wrap"><?php echo wbcom_wc_get_rating_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function returns escaped HTML ?></div>

													<?php
													if ( $settings['show_action_button'] == 'yes' ) {
														if ( $settings['action_button_position'] == 'contentbottom' ) :
															?>
														<div class="wb-product-action">
																													<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor method ?>
														<ul <?php echo $this->get_render_attribute_string( 'action_btn_attr' ); ?>>
																<?php
																if ( function_exists( 'wbcom_compare_button' ) && true === wbcom_exist_compare_plugin() ) {
																	echo '<li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																		wbcom_compare_button(
																			array(
																				'style' => 2,
																				'btn_text' => '<i class="wbe-icons wbe-icon-spin5"></i>',
																				'btn_added_txt' => '<i class="wbe-icons wbe-icon-check-circle"></i>',
																			)
																		);
																	echo '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																}
																?>
																<li class="wbcom-cart"><?php woocommerce_template_loop_add_to_cart(); ?></li>
															</ul>
														</div>
																											<?php endif; } ?>

												</div>
											</div>

										</div>
									</div>
									<!--Product End-->

									<?php
								endwhile;
								wp_reset_query();
								wp_reset_postdata();
								?>

							</div>
						</div>
							<?php
					endif;
endforeach;
					?>
					
				</div>

			<?php else : ?>
				<?php
					$slider_main_div_style = '';
				if ( $settings['product_layout_style'] == 'slider' ) {
					$slider_main_div_style = "style='display:none'";
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
					echo '<div class="wb-row">';
				}
				?>
					<div class="<?php echo esc_attr( $same_height_box == 'yes' ? 'wbcom-product-same-height' : '' ); ?> wb-products woocommerce
					<?php
					if ( $settings['product_layout_style'] == 'slider' ) {
						echo esc_attr( 'product-slider swiper-container' );
					} else {
						echo esc_attr( 'wb-row' ); }
					?>
					" dir="<?php echo esc_attr( $direction ); ?>" data-settings='
					<?php
					if ( $settings['product_layout_style'] == 'slider' ) {
										echo esc_attr( wp_json_encode( $slider_settings ) ); }
					?>
' <?php echo esc_attr( $slider_main_div_style ); ?> >
					<?php if ( $settings['product_layout_style'] == 'slider' ) : ?>
						<div class="swiper-wrapper">
					<?php endif; ?>

						<?php
						if ( $products->have_posts() ) :

							while ( $products->have_posts() ) :
								$products->the_post();

								// Sale Schedule.
								$offer_start_date_timestamp = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
								$offer_start_date           = $offer_start_date_timestamp ? date_i18n( 'Y/m/d', $offer_start_date_timestamp ) : '';
								$offer_end_date_timestamp   = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
								$offer_end_date             = $offer_end_date_timestamp ? date_i18n( 'Y/m/d', $offer_end_date_timestamp ) : '';

								// Gallery Image.
								global $product;
								$gallery_images_ids = $product->get_gallery_image_ids() ? $product->get_gallery_image_ids() : array();
								if ( has_post_thumbnail() ) {
									array_unshift( $gallery_images_ids, $product->get_image_id() );
								}

								?>

							<!--Product Start-->
							<div class="<?php echo esc_attr( $collumval ); ?>">
								<div class="wb-product-inner">

									<div class="wb-product-image-wrap">
									<?php
									if ( class_exists( 'WooCommerce' ) ) {
										wbcom_custom_product_badge();
										wbcom_sale_flash();
									}
									?>
										<div class="wb-product-image">
										<?php if ( $settings['thumbnails_style'] == 2 && $gallery_images_ids ) : ?>
												<div class="wb-product-image-slider swiper-container wb-product-image-thumbnaisl-<?php echo esc_attr( $tabuniqid ); ?>">
													<div class="swiper-wrapper">
													<?php
													foreach ( $gallery_images_ids as $gallery_attachment_id ) {
														echo '<div class="swiper-slide"><a href="' . esc_url( get_the_permalink() ) . '" class="item">' . wp_get_attachment_image( $gallery_attachment_id, 'woocommerce_thumbnail' ) . '</a></div>';
													}
													?>
													</div>
													<div class="swiper-pagination"></div>
													<div class="elementor-swiper-button elementor-swiper-button-prev"><i class="wbe-icons wbe-icon-angle-left"></i></div>
													<div class="elementor-swiper-button elementor-swiper-button-next"><i class="wbe-icons wbe-icon-angle-right"></i></div>
												</div>

											<?php
											elseif ( $settings['thumbnails_style'] == 3 && $gallery_images_ids ) :
												$tabactive = '';
												?>
												<div class="wb-product-cus-tab">
													<?php
														$i = 0;
													foreach ( $gallery_images_ids as $gallery_attachment_id ) {
														++$i;
														if ( $i == 1 ) {
															$tabactive = 'htactive';
														} else {
															$tabactive = ' '; }
														echo '<div class="wb-product-cus-tab-pane ' . esc_attr( $tabactive ) . '" id="image-' . esc_attr( $i . get_the_ID() ) . '"><a href="#">' . wp_get_attachment_image( $gallery_attachment_id, 'woocommerce_thumbnail' ) . '</a></div>';
													}
													?>
												</div>
												<ul class="wb-product-cus-tab-links">
													<?php
														$j = 0;
													foreach ( $gallery_images_ids as $gallery_attachment_id ) {
														++$j;
														if ( $j == 1 ) {
															$tabactive = 'htactive';
														} else {
															$tabactive = ' '; }
														echo '<li><a href="#image-' . esc_attr( $j . get_the_ID() ) . '" class="' . esc_attr( $tabactive ) . '">' . wp_get_attachment_image( $gallery_attachment_id, 'woocommerce_gallery_thumbnail' ) . '</a></li>';
													}
													?>
												</ul>

											<?php else : ?>
												<a href="<?php the_permalink(); ?>"> 
													<?php woocommerce_template_loop_product_thumbnail(); ?> 
												</a>
											<?php endif; ?>

										</div>

										<?php
										if ( $settings['show_action_button'] == 'yes' ) {
											if ( $settings['action_button_position'] != 'contentbottom' ) :
												?>
											<div class="wb-product-action">
																							<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor method ?>
														<ul <?php echo $this->get_render_attribute_string( 'action_btn_attr' ); ?>>
																								<?php
																								if ( function_exists( 'wbcom_compare_button' ) && true === wbcom_exist_compare_plugin() ) {
																									echo '<li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																										wbcom_compare_button(
																											array(
																												'style' => 2,
																												'btn_text' => '<i class="wbe-icons wbe-icon-spin5"></i>',
																												'btn_added_txt' => '<i class="wbe-icons wbe-icon-check-circle"></i>',
																											)
																										);
																									echo '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																								}
																								?>
													<li class="wbcom-cart"><?php woocommerce_template_loop_add_to_cart(); ?></li>
												</ul>
											</div>
																					<?php endif; } ?>

									</div>

									<div class="wb-product-content">
										<div class="wb-product-content-inner">
											<div class="wb-product-categories"><?php wbcom_get_product_category_list(); ?></div>
											<?php do_action( 'wbcom_universal_before_title' ); ?>
											<?php echo wp_kses_post( sprintf( "<%s class='wb-product-title'><a href='%s'>%s</a></%s>", esc_html( $title_html_tag ), esc_url( get_the_permalink() ), esc_html( get_the_title() ), esc_html( $title_html_tag ) ) ); ?>
											<?php do_action( 'wbcom_universal_after_title' ); ?>
											<?php do_action( 'wbcom_universal_before_price' ); ?>
											<div class="wb-product-price"><?php woocommerce_template_loop_price(); ?></div>
											<?php do_action( 'wbcom_universal_after_price' ); ?>
											<div class="wb-product-ratting-wrap"><?php echo wbcom_wc_get_rating_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Function returns escaped HTML ?></div>

											<?php
											if ( $settings['show_action_button'] == 'yes' ) {
												if ( $settings['action_button_position'] == 'contentbottom' ) :
													?>
												<div class="wb-product-action">
																									<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor method ?>
														<ul <?php echo $this->get_render_attribute_string( 'action_btn_attr' ); ?>>
																										<?php
																										if ( function_exists( 'wbcom_compare_button' ) && true === wbcom_exist_compare_plugin() ) {
																											echo '<li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																												wbcom_compare_button(
																													array(
																														'style' => 2,
																														'btn_text' => '<i class="wbe-icons wbe-icon-spin5"></i>',
																														'btn_added_txt' => '<i class="wbe-icons wbe-icon-check-circle"></i>',
																													)
																												);
																											echo '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
																										}
																										?>
														<li class="wbcom-cart"><?php woocommerce_template_loop_add_to_cart(); ?></li>
													</ul>
												</div>
																							<?php endif; } ?>
										</div>
									</div>

								</div>
							</div>
							<!--Product End-->

								<?php
						endwhile;
							wp_reset_query();
							wp_reset_postdata();
endif;
						?>
					<?php if ( $settings['product_layout_style'] == 'slider' ) : ?>
						</div><!-- /.swiper-wrapper -->
						<div class="swiper-pagination"></div>
						<div class="elementor-swiper-button elementor-swiper-button-prev"><i class="wbe-icons wbe-icon-angle-left"></i></div>
						<div class="elementor-swiper-button elementor-swiper-button-next"><i class="wbe-icons wbe-icon-angle-right"></i></div>
					<?php endif; ?>
					</div>
				<?php
				if ( $settings['product_layout_style'] == 'slider' ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded HTML tag.
					echo '</div>'; }
				?>
			<?php endif; ?>

			<?php if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) { ?>
				<script>
					;jQuery(document).ready(function($) {
						'use strict';
						$(".wb-product-image-thumbnaisl-<?php echo esc_js( $tabuniqid ); ?>").each(function() {
							new Swiper(this, {
								slidesPerView: 1,
								pagination: {
									el: $(this).find('.swiper-pagination').get(0),
									type: 'bullets',
									clickable: true
								},
								navigation: {
									prevEl: $(this).find('.elementor-swiper-button-prev').get(0),
									nextEl: $(this).find('.elementor-swiper-button-next').get(0)
								}
							});
						});
					});
				</script>
			<?php } ?>

		<?php
	}
}
