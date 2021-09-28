<?php
/**
 * Page.
 *
 * Template Name: Archive Page
 *
 * @package Salon
 */

use Salon\Repositories\PostRepository;

$context = Timber::context();
$post    = Timber::get_post( '\Salon\Models\Post' );

$post_type = $post->meta( 'archive_post_type' );
$post_repo = new PostRepository( $post_type, '\Salon\Models\Post' );

if ( 'designer' === $post_type ) {
	$posts = $post_repo->all()->get();
	$types = Timber::get_terms(
		array(
			'taxonomy'   => 'designer_type',
			'hide_empty' => false,
			'orderby'    => 'name',
		)
	);

	$designer_archive_page = Timber::get_posts(
		array(
			'post_type'  => 'page',
			'meta_query' => array(
				array(
					'key'   => 'archive_post_type',
					'value' => 'designer',
				),
			),
		)
	);

	if ( $designer_archive_page ) {
		$types = array_merge(
			array(
				array(
					'title' => 'All',
					'link'  => $designer_archive_page[0]->link,
				),
			),
			$types
		);
	}

	$context['types'] = $types;
} elseif ( 'press' === $post_type ) {
	$posts = $post_repo->latest( 1000 )->get();
} else {
	$posts = $post_repo->latest()->get();
}

$context['post']      = $post;
$context['posts']     = $posts;
$context['post_type'] = $post_type;

Timber::render( array( 'pages/page-archive-' . $post_type . '.twig', 'pages/page-archive.twig' ), $context );
