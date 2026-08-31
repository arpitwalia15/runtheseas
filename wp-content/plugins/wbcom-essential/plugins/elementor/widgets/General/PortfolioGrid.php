<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor PortfolioGrid
 *
 * Elementor widget for PortfolioGrid
 *
 * @since 3.6.0
 */
class PortfolioGrid extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style( 'wb-portfolio-grid', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/portfolio-grid.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-portfolio-grid', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/portfolio-grid.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-portfolio-grid';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Portfolio Grid', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
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
		return array( 'portfolio', 'grid', 'gallery', 'projects', 'showcase' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-portfolio-grid' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-portfolio-grid', 'dashicons' );
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
		// section start
		$this->start_controls_section(
			'grid_items_section',
			array(
				'label' => esc_html__( 'Grid Items', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Image', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'info',
			array(
				'label'       => esc_html__( 'Info', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'website_link',
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

		$repeater->add_control(
			'item_filter_ids',
			array(
				'label'       => esc_html__( 'Filter ID(s)', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_responsive_control(
			'column_width',
			array(
				'label'     => esc_html__( 'Column Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'grid-column: span {{VALUE}};',
				),
			)
		);

		$repeater->add_responsive_control(
			'column_height',
			array(
				'label'     => esc_html__( 'Column Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'grid-row: span {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'list',
			array(
				'label'       => esc_html__( 'Portfolio Items', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'title'           => esc_html__( 'Title 1', 'wbcom-essential' ),
						'info'            => esc_html__( 'Lorem ipsum dolor...', 'wbcom-essential' ),
						'image'           => \Elementor\Utils::get_placeholder_image_src(),
						'item_filter_ids' => 'filter-1 filter-2',
					),
					array(
						'title'           => esc_html__( 'Title 2', 'wbcom-essential' ),
						'info'            => esc_html__( 'Lorem ipsum dolor...', 'wbcom-essential' ),
						'image'           => \Elementor\Utils::get_placeholder_image_src(),
						'item_filter_ids' => 'filter-2',
					),
					array(
						'title'           => esc_html__( 'Title 3', 'wbcom-essential' ),
						'info'            => esc_html__( 'Lorem ipsum dolor...', 'wbcom-essential' ),
						'image'           => \Elementor\Utils::get_placeholder_image_src(),
						'item_filter_ids' => 'filter-3',
					),
					array(
						'title'           => esc_html__( 'Title 4', 'wbcom-essential' ),
						'info'            => esc_html__( 'Lorem ipsum dolor...', 'wbcom-essential' ),
						'image'           => \Elementor\Utils::get_placeholder_image_src(),
						'item_filter_ids' => 'filter-2 filter-3',
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->add_control(
			'hr_img_size',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => esc_html__( 'Title HTML Tag', 'wbcom-essential' ),
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
				'default' => 'h5',
			)
		);

		$this->add_control(
			'info_html_tag',
			array(
				'label'   => esc_html__( 'Info HTML Tag', 'wbcom-essential' ),
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
				'default' => 'p',
			)
		);

		$this->add_control(
			'img_size',
			array(
				'label'   => esc_html__( 'Image Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'large',
				'options' => wba_get_image_sizes(),
			)
		);

		$this->add_control(
			'layout_default',
			array(
				'label'   => esc_html__( 'Default Layout', 'wbcom-essential' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'wba-fpg-grid-view' => array(
						'title' => esc_html__( 'Grid', 'wbcom-essential' ),
						'icon'  => 'eicon-posts-grid',
					),
					'wba-fpg-list-view' => array(
						'title' => esc_html__( 'List', 'wbcom-essential' ),
						'icon'  => 'eicon-post-list',
					),
				),
				'default' => 'wba-fpg-grid-view',
				'toggle'  => false,
			)
		);

		$this->add_control(
			'layout_menu',
			array(
				'label'        => esc_html__( 'Show Layout Switcher', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'show_label'   => true,
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'filters_section',
			array(
				'label' => esc_html__( 'Filters', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater2 = new \Elementor\Repeater();

		$repeater2->add_control(
			'filter_name',
			array(
				'label'       => esc_html__( 'Filter Name', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater2->add_control(
			'filter_id',
			array(
				'label'       => esc_html__( 'Filter ID', 'wbcom-essential' ),
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows A-Z 0-9  & underscore chars without spaces.', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater2->add_control(
			'filter_default',
			array(
				'label'        => esc_html__( 'Default Filter', 'wbcom-essential' ),
				'description'  => esc_html__( 'There must be a default filter in the menu.', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'list2',
			array(
				'label'       => esc_html__( 'Filters', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater2->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'filter_name'    => esc_html__( 'All', 'wbcom-essential' ),
						'filter_id'      => esc_html__( 'all', 'wbcom-essential' ),
						'filter_default' => 'yes',
					),
					array(
						'filter_name' => esc_html__( 'Filter 1', 'wbcom-essential' ),
						'filter_id'   => esc_html__( 'filter-1', 'wbcom-essential' ),
					),
					array(
						'filter_name' => esc_html__( 'Filter 2', 'wbcom-essential' ),
						'filter_id'   => esc_html__( 'filter-2', 'wbcom-essential' ),
					),
					array(
						'filter_name' => esc_html__( 'Filter 3', 'wbcom-essential' ),
						'filter_id'   => esc_html__( 'filter-3', 'wbcom-essential' ),
					),
				),
				'title_field' => '{{{ filter_name }}}',
			)
		);

		$this->add_control(
			'filter_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'filter_menu',
			array(
				'label'        => esc_html__( 'Show Menu', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'show_label'   => true,
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'grid_item_style',
			array(
				'label' => esc_html__( 'Grid Item', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'grid_item_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.1)',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-container li .wba-fpg-inner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'grid_item_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-container li .wba-fpg-inner',
			)
		);

		$this->add_control(
			'grid_item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container li .wba-fpg-inner' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'grid_item_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-container li .wba-fpg-inner',
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'grid_view_style',
			array(
				'label' => esc_html__( 'Grid View', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'grid_item_animation',
			array(
				'label' => esc_html__( 'Hover Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->add_control(
			'grid_item_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'grid_item_width',
			array(
				'label'      => esc_html__( 'Max. Item Width', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 100,
						'max'  => 1400,
						'step' => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-grid-view' => 'grid-template-columns:repeat(auto-fit, minmax({{SIZE}}{{UNIT}}, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'Grid Gap (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 30,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-grid-view' => 'grid-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grid_view_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'grid_txt_placement',
			array(
				'label'   => esc_html__( 'Text Box Placement', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'below-img',
				'options' => array(
					'in-img'    => esc_html__( 'On the image', 'wbcom-essential' ),
					'below-img' => esc_html__( 'Below the image', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'grid_view_img_height',
			array(
				'label'     => esc_html__( 'Grid Item Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 100,
				'max'       => 2000,
				'step'      => 10,
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-container.in-img.wba-fpg-grid-view li' => 'height: {{VALUE}}px;',
				),
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->add_responsive_control(
			'grid_view_v_align',
			array(
				'label'     => esc_html__( 'Text Box Vertical Align', 'wbcom-essential' ),
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
				'default'   => 'flex-start',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-grid-view figcaption' => 'justify-content: {{VALUE}};',
				),
				'toggle'    => false,
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->add_responsive_control(
			'grid_view_text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
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
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-grid-view figcaption' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'grid_view_txt_border',
				'label'     => esc_html__( 'Border', 'wbcom-essential' ),
				'selector'  => '{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view figcaption',
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->add_control(
			'grid_view_txt_border_radius',
			array(
				'label'      => esc_html__( 'Text Box Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view figcaption' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
				'condition'  => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->add_responsive_control(
			'grid_view_txt_padding',
			array(
				'label'      => esc_html__( 'Text Box Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view figcaption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_view_txt_margin',
			array(
				'label'      => esc_html__( 'Text Box Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view figcaption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->add_control(
			'grid_view_hr_2',
			array(
				'type'      => \Elementor\Controls_Manager::DIVIDER,
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->start_controls_tabs( 'tabs_overlay_style' );

		$this->start_controls_tab(
			'tab_overlay_normal',
			array(
				'label'     => esc_html__( 'Overlay', 'wbcom-essential' ),
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'grid_view_txt_bg',
				'label'     => esc_html__( 'Background', 'wbcom-essential' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view figcaption',
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_overlay_hover',
			array(
				'label'     => esc_html__( 'Overlay Hover', 'wbcom-essential' ),
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'grid_view_txt_bg_hover',
				'label'     => esc_html__( 'Background', 'wbcom-essential' ),
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view figcaption:hover',
				'condition' => array( 'grid_txt_placement' => 'in-img' ),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'grid_view_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'grid_view_title_typography',
				'label'    => esc_html__( 'Title Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view li .wba-fpg-title',
			)
		);

		$this->add_control(
			'grid_view_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view li .wba-fpg-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_view_title_margin',
			array(
				'label'      => esc_html__( 'Title Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view li .wba-fpg-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'grid_view_info_typography',

				'label'    => esc_html__( 'Info Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view li .wba-fpg-info',
			)
		);

		$this->add_control(
			'grid_view_info_color',
			array(
				'label'     => esc_html__( 'Info Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view li .wba-fpg-info' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_view_info_margin',
			array(
				'label'      => esc_html__( 'Info Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view li .wba-fpg-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grid_view_hr_4',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'grid_view_img_padding',
			array(
				'label'      => esc_html__( 'Image Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grid_view_img_border_radius',
			array(
				'label'      => esc_html__( 'Image Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-grid-view img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		 // section start
		$this->start_controls_section(
			'list_view_style',
			array(
				'label' => esc_html__( 'List View', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'list_view_img_width',
			array(
				'label'      => esc_html__( 'Image Width (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 200,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li figure' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;',
				),
			)
		);

		$this->add_responsive_control(
			'list_view_grid_gap',
			array(
				'label'      => esc_html__( 'Grid Gap (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li:last-child' => 'margin-bottom: 0;',
				),
			)
		);

		$this->add_control(
			'list_view_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'list_view_text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
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
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-list-view figcaption' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_view_txt_padding',
			array(
				'label'      => esc_html__( 'Text Box Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view figcaption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'list_view_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'list_view_title_typography',
				'label'    => esc_html__( 'Title Typography', 'wbcom-essential' ),

				'selector' => '{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li .wba-fpg-title',
			)
		);

		$this->add_control(
			'list_view_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li .wba-fpg-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_view_title_margin',
			array(
				'label'      => esc_html__( 'Title Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li .wba-fpg-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'list_view_info_typography',

				'label'    => esc_html__( 'Info Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li .wba-fpg-info',
			)
		);

		$this->add_control(
			'list_view_info_color',
			array(
				'label'     => esc_html__( 'Info Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li .wba-fpg-info' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_view_info_margin',
			array(
				'label'      => esc_html__( 'Info Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view li .wba-fpg-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'list_view_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'list_view_img_padding',
			array(
				'label'      => esc_html__( 'Image Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'list_view_img_border_radius',
			array(
				'label'      => esc_html__( 'Image Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-container.wba-fpg-list-view img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'toolbar_style',
			array(
				'label' => esc_html__( 'Toolbar', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'toolbar_bg',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'toolbar_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar',
			)
		);

		$this->add_control(
			'toolbar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'toolbar_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar',
			)
		);

		$this->add_control(
			'toolbar_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'toolbar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'toolbar_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'filters_style',
			array(
				'label'     => esc_html__( 'Filters', 'wbcom-essential' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array( 'filter_menu' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filters_typography',

				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label',
			)
		);

		$this->start_controls_tabs( 'tabs_filters_style' );

		$this->start_controls_tab(
			'tab_filters_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'filters_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filters_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.1)',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filters_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label',
			)
		);

		$this->add_control(
			'filters_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'filters_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_filters_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'filters_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li label.active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filters_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li label.active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'filters_border_hover',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label:hover,{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li label.active',
			)
		);

		$this->add_control(
			'filters_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li label.active' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'filters_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label:hover,{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li label.active',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'filters_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'filters_max_width',
			array(
				'label'      => esc_html__( 'Max. Label Width', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 100,
						'max'  => 900,
						'step' => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 200,
				),
				'selectors'  => array(
					'{{WRAPPER}} {{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label' => 'max-width:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon) label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filters_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li:not(.wba-fpg-mobile-icon)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'mobile_menu_style',
			array(
				'label'     => esc_html__( 'Mobile Menu Icon', 'wbcom-essential' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array( 'filter_menu' => 'yes' ),
			)
		);

		$this->add_control(
			'mobile_menu_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label .dashicons:before' => 'font-size:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};line-height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label .dashicons' => 'font-size:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};line-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_mobile_menu_style' );

		$this->start_controls_tab(
			'tab_mobile_menu_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'mobile_menu_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_menu_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'mobile_menu_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label',
			)
		);

		$this->add_control(
			'mobile_menu_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'mobile_menu_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile_menu_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'mobile_menu_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mobile_menu_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'mobile_menu_border_hover',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label:hover',
			)
		);

		$this->add_control(
			'mobile_menu_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'mobile_menu_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'mobile_menu_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'mobile_menu_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'mobile_menu_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-search-wrapper li.wba-fpg-mobile-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'layout_style',
			array(
				'label'     => esc_html__( 'Layout Menu', 'wbcom-essential' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array( 'layout_menu' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'layout_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size (px)', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label .dashicons:before' => 'font-size:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};line-height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label .dashicons' => 'font-size:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};line-height:{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_layout_style' );

		$this->start_controls_tab(
			'tab_layout_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'layout_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'layout_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.1)',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'layout_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label',
			)
		);

		$this->add_control(
			'layout_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'layout_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_layout_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'layout_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label.active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'layout_bg_color_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label.active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'layout_border_hover',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label:hover,{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label.active',
			)
		);

		$this->add_control(
			'layout_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label:hover' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label.active' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'layout_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label:hover,{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label.active',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'layout_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'layout_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'layout_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-fpg-toolbar .wba-fpg-view-options li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$settings_id = $this->get_id();
        if ( $settings['list2'] ) { ?>

       <form id="wba_fpg_form-<?php echo esc_attr($settings_id); ?>" name="wba_fpg_form-<?php echo esc_attr($settings_id); ?>" class="wba-fpg-toolbar">
           <?php if ($settings['filter_menu']) { ?>
            <ul class="wba-fpg-search-wrapper">
                <li class="wba-fpg-mobile-icon"><label><span class="dashicons dashicons-menu-alt"></span></label></li>
                <?php foreach ( $settings['list2'] as $item ) { ?>
                <li>
                    <input id="wba-fpg-filter-<?php echo esc_attr($settings_id); ?>-<?php echo esc_attr($item['filter_id']); ?>" type="radio" <?php if ($item['filter_default']) { ?>checked="checked"<?php } ?> name="filter" value="<?php echo esc_attr($item['filter_id']); ?>" style="display:none">
                    <label for="wba-fpg-filter-<?php echo esc_attr($settings_id); ?>-<?php echo esc_attr($item['filter_id']); ?>" <?php if ($item['filter_default']) { ?>class="active"<?php } ?>><?php echo esc_html($item['filter_name']); ?></label>
                </li>
                <?php } ?>
            </ul>
            <?php } ?>
            <?php if ($settings['layout_menu']) { ?>
            <ul class="wba-fpg-view-options">
              <li>
				<input id="wba-fpg-show-grid-<?php echo esc_attr($settings_id); ?>" type="radio" <?php if ($settings['layout_default'] == 'wba-fpg-grid-view') { ?>checked="checked"<?php } ?> name="view" value="show-grid" style="display:none">
                <label for="wba-fpg-show-grid-<?php echo esc_attr($settings_id); ?>" <?php if ($settings['layout_default'] == 'wba-fpg-grid-view') { ?>class="active"<?php } ?>><span class="dashicons dashicons-grid-view"></span></label>
              </li>
              <li>
                <input id="wba-fpg-show-list-<?php echo esc_attr($settings_id); ?>" type="radio" <?php if ($settings['layout_default'] == 'wba-fpg-list-view') { ?>checked="checked"<?php } ?> name="view" value="show-list" style="display:none">
                <label for="wba-fpg-show-list-<?php echo esc_attr($settings_id); ?>" <?php if ($settings['layout_default'] == 'wba-fpg-list-view') { ?>class="active"<?php } ?>><span class="dashicons dashicons-list-view"></span></label>
              </li>
            </ul>
            <?php } ?>
          </form>
        <?php } ?>

        <?php if ( $settings['list'] ) { ?>
          <ol class="wba-fpg-container <?php echo esc_attr($settings['layout_default'] . ' ' . $settings['grid_txt_placement']); ?> wba-fpg-zoom-in" style="display:none;">
          <?php foreach ( $settings['list'] as $item ) { ?>
            <li data-filter="<?php echo esc_attr($item['item_filter_ids']); ?>" class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>">
            <div class="wba-fpg-inner elementor-animation-<?php echo esc_attr($settings['grid_item_animation']); ?>">
            <?php
            $target = $item['website_link']['is_external'] ? ' target="_blank"' : '';
            $nofollow = $item['website_link']['nofollow'] ? ' rel="nofollow"' : '';
            if ($item['website_link']['url']) {
                echo '<a href="' . esc_url($item['website_link']['url']) . '"' . esc_attr( $target ) . esc_attr( $nofollow ) . '>';
            }
            ?>
              <figure>
                <?php
                $img_url = '';
                $img_alt = '';
                if ($item['image']['url'] && $item['image']['url'] != \Elementor\Utils::get_placeholder_image_src()) {
                    $img_array = wp_get_attachment_image_src($item['image']['id'], $settings['img_size'], true);
                    $img_url = $img_array[0];
                    $img_alt = get_post_meta( $item['image']['id'], '_wp_attachment_image_alt', true );
                } else if ($item['image']['url'] == \Elementor\Utils::get_placeholder_image_src()) {
                    $img_url = \Elementor\Utils::get_placeholder_image_src();
                }
                if (!empty($img_url)) {
                    echo '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($img_alt) . '" />';
                }
                ?>
                <figcaption>
                <?php
                if (!empty($item['title'])) {
                    echo '<' . esc_attr($settings['title_html_tag']) . ' class="wba-fpg-title">' . esc_html($item['title']) . '</' . esc_attr($settings['title_html_tag']) . '>';
                }
                if (!empty($item['info'])) {
                    echo '<' . esc_attr($settings['info_html_tag']) . ' class="wba-fpg-info">' . wp_kses_post(do_shortcode($item['info'])) . '</' . esc_attr($settings['info_html_tag']) . '>';
                }
                ?>
                </figcaption>
              </figure>
              <?php if ($item['website_link']['url']) { echo '</a>'; } ?>
            </div>
            </li>
          <?php } ?>
          </ol>
    <?php } 
	}
}
