<?php
/**
 * A editor block for a grid of images.
 *
 * @package Salon
 */

namespace Salon\Blocks\PhotoGrid;

use Timber;
use Timber\Image;

/** Class */
class PhotoGrid {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'acf/init', array( $this, 'register' ), 999 );
	}

	/**
	 * Uses ACF function to register custom blocks
	 *
	 * @return void
	 */
	public function register() {
		if ( function_exists( 'acf_register_block_type' ) ) {
			acf_register_block_type(
				array(
					'name'            => 'photo-grid',
					'title'           => 'Photo Grid',
					'description'     => 'Add a gallery of images.',
					'render_callback' => array( $this, 'render' ),
					'category'        => 'widgets',
					'icon'            => array(
						'background' => '#fff',
						'src'        => 'format-gallery',
					),
					'keywords'        => array( 'image', 'gallery' ),
					'mode'            => 'edit',
				)
			);
		}
	}

	/**
	 * Get info from the related ACF fields
	 * and then render corrosponding template
	 *
	 * @param array  $block      The block settings and attributes.
	 * @param string $content    The block content (empty content).
	 * @param bool   $is_preview True during AJAX preview.
	 *
	 * @return void
	 */
	public function render( $block, $content, $is_preview ) {
		$photos            = self::setup_grid( get_field( 'photo_grid' ) );
		$context['photos'] = $photos;

		if ( $is_preview ) {
			echo 'Preview mode is not supported. Please change to Edit mode by clicking the pencil icon in the toolbar above.';
		} else {
			Timber::render( 'components/photo-grid.twig', $context );
		}
	}

	/**
	 * Handle the layout of the photos passed through the ACF repeater.
	 *
	 * @param array $photos Rows from the ACF field.
	 *
	 * @return array
	 */
	public static function setup_grid( $photos ) {
		$clean_photos = array();

		foreach ( $photos as $index => $item ) {
			$is_product = 'Product' === $item['photo_type'];
			$image      = $is_product ? $item['product']->thumbnail : new Image( $item['photo'] );

			if ( ! $image ) {
				break;
			}

			$clean = array(
				'image' => $image,
				'class' => 0 === $index % 4 || 3 === $index % 4 ? 'cell--large' : 'cell--small',
			);

			if ( $is_product ) {
				$clean['link']  = $item['product']->link;
				$clean['title'] = $item['product']->title;
			}

			$clean_photos[] = $clean;
		}

		return $clean_photos;
	}
}
