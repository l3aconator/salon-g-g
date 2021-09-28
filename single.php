<?php
/**
 * Single post / article.
 *
 * @package Salon
 */

use Salon\Models\Post;
use Salon\Models\Designer;
use Salon\Models\Exhibition;

$context   = Timber::context();
$post_type = get_post_type();

switch ( $post_type ) {
	case 'designer':
		$post = new Designer();
		break;
	case 'exhibition':
		$post = new Exhibition();

		$global_exhibition_content = get_field( 'site_exhibitions', 'option' );
		if ( isset( $global_exhibition_content['rsvp_form'] ) ) {
			$rsvp_form = $global_exhibition_content['rsvp_form'];
			$rsvp_form = preg_replace( '/(<input type="hidden" name="gallery" value=")(" class="wpcf7-form-control wpcf7-hidden" \/>)/', '${1}' . $post->title . '${2}', $rsvp_form );
			$rsvp_form = preg_replace( '/(<input type="hidden" name="gallery_id" value=")(" class="wpcf7-form-control wpcf7-hidden" \/>)/', '${1}' . $post->ID . '${2}', $rsvp_form );

			$context['rsvp_form'] = $rsvp_form;
		}

		break;
	default:
		$post = new Post();
		break;
}

$context['post'] = $post;

$templates = array(
	'pages/single-' . $post_type . '.twig',
	'pages/single.twig',
);

Timber::render( $templates, $context );
