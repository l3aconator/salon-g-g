<?php
/**
 * Archive template.
 *
 * @package Salon
 */

$context = Timber::context();

$templates = array( 'pages/page-archive.twig' );

if ( is_tax( 'designer_type' ) ) {
	$context['posts']     = new Timber\PostQuery( '\Salon\Models\Designer' );
	$context['post_type'] = 'designer';
	$context['no_posts']  = 'No designers in this category.';
	$context['term']      = single_term_title( '', false );
	$context['post']      = array( 'title' => 'Designers' );

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

		$hero = $designer_archive_page[0]->get_thumbnail();

		if ( ! isset( $context['post']['hero'] ) && $hero ) {
			$context['post']['hero'] = $hero;
		}
	}

	$context['types'] = $types;

	array_unshift( $templates, 'pages/page-archive-designer.twig' );
} else {
	$context['posts'] = new Timber\PostQuery();
}

Timber::render( $templates, $context );
