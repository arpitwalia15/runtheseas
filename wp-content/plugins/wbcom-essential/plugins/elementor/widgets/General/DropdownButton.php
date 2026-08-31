<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Dropdown Button
 *
 * Elementor widget for Dropdown Button
 *
 * @since 3.6.0
 */
class DropdownButton extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-dropdown-button', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/dropdown-button.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-dropdown-button', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/dropdown-button.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-dropdown-button';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Dropdown Button', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-dual-button';
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
		return array( 'dropdown', 'button', 'menu', 'action', 'select' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-dropdown-button' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-dropdown-button' );
	}

	/**
	 * Get btn skins
	 */
	public function get_btn_skins() {
		$output_skins = apply_filters(
			'wbcom_btn_skins',
			array(
				''            => esc_html__( 'None', 'wbcom-essential' ),
				'wbcom-btn-1' => esc_html__( 'Animation 1', 'wbcom-essential' ),
				'wbcom-btn-2' => esc_html__( 'Animation 2', 'wbcom-essential' ),
				'wbcom-btn-3' => esc_html__( 'Animation 3', 'wbcom-essential' ),
				'wbcom-btn-4' => esc_html__( 'Animation 4', 'wbcom-essential' ),
				'wbcom-btn-5' => esc_html__( 'Animation 5', 'wbcom-essential' ),
				'wbcom-btn-6' => esc_html__( 'Animation 6', 'wbcom-essential' ),
				'wbcom-btn-7' => esc_html__( 'Animation 7', 'wbcom-essential' ),
				'wbcom-btn-8' => esc_html__( 'Animation 8', 'wbcom-essential' ),

			)
		);
		return $output_skins;
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
			'button_content',
			array(
				'label' => esc_html__( 'Button', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'text',
			array(
				'label'   => esc_html__( 'Button Text', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Click Here', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'size',
			array(
				'label'   => esc_html__( 'Button Size', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'wbcom-btn-md',
				'options' => array(
					'wbcom-btn-md' => esc_html__( 'Normal', 'wbcom-essential' ),
					'wbcom-btn-lg' => esc_html__( 'Large', 'wbcom-essential' ),
					'wbcom-btn-sm' => esc_html__( 'Small', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'h_align',
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
					'{{WRAPPER}} .wbcom-dropdown-wrapper' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => esc_html__( 'Button Text Align', 'wbcom-essential' ),
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
				'selectors' => array(
					'{{WRAPPER}} .wbcom-btn-wrapper' => 'text-align: {{VALUE}};',
				),
				'toggle'    => true,
			)
		);

		$this->add_responsive_control(
			'dropdown_align',
			array(
				'label'     => esc_html__( 'Dropdown Text Align', 'wbcom-essential' ),
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
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-menu li a' => 'text-align: {{VALUE}};',
				),
				'toggle'    => true,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Button Icon', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-caret-down',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => esc_html__( 'Button Icon Position', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'after',
				'options' => array(
					'after'  => esc_html__( 'After', 'wbcom-essential' ),
					'before' => esc_html__( 'Before', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'btn_id',
			array(
				'label'       => esc_html__( 'Button ID', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows A-Z 0-9  & underscore chars without spaces.', 'wbcom-essential' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'dropdown_content',
			array(
				'label' => esc_html__( 'Dropdown Items', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'title_icon_position',
			array(
				'label'   => esc_html__( 'Icon Position', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'before',
				'options' => array(
					'after'  => esc_html__( 'After', 'wbcom-essential' ),
					'before' => esc_html__( 'Before', 'wbcom-essential' ),
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
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

		$this->add_control(
			'list',
			array(
				'label'       => esc_html__( 'Menu Items', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'title_icon'          => '',
						'title_icon_position' => 'before',
						'title'               => esc_html__( 'Menu Item #1', 'wbcom-essential' ),
						'website_link'        => '',
					),
					array(
						'title_icon'          => '',
						'title_icon_position' => 'before',
						'title'               => esc_html__( 'Menu Item #2', 'wbcom-essential' ),
						'website_link'        => '',
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_btn_style',
			array(
				'label' => esc_html__( 'Button', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',

				'selector' => '{{WRAPPER}} .wbcom-dd-button',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-dd-button',
			)
		);

		$this->add_control(
			'skin',
			array(
				'label'   => esc_html__( 'Animation', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_btn_skins(),
			)
		);

		$this->add_control(
			'dropdown_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'bg_color_gradient',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-dd-button',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-dd-button',
			)
		);

		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-button' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-dd-button',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'text_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dropdown:hover .wbcom-dd-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dropdown:hover .wbcom-dd-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'bg_color_hover_gradient',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-dropdown:hover .wbcom-dd-button',
			)
		);

		$this->add_control(
			'animation_color',
			array(
				'label'     => esc_html__( 'Animation Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-button:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'hover_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-dropdown:hover .wbcom-dd-button',
			)
		);

		$this->add_responsive_control(
			'border_hover_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dropdown:hover .wbcom-dd-button' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'border_hover_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-dropdown:hover .wbcom-dd-button',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'dropdown_hr_4',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'icon_bg_color',
			array(
				'label'     => esc_html__( 'Icon Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-button i' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'      => esc_html__( 'Icon Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-button i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'      => esc_html__( 'Icon Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-button i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_width',
			array(
				'label'      => esc_html__( 'Button Width', 'wbcom-essential' ),
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
					'{{WRAPPER}} .wbcom-dd-button' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-dropdown'  => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_dropdown_style',
			array(
				'label' => esc_html__( 'Dropdown', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'dropdown_typography',

				'selector' => '{{WRAPPER}} .wbcom-dd-menu li a',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'dropdown_text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-dd-menu li a',
			)
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-menu' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'dropdown_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-dd-menu',
			)
		);

		$this->add_control(
			'dropdown_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->start_controls_tabs( 'tabs_dropdown_style' );

		$this->start_controls_tab(
			'tab_dropdown_normal',
			array(
				'label' => esc_html__( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'dropdown_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-menu li a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dropdown_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-menu li a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'seperator_color',
			array(
				'label'     => esc_html__( 'Separator Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-menu li' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_hover',
			array(
				'label' => esc_html__( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'dropdown_text_hover_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-menu li a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dropdown_bg_hover_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#cccccc',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-menu li a:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'seperator_hover_color',
			array(
				'label'     => esc_html__( 'Separator Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-dd-menu li:hover' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'dropdown_hr_5',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'dropdown_position',
			array(
				'label'   => esc_html__( 'Dropdown Position', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'wbcom-dd-menu-bottom',
				'options' => array(
					'wbcom-dd-menu-bottom' => esc_html__( 'Bottom', 'wbcom-essential' ),
					'wbcom-dd-menu-top'    => esc_html__( 'Top', 'wbcom-essential' ),
					'wbcom-dd-menu-right'  => esc_html__( 'Right', 'wbcom-essential' ),
					'wbcom-dd-menu-left'   => esc_html__( 'Left', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_width',
			array(
				'label'      => esc_html__( 'Dropdown Width', 'wbcom-essential' ),
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
					'{{WRAPPER}} .wbcom-dd-menu'      => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-dd-menu li'   => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-dd-menu li a' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'dropdown_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'dropdown_icon_spacing',
			array(
				'label'      => esc_html__( 'Icon Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-menu li a i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-menu li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-dd-menu' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings      = $this->get_settings_for_display();
		$icon_position = $settings['icon_position'];
		?>
		<div class="wbcom-btn-wrapper wbcom-dropdown-wrapper">
			<div class="wbcom-dropdown">
				<div tabindex="0" role="button" aria-haspopup="menu" class="wbcom-dd-button <?php echo esc_attr( $settings['size'] ); ?> <?php echo esc_attr( $settings['skin'] ); ?>">
					<?php
					if ( $icon_position == 'before' ) {
						\Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
						echo esc_html( $settings['text'] );
					} else {
						echo esc_html( $settings['text'] );
						\Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
					}
					?>
				</div>
				<?php if ( $settings['list'] ) { ?>
				<ul class="wbcom-dd-menu <?php echo esc_attr( $settings['dropdown_position'] ); ?>">
					<?php
					foreach ( $settings['list'] as $item ) {
						$target   = $item['website_link']['is_external'] ? ' target="' . esc_attr( '_blank' ) . '"' : '';
						$nofollow = $item['website_link']['nofollow'] ? ' rel="' . esc_attr( 'nofollow' ) . '"' : '';
						?>
					<li>
						<a href="<?php echo esc_url( $item['website_link']['url'] ); ?>" <?php echo esc_attr( $target ); ?> <?php echo esc_attr( $nofollow ); ?>>
							<?php
							if ( $item['title_icon_position'] == 'before' ) {
								\Elementor\Icons_Manager::render_icon( $item['title_icon'], array( 'aria-hidden' => 'true' ) );
								echo esc_html( $item['title'] );
							} else {
								echo esc_html( $item['title'] );
								\Elementor\Icons_Manager::render_icon( $item['title_icon'], array( 'aria-hidden' => 'true' ) );
							}
							?>
						</a>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>
			</div>
		</div>
		<?php
	}
}
