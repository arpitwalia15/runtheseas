<?php
/**
 * Add Elementor Hooks
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/hooks
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR;

use Elementor\Utils;
use ELEMENTOR\Controls_Manager;
use WBCOM_ESSENTIAL\ELEMENTOR\Widgets\QueryControl\Group_Control_Posts;
use WBCOM_ESSENTIAL\ELEMENTOR\Widgets\QueryControl\Query;

defined( 'ABSPATH' ) || die();

/**
 * Add Elementor Hooks
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/hooks
 */
class ElementorHooks {

	/**
	 * Query control ID constant.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	const QUERY_CONTROL_ID = 'query';

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var ElementorHooks
	 */
	public static $instance;

	/**
	 * ElementorHooks constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'categories_registered' ) );
		add_action( 'elementor/widgets/register', array( $this, 'widgets_registered' ) );
		add_action( 'elementor/controls/controls_registered', array( $this, 'register_controls' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_css' ) );
		add_action( 'elementor/editor/after_save', array( $this, 'save_buddypress_options' ), 10, 2 );

		add_action(
			'wp',
			function () {
				if ( is_singular() ) {
					$meta     = get_post_meta( get_the_ID(), '_elementor_controls_usage', true );
					$elements = array();

					$register_widgets = Plugin::get_instance()->get_elements();

					foreach ( $register_widgets as $widget ) {
						if ( ! isset( $widget['template'] ) ) {
							$elements[ $widget['name'] ] = true;
						}
					}

					if ( is_array( $meta ) && count( array_intersect_key( $elements, $meta ) ) > 0 ) {
						add_filter( 'buddy_builder/has_template/pre', '__return_true' );
					}
				}
			}
		);
	}

	/**
	 * Get instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return ElementorHooks The single instance of the class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register elementor category.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function categories_registered() {
		global $post;

		\Elementor\Plugin::instance()->elements_manager->add_category(
			'wbcom-elements',
			array(
				'title' => __( 'Wbcom Essential', 'wbcom-essential' ),
				'icon'  => 'font',
			),
			1
		);
	}

	/**
	 * Register elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function widgets_registered() {
		$elementor = \Elementor\Plugin::instance();

		if ( isset( $elementor->widgets_manager ) && method_exists( $elementor->widgets_manager, 'register' ) ) {

			$elements = \WBCOM_ESSENTIAL\ELEMENTOR\Plugin::get_instance()->get_elements();
			foreach ( $elements as $k => $element ) {
				if ( class_exists( 'Youzify' ) ) {
					if ( strpos( $k, 'Buddypress/MembersGrid' ) !== false || strpos( $k, 'Buddypress/GroupGrid' ) !== false ) {
						continue;
					}
				}
				if ( $template_file = $this->get_element_path( $element['template_base_path'] . $k ) ) {
					require_once $template_file;
					$class_name = $element['class_base_namespace'] . $element['class'];
					$elementor->widgets_manager->register( new $class_name() );
				}
			}
		}
	}

	/**
	 * Register Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_controls() {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;

		$controls_manager->add_group_control( Group_Control_Posts::get_type(), new Group_Control_Posts() );

		$controls_manager->register( new Query() );
	}

	/**
	 * Sync elementor widget options with customizer.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int   $post_id     Post ID.
	 * @param array $editor_data Editor Data.
	 *
	 * @return void
	 */
	public function save_buddypress_options( $post_id, $editor_data ) {
		// Check if function exists to prevent fatal error
		if ( ! function_exists( 'bpb_get_settings' ) ) {
			return;
		}
		
		$settings = bpb_get_settings();
		$save     = false;

		foreach ( $settings['templates'] as $template ) {
			if ( (int) $template === (int) $post_id ) {
				$save = true;
			}
		}

		if ( $save ) {
			$document = \Elementor\Plugin::instance()->documents->get( $post_id );

			if ( $document ) {
				\Elementor\Plugin::instance()->db->iterate_data(
					$document->get_elements_data(),
					static function ( $element ) {
						if ( empty( $element['widgetType'] ) || 'widget' !== $element['elType'] ) {
							return $element;
						}

						if ( 'bpb-members-directory-list' === $element['widgetType'] ) {
							$listing_columns = bpb_get_listing_columns();

							$listing_columns['members_directory'] = array(
								'desktop' => isset( $element['settings']['columns'] ) ? $element['settings']['columns'] : '3',
								'tablet'  => isset( $element['settings']['columns_tablet'] ) ? $element['settings']['columns_tablet'] : '2',
								'mobile'  => isset( $element['settings']['columns_mobile'] ) ? $element['settings']['columns_mobile'] : '1',
							);

							bpb_update_listing_columns( $listing_columns );
						}

						if ( 'bpb-groups-directory-list' === $element['widgetType'] ) {
							$listing_columns = bpb_get_listing_columns();

							$listing_columns['groups_directory'] = array(
								'desktop' => isset( $element['settings']['columns'] ) ? $element['settings']['columns'] : '3',
								'tablet'  => isset( $element['settings']['columns_tablet'] ) ? $element['settings']['columns_tablet'] : '2',
								'mobile'  => isset( $element['settings']['columns_mobile'] ) ? $element['settings']['columns_mobile'] : '1',
							);

							bpb_update_listing_columns( $listing_columns );
						}

						if ( 'bpb-profile-member-navigation' === $element['widgetType'] && isset( $element['settings']['show_home_tab'] ) ) {
							$bp_appearance = bpb_get_appearance();

							$bp_appearance['user_front_page'] = 'yes' === $element['settings']['show_home_tab'] ? 1 : 0;

							bpb_update_appearance( $bp_appearance );
						}

						if ( 'bpb-profile-group-navigation' === $element['widgetType'] && isset( $element['settings']['show_home_tab'] ) ) {
							$bp_appearance = bpb_get_appearance();

							$bp_appearance['group_front_page'] = 'yes' === $element['settings']['show_home_tab'] ? 1 : 0;

							bpb_update_appearance( $bp_appearance );
						}

						return $element;
					}
				);
			}
		}
	}

	/**
	 * Enqueue Elementor Editor CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function editor_css() {
		wp_enqueue_style(
			'stax-elementor-panel-style',
			WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/icons.css',
			null,
			WBCOM_ESSENTIAL_VERSION
		);

		wp_enqueue_style(
			'stax-elementor-panel-label-style',
			WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/label.css',
			null,
			WBCOM_ESSENTIAL_VERSION
		);
	}

	/**
	 * Get widget template path.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $file_path File Path.
	 *
	 * @return bool|string Returns the template file path if readable, false otherwise.
	 */
	public function get_element_path( $file_path ) {
		$template_file = $file_path . '.php';
		if ( $template_file && is_readable( $template_file ) ) {
			return $template_file;
		}

		return false;
	}

	/**
	 * Added Exclude Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param \Elementor\Widget_Base $widget Widget instance.
	 *
	 * @return void
	 */
	public static function add_exclude_controls( $widget ) {
		$widget->add_control(
			'exclude',
			array(
				'label'       => __( 'Exclude', 'wbcom-essential' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => array(
					'current_post'     => __( 'Current Post', 'wbcom-essential' ),
					'manual_selection' => __( 'Manual Selection', 'wbcom-essential' ),
				),
				'label_block' => true,
			)
		);

		$widget->add_control(
			'exclude_ids',
			array(
				'label'       => _x( 'Search & Select', 'Posts Query Control', 'wbcom-essential' ),
				'type'        => self::QUERY_CONTROL_ID,
				'post_type'   => '',
				'options'     => array(),
				'label_block' => true,
				'multiple'    => true,
				'filter_type' => 'by_id',
				'condition'   => array(
					'exclude' => 'manual_selection', // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control condition, not a WP_Query parameter.
				),
			)
		);
	}

	/**
	 * Get query args.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $control_id Control ID.
	 * @param array  $settings   Widget settings.
	 *
	 * @return array Query arguments.
	 */
	public static function get_query_args( $control_id, $settings ) {
		$defaults = array(
			$control_id . '_post_type' => 'post',
			$control_id . '_posts_ids' => array(),
			'orderby'                  => 'date',
			'order'                    => 'desc',
			'posts_per_page'           => 3,
			'offset'                   => 0,
		);

		$settings = wp_parse_args( $settings, $defaults );

		$post_type = $settings[ $control_id . '_post_type' ];

		$query_args = array(
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish', // Hide drafts/private posts for admins.
		);

		if ( 'by_id' === $post_type ) {
			$query_args['post_type'] = 'any';
			$query_args['post__in']  = $settings[ $control_id . '_posts_ids' ];

			if ( empty( $query_args['post__in'] ) ) {
				// If no selection - return an empty query.
				$query_args['post__in'] = array( 0 );
			}
		} else {
			$query_args['post_type']      = $post_type;
			$query_args['posts_per_page'] = $settings['posts_per_page'];
			$query_args['tax_query']      = array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Required for taxonomy filtering.

			if ( 0 < $settings['offset'] ) {
				// Due to a WordPress bug, the offset will be set later.
				// @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
				$query_args['offset_to_fix'] = $settings['offset'];
			}

			$taxonomies = get_object_taxonomies( $post_type, 'objects' );

			foreach ( $taxonomies as $object ) {
				$setting_key = $control_id . '_' . $object->name . '_ids';

				if ( ! empty( $settings[ $setting_key ] ) ) {
					$query_args['tax_query'][] = array(
						'taxonomy' => $object->name,
						'field'    => 'term_id',
						'terms'    => $settings[ $setting_key ],
					);
				}
			}
		}

		if ( ! empty( $settings[ $control_id . '_authors' ] ) ) {
			$query_args['author__in'] = $settings[ $control_id . '_authors' ];
		}

		if ( ! empty( $settings['exclude'] ) ) {
			$post__not_in = array();
			if ( in_array( 'current_post', $settings['exclude'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading post_id in Elementor Ajax context, nonce verified by Elementor.
				if ( wp_doing_ajax() && ! empty( $_REQUEST['post_id'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Same as above.
					$post__not_in[] = absint( $_REQUEST['post_id'] );
				} elseif ( is_singular() ) {
					$post__not_in[] = get_queried_object_id();
				}
			}

			if ( in_array( 'manual_selection', $settings['exclude'] ) && ! empty( $settings['exclude_ids'] ) ) {
				$post__not_in = array_merge( $post__not_in, $settings['exclude_ids'] );
			}

			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- Required for excluding current post in Elementor widgets.
		$query_args['post__not_in'] = $post__not_in;
		}

		return $query_args;
	}
}