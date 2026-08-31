<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * [wbcom_is_woocommerce]
 *
 * @return [boolean]
 */
function wbcom_is_woocommerce() {
	return class_exists( 'WooCommerce' );
}

/**
 * Get a list of taxonomy terms as an array of options.
 *
 * @param string $taxonomy     The taxonomy from which to retrieve terms. Default is 'product_cat'.
 * @param string $option_value The property to use as the array key ('slug' or 'term_id'). Default is 'slug'.
 *
 * @return array $options      An associative array of term properties and names.
 */
function wbcom_taxonomy_list( $taxonomy = 'product_cat', $option_value = 'slug' ) {
	// Validate that the option value is either 'slug' or 'term_id'.
	$valid_option_values = array( 'slug', 'term_id' );
	if ( ! in_array( $option_value, $valid_option_values, true ) ) {
		$option_value = 'slug'; // Fallback to 'slug' if invalid value is passed.
	}

	// Fetch terms from the specified taxonomy.
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);

	$options = array();

	// If terms exist and there are no errors, process them.
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			// Escape both the key and the name for security.
			$options[ esc_attr( $term->$option_value ) ] = esc_html( $term->name );
		}
	} else {
		// Handle case where no terms are found.
		$options[0] = __( 'No terms found', 'wbcom-essential' );
	}

	// Allow filtering of the final options array for flexibility.
	return apply_filters( 'wbcom_taxonomy_list_options', $options, $taxonomy, $option_value );
}

/**
 * Get post titles for a given post type.
 *
 * @param string $post_type The post type to retrieve posts from. Default is 'post'.
 * @param array  $args      Optional. Additional query arguments. Default is empty array.
 *
 * @return array $options   An associative array of post IDs and titles.
 */
function wbcom_post_name( $post_type = 'post', $args = array() ) {
	// Initialize the options array with a default 'Select' option.
	$options      = array();
	$options['0'] = __( 'Select', 'wbcom-essential' );

	// Define the per page limit, either from the args or the default setting.
	$perpage = ! empty( $args['limit'] ) && is_int( $args['limit'] ) ? $args['limit'] : (int) wbcom_get_option( 'loadproductlimit', 'wbcom_others_tabs', '20' );

	// Prepare query arguments for fetching posts.
	$default_args = array(
		'posts_per_page' => $perpage,
		'post_type'      => $post_type,
		'post_status'    => 'publish', // Only fetch published posts.
	);

	// Allow filtering of the query arguments.
	$all_post = apply_filters( 'wbcom_post_name_query_args', array_merge( $default_args, $args ), $post_type );

	// Fetch posts.
	$post_terms = get_posts( $all_post );

	// Process posts if available and valid.
	if ( ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ) {
		foreach ( $post_terms as $term ) {
			$options[ $term->ID ] = esc_html( $term->post_title ); // Escape the title.
		}
	} else {
		// Optionally handle no posts found scenario.
		$options['0'] = __( 'No posts found', 'wbcom-essential' );
	}

	// Allow filtering of the final options array.
	return apply_filters( 'wbcom_post_name_options', $options, $post_type );
}


/**
 * Plugisn Options value
 * return on/off
 */
function wbcom_get_option( $option, $section, $default = '' ) {
	$options = get_option( $section );
	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}

/**
 * HTML Tag list
 * return array
 */
function wbcom_html_tag_lists() {
	$html_tag_list = array(
		'h1'   => __( 'H1', 'wbcom-essential' ),
		'h2'   => __( 'H2', 'wbcom-essential' ),
		'h3'   => __( 'H3', 'wbcom-essential' ),
		'h4'   => __( 'H4', 'wbcom-essential' ),
		'h5'   => __( 'H5', 'wbcom-essential' ),
		'h6'   => __( 'H6', 'wbcom-essential' ),
		'p'    => __( 'p', 'wbcom-essential' ),
		'div'  => __( 'div', 'wbcom-essential' ),
		'span' => __( 'span', 'wbcom-essential' ),
	);
	return $html_tag_list;
}

/**
 * Validate HTML tag.
 *
 * @param string $tag The HTML tag to validate.
 *
 * @return string The valid HTML tag or the default tag if invalid.
 */
function wbcom_validate_html_tag( $tag ) {
	// List of allowed HTML tags.
	$allowed_html_tags = apply_filters(
		'wbcom_allowed_html_tags',
		array(
			'article',
			'aside',
			'footer',
			'header',
			'section',
			'nav',
			'main',
			'div',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'span',
		)
	);

	// Convert the tag to lowercase for case-insensitive comparison.
	$tag = strtolower( $tag );

	// Return the tag if valid, otherwise return the default tag (div).
	return in_array( $tag, $allowed_html_tags, true ) ? $tag : apply_filters( 'wbcom_default_html_tag', 'div' );
}

/**
 * Display a custom product badge on WooCommerce products.
 *
 * @param string $show Whether to show the badge. Default is 'yes'.
 */
function wbcom_custom_product_badge( $show = 'yes' ) {
	global $product;

	// Ensure we have a valid WooCommerce product object.
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return;
	}

	// Retrieve and sanitize the custom sale flash text.
	$custom_saleflash_text = sanitize_text_field( get_post_meta( get_the_ID(), '_saleflash_text', true ) );

	// Allow filtering whether the badge should be shown or not.
	$show = apply_filters( 'wbcom_show_custom_product_badge', $show, $product );

	if ( $show === 'yes' && ! empty( $custom_saleflash_text ) && $product->is_in_stock() ) {
		// Apply filter to the sale flash text for further customization.
		$custom_saleflash_text = apply_filters( 'wbcom_custom_saleflash_text', $custom_saleflash_text, $product );

		// Determine if the product is featured.
		$featured_class = $product->is_featured() ? ' hot' : '';

		// Output the custom product badge with escaping.
		echo '<span class="wb-product-label wb-product-label-left' . esc_attr( $featured_class ) . '">' . esc_html( $custom_saleflash_text ) . '</span>';
	}
}

/**
 * Prepare a WooCommerce product query based on various parameters.
 *
 * @param array $query_args An array of arguments to customize the product query.
 *
 * @return array The arguments array to be passed to WP_Query.
 */
function wbcom_product_query( $query_args = array() ) {

	$meta_query = $tax_query = array();

	// Sanitize input values.
	$per_page = isset( $query_args['per_page'] ) ? absint( $query_args['per_page'] ) : 3;

	// Category filter (Tax Query).
	if ( ! empty( $query_args['categories'] ) ) {
		$tax_query[] = array(
			'taxonomy'         => 'product_cat',
			'terms'            => array_map( 'sanitize_text_field', (array) $query_args['categories'] ),
			'field'            => 'slug',
			'include_children' => false,
		);
	}

	// Tag filter (Tax Query).
	if ( ! empty( $query_args['tags'] ) ) {
		$tax_query[] = array(
			'taxonomy'         => 'product_tag',
			'terms'            => array_map( 'sanitize_text_field', (array) $query_args['tags'] ),
			'field'            => 'slug',
			'include_children' => false,
		);
	}

	// Featured products.
	if ( isset( $query_args['product_type'] ) && $query_args['product_type'] === 'featured' ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'featured',
			'operator' => 'IN',
		);
	}

	// Hide hidden items (if specified).
	if ( ! empty( $query_args['hidden'] ) && $query_args['hidden'] === true ) {
		$tax_query[] = array(
			'taxonomy'         => 'product_visibility',
			'field'            => 'name',
			'terms'            => array( 'exclude-from-search', 'exclude-from-catalog' ),
			'operator'         => 'NOT IN',
			'include_children' => false,
		);
	}

	// Meta Query for hiding out of stock items.
	$hide_out_of_stock = isset( $query_args['hide_out_of_stock'] ) && $query_args['hide_out_of_stock'] === true
		? 'yes'
		: get_option( 'woocommerce_hide_out_of_stock_items', 'no' );
	if ( 'yes' === $hide_out_of_stock ) {
		$meta_query[] = array(
			'key'     => '_stock_status',
			'value'   => 'instock',
			'compare' => '==',
		);
	}

	// Default query arguments.
	$args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'posts_per_page'      => $per_page,
		'meta_query'          => $meta_query,
		'tax_query'           => $tax_query,
	);

	// Handle product type.
	if ( ! empty( $query_args['product_type'] ) ) {
		switch ( $query_args['product_type'] ) {
			case 'sale':
				$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
				break;

			case 'best_selling':
				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'desc';
				break;

			case 'top_rated':
				$args['meta_key'] = '_wc_average_rating';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'desc';
				break;

			case 'mixed_order':
				$args['orderby'] = 'rand';
				break;

			case 'show_byid':
			case 'show_byid_manually':  // Merged to avoid redundancy.
				if ( ! empty( $query_args['product_ids'] ) ) {
					$args['post__in'] = array_map( 'absint', $query_args['product_ids'] );
					$args['orderby']  = 'post__in';
				}
				break;

			default:  // Fallback to recent.
				$args['orderby'] = 'date';
				$args['order']   = 'desc';
				break;
		}
	}

	// Custom Order.
	if ( ! empty( $query_args['custom_order'] ) ) {
		$args['orderby'] = sanitize_text_field( $query_args['custom_order']['orderby'] );
		$args['order']   = sanitize_text_field( $query_args['custom_order']['order'] );
	}

	// Allow filters for custom modifications to the query args.
	return apply_filters( 'wbcom_product_query_args', $args, $query_args );
}

/**
 * Display a sale flash for WooCommerce products.
 *
 * @param string $offertype      The type of sale display ('default', 'number', 'percent').
 * @param bool   $echo           Whether to echo the output (default is true).
 * @param string $outofstocktxt  Custom text to display for out-of-stock products (optional).
 *
 * @return string|null           Returns the output if $echo is false, otherwise null.
 */
function wbcom_sale_flash( $offertype = 'default', $echo = true, $outofstocktxt = '' ) {
	global $product;

	if ( ! isset( $product ) || ! is_object( $product ) ) {
		return;
	}

	if ( $echo == false ) {
		ob_start();
	}

	if ( $product->is_on_sale() && $product->is_in_stock() ) {
		if ( $offertype != 'default' && $product->get_regular_price() > 0 ) {
			$_off_percent  = ( 1 - round( $product->get_price() / $product->get_regular_price(), 2 ) ) * 100;
			$_off_price    = round( $product->get_regular_price() - $product->get_price(), 0 );
			$_price_symbol = get_woocommerce_currency_symbol();
			$symbol_pos    = get_option( 'woocommerce_currency_pos', 'left' );
			$price_display = '';

			switch ( $symbol_pos ) {
				case 'left':
					$price_display = '-' . $_price_symbol . $_off_price;
					break;
				case 'right':
					$price_display = '-' . $_off_price . $_price_symbol;
					break;
				case 'left_space':
					$price_display = '-' . $_price_symbol . ' ' . $_off_price;
					break;
				default: /* right_space */
					$price_display = '-' . $_off_price . ' ' . $_price_symbol;
					break;
			}

			if ( $offertype == 'number' ) {
				echo '<span class="wb-product-label wb-product-label-right">' . esc_html( $price_display ) . '</span>';
			} elseif ( $offertype == 'percent' ) {
				echo '<span class="wb-product-label wb-product-label-right">' . esc_html( $_off_percent ) . '%</span>';
			} else {
				echo ' ';
			}
		} else {
			$sale_badge_text = apply_filters( 'wbcom_sale_badge_text', __( 'Sale!', 'wbcom-essential' ) );
			echo '<span class="wb-product-label wb-product-label-right">' . esc_html( $sale_badge_text ) . '</span>';
		}
	} else {
		$out_of_stock      = get_post_meta( get_the_ID(), '_stock_status', true );
		$out_of_stock_text = ! empty( $outofstocktxt ) ? esc_html( $outofstocktxt ) : apply_filters( 'wbcom_shop_out_of_stock_text', __( 'Out of stock', 'wbcom-essential' ) );

		if ( 'outofstock' === $out_of_stock ) {
			echo '<span class="wb-stockout wb-product-label wb-product-label-right">' . esc_html( $out_of_stock_text ) . '</span>';
		}
	}

	if ( $echo == false ) {
		return ob_get_clean();
	}
}

/*
* Category list
* return first one
*/
function wbcom_get_product_category_list( $id = null, $taxonomy = 'product_cat', $limit = 1 ) {
	$terms = get_the_terms( $id, $taxonomy );
	$i     = 0;
	if ( is_wp_error( $terms ) ) {
		return $terms;
	}

	if ( empty( $terms ) ) {
		return false;
	}

	foreach ( $terms as $term ) {
		++$i;
		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) ) {
			return $link;
		}
		echo '<a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a>';
		if ( $i == $limit ) {
			break;
		} else {
			continue; }
	}
}

// Customize rating html.
if ( ! function_exists( 'wbcom_wc_get_rating_html' ) ) {
	function wbcom_wc_get_rating_html( $block = '' ) {
		if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
			return; }
		global $product;
		$rating_count    = $product->get_rating_count();
		$average         = $product->get_average_rating();
		$rating_whole    = floor( $average );
		$rating_fraction = $average - $rating_whole;
		$flug            = 0;

		$icon_svg    = get_option( 'elementor_experiment-e_font_icon_svg', 'default' );
		$icon_prefix = ( $icon_svg == 'active' || $block == 'yes' ) ? 'fa' : 'fas';

		if ( $rating_count > 0 ) {
			$wrapper_class = is_single() ? 'rating-number' : 'top-rated-rating';
			ob_start();
			?>
			<div class="<?php echo esc_attr( $wrapper_class ); ?>">
				<span class="wb-product-ratting">
					<span class="wb-product-user-ratting">
						<?php
						for ( $i = 1; $i <= 5; $i++ ) {
							if ( $i <= $rating_whole ) {
								echo '<i class="' . esc_attr( $icon_prefix ) . ' fa-star"></i>';
							} elseif ( $rating_fraction > 0 && $flug == 0 ) {
								if ( $icon_svg == 'active' || $block == 'yes' ) {
									echo '<i class="fa fa-star-half-o"></i>';
								} else {
									echo '<i class="fas fa-star-half-alt"></i>';
								}
									$flug = 1;
							} elseif ( $icon_svg == 'active' || $block == 'yes' ) {
									echo '<i class="fa fa-star-o"></i>';
							} else {
								echo '<i class="far fa-star empty"></i>';
							}
						}
						?>
					</span>
				</span>
			</div>
			<?php
				$html = ob_get_clean();
		} else {
			$html = '';
		}
		return $html;
	}
}

/**
 * Enqueue scripts based on Elementor widget usage
 */
function wbcom_direct_check_elementor_data() {
	global $post;

	if ( ! is_a( $post, 'WP_Post' ) ) {
		return;
	}

	$elementor_data = get_post_meta( $post->ID, '_elementor_data', true );
	if ( ! empty( $elementor_data ) ) {
		// Ensure $elementor_data is a string before decoding
		if ( is_string( $elementor_data ) ) {
			// Decode the Elementor data as it's stored in a JSON encoded format
			$elementor_data_decoded = json_decode( $elementor_data, true );

			// Check if the heading widget is used
			if ( wbcom_check_for_heading_widget( $elementor_data_decoded ) ) {
				// Enqueue css
				add_filter(
					'bp_enqueue_assets_in_bp_pages_only',
					function () {
						return false;
					}
				);
			}
		}
	}
}
add_action( 'wp', 'wbcom_direct_check_elementor_data' );

/**
 * Check if the Elementor widget is used
 *
 * @param string $widget_name The widget name to check.
 * @return bool Whether the widget is used or not.
 */
function wbcom_check_for_heading_widget( $elementor_data ) {
	if ( is_array( $elementor_data ) ) {
		foreach ( $elementor_data as $element ) {
			if ( isset( $element['widgetType'] ) && ( $element['widgetType'] === 'wbcom-members-grid' || $element['widgetType'] === 'wbcom-groups-grid' ) ) {
				return true;
			}
			// Check nested elements if they exist
			if ( isset( $element['elements'] ) && wbcom_check_for_heading_widget( $element['elements'] ) ) {
				return true;
			}
		}
	}
	return false;
}
