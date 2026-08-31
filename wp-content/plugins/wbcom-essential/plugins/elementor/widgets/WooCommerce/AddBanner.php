<?php
/**
 * Elementor widget add banner.
 *
 * @since      3.7.1
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/woocommerce
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;

/**
 * Add Banner.
 *
 * @since      3.7.1
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/woocommerce
 */
class AddBanner extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wbcom-widgets', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wbcom-widgets.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-add-banner';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Add Banner', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-photo-library';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array(
			'wbcom-widgets',
		);
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
		return array( 'banner', 'image banner', 'adds', 'adds banner' );
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'banner-conent',
			array(
				'label' => __( 'Banner', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'banner_layout',
				array(
					'label'   => __( 'Style', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1' => __( 'Style One', 'wbcom-essential' ),
						'2' => __( 'Style Two', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'content_alignment',
				array(
					'label'   => __( 'Content Alignment', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => array(
						'left'   => __( 'Left', 'wbcom-essential' ),
						'right'  => __( 'Right', 'wbcom-essential' ),
						'bottom' => __( 'Bottom', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'bannerimage',
				array(
					'label'   => __( 'Banner image', 'wbcom-essential' ),
					'type'    => Controls_Manager::MEDIA,
					'default' => array(
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'bannerimagesize',
					'default'   => 'large',
					'separator' => 'none',
				)
			);

			$this->add_control(
				'bannertitle',
				array(
					'label' => __( 'Banner Title', 'wbcom-essential' ),
					'type'  => Controls_Manager::TEXTAREA,
				)
			);

			$this->add_control(
				'bannersubtitle',
				array(
					'label' => __( 'Banner Sub Title', 'wbcom-essential' ),
					'type'  => Controls_Manager::TEXTAREA,
				)
			);

			$this->add_control(
				'buttontxt',
				array(
					'label' => __( 'Button Text', 'wbcom-essential' ),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$this->add_control(
				'buttonlink',
				array(
					'label'         => __( 'Button Link', 'wbcom-essential' ),
					'type'          => Controls_Manager::URL,
					'placeholder'   => __( 'https://your-link.com', 'wbcom-essential' ),
					'show_external' => true,
					'default'       => array(
						'url'         => '',
						'is_external' => true,
						'nofollow'    => true,
					),
				)
			);

		$this->end_controls_section();

		// Slider Button stle.
		$this->start_controls_section(
			'banner-style-section',
			array(
				'label' => esc_html__( 'Style', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'title_style_heading',
				array(
					'label' => __( 'Title', 'wbcom-essential' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'title_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#404040',
					'selectors' => array(
						'{{WRAPPER}} .wbcom-banner .banner_title' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'title_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wbcom-banner .banner_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'title_typography',
					'label'    => __( 'Typography', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wbcom-banner .banner_title',
				)
			);

			$this->add_control(
				'sub_title_style_heading',
				array(
					'label'     => __( 'Sub Title', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'sub_title_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#404040',
					'selectors' => array(
						'{{WRAPPER}} .wbcom-banner .banner_subtitle' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'sub_title_typography',
					'label'    => __( 'Typography', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wbcom-banner .banner_subtitle',
				)
			);

			$this->add_responsive_control(
				'sub_title_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wbcom-banner .banner_subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'button_style_heading',
				array(
					'label'     => __( 'Button', 'wbcom-essential' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'button_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#404040',
					'selectors' => array(
						'{{WRAPPER}} .wbcom-banner .banner_button' => 'color: {{VALUE}};border-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'button_hover_color',
				array(
					'label'     => __( 'Hover Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#404040',
					'selectors' => array(
						'{{WRAPPER}} .wbcom-banner .banner_button:hover' => 'color: {{VALUE}};border-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'button_typography',
					'label'    => __( 'Typography', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wbcom-banner .banner_button',
				)
			);

		$this->end_controls_section(); // Tab option end.
	}

	protected function render( $instance = array() ) {

		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'area_attr', 'class', 'wbcom-banner' );
		$this->add_render_attribute( 'area_attr', 'class', 'wbcom-content-align-' . $settings['content_alignment'] );
		$this->add_render_attribute( 'area_attr', 'class', 'wbcom-banner-layout-' . $settings['banner_layout'] );

		// Button Link.
		$target   = $settings['buttonlink']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['buttonlink']['nofollow'] ? ' rel="nofollow"' : '';

		?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_render_attribute_string() returns escaped HTML ?>
			<div <?php echo $this->get_render_attribute_string( 'area_attr' ); ?> >
				<div class="wbcom-content">
					<?php
					if ( ! empty( $settings['bannersubtitle'] ) ) {
						echo '<h3 class="banner_subtitle">' . esc_html( $settings['bannersubtitle'] ) . '</h3>';
					}
					if ( ! empty( $settings['bannertitle'] ) ) {
						echo '<h2 class="banner_title">' . esc_html( $settings['bannertitle'] ) . '</h2>';
					}
					if ( ! empty( $settings['buttontxt'] ) ) {
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $target and $nofollow are hardcoded safe strings
						echo '<a class="banner_button" href="' . esc_url( $settings['buttonlink']['url'] ) . '" ' . $target . $nofollow . '>' . esc_html( $settings['buttontxt'] ) . '</a>';
					}
					?>
				</div>
				<div class="wbcom-banner-img">
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $target and $nofollow are hardcoded safe strings ?>
					<a href="<?php echo esc_url( $settings['buttonlink']['url'] ); ?>" <?php echo $target . $nofollow; ?> >
						<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor's get_attachment_image_html() returns escaped HTML ?>
						<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'bannerimagesize', 'bannerimage' ); ?>
					</a>
				</div>
			</div>
		<?php
	}
}
