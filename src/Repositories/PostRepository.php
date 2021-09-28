<?php
/**
 * Repository entity for retrieving post type objects.
 *
 * @package Salon
 */

namespace Salon\Repositories;

/** Class */
class PostRepository extends Repository {
	/**
	 * The post type.
	 *
	 * @var string
	 */
	private $post_type = '';

	/**
	 * The post class to return models of.
	 *
	 * @var string
	 */
	private $post_class = '\Timber\Post';

	/**
	 * Initialize and set the post type for this repository.
	 *
	 * @param string $post_type  The post type.
	 * @param string $post_class Post class to return models of.
	 *
	 * @return void
	 */
	public function __construct( $post_type, $post_class = null ) {
		$this->post_type = $post_type;

		if ( $post_class ) {
			$this->post_class = $post_class;
		}
	}

	/**
	 * Returns a chronological list of latest posts of our current post type.
	 *
	 * @param integer $limit Number of posts to return.
	 * @param integer $paged Enable pagination.
	 *
	 * @return Repository
	 */
	public function latest( $limit = 10, $paged = 0 ) {

		// Set sane defaults so we don't do full table scans.
		if ( $limit <= 0 || $limit > 1000 ) {
			$limit = 1000;
		}

		$params = array(
			'posts_per_page' => (int) $limit,
			'post_type'      => $this->post_type,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( (int) $paged > 0 ) {
			$params['paged'] = $paged;
		}

		return $this->query( $params, $this->post_class );
	}

	/**
	 * Get all (well, the first 1,000) posts of the post type.
	 *
	 * @return Repository
	 */
	public function all() {
		$limit = 1000;

		$params = array(
			'posts_per_page' => (int) $limit,
			'post_type'      => $this->post_type,
			'post_status'    => 'publish',
			'orderby'        => 'post_title',
			'order'          => 'ASC',
		);

		return $this->query( $params, $this->post_class );
	}
}
