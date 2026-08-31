<?php
/**
 * Elementor branding widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/general
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\General;

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
 * Elementor branding widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/forums
 */
class Branding extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-branding', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/branding.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get name.
	 */
	public function get_name() {
		return 'wbcom-branding';
	}

	/**
	 * Get title.
	 */
	public function get_title() {
		return esc_html__( 'Branding', 'wbcom-essential' );
	}

	/**
	 * Get icon.
	 */
	public function get_icon() {
		return 'eicon-banner';
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
		return array( 'branding', 'logo', 'site', 'identity', 'brand' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'wb-branding' );
	}

	/**
	 * Register elementor branding widget controls.
	 */
	protected function register_controls() {

		do_action( 'wbcom_essential/widget/branding/settings', $this );

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Branding', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'el_site_branding',
			array(
				'label'       => __( 'Branding Type', 'wbcom-essential' ),
				'description' => __( 'Your theme must declare the "add_theme_support( \'custom-logo\')" for the logo to work', 'wbcom-essential' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'title' => __( 'Title', 'wbcom-essential' ),
					'logo'  => __( 'Logo', 'wbcom-essential' ),
				),
				'default'     => 'title',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'        => __( 'Alignment', 'wbcom-essential' ),
				'type'         => Controls_Manager::CHOOSE,
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
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Brand', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'branding_title_color',
			array(
				'label'     => __( 'Title Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'el_site_branding' => 'title',
				),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .elementor-branding .site-title a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'branding_title_hover',
			array(
				'label'     => __( 'Hover', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'el_site_branding' => 'title',
				),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => array(
					'{{WRAPPER}} .elementor-branding .site-title a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_padding',
			array(
				'label'      => __( 'Title Padding - Default 1em', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'condition'  => array(
					'el_site_branding' => 'title',
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-branding .site-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'label'     => __( 'Typography', 'wbcom-essential' ),
				'condition' => array(
					'el_site_branding' => 'title',
				),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'  => '{{WRAPPER}} .elementor-branding .site-title',
			)
		);

		$this->add_control(
			'logo_padding',
			array(
				'label'      => __( 'Title Padding - Default 1em', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'condition'  => array(
					'el_site_branding' => 'logo',
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-branding .custom-logo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_desc_style',
			array(
				'label'     => __( 'Description Options', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'el_site_branding' => 'title',
				),
			)
		);

		$this->add_control(
			'branding_description_color',
			array(
				'label'     => __( 'Description Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'el_site_branding' => 'title',
				),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => array(
					'{{WRAPPER}} .elementor-branding .site-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'desc_padding',
			array(
				'label'      => __( 'Description Padding - Default 1em', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'condition'  => array(
					'el_site_branding' => 'title',
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-branding .site-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'desc_typography',
				'label'     => __( 'Typography', 'wbcom-essential' ),
				'condition' => array(
					'el_site_branding' => 'title',
				),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector'  => '{{WRAPPER}} .elementor-branding .site-description',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_branding_borders',
			array(
				'label' => __( 'Branding Border', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border',
				'label'    => __( 'Border', 'wbcom-essential' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .elementor-branding',
			)
		);

		$this->add_control(
			'border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-branding' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		do_action( 'wbcom_essential_branding_elementor_controls', $this );
	}

	/**
	 * Elementor branding widget output.
	 */
	protected function branding_output() {
		$settings = $this->get_settings();

		if ( 'title' === $settings['el_site_branding'] ) {
			$this->render_title();
		} elseif ( 'logo' === $settings['el_site_branding'] ) {
			$this->elementor_the_site_logo();
		}
	}

	/**
	 * Display site logo.
	 */
	protected function elementor_the_site_logo() {
		if ( function_exists( 'the_custom_logo' ) ) {
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				$this->render_title();
			}
		} else {
			$this->render_title();
		}
	}

	/**
	 * Render site title.
	 */
	protected function render_title() {
		?>
		<span class="site-title">
			<?php
			$title = get_bloginfo( 'name' );
			?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( $title ); ?>" alt="<?php echo esc_attr( $title ); ?>">
				<?php bloginfo( 'name' ); ?>
			</a>
		</span>
		<?php
		$description = get_bloginfo( 'description', 'display' );
		if ( $description || is_customize_preview() ) :
			?>
	<p class="site-description"><?php echo wp_kses_post( $description ); ?></p>
			<?php
	endif;
	}

	/**
	 * Render branding widget output.
	 */
	protected function render() {

		$settings = $this->get_settings();
		?>

		<div id="elementor-branding" class="elementor-branding">
			<div class="header-title">
				<?php
				$this->branding_output();
				?>
			</div>
		</div>
		<?php
	}
}
