<?php
/**
 * Elementor skin base.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/helper/skins
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Helper\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Elementor skin base.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/helper/skins
 */
abstract class Skin_Base extends Elementor_Skin_Base {

	/**
	 * Register controls actions.
	 */
	protected function _register_controls_actions() {
		add_action( 'elementor/element/wbcom-posts/section_layout/before_section_end', array( $this, 'register_controls' ) );
		add_action( 'elementor/element/wbcom-posts/section_query/after_section_end', array( $this, 'register_style_sections' ) );
	}
	
	/**
	 * Register controls actions - new method for Elementor 3.1.0+
	 */
	protected function register_controls_actions() {
		$this->_register_controls_actions();
	}

	/**
	 * Register style actions.
	 *
	 * @param string Widget_Base $widget Elementor Widget.
	 */
	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_design_controls();
	}

	/**
	 * Register controls.
	 *
	 * @param string Widget_Base $widget Elementor Widget.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_columns_controls();
		$this->register_post_count_control();
		$this->register_thumbnail_controls();
		$this->register_title_controls();
		$this->register_excerpt_controls();
		$this->register_meta_data_controls();
		$this->register_read_more_controls();
	}

	/**
	 * Register design controls.
	 */
	public function register_design_controls() {
		$this->register_design_layout_controls();
		$this->register_design_image_controls();
		$this->register_design_content_controls();
	}

	/**
	 * Register thumbnail controls.
	 */
	protected function register_thumbnail_controls() {
		$this->add_control(
			'thumbnail',
			array(
				'label'        => __( 'Image Position', 'wbcom-essential' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'top',
				'options'      => array(
					'top'   => __( 'Top', 'wbcom-essential' ),
					'left'  => __( 'Left', 'wbcom-essential' ),
					'right' => __( 'Right', 'wbcom-essential' ),
					'none'  => __( 'None', 'wbcom-essential' ),
				),
				'prefix_class' => 'elementor-posts--thumbnail-',
			)
		);

		$this->add_control(
			'masonry',
			array(
				'label'              => __( 'Masonry', 'wbcom-essential' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_off'          => __( 'Off', 'wbcom-essential' ),
				'label_on'           => __( 'On', 'wbcom-essential' ),
				'condition'          => array(
					$this->get_control_id( 'columns!' )  => '1',
					$this->get_control_id( 'thumbnail' ) => 'top',
				),
				'render_type'        => 'ui',
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'         => 'thumbnail_size',
				'label'        => __( 'Thumbnail Size', 'wbcom-essential' ),
				'default'      => 'medium',
				'exclude'      => array( 'custom' ),
				'condition'    => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
				'prefix_class' => 'elementor-posts--thumbnail-size-',
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'          => __( 'Image Width', 'wbcom-essential' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 10,
						'max' => 600,
					),
				),
				'default'        => array(
					'size' => 100,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => '',
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'size_units'     => array( '%', 'px' ),
				'selectors'      => array(
					'{{WRAPPER}} .elementor-post__thumbnail__link' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);
	}

	/**
	 * Register columns controls.
	 */
	protected function register_columns_controls() {
		$this->add_responsive_control(
			'columns',
			array(
				'label'              => __( 'Columns', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'prefix_class'       => 'elementor-grid%s-',
				'frontend_available' => true,
			)
		);
	}

	/**
	 * Register post count controls.
	 */
	protected function register_post_count_control() {
		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __( 'Posts Per Page', 'wbcom-essential' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			)
		);
	}

	/**
	 * Register title controls.
	 */
	protected function register_title_controls() {
		$this->add_control(
			'show_title',
			array(
				'label'        => __( 'Title', 'wbcom-essential' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'wbcom-essential' ),
				'label_off'    => __( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'     => __( 'Title HTML Tag', 'wbcom-essential' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
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
				'default'   => 'h3',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Register excerpt controls.
	 */
	protected function register_excerpt_controls() {
		$this->add_control(
			'show_excerpt',
			array(
				'label'        => __( 'Excerpt', 'wbcom-essential' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'wbcom-essential' ),
				'label_off'    => __( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'     => __( 'Excerpt Length', 'wbcom-essential' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => apply_filters( 'excerpt_length', 25 ),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Register read more controls.
	 */
	protected function register_read_more_controls() {
		$this->add_control(
			'show_read_more',
			array(
				'label'        => __( 'Read More', 'wbcom-essential' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'wbcom-essential' ),
				'label_off'    => __( 'Hide', 'wbcom-essential' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'       => __( 'Read More Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Read More »', 'wbcom-essential' ),
				'placeholder' => __( 'Read More »', 'wbcom-essential' ),
				'condition'   => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Register meta data controls.
	 */
	protected function register_meta_data_controls() {
		$this->add_control(
			'meta_data',
			array(
				'label'       => __( 'Meta Data', 'wbcom-essential' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'default'     => array( 'date', 'comments' ),
				'multiple'    => true,
				'options'     => array(
					'author'   => __( 'Author', 'wbcom-essential' ),
					'date'     => __( 'Date', 'wbcom-essential' ),
					'time'     => __( 'Time', 'wbcom-essential' ),
					'comments' => __( 'Comments', 'wbcom-essential' ),
				),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'meta_separator',
			array(
				'label'     => __( 'Separator Between', 'wbcom-essential' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '///',
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data span + span:before' => 'content: "{{VALUE}}"',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);
	}

	/**
	 * Style Tab
	 */
	protected function register_design_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => __( 'Layout', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'column_gap',
			array(
				'label'     => __( 'Columns Gap', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .elementor-posts-container' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->add_control(
			'row_gap',
			array(
				'label'     => __( 'Rows Gap', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 35,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'        => __( 'Alignment', 'wbcom-essential' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'wbcom-essential' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'prefix_class' => 'elementor-posts--align-',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register design image controls.
	 */
	protected function register_design_image_controls() {
		$this->start_controls_section(
			'section_design_image',
			array(
				'label'     => __( 'Image', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);

		$this->add_control(
			'img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-post__thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);

		$this->add_control(
			'image_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.elementor-posts--thumbnail-left .elementor-post__thumbnail__link'   => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-posts--thumbnail-right .elementor-post__thumbnail__link'  => 'margin-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.elementor-posts--thumbnail-top .elementor-post__thumbnail__link'    => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'default'   => array(
					'size' => 20,
				),
				'condition' => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register design content controls.
	 */
	protected function register_design_content_controls() {

		$this->start_controls_section(
			'section_design_content',
			array(
				'label' => __( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label'     => __( 'Title', 'wbcom-essential' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__title, {{WRAPPER}} .elementor-post__title a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'label'     => __( 'Typography', 'wbcom-essential' ),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'  => '{{WRAPPER}} .elementor-post__title, {{WRAPPER}} .elementor-post__title a',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_meta_style',
			array(
				'label'     => __( 'Meta', 'wbcom-essential' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_separator_color',
			array(
				'label'     => __( 'Separator Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data span:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'meta_typography',
				'label'     => __( 'Typography', 'wbcom-essential' ),
				'global' => [
			'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_SECONDARY,
		],
				'selector'  => '{{WRAPPER}} .elementor-post__meta-data',
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'heading_excerpt_style',
			array(
				'label'     => __( 'Excerpt', 'wbcom-essential' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__excerpt' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'excerpt_typography',
				'label'     => __( 'Typography', 'wbcom-essential' ),
				'global' => [
			'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
		],
				'selector'  => '{{WRAPPER}} .elementor-post__excerpt',
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_readmore_style',
			array(
				'label'     => __( 'Read More', 'wbcom-essential' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_ACCENT,
				],
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__read-more' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'read_more_typography',
				'label'     => __( 'Typography', 'wbcom-essential' ),
				'selector'  => '{{WRAPPER}} .elementor-post__read-more',
				'global' => [
			'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
		],
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the content.
	 */
	public function render() {
		$this->parent->query_posts();

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		add_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
		add_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );

		$this->render_loop_header();

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->render_loop_footer();

		wp_reset_postdata();

		remove_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );
		remove_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
	}

	/**
	 * Filter excerpt length.
	 */
	public function filter_excerpt_length() {
		return $this->get_instance_value( 'excerpt_length' );
	}

	/**
	 * Register excerpt more.
	 *
	 * @param string $more More Text.
	 */
	public function filter_excerpt_more( $more ) {
		return '';
	}

	/**
	 * Render Thumbnail.
	 */
	protected function render_thumbnail() {
		$thumbnail = $this->get_instance_value( 'thumbnail' );

		if ( 'none' === $thumbnail ) {
			return;
		}

		$settings                 = $this->parent->get_settings();
		$setting_key              = $this->get_control_id( 'thumbnail_size' );
		$settings[ $setting_key ] = array(
			'id' => get_post_thumbnail_id(),
		);
		$thumbnail_html           = Group_Control_Image_Size::get_attachment_image_html( $settings, $setting_key );

		if ( empty( $thumbnail_html ) ) {
			return;
		}
		?>
		<a class="elementor-post__thumbnail__link" href="<?php echo esc_url( get_permalink() ); ?>">
			<div class="elementor-post__thumbnail"><?php echo wp_kses_post( $thumbnail_html ); ?></div>
		</a>
		<?php
	}

	/**
	 * Render Thumbnail.
	 */
	protected function render_title() {
		if ( ! $this->get_instance_value( 'show_title' ) ) {
			return;
		}

		$tag = $this->get_instance_value( 'title_tag' );
		?>
		<<?php echo esc_attr( $tag ); ?> class="elementor-post__title">
		<a href="<?php echo esc_url( get_permalink() ); ?>">
			<?php the_title(); ?>
		</a>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}

	/**
	 * Render Excerpt.
	 */
	protected function render_excerpt() {
		if ( ! $this->get_instance_value( 'show_excerpt' ) ) {
			return;
		}
		?>
		<div class="elementor-post__excerpt">
			<?php the_excerpt(); ?>
		</div>
		<?php
	}

	/**
	 * Render read more.
	 */
	protected function render_read_more() {
		if ( ! $this->get_instance_value( 'show_read_more' ) ) {
			return;
		}
		?>
		<a class="elementor-post__read-more" href="<?php echo esc_url( get_permalink() ); ?>">
			<?php echo esc_html( $this->get_instance_value( 'read_more_text' ) ); ?>
		</a>
		<?php
	}

	/**
	 * Render post header.
	 */
	protected function render_post_header() {
		?>
		<article <?php post_class( array( 'elementor-post elementor-grid-item' ) ); ?>>
			<?php
	}

	/**
	 * Render post footer.
	 */
	protected function render_post_footer() {
		?>
		</article>
		<?php
	}

	/**
	 * Render header text.
	 */
	protected function render_text_header() {
		?>
		<div class="elementor-post__text">
			<?php
	}

	/**
	 * Render footer text.
	 */
	protected function render_text_footer() {
		?>
		</div>
		<?php
	}

	/**
	 * Render header loop.
	 */
	protected function render_loop_header() {
		$this->parent->add_render_attribute(
			'container',
			array(
				'class' => array(
					'elementor-posts-container',
					'elementor-posts',
					'elementor-grid',
					'elementor-posts--skin-' . $this->get_id(),
				),
			)
		);
		?>
		<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor's get_render_attribute_string() returns escaped HTML ?>
		<div <?php echo $this->parent->get_render_attribute_string( 'container' ); ?>>
			<?php
	}

	/**
	 * Render footer loop.
	 */
	protected function render_loop_footer() {
		?>
		</div>
		<?php
		$parent_settings = $this->parent->get_settings();

		if ( '' === $parent_settings['pagination_type'] ) {
			return;
		}

		$page_limit = $this->parent->get_query()->max_num_pages;

		if ( '' !== $parent_settings['pagination_page_limit'] ) {
			$page_limit = min( $parent_settings['pagination_page_limit'], $page_limit );
		}

		if ( 2 > $page_limit ) {
			return;
		}

		$this->parent->add_render_attribute( 'pagination', 'class', 'elementor-pagination' );

		$has_numbers   = in_array( $parent_settings['pagination_type'], array( 'numbers', 'numbers_and_prev_next' ) );
		$has_prev_next = in_array( $parent_settings['pagination_type'], array( 'prev_next', 'numbers_and_prev_next' ) );

		$links = array();

		if ( $has_numbers ) {
			$links = paginate_links(
				array(
					'type'               => 'array',
					'current'            => $this->parent->get_current_page(),
					'total'              => $page_limit,
					'prev_next'          => false,
					'show_all'           => 'yes' !== $parent_settings['pagination_numbers_shorten'],
					'before_page_number' => '<span class="elementor-screen-only">' . __( 'Page', 'wbcom-essential' ) . '</span>',
				)
			);
		}

		if ( $has_prev_next ) {
			$prev_next = $this->parent->get_posts_nav_link( $page_limit );
			array_unshift( $links, $prev_next['prev'] );
			$links[] = $prev_next['next'];
		}
		?>
		<nav class="elementor-pagination" role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'wbcom-essential' ); ?>">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- paginate_links() returns safe HTML
		echo implode( PHP_EOL, $links );
		?>
		</nav>
		<?php
	}

	/**
	 * Render meta data.
	 */
	protected function render_meta_data() {

		$settings = $this->get_instance_value( 'meta_data' );
		if ( empty( $settings ) ) {
			return;
		}
		?>
		<div class="elementor-post__meta-data">
			<?php
			if ( in_array( 'author', $settings ) ) {
				$this->render_author();
			}

			if ( in_array( 'date', $settings ) ) {
				$this->render_date();
			}

			if ( in_array( 'time', $settings ) ) {
				$this->render_time();
			}

			if ( in_array( 'comments', $settings ) ) {
				$this->render_comments();
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render author.
	 */
	protected function render_author() {
		?>
		<span class="elementor-post-author">
			<?php the_author(); ?>
		</span>
		<?php
	}

	/**
	 * Render date.
	 */
	protected function render_date() {
		?>
		<span class="elementor-post-date">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- apply_filters('the_date') is a core WordPress filter that returns safe HTML
			echo apply_filters( 'the_date', get_the_date(), get_option( 'date_format' ), '', '' );
			?>
		</span>
		<?php
	}

	/**
	 * Render time.
	 */
	protected function render_time() {
		?>
		<span class="elementor-post-time">
			<?php the_time(); ?>
		</span>
		<?php
	}

	/**
	 * Render comments.
	 */
	protected function render_comments() {
		?>
		<span class="elementor-post-avatar">
			<?php comments_number(); ?>
		</span>
		<?php
	}

	/**
	 * Render post.
	 */
	protected function render_post() {
		$this->render_post_header();
		$this->render_thumbnail();
		$this->render_text_header();
		$this->render_title();
		$this->render_meta_data();
		$this->render_excerpt();
		$this->render_read_more();
		$this->render_text_footer();
		$this->render_post_footer();
	}

}
