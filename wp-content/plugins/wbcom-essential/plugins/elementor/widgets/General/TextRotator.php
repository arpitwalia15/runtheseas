<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor TextRotator
 *
 * Elementor widget for TextRotator
 *
 * @since 3.6.0
 */
class TextRotator extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wbcom-animations', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/animations.min.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wb-text-rotator', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/text-rotator.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-text-rotator', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/text-rotator.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-text-rotator';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Text Rotator', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-animation-text';
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
		return array( 'text', 'rotator', 'animation', 'headline', 'typing' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wbcom-animations', 'wb-text-rotator' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-text-rotator' );
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
			'anim_text_content',
			array(
				'label' => esc_html__( 'Text Rotator', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'prefix_text',
			array(
				'label' => esc_html__( 'Prefix Text', 'wbcom-essential' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Text', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Animated Text', 'wbcom-essential' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'list',
			array(
				'label'       => esc_html__( 'Animated Texts', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'title' => esc_html__( 'Animated Text #1', 'wbcom-essential' ),
					),
					array(
						'title' => esc_html__( 'Animated Text #2', 'wbcom-essential' ),
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->add_control(
			'suffix_text',
			array(
				'label' => esc_html__( 'Suffix Text', 'wbcom-essential' ),
				'type'  => Controls_Manager::TEXTAREA,
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'anim_text_settings',
			array(
				'label' => esc_html__( 'Settings', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h2',
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => esc_html__( 'Text Align', 'wbcom-essential' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Start', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'End', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'anim',
			array(
				'label'       => esc_html__( 'Animation', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::ANIMATION,
				'label_block' => true,
			)
		);

		$this->add_control(
			'anim_duration',
			array(
				'label'   => esc_html__( 'Animation Duration', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1000,
				'max'     => 10000,
				'step'    => 500,
				'default' => 3000,
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'anim_text_general_styles',
			array(
				'label' => esc_html__( 'General', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',

				'selector' => '{{WRAPPER}} .wbcom-anim-text-wrapper',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-anim-text-wrapper',
			)
		);

		$this->add_control(
			'text_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'text_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-anim-text-wrapper',
			)
		);

		$this->add_control(
			'text_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'text_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// section start.
		$this->start_controls_section(
			'anim_text_styles',
			array(
				'label' => esc_html__( 'Animated Text', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'anim_text_typography',

				'selector' => '{{WRAPPER}} .wbcom-anim-text-wrapper .wbcom-anim-text span',
			)
		);

		$this->add_control(
			'anim_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper .wbcom-anim-text span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'anim_text_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper .wbcom-anim-text span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'anim_text_shadow',
				'selector' => '{{WRAPPER}} .wbcom-anim-text-wrapper .wbcom-anim-text span',
			)
		);

		$this->add_control(
			'anim_text_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'anim_text_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-anim-text-wrapper .wbcom-anim-text span',
			)
		);

		$this->add_control(
			'anim_text_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'anim_text_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper .wbcom-anim-text span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'anim_text_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-anim-text-wrapper .wbcom-anim-text span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		if ( $settings['list'] ) {
			?>
			<?php echo '<' . esc_attr( $settings['html_tag'] ) . ' class="wbcom-anim-text-wrapper">'; ?>
			<span class="wbcom-anim-text-prefix"><?php echo esc_html( $settings['prefix_text'] ); ?></span>
			<span class="wbcom-anim-text" style="display:none;" data-txtanim="<?php echo esc_attr( $settings['anim'] ); ?>" data-animduration="<?php echo esc_attr( $settings['anim_duration'] ); ?>">
			<?php $last_key = end( $settings['list'] ); ?>
			<?php foreach ( $settings['list'] as $item ) { ?>
				<?php echo esc_html( $item['title'] ); ?>
						<?php
						if ( $item != $last_key ) {
							?>
					| <?php } ?>
			<?php } ?>
			</span>
			<span class="wbcom-anim-text-suffix"><?php echo esc_html( $settings['suffix_text'] ); ?></span>
				<?php echo '</' . esc_attr( $settings['html_tag'] ) . '>'; ?>
			<?php
		}
	}
}
