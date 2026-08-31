<?php

namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\QueryControl;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;
use WBCOM_ESSENTIAL\ELEMENTOR\ElementorHooks;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Class Group_Control_Posts
 *
 * @deprecated since 2.5.0, use class Group_Control_Query and Elementor_Post_Query
 */
class Group_Control_Posts extends Group_Control_Base {
	const INLINE_MAX_RESULTS = 15;

	protected static $fields;

	public static function get_type() {
		return 'posts';
	}

	public static function on_export_remove_setting_from_element( $element, $control_id ) {
		unset( $element['settings'][ $control_id . '_posts_ids' ] );
		unset( $element['settings'][ $control_id . '_authors' ] );

		foreach ( self::get_post_types() as $post_type => $label ) {
			$taxonomy_filter_args = array(
				'show_in_nav_menus' => true,
				'object_type'       => array( $post_type ),
			);

			$taxonomies = get_taxonomies( $taxonomy_filter_args, 'objects' );

			foreach ( $taxonomies as $taxonomy => $object ) {
				unset( $element['settings'][ $control_id . '_' . $taxonomy . '_ids' ] );
			}
		}

		return $element;
	}

	protected function init_fields() {
		$fields = array();

		$fields['post_type'] = array(
			'label' => _x( 'Source', 'Posts Query Control', 'wbcom-essential' ),
			'type'  => Controls_Manager::SELECT,
		);

		$fields['posts_ids'] = array(
			'label'       => _x( 'Search & Select', 'Posts Query Control', 'wbcom-essential' ),
			'type'        => ElementorHooks::QUERY_CONTROL_ID,
			'post_type'   => '',
			'options'     => array(),
			'label_block' => true,
			'multiple'    => true,
			'filter_type' => 'by_id',
			'condition'   => array(
				'post_type' => 'by_id',
			),
		);

		$author_args = array(
			'label'       => _x( 'Author', 'Posts Query Control', 'wbcom-essential' ),
			'label_block' => true,
			'multiple'    => true,
			'default'     => array(),
			'options'     => array(),
			'condition'   => array(
				'post_type!' => 'by_id',
			),
		);

		$user_query = new \WP_User_Query(
			array(
				'role'   => 'Author',
				'fields' => 'ID',
			)
		);

		// For large websites, use Ajax to search
		if ( $user_query->get_total() > self::INLINE_MAX_RESULTS ) {
			$author_args['type'] = ElementorHooks::QUERY_CONTROL_ID;

			$author_args['filter_type'] = 'author';
		} else {
			$author_args['type'] = Controls_Manager::SELECT2;

			$author_args['options'] = $this->get_authors();
		}

		$fields['authors'] = $author_args;

		return $fields;
	}

	protected function prepare_fields( $fields ) {
		$args = $this->get_args();

		$post_types = self::get_post_types( $args );

		$post_types_options = $post_types;

		$post_types_options['by_id'] = _x( 'Manual Selection', 'Posts Query Control', 'wbcom-essential' );

		$fields['post_type']['options'] = $post_types_options;

		$fields['post_type']['default'] = key( $post_types );

		$fields['posts_ids']['object_type'] = array_keys( $post_types );

		$taxonomy_filter_args = array(
			'show_in_nav_menus' => true,
		);

		if ( ! empty( $args['post_type'] ) ) {
			$taxonomy_filter_args['object_type'] = array( $args['post_type'] );
		}

		$taxonomies = get_taxonomies( $taxonomy_filter_args, 'objects' );

		foreach ( $taxonomies as $taxonomy => $object ) {
			$taxonomy_args = array(
				'label'       => $object->label,
				'type'        => ElementorHooks::QUERY_CONTROL_ID,
				'label_block' => true,
				'multiple'    => true,
				'object_type' => $taxonomy,
				'options'     => array(),
				'condition'   => array(
					'post_type' => $object->object_type,
				),
			);

			$count = wp_count_terms( $taxonomy );

			$options = array();

			// For large websites, use Ajax to search
			if ( $count > self::INLINE_MAX_RESULTS ) {
				$taxonomy_args['type'] = ElementorHooks::QUERY_CONTROL_ID;

				$taxonomy_args['filter_type'] = 'taxonomy';
			} else {
				$taxonomy_args['type'] = Controls_Manager::SELECT2;

				$terms = get_terms( $taxonomy );

				foreach ( $terms as $term ) {
					$options[ $term->term_id ] = $term->name;
				}

				$taxonomy_args['options'] = $options;
			}

			$fields[ $taxonomy . '_ids' ] = $taxonomy_args;
		}

		return parent::prepare_fields( $fields );
	}

	private function get_authors() {
		$args ['fields'] = array(
			'ID',
			'display_name',
		);

		if ( version_compare( $GLOBALS['wp_version'], '5.9', '<' ) ) {
			$args['who'] = 'authors';
		} else {
			$args['capability'] = array( 'edit_posts' );
		}
		$user_query = new \WP_User_Query( $args );

		$authors = array();

		foreach ( $user_query->get_results() as $result ) {
			$authors[ $result->ID ] = $result->display_name;
		}

		return $authors;
	}

	private static function get_post_types( $args = array() ) {
		$post_type_args = array(
			'show_in_nav_menus' => true,
		);

		if ( ! empty( $args['post_type'] ) ) {
			$post_type_args['name'] = $args['post_type'];
		}

		$_post_types = get_post_types( $post_type_args, 'objects' );

		$post_types = array();

		foreach ( $_post_types as $post_type => $object ) {
			$post_types[ $post_type ] = $object->label;
		}

		return $post_types;
	}


}
