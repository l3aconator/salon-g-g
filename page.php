<?php
/**
 * Page.
 *
 * @package Salon
 */

$context = Timber::context();

$post = Timber::get_post( 'Salon\Models\Post' );

$context['post'] = $post;

if ( is_checkout() ) {
	Timber::render( 'woocommerce/checkout.twig', $context );
	return;
}

if ( is_cart() ) {
	Timber::render( 'woocommerce/cart.twig', $context );
	return;
}

Timber::render( 'pages/page.twig', $context );
