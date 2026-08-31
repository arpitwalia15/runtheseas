<?php
/**
 * Elementor forums activity widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\Buddypress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;
/**
 * Elementor forums activity widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */
class ForumsActivity extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'forums-activity', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/forums-activity.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-forums-activity';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Forums Activity', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-archive';
	}

	/**
	 * Get dependent style..
	 */
	public function get_style_depends() {
		return array( 'forums-activity' );
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
		return array( 'forums', 'activity', 'bbpress', 'recent', 'discussions' );
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			array(
				'label' => esc_html__( 'Layout', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'switch_forum_title',
			array(
				'label'   => esc_html__( 'Show Forum Title', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_meta',
			array(
				'label'   => esc_html__( 'Show Meta Data', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_excerpt',
			array(
				'label'   => esc_html__( 'Show Excerpt', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_excerpt_icon',
			array(
				'label'     => esc_html__( 'Show Reply Icon', 'wbcom-essential' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'switch_excerpt' => 'yes',
				),
			)
		);

		$this->add_control(
			'switch_link',
			array(
				'label'     => esc_html__( 'Show Link', 'wbcom-essential' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => __( 'Button Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'View Discussion', 'wbcom-essential' ),
				'placeholder' => __( 'Enter button text', 'wbcom-essential' ),
				'label_block' => true,
				'condition'   => array(
					'switch_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'switch_my_discussions',
			array(
				'label'     => esc_html__( 'My Discussions Button', 'wbcom-essential' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'my_discussions_button_text',
			array(
				'label'       => __( 'Button Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'View My Discussions', 'wbcom-essential' ),
				'placeholder' => __( 'Enter button text', 'wbcom-essential' ),
				'label_block' => true,
				'condition'   => array(
					'switch_my_discussions' => 'yes',
				),
			)
		);

		$this->add_control(
			'no_forums_paragraph_text',
			array(
				'label'       => __( 'No Forums Paragraph Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'You don\'t have any discussions yet.', 'wbcom-essential' ),
				'placeholder' => __( 'Enter no forums paragraph text', 'wbcom-essential' ),
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'no_forums_button_text',
			array(
				'label'       => __( 'No Forums Button Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Explore Forums', 'wbcom-essential' ),
				'placeholder' => __( 'Enter no forums button text', 'wbcom-essential' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => esc_html__( 'Box', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'box_border',
				'label'       => __( 'Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .wbcom-essential-forums-activity',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '4',
					'right'  => '4',
					'bottom' => '4',
					'left'   => '4',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'background_color',
				'label'    => __( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-forums-activity',
			)
		);

		$this->add_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '20',
					'right'  => '20',
					'bottom' => '20',
					'left'   => '20',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'separator_forum_title',
			array(
				'label'     => __( 'Forum Title', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'forum_title_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__forum-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'forum_title_typography',
				'label'    => __( 'Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-fa__forum-title',
			)
		);

		$this->add_control(
			'forum_title_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__forum-title' => 'margin-bottom: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'separator_topic_title',
			array(
				'label'     => __( 'Topic Title', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'topic_title_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__topic-title h2' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'topic_title_typography',
				'label'    => __( 'Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-fa__topic-title h2',
			)
		);

		$this->add_control(
			'topic_title_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__topic-title h2' => 'margin-bottom: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'separator_meta',
			array(
				'label'     => __( 'Meta Data', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__meta span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'label'    => __( 'Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-fa__meta span',
			)
		);

		$this->add_control(
			'meta_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 20,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__meta' => 'margin-bottom: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'separator_excerpt',
			array(
				'label'     => __( 'Excerpt', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_typography',
				'label'    => __( 'Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-fa__excerpt',
			)
		);

		$this->add_control(
			'excerpt_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 20,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__excerpt' => 'margin-bottom: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label'     => __( 'Button', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'switch_link' => 'yes',
				),
			)
		);

		$this->start_controls_tabs(
			'button_tabs'
		);

		$this->start_controls_tab(
			'button_normal_tab',
			array(
				'label' => __( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__link a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_bgr_color',
			array(
				'label'     => __( 'Background Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__link a' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'fa_button_border_color',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__link a' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover_tab',
			array(
				'label' => __( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'button_color_hover',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__link a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_bgr_color_hover',
			array(
				'label'     => __( 'Background Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__link a:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'fa_button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-fa__link a:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => __( 'Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-fa__link a',
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'        => __( 'Button Alignment', 'wbcom-essential' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'      => 'right',
				'prefix_class' => 'elementor-cta-%s-falign-',
			)
		);

		$this->add_control(
			'button_padding',
			array(
				'label'      => __( 'Button Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '4',
					'right'  => '20',
					'bottom' => '4',
					'left'   => '20',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-fa__link a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_border',
				'label'       => __( 'Button Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .wbcom-essential-fa__link a',
				'separator'   => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_my_discussions',
			array(
				'label'     => esc_html__( 'My Discussions Button', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'switch_my_discussions' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'my_alignment',
			array(
				'label'        => __( 'Button Alignment', 'wbcom-essential' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'      => 'right',
				'prefix_class' => 'elementor-cta-%s-fa-my-align-',
			)
		);

		$this->start_controls_tabs(
			'button_my_tabs'
		);

		$this->start_controls_tab(
			'button_my_normal_tab',
			array(
				'label' => __( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'button_my_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_my_bgr_color',
			array(
				'label'     => __( 'Background Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_fa_border_color',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_my_hover_tab',
			array(
				'label' => __( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'button_my_color_hover',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_my_bgr_color_hover',
			array(
				'label'     => __( 'Background Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_fa_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_my_typography',
				'label'    => __( 'Typography', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link',
			)
		);

		$this->add_control(
			'button_my_padding',
			array(
				'label'      => __( 'Button Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '2',
					'right'  => '15',
					'bottom' => '2',
					'left'   => '15',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_my_border',
				'label'       => __( 'Button Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .wbcom-essential-forums-activity-btn a.wbcom-essential-forums-activity-btn__link',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'button_my_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 50,
				),
				'range'     => array(
					'px' => array(
						'min'  => 30,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums-activity-btn' => 'top: -{{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Elementor forums activity widget.
	 *
	 * @return void
	 */
	protected function render() {
		// Check if BuddyPress and bbPress are active before rendering.
		if ( ! function_exists( 'buddypress' ) || ! class_exists( 'bbPress' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<p>' . esc_html__( 'BuddyPress and bbPress are required for this widget.', 'wbcom-essential' ) . '</p>';
			}
			return;
		}

		$settings            = $this->get_settings_for_display();
		$my_discussions_link = trailingslashit( bp_loggedin_user_domain() . bbp_maybe_get_root_slug() );
		?>
		<div class="wbcom-essential-forums-activity-wrapper <?php echo esc_attr( $settings['switch_my_discussions'] ? 'wbcom-essential-forums-activity-wrapper--ismy' : '' ); ?>">

			<?php if ( $settings['switch_my_discussions'] && is_user_logged_in() ) { ?>
				<div class="wbcom-essential-forums-activity-btn">
					<a class="wbcom-essential-forums-activity-btn__link" href="<?php echo esc_url( $my_discussions_link ); ?>"><?php echo esc_html( $settings['my_discussions_button_text'] ); ?><i class="eicon-angle-right"></i></a>
				</div>
			<?php } ?>

			<div class="wbcom-essential-forums-activity">

			<?php
			if ( is_user_logged_in() ) {
				$current_user_id = get_current_user_id();

				$query = bbp_has_topics(
					array(
						'author'         => $current_user_id,
						'orderby'        => 'date',
						'order'          => 'DESC',
						'posts_per_page' => 1,
					)
				);

				if ( $query ) {

					// Determine user to use.
					if ( bp_displayed_user_id() ) {
						$user_domain = bp_displayed_user_domain();
					} elseif ( bp_loggedin_user_domain() ) {
						$user_domain = bp_loggedin_user_domain();
					} else {
						return;
					}

					// User link.
					$my_discussion_link = trailingslashit( $user_domain . bbp_get_root_slug() );
					while ( bbp_topics() ) :
						bbp_the_topic();

						$forum_title                = bbp_get_forum_title( bbp_get_topic_forum_id() );
						$topic_title                = bbp_get_topic_title( bbp_get_topic_id() );
						$topic_reply_count          = bbp_get_topic_reply_count( bbp_get_topic_id() );
						$get_last_reply_id          = bbp_get_topic_last_reply_id();
						$get_last_reply_author_name = bbp_get_reply_author_display_name( $get_last_reply_id );
						$get_last_reply_since       = bbp_get_topic_last_active_time( bbp_get_topic_id() );
						$get_discussion_link        = bbp_get_topic_permalink( bbp_get_topic_id() );
						$get_last_reply_excerpt     = bbp_get_reply_excerpt( $get_last_reply_id, 50 );

						?>
						<div class="wbcom-essential-fa wbcom-essential-fa--item">

							<?php if ( $settings['switch_forum_title'] ) : ?>
								<div class="wbcom-essential-fa__forum-title"><?php echo esc_html( $forum_title ); ?></div>
							<?php endif; ?>
							<div class="wbcom-essential-fa__topic-title"><h2><?php echo esc_html( $topic_title ); ?></h2></div>
							<?php if ( $settings['switch_meta'] ) : ?>
								<div class="wbcom-essential-fa__meta">
									<span class="wbcom-essential-fa__meta-count">
										<?php
										echo esc_html( $topic_reply_count ) . ' ';
										echo esc_html( 1 !== $topic_reply_count ? __( 'replies', 'wbcom-essential' ) : __( 'reply', 'wbcom-essential' ) );
										?>
									</span>
									<span class="bs-separator">·</span>
									<span class="wbcom-essential-fa__meta-who"><?php echo esc_html( $get_last_reply_author_name ); ?> <?php esc_html_e( 'replied', 'wbcom-essential' ); ?> </span>
									<span class="wbcom-essential-fa__meta-when"><?php echo esc_html( $get_last_reply_since ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $settings['switch_excerpt'] ) : ?>
								<div class="wbcom-essential-fa__excerpt <?php echo esc_attr( ! empty( $get_last_reply_excerpt ) ? 'is-excerpt' : 'is-empty' ); ?> <?php echo esc_attr( $settings['switch_excerpt_icon'] ? 'is-link' : 'no-link' ); ?>">
									<?php
									if ( $settings['switch_excerpt_icon'] ) :
										$get_last_reply_id = bbp_get_topic_last_reply_id( bbp_get_topic_id() );
										if ( bbp_is_topic( $get_last_reply_id ) ) {
											add_filter( 'bbp_get_topic_reply_link', 'wbcom_essential_theme_elementor_topic_link_attribute_change', 9999, 3 );
											// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bbp_get_topic_reply_link() is a safe bbPress function that returns escaped HTML
											echo bbp_get_topic_reply_link(
												array(
													'id' => $get_last_reply_id,
													'reply_text' => '',
												)
											);

											remove_filter( 'bbp_get_topic_reply_link', 'wbcom_essential_theme_elementor_topic_link_attribute_change', 9999, 3 );
											// If post is a reply, print the reply admin links instead.
										} else {
											add_filter( 'bbp_get_reply_to_link', 'wbcom_essential_theme_elementor_reply_link_attribute_change', 9999, 3 );
											// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bbp_get_reply_to_link() is a safe bbPress function that returns escaped HTML
											echo bbp_get_reply_to_link(
												array(
													'id' => $get_last_reply_id,
													'reply_text' => '',
												)
											);
											remove_filter( 'bbp_get_reply_to_link', 'wbcom_essential_theme_elementor_reply_link_attribute_change', 9999, 3 );
										}
									endif;
									?>
									<?php echo esc_html( $get_last_reply_excerpt ); ?>
								</div>
							<?php endif; ?>
							<?php if ( $settings['switch_link'] ) : ?>
								<div class="wbcom-essential-fa__link"><a href="<?php echo esc_url( $get_discussion_link ); ?>"><?php echo esc_html( $settings['button_text'] ); ?></a></div>
							<?php endif; ?>

						</div>

						<?php

					endwhile;

				} else {
					?>
					<div class="wbcom-essential-no-data wbcom-essential-no-data--fa-activity">
						<img class="wbcom-essential-no-data__image" src="<?php echo esc_url( WBCOM_ESSENTIAL_ASSETS_URL ); ?>images/no-data-found.svg" alt="Forums Activity" />
						<br />
						<div class="wbcom-essential-no-data__msg"><?php echo esc_html( $settings['no_forums_paragraph_text'] ); ?></div>
						<?php if ( '' !== $settings['no_forums_button_text'] ) { ?>
							<a href="<?php echo esc_url( home_url( bbp_get_root_slug() ) ); ?>" class="wbcom-essential-no-data__link"><?php echo esc_html( $settings['no_forums_button_text'] ); ?></a>
						<?php } ?>
					</div>

					<?php
				}
			} else {
				?>
				<div class="wbcom-essential-no-data wbcom-essential-no-data--fa-activity">
					<img class="wbcom-essential-no-data__image" src="<?php echo esc_url( WBCOM_ESSENTIAL_ASSETS_URL ); ?>images/no-data-found.svg" alt="Forums Activity" />
					<br/>
					<div class="wbcom-essential-no-data__msg"><?php esc_html_e( 'You are not logged in.', 'wbcom-essential' ); ?></div>
					<?php if ( '' !== $settings['no_forums_button_text'] ) { ?>
						<a href="<?php echo esc_url( home_url( bbp_get_root_slug() ) ); ?>" class="wbcom-essential-no-data__link"><?php echo esc_html( $settings['no_forums_button_text'] ); ?></a>
					<?php } ?>
				</div>

			<?php } ?>

			</div>

		</div>

		<?php

	}

}
