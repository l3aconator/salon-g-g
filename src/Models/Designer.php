<?php
/**
 * Designer model.
 *
 * @package Salon
 */

namespace Salon\Models;

use Timber\Image;
use Salon\Repositories\ProductRepository;

/** Class */
class Designer extends Post {
	/**
	 * Get an artist's products.
	 *
	 * @return array|boolean
	 */
	public function products() {
		$products_repo = new ProductRepository();
		return $products_repo->by_designer( $this->ID )->get();
	}

	/**
	 * The link to their shop page.
	 *
	 * @return string
	 */
	public function shop_link() {
		return '/shop/designer/' . $this->slug;
	}

	/**
	 * The related designer recirculation.
	 *
	 * @return array|boolean
	 */
	public function similar_designers() {
		$recirc = $this->meta( 'designer_recirc' );

		if ( ! $recirc ) {
			return false;
		}

		$designers = array();

		foreach ( $recirc as $designer ) {
			$designers[] = new Designer( $designer );
		}

		return $designers;
	}
}
