<?php
/**
 * WP Theme constants and setup functions
 *
 * @package Salon
 */

/**
 * Require the autoloader.
 */
require_once 'vendor/autoload.php';

use Salon\Managers\ThemeManager;

define( 'SALON_THEME_URL', get_stylesheet_directory_uri() );
define( 'SALON_THEME_PATH', dirname( __FILE__ ) . '/' );
define( 'SALON_DOMAIN', get_site_url() );
define( 'SALON_SITE_NAME', get_bloginfo( 'name' ) );
define( 'SALON_THEME_VERSION', wp_get_theme()->get( 'Version' ) );

/**
 * Use Dotenv to set required environment variables and load .env file when present.
 */
Dotenv\Dotenv::create( __DIR__ )->safeLoad();

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define( 'WP_ENV', getenv( 'WP_ENV' ) ?: 'production' );

$timber = new Timber\Timber();

Timber::$dirname = array( 'templates' );

add_action(
	'after_setup_theme',
	function () {
		$managers = [
			new \Salon\Managers\WordPressManager(),
			new \Salon\Managers\CustomPostsManager(),
			new \Salon\Managers\TaxonomiesManager(),
			new \Salon\Managers\GutenbergManager(),
			new \Salon\Managers\AcfManager(),
			new \Salon\Managers\WoocommerceManager(),
			new \Salon\Managers\ContextManager(),
		];

		$theme_manager = new ThemeManager( $managers );
		$theme_manager->run();
	}
);

/**
 * Woocommerce context function.
 *
 * @param WP_Post $post The post object.
 *
 * @return void
 */
function timber_set_product( $post ) {
	global $product;

	if ( ! empty( $post ) && is_woocommerce() ) {
		$product = wc_get_product( $post->ID );
	}
}
