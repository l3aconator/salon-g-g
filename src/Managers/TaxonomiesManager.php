<?php
/**
 * Bootstraps settings and configurations for taxonomies.
 *
 * @package Salon
 */

namespace Salon\Managers;

/** Class */
class TaxonomiesManager {

	/**
	 * Runs initialization tasks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 1 );
	}

	/**
	 * Register taxonomies in WordPress
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		register_taxonomy(
			'designer_type',
			'designer',
			array(
				'labels'            => array(
					'name'          => 'Designer Types',
					'singular_name' => 'Designer Type',
					'edit_item'     => 'Edit Designer Type',
					'add_new_item'  => 'Add New Designer Type',
					'not_found'     => 'No Types found.',
					'parent_item'   => 'Parent Type',
					'back_to_items' => 'Back to Types',
				),
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => array(
					'slug' => 'designer-type',
				),
			)
		);
	}
}
