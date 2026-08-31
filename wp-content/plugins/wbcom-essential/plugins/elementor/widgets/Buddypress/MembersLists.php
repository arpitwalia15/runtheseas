<?php
/**
 * Elementor member lists widget.
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
 * Elementor member lists widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */
class MembersLists extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'members-lists', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/members-lists.css', array(), WBCOM_ESSENTIAL_VERSION );
		wp_register_script( 'members-lists', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/js/members-lists.js', array( 'jquery' ), WBCOM_ESSENTIAL_VERSION, true );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-members-lists';
	}

	/**
	 * Get title.
	 */
	public function get_title() {
		return esc_html__( 'Members Lists', 'wbcom-essential' );
	}

	/**
	 * Get icon.
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * Get dependent style.
	 */
	public function get_style_depends() {
		return array( 'members-lists' );
	}

	/**
	 * Get dependent script.
	 */
	public function get_script_depends() {
		return array( 'members-lists' );
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
		return array( 'members', 'list', 'users', 'buddypress', 'directory' );
	}

	/**
	 * Register elementor member lists widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			array(
				'label' => esc_html__( 'Layout', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'members_order',
			array(
				'label'   => esc_html__( 'Default Members Order', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'active',
				'options' => array(
					'newest'  => esc_html__( 'Newest', 'wbcom-essential' ),
					'popular' => esc_html__( 'Popular', 'wbcom-essential' ),
					'active'  => esc_html__( 'Active', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'profile_types',
			array(
				'label'    => __( 'Profile Types', 'wbcom-essential' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => $this->wbcom_essential_elementor_profile_types(),
			)
		);

		$this->add_control(
			'members_count',
			array(
				'label'   => esc_html__( 'Members Count', 'wbcom-essential' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 5,
				),
				'range'   => array(
					'px' => array(
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					),
				),
			)
		);

		$this->add_control(
			'row_space',
			array(
				'label'     => esc_html__( 'Row Space', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-members-list__item' => 'margin-bottom: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'   => __( 'Alignment', 'wbcom-essential' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'wbcom-essential' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
			)
		);

		$this->add_control(
			'switch_more',
			array(
				'label'   => esc_html__( 'Show All Members Link', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_filter',
			array(
				'label'   => esc_html__( 'Show Filter Types', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_avatar',
			array(
				'label'   => esc_html__( 'Show Avatar', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_name',
			array(
				'label'   => esc_html__( 'Show Name', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_status',
			array(
				'label'   => esc_html__( 'Show Online Status', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'heading_text',
			array(
				'label'       => __( 'Heading Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Members', 'wbcom-essential' ),
				'placeholder' => __( 'Enter heading text', 'wbcom-essential' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'member_link_text',
			array(
				'label'       => __( 'Member Link Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'All Members', 'wbcom-essential' ),
				'placeholder' => __( 'Enter member link text', 'wbcom-essential' ),
				'label_block' => true,
				'condition'   => array(
					'switch_more' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => esc_html__( 'Box', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'box_border',
				'label'       => __( 'Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .wbcom-essential-members',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '4',
					'right'  => '4',
					'bottom' => '4',
					'left'   => '4',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-members' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'background_color',
				'label'    => __( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-members',
			)
		);

		$this->add_control(
			'separator_all',
			array(
				'label'     => __( 'All Members Link', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'extra_color',
			array(
				'label'     => __( 'All Members Link Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-block-header__extra a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_filter',
			array(
				'label'     => __( 'Filter Types', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'filter_border_style',
			array(
				'label'   => __( 'Border Type', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => array(
					'solid'  => __( 'Solid', 'wbcom-essential' ),
					'dashed' => __( 'Dashed', 'wbcom-essential' ),
					'dotted' => __( 'Dotted', 'wbcom-essential' ),
					'double' => __( 'Double', 'wbcom-essential' ),
					'none'   => __( 'None', 'wbcom-essential' ),
				),
			)
		);

		$this->add_control(
			'filter_border_color',
			array(
				'label'     => __( 'Border Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-members div.item-options' => 'border-bottom-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			array(
				'label'     => esc_html__( 'Avatar', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'switch_avatar' => 'yes',
				),
			)
		);

		$this->add_control(
			'avatar_width',
			array(
				'label'   => __( 'Size', 'wbcom-essential' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 40,
				),
				'range'   => array(
					'px' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					),
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
				'selector'    => '{{WRAPPER}} .wbcom-essential-members-list__avatar img',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'avatar_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-members-list__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_control(
			'avatar_opacity',
			array(
				'label'     => __( 'Opacity (%)', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-members-list__avatar img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'avatar_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-members-list--align-left .wbcom-essential-members-list__item .wbcom-essential-members-list__avatar'  => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .wbcom-essential-members-list--align-righ .wbcom-essential-members-list__item .wbcom-essential-members-list__avatar'  => 'margin-left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'separator_online_status',
			array(
				'label'     => __( 'Online Status', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'online_status_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1CD991',
				'selectors' => array(
					'{{WRAPPER}} .member-status.online' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'online_status_width',
			array(
				'label'      => __( 'Size', 'wbcom-essential' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 5,
						'max'  => 30,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 13,
				),
				'selectors'  => array(
					'{{WRAPPER}} .member-status.online' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'online_status_border',
				'label'       => __( 'Online Status Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .member-status.online',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'online_status_border_radius',
			array(
				'label'      => __( 'Online Status Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .member-status.online' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_name',
			array(
				'label'     => __( 'Name', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'switch_name' => 'yes',
				),
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => __( 'Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#122B46',
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-members-list__name a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .wbcom-essential-members-list__name a',
				'global' => [
			'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_ACCENT,
		],
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get elementor profile types.
	 */
	public function wbcom_essential_elementor_profile_types() {

		$profile_types      = bp_get_member_types( array(), 'objects' );
		$profile_types_data = array();
		foreach ( $profile_types as $profile_type ) :
			if ( ! empty( $profile_type->name ) ) {
				$profile_types_data[ $profile_type->name ] = $profile_type->labels['singular_name'];
			}
		endforeach;

		return $profile_types_data;

	}

	/**
	 * Render elementor member lists widget.
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
		$type     = $settings['members_order'];

		$avatar = array(
			'type'  => 'full',			
			'width' => ( ! empty( $settings['avatar_width']['size'] ) ) ? esc_attr( $settings['avatar_width']['size'] ) : '',
			'class' => 'avatar',
		);

		global $members_template;

		?>
		<div class="wbcom-essential-members">

			<?php if ( ( '' !== $settings['member_link_text'] ) || ( '' !== $settings['heading_text'] ) ) { ?>
				<div class="wbcom-essential-block-header flex align-items-center">
					<div class="wbcom-essential-block-header__title"><h3><?php echo esc_html( $settings['heading_text'] ); ?></h3></div>
					<?php if ( $settings['switch_more'] ) : ?>
						<div class="wbcom-essential-block-header__extra push-right">
							<?php if ( '' !== $settings['member_link_text'] ) { ?>
								<a href="<?php bp_members_directory_permalink(); ?>"
									class="count-more"><?php echo esc_html( $settings['member_link_text'] ); ?><i
									class="eicon-chevron-right"></i></a>
							<?php } ?>
						</div>
					<?php endif; ?>
				</div>
			<?php } ?>

			<?php
			$members_type = array(
				'active'  => __( 'active', 'wbcom-essential' ),
				'popular' => __( 'popular', 'wbcom-essential' ),
				'newest'  => __( 'newest', 'wbcom-essential' ),
			);
			?>

			<?php if ( $settings['switch_filter'] ) : ?>
				<div class="item-options border-<?php echo esc_attr( $settings['filter_border_style'] ); ?>">
					<?php foreach ( $members_type as $k => $mtype ) { ?>
						<a href="#" id="wbcom-essential-<?php echo esc_attr( $k ); ?>-members"
							class="wbcom-essential-members__tab <?php echo $k === $type ? esc_attr( 'selected' ) : ''; ?>"
							data-type="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $mtype ); ?></a>
					<?php } ?>
				</div>
			<?php endif; ?>

			<div class="bbel-list-flow">
				<?php
				foreach ( $members_type as $k => $mtype ) {

					// Query members args.
					$members_args = array(
						'user_id'         => 0,
						'type'            => esc_attr( $k ),
						'per_page'        => esc_attr( $settings['members_count']['size'] ),
						'max'             => esc_attr( $settings['members_count']['size'] ),
						'member_type'     => ! empty( $settings['profile_types'] ) ? $settings['profile_types'] : 0,
						'populate_extras' => true,
						'search_terms'    => false,
					);

					// Query members.
					if ( bp_has_members( $members_args ) ) :
						?>

						<div class="wbcom-essential-members-list wbcom-essential-members-list--<?php echo esc_attr( $k ); ?> wbcom-essential-members-list--align-<?php echo esc_attr( $settings['alignment'] ); ?> <?php echo $k === $type ? esc_attr( 'active' ) : ''; ?>">

							<?php $this->add_render_attribute( 'wbcom-essential-member', 'class', 'wbcom-essential-members-list__item' ); ?>

							<?php
							while ( bp_members() ) :
								bp_the_member();
								?>

								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wbcom-essential-member' ) ); ?>>
									<?php if ( $settings['switch_avatar'] ) : ?>
										<div class="wbcom-essential-members-list__avatar">
											<a href="<?php bp_member_permalink(); ?>">
												<?php bp_member_avatar( $avatar ); ?>
											</a>
										</div>
									<?php endif; ?>

									<?php if ( $settings['switch_name'] ) : ?>
										<div class="wbcom-essential-members-list__name fn"><a
													href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a></div>
									<?php endif; ?>
									<?php
									$current_time = current_time( 'mysql', 1 );
									$diff         = strtotime( $current_time ) - strtotime( $members_template->member->last_activity );
									if ( $diff < 300 && $settings['switch_status'] ) { // 5 minutes  =  5 * 60
										?>
										<span class="member-status online"></span>
									<?php } ?>
								</div>

							<?php endwhile; ?>
						</div>
					<?php else : ?>

						<div class="wbcom-essential-members-list wbcom-essential-members-list--<?php echo esc_attr( $mtype ); ?> wbcom-essential-no-data wbcom-essential-no-data--members <?php echo $mtype === $type ? 'active' : ''; ?>">
							<img class="wbcom-essential-no-data__image"
								src="<?php echo esc_url( WBCOM_ESSENTIAL_ASSETS_URL ); ?>images/no-data-found.svg"
								alt="Members"/>
							<br />
							<div><?php echo esc_html__( 'Sorry, no members were found.', 'wbcom-essential' ); ?></div>
						</div>

					<?php endif; ?>

				<?php } ?>
			</div>

		</div>
		<?php
	}

}
