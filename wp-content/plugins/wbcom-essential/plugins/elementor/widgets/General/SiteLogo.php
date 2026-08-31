<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor SiteLogo
 *
 * Elementor widget for SiteLogo
 *
 * @since 3.6.0
 */
class SiteLogo extends \Elementor\Widget_Base {

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-site-logo';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Site Logo', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-site-logo';
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
		return array( 'site', 'logo', 'brand', 'image', 'identity' );
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
			'logo_section',
			array(
				'label' => esc_html__( 'Site Logo', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_source',
			array(
				'label'              => esc_html__( 'Source', 'wbcom-essential' ),
				'type'               => \Elementor\Controls_Manager::SELECT,
				'default'            => 'custom',
				'options'            => array(
					'custom'     => esc_html__( 'Custom Logo', 'wbcom-essential' ),
					'customizer' => esc_html__( 'Customizer', 'wbcom-essential' ),
				),
				'frontend_available' => true,
			)
		);

		$this->start_controls_tabs( 'tabs_thumbnail_style' );

		$this->start_controls_tab(
			'tab_desktop',
			array(
				'label'     => esc_html__( 'Desktop', 'wbcom-essential' ),
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'     => esc_html__( 'Logo', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'img_size',
			array(
				'label'     => esc_html__( 'Image Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'full',
				'options'   => wba_get_image_sizes(),
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_mobile',
			array(
				'label'     => esc_html__( 'Mobile', 'wbcom-essential' ),
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'after_image',
			array(
				'label'     => esc_html__( 'Logo', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'mobile_img_size',
			array(
				'label'     => esc_html__( 'Image Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'large',
				'options'   => wba_get_image_sizes(),
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'logo_hr_1',
			array(
				'type'      => \Elementor\Controls_Manager::DIVIDER,
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'         => esc_html__( 'Link', 'wbcom-essential' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https://your-link.com', 'wbcom-essential' ),
				'show_external' => true,
				'default'       => array(
					'url'         => '',
					'is_external' => true,
					'nofollow'    => true,
				),
				'dynamic'       => array(
					'active' => true,
				),
				'default'       => array(
					'url'         => home_url( '/' ),
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'     => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'logo_hr_2',
			array(
				'type'      => \Elementor\Controls_Manager::DIVIDER,
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'breakpoint',
			array(
				'label'     => esc_html__( 'Mobile Breakpoint', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => get_option( 'elementor_viewport_lg', true ),
				'condition' => array(
					'logo_source' => 'custom',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_logo_style',
			array(
				'label' => esc_html__( 'Site Logo', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_h_align',
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
					'{{WRAPPER}} .wba-site-logo-container' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_responsive_control(
			'logo_max_width',
			array(
				'label'      => esc_html__( 'Maximum Width', 'wbcom-essential' ),
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
					'{{WRAPPER}} .wba-site-logo-container img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
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
					'{{WRAPPER}} .wba-site-logo-container img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'logo_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'logo_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wba-site-logo-container img' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'logo_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-site-logo-container img',
			)
		);

		$this->add_control(
			'logo_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-site-logo-container img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'logo_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wba-site-logo-container img',
			)
		);

		$this->add_control(
			'logo_hr_4',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'logo_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-site-logo-container img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'logo_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wba-site-logo-container img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$target   = isset( $settings['link']['is_external'] ) ? ' target="_blank"' : '';
		$nofollow = isset( $settings['link']['nofollow'] ) ? ' rel="nofollow"' : '';
		?>
		<div class="wba-site-logo-container" style="display:flex;flex-direction:column;">
		<?php
		if ( $settings['logo_source'] == 'customizer' ) {
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<strong>' . esc_html__( 'Please add a logo from customizer.', 'wbcom-essential' ) . '</strong>';
			}
		} else {
			if ( $settings['link']['url'] ) {
				if ( $settings['before_image']['url'] ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image is safe
					echo '<a href="' . esc_url( $settings['link']['url'] ) . '" class="wba-logo-desktop"' . $target . ' ' . $nofollow . '><span>' . wp_get_attachment_image( $settings['before_image']['id'], $settings['img_size'] ) . '</span></a>';
				}
				if ( $settings['after_image']['url'] && $settings['breakpoint'] ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image is safe
					echo '<a href="' . esc_url( $settings['link']['url'] ) . '" class="wba-logo-mobile"' . $target . ' ' . $nofollow . '><span>' . wp_get_attachment_image( $settings['after_image']['id'], $settings['mobile_img_size'] ) . '</span></a>';
				}
			} else {
				if ( $settings['before_image']['url'] ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image is safe
					echo '<div class="wba-logo-desktop"><span>' . wp_get_attachment_image( $settings['before_image']['id'], $settings['img_size'] ) . '</span></div>';
				}
				if ( $settings['after_image']['url'] && $settings['breakpoint'] ) {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image is safe
					echo '<div class="wba-logo-mobile"><span>' . wp_get_attachment_image( $settings['after_image']['id'], $settings['mobile_img_size'] ) . '</span></div>';
				}
			}
		}
		?>
		</div>
		<?php if ( isset( $settings['after_image']['url'] ) && $settings['breakpoint'] ) { ?>
		<style>
		@media screen and (min-width: <?php echo esc_attr( $settings['breakpoint'] + 1 ) . 'px'; ?>) {
			.wba-logo-desktop span {display:block;}
			.wba-logo-mobile span {display:none;}
		}
		@media screen and (max-width: <?php echo esc_attr( $settings['breakpoint'] ) . 'px'; ?>) {
			.wba-logo-desktop span {display:none;}
			.wba-logo-mobile span {display:block;}
		}
		</style>
		<?php } ?>
		<?php
	}
}
