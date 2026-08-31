<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor ProgressBar
 *
 * Elementor widget for ProgressBar
 *
 * @since 3.6.0
 */
class ProgressBar extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-progress-bar', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/progress-bar.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-progress-bar', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/progress-bar.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-progress-bar';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Progress Bar', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-skill-bar';
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
		return array( 'progress', 'bar', 'skill', 'percentage', 'stats' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-progress-bar' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-progress-bar' );
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
			'progress_bar_content',
			array(
				'label' => esc_html__( 'Progress Bar', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'WordPress', 'wbcom-essential' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'percent',
			array(
				'label'      => esc_html__( 'Percent', 'wbcom-essential' ),
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
					'size' => 60,
				),
				'dynamic'    => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'percent_select',
			array(
				'label'   => esc_html__( 'Display Percent', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'in',
				'options' => array(
					''    => esc_html__( 'No', 'wbcom-essential' ),
					'in'  => esc_html__( 'Display in the bar', 'wbcom-essential' ),
					'out' => esc_html__( 'Display out of the bar', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'stripes',
			array(
				'label'        => esc_html__( 'Animated Stripes', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'progress_bar_content_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'anim_duration',
			array(
				'label'   => esc_html__( 'Animation Duration (ms)', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 10000,
				'step'    => 100,
				'default' => 1000,
			)
		);

		$this->add_control(
			'scroll_anim_switcher',
			array(
				'label'        => esc_html__( 'Scroll Based Animation', 'wbcom-essential' ),
				'description'  => esc_html__( 'Activate animation when the pie chart scrolls into view.', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_progress_bar_style',
			array(
				'label' => esc_html__( 'Progress Bar', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'progress_bar_overflow',
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
			'progress_bar_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.1)',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-progress-bar-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'progress_bar_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'progress_bar_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-progress-bar-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'progress_bar_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-progress-bar-wrapper',
			)
		);

		$this->add_control(
			'progress_bar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar-wrapper' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'progress_bar_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'progress_bar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_bar_style',
			array(
				'label' => esc_html__( 'Bar', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3498db',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-progress-bar-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bar_height',
			array(
				'label'      => esc_html__( 'Height', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bar_valign',
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
				'selectors' => array(
					'{{WRAPPER}} .wbcom-progress-bar' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'bar_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'bar_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-progress-bar-overlay',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'bar_border_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-progress-bar-overlay',
			)
		);

		$this->add_control(
			'bar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar-overlay' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bar_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => esc_html__( 'Text', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_heading_3',
			array(
				'label'     => esc_html__( 'Title', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'none',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-progress-bar-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',

				'selector' => '{{WRAPPER}} .wbcom-progress-bar-title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-progress-bar-title',
			)
		);

		$this->add_control(
			'text_heading',
			array(
				'label'     => esc_html__( 'Percent', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'percent_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-progress-bar-percent' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-progress-bar-percent-out' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'percent_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-progress-bar-percent' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-progress-bar-percent:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-progress-bar-percent-out' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-progress-bar-percent-out:after' => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'percent_typography',

				'selector' => '{{WRAPPER}} .wbcom-progress-bar-percent,{{WRAPPER}} .wbcom-progress-bar-percent-out',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'percent_text_shadow',
				'label'    => esc_html__( 'Text Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-progress-bar-percent,{{WRAPPER}} .wbcom-progress-bar-percent-out',
			)
		);

		$this->add_responsive_control(
			'percent_horizontal_align',
			array(
				'label'      => esc_html__( 'Horizontal Align', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar-percent' => ' transform:translateX({{SIZE}}%);',
					'{{WRAPPER}} .wbcom-progress-bar-percent-out' => ' transform:translateX({{SIZE}}%);',
				),
			)
		);

		$this->add_control(
			'percent_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar-percent' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-progress-bar-percent-out' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'percent_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar-percent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-progress-bar-percent-out' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'percent_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-progress-bar-percent' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .wbcom-progress-bar-percent-out' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		?>
		<div id="wbcom-progress-bar-<?php echo esc_attr( $this->get_id() ); ?>" class="wbcom-progress-bar-wrapper" <?php if ( $settings['progress_bar_overflow'] ) { ?>
			style="overflow:hidden;"<?php } ?>>
			<div class="wbcom-progress-bar-overlay"></div>
			<div class="wbcom-progress-bar
			<?php if ( $settings['stripes'] ) { echo 'stripes'; } ?> " style="width:<?php echo esc_attr( $settings['percent']['size'] ); ?>%;" data-prct="<?php echo esc_attr( $settings['percent']['size'] ); ?>%" data-animduration="<?php echo esc_attr($settings['anim_duration']); ?>"  <?php if ( $settings['scroll_anim_switcher'] ) { ?> data-scrollanim<?php } ?>>
				<div class="wbcom-progress-bar-title"><?php echo esc_html( $settings['title'] ); ?></div>
				<?php if ( $settings['percent_select'] == 'in' ) { ?>
				<div class="wbcom-progress-bar-percent"><?php echo esc_html( $settings['percent']['size'] ); ?>%</div>
				<?php } ?>
			</div>
			<?php if ( $settings['percent_select'] == 'out' ) { ?>
				<div class="wbcom-progress-bar-percent-out"><?php echo esc_html( $settings['percent']['size'] ); ?>%</div>
			<?php } ?>
		</div>
		<?php
	}
}
