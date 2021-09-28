<?php
/**
 * Generic 404 page controller.
 *
 * @package Salon
 */

$context = Timber::context();

// Page title.
$context['post'] = array(
	'title' => '404',
);

// Render view.
Timber::render( 'pages/404.twig', $context );
