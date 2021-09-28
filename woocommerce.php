<?php
/**
 * Woocommerce point of entry.
 *
 * @package Salon
 */

global $wp;

use Timber\PostQuery;
use Salon\Repositories\PostRepository;
use Salon\Models\Product;
use Salon\Models\Shop;
use Salon\Models\Designer;
use Salon\Services\WordPressService;

if ( ! class_exists( 'Timber' ) ) {
	echo 'Timber not activated. Make sure you activate the plugin in <a href="/wp-admin/plugins.php#timber">/wp-admin/plugins.php</a>';
	return;
}

$context            = Timber::context();
$context['sidebar'] = Timber::get_widgets( 'shop-sidebar' );

/**
 * If this is a singular product.
 */
if ( is_singular( 'product' ) ) {
	$product         = new Product();
	$context['post'] = $product;

	// Restore the context and loop back to the main query loop.
	wp_reset_postdata();

	Timber::render( 'woocommerce/single-product.twig', $context );
	return;
}

/**
 * Otherwise, we're on the homepage or an archive page.
 */

// Get the categories and the designers for the filters.
$designers_repo = new PostRepository( 'designer', '\Salon\Models\Designer' );
$categories     = Timber::get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'exclude'    => array( 15 ),
		'orderby'    => 'name',
	)
);

$context['designers']  = $designers_repo->all()->get();
$context['categories'] = $categories;


// On the shop homepage (but NOT the search page).
if ( is_shop() && ! is_search() ) {
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
	$context['post'] = new Shop();
	Timber::render( 'woocommerce/shop.twig', $context );
	return;
}

// If nothing else, we're on an archive.
$products       = array();
$in_stock_only  = get_query_var( 'in_stock' );
$queried_object = get_queried_object();
$page_url       = home_url( $wp->request );
$pos            = strpos( $page_url, '/page' );

if ( $pos ) {
	$page_url = substr( $page_url, 0, $pos );
}

if ( is_tax() ) {
	$context['title'] = $queried_object->name;
	$context['type']  = $queried_object->taxonomy;
} elseif ( is_search() ) {
	$context['query'] = get_search_query();
	$page_url         = add_query_arg( $_SERVER['QUERY_STRING'], '', $page_url );
} elseif ( get_query_var( 'designer_shop' ) ) {
	$designer_object = get_page_by_path( get_query_var( 'designer_shop' ), OBJECT, 'designer' );

	if ( empty( $designer_object ) ) {
		return WordPressService::abort_request( 404 );
	}

	$designer = new Designer( $designer_object->ID );

	$products              = $designer->products();
	$context['title']      = $designer->title;
	$context['wp_title']   = 'Shop products from ' . $designer->title;
	$context['pagination'] = $products->pagination();
}

if ( empty( $products ) ) {
	$products = new PostQuery( '\Salon\Models\Product' );
}

$context['in_stock_only']        = $in_stock_only;
$context['in_stock_filter_link'] = $in_stock_only ? remove_query_arg( 'in_stock', $page_url ) : add_query_arg( 'in_stock', 1, $page_url );
$context['products']             = $products;
$context['post']                 = array( 'header_theme' => 'dark' );

Timber::render( 'woocommerce/archive.twig', $context );
