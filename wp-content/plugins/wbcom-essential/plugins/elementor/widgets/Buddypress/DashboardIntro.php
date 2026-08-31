<?php
/**
 * Elementor widget dashboard intro.
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
use Elementor\Group_Control_Typography;

/**
 * Dashboard Intro.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */
class DashboardIntro extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'dashboard-intro', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/dashboard-intro.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-dashboard-intro';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Dashboard Intro', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-icon-box';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'dashboard-intro' );
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
		return array( 'dashboard', 'intro', 'welcome', 'user', 'buddypress' );
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content_content',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
			)
		);

		$this->add_responsive_control(
			'layout',
			array(
				'label'        => __( 'Position', 'wbcom-essential' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'  => array(
						'title' => __( 'Left', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'above' => array(
						'title' => __( 'Above', 'wbcom-essential' ),
						'icon'  => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'prefix_class' => 'elementor-cta-%s-dash-intro-',
			)
		);

		$this->add_control(
			'content_align',
			array(
				'label'        => esc_html__( 'Alignment', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::CHOOSE,
				'options'      => array(
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
				'default'      => 'left',
				'toggle'       => true,
				'prefix_class' => 'elementor-cta-dash-intro-content-',
			)
		);

		$this->add_control(
			'separator_content',
			array(
				'label'     => __( 'Description', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => __( 'Greeting & Description', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Welcome', 'wbcom-essential' ),
				'placeholder' => __( 'Enter greeting text', 'wbcom-essential' ),
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'description',
			array(
				'label'       => __( 'Description', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'to your Member Dashboard', 'wbcom-essential' ),
				'placeholder' => __( 'Enter your introductory text', 'wbcom-essential' ),
				'separator'   => 'none',
				'rows'        => 5,
				'show_label'  => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			array(
				'label' => esc_html__( 'Avatar', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'avatar_size',
			array(
				'label'     => __( 'Size', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 150,
				),
				'range'     => array(
					'px' => array(
						'min'  => 20,
						'max'  => 200,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-dash__avatar' => 'flex: 0 0 {{SIZE}}px;',
					'{{WRAPPER}} .wbcom-essential-dash__avatar img' => 'max-width: {{SIZE}}px; width: {{SIZE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'avatar_border',
				'label'       => __( 'Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .wbcom-essential-dash__avatar img',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'avatar_padding',
			array(
				'label'      => __( 'Padding', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'      => '3',
					'right'    => '3',
					'bottom'   => '3',
					'left'     => '3',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-dash__avatar img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'avatar_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( '%', 'px' ),
				'default'    => array(
					'top'    => '4',
					'right'  => '4',
					'bottom' => '4',
					'left'   => '4',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-dash__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'avatar_shadow',
				'label'    => __( 'Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-dash__avatar img',
			)
		);

		$this->add_control(
			'avatar_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'gap' => 15,
				),
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-dash>.flex'  => 'gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'greeting_color',
			array(
				'label'     => __( 'Greeting Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#122B46',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-dash__prior' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'info_color',
			array(
				'label'     => __( 'Description Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4D5C6D',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-dash__brief' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_greeting',
				'label'    => __( 'Typography Greeting', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-dash__prior .wbcom-essential-dash__intro',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_name',
				'label'    => __( 'Typography Name', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-dash__prior .wbcom-essential-dash__name',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_info',
				'label'    => __( 'Typography Description', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-dash__brief',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render dashboard intro widget.
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$current_user = wp_get_current_user();
		$display_name = function_exists( 'bp_core_get_user_displayname' ) ? bp_core_get_user_displayname( $current_user->ID ) : $current_user->display_name;

		// IF user not logged in then return and display nothing.
		if ( ! is_user_logged_in() ) {
			return;
		}
		?>

		<div class="wbcom-essential-dash">

			<div class="flex align-items-center">
				<div class="wbcom-essential-dash__avatar"><?php echo get_avatar( get_current_user_id() ); ?></div>
				<div class="wbcom-essential-dash__intro">
					<h2 class="wbcom-essential-dash__prior">
						<span class="wbcom-essential-dash__intro"><?php echo esc_html( $settings['heading'] ); ?></span>
						<span class="wbcom-essential-dash__name"><?php echo esc_html( $display_name ); ?></span>
					</h2>
					<div class="wbcom-essential-dash__brief"><?php echo esc_html( $settings['description'] ); ?></div>
				</div>
			</div>
		</div>
		<?php
	}
}
