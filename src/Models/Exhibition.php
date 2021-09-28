<?php
/**
 * Exhibition model.
 *
 * @package Salon
 */

namespace Salon\Models;

use Timber\Image;
use Salon\Models\Designer;
use Salon\Repositories\ExhibitionRepository;

/** Class */
class Exhibition extends Post {
	/**
	 * Get an exhibition's designers.
	 *
	 * @return array|null
	 */
	public function designer() {
		$designers = $this->meta( 'related_designer' );

		if ( empty( $designers ) ) {
			return null;
		}

		return new Designer( $designers[0] );
	}

	/**
	 * Checks if the current exhibition is current.
	 *
	 * @return boolean
	 */
	public function is_current() {
		$opening = $this->opening_day;
		$closing = $this->closing_day;
		$today   = gmdate( 'Ymd' );

		if ( $opening && $closing ) {
			return $today < $opening || $today < $closing;
		} if ( $opening ) {
			return true;
		} elseif ( $closing ) {
			return $today < $closing;
		}

		return false;
	}

	/**
	 * Get the map image.
	 *
	 * @return \Timber\Image|null
	 */
	public function details_image() {
		$img = $this->meta( 'details_bg_image' );

		if ( ! $img ) {
			return null;
		}

		return new Image( $img );
	}

	/**
	 * Gets past galleries.
	 *
	 * @return array
	 */
	public function past_exhibitions() {
		$repo             = new ExhibitionRepository();
		$past_exhibitions = $repo->past( 4, array( $this->ID ) )->get();

		if ( $past_exhibitions->found_posts < 1 ) {
			return array();
		}

		return $past_exhibitions;
	}

}
