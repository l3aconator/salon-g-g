<?php
/**
 * Repository entity for retrieving Exhibitions.
 *
 * @package Salon
 */

namespace Salon\Repositories;

/** Class */
class ExhibitionRepository extends Repository {
	const POST_TYPE = array( 'exhibition' );

	/**
	 * Returns a chronological list of previous exhibitions.
	 *
	 * @param int   $limit   Number of posts to return.
	 * @param array $exclude Posts to exclude from the query.
	 *
	 * @return Repository
	 */
	public function past( $limit = 5, $exclude = array() ) {

		// Set sane defaults so we don't do full table scans.
		if ( $limit <= 0 || $limit > 1000 ) {
			$limit = 1000;
		}

		$today = gmdate( 'Ymd' );

		$params = array(
			'posts_per_page' => (int) $limit,
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'publish',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => 'closing_day',
					'compare' => '<=',
					'value'   => $today,
				),
			),
		);

		if ( ! empty( $exclude ) ) {
			$params['post__not_in'] = $exclude;
		}

		return $this->query( $params, 'Salon\Models\Exhibition' );
	}
}
