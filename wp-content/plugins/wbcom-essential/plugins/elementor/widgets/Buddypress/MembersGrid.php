<?php
/**
 * Elementor member grid widget.
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
 * Elementor member grid widget.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */
class MembersGrid extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'member-grid', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/member-grid.css', array(), WBCOM_ESSENTIAL_VERSION );
		// wp_register_style( 'style-handle', 'path/to/file.CSS' );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-members-grid';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Members Grid', 'wbcom-essential' );
	}

	/**
	 * Get icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
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
		return array( 'members', 'grid', 'users', 'buddypress', 'cards' );
	}

	/**
	 * Get dependent styles.
	 */
	public function get_style_depends() {
		return array( 'member-grid' );
	}

	/**
	 * Register elememnor members grid controls.
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
			'columns',
			array(
				'label'   => esc_html__( 'Columns', 'wbcom-essential' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'three',
				'options' => array(
					'three' => '3',
					'four'  => '4',
				),
			)
		);

		if ( _is_theme_active( 'REIGN' ) ) {

			$this->add_control(
				'rg-mem-grid-layout',
				array(
					'label'   => esc_html__( 'Layout', 'wbcom-essential' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'wbtm-member-directory-type-2',
					'options' => array(
						'wbtm-member-directory-type-1' => 'Layout 1',
						'wbtm-member-directory-type-2' => 'Layout 2',
						'wbtm-member-directory-type-3' => 'Layout 3',
						'wbtm-member-directory-type-4' => 'Layout 4',
					),
				)
			);
		}

		$this->end_controls_section();

		do_action( 'reign_wp_menu_elementor_controls', $this );

	}

	/**
	 * Render elementor members grid widget.
	 */
	protected function render() {
		// Check if BuddyPress is active before rendering.
		if ( ! function_exists( 'buddypress' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<p>' . esc_html__( 'BuddyPress is required for this widget.', 'wbcom-essential' ) . '</p>';
			}
			return;
		}

		parent::render();
		$settings = $this->get_settings_for_display();

		$current_component = static function () {
			return 'members';
		};

		add_filter( 'wbcom_essential/has_template/pre', '__return_true' );

		add_filter( 'bp_current_component', $current_component );

		apply_filters( 'wbcom_essential/members-loop/before/template', $settings );

		add_filter( 'bp_members_pagination_count', '__return_zero' );
		add_filter( 'bp_get_members_pagination_links', '__return_zero' );

		$active_template       = get_option( '_bp_theme_package_id' );
		$member_directory_type = isset( $settings['rg-mem-grid-layout'] ) ? $settings['rg-mem-grid-layout'] : '';
		$img_class             = '';
		if ( 'wbtm-member-directory-type-4' === $member_directory_type ) {
			$img_class = 'img-card';
		}

		$col_class = isset( $settings['columns'] ) ? $settings['columns'] : 'three';

		$query_string = '&type=' . $settings['type'] . '&per_page=' . $settings['total'] . '&max=' . $settings['total'];
		?>

		<div id="buddypress" class="buddypress-wrap bp-dir-hori-nav members wbcom-members-grid-widget">
			<?php if ( 'legacy' === $active_template ) : ?>
				<div class="screen-content">
					<div id="members-dir-list" class="members dir-list" data-bp-list="">
						<?php
						wbcom_essential_get_template(
							'members/members-loop.php',
							array(
								'query_string'          => $query_string,
								'column_class'          => $col_class,
								'member_directory_type' => $member_directory_type,
							),
							'reign/buddypress/legacy'
						);
						?>
					</div>
				</div>
			<?php elseif ( 'nouveau' === $active_template ) : ?>
				<?php bp_nouveau_before_members_directory_content(); ?>
				<div class="screen-content">
					<div id="members-dir-list" class="members dir-list" data-bp-list="">
				<?php

				if ( _is_theme_active( 'BuddyX' ) || _is_theme_active( 'BuddyxPro' ) ) {

					$loop_classes = static function () use ( $settings ) {
						return array(
							'item-list',
							'members-list',
							'bp-list',
							'grid',
							_get_column_class( $settings['columns'] ),
							_get_column_class( $settings['columns'], 'tablet' ),
							_get_column_class( $settings['columns'], 'mobile' ),
						);
					};

					add_filter( 'bp_nouveau_get_loop_classes', $loop_classes );

					wbcom_essential_get_template(
						'members/members-loop.php',
						array(
							'query_string' => $query_string,
						),
						'buddyx/buddypress'
					);

					remove_filter( 'bp_nouveau_get_loop_classes', $loop_classes );

				} elseif ( _is_theme_active( 'REIGN' ) ) {
						wbcom_essential_get_template(
							'members/members-loop.php',
							array(
								'query_string'          => $query_string,
								'column_class'          => $col_class,
								'member_directory_type' => $member_directory_type,
								'img_class'             => $img_class,
							),
							'reign/buddypress/nouveau'
						);
				} else {
					$loop_classes = static function () use ( $settings ) {
						return array(
							'item-list',
							'members-list',
							'bp-list',
							'grid',
							_get_column_class( $settings['columns'] ),
							_get_column_class( $settings['columns'], 'tablet' ),
							_get_column_class( $settings['columns'], 'mobile' ),
						);
					};

					add_filter( 'bp_nouveau_get_loop_classes', $loop_classes );

					wbcom_essential_get_template(
						'members/members-loop.php',
						array(
							'query_string' => $query_string,
						),
						'buddypress'
					);

					remove_filter( 'bp_nouveau_get_loop_classes', $loop_classes );
				}
				?>
			</div>
				<?php bp_nouveau_after_members_directory_content(); ?>
		</div>
		<?php endif; ?>
		</div>

				<?php
				// remove_filter( 'bp_nouveau_get_loop_classes', $loop_classes );
				remove_filter( 'bp_current_component', $current_component );

				apply_filters( 'wbcom_essential/members-loop/after/template', $settings );

				remove_filter( 'bp_members_pagination_count', '__return_zero' );
				remove_filter( 'bp_get_members_pagination_links', '__return_zero' );
				remove_filter( 'wbcom_essential/has_template/pre', '__return_true' );
	}

}
