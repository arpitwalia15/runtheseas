<?php
/**
 * Blog Index Layout Settings.
 *
 * Registers the global option, section configurator, and admin settings
 * for blog archive / index page layouts (Grid, List, Magazine, Newspaper).
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/includes/blog-index
 * @since      4.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Wbcom_Blog_Index_Settings
 */
class Wbcom_Blog_Index_Settings {

	/**
	 * Layout choices.
	 *
	 * @var array
	 */
	private $layout_choices = array();

	/**
	 * Display type labels for the section configurator.
	 *
	 * @var array
	 */
	private $display_types = array();

	/**
	 * Default sections per layout.
	 *
	 * @var array
	 */
	private static $layout_defaults = array();

	/**
	 * Initialize hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_js' ) );
	}

	/**
	 * Lazy-load layout choices to avoid early textdomain calls.
	 *
	 * @return array
	 */
	public function get_layout_choices() {
		if ( empty( $this->layout_choices ) ) {
			$this->layout_choices = array(
				''          => __( 'None (Use Theme Default)', 'wbcom-essential' ),
				'grid'      => __( 'Grid (Card Layout)', 'wbcom-essential' ),
				'list'      => __( 'List (Horizontal Cards)', 'wbcom-essential' ),
				'magazine'  => __( 'Magazine (Multi-Section)', 'wbcom-essential' ),
				'newspaper' => __( 'Newspaper (Breaking News)', 'wbcom-essential' ),
			);
		}
		return $this->layout_choices;
	}

	/**
	 * Lazy-load display types.
	 *
	 * @return array
	 */
	public function get_display_types() {
		if ( empty( $this->display_types ) ) {
			$this->display_types = array(
				'posts_type1' => __( 'Hero + Sidebar', 'wbcom-essential' ),
				'posts_type2' => __( 'Featured + List', 'wbcom-essential' ),
				'posts_type3' => __( 'Grid (Columns)', 'wbcom-essential' ),
				'posts_type4' => __( 'Side by Side', 'wbcom-essential' ),
				'posts_type5' => __( 'Hero + Text Sidebar', 'wbcom-essential' ),
				'posts_type6' => __( 'Magazine (1+3+Rest)', 'wbcom-essential' ),
				'posts_type7' => __( 'Two Featured + List', 'wbcom-essential' ),
			);
		}
		return $this->display_types;
	}

	/**
	 * Get layout defaults.
	 *
	 * @return array
	 */
	public static function get_layout_defaults() {
		if ( empty( self::$layout_defaults ) ) {
			self::$layout_defaults = array(
				'magazine'  => array(
					array(
						'title'        => __( 'Featured', 'wbcom-essential' ),
						'category'     => '',
						'display_type' => 'posts_type1',
						'posts_count'  => 5,
					),
					array(
						'title'        => '',
						'category'     => '',
						'display_type' => 'posts_type3',
						'posts_count'  => 6,
					),
					array(
						'title'        => '',
						'category'     => '',
						'display_type' => 'posts_type4',
						'posts_count'  => 4,
					),
					array(
						'title'        => '',
						'category'     => '',
						'display_type' => 'posts_type6',
						'posts_count'  => 5,
					),
				),
				'newspaper' => array(
					array(
						'title'        => '',
						'category'     => '',
						'display_type' => 'posts_type3',
						'posts_count'  => 6,
					),
					array(
						'title'        => '',
						'category'     => '',
						'display_type' => 'posts_type5',
						'posts_count'  => 5,
					),
					array(
						'title'        => '',
						'category'     => '',
						'display_type' => 'posts_type7',
						'posts_count'  => 6,
					),
					array(
						'title'        => '',
						'category'     => '',
						'display_type' => 'posts_type4',
						'posts_count'  => 4,
					),
				),
			);
		}
		return self::$layout_defaults;
	}

	/**
	 * Register global settings.
	 */
	public function register_settings() {
		register_setting(
			'wbcom_essential_settings',
			'wbcom_essential_blog_index_layout',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_layout_choice' ),
				'default'           => '',
			)
		);

		register_setting(
			'wbcom_essential_settings',
			'wbcom_essential_blog_posts_per_page',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 0,
			)
		);

		register_setting(
			'wbcom_essential_settings',
			'wbcom_essential_blog_show_ticker',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => true,
			)
		);

		register_setting(
			'wbcom_essential_settings',
			'wbcom_essential_blog_show_category_nav',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => true,
			)
		);

		register_setting(
			'wbcom_essential_settings',
			'wbcom_essential_blog_show_slider',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => true,
			)
		);

		register_setting(
			'wbcom_essential_settings',
			'wbcom_essential_blog_sections',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_sections' ),
				'default'           => array(),
			)
		);

		add_settings_section(
			'wbcom_essential_blog_index_section',
			__( 'Blog Index', 'wbcom-essential' ),
			array( $this, 'render_section_description' ),
			'wbcom-essential'
		);

		add_settings_field(
			'wbcom_essential_blog_index_layout',
			__( 'Blog Layout', 'wbcom-essential' ),
			array( $this, 'render_layout_field' ),
			'wbcom-essential',
			'wbcom_essential_blog_index_section'
		);

		add_settings_field(
			'wbcom_essential_blog_posts_per_page',
			__( 'Posts Per Page', 'wbcom-essential' ),
			array( $this, 'render_posts_per_page_field' ),
			'wbcom-essential',
			'wbcom_essential_blog_index_section'
		);

		add_settings_field(
			'wbcom_essential_blog_layout_options',
			__( 'Layout Options', 'wbcom-essential' ),
			array( $this, 'render_layout_options_field' ),
			'wbcom-essential',
			'wbcom_essential_blog_index_section'
		);

		add_settings_field(
			'wbcom_essential_blog_sections',
			__( 'Content Sections', 'wbcom-essential' ),
			array( $this, 'render_sections_field' ),
			'wbcom-essential',
			'wbcom_essential_blog_index_section'
		);
	}

	/**
	 * Render section description.
	 */
	public function render_section_description() {
		echo '<p>' . esc_html__( 'Choose a layout for your blog index and archive pages. Magazine and Newspaper layouts combine multiple blocks into configurable sections.', 'wbcom-essential' ) . '</p>';
	}

	/**
	 * Render the layout dropdown field.
	 */
	public function render_layout_field() {
		$current = get_option( 'wbcom_essential_blog_index_layout', '' );
		echo '<select name="wbcom_essential_blog_index_layout" id="wbcom_essential_blog_index_layout">';
		foreach ( $this->get_layout_choices() as $value => $label ) {
			echo '<option value="' . esc_attr( $value ) . '"' . selected( $current, $value, false ) . '>' . esc_html( $label ) . '</option>';
		}
		echo '</select>';
		echo '<p class="description">' . esc_html__( 'Applies to the main blog page and category/tag archives.', 'wbcom-essential' ) . '</p>';
	}

	/**
	 * Render the posts per page field.
	 */
	public function render_posts_per_page_field() {
		$current = get_option( 'wbcom_essential_blog_posts_per_page', 0 );
		echo '<input type="number" name="wbcom_essential_blog_posts_per_page" id="wbcom_essential_blog_posts_per_page" value="' . esc_attr( $current ) . '" min="0" max="50" step="1" class="small-text" />';
		echo '<p class="description">' . esc_html__( 'Set to 0 to use the WordPress default. Only applies to Grid and List layouts.', 'wbcom-essential' ) . '</p>';
	}

	/**
	 * Render layout-specific toggle options.
	 */
	public function render_layout_options_field() {
		$show_ticker       = get_option( 'wbcom_essential_blog_show_ticker', true );
		$show_category_nav = get_option( 'wbcom_essential_blog_show_category_nav', true );
		$show_slider       = get_option( 'wbcom_essential_blog_show_slider', true );
		?>
		<div class="wbcom-layout-options" id="wbcom-layout-options">
			<label class="wbcom-layout-option wbcom-option-magazine" style="display:none;">
				<input type="checkbox" name="wbcom_essential_blog_show_category_nav" value="1" <?php checked( $show_category_nav ); ?> />
				<?php esc_html_e( 'Show category navigation bar', 'wbcom-essential' ); ?>
			</label>
			<label class="wbcom-layout-option wbcom-option-newspaper" style="display:none;">
				<input type="checkbox" name="wbcom_essential_blog_show_ticker" value="1" <?php checked( $show_ticker ); ?> />
				<?php esc_html_e( 'Show breaking news ticker', 'wbcom-essential' ); ?>
			</label>
			<label class="wbcom-layout-option wbcom-option-newspaper" style="display:none;">
				<input type="checkbox" name="wbcom_essential_blog_show_slider" value="1" <?php checked( $show_slider ); ?> />
				<?php esc_html_e( 'Show hero slider', 'wbcom-essential' ); ?>
			</label>
			<p class="wbcom-layout-option wbcom-option-simple description" style="display:none;">
				<?php esc_html_e( 'No additional options for this layout.', 'wbcom-essential' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the sections configurator.
	 */
	public function render_sections_field() {
		$layout   = get_option( 'wbcom_essential_blog_index_layout', '' );
		$sections = get_option( 'wbcom_essential_blog_sections', array() );

		// If no sections saved yet, use defaults for the current layout.
		if ( empty( $sections ) && isset( self::get_layout_defaults()[ $layout ] ) ) {
			$sections = self::$layout_defaults[ $layout ];
		}

		// Get categories with posts.
		$categories = get_categories(
			array(
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		?>
		<div class="wbcom-sections-config" id="wbcom-sections-config" style="display:none;">
			<div class="wbcom-sections-list" id="wbcom-sections-list">
				<?php
				if ( ! empty( $sections ) ) {
					foreach ( $sections as $index => $section ) {
						$this->render_section_row( $index, $section, $categories );
					}
				}
				?>
			</div>
			<button type="button" class="button" id="wbcom-add-section">
				<span class="dashicons dashicons-plus-alt2" style="vertical-align:middle;margin-right:4px;"></span>
				<?php esc_html_e( 'Add Section', 'wbcom-essential' ); ?>
			</button>
			<p class="description" style="margin-top:12px;">
				<?php esc_html_e( 'Leave category as "Auto" to automatically assign categories that have posts. Drag sections to reorder.', 'wbcom-essential' ); ?>
			</p>
		</div>

		<!-- Hidden template for adding new sections via JS -->
		<script type="text/html" id="tmpl-wbcom-section-row">
			<?php $this->render_section_row( '{{INDEX}}', array(), $categories ); ?>
		</script>
		<?php
	}

	/**
	 * Render a single section configuration row.
	 *
	 * @param int|string $index      Section index.
	 * @param array      $section    Section data.
	 * @param array      $categories Categories list.
	 */
	private function render_section_row( $index, $section, $categories ) {
		$section = wp_parse_args(
			$section,
			array(
				'title'        => '',
				'category'     => '',
				'display_type' => 'posts_type3',
				'posts_count'  => 6,
			)
		);

		$name_prefix = 'wbcom_essential_blog_sections[' . $index . ']';
		?>
		<div class="wbcom-section-row" data-index="<?php echo esc_attr( $index ); ?>">
			<div class="wbcom-section-row__header">
				<span class="wbcom-section-row__handle dashicons dashicons-menu"></span>
				<span class="wbcom-section-row__number"><?php echo esc_html( is_numeric( $index ) ? $index + 1 : '#' ); ?></span>
				<input type="text"
					name="<?php echo esc_attr( $name_prefix ); ?>[title]"
					value="<?php echo esc_attr( $section['title'] ); ?>"
					placeholder="<?php esc_attr_e( 'Section title (auto from category)', 'wbcom-essential' ); ?>"
					aria-label="<?php esc_attr_e( 'Section title', 'wbcom-essential' ); ?>"
					class="wbcom-section-row__title" />
				<button type="button" class="wbcom-section-row__remove" title="<?php esc_attr_e( 'Remove', 'wbcom-essential' ); ?>">
					<span class="dashicons dashicons-trash"></span>
				</button>
			</div>
			<div class="wbcom-section-row__fields">
				<label>
					<span><?php esc_html_e( 'Category', 'wbcom-essential' ); ?></span>
					<select name="<?php echo esc_attr( $name_prefix ); ?>[category]">
						<option value=""><?php esc_html_e( 'Auto (random with posts)', 'wbcom-essential' ); ?></option>
						<option value="__all__" <?php selected( $section['category'], '__all__' ); ?>><?php esc_html_e( 'All Categories (Latest)', 'wbcom-essential' ); ?></option>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $section['category'], $cat->slug ); ?>>
								<?php echo esc_html( $cat->name . ' (' . $cat->count . ')' ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<span><?php esc_html_e( 'Display Style', 'wbcom-essential' ); ?></span>
					<select name="<?php echo esc_attr( $name_prefix ); ?>[display_type]">
						<?php foreach ( $this->get_display_types() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $section['display_type'], $value ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<span><?php esc_html_e( 'Posts', 'wbcom-essential' ); ?></span>
					<input type="number"
						name="<?php echo esc_attr( $name_prefix ); ?>[posts_count]"
						value="<?php echo esc_attr( $section['posts_count'] ); ?>"
						min="1" max="20" class="small-text" />
				</label>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue settings page JS for section configurator.
	 *
	 * @param string $hook_suffix Current admin page.
	 */
	public function enqueue_settings_js( $hook_suffix ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';

		if ( 'wbcom-essential' !== $page || 'settings' !== $tab ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_add_inline_script(
			'jquery-ui-sortable',
			$this->get_settings_js(),
			'after'
		);
		wp_add_inline_style( 'wbcom-essential-admin', $this->get_sections_css() );
	}

	/**
	 * Inline JS for the section configurator.
	 *
	 * @return string
	 */
	private function get_settings_js() {
		$defaults_json = wp_json_encode( self::get_layout_defaults() );
		return <<<JS
jQuery(function($) {
	var layoutDefaults = {$defaults_json};
	var \$layout     = $('#wbcom_essential_blog_index_layout');
	var \$options    = $('#wbcom-layout-options');
	var \$sections   = $('#wbcom-sections-config');
	var \$list       = $('#wbcom-sections-list');
	var sectionCount = \$list.children('.wbcom-section-row').length;

	function toggleUI() {
		var val = \$layout.val();
		var isComposite = (val === 'magazine' || val === 'newspaper');

		\$options.find('.wbcom-layout-option').hide();
		if (val === 'magazine') {
			\$options.find('.wbcom-option-magazine').show();
		} else if (val === 'newspaper') {
			\$options.find('.wbcom-option-newspaper').show();
		} else if (val === 'grid' || val === 'list') {
			\$options.find('.wbcom-option-simple').show();
		}

		if (isComposite) {
			\$sections.show();
			if (\$list.children('.wbcom-section-row').length === 0 && layoutDefaults[val]) {
				loadDefaults(val);
			}
		} else {
			\$sections.hide();
		}
	}

	function loadDefaults(layout) {
		\$list.empty();
		sectionCount = 0;
		if (!layoutDefaults[layout]) return;
		layoutDefaults[layout].forEach(function(sec) {
			addSection(sec);
		});
	}

	function addSection(data) {
		data = data || {};
		var tmpl = $('#tmpl-wbcom-section-row').html();
		tmpl = tmpl.replace(/\{\{INDEX\}\}/g, sectionCount);
		var \$row = $(tmpl);
		if (data.title) \$row.find('.wbcom-section-row__title').val(data.title);
		if (data.category) \$row.find('select[name*="[category]"]').val(data.category);
		if (data.display_type) \$row.find('select[name*="[display_type]"]').val(data.display_type);
		if (data.posts_count) \$row.find('input[name*="[posts_count]"]').val(data.posts_count);
		\$row.find('.wbcom-section-row__number').text(sectionCount + 1);
		\$list.append(\$row);
		sectionCount++;
		renumber();
	}

	function renumber() {
		\$list.children('.wbcom-section-row').each(function(i) {
			$(this).find('.wbcom-section-row__number').text(i + 1);
			$(this).attr('data-index', i);
			$(this).find('[name]').each(function() {
				var name = $(this).attr('name');
				$(this).attr('name', name.replace(/\[\d+\]/, '[' + i + ']'));
			});
		});
	}

	\$layout.on('change', toggleUI);
	toggleUI();

	$('#wbcom-add-section').on('click', function() {
		addSection();
	});

	\$list.on('click', '.wbcom-section-row__remove', function() {
		$(this).closest('.wbcom-section-row').remove();
		renumber();
	});

	\$list.sortable({
		handle: '.wbcom-section-row__handle',
		placeholder: 'wbcom-section-row--placeholder',
		update: renumber
	});
});
JS;
	}

	/**
	 * Inline CSS for the section configurator.
	 *
	 * @return string
	 */
	private function get_sections_css() {
		return '
			.wbcom-sections-config { max-width: 700px; }
			.wbcom-section-row { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; overflow: hidden; }
			.wbcom-section-row__header { display: flex; align-items: center; gap: 8px; padding: 10px 14px; background: #fff; border-bottom: 1px solid #e5e7eb; }
			.wbcom-section-row__handle { cursor: grab; color: #9ca3af; }
			.wbcom-section-row__handle:active { cursor: grabbing; }
			.wbcom-section-row__number { background: #2c5282; color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; flex-shrink: 0; }
			.wbcom-section-row__title { flex: 1; border: 1px solid #d1d5db !important; border-radius: 4px !important; padding: 6px 10px !important; font-size: 14px !important; }
			.wbcom-section-row__remove { background: none; border: none; cursor: pointer; color: #ef4444; padding: 4px; opacity: 0.6; transition: opacity 0.15s; }
			.wbcom-section-row__remove:hover { opacity: 1; }
			.wbcom-section-row__fields { display: flex; gap: 12px; padding: 14px; flex-wrap: wrap; }
			.wbcom-section-row__fields label { display: flex; flex-direction: column; gap: 4px; font-size: 13px; color: #6b7280; flex: 1; min-width: 140px; }
			.wbcom-section-row__fields select, .wbcom-section-row__fields input { font-size: 13px; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 4px; }
			.wbcom-section-row--placeholder { height: 80px; background: #e0e7ff; border: 2px dashed #6366f1; border-radius: 8px; margin-bottom: 12px; }
			.wbcom-layout-options label { display: block; margin-bottom: 8px; font-size: 14px; }
			.wbcom-layout-options label input { margin-right: 6px; }
		';
	}

	/**
	 * Sanitize sections array.
	 *
	 * @param mixed $value Input value.
	 * @return array Sanitized sections.
	 */
	public function sanitize_sections( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized = array();
		$valid_types = array_keys( $this->get_display_types() );

		foreach ( $value as $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$title        = isset( $section['title'] ) ? sanitize_text_field( $section['title'] ) : '';
			$category     = isset( $section['category'] ) ? sanitize_text_field( $section['category'] ) : '';
			$display_type = isset( $section['display_type'] ) ? sanitize_text_field( $section['display_type'] ) : 'posts_type3';
			$posts_count  = isset( $section['posts_count'] ) ? absint( $section['posts_count'] ) : 6;

			if ( ! in_array( $display_type, $valid_types, true ) ) {
				$display_type = 'posts_type3';
			}

			$posts_count = max( 1, min( 20, $posts_count ) );

			$sanitized[] = array(
				'title'        => $title,
				'category'     => $category,
				'display_type' => $display_type,
				'posts_count'  => $posts_count,
			);
		}

		return $sanitized;
	}

	/**
	 * Sanitize layout choice.
	 *
	 * @param string $value Input value.
	 * @return string Sanitized value.
	 */
	public function sanitize_layout_choice( $value ) {
		$valid = array_keys( $this->get_layout_choices() );
		return in_array( $value, $valid, true ) ? $value : '';
	}

	/**
	 * Get default sections for a layout.
	 *
	 * @param string $layout Layout slug.
	 * @return array Default sections.
	 */
	public static function get_defaults( $layout ) {
		$defaults = self::get_layout_defaults();
		return isset( $defaults[ $layout ] ) ? $defaults[ $layout ] : array();
	}

	/**
	 * Get categories with posts, ordered by count.
	 *
	 * @param int $limit Max categories to return.
	 * @return array Array of category objects.
	 */
	public static function get_auto_categories( $limit = 10 ) {
		$cats = get_categories(
			array(
				'hide_empty' => true,
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => $limit,
			)
		);

		// Exclude "Uncategorized" if it's the only one.
		if ( count( $cats ) > 1 ) {
			$default_cat = (int) get_option( 'default_category' );
			$cats        = array_filter(
				$cats,
				function ( $cat ) use ( $default_cat ) {
					return $cat->term_id !== $default_cat;
				}
			);
			$cats = array_values( $cats );
		}

		return $cats;
	}

	/**
	 * Resolve section categories - fills "auto" slots with real categories.
	 *
	 * @param array $sections Sections config.
	 * @return array Resolved sections with category slugs filled in.
	 */
	public static function resolve_section_categories( $sections ) {
		if ( empty( $sections ) ) {
			return $sections;
		}

		$auto_cats = self::get_auto_categories( count( $sections ) + 5 );
		$used      = array();

		// Collect already-assigned categories.
		foreach ( $sections as $section ) {
			if ( ! empty( $section['category'] ) && '__all__' !== $section['category'] ) {
				$used[] = $section['category'];
			}
		}

		// Fill auto slots.
		$auto_index = 0;
		foreach ( $sections as &$section ) {
			if ( empty( $section['category'] ) ) {
				// Find next unused category.
				while ( $auto_index < count( $auto_cats ) ) {
					$cat_slug = $auto_cats[ $auto_index ]->slug;
					$auto_index++;
					if ( ! in_array( $cat_slug, $used, true ) ) {
						$section['category']       = $cat_slug;
						$section['_auto_resolved']  = true;
						$section['_category_name']  = $auto_cats[ $auto_index - 1 ]->name;
						$used[]                    = $cat_slug;
						break;
					}
				}
			} elseif ( '__all__' === $section['category'] ) {
				$section['category']      = '';
				$section['_category_name'] = __( 'Latest', 'wbcom-essential' );
			}

			// Auto-fill title from category name if empty.
			if ( empty( $section['title'] ) ) {
				if ( ! empty( $section['_category_name'] ) ) {
					$section['title'] = $section['_category_name'];
				} elseif ( ! empty( $section['category'] ) ) {
					$cat = get_category_by_slug( $section['category'] );
					if ( $cat ) {
						$section['title'] = $cat->name;
					}
				}
			}
		}
		unset( $section );

		return $sections;
	}
}
