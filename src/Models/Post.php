<?php
/**
 * Default Post model.
 *
 * @package Salon
 */

namespace Salon\Models;

use Timber\Image;
use Salon\Blocks\PhotoGrid\PhotoGrid;

/** Class */
class Post extends \Timber\Post {

	/**
	 * Get the header theme.
	 *
	 * @return string
	 */
	public function header_theme() {
		return is_woocommerce() || is_cart() || is_checkout() || ( is_front_page() && ! $this->hero() ) ? 'dark' : 'light';
	}

	/**
	 * Gets the post's hero banner iamge.
	 *
	 * @return Timber\Image|boolean
	 */
	public function hero() {
		if ( 'page' === $this->post_type ) {
			return $this->thumbnail();
		}

		$hero = $this->meta( 'hero_banner' );

		if ( $hero ) {
			return new Image( $hero );
		}

		return false;
	}

	/**
	 * Featured image.
	 *
	 * @return Timber\Image
	 */
	public function post_image() {
		$feature_photo = $this->meta( 'feature_photo' );
		if ( $feature_photo ) {
			return new Image( $feature_photo );
		}

		return $this->thumbnail();
	}

	/**
	 * Featured image.
	 *
	 * @return Timber\Image|boolean
	 */
	public function hover_image() {
		$photo = $this->meta( 'tease_hover_photo' );
		if ( $photo ) {
			return new Image( $photo );
		}

		return false;
	}

	/**
	 * Carousel image. This will look for images in this order:
	 * 1. The `wide_photo`.
	 * 2. If this is a page, the `post_thumbnail`; otherwise, the `hero_banner`.
	 * 3. The `feature_photo`.
	 * 4. The `post_thumbnail`.
	 *
	 * @return Timber\Image|boolean
	 */
	public function carousel_image() {
		$photo = $this->meta( 'wide_photo' );

		if ( $photo ) {
			return new Image( $photo );
		}

		$photo = $this->hero();

		if ( $photo ) {
			return $photo;
		}

		return $this->post_image();
	}

	/**
	 * Override the preview.
	 *
	 * @return string
	 */
	public function preview() {
		$preview  = parent::preview();
		$stripped = preg_replace( '/([^?!.]*.).*/', '\\1', $preview->read_more( false ) );
		return $stripped;
	}

	/**
	 * Get the photo grid and organize the images into the right gridded order.
	 *
	 * @return array | boolean
	 */
	public function photo_grid() {
		$photos = $this->meta( 'photo_grid' );

		if ( empty( $photos ) ) {
			return false;
		}

		return PhotoGrid::setup_grid( $photos );
	}
}
