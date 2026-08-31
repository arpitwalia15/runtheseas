<?php
/**
 * Elementor widget customer review.
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
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

/**
 * Customer Review.
 *
 * @since      3.7.1
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/woocommerce
 */
class CustomerReview extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'wb-wc-testimonial', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wc-testimonial.css', [], WBCOM_ESSENTIAL_VERSION );
		wp_register_style( 'wbcom-widgets', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/wbcom-widgets.css', [], WBCOM_ESSENTIAL_VERSION );

		wp_register_script( 'wbcom-widgets-scripts', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/wbcom-widgets-active.js', [ 'jquery' ], WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-customer-review';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Customer Review', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-comments';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return [
			'wbcom-widgets',
		];
	}

	/**
	 * Get categories.
	 */
	public function get_categories() {
		return [ 'wbcom-elements' ];
	}

	/**
	 * Get keywords.
	 */
	public function get_keywords() {
		return [ 'review', 'customer', 'product review', 'customer review' ];
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'review_content',
			array(
				'label' => __( 'Review', 'wbcom-essential' ),
			)
		);

			$this->add_control(
				'review_layout',
				array(
					'label'   => __( 'Style', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1' => __( 'Style One', 'wbcom-essential' ),
						'2' => __( 'Style Two', 'wbcom-essential' ),
						'3' => __( 'Style Three', 'wbcom-essential' ),
						'4' => __( 'Style Four', 'wbcom-essential' ),
					),
				)
			);

			$this->add_control(
				'review_type',
				array(
					'label'       => __( 'Review Type', 'wbcom-essential' ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'custom',
					'options'     => array(
						'custom' => __( 'Custom', 'wbcom-essential' ),
					),
				)
			);

			$repeater = new Repeater();

			$repeater->add_control(
				'client_name',
				array(
					'label'   => __( 'Name', 'wbcom-essential' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Carolina Monntoya', 'wbcom-essential' ),
				)
			);

			$repeater->add_control(
				'client_designation',
				array(
					'label'   => __( 'Designation', 'wbcom-essential' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Managing Director', 'wbcom-essential' ),
				)
			);

			$repeater->add_control(
				'client_rating',
				array(
					'label' => __( 'Client Rating', 'wbcom-essential' ),
					'type'  => Controls_Manager::NUMBER,
					'min'   => 1,
					'max'   => 5,
					'step'  => 1,
				)
			);

			$repeater->add_control(
				'client_image',
				array(
					'label' => __( 'Image', 'wbcom-essential' ),
					'type'  => Controls_Manager::MEDIA,
				)
			);

			$repeater->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'client_imagesize',
					'default'   => 'large',
					'separator' => 'none',
				)
			);

			$repeater->add_control(
				'client_say',
				array(
					'label'   => __( 'Client Say', 'wbcom-essential' ),
					'type'    => Controls_Manager::TEXTAREA,
					'default' => __( 'Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'wbcom-essential' ),
				)
			);

			$this->add_control(
				'review_list',
				array(
					'type'        => Controls_Manager::REPEATER,
					'condition'   => array(
						'review_type' => 'custom',
					),
					'fields'      => $repeater->get_controls(),
					'default'     => array(

						array(
							'client_name'        => __( 'Carolina Monntoya', 'wbcom-essential' ),
							'client_designation' => __( 'Managing Director', 'wbcom-essential' ),
							'client_rating'      => '5',
							'client_say'         => __( 'Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'wbcom-essential' ),
						),

						array(
							'client_name'        => __( 'Peter Rose', 'wbcom-essential' ),
							'client_designation' => __( 'Manager', 'wbcom-essential' ),
							'client_rating'      => '5',
							'client_say'         => __( 'Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'wbcom-essential' ),
						),

						array(
							'client_name'        => __( 'Gerald Gilbert', 'wbcom-essential' ),
							'client_designation' => __( 'Developer', 'wbcom-essential' ),
							'client_rating'      => '5',
							'client_say'         => __( 'Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'wbcom-essential' ),
						),
					),
					'title_field' => '{{{ client_name }}}',
				)
			);

		$this->end_controls_section();

		// Options.
		$this->start_controls_section(
			'review_option',
			array(
				'label' => __( 'Option', 'wbcom-essential' ),
			)
		);

			$this->add_responsive_control(
				'column',
				array(
					'label'        => esc_html__( 'Columns', 'wbcom-essential' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => '3',
					'options'      => array(
						'1'  => esc_html__( 'One', 'wbcom-essential' ),
						'2'  => esc_html__( 'Two', 'wbcom-essential' ),
						'3'  => esc_html__( 'Three', 'wbcom-essential' ),
						'4'  => esc_html__( 'Four', 'wbcom-essential' ),
						'5'  => esc_html__( 'Five', 'wbcom-essential' ),
						'6'  => esc_html__( 'Six', 'wbcom-essential' ),
						'7'  => esc_html__( 'Seven', 'wbcom-essential' ),
						'8'  => esc_html__( 'Eight', 'wbcom-essential' ),
						'9'  => esc_html__( 'Nine', 'wbcom-essential' ),
						'10' => esc_html__( 'Ten', 'wbcom-essential' ),
					),
					'label_block'  => true,
					'prefix_class' => 'wb-columns%s-',
				)
			);

			$this->add_control(
				'no_gutters',
				array(
					'label'        => esc_html__( 'No Gutters', 'wbcom-essential' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'wbcom-essential' ),
					'label_off'    => esc_html__( 'No', 'wbcom-essential' ),
					'return_value' => 'yes',
					'default'      => 'no',
				)
			);

			$this->add_responsive_control(
				'item_space',
				array(
					'label'      => esc_html__( 'Space', 'wbcom-essential' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'default'    => array(
						'unit' => 'px',
						'size' => 15,
					),
					'condition'  => array(
						'no_gutters!' => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} .wb-row > [class*="col-"]' => 'padding: 0  {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'item_bottom_space',
				array(
					'label'      => esc_html__( 'Bottom Space', 'wbcom-essential' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'default'    => array(
						'unit' => 'px',
						'size' => 30,
					),
					'condition'  => array(
						'no_gutters!' => 'yes',
					),
					'selectors'  => array(
						'{{WRAPPER}} .wb-row > [class*="col-"]' => 'margin-bottom:{{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_controls_section();

		// Style style start.
		$this->start_controls_section(
			'testimonial_area_style',
			array(
				'label' => __( 'Area', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_responsive_control(
				'testimonial_content_align',
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
					'selectors'    => array(
						'{{WRAPPER}} .wb-customer-testimonal' => 'text-align: {{VALUE}};',
					),
					'prefix_class' => 'wb-customer-align%s-',
					'separator'    => 'before',
				)
			);

			$this->add_responsive_control(
				'testimonial_area_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				array(
					'name'     => 'testimonial_area_background',
					'label'    => __( 'Background', 'wbcom-essential' ),
					'types'    => array( 'classic', 'gradient' ),
					'selector' => '{{WRAPPER}} .wb-customer-testimonal',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'testimonial_area_border',
					'label'    => __( 'Border', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wb-customer-testimonal',
				)
			);

			$this->add_responsive_control(
				'testimonial_area_border_radius',
				array(
					'label'      => __( 'Border Radius', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section();

		// Style image style start.
		$this->start_controls_section(
			'testimonial_image_style',
			array(
				'label' => __( 'Image', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'testimonial_image_border',
					'label'    => __( 'Border', 'wbcom-essential' ),
					'selector' => '{{WRAPPER}} .wb-customer-testimonal img',
				)
			);

			$this->add_responsive_control(
				'testimonial_image_border_radius',
				array(
					'label'     => esc_html__( 'Border Radius', 'wbcom-essential' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .wb-customer-testimonal img' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					),
				)
			);

		$this->end_controls_section(); // Style Testimonial image style end.

		// Style Testimonial name style start.
		$this->start_controls_section(
			'testimonial_name_style',
			array(
				'label' => __( 'Name', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'testimonial_name_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-customer-testimonal .clint-info h4' => 'color: {{VALUE}};',
						'{{WRAPPER}} .wb-review-style-2 .wb-customer-testimonal .clint-info h4:before' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'testimonial_name_typography',
					'selector' => '{{WRAPPER}} .wb-customer-testimonal .clint-info h4',
				)
			);

			$this->add_responsive_control(
				'testimonial_name_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal .clint-info h4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_responsive_control(
				'testimonial_name_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal .clint-info h4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section(); // Style Testimonial name style end.

		// Style Testimonial designation style start.
		$this->start_controls_section(
			'testimonial_designation_style',
			array(
				'label' => __( 'Designation', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'testimonial_designation_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-customer-testimonal .clint-info span' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'testimonial_designation_typography',
					'selector' => '{{WRAPPER}} .wb-customer-testimonal .clint-info span',
				)
			);

			$this->add_responsive_control(
				'testimonial_designation_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal .clint-info span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_responsive_control(
				'testimonial_designation_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal .clint-info span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section(); // Style Testimonial designation style end.

		// Style Testimonial designation style start.
		$this->start_controls_section(
			'testimonial_clientsay_style',
			array(
				'label' => __( 'Client say', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'testimonial_clientsay_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-customer-testimonal p' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'testimonial_clientsay_typography',
					'selector' => '{{WRAPPER}} .wb-customer-testimonal p',
				)
			);

			$this->add_responsive_control(
				'testimonial_clientsay_margin',
				array(
					'label'      => __( 'Margin', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

			$this->add_responsive_control(
				'testimonial_clientsay_padding',
				array(
					'label'      => __( 'Padding', 'wbcom-essential' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .wb-customer-testimonal p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'  => 'before',
				)
			);

		$this->end_controls_section(); // Style Testimonial designation style end.

		// Style Testimonial designation style start.
		$this->start_controls_section(
			'testimonial_clientrating_style',
			array(
				'label' => __( 'Rating', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_control(
				'testimonial_clientrating_color',
				array(
					'label'     => __( 'Color', 'wbcom-essential' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .wb-customer-testimonal .clint-info .rating' => 'color: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section(); // Style Testimonial designation style end.
	}


	protected function render( $instance = array() ) {

		$settings = $this->get_settings_for_display();
		$column   = $this->get_settings_for_display( 'column' );

		$this->add_render_attribute( 'review_area_attr', 'class', 'wb-customer-review wb-review-style-' . $settings['review_layout'] );

		$collumval = 'wb-col-6';
		if ( $column != '' ) {
			$collumval = 'wb-col-' . $column;
		}

		// Generate review.
		$review_list = array();
		if ( $settings['review_type'] === 'custom' ) {
			foreach ( $settings['review_list'] as $review ) {
				$review_list[] = [
					'image'       => Group_Control_Image_Size::get_attachment_image_html( $review, 'client_imagesize', 'client_image' ),
					'name'        => $review['client_name'],
					'designation' => $review['client_designation'],
					'ratting'     => $review['client_rating'],
					'message'     => $review['client_say'],
				];
			}
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor's get_render_attribute_string() handles escaping.
		echo '<div ' . $this->get_render_attribute_string( 'review_area_attr' ) . '>';
		echo '<div class="wb-row ' . esc_attr( $settings['no_gutters'] === 'yes' ? 'wb-gutters' : '' ) . '">';
		?>
			<?php foreach ( $review_list as $review ) : ?>
			<div class="<?php echo esc_attr( $collumval ); ?>">

				<?php if ( $settings['review_layout'] == 2 || $settings['review_layout'] == 3 ) : ?>

				<div class="wb-customer-testimonal">
					<?php
					if ( $review['image'] ) {
						echo wp_kses_post( $review['image'] );
					}
					?>
					<div class="content">
						<?php
						if ( ! empty( $review['message'] ) ) {
							echo '<p>' . esc_html( $review['message'] ) . '</p>';
						}
						?>
						<div class="clint-info">
							<?php
							if ( ! empty( $review['name'] ) ) {
								echo '<h4>' . esc_html( $review['name'] ) . '</h4>';
							}
							if ( ! empty( $review['designation'] ) ) {
								echo '<span>' . esc_html( $review['designation'] ) . '</span>';
							}

								// Rating
							if ( ! empty( $review['ratting'] ) ) {
								$this->ratting( $review['ratting'] );
							}
							?>
						</div>
					</div>
				</div>

				<?php elseif ( $settings['review_layout'] == 4 ) : ?>
				<div class="wb-customer-testimonal">
					<div class="content">
						<?php
						if ( ! empty( $review['message'] ) ) {
							echo '<p>' . esc_html( $review['message'] ) . '</p>';
						}
						?>
						<div class="triangle"></div>
					</div>
					<div class="clint-info">
						<?php
						if ( $review['image'] ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML from Elementor's Group_Control_Image_Size is safe.
							echo $review['image'];
						}

						if ( ! empty( $review['name'] ) ) {
							echo '<h4>' . esc_html( $review['name'] ) . '</h4>';
						}

						if ( ! empty( $review['designation'] ) ) {
							echo '<span>' . esc_html( $review['designation'] ) . '</span>';
						}

							// Rating
						if ( ! empty( $review['ratting'] ) ) {
							$this->ratting( $review['ratting'] );
						}

						?>
					</div>
				</div>

				<?php else : ?>
				<div class="wb-customer-testimonal">
					<div class="content">
						<?php
						if ( $review['image'] ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML from Elementor's Group_Control_Image_Size is safe.
							echo $review['image'];
						}
						?>
						<div class="clint-info">
							<?php
							if ( ! empty( $review['name'] ) ) {
								echo '<h4>' . esc_html( $review['name'] ) . '</h4>';
							}
							if ( ! empty( $review['designation'] ) ) {
								echo '<span>' . esc_html( $review['designation'] ) . '</span>';
							}

								// Rating
							if ( ! empty( $review['ratting'] ) ) {
								$this->ratting( $review['ratting'] );
							}

							?>
						</div>
					</div>
					<?php
					if ( ! empty( $review['message'] ) ) {
						echo '<p>' . esc_html( $review['message'] ) . '</p>';
					}
					?>
				</div>
			<?php endif; ?>

			</div>
				<?php
			endforeach;
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Hardcoded closing HTML tags.
			echo '</div></div>';
	}

	public function ratting( $ratting_num ) {
		if ( ! empty( $ratting_num ) ) {
			$rating          = $ratting_num;
			$rating_whole    = floor( $ratting_num );
			$rating_fraction = $rating - $rating_whole;
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- All strings are hardcoded HTML with no user data.
			echo '<ul class="rating">';
			for ( $i = 1; $i <= 5; $i++ ) {
				if ( $i <= $rating_whole ) {
					echo '<li><i class="fas fa-star"></i></li>';
				} elseif ( $rating_fraction != 0 ) {
						echo '<li><i class="fas fa-star-half-alt"></i></li>';
						$rating_fraction = 0;
				} else {
					echo '<li><i class="far fa-star empty"></i></li>';
				}
			}
			echo '</ul>';
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}