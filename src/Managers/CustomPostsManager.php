<?php
/**
 * Bootstraps settings and configurations for custom post types.
 *
 * @package Salon
 */

namespace Salon\Managers;

/** Class */
class CustomPostsManager {

	/**
	 * Runs initialization tasks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_post_types' ), 1 );
	}


	/**
	 * Register post types in WoPostsrdPress
	 *
	 * @return void
	 */
	public function register_post_types() {
		// Designer post type.
		register_post_type(
			'designer',
			array(
				'labels'        => array(
					'name'          => __( 'Designers' ),
					'singular_name' => __( 'Designers' ),
					'add_new_item'  => __( 'Add New Designer' ),
					'not_found'     => __( 'No Designers Found' ),
					'search_items'  => __( 'Search Designers' ),
				),
				'public'        => true,
				'has_archive'   => false,
				'menu_position' => 21,
				'menu_icon'     => 'dashicons-art',
				'rewrite'       => array( 'slug' => 'designer' ),
				'supports'      => array( 'editor', 'title' ),
				'taxonomies'    => array( 'designer_type' ),
			)
		);

		// Exhibition post type.
		register_post_type(
			'exhibition',
			array(
				'labels'        => array(
					'name'          => __( 'Exhibitions' ),
					'singular_name' => __( 'Exhibition' ),
					'add_new_item'  => __( 'Add New Exhibition' ),
					'not_found'     => __( 'No Exhibitions Found' ),
					'search_items'  => __( 'Search Exhibitions' ),
				),
				'public'        => true,
				'has_archive'   => false,
				'menu_position' => 26,
				'menu_icon'     => 'dashicons-format-image',
				'rewrite'       => array( 'slug' => 'exhibition' ),
				'supports'      => array( 'title', 'editor' ),
				'taxonomies'    => array(),
			)
		);

		// Press post type.
		register_post_type(
			'press',
			array(
				'labels'        => array(
					'name'          => __( 'Press' ),
					'singular_name' => __( 'Press' ),
					'add_new_item'  => __( 'Add New Press' ),
					'not_found'     => __( 'No Press Found' ),
					'search_items'  => __( 'Search Press' ),
				),
				'public'        => true,
				'has_archive'   => false,
				'menu_position' => 31,
				'menu_icon'     => 'dashicons-media-text',
				'rewrite'       => array( 'slug' => 'press' ),
				'supports'      => array( 'title', 'editor', 'thumbnail' ),
				'taxonomies'    => array(),
			)
		);
	}
}
