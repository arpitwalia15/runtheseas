<?php
namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Timeline
 *
 * Elementor widget for Timeline
 *
 * @since 3.6.0
 */
class Timeline extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		// phpcs:ignore PluginCheck.CodeAnalysis.EnqueuedResourceOffloading.OffloadedContent -- Font Awesome is bundled locally in the plugin.
		if ( ! wp_style_is( 'font-awesome-5', 'enqueued' ) ) {
			wp_register_style(
				'font-awesome-5',
				WBCOM_ESSENTIAL_URL . 'assets/vendor/font-awesome/css/all.min.css',
				array(),
				'5.15.4'
			);
		}

		wp_register_style( 'wb-timeline', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/timeline.css', array(), WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wb-timeline', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/timeline.min.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-timeline';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Timeline', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-time-line';
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
		return array( 'timeline', 'history', 'events', 'chronology', 'vertical' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'wb-timeline' );
	}

	/**
	 * Get dependent style..
	 */
	public function get_style_depends() {
		return array( 'wb-timeline', 'font-awesome-5' );
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
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-star',
					'library' => 'solid',
				),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'Image', 'wbcom-essential' ),
				'type'  => \Elementor\Controls_Manager::MEDIA,
			)
		);

		$repeater->add_responsive_control(
			'content_text_align',
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .wbcom-timeline__content' => 'text-align: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'date',
			array(
				'label'   => esc_html__( 'Date', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Jan 14', 'wbcom-essential' ),
			)
		);

		$repeater->add_control(
			'title_html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'wbcom-essential' ),
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
				'default' => 'h3',
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Title', 'wbcom-essential' ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label'      => esc_html__( 'Content', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::WYSIWYG,
				'default'    => esc_html__( 'Enim ad commodo do est proident excepteur nulla enim pariatur. Proident et laborum reprehenderit voluptate velit Lorem culpa ullamco.', 'wbcom-essential' ),
				'show_label' => false,
			)
		);

		$this->add_control(
			'list',
			array(
				'label'       => esc_html__( 'Items', 'wbcom-essential' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'show_label'  => false,
				'default'     => array(
					array(
						'date'           => esc_html__( 'Jan 14', 'wbcom-essential' ),
						'title'          => esc_html__( 'Title #1', 'wbcom-essential' ),
						'title_html_tag' => 'h3',
						'text'           => esc_html__( 'Enim ad commodo do est proident excepteur nulla enim pariatur. Proident et laborum reprehenderit voluptate velit Lorem culpa ullamco.', 'wbcom-essential' ),
					),
					array(
						'date'           => esc_html__( 'Jan 14', 'wbcom-essential' ),
						'title'          => esc_html__( 'Title #2', 'wbcom-essential' ),
						'title_html_tag' => 'h3',
						'text'           => esc_html__( 'Enim ad commodo do est proident excepteur nulla enim pariatur. Proident et laborum reprehenderit voluptate velit Lorem culpa ullamco.', 'wbcom-essential' ),
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'section_timeline_style',
			array(
				'label' => esc_html__( 'Timeline', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'wbcom-essential' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'wbcom-timeline-2-col',
				'options' => array(
					'wbcom-timeline-2-col' => esc_html__( 'Two Column', 'wbcom-essential' ),
					'wbcom-timeline-1-col' => esc_html__( 'One Column', 'wbcom-essential' ),
				),
			)
		);

		$this->add_responsive_control(
			'bar_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline__container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'timeline_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'bar_thickness',
			array(
				'label'     => esc_html__( 'Bar Thickness', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 100,
				'step'      => 1,
				'default'   => 4,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__container:before' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'bar_color',
			array(
				'label'     => esc_html__( 'Bar Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__container:before' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'timeline_hr_2',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'icon_container_size',
			array(
				'label'     => esc_html__( 'Icon Container Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 500,
				'step'      => 1,
				'default'   => 60,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__img' => 'width: {{VALUE}}px;height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-timeline.wbcom-timeline-2-col .wbcom-timeline__block .wbcom-timeline__img' => 'margin-left: calc(5% - ({{VALUE}}px / 2));',
					'{{WRAPPER}} .wbcom-timeline.wbcom-timeline-2-col .wbcom-timeline__block:nth-child(even) .wbcom-timeline__img' => 'margin-right: calc(5% - ({{VALUE}}px / 2));',
					'{{WRAPPER}} .wbcom-timeline__container:before' => 'left: calc(({{VALUE}}px - {{bar_thickness.VALUE}}px) / 2);',
					'{{WRAPPER}} .wbcom-timeline__content:before' => 'top: calc(({{VALUE}}px / 2) - 8px);',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'icon_container_bg',
				'label'    => esc_html__( 'Icon Container Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-timeline__img',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'icon_container_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-timeline__img',
			)
		);

		$this->add_control(
			'icon_container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline__img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'icon_container_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-timeline__img',
			)
		);

		$this->add_control(
			'timeline_hr_3',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'     => esc_html__( 'Icon Size', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 500,
				'step'      => 1,
				'default'   => 22,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__img i'   => 'font-size: {{VALUE}}px;line-height: {{VALUE}}px;',
					'{{WRAPPER}} .wbcom-timeline__img svg' => 'width: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__img i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-timeline__img svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// section start
		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => esc_html__( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'display_arrow',
			array(
				'label'        => esc_html__( 'Display arrow', 'wbcom-essential' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
				'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
				'return_value' => 'show-arrow',
				'default'      => 'show-arrow',
				'show_label'   => true,
			)
		);

		$this->add_control(
			'content_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#eeeeee',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__content' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-timeline__content:before' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}} .wbcom-timeline.wbcom-timeline-2-col .wbcom-timeline__block:nth-child(odd) .wbcom-timeline__content:before' => 'border-left-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'content_border',
				'label'    => esc_html__( 'Border', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-timeline__content',
			)
		);

		$this->add_control(
			'content_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline__content' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'content_shadow',
				'label'    => esc_html__( 'Box Shadow', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .wbcom-timeline__content',
			)
		);

		$this->add_control(
			'content_hr_1',
			array(
				'type' => \Elementor\Controls_Manager::DIVIDER,
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline__content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_heading_1',
			array(
				'label'     => esc_html__( 'Image', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'content_img_width',
			array(
				'label'      => esc_html__( 'Image Width', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline-img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_img_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline__content img' => 'border-top-left-radius: {{TOP}}{{UNIT}};border-top-right-radius: {{RIGHT}}{{UNIT}};border-bottom-right-radius: {{BOTTOM}}{{UNIT}};border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_img_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_heading_2',
			array(
				'label'     => esc_html__( 'Title', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',

				'selector' => '{{WRAPPER}} .wbcom-timeline-title',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_heading_3',
			array(
				'label'     => esc_html__( 'Paragraph', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__content p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',

				'selector' => '{{WRAPPER}} .wbcom-timeline__content p',
			)
		);

		$this->add_responsive_control(
			'text_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline__content p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'content_heading_4',
			array(
				'label'     => esc_html__( 'Date', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label'     => esc_html__( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-timeline__date' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'date_typography',

				'selector' => '{{WRAPPER}} .wbcom-timeline__date',
			)
		);

		$this->add_responsive_control(
			'date_margin',
			array(
				'label'      => esc_html__( 'Margin', 'wbcom-essential' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-timeline-date .wbcom-timeline__date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		if ( $settings['list'] ) { ?>
		<div class="wbcom-timeline <?php echo esc_attr( $settings['layout'] ); ?>">
			<div class="wbcom-timeline__container">
				<?php foreach ( $settings['list'] as $item ) { ?>
				<div class="wbcom-timeline__block elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
					<div class="wbcom-timeline__img">
					<?php \Elementor\Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</div>
					<div class="wbcom-timeline__content <?php echo esc_attr( $settings['display_arrow'] ); ?>">
					<?php
					if ( $item['image']['url'] ) {
						echo '<div class="wbcom-timeline-img">' . wp_get_attachment_image( $item['image']['id'], 'full' ) . '</div>';
					}
					?>
					<?php if ( $item['date'] ) { ?>
					<div class="wbcom-timeline-date">
						<span class="wbcom-timeline__date"><?php echo esc_html( $item['date'] ); ?></span>
					</div>
					<?php } ?>
					<?php
					if ( $item['title'] ) {
						echo '<' . esc_attr( $item['title_html_tag'] ) . ' class="wbcom-timeline-title">' . esc_html( $item['title'] ) . '</' . esc_attr( $item['title_html_tag'] ) . '>';
					}
					?>
					<?php
					echo wp_kses_post( wpautop( $item['text'] ) ); ?>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
			<?php
		}
	}
}
