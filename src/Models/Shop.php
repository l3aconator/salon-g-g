<?php
/**
 * Product model.
 *
 * @package Salon
 */

namespace Salon\Models;

use Timber\Term;
use Timber\Image;
use Salon\Repositories\ProductRepository;
use Salon\Repositories\PostRepository;

/** Class */
class Shop extends Post {

	/**
	 * Featured banner, cached.
	 *
	 * @var array
	 */
	private $banner = array();

	/**
	 * The banner category, cached.
	 *
	 * @var Timber\Term|null
	 */
	private $featured_category;


	/**
	 * Initialize the Woocommerce product.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( wc_get_page_id( 'shop' ) );

		$this->banner = $this->meta( 'featured_banner' );
	}

	/**
	 * Gets the banner title.
	 *
	 * @return string
	 */
	public function banner_title() {
		$featured_category = $this->get_featured_category();

		if ( ! $featured_category ) {
			return 'Shop';
		}

		return $featured_category->title;
	}

	/**
	 * Gets the banner description.
	 *
	 * @return string
	 */
	public function banner_description() {
		if ( $this->banner['description'] ) {
			return $this->banner['description'];
		}

		return '';
	}

	/**
	 * Gets the banner image.
	 *
	 * @return Timber\Image|false
	 */
	public function banner_image() {
		if ( $this->banner['image'] ) {
			return new Image( $this->banner['image'] );
		}

		return false;
	}

	/**
	 * Gets the banner link.
	 *
	 * @return string|null
	 */
	public function banner_link() {
		$featured_category = $this->get_featured_category();

		if ( ! $featured_category ) {
			return null;
		}

		return $featured_category->link;
	}

	/**
	 * Gets the banner term.
	 *
	 * @return string|null
	 */
	public function banner_term() {
		$featured_category = $this->get_featured_category();

		if ( ! $featured_category ) {
			return null;
		}

		return $featured_category->title;
	}

	/**
	 * Get carousel 1.
	 *
	 * @return array|null;
	 */
	public function carousel_1() {
		$content = $this->meta( 'product_carousel_1' );
		return $this->get_carousel_content( $content );
	}

	/**
	 * Get featured categories blocks.
	 *
	 * @return array|null
	 */
	public function featured_categories() {
		$group = $this->meta( 'featured_categories' );

		if ( empty( $group ) || empty( $group['categories'] ) ) {
			return null;
		}

		$section = array(
			'title'  => $group['title'] ? $group['title'] : 'Categories',
			'blocks' => array(),
		);

		foreach ( $group['categories'] as $cat ) {
			if ( empty( $cat['category'] ) ) {
				continue;
			}

			$term = new Term( $cat['category'], 'product_cat' );

			if ( ! $term ) {
				continue;
			}

			$block = array(
				'title' => $term->title,
				'link'  => $term->link,
				'image' => $cat['image'] ? $cat['image']['sizes']['grid_image'] : false,
			);

			$section['blocks'][] = $block;
		}

		return $section;
	}

	// public function products() {
	// 	$products_repo = new ProductRepository();
	// 	return $products_repo->by_designer( $this->ID )->get();
	// }


	/** ---------- Private getters ---------- */

	/**
	 * Retrieve the featured category.
	 *
	 * @return Timber\Term|null
	 */
	private function get_featured_category() {
		if ( $this->featured_category ) {
			return $this->featured_category;
		}

		$featured_category = $this->banner['category'];

		if ( $featured_category ) {
			$term                    = new Term( $featured_category, 'product_cat' );
			$this->featured_category = $term;
			return $term;
		}

		return false;
	}

	/**
	 * Gets carousel content.
	 *
	 * @param array $content Data from the field.
	 *
	 * @return array|null
	 */
	private function get_carousel_content( $content ) {
		if ( ! $content['tag'] ) {
			return null;
		}

		$carousel      = array();
		$products_repo = new ProductRepository();
		$term          = new Term( $content['tag'], 'product_tag' );

		$carousel['title']    = $content['title'];
		$carousel['products'] = $products_repo->by_tag( $content['tag'], 6 )->get();
		$carousel['link']     = $term->link;

		if ( ! $carousel['title'] ) {
			$carousel['title'] = $term->title;
		}

		return $carousel;
	}
}
