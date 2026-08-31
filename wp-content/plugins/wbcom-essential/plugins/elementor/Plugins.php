<?php
/**
 * Add plugin main class.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR;

defined( 'ABSPATH' ) || die();

/**
 * Plugin class.
 *
 * @since 1.0.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Plugin {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Plugin
	 */
	public static $instance;

	/**
	 * Modules.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var object
	 */
	public $modules = array();

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_name;

	/**
	 * The plugin version number.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_version;

	/**
	 * The minimum Elementor version number required.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $minimum_elementor_version = '3.5.0';

	/**
	 * The plugin directory.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_path;

	/**
	 * The plugin URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_url;

	/**
	 * The plugin assets URL.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $plugin_assets_url;

	/**
	 * The plugin directory.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var string
	 */
	public static $widget_path;

	/**
	 * The plugin classes.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @var string
	 */
	private $classes_aliases = array(
		'WBCOM_ESSENTIAL\ELEMENTOR\PanelPostsControl\Controls\Group_Control_Posts' => 'WBCOM_ESSENTIAL\ELEMENTOR\Widgets\QueryControl\Group_Control_Posts',
		'WBCOM_ESSENTIAL\ELEMENTOR\PanelPostsControl\Controls\Query' => 'WBCOM_ESSENTIAL\ELEMENTOR\Widgets\QueryControl\Query',
	);

	/**
	 * Disables class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Security error: Direct cloning of this class is not allowed.', 'wbcom-essential' ), esc_attr( WBCOM_ESSENTIAL_VERSION ) );
	}

	/**
	 * Disables unserializing of the class.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Security error: Unserializing of this class is not allowed.', 'wbcom-essential' ), esc_attr( WBCOM_ESSENTIAL_VERSION ) );
	}


	/**
	 * Elementor.
	 *
	 * @return \Elementor\Plugin
	 */
	public static function elementor() {
		return \Elementor\Plugin::instance();
	}

	/**
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @return Plugin An instance of the class.
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {

		add_action( 'plugins_loaded', array( $this, 'wbcom_essential_load_plugin' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_css' ), 12 );
		define( 'WBCOM_ESSENTIAL_ELEMENTOR_URL', WBCOM_ESSENTIAL_URL . 'plugins/elementor/' );
		define( 'WBCOM_ESSENTIAL_ELEMENTOR_PATH', WBCOM_ESSENTIAL_PATH . 'plugins/elementor/' );
		define( 'WBCOM_ESSENTIAL_ELEMENTOR_WIDGET_PATH', WBCOM_ESSENTIAL_ELEMENTOR_PATH . 'widgets/' );
	}

	/**
	 * Checks Elementor version compatibility.
	 *
	 * First checks if Elementor is installed and active,
	 * then checks Elementor version compatibility.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wbcom_essential_load_plugin() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			// Use the consolidated notice from wbcom-essential-elementor.php
			return;
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->define_constants();
		$this->add_hooks();
		$this->includes();
		do_action( 'wbcom_essential/init' );
	}

	/**
	 * Autoload classes based on namespace.
	 *
	 * @param string $class Name of class.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function autoload( $class ) {
		// Return if WBCOM_ESSENTIAL name space is not set.
		if ( false === strpos( $class, __NAMESPACE__ ) ) {
			return;
		}
		/**
		 * Prepare filename from class name
		 */
		$filename = str_replace(
			array( __NAMESPACE__ . '\\', '\\', '_' ),
			array(
				'',
				DIRECTORY_SEPARATOR,
				'-',
			),
			$class
		);

		$filename = __DIR__ . '/' . strtolower( $filename ) . '.php';
		
		if ( file_exists( $filename ) && is_readable( $filename ) ) {
			require_once $filename;
		}
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function define_constants() {
		self::$plugin_path       = trailingslashit( plugin_dir_path( WBCOM_ESSENTIAL_PATH ) );
		self::$plugin_url        = trailingslashit( plugin_dir_url( WBCOM_ESSENTIAL_PATH ) );
		self::$plugin_assets_url = trailingslashit( self::$plugin_url . 'assets' );
		self::$widget_path       = trailingslashit( self::$plugin_path . '/plugins/elementor/widgets' );
	}

	/**
	 * Adds required hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function add_hooks() {
		include_once WBCOM_ESSENTIAL_PATH . 'plugins/elementor/hooks/ElementorHooks.php';

		\WBCOM_ESSENTIAL\ELEMENTOR\ElementorHooks::get_instance();
	}

		/**
		 * Adds required hooks.
		 *
		 * @since 1.0.0
		 * @access private
		 */
	private function includes() {}

	/**
	 * Elements
	 *
	 * @return array
	 */
	public function get_elements() {
		$elements = array();

		if ( class_exists( 'BuddyPress' ) ) {

			$elements['Buddypress/HeaderBar'] = array(
				'name'  => 'wbcom-header-bar',
				'class' => 'Buddypress\HeaderBar',
			);

			$elements['Buddypress/ProfileCompletion'] = array(
				'name'  => 'wbcom-profile-completion',
				'class' => 'Buddypress\ProfileCompletion',
			);

			$elements['Buddypress/MembersLists'] = array(
				'name'  => 'wbcom-members-lists',
				'class' => 'Buddypress\MembersLists',
			);

			$elements['Buddypress/MembersGrid'] = array(
				'name'  => 'wbcom-members-grid',
				'class' => 'Buddypress\MembersGrid',
			);

			$elements['Buddypress/MemberCarousel'] = array(
				'name'  => 'wbcom-members-carousel',
				'class' => 'Buddypress\MemberCarousel',
			);

			if ( bp_is_active( 'groups' ) ) {

				$elements['Buddypress/GroupsLists'] = array(
					'name'  => 'wbcom-groups-lists',
					'class' => 'Buddypress\GroupsLists',
				);

				$elements['Buddypress/GroupGrid']     = array(
					'name'  => 'wbcom-groups-grid',
					'class' => 'Buddypress\GroupGrid',
				);
				$elements['Buddypress/GroupCarousel'] = array(
					'name'  => 'wbcom-group-carousel',
					'class' => 'Buddypress\GroupCarousel',
				);
			}

			$elements['Buddypress/DashboardIntro'] = array(
				'name'  => 'wbcom-dashboard-intro',
				'class' => 'Buddypress\DashboardIntro',
			);

			if ( class_exists( 'bbPress' ) ) {

				$elements['Buddypress/Forums'] = array(
					'name'  => 'wbcom-forums',
					'class' => 'Buddypress\Forums',
				);

				$elements['Buddypress/ForumsActivity'] = array(
					'name'  => 'wbcom-forums-activity',
					'class' => 'Buddypress\ForumsActivity',
				);

			}
		}

		$elements['General/Branding'] = array(
			'name'  => 'wbcom-branding',
			'class' => 'General\Branding',
		);

		if ( _is_theme_active( 'REIGN' ) ) {
			$elements['General/NotificationArea'] = array(
				'name'  => 'wbcom-notification-area',
				'class' => 'General\NotificationArea',
			);
		}

		$elements['General/PostsRevolution'] = array(
			'name'  => 'wbcom-posts-revolution',
			'class' => 'General\PostsRevolution',
		);

		$elements['General/PostsCarousel'] = array(
			'name'  => 'wbcom-posts-carousel',
			'class' => 'General\PostsCarousel',
		);

		$elements['General/PostsTicker'] = array(
			'name'  => 'wbcom-posts-ticker',
			'class' => 'General\PostsTicker',
		);

		$elements['General/LoginForm'] = array(
			'name'  => 'wbcom-login-form',
			'class' => 'General\LoginForm',
		);

		$elements['General/Heading'] = array(
			'name'  => 'wbcom-heading',
			'class' => 'General\Heading',
		);

		$elements['General/DropdownButton'] = array(
			'name'  => 'wbcom-dropdown-button',
			'class' => 'General\DropdownButton',
		);

		$elements['General/Accordion'] = array(
			'name'  => 'wbcom-accordion',
			'class' => 'General\Accordion',
		);

		$elements['General/SmartMenu'] = array(
			'name'  => 'wbcom-smart-menu',
			'class' => 'General\SmartMenu',
		);

		$elements['General/Tabs'] = array(
			'name'  => 'wbcom-tabs',
			'class' => 'General\Tabs',
		);

		$elements['General/Slider'] = array(
			'name'  => 'wbcom-slider',
			'class' => 'General\Slider',
		);

		$elements['General/PostSlider'] = array(
			'name'  => 'wbcom-post-slider',
			'class' => 'General\PostSlider',
		);

		$elements['General/PostCarousel'] = array(
			'name'  => 'wbcom-post-carousel',
			'class' => 'General\PostCarousel',
		);

		$elements['General/PostTimeline'] = array(
			'name'  => 'wbcom-post-timeline',
			'class' => 'General\PostTimeline',
		);

		$elements['General/PortfolioGrid'] = array(
			'name'  => 'wbcom-portfolio-grid',
			'class' => 'General\PortfolioGrid',
		);

		$elements['General/TeamCarousel'] = array(
			'name'  => 'wbcom-team-carousel',
			'class' => 'General\TeamCarousel',
		);

		$elements['General/PricingTable'] = array(
			'name'  => 'wbcom-pricing-table',
			'class' => 'General\PricingTable',
		);

		$elements['General/FlipBox'] = array(
			'name'  => 'wbcom-flip-box',
			'class' => 'General\FlipBox',
		);

		$elements['General/SiteLogo'] = array(
			'name'  => 'wbcom-site-logo',
			'class' => 'General\SiteLogo',
		);

		$elements['General/Testimonial'] = array(
			'name'  => 'wbcom-testimonial',
			'class' => 'General\Testimonial',
		);

		$elements['General/TestimonialCarousel'] = array(
			'name'  => 'wbcom-testimonial-carousel',
			'class' => 'General\TestimonialCarousel',
		);

		$elements['General/Countdown'] = array(
			'name'  => 'wbcom-countdown',
			'class' => 'General\Countdown',
		);

		$elements['General/TextRotator'] = array(
			'name'  => 'wbcom-text-rotator',
			'class' => 'General\TextRotator',
		);

		$elements['General/ProgressBar'] = array(
			'name'  => 'wbcom-progress-bar',
			'class' => 'General\ProgressBar',
		);

		$elements['General/Timeline'] = array(
			'name'  => 'wbcom-timeline',
			'class' => 'General\Timeline',
		);

		$elements['General/Shape'] = array(
			'name'  => 'wbcom-shape',
			'class' => 'General\Shape',
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$elements['WooCommerce/UniversalProduct'] = array(
				'name'  => 'wbcom-universal-product',
				'class' => 'WooCommerce\UniversalProduct',
			);

			$elements['WooCommerce/ProductTab'] = array(
				'name'  => 'wbcom-product-tab',
				'class' => 'WooCommerce\ProductTab',
			);

			$elements['WooCommerce/AddBanner'] = array(
				'name'  => 'wbcom-add-banner',
				'class' => 'WooCommerce\AddBanner',
			);

			$elements['WooCommerce/WcTestimonial'] = array(
				'name'  => 'wbcom-wc-testimonial',
				'class' => 'WooCommerce\WcTestimonial',
			);

			$elements['WooCommerce/CustomerReview'] = array(
				'name'  => 'wbcom-customer-review',
				'class' => 'WooCommerce\CustomerReview',
			);
		}

		foreach ( $elements as &$element ) {
			$element['template_base_path']   = WBCOM_ESSENTIAL_ELEMENTOR_WIDGET_PATH;
			$element['class_base_namespace'] = '\WBCOM_ESSENTIAL\ELEMENTOR\Widgets\\';
		}

		return apply_filters( 'wbcom_essential/get_elements', $elements );
	}

	/**
	 * Enqueue Front CSS
	 */
	public function front_css() {
		wp_register_style(
			'wbcom-essential-elementor-css',
			WBCOM_ESSENTIAL_ASSETS_URL . 'css/wbcom-essential-elementor.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);

		wp_enqueue_style( 'wbcom-essential-elementor-css' );

		wp_register_style(
			'wbcom-animation-icons',
			WBCOM_ESSENTIAL_ASSETS_URL . 'css/animation.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);

		wp_enqueue_style( 'wbcom-animation-icons' );

		wp_register_style(
			'wbcom-essential-icons',
			WBCOM_ESSENTIAL_ASSETS_URL . 'css/wbe-icons.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);

		wp_enqueue_style( 'wbcom-essential-icons' );

		// Localize Scripts.
		$localizeargs = array(
			'woolentorajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'       => wp_create_nonce( 'woolentor_psa_nonce' ),
		);
		wp_localize_script( 'wbcom-widgets-scripts', 'woolentor_addons', $localizeargs );
	}
}

return Plugin::get_instance();
