<?php
/**
 * Bootstraps WordPress theme related functions, most importantly enqueuing
 * javascript and styles.
 *
 * @package Salon
 */

namespace Salon\Managers;

/** Class */
class ThemeManager {
	/**
	 * List of theme managers.
	 *
	 * @var array
	 */
	private $managers = array();

	/**
	 * Constructor
	 *
	 * @param array $managers Array of managers.
	 */
	public function __construct( array $managers ) {
		$this->managers = $managers;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'add_documentation_widget' ) );
		add_action( 'admin_init', array( $this, 'redirect_to_docs' ), 1 );
		add_action( 'admin_menu', array( $this, 'add_documentation_menu_item' ) );
		add_action( 'admin_init', array( $this, 'register_menus' ) );
		add_action( 'pre_get_posts', array( $this, 'modify_query' ) );
		add_action( 'init', array( $this, 'register_options' ), 1, 3 );
		add_action( 'init', array( $this, 'setup_designer_shop_rewrites' ) );
		add_action( 'template_redirect', array( $this, 'setup_designer_shop_template' ) );

		add_filter( 'body_class', array( $this, 'manage_body_classes' ) );
		add_filter( 'nav_menu_css_class', array( $this, 'manage_nav_classes' ), 10, 2 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_filter( 'wp_title', array( $this, 'manage_title_meta' ) );
		add_filter( 'image_resize_dimensions', array( $this, 'image_upscale' ), 10, 6 );
	}

	/**
	 * Runs initialization tasks.
	 *
	 * @return void
	 */
	public function run() {
		if ( count( $this->managers ) > 0 ) {
			foreach ( $this->managers as $manager ) {
				$manager->run();
			}
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 999 );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );

		add_image_size( 'hero', 1400 );
		add_image_size( 'feature', 486 );
		add_image_size( 'homepage_callout', 880, 660, true );
		add_image_size( 'grid_image', 800 );
		add_image_size( 'feature_square', 480, 480, true );
		add_image_size( 'carousel_image', 1200, 620, true );
	}

	/**
	 * Enqueue javascript using WordPress
	 *
	 * @return void
	 */
	public function enqueue() {
		// Remove default Gutenberg CSS.
		wp_deregister_style( 'wp-block-library' );

		/* wp_enqueue_script( 'vendor', SALON_THEME_URL . '/dist/vendor.js', array(), SALON_THEME_VERSION, true ); */

		// Enqueue custom js file, with cache busting.
		wp_enqueue_script( 'script.js', SALON_THEME_URL . '/dist/app.js', array(), SALON_THEME_VERSION, true );
	}

	/**
	 * Enqueue JS and CSS for WP admin panel
	 *
	 * @return void
	 */
	public function enqueue_admin() {
		wp_enqueue_style( 'admin-styles', SALON_THEME_URL . '/dist/admin.css', array(), SALON_THEME_VERSION );

		/* wp_enqueue_script( 'vendor', SALON_THEME_URL . '/dist/vendor.js', array(), SALON_THEME_VERSION, false ); */
		wp_enqueue_script( 'admin.js', SALON_THEME_URL . '/dist/admin.js', array(), SALON_THEME_VERSION, false );
	}

	/**
	 * Register nav menus
	 *
	 * @return void
	 */
	public function register_menus() {
		register_nav_menus(
			array(
				'main_nav' => 'Main navigation',
				'shop_filters' => 'Shop Filters',
			)
		);
	}


	/**
	 * Adds a widget to the dashboard with a link to editor docs
	 *
	 * @return void
	 */
	public function add_documentation_widget() {
		wp_add_dashboard_widget(
			'custom_dashboard_widget',
			'Editor Documentation',
			function () {
				echo "<p><a href='/wp-content/themes/salon/documentation/index.html' target='_blank' rel='noopener noreferrer'>View</a> the editor documentation</p>";
			}
		);
	}

	/**
	 * Adds a menu item to WP admin that links to editor docs
	 *
	 * @return void
	 */
	public function add_documentation_menu_item() {
		add_menu_page(
			'Editor Docs',
			'Editor Docs',
			'manage_options',
			'link-to-docs',
			array( $this, 'redirect_to_docs' ),
			'dashicons-admin-links',
			100
		);
	}


	/**
	 * To have an external link to the docs we need this weird function
	 *
	 * @return void
	 */
	public function redirect_to_docs() {
		$menu_redirect = isset( $_GET['page'] ) ? $_GET['page'] : false;
		if ( 'link-to-docs' === $menu_redirect ) {
			header( 'Location: https://' . $_SERVER['HTTP_HOST'] . '/wp-content/themes/salon/documentation' );
			exit();
		}
	}

	/**
	 * Add ACF options page to WordPress
	 *
	 * @return void
	 */
	public function register_options() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => 'Site Settings',
					'menu_title' => 'Site Settings',
					'menu_slug'  => 'site-settings',
				)
			);
		}
	}

	/**
	 * Sets up rewrite rules for designer's shop pages.
	 *
	 * @return void
	 */
	public function setup_designer_shop_rewrites() {
		add_rewrite_rule( 'shop/designer/([^/]+)[/]?$', 'index.php?designer_shop=$matches[1]', 'top' );
		add_rewrite_rule( 'shop/designer/([^\/]+)/page/(\d)[/]?$', 'index.php?designer_shop=$matches[1]&paged=$matches[2]', 'top' );
	}

	/**
	 * Sets up the template for designer shop pages.
	 *
	 * @return void
	 */
	public function setup_designer_shop_template() {
		if ( get_query_var( 'designer_shop' ) ) {
			add_filter(
				'template_include',
				function() {
					return get_template_directory() . '/woocommerce.php';
				}
			);
		}
	}

	/**
	 * Manage the classes added to the body.
	 *
	 * @param array $classes List of classes.
	 *
	 * @return array
	 */
	public function manage_body_classes( $classes ) {
		global $woocommerce;

		if ( is_shop() && ! get_query_var( 's' ) ) {
			$classes[] = 'shop-homepage';
		}

		if ( $woocommerce->cart->get_cart_contents_count() > 0 ) {
			$classes[] = 'has-cart';
		}

		return $classes;
	}


	/**
	 * Manages the classes applied to the main nav.
	 *
	 * @param array   $classes CSS classes that are applied to the menu item's
	 *                         <li> element.
	 * @param WP_Post $item    The current menu item.
	 *
	 * @return array
	 */
	public function manage_nav_classes( $classes, $item ) {
		global $post;

		$archive_type = get_field( 'archive_post_type', $item->object_id );

		if (
			( ! empty( $archive_type ) && $post && $post->post_type === $archive_type )
			|| ( is_tax( 'designer_type' ) && ! empty( $archive_type ) && 'designer' === $archive_type )
			|| ( ( is_cart() || is_checkout() || is_woocommerce() ) && get_option( 'woocommerce_shop_page_id' ) === $item->object_id )
		) {
			$classes[] = 'current_page_parent';
		}

		return $classes;
	}

	/**
	 * Handles different query modifications.
	 *
	 * @param WP_Query $query The query object.
	 *
	 * @return WP_Query
	 */
	public function modify_query( $query ) {
		if ( ! is_admin() && $query->is_main_query() && $query->get( 'wc_query' ) ) {
			$query->set( 'posts_per_page', 12 );

			if ( $query->get( 'in_stock' ) ) {
				$meta_query = $query->get( 'meta_query' );

				array_push(
					$meta_query,
					array(
						'key'   => '_stock_status',
						'value' => 'instock',
					),
					array(
						'key'   => '_backorders',
						'value' => 'no',
					),
				);

				$query->set( 'meta_query', $meta_query );
			}

			if ( $query->get( 'sort' ) ) {
				$query->set( 'order', $query->get( 'sort' ) );
			}

			if ( $query->get( 'sortBy' ) == 'price' ) {
				$query->set( 'orderby', 'meta_value_num' );
				$query->set( 'meta_key', '_price' );
			}

			if ( $query->get( 'sortBy' ) == 'date' ) {
				$query->set( 'orderby', 'publish_date' );
			}
		}

		if ( ! is_admin() && $query->is_main_query() && $query->is_archive && is_tax( 'designer_type' ) ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}

		return $query;
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Registered query variables.
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'in_stock';
		$vars[] = 'designer_shop';
		$vars[] = 'sort';
		$vars[] = 'sortBy';
		return $vars;
	}

	/**
	 * Manages the title meta value.
	 *
	 * @param string $title The title.
	 *
	 * @return string
	 */
	public function manage_title_meta( $title ) {
		if ( is_woocommerce() && is_tax() ) {
			$queried_object = get_queried_object();
			return 'Shop ' . $queried_object->name;
		}

		return $title;
	}

	/**
	 * Allow upscaling of images.
	 *
	 * @param null    $payload Variable to be filtered.
	 * @param int     $orig_w  Original image width in pixels.
	 * @param int     $orig_h  Original image height in pixels.
	 * @param int     $new_w   Destination image width in pixels.
	 * @param int     $new_h   Destination image height in pixels.
	 * @param boolean $crop    Flag to enable image croping.
	 *
	 * @return array
	 */
	public function image_upscale( $payload, $orig_w, $orig_h, $new_w, $new_h, $crop ) {
		if ( ! $crop ) {
			return null; // let the WordPress default function handle this.
		}

		$aspect_ratio = $orig_w / $orig_h;
		$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

		$crop_w = round( $new_w / $size_ratio );
		$crop_h = round( $new_h / $size_ratio );

		$s_x = floor( ( $orig_w - $crop_w ) / 2 );
		$s_y = floor( ( $orig_h - $crop_h ) / 2 );

		return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
	}

}
