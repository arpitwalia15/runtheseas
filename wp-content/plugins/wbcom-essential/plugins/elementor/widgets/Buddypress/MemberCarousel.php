<?php
/**
 * Elementor member carousel widget.
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
/**
 * Elementor member carousel widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */
class MemberCarousel extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wbe-swiper', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/swiper-base.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'member-carousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/member-carousel.css', array( 'wbe-swiper' ), WBCOM_ESSENTIAL_VERSION );

		wp_register_script(
			'swiper',
			WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/swiper.min.js',
			array( 'jquery' ),
			WBCOM_ESSENTIAL_VERSION,
			true
		);
		wp_register_script( 'member-carousel', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/member-carousel.js', array( 'jquery', 'swiper' ), WBCOM_ESSENTIAL_VERSION, true );
		// wp_register_style( 'style-handle', 'path/to/file.CSS' );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-members-carousel';
	}

	/**
	 * Get title.
	 */
	public function get_title() {
		return esc_html__( 'Members Carousel', 'wbcom-essential' );
	}

	/**
	 * Get icon.
	 */
	public function get_icon() {
		return 'eicon-slideshow';
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'member-carousel' );
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'member-carousel' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'member', 'members', 'carousel', 'slider' );
	}

	/**
	 * Get Categories.
	 */
	public function get_categories() {
		return array( 'wbcom-elements' );
	}

	/**
	 * Register members carousel widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.0.0
	 * @access protected
	 */
	protected function register_controls() {

		do_action( 'wbcom_essential/widget/members-listing/settings', $this );

						$this->start_controls_section(
							'section_members_carousel',
							array(
								'label' => __( 'Settings', 'wbcom-essential' ),
								'tab'   => Controls_Manager::TAB_CONTENT,
							)
						);

			$this->add_control(
				'type',
				array(
					'label'   => esc_html__( 'Sort', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'newest',
					'options' => array(
						'newest'  => esc_html__( 'Newest', 'wbcom-essential' ),
						'active'  => esc_html__( 'Most Active', 'wbcom-essential' ),
						'popular' => esc_html__( 'Most Popular', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'total',
				array(
					'label'       => __( 'Total members', 'wbcom-essential' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => '12',
					'placeholder' => __( 'Total members', 'wbcom-essential' ),
				)
			);

		$this->add_control(
			'hide_member_last_active',
			array(
				'label' => esc_html__( 'Hide member last active info', 'wbcom-essential' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

			// $this->add_control(
			// 'scroll',
			// array(
			// 'label'       => __( 'Members to scroll', 'wbcom-essential' ),
			// 'type'        => Controls_Manager::NUMBER,
			// 'default'     => 2,
			// 'placeholder' => '',
			// )
			// );

		$this->end_controls_section();

		$this->start_controls_section(
			'members_carousel_additional_options',
			array(
				'label' => __( 'Additional Options', 'wbcom-essential' ),
			)
		);

		$slides_to_show = range( 1, 10 );
		$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

		$this->add_responsive_control(
			'slides_to_show',
			array(
				'label'              => __( 'Members to Show', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'' => __( 'Default', 'wbcom-essential' ),
				) + $slides_to_show,
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'              => __( 'Members to Scroll', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'description'        => __( 'Set how many slides are scrolled per swipe.', 'wbcom-essential' ),
				'options'            => array(
					'' => __( 'Default', 'wbcom-essential' ),
				) + $slides_to_show,
				'condition'          => array(
					'slides_to_show!' => '1',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label'              => __( 'Navigation', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'both',
				'options'            => array(
					'both'   => __( 'Arrows and Dots', 'wbcom-essential' ),
					'arrows' => __( 'Arrows', 'wbcom-essential' ),
					'dots'   => __( 'Dots', 'wbcom-essential' ),
					'none'   => __( 'None', 'wbcom-essential' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'              => __( 'Autoplay', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'yes',
				'options'            => array(
					'yes' => __( 'Yes', 'wbcom-essential' ),
					'no'  => __( 'No', 'wbcom-essential' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'              => __( 'Pause on Hover', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'yes',
				'options'            => array(
					'yes' => __( 'Yes', 'wbcom-essential' ),
					'no'  => __( 'No', 'wbcom-essential' ),
				),
				'condition'          => array(
					'autoplay' => 'yes',
				),
				'render_type'        => 'none',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'pause_on_interaction',
			array(
				'label'              => __( 'Pause on Interaction', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'yes',
				'options'            => array(
					'yes' => __( 'Yes', 'wbcom-essential' ),
					'no'  => __( 'No', 'wbcom-essential' ),
				),
				'condition'          => array(
					'autoplay' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed', 'wbcom-essential' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5000,
				'condition'          => array(
					'autoplay' => 'yes',
				),
				'render_type'        => 'none',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'              => __( 'Infinite Loop', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'yes',
				'options'            => array(
					'yes' => __( 'Yes', 'wbcom-essential' ),
					'no'  => __( 'No', 'wbcom-essential' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'              => __( 'Effect', 'wbcom-essential' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'slide',
				'options'            => array(
					'slide' => __( 'Slide', 'wbcom-essential' ),
					'fade'  => __( 'Fade', 'wbcom-essential' ),
				),
				'condition'          => array(
					'slides_to_show' => '1',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'              => __( 'Animation Speed', 'wbcom-essential' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 500,
				'render_type'        => 'none',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Direction', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ltr',
				'options' => array(
					'ltr' => __( 'Left', 'wbcom-essential' ),
					'rtl' => __( 'Right', 'wbcom-essential' ),
				),
			)
		);

			$this->end_controls_section();

		do_action( 'reign_wp_menu_elementor_controls', $this );

	}

	/**
	 * Render elementor member carousel widget.
	 */
	protected function render() {
		// Check if BuddyPress is active before rendering.
		if ( ! function_exists( 'buddypress' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<p>' . esc_html__( 'BuddyPress is required for this widget.', 'wbcom-essential' ) . '</p>';
			}
			return;
		}

		$settings = $this->get_settings_for_display();

		$current_component = static function () {
			return 'members';
		};

		if ( isset( $settings['slides_to_show'] ) && '' !== $settings['slides_to_show'] ) {
			$swiper_options['slides_to_show'] = $settings['slides_to_show'];
		}
		if ( isset( $settings['slides_to_show_tablet'] ) && '' !== $settings['slides_to_show_tablet'] ) {
			$swiper_options['slides_to_show_tablet'] = $settings['slides_to_show_tablet'];
		}
		if ( isset( $settings['slides_to_show_mobile'] ) && '' !== $settings['slides_to_show_mobile'] ) {
			$swiper_options['slides_to_show_mobile'] = $settings['slides_to_show_mobile'];
		}
		if ( isset( $settings['slides_to_scroll'] ) && '' !== $settings['slides_to_scroll'] ) {
			$swiper_options['slides_to_scroll'] = $settings['slides_to_scroll'];
		}
		if ( isset( $settings['slides_to_scroll_tablet'] ) && '' !== $settings['slides_to_scroll_tablet'] ) {
			$swiper_options['slides_to_scroll_tablet'] = $settings['slides_to_scroll_tablet'];
		}
		if ( isset( $settings['slides_to_scroll_mobile'] ) && '' !== $settings['slides_to_scroll_mobile'] ) {
			$swiper_options['slides_to_scroll_mobile'] = $settings['slides_to_scroll_mobile'];
		}
		if ( isset( $settings['navigation'] ) && '' !== $settings['navigation'] ) {
			$swiper_options['navigation'] = $settings['navigation'];
		}
		if ( isset( $settings['autoplay_speed'] ) && '' !== $settings['autoplay_speed'] ) {
			$swiper_options['autoplay_speed'] = $settings['autoplay_speed'];
		}
		if ( isset( $settings['autoplay'] ) && '' !== $settings['autoplay'] ) {
			$swiper_options['autoplay'] = $settings['autoplay'];
		}
		if ( isset( $settings['pause_on_hover'] ) && '' !== $settings['pause_on_hover'] ) {
			$swiper_options['pause_on_hover'] = $settings['pause_on_hover'];
		}
		if ( isset( $settings['pause_on_interaction'] ) && '' !== $settings['pause_on_interaction'] ) {
			$swiper_options['pause_on_interaction'] = $settings['pause_on_interaction'];
		}
		if ( isset( $settings['infinite'] ) && '' !== $settings['infinite'] ) {
			$swiper_options['infinite'] = $settings['infinite'];
		}
		if ( isset( $settings['speed'] ) && '' !== $settings['speed'] ) {
			$swiper_options['speed'] = $settings['speed'];
		}

		$this->add_render_attribute(
			array(
				'carousel'         => array(
					'class' => 'elementor-member-carousel swiper-wrapper',
				),
				'carousel-wrapper' => array(
					'class'         => 'member-carousel-container swiper-container',
					'dir'           => $settings['direction'],
					'data-settings' => wp_json_encode( $swiper_options ),
				),
			)
		);

		$query_string = '&type=' . $settings['type'] . '&per_page=' . $settings['total'] . '&max=' . $settings['total'];
		$slides_count = isset( $settings['total'] ) ? $settings['total'] : 0;
		$show_dots    = ( in_array( $settings['navigation'], array( 'dots', 'both' ) ) );
		$show_arrows  = ( in_array( $settings['navigation'], array( 'arrows', 'both' ) ) );

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'carousel-wrapper' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'carousel' ) ); ?>>
					<?php
					if ( bp_has_members( bp_ajax_querystring( 'members' ) . $query_string ) ) {
						while ( bp_members() ) {
							bp_the_member();
							?>
							<div class="swiper-slide">
								<div <?php bp_member_class(); ?>>
									<div class="item-container">
										<div class="item-avatar">
											<figure class="swiper-slide-inner">
												<a class="member-avatar" href="<?php bp_member_permalink(); ?>">
														<?php
														bp_member_avatar(
															array(
																'type'  => 'full',
																'class'  => 'swiper-slide-image',
															)
														);
														?>
												</a>
											</figure>
										</div>
										<div class="item-card">
											<div class="item">
												<div class="item-meta">
													<h5 class="item-title">
														<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
													</h5>
													<?php if ( $settings['hide_member_last_active'] != 'yes' ) : ?>
														<span class="last-activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>">
															<?php bp_member_last_active(); ?>
														</span>
													<?php endif; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
					}
					?>
				</div>
				<?php if ( 1 < $slides_count ) : ?>
					<?php if ( $show_dots ) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
					<?php if ( $show_arrows ) : ?>
					<div class="elementor-swiper-button elementor-swiper-button-prev">
						<svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-left" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"><path d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z"></path></svg>
						<span class="elementor-screen-only"><?php esc_html_e( 'Previous', 'wbcom-essential' ); ?></span>
					</div>
					<div class="elementor-swiper-button elementor-swiper-button-next">
						<svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-right" viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg"><path d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z"></path></svg>
						<span class="elementor-screen-only"><?php esc_html_e( 'Next', 'wbcom-essential' ); ?></span>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
		// remove_filter( 'bp_nouveau_get_loop_classes', $loop_classes );
		// remove_filter( 'bp_current_component', $current_component );
	}

}
