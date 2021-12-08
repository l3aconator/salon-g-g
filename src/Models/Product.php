<?php
/**
 * Product model.
 *
 * @package Salon
 */

namespace Salon\Models;

use Timber\Image;

/** Class */
class Product extends Post {

	/**
	 * The Woocommerce product.
	 *
	 * @var WC_Product|null
	 */
	public $product;

	/**
	 * The designer object of this product.
	 *
	 * @var Salon\Models\Designer|null
	 */
	public $designer;

	/**
	 * Initialize the Woocommerce product.
	 *
	 * @param mixed $p Post reference.
	 *
	 * @return void
	 */
	public function __construct( $p = null ) {
		parent::__construct( $p );
		$this->set_designer();
		$this->product = wc_get_product( $this->ID );
		$this->stock = get_post_meta( $this->ID, '_stock_status', true );
		$this->category = array_values(get_the_terms( $this->ID, 'product_cat' ))[0];
	}

	/**
	 * Set designer on init to avoid woocommerce issues.
	 *
	 * @return void
	 */
	private function set_designer() {
		$designer       = $this->meta( 'designer' );
		$this->designer = new Designer( $designer );
	}

	/**
	 * Get the price.
	 *
	 * @return string
	 */
	public function price() {
		return $this->product->get_price();
	}

	/**
	 * Get the stock.
	 *
	 * @return string
	 */
	public function stock() {
		return $this->product-get_stock_quantity();
	}

	// /**
	//  * Get the category.
	//  *
	//  * @return string
	//  */
	// public function category() {
	// 	return $this->meta( 'category' );
	// }

	/**
	 * Get the product designer.
	 *
	 * @return Salon\Models\Designer|boolean
	 */
	public function designer() {
		return $this->designer;
	}

	/**
	 * The gallery of images.
	 *
	 * @return array|boolean
	 */
	public function product_gallery() {
		$image_ids = $this->product->get_gallery_image_ids();

		if ( empty( $image_ids ) ) {
			return false;
		}

		return array_map(
			function( $id ) {
				$img = new Image( $id );
				return array(
					'thumbnail' => $img->src( 'thumbnail' ),
					'full'      => $img->src( 'shop_single' ),
					'alt'       => $img->alt(),
				);
			},
			$image_ids
		);
	}

}
