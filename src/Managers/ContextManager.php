<?php
/**
 * Set up context.
 *
 * @package Salon
 */

namespace Salon\Managers;

use Timber\Menu;

/** Class */
class ContextManager {
	/**
	 * Run.
	 *
	 * @return void
	 */
	public function run() {
		$context_functions = array(
			'theme_version',
			'is_home',
			'menus',
			'acf_options',
			'cart',
		);

		foreach ( $context_functions as $fn ) {
			add_filter( 'timber/context', array( $this, $fn ) );
		}
	}


	/**
	 * Expose current theme version to Timber context
	 *
	 * @param array $context Timber context.
	 *
	 * @return array
	 */
	public function theme_version( $context ) {
		$context['theme_version'] = SALON_THEME_VERSION;

		return $context;
	}

	/**
	 * Adds ability to check if we are on the homepage in a twig file
	 *
	 * @param array $context Timber context.
	 *
	 * @return array
	 */
	public function is_home( $context ) {
		$context['is_home']       = is_home();
		$context['is_front_page'] = is_front_page();
		$context['is_shop']       = is_woocommerce();
		return $context;
	}

	/**
	 * Registers and adds menus to context
	 *
	 * @param array $context Timber context.
	 *
	 * @return array
	 */
	public function menus( $context ) {
		$context['main_nav'] = new Menu( 'main_nav' );
		$context['shop_filters'] = new Menu( 'shop_filters' );
		return $context;
	}

	/**
	 * Adds ability to access array of ACF options fields in a twig field
	 *
	 * @param array $context Timber context.
	 *
	 * @return array
	 */
	public function acf_options( $context ) {
		if ( class_exists( 'acf' ) ) {
			$context['options'] = get_fields( 'option' );
		}
		return $context;
	}

	/**
	 * Cart data.
	 *
	 * @param array $context Timber context.
	 *
	 * @return array
	 */
	public function cart( $context ) {
		global $woocommerce;

		$item_count = $woocommerce->cart->get_cart_contents_count();

		$cart = array(
			'show_flag' => ! is_cart() && ! is_checkout() && $item_count > 0,
			'count'     => $item_count,
			'subtotal'  => $woocommerce->cart->get_subtotal(),
			'link'      => wc_get_cart_url(),
		);

		$context['cart'] = $cart;

		return $context;
	}
}
