<?php
/**
 * Repository entity for retrieving post type objects.
 *
 * @package Salon
 */

namespace Salon\Repositories;

/** Class */
class ProductRepository extends Repository {
	const POST_TYPE = array( 'product' );

	/**
	 * Returns a chronological list of latest posts of our current post type.
	 *
	 * @param int $designer Post ID of the designer whose products we're getting.
	 * @param int $limit    Number of posts to return.
	 * @param int $paged    Enable pagination.
	 *
	 * @return Repository
	 */
	public function by_designer( $designer, $limit = 10, $paged = 0 ) {

		// Set sane defaults so we don't do full table scans.
		if ( $limit <= 0 || $limit > 1000 ) {
			$limit = 1000;
		}

		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		}

		$meta_query = array(
			array(
				'key'     => 'designer',
				'value'   => $designer,
				'compare' => '=',
			),
		);

		if ( get_query_var( 'in_stock' ) ) {
			array_push(
				$meta_query,
				array(
					'key'   => '_stock_status',
					'value' => 'instock',
				),
				array(
					'key'   => '_backorders',
					'value' => 'no',
				),
			);
		}

		$params = array(
			'posts_per_page' => (int) $limit,
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'publish',
			'orderby'        => 'post_title',
			'order'          => 'DESC',
			'meta_query'     => $meta_query,
		);

		if ( (int) $paged > 0 ) {
			$params['paged'] = $paged;
		}

		return $this->query( $params, 'Salon\Models\Product' );
	}

	/**
	 * Returns a chronological list of posts by a specific tag.
	 *
	 * @param int|array $tags   Tag ID.
	 * @param int       $limit  Number of posts to return.
	 *
	 * @return Repository
	 */
	public function by_tag( $tags, $limit = 10 ) {

		// Set sane defaults so we don't do full table scans.
		if ( $limit <= 0 || $limit > 1000 ) {
			$limit = 1000;
		}

		$params = array(
			'posts_per_page' => (int) $limit,
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'publish',
			'orderby'        => 'post_title',
			'order'          => 'DESC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_tag',
					'terms'    => $tags,
					'field'    => 'term_id',
				),
			),
		);

		return $this->query( $params, 'Salon\Models\Product' );
	}
}
