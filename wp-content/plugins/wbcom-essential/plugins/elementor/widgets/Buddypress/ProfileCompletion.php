<?php
/**
 * Elementor profile completion widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\Buddypress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
/**
 * Elementor profile completion widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */
class ProfileCompletion extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'profile-completion', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/profile-completion.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_script( 'profile-completion', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/profile-completion.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-profile-completion';
	}

	/**
	 * Get title.
	 */
	public function get_title() {
		return esc_html__( 'Profile Completion', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-check-circle';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'profile-completion' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'profile-completion' );
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
		return array( 'profile', 'completion', 'progress', 'user', 'buddypress' );
	}

	/**
	 * Register elementor profile completion widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			array(
				'label' => esc_html__( 'Layout', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'skin_style',
			array(
				'label'   => __( 'Skin', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => array(
					'circle' => __( 'Circle', 'wbcom-essential' ),
					'linear' => __( 'Linear', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'        => __( 'Alignment', 'wbcom-essential' ),
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
				'prefix_class' => 'elementor-cta-%s-completion-',
			)
		);

		/* Profile Groups and Profile Cover Photo VARS. */
		if ( function_exists( 'bp_core_profile_completion_steps_options' ) ) {
			$steps_options             = bp_core_profile_completion_steps_options();
			$profile_groups            = $steps_options['profile_groups'];
			$is_profile_photo_disabled = $steps_options['is_profile_photo_disabled'];
			$is_cover_photo_disabled   = $steps_options['is_cover_photo_disabled'];
		} else {
			if ( function_exists( 'bp_xprofile_get_groups' ) ) {
				$profile_groups = bp_xprofile_get_groups();
			} else {
				$profile_groups = array(); // Set an empty array if BuddyPress extended fields are disabled
			}
			$is_profile_photo_disabled = bp_disable_avatar_uploads();
			$is_cover_photo_disabled   = bp_disable_cover_image_uploads();
		}

		$photos_enabled_arr = array();
		$widget_enabled_arr = array();

		// Show Options only when Profile Photo and Cover option enabled in the Profile Settings.
		if ( ! $is_profile_photo_disabled ) {
			$photos_enabled_arr['profile_photo'] = __( 'Profile Photo', 'wbcom-essential' );
		}
		if ( ! $is_cover_photo_disabled ) {
			$photos_enabled_arr['cover_photo'] = __( 'Cover Photo', 'wbcom-essential' );
		}

		foreach ( $profile_groups as $single_group_details ) :

			$this->add_control(
				'profile_field_' . $single_group_details->id,
				array(
					'label'     => $single_group_details->name,
					'type'      => \Elementor\Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_on'  => __( 'Show', 'wbcom-essential' ),
					'label_off' => __( 'Hide', 'wbcom-essential' ),
				)
			);

		endforeach;

		foreach ( $photos_enabled_arr as $photos_value => $photos_label ) :

			$this->add_control(
				sanitize_title( $photos_value ),
				array(
					'label'     => $photos_label,
					'type'      => \Elementor\Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'label_on'  => __( 'Show', 'wbcom-essential' ),
					'label_off' => __( 'Hide', 'wbcom-essential' ),
				)
			);

		endforeach;

		$this->add_control(
			'switch_hide_widget',
			array(
				'label'       => esc_html__( 'Hide Widget', 'wbcom-essential' ),
				'description' => esc_html__( 'Hide widget once progress hits 100%', 'wbcom-essential' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'no',
			)
		);

		$this->add_control(
			'switch_profile_btn',
			array(
				'label'   => esc_html__( 'Profile Complete Button', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'heading_text',
			array(
				'label'       => __( 'Heading Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Complete your profile', 'wbcom-essential' ),
				'placeholder' => __( 'Enter heading text', 'wbcom-essential' ),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => array(
					'skin_style' => 'linear',
				),
			)
		);

		$this->add_control(
			'completion_text',
			array(
				'label'       => __( 'Completion Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Complete', 'wbcom-essential' ),
				'placeholder' => __( 'Enter completion text', 'wbcom-essential' ),
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'completion_button_text',
			array(
				'label'       => __( 'Complete Profile Button Text', 'wbcom-essential' ),
				'description' => esc_html__( 'Button text if progress is less than 100%', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Complete Profile', 'wbcom-essential' ),
				'placeholder' => __( 'Enter button text', 'wbcom-essential' ),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => array(
					'switch_profile_btn' => 'yes',
				),
			)
		);

		$this->add_control(
			'edit_button_text',
			array(
				'label'       => __( 'Edit Profile Button Text', 'wbcom-essential' ),
				'description' => esc_html__( 'Button text once progress hits 100%', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Edit Profile', 'wbcom-essential' ),
				'placeholder' => __( 'Enter button text', 'wbcom-essential' ),
				'label_block' => true,
				'separator'   => 'before',
				'condition'   => array(
					'switch_profile_btn' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box',
			array(
				'label'     => esc_html__( 'Box', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'skin_style' => 'linear',
				),
			)
		);

		$this->add_control(
			'box_width',
			array(
				'label'     => __( 'Width', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 100,
				),
				'range'     => array(
					'%' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .profile_bit.skin-linear' => 'width: {{SIZE}}%;',
				),
			)
		);

		$this->add_control(
			'box_bgr_color',
			array(
				'label'     => __( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} .profile_bit.skin-linear .progress_container'   => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'box_border_style',
			array(
				'label'   => __( 'Border Type', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => array(
					'solid'  => __( 'Solid', 'wbcom-essential' ),
					'dashed' => __( 'Dashed', 'wbcom-essential' ),
					'dotted' => __( 'Dotted', 'wbcom-essential' ),
					'double' => __( 'Double', 'wbcom-essential' ),
					'none'   => __( 'None', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'box_border_width',
			array(
				'label'     => __( 'Border Width', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .profile_bit.skin-linear:not(.active) .progress_container' => 'border-width: {{SIZE}}px;',
					'{{WRAPPER}} .profile_bit.skin-linear.active .progress_container'       => 'border-top-width: {{SIZE}}px;border-left-width: {{SIZE}}px;border-right-width: {{SIZE}}px;',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details'            => 'border-bottom-width: {{SIZE}}px;border-left-width: {{SIZE}}px;border-right-width: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit.skin-linear .progress_container'   => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .profile_bit.skin-linear:not(.active) .progress_container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .profile_bit.skin-linear.active .progress_container'       => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
					'{{WRAPPER}} .profile_bit.skin-linear .profile_bit__details'            => 'border-radius: 0 0 {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_progress',
			array(
				'label' => esc_html__( 'Progress Graph', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'progress_spacing',
			array(
				'label'      => __( 'Spacing', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '0',
					'right'  => '10',
					'bottom' => '0',
					'left'   => '10',
				),
				'selectors'  => array(
					'{{WRAPPER}} .profile_bit.skin-circle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .profile_bit.skin-linear' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'progress_active_width',
			array(
				'label'     => __( 'Progress Graph Border Width', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 6,
				),
				'range'     => array(
					'px' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
				),
				'selectors' => array(
					/**
					* '{{WRAPPER}} .progress_bit_graph:not(.progress_bit_graph--sm) .progress-bit__ring .progress-bit__disc' => 'border-width: {{SIZE}}px;',
					*/
					/**
					* '{{WRAPPER}} .progress_bit_graph:not(.progress_bit_graph--sm) .progress-bit__ring:after' => 'border-width: {{SIZE}}px;',
					*/
					'{{WRAPPER}} .progress_bit_linear .progress_bit__line'  => 'height: {{SIZE}}px;',
					'{{WRAPPER}} .progress_bit_linear .progress_bit__scale' => 'height: {{SIZE}}px;',
				),
				'condition' => array(
					'skin_style' => 'linear',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'typography_heading',
				'label'     => __( 'Typography Heading', 'wbcom-essential' ),
				'selector'  => '{{WRAPPER}} .progress_bit__heading h3',
				'condition' => array(
					'skin_style' => 'linear',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'typography_progress_value',
				'label'     => __( 'Typography Progress Value', 'wbcom-essential' ),
				'selector'  => '{{WRAPPER}} .progress_bit__data-num',
				'condition' => array(
					'skin_style' => 'circle',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'typography_progress_info',
				'label'     => __( 'Typography Progress Info', 'wbcom-essential' ),
				'selector'  => '{{WRAPPER}} .progress_bit__data-remark, {{WRAPPER}} .progress_bit__data-num > span',
				'condition' => array(
					'skin_style' => 'circle',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'typography_progress_data',
				'label'     => __( 'Typography Progress Data', 'wbcom-essential' ),
				'selector'  => '{{WRAPPER}} .progress_bit__data-remark, {{WRAPPER}} .progress_bit__data-num > span, {{WRAPPER}} .progress_bit__data-num',
				'condition' => array(
					'skin_style' => 'linear',
				),
			)
		);

		$this->add_control(
			'details_color_linear',
			array(
				'label'     => __( 'Details Completion Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .progress_bit__data-remark' => 'color: {{VALUE}};',
					'{{WRAPPER}} .progress_bit__data-num > span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .progress_bit__data-num' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin_style' => 'linear',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_colors',
			array(
				'label' => esc_html__( 'Colors', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'details_color',
			array(
				'label'     => __( 'Details Completion Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} .profile_bit__details' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'skin_style' => 'circle',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => __( 'Heading Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .progress_bit_linear .progress_bit__heading h3'             => 'color: {{VALUE}};',
					'{{WRAPPER}} .skin-linear .progress_bit_linear .progress_bit__heading i' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'skin_style' => 'linear',
				),
			)
		);

		$this->add_control(
			'completion_color',
			array(
				'label'     => __( 'Completion Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1CD991',
				'selectors' => array(
					'{{WRAPPER}} .progress-bit__ring .progress-bit__disc' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li.completed .section_number:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li.completed .completed_staus' => 'border-color: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .progress_bit__scale' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'incomplete_color',
			array(
				'label'     => __( 'Incomplete Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EF3E46',
				'selectors' => array(
					'{{WRAPPER}} ul.profile_bit__list li.incomplete .section_name a'  => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li.incomplete .completed_staus' => 'border-color: {{VALUE}}; color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ring_border_color',
			array(
				'label'     => __( 'Progress Border Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DEDFE2',
				'selectors' => array(
					'{{WRAPPER}} .progress-bit__ring:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} ul.profile_bit__list li .section_number:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .progress_bit__line' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ring_num_color',
			array(
				'label'     => __( 'Progress Number Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .progress_bit__data-num' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ring_text_color',
			array(
				'label'     => __( 'Progress Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .progress_bit__data-remark' => 'color: {{VALUE}};',
					'{{WRAPPER}} .progress_bit__data-num > span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_details',
			array(
				'label' => esc_html__( 'Details Dropdown', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'switch_heading',
			array(
				'label'     => esc_html__( 'Show Header', 'wbcom-essential' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'skin_style' => 'circle',
				),
			)
		);

		$this->add_control(
			'switch_completion_icon',
			array(
				'label'   => esc_html__( 'Show Completion Icon', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_completion_status',
			array(
				'label'   => esc_html__( 'Show Completion Status', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'details_box_shadow',
				'label'    => __( 'Details Container Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .profile_bit__details',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'details_typography',
				'label'    => __( 'Typography Progress Value', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} ul.profile_bit__list li .section_name a, {{WRAPPER}} .profile_bit__heading',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label'     => esc_html__( 'Button', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'switch_profile_btn' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

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
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_bgr_color',
			array(
				'label'     => __( 'Background Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'la_button_border_color',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_bgr_color_hover',
			array(
				'label'     => __( 'Background Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'la_button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link:hover' => 'border-color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .profile_bit_action a.profile_bit_action__link',
			)
		);

		$this->add_control(
			'button_padding',
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
					'{{WRAPPER}} .profile_bit_action a.profile_bit_action__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'    => '{{WRAPPER}} .profile_bit_action a.profile_bit_action__link',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'button_spacing',
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
					'{{WRAPPER}} .profile_bit_action' => 'margin-top: {{SIZE}}px;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render elementor profile completion widget.
	 *
	 * @return void
	 */
	protected function render() {
		$settings        = $this->get_settings_for_display();
		$settings_skin   = $settings['skin_style'];
		$selected_groups = array();
		foreach ( $settings as $k => $v ) {
			if ( strpos( $k, 'profile_field_' ) !== false && '' !== $v ) {
				$id                = explode( 'profile_field_', $k );
				$selected_groups[] = $id[1];
			}
		}

		$profile_phototype_selected = array();

		if ( isset( $settings['profile_photo'] ) && 'yes' === $settings['profile_photo'] ) {
			$profile_phototype_selected[] = 'profile_photo';
		}

		if ( isset( $settings['cover_photo'] ) && 'yes' === $settings['cover_photo'] ) {
			$profile_phototype_selected[] = 'cover_photo';
		}

		// IF nothing selected then return and nothing to display.
		if ( ( empty( $selected_groups ) && empty( $profile_phototype_selected ) ) || ! is_user_logged_in() ) {
			return;
		}

		$profile_percent = $this->profile_calculate_profile_percentage( get_current_user_id(), $settings );

		if ( ! empty( $profile_percent ) ) {

			$settings_options                       = array();
			$settings_options['profile_groups']     = $selected_groups;
			$settings_options['profile_photo_type'] = $profile_phototype_selected;
			$user_progress                          = $profile_percent;
			$progress_label = $user_progress['completion_percentage'];

			if ( ( 'yes' === $settings['switch_hide_widget'] || ! isset( $settings['switch_hide_widget'] ) ) && ( 100 === $user_progress['completion_percentage'] ) ) { ?>
				<div class="profile_bit_wrapper profile_bit_wrapper--blank"></div>
				<?php
			} else {
				?>
				<div class="profile_bit_wrapper <?php echo esc_attr( $settings['switch_profile_btn'] ? 'has-profile-button' : '' ); ?> ">
					<div class="profile_bit_figure">
						<div class="profile_bit <?php echo esc_attr( 'skin-' . $settings_skin ); ?> border-<?php echo esc_attr( $settings['box_border_style'] ); ?>">
							<div class="progress_container">
								<div class="progress_bit">
									<div class="progress_bit_graph">
										<div class="progress-bit__ring <?php echo ( 100 === $user_progress['completion_percentage'] ) ? 'wbcom-essential-completed' : 'wbcom-essential-not-completed'; ?>" data-percentage="<?php echo esc_attr( $user_progress['completion_percentage'] ); ?>">
											<span class="progress-bit__left"><span class="progress-bit__disc"></span></span>
											<span class="progress-bit__right"><span class="progress-bit__disc"></span></span>
										</div>
									</div>
									<div class="progress_bit_linear">
										<div class="progress_bit__heading">
											<h3><?php echo esc_html( $settings['heading_text'] ); ?></h3>
											<i class="eicon-chevron-right"></i></div>
										<div class="progress_bit__line <?php echo ( 100 === $user_progress['completion_percentage'] ) ? 'wbcom-essential-completed' : 'wbcom-essential-not-completed'; ?>">
											<div class="progress_bit__scale" style="width: <?php echo esc_attr( $user_progress['completion_percentage'] ); ?>%"></div>
										</div>
									</div>
									<div class="progress_bit__data">
										<span class="progress_bit__data-num"><?php echo esc_html( $progress_label ); ?><span><?php esc_html_e( '%', 'wbcom-essential' ); ?></span></span>
										<span class="progress_bit__data-remark"><?php echo esc_html( $settings['completion_text'] ); ?></span>
									</div>
								</div>
								<?php if ( $settings['switch_profile_btn'] && 'linear' === $settings['skin_style'] ) { ?>
									<div class="profile_bit_action">
										<a class="profile_bit_action__link" href="<?php echo esc_url( bp_loggedin_user_domain() . 'profile/edit/' ); ?>"><?php echo esc_html( ( 100 === $user_progress['completion_percentage'] ) ? $settings['edit_button_text'] : $settings['completion_button_text'] ); ?>
											<i class="eicon-chevron-right"></i></a>
									</div>
								<?php } ?>
							</div>
							<div class="profile_bit__details">
								<?php if ( $settings['switch_heading'] ) : ?>
									<div class="profile_bit__heading">
										<span class="progress-num"><?php echo esc_html( $progress_label ); ?><span><?php esc_html_e( '%', 'wbcom-essential' ); ?></span></span>
										<span class="progress-figure">
										<div class="progress_bit_graph progress_bit_graph--sm">
											<div class="progress-bit__ring <?php echo ( 100 === $user_progress['completion_percentage'] ) ? 'wbcom-essential-completed' : 'wbcom-essential-not-completed'; ?>" data-percentage="<?php echo esc_attr( $user_progress['completion_percentage'] ); ?>">
												<span class="progress-bit__left"><span class="progress-bit__disc"></span></span>
												<span class="progress-bit__right"><span class="progress-bit__disc"></span></span>
											</div>
										</div>
									</span>
										<span class="progress-label"><?php echo esc_html( $settings['completion_text'] ); ?></span>
									</div>
								<?php endif; ?>
								<ul class="profile_bit__list">
									<?php
									// Loop through all sections and show progress.
									foreach ( $user_progress['groups'] as $single_section_details ) :
										$user_progress_status = ( 0 === $single_section_details['completed'] && $single_section_details['total'] > 0 ) ? 'progress_not_started' : '';
										?>
										<li class="single_section_wrap <?php echo esc_attr( $single_section_details['is_group_completed'] ) ? esc_attr( 'completed ' ) : esc_attr( 'incomplete ' ); ?> <?php echo esc_attr( $user_progress_status ); ?>">
											<?php if ( $settings['switch_completion_icon'] ) : ?>
												<span class="section_number"></span>
											<?php endif; ?>
											<span class="section_name">
											<a href="<?php echo esc_url( $single_section_details['link'] ); ?>" class="group_link"><?php echo esc_html( $single_section_details['label'] ); ?></a>
										</span>
											<?php if ( $settings['switch_completion_status'] ) : ?>
												<span class="progress">
												<span class="completed_staus">
													<span class="completed_steps"><?php echo absint( $single_section_details['completed'] ); ?></span>/<span class="total_steps"><?php echo absint( $single_section_details['total'] ); ?></span>
												</span>
											</span>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
						<?php if ( $settings['switch_profile_btn'] && 'circle' === $settings['skin_style'] ) { ?>
							<div class="profile_bit_action">
								<a class="profile_bit_action__link" href="<?php echo esc_url( bp_loggedin_user_domain() . 'profile/edit/' ); ?>"><?php echo esc_html( ( 100 === $user_progress['completion_percentage'] ) ? $settings['edit_button_text'] : $settings['completion_button_text'] ); ?>
									<i class="eicon-chevron-right"></i></a>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php
			}
		}

	}

	/**
	 * Calculate the profile percentage.
	 *
	 * @param  int   $user_id Get User ID.
	 * @param  array $settings Get Widget Setting.
	 */
	public function profile_calculate_profile_percentage( $user_id, $settings ) {

		/* User Progress specific VARS. */
		$progress_details       = array();
		$grand_total_fields     = 0;
		$grand_completed_fields = 0;
		$total_fields           = 0;
		$completed_fields       = 0;
		// Profile Photo.
		$is_profile_photo_disabled = bp_disable_avatar_uploads();
		if ( ! $is_profile_photo_disabled && isset( $settings['profile_photo'] ) && 'yes' === $settings['profile_photo'] ) {

			++$grand_total_fields;

			remove_filter( 'bp_core_avatar_default', 'reign_alter_bp_core_avatar_default', 10, 2 );
			remove_filter( 'bp_core_default_avatar_user', 'reign_alter_bp_core_default_avatar_user', 10, 2 );

			$is_profile_photo_uploaded = ( bp_get_user_has_avatar( $user_id ) ) ? 1 : 0;

			if ( $is_profile_photo_uploaded ) {
				++$grand_completed_fields;
			}

			$progress_details['photo_type']['profile_photo'] = array(
				'is_uploaded' => $is_profile_photo_uploaded,
				'name'        => __( 'Profile Photo', 'wbcom-essential' ),
			);
		}

		/*
		 * Cover Photo
		 */

		$is_cover_photo_disabled = bp_disable_cover_image_uploads();
		if ( ! $is_cover_photo_disabled && isset( $settings['cover_photo'] ) && 'yes' === $settings['cover_photo'] ) {

			++$grand_total_fields;

			$is_cover_photo_uploaded = ( bp_attachments_get_user_has_cover_image( $user_id ) ) ? 1 : 0;

			if ( $is_cover_photo_uploaded ) {
				++$grand_completed_fields;
			}

			$progress_details['photo_type']['cover_photo'] = array(
				'is_uploaded' => $is_cover_photo_uploaded,
				'name'        => __( 'Cover Photo', 'wbcom-essential' ),
			);
		}

		/*
		 * Groups Fields
		 */
		$profile_groups = array();
		if ( function_exists( 'bp_xprofile_get_groups' ) ) {
			$profile_groups = bp_xprofile_get_groups(
				array(
					'fetch_fields'     => true,
					'fetch_field_data' => true,
					'user_id'          => $user_id,
				)
			);
		}

		if ( ! empty( $profile_groups ) ) {

			foreach ( $profile_groups as $single_group_details ) {

				if ( empty( $single_group_details->fields ) ) {
					continue;
				}

				/* Single Group Specific VARS */
				$group_id              = $single_group_details->id;
				$single_group_progress = array();

				/*
				 * Consider only selected Groups ids from the widget form settings, skip all others.
				 */
				if ( isset( $settings[ 'profile_field_' . $group_id ] ) && 'yes' !== $settings[ 'profile_field_' . $group_id ] ) {
					continue;
				}

				// Check if Current Group is repeater if YES then get number of fields inside current group.
				$is_group_repeater_str = bp_xprofile_get_meta( $group_id, 'group', 'is_repeater_enabled', true );
				$is_group_repeater     = ( 'on' === $is_group_repeater_str ) ? true : false;

				/* Loop through all the fields and check if fields completed or not. */
				$group_total_fields     = 0;
				$group_completed_fields = 0;
				foreach ( $single_group_details->fields as $group_single_field ) {
					/*
					 * If current group is repeater then only consider first set of fields.
					 */
					if ( $is_group_repeater ) {
						/*
						 * If field not a "clone number 1" then stop. That means proceed with the first set of fields and restrict others.
						 */
						$field_id     = $group_single_field->id;
						$clone_number = bp_xprofile_get_meta( $field_id, 'field', '_clone_number', true );
						if ( $clone_number > 1 ) {
							continue;
						}
					}

					$field_data_value = maybe_unserialize( $group_single_field->data->value );

					if ( ! empty( $field_data_value ) ) {
						++$group_completed_fields;
					}

					++$group_total_fields;
				}

				/*
				 * Prepare array to return group specific progress details
				 */
				$single_group_progress['group_name']             = $single_group_details->name;
				$single_group_progress['group_total_fields']     = $group_total_fields;
				$single_group_progress['group_completed_fields'] = $group_completed_fields;

				$grand_total_fields     += $group_total_fields;
				$grand_completed_fields += $group_completed_fields;

				$total_fields     += $group_total_fields;
				$completed_fields += $group_completed_fields;

				// $progress_details[ 'groups' ][ $group_id ] = $single_group_progress;
			}
		}

		$progress_details['groups'][] = array(
			'group_name'             => __( 'Profile Fields', 'wbcom-essential' ),
			'group_total_fields'     => $total_fields,
			'group_completed_fields' => $completed_fields,
		);

		/*
		 * Total Fields vs completed fields to calculate progress percentage.
		 */
		$progress_details['total_fields']     = $grand_total_fields;
		$progress_details['completed_fields'] = $grand_completed_fields;

		$user_progress_formmatted = array();
		$user_progress_formmatted = $this->get_user_profile_progress_formatted( $progress_details, $settings );
		return $user_progress_formmatted;
	}

	/**
	 * Get User profile progress formatted.
	 *
	 * @param  array $user_progress_arr User progress attributes.
	 * @param  array $settings  Get Widget Setting.
	 */
	public function get_user_profile_progress_formatted( $user_progress_arr, $settings ) {

		/* Groups */

		$loggedin_user_domain = bp_loggedin_user_domain();
		$profile_slug         = bp_get_profile_slug();

		/*
		 * Calculate Total Progress percentage.
		 */
		if ( $user_progress_arr['total_fields'] > 0 ) {
			$profile_completion_percentage = round( ( $user_progress_arr['completed_fields'] * 100 ) / $user_progress_arr['total_fields'] );
			$user_prgress_formatted        = array(
				'completion_percentage' => $profile_completion_percentage,
			);
		}

		/*
		 * Group specific details
		 */
		$listing_number = 1;
		foreach ( $user_progress_arr['groups'] as $group_id => $group_details ) {

			$group_link = trailingslashit( $loggedin_user_domain . $profile_slug . '/edit/group/' . $group_id );

			$user_prgress_formatted['groups'][] = array(
				'number'             => $listing_number,
				'label'              => $group_details['group_name'],
				'link'               => $group_link,
				'is_group_completed' => ( $group_details['group_total_fields'] === $group_details['group_completed_fields'] ) ? true : false,
				'total'              => $group_details['group_total_fields'],
				'completed'          => $group_details['group_completed_fields'],
			);

			$listing_number ++;
		}

		/* Profile Photo */

		if ( isset( $user_progress_arr['photo_type']['profile_photo'] ) ) {

			$change_avatar_link  = trailingslashit( $loggedin_user_domain . $profile_slug . '/change-avatar' );
			$is_profile_uploaded = ( 1 === $user_progress_arr['photo_type']['profile_photo']['is_uploaded'] );

			$user_prgress_formatted['groups'][] = array(
				'number'             => $listing_number,
				'label'              => $user_progress_arr['photo_type']['profile_photo']['name'],
				'link'               => $change_avatar_link,
				'is_group_completed' => ( $is_profile_uploaded ) ? true : false,
				'total'              => 1,
				'completed'          => ( $is_profile_uploaded ) ? 1 : 0,
			);

			$listing_number ++;
		}

		/* Cover Photo */

		if ( isset( $user_progress_arr['photo_type']['cover_photo'] ) ) {

			$change_cover_link = trailingslashit( $loggedin_user_domain . $profile_slug . '/change-cover-image' );
			$is_cover_uploaded = ( 1 === $user_progress_arr['photo_type']['cover_photo']['is_uploaded'] );

			$user_prgress_formatted['groups'][] = array(
				'number'             => $listing_number,
				'label'              => $user_progress_arr['photo_type']['cover_photo']['name'],
				'link'               => $change_cover_link,
				'is_group_completed' => ( $is_cover_uploaded ) ? true : false,
				'total'              => 1,
				'completed'          => ( $is_cover_uploaded ) ? 1 : 0,
			);

			$listing_number ++;
		}

		/**
		 * Filter returns User Progress array in the template friendly format.
		 */
		return apply_filters( 'wbcom_essential_user_progress_formatted', $user_prgress_formatted );
	}

}
