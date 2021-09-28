<?php
/**
 * Manager for ACF things.
 *
 * @package Salon
 */

namespace Salon\Managers;

use Salon\Models\Post;

/** Class */
class AcfManager {
	/**
	 * Run.
	 *
	 * @return void
	 */
	public function run() {
		add_filter( 'acf/fields/relationship/query', array( $this, 'post_relationship_query' ), 10, 3 );
		add_filter( 'acf/fields/post_object/query/key=field_5f5850d63f7ca', array( $this, 'designer_product_query' ), 10, 3 );
		add_filter( 'acf/fields/relationship/query', array( $this, 'customize_relationship_query' ), 10, 1 );
		add_filter( 'acf/fields/relationship/result', array( $this, 'customize_relationship_featured_image' ), 10, 2 );

		add_filter( 'acf/location/rule_values/page_type', array( $this, 'page_type_values' ) );
		add_filter( 'acf/location/rule_match/page_type', array( $this, 'page_type_match' ), 10, 3 );
	}

	/**
	 * Modify ACF relationship field to show most recent posts instead of alpha
	 *
	 * @param array  $args    Args.
	 * @param string $field   Field.
	 * @param int    $post_id Post ID.
	 *
	 * @return array
	 */
	public function post_relationship_query( $args, $field, $post_id ) {
		// Order returned query collection by date, starting with most recent.
		$args['order']   = 'DESC';
		$args['orderby'] = 'post_date';

		return $args;
	}

	/**
	 * Modify ACF post_object field in the designer photo grid to only show products
	 * that belong to the current designer.
	 *
	 * @param array  $args    Args.
	 * @param string $field   Field.
	 * @param int    $post_id Post ID.
	 *
	 * @return array
	 */
	public function designer_product_query( $args, $field, $post_id ) {
		if ( 'designer' === get_post_type( $post_id ) ) {
			$args['meta_query'] = array(
				array(
					'key'     => 'designer',
					'value'   => $post_id,
					'compare' => '=',
				),
			);
		}

		return $args;
	}


	/**
	 * Exclude specific pages from the relationship queries.
	 *
	 * @param array $args Query args.
	 *
	 * @return array
	 */
	public function customize_relationship_query( $args ) {
		$exclude_pages = array(
			get_option( 'woocommerce_cart_page_id' ),
			get_option( 'woocommerce_checkout_page_id' ),
			get_option( 'woocommerce_pay_page_id' ),
			get_option( 'woocommerce_thanks_page_id' ),
			get_option( 'woocommerce_myaccount_page_id' ),
			get_option( 'woocommerce_edit_address_page_id' ),
			get_option( 'woocommerce_view_order_page_id' ),
			get_option( 'woocommerce_terms_page_id' ),
			get_option( 'wp_page_for_privacy_policy' ),
		);

		$args['post__not_in'] = array_filter( $exclude_pages );

		return $args;
	}

	/**
	 * Customize the ACF featured image in relationship fields.
	 *
	 * @param string  $text The text displayed for this post (the post title).
	 * @param WP_Post $post The Relationship.
	 *
	 * @return string
	 */
	public function customize_relationship_featured_image( $text, $post ) {
		$p     = new Post( $post );
		$thumb = $p->post_image();
		$img   = $thumb ? sprintf( '<img src="%s">', $thumb->src( 'thumbnail' ) ) : '';
		return '<div class="thumbnail">' . $img . '</div>' . $text;
	}

	/**
	 * Add a "Shop Page" option to the page_type options.
	 *
	 * @param array $choices The page_type choices.
	 *
	 * @return array
	 */
	public function page_type_values( $choices ) {
		$choices['shop_page'] = __( 'Shop Page' );
		return $choices;
	}

	/**
	 * Add logic to show the field group for the shop page.
	 *
	 * @param boolean $match   Whether to show the group or now.
	 * @param array   $rule    Parameters for the field group rule.
	 * @param array   $options Data about the post.
	 *
	 * @return boolean
	 */
	public function page_type_match( $match, $rule, $options ) {
		if ( ! isset( $options['post_id'] ) || ! $options['post_id'] || 'page' !== get_post_type( $options['post_id'] ) ) {
			return false;
		}

		$shop_page = get_option( 'woocommerce_shop_page_id' );

		// If we're not looking for the shop page or we're not on the shop page, just
		// return the match value.
		if ( 'shop_page' !== $rule['value'] || $options['post_id'] !== $shop_page ) {
			return $match;
		}

		if ( '==' === $rule['operator'] ) {
			$match = true;
		} elseif ( '!=' === $rule['operator'] ) {
			$match = false;
		}

		return $match;
	}
}
