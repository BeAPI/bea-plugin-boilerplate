<?php

namespace BEA\PB;

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
		add_action( 'init', [ $this, 'init_translations' ] );
	}

	/**
	 * Load the plugin translation
	 */
	public function init_translations(): void {
		// Load translations
		load_plugin_textdomain( 'bea-plugin-boilerplate', false, dirname( BEA_PB_PLUGIN_BASENAME ) . '/languages' );
	}

}
