<?php
/**
 * Sets up Gutenberg and calls constructor for custom blocks.
 *
 * @package Salon
 */

namespace Salon\Managers;

use Salon\Blocks;

/** Class */
class GutenbergManager {

	/**
	 * Runs initialization tasks.
	 *
	 * @return void
	 */
	public function run() {
		add_filter( 'allowed_block_types', array( $this, 'allow_blocks' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg' ), 10, 2 );

		new \Salon\Blocks\Blocks();
	}

	/**
	 * Only allow the Gutenberg blocks we actually needed. As of Gutenberg 5.0 this
	 * was the easiest way to disable blocks. Also note that custom blocks need to
	 * be added to this list in order to appear.
	 *
	 * @param array $allowed_blocks Allowed blocks.
	 *
	 * @return array
	 */
	public function allow_blocks( $allowed_blocks ) {
		return array(
			'core/image',
			'core/paragraph',
			'core/heading',
			'core/list',
			'core/buttons',
			'core/shortcode',
			'core/embed',
			'core/columns',

			'salon/split-content',
			'salon/text-columns',

			'acf/photo-grid',
		);
	}

	/**
	 * Disable the Gutenberg editor in certain cases.
	 *
	 * @param boolean $can_edit Default Gutenberg status for the current post.
	 *
	 * @return boolean
	 */
	public function disable_gutenberg( $can_edit ) {

		if ( ! isset( $_GET['post'] ) ) {
			return $can_edit;
		}

		$excluded_templates = array(
			'page-template-archive.php',
		);

		$excluded_ids = array(
			intval( get_option( 'page_on_front' ) ),
			intval( get_option( 'woocommerce_shop_page_id' ) ),
		);

		$id       = intval( $_GET['post'] );
		$template = get_page_template_slug( $id );

		if ( in_array( $template, $excluded_templates, true ) || in_array( $id, $excluded_ids, true ) ) {
			return false;
		}

		return $can_edit;

	}
}
