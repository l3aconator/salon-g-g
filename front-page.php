<?php
/**
 * Page.
 *
 * @package Salon
 */

$context = Timber::context();

$post = Timber::get_post( 'Salon\Models\Homepage' );

$context['callout_blocks'] = $post->callout_blocks();
$context['post']           = $post;


Timber::render( 'pages/front-page.twig', $context );
