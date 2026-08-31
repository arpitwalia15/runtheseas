<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Shape
 *
 * Elementor widget for Shape
 *
 * @since 3.6.0
 */
class Shape extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
		wp_register_style( 'wb-shape', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/shape.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-shape';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Shape', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-circle-o';
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
		return array( 'shape', 'divider', 'blob', 'decoration', 'graphic' );
	}

	/**
	 * Get dependent style..
	 */
	public function get_style_depends() {
		return array( 'wb-shape' );
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
			'section_shape',
			array(
				'label' => esc_html__( 'Shape', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'shape_value_1',
			array(
				'label'      => esc_html__( 'Point 1', 'wbcom-essential' ),
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
					'size' => 30,
				),
			)
		);

		$this->add_control(
			'shape_value_2',
			array(
				'label'      => esc_html__( 'Point 2', 'wbcom-essential' ),
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
					'size' => 70,
				),
			)
		);

		$this->add_control(
			'shape_value_3',
			array(
				'label'      => esc_html__( 'Point 3', 'wbcom-essential' ),
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
					'size' => 70,
				),
			)
		);

		$this->add_control(
			'shape_value_4',
			array(
				'label'      => esc_html__( 'Point 4', 'wbcom-essential' ),
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
					'size' => 30,
				),
			)
		);

		$this->add_control(
			'shape_value_5',
			array(
				'label'      => esc_html__( 'Point 5', 'wbcom-essential' ),
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
					'size' => 30,
				),
			)
		);

		$this->add_control(
			'shape_value_6',
			array(
				'label'      => esc_html__( 'Point 6', 'wbcom-essential' ),
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
					'size' => 30,
				),
			)
		);

		$this->add_control(
			'shape_value_7',
			array(
				'label'      => esc_html__( 'Point 7', 'wbcom-essential' ),
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
					'size' => 70,
				),
			)
		);

		$this->add_control(
			'shape_value_8',
			array(
				'label'      => esc_html__( 'Point 8', 'wbcom-essential' ),
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
					'size' => 70,
				),
			)
		);

		$this->add_responsive_control(
			'rotate',
			array(
				'label'      => esc_html__( 'Rotate', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 360,
					),
				),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-custom-shape' => 'transform:rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_shape_icon',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas long-arrow-alt-down',
					'library' => 'solid',
				),
			)
		);

		$this->add_control(
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
			'shape_animation',
			array(
				'label' => esc_html__( 'Hover Animation', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'section_shape_style',
			array(
				'label' => esc_html__( 'Shape', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'shape_width',
			array(
				'label'      => esc_html__( 'Width', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 2000,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-custom-shape' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'shape_height',
			array(
				'label'      => esc_html__( 'Height', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'rem' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 2000,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-custom-shape' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'background',
				'label'    => esc_html__( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-custom-shape',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-custom-shape',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-custom-shape',
			)
		);

		$this->add_responsive_control(
			'shave_align',
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
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-custom-shape-wrapper' => 'align-items: {{VALUE}};',
				),
				'toggle'    => false,
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => esc_html__( 'Icon', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-custom-shape i'      => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-custom-shape-link i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-custom-shape svg'    => 'fill: {{VALUE}};',
					'{{WRAPPER}} .wbcom-custom-shape-link svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'rem' => array(
						'min' => 0,
						'max' => 50,
					),
					'px'  => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-custom-shape i' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_width',
			array(
				'label'     => esc_html__( 'SVG Width', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-custom-shape svg' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_height',
			array(
				'label'     => esc_html__( 'SVG Height', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1000,
				'step'      => 5,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-custom-shape svg' => 'height: {{VALUE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'icon_rotate',
			array(
				'label'      => esc_html__( 'Rotate', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 360,
					),
				),
				'default'    => array(
					'unit' => 'deg',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-custom-shape i'   => 'transform:rotate({{SIZE}}{{UNIT}});',
					'{{WRAPPER}} .wbcom-custom-shape svg' => 'transform:rotate({{SIZE}}{{UNIT}});',
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
		$target   = $settings['website_link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['website_link']['nofollow'] ? ' rel="nofollow"' : '';
		?>
		<div class="wbcom-custom-shape-wrapper elementor-animation-<?php echo esc_attr( $settings['shape_animation'] ); ?>">
		<div class="wbcom-custom-shape" style="border-radius:<?php echo esc_attr( $settings['shape_value_1']['size'] ); ?>% <?php echo esc_attr( $settings['shape_value_2']['size'] ); ?>% <?php echo esc_attr( $settings['shape_value_3']['size'] ); ?>% <?php echo esc_attr( $settings['shape_value_4']['size'] ); ?>% / <?php echo esc_attr( $settings['shape_value_5']['size'] ); ?>% <?php echo esc_attr( $settings['shape_value_6']['size'] ); ?>% <?php echo esc_attr( $settings['shape_value_7']['size'] ); ?>% <?php echo esc_attr( $settings['shape_value_8']['size'] ); ?>% "><?php \Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) ); ?>
		<?php
		if ( $settings['website_link']['url'] ) {
			echo '<a class="wbcom-custom-shape-link" href="' . esc_url( $settings['website_link']['url'] ) . '"' . esc_attr( $target ) . esc_attr( $nofollow ) . '></a>'; }
		?>
		</div>
		</div>
		<?php
	}

	/**
	 * Content Template
	 *
	 * @since 3.6.0
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<# var iconHTML = elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden': true }, 'i' , 'object' ); #>
		<div class="wbcom-custom-shape-wrapper elementor-animation-{{ settings.shape_animation }}">   
		<div class="wbcom-custom-shape" style="border-radius:{{ settings.shape_value_1.size }}% {{ settings.shape_value_2.size }}% {{ settings.shape_value_3.size }}% {{ settings.shape_value_4.size }}% / {{ settings.shape_value_5.size }}% {{ settings.shape_value_6.size }}% {{ settings.shape_value_7.size }}% {{ settings.shape_value_8.size }}% ">{{{ iconHTML.value }}}<a href="#" class="wbcom-custom-shape-link"></a></div>
		</div>    
		<?php
	}
}
