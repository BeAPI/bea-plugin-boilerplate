<?php

namespace BEA\PB;

use BEA\PB\Controllers\Example_Controller;
use BEA\PB\Post_Types\Custom_Post_Type;
use BEA\PB\Routes\Router;
use BEA\PB\Shortcodes\Shortcode_Factory;

/**
 * The purpose of the main class is to init all the plugin base code like :
 *  - Taxonomies
 *  - Post types
 *  - Shortcodes
 *  - Posts to posts relations etc.
 *  - Loading the text domain
 *
 * Class Main
 * @package BEA\PB
 */
class Main {
	/**
	 * Use the trait
	 */
	use Singleton;

	protected function init(): void {
		add_action( 'init', [ $this, 'boot_router' ], 0 );
		add_action( 'init', [ $this, 'register_post_types' ], 0 );
		add_action( 'init', [ $this, 'init_translations' ] );
		add_action( 'init', [ $this, 'register_shortcodes' ] );

		Example_Controller::get_instance();
	}

	/**
	 * Boot custom rewrite elements.
	 */
	public function boot_router(): void {
		Router::boot();
	}

	/**
	 * Register example post types and taxonomies.
	 */
	public function register_post_types(): void {
		Custom_Post_Type::register();
	}

	/**
	 * Register example shortcodes.
	 */
	public function register_shortcodes(): void {
		Shortcode_Factory::register( 'Hello' );
	}

	/**
	 * Load the plugin translation
	 */
	public function init_translations(): void {
		load_plugin_textdomain( 'bea-plugin-boilerplate', false, dirname( BEA_PB_PLUGIN_BASENAME ) . '/languages' );
	}
}
