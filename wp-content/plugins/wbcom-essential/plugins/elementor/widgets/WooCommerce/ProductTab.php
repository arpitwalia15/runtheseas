<?php
/**
 * Elementor widget product tab.
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
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

/**
 * Product Tab.
 *
 * @since      3.7.1
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/woocommerce
 */
class ProductTab extends \Elementor\Widget_Base {

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
		return 'wbcom-product-tab';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Product Tab', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-cart-medium';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array(
			'wbflexboxgrid',
			'wbe-swiper',
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
		return array( 'product tab', 'tab', 'tabs', 'tab with product', 'product' );
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'wbcom-products',
			array(
				'label' => esc_html__( 'Product Settings', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'wbcom_product_style',
				array(
					'label'   => esc_html__( 'Product Style', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1' => esc_html__( 'Style One', 'wbcom-essential' ),
						'2' => esc_html__( 'Style Two', 'wbcom-essential' ),
						'3' => esc_html__( 'Style Three', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'wbcom_product_grid_product_filter',
				array(
					'label'   => esc_html__( 'Filter By', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'recent',
					'options' => array(
						'recent'       => esc_html__( 'Recent Products', 'wbcom-essential' ),
						'featured'     => esc_html__( 'Featured Products', 'wbcom-essential' ),
						'best_selling' => esc_html__( 'Best Selling Products', 'wbcom-essential' ),
						'sale'         => esc_html__( 'Sale Products', 'wbcom-essential' ),
						'top_rated'    => esc_html__( 'Top Rated Products', 'wbcom-essential' ),
						'mixed_order'  => esc_html__( 'Mixed order Products', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'wbcom_product_grid_column',
				array(
					'label'     => esc_html__( 'Columns', 'wbcom-essential' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => '4',
					'options'   => array(
						'1' => esc_html__( '1', 'wbcom-essential' ),
						'2' => esc_html__( '2', 'wbcom-essential' ),
						'3' => esc_html__( '3', 'wbcom-essential' ),
						'4' => esc_html__( '4', 'wbcom-essential' ),
						'5' => esc_html__( '5', 'wbcom-essential' ),
						'6' => esc_html__( '6', 'wbcom-essential' ),
					),
					'condition' => array(
						'proslider!' => 'yes',
					),
				)
			);

			$this->add_control(
				'wbcom_product_grid_row',
				array(
					'label'   => __( 'Rows', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 1,
					'min'     => 1,
					'max'     => 20,
					'step'    => 1,
				)
			);

			$this->add_control(
				'wbcom_product_grid_products_count',
				array(
					'label'   => __( 'Number of Products', 'wbcom-essential' ),
					'type'    => Controls_Manager::NUMBER,
					'default' => 4,
					'min'     => 1,
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
				)
			);

			$this->add_control(
				'custom_order',
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
						'custom_order' => 'yes',
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
						'custom_order' => 'yes',
					),
				)
			);

			$this->add_control(
				'producttab',
				array(
					'label'        => esc_html__( 'Product Tab', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
					'condition'    => array(
						'proslider!' => 'yes',
					),
				)
			);

			$this->add_control(
				'proslider',
				array(
					'label'        => esc_html__( 'Product Slider', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
					'description'  => esc_html__( 'When the product tab is off, Then working slider.', 'wbcom-essential' ),
				)
			);

		$this->end_controls_section();

		// Product Tab menu setting.
		$this->start_controls_section(
			'wbcom-products-tab-menu',
			array(
				'label'     => esc_html__( 'Tab Menu Style', 'wbcom-essential' ),
				'condition' => array(
					'producttab' => 'yes',
					'proslider!' => 'yes',
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

			$this->start_controls_tabs(
				'product_tab_style_tabs'
			);

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
								'{{WRAPPER}} .wb-tab-menus li a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
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

		// Product slider setting.
		$this->start_controls_section(
			'wbcom-products-slider',
			array(
				'label'     => esc_html__( 'Slider Option', 'wbcom-essential' ),
				'condition' => array(
					'proslider' => 'yes',
				),
			)
		);

			$this->add_control(
				'slitems',
				array(
					'label'     => esc_html__( 'Slider Items', 'wbcom-essential' ),
					'type'      => Controls_Manager::NUMBER,
					'min'       => 1,
					'max'       => 10,
					'step'      => 1,
					'default'   => 4,
					'condition' => array(
						'proslider' => 'yes',
					),
				)
			);

			$this->add_control(
				'slarrows',
				array(
					'label'        => esc_html__( 'Slider Arrow', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array(
						'proslider' => 'yes',
					),
				)
			);

			$this->add_control(
				'sldots',
				array(
					'label'        => esc_html__( 'Slider dots', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'no',
					'condition'    => array(
						'proslider' => 'yes',
					),
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
					'condition'    => array(
						'proslider' => 'yes',
					),
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

			// Slider Button stle.
			$this->start_controls_section(
				'products-slider-controller-style',
				array(
					'label'     => esc_html__( 'Slider Controller Style', 'wbcom-essential' ),
					'condition' => array(
						'proslider' => 'yes',
					),
				)
			);

				$this->start_controls_tabs(
					'product_sliderbtn_style_tabs'
				);

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

			// Style tab section.
			$this->start_controls_section(
				'product_style',
				array(
					'label' => __( 'Style', 'wbcom-essential' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				)
			);

			$this->start_controls_tabs(
				'style_tabs'
			);

				// Normal style tab.
				$this->start_controls_tab(
					'style_normal_tab',
					array(
						'label' => __( 'Normal', 'wbcom-essential' ),
					)
				);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						array(
							'name'     => 'product_border',
							'label'    => __( 'Border', 'wbcom-essential' ),
							'selector' => '{{WRAPPER}} .product-item .product-inner',
						)
					);

					$this->add_responsive_control(
						'product_border_radius',
						array(
							'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
							'type'      => Controls_Manager::DIMENSIONS,
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
								'{{WRAPPER}} .product-item .product-inner .image-wrap' => 'border-radius: {{TOP}}px {{RIGHT}}px 0px 0px;',
								'{{WRAPPER}} .product-item .product-inner .image-wrap img' => 'border-radius: {{TOP}}px {{RIGHT}}px 0px 0px;',
								'{{WRAPPER}} .product-item .product-inner .content' => 'border-radius: 0px 0px {{BOTTOM}}px {{LEFT}}px;',
							),
						)
					);

					$this->add_responsive_control(
						'product_image_padding',
						array(
							'label'      => __( 'Product Image Area Padding', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .product-item .product-inner .image-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							),
						)
					);

					$this->add_control(
						'product_image_bg_color',
						array(
							'label'     => __( 'Product Image Background', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#ffffff',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .image-wrap' => 'background-color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_responsive_control(
						'product_content_padding',
						array(
							'label'      => __( 'Product Content Area Padding', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .product-item .product-inner .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							),
						)
					);

					$this->add_control(
						'product_content_bg_color',
						array(
							'label'     => __( 'Product Content Background', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .content' => 'background-color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_control(
						'wbcom_product_title_heading',
						array(
							'label' => __( 'Title', 'wbcom-essential' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_responsive_control(
						'aligntitle',
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
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .content .title' => 'text-align: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Typography::get_type(),
						array(
							'name'     => 'typography',
							'selector' => '{{WRAPPER}} .product-item .product-inner .content .title',
						)
					);

					$this->add_control(
						'title_color',
						array(
							'label'     => __( 'Title color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#444444',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .content .title a' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'wbcom_product_price_heading',
						array(
							'label' => __( 'Product Price', 'wbcom-essential' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'price_color',
						array(
							'label'     => __( 'Price color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#444444',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .content .price ' => 'color: {{VALUE}};',
								'{{WRAPPER}} .product-item .product-inner .content .price .amount' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_group_control(
						Group_Control_Typography::get_type(),
						array(
							'name'     => 'pricetypography',
							'selector' => '{{WRAPPER}} .product-item .product-inner .content .price ',
						)
					);

					$this->add_responsive_control(
						'alignprice',
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
							'default'   => '',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .content' => 'text-align: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

				// Hover Style tab.
				$this->start_controls_tab(
					'style_hover_tab',
					array(
						'label' => __( 'Hover', 'wbcom-essential' ),
					)
				);

					$this->add_control(
						'title_hovercolor',
						array(
							'label'     => __( 'Title color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#dc9a0e',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .content .title a:hover' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'product_hoverbg_color',
						array(
							'label'     => __( 'Product content background', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#ffffff',
							'selectors' => array(
								'{{WRAPPER}} .product-item .product-inner .product_information_area .content' => 'background-color: {{VALUE}} !important;',
							),
						)
					);

					$this->add_responsive_control(
						'product_hover_content_padding',
						array(
							'label'      => __( 'Product hover content area padding', 'wbcom-essential' ),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => array( 'px', '%', 'em' ),
							'selectors'  => array(
								'{{WRAPPER}} .product-item .product-inner .product_information_area .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		// Action Button Style.
		$this->start_controls_section(
			'product_action_button_style',
			array(
				'label' => __( 'Action Button', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->start_controls_tabs( 'action_button_style_tabs' );

				// Normal style tab.
				$this->start_controls_tab(
					'action_button_style_normal_tab',
					array(
						'label' => __( 'Normal', 'wbcom-essential' ),
					)
				);

					$this->add_control(
						'action_button_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#444444',
							'selectors' => array(
								'{{WRAPPER}} .product-item .actions a, {{WRAPPER}} .product-item .woocommerce.compare-button a.button, {{WRAPPER}} .product-item .actions a::before' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'action_button_bg_color',
						array(
							'label'     => __( 'Background Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#ffffff',
							'selectors' => array(
								'{{WRAPPER}} .product-item .actions' => 'background-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'action_button_font_size',
						array(
							'label'      => __( 'Font Size', 'wbcom-essential' ),
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
								'unit' => 'px',
								'size' => 16,
							),
							'selectors'  => array(
								'{{WRAPPER}} .product-item .actions a::before,{{WRAPPER}} .product-item .actions a' => 'font-size: {{SIZE}}{{UNIT}};',
							),
						)
					);

				$this->end_controls_tab();

				// Hover style tab.
				$this->start_controls_tab(
					'action_button_style_hover_tab',
					array(
						'label' => __( 'Hover', 'wbcom-essential' ),
					)
				);
					$this->add_control(
						'action_button_hover_color',
						array(
							'label'     => __( 'Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#dc9a0e',
							'selectors' => array(
								'{{WRAPPER}} .product-item .actions a:hover, {{WRAPPER}} .product-item .woocommerce.compare-button a.button:hover, {{WRAPPER}} .product-item .actions a:hover::before' => 'color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						'action_button_hover_bg_color',
						array(
							'label'     => __( 'Background Color', 'wbcom-essential' ),
							'type'      => Controls_Manager::COLOR,
							'default'   => '#ffffff',
							'selectors' => array(
								'{{WRAPPER}} .product-item .actions:hover' => 'background-color: {{VALUE}};',
							),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
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
		$custom_order_ck = $this->get_settings_for_display( 'custom_order' );
		$orderby         = $this->get_settings_for_display( 'orderby' );
		$order           = $this->get_settings_for_display( 'order' );
		$columns         = $this->get_settings_for_display( 'wbcom_product_grid_column' );
		$rows            = $this->get_settings_for_display( 'wbcom_product_grid_row' );
		$tabuniqid       = $this->get_id();
		$proslider       = $this->get_settings_for_display( 'proslider' );
		$producttab      = $this->get_settings_for_display( 'producttab' );

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

		// WooCommerce Category.
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $per_page,
		);

		switch ( $product_type ) {

			case 'sale':
				$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
				break;

			case 'featured':
				$args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);
				break;

			case 'best_selling':
				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'desc';
				break;

			case 'top_rated':
				$args['meta_key'] = '_wc_average_rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'desc';
				break;

			case 'mixed_order':
				$args['orderby'] = 'rand';
				break;

			default: /* Recent */
				$args['orderby'] = 'date';
				$args['order']   = 'desc';
				break;
		}

		// Custom Order.
		if ( $custom_order_ck == 'yes' ) {
			$args['orderby'] = $orderby;
			$args['order']   = $order;
		}

		$get_product_categories = $settings['wbcom_product_grid_categories']; // get custom field value
		$product_cats           = str_replace( ' ', '', $get_product_categories );

		if ( '0' != $get_product_categories ) {
			if ( is_array( $product_cats ) && count( $product_cats ) > 0 ) {
				$field_name          = is_numeric( $product_cats[0] ) ? 'term_id' : 'slug';
				$args['tax_query'][] = array(
					array(
						'taxonomy'         => 'product_cat',
						'terms'            => $product_cats,
						'field'            => $field_name,
						'include_children' => false,
					),
				);
			}
		}

		$products = new \WP_Query( $args );

		$tabmenu = 'yes';

		if ( ( $proslider == 'yes' ) && ( $producttab != 'yes' ) ) {
			$collumval = 'slide-item wb-col-xs-12';
		} else {
			$collumval = 'wb-col-lg-3 wb-col-md-6 wb-col-sm-6 wb-col-xs-12 mb-50';
			if ( $columns != '' ) {
				if ( $columns == 5 ) {
					$collumval = 'cus-col-5 wb-col-md-6 wb-col-sm-6 wb-col-xs-12 mb-50';
				} else {
					$colwidth  = round( 12 / $columns );
					$collumval = 'wb-col-lg-' . $colwidth . ' wb-col-md-6 wb-col-sm-6 wb-col-xs-12 mb-50';
				}
			}
		}

		?>
		  
		<div class="product-style">

			<?php if ( $producttab == 'yes' ) { ?>
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

			<?php if ( is_array( $product_cats ) && ( count( $product_cats ) > 0 ) && ( $producttab == 'yes' ) ) : ?>
				
				<?php
				$j           = 0;
				$tabcatargs  = array(
					'taxonomy'   => 'product_cat',
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => true,
					'slug'       => $product_cats,
				);
				$tabcat_fach = get_terms( $tabcatargs );
				foreach ( $tabcat_fach as $cats ) :
					++$j;
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
						if ( $j == 1 ) {
							echo esc_attr( 'htactive' );}
						?>
					" id="<?php echo esc_attr( 'wbcomtab' . $tabuniqid . $j ); ?>">
						
						<div class="wb-row">
							<div class="<?php echo esc_attr( $collumval ); ?>">
								<?php
									$k = 1;
								while ( $products->have_posts() ) :
									$products->the_post();
									?>

								<div class="product-item 
									<?php
									if ( $rows > 1 && ( $k % $rows != 0 ) ) {
										echo esc_attr( 'mb-30 ' );
									} if ( $settings['wbcom_product_style'] == 3 ) {
										echo esc_attr( 'product_style_three' ); }
									?>
								">

									<div class="product-inner">
										<div class="image-wrap">
											<a href="<?php the_permalink(); ?>" class="image">
											<?php
												woocommerce_show_product_loop_sale_flash();
												woocommerce_template_loop_product_thumbnail();
											?>
											</a>
											<?php if ( $settings['wbcom_product_style'] == 3 ) : ?>
												<div class="product_information_area">

													<?php
														global $product;
														$attributes = $product->get_attributes();
													if ( $attributes ) :
														echo '<div class="product_attribute">';
														foreach ( $attributes as $attribute ) :
															$name = $attribute->get_name();
															?>
															<ul>
															<?php
																echo '<li class="attribute_label">' . esc_html( wc_attribute_label( $attribute->get_name() ) ) . esc_html__( ':', 'wbcom-essential' ) . '</li>';
															if ( $attribute->is_taxonomy() ) {
																global $wc_product_attributes;
																$product_terms = wc_get_product_terms( $product->get_id(), $name, array( 'fields' => 'all' ) );
																foreach ( $product_terms as $product_term ) {
																	$product_term_name = esc_html( $product_term->name );
																	$link              = get_term_link( $product_term->term_id, $name );
																	$color             = get_term_meta( $product_term->term_id, 'color', true );
																	if ( ! empty( $wc_product_attributes[ $name ]->attribute_public ) ) {
																		echo '<li><a href="' . esc_url( $link ) . '" rel="tag">' . esc_html( $product_term_name ) . '</a></li>';
																	} elseif ( ! empty( $color ) ) {
																			echo '<li class="color_attribute" style="background-color: ' . esc_attr( $color ) . ';">&nbsp;</li>';
																	} else {
																		echo '<li>' . esc_html( $product_term_name ) . '</li>';
																	}
																}
															}
															?>
															</ul>
															<?php
													endforeach;
														echo '</div>';
													endif;
													?>

													<div class="content">
														<h4 class="title"><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h4>
														<?php woocommerce_template_loop_price(); ?>
														<?php do_action( 'wbcom_addon_after_price' ); ?>
													</div>

												</div>

											<?php else : ?>
												<div class="actions 
												<?php
												if ( $settings['wbcom_product_style'] == 2 ) {
													echo esc_attr( 'style_two' ); }
												?>
													">
													<?php
													if ( $settings['wbcom_product_style'] == 2 ) {
														woocommerce_template_loop_add_to_cart();
													} else {
														woocommerce_template_loop_add_to_cart();
													}
													?>
												</div>
											<?php endif; ?>

											
										</div>
										
										<div class="content">
											<h4 class="title"><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h4>
											<?php woocommerce_template_loop_price(); ?>
											<?php do_action( 'wbcom_addon_after_price' ); ?>
										</div>
									</div>

								</div>

									<?php if ( $k % $rows == 0 && ( $products->post_count != $k ) ) { ?>
							</div>
							<div class="<?php echo esc_attr( $collumval ); ?>">
										<?php
									} ++$k;
								endwhile;
								wp_reset_postdata();
								?>
							</div>
						</div>

					</div>
						<?php
				endif;
				endforeach;
				?>

			<?php else : ?>
				<div class="wb-row product-slider-row">
					<?php
						$slider_main_div_style = '';
					if ( $proslider == 'yes' ) {
						$slider_main_div_style = "style='display:none'";
						echo '<div id="product-slider-' . esc_attr( uniqid() ) . '" dir="' . esc_attr( $direction ) . '" class="product-slider swiper-container" ' . esc_attr( $slider_main_div_style ) . ' data-settings=\'' . esc_attr( wp_json_encode( $slider_settings ) ) . '\'>';
						echo '<div class="swiper-wrapper">';
					}
					?>

						<div class="<?php echo esc_attr( $proslider == 'yes' ? $collumval . ' swiper-slide' : $collumval ); ?>">
							<?php
								$k = 1;
							if ( $products->have_posts() ) :
								while ( $products->have_posts() ) :
									$products->the_post();
									?>

								<div class="product-item 
									<?php
									if ( $rows > 1 && ( $k % $rows != 0 ) ) {
										echo esc_attr( 'mb-30' );
									} if ( $settings['wbcom_product_style'] == 3 ) {
										echo esc_attr( 'product_style_three' ); }
									?>
								">

									<div class="product-inner">
										<div class="image-wrap">
											<a href="<?php the_permalink(); ?>" class="image">
											<?php
												woocommerce_show_product_loop_sale_flash();
												woocommerce_template_loop_product_thumbnail();
											?>
											</a>
											<?php if ( $settings['wbcom_product_style'] == 3 ) : ?>
												<div class="product_information_area">

													<?php
														global $product;
														$attributes = $product->get_attributes();
													if ( $attributes ) :
														echo '<div class="product_attribute">';
														foreach ( $attributes as $attribute ) :
															$name = $attribute->get_name();
															?>
															<ul>
															<?php
																echo '<li class="attribute_label">' . esc_html( wc_attribute_label( $attribute->get_name() ) ) . esc_html__( ':', 'wbcom-essential' ) . '</li>';
															if ( $attribute->is_taxonomy() ) {
																global $wc_product_attributes;
																$product_terms = wc_get_product_terms( $product->get_id(), $name, array( 'fields' => 'all' ) );
																foreach ( $product_terms as $product_term ) {
																	$product_term_name = esc_html( $product_term->name );
																	$link              = get_term_link( $product_term->term_id, $name );
																	$color             = get_term_meta( $product_term->term_id, 'color', true );
																	if ( ! empty( $wc_product_attributes[ $name ]->attribute_public ) ) {
																		echo '<li><a href="' . esc_url( $link ) . '" rel="tag">' . esc_html( $product_term_name ) . '</a></li>';
																	} elseif ( ! empty( $color ) ) {
																			echo '<li class="color_attribute" style="background-color: ' . esc_attr( $color ) . ';">&nbsp;</li>';
																	} else {
																		echo '<li>' . esc_html( $product_term_name ) . '</li>';
																	}
																}
															}
															?>
															</ul>
															<?php
													endforeach;
														echo '</div>';
													endif;
													?>

													<div class="actions style_two">
														<?php
															woocommerce_template_loop_add_to_cart();
														?>
													</div>

													<div class="content">
														<h4 class="title"><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h4>
														<?php woocommerce_template_loop_price(); ?>
														<?php do_action( 'wbcom_addon_after_price' ); ?>
													</div>

												</div>

											<?php else : ?>
												<div class="actions 
												<?php
												if ( $settings['wbcom_product_style'] == 2 ) {
													echo esc_attr( 'style_two' ); }
												?>
													">
													<?php
													if ( $settings['wbcom_product_style'] == 2 ) {
														woocommerce_template_loop_add_to_cart();
													} else {
														woocommerce_template_loop_add_to_cart();
													}
													?>
												</div>
											<?php endif; ?>

											
										</div>
										
										<div class="content">
											<h4 class="title"><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h4>
											<?php woocommerce_template_loop_price(); ?>
											<?php do_action( 'wbcom_addon_after_price' ); ?>
										</div>
									</div>

								</div>

									<?php if ( $k % $rows == 0 && ( $products->post_count != $k ) ) { ?>
							</div>
							<div class="<?php echo esc_attr( $proslider == 'yes' ? $collumval . ' swiper-slide' : $collumval ); ?>">
										<?php
									} ++$k;
								endwhile;
								wp_reset_query();
								wp_reset_postdata();
								endif;
							?>
							</div>
					<?php
					if ( $proslider == 'yes' ) {
						echo '</div>'; // close swiper-wrapper
						echo '<div class="swiper-pagination"></div>';
						echo '<div class="elementor-swiper-button elementor-swiper-button-prev"><i class="wbe-icons wbe-icon-angle-left"></i></div>';
						echo '<div class="elementor-swiper-button elementor-swiper-button-next"><i class="wbe-icons wbe-icon-angle-right"></i></div>';
						echo '</div>'; // close product-slider
					}
					?>
				</div>
			<?php endif; ?>

		</div>  


		<?php
	}
}
