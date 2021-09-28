<?php
/**
 * Homepage model.
 *
 * @package Salon
 */

namespace Salon\Models;

use Timber;
use Salon\Models\Post;

/** Class */
class Homepage extends Post {
	/**
	 * The callout blocks.
	 *
	 * @return array|false
	 */
	public function callout_blocks() {
		$callout_blocks = $this->meta( 'callout_blocks' );

		if ( empty( $callout_blocks ) ) {
			return false;
		}

		$callout_blocks_clean = array();

		foreach ( $callout_blocks as $block ) {
			if ( empty( $block['link'] ) ) {
				continue;
			}

			$link = new Post( $block['link'][0] );

			if ( empty( $block['title'] ) ) {
				$block['title'] = $link->title;
			}

			if ( empty( $block['image'] ) ) {
				$block['image'] = $link->post_image();
			} else {
				$block['image'] = new Timber\Image( $block['image'] );
			}

			$block['link'] = $link;
			$callout_blocks_clean[] = $block;
		}

		return $callout_blocks_clean;
	}

	/**
	 * The carousel items.
	 *
	 * @return array|false
	 */
	public function carousel_items() {
		$rows = $this->meta( 'feature_carousel' );

		if ( empty( $rows['items'] ) ) {
			return false;
		}

		$slides = array();

		foreach ( $rows['items'] as $block ) {
			if ( empty( $block['link'] ) ) {
				continue;
			}

			$_post = $block['link'][0];
			$link  = 'product' === $_post->post_type ? new Product( $_post ) : new Post( $_post );

			if ( ! empty( $block['description'] ) ) {
				$link->override_description = $block['description'];
			}

			if ( ! empty( $block['image'] ) ) {
				$link->override_image = new Timber\Image( $block['image'] );
			}

			$slides[] = $link;
		}

		return $slides;
	}

	/**
	 * Subscribe block.
	 *
	 * @return array|boolean
	 */
	public function subscribe_block() {
		$block = $this->meta( 'subscribe_block' );

		if ( ! isset( $block['text'] ) ) {
			return false;
		}

		return $block;
	}

	/**
	 * Contact block.
	 *
	 * @return array|boolean
	 */
	public function contact_block() {
		$block = $this->meta( 'contact_block' );
		return $block;
	}
}
