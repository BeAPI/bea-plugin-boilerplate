<?php
namespace BEA\PB;

/**
 * The purpose of the main class is to init all the plugin base code like :
 *  - Taxonomies
 *  - Post types
 *  - Posts to posts relations etc.
 *  - Loading the text domain
 *
 * Class Main
 * @package BEA\PB
 */
class Main {

	public function __construct() {
		add_action( 'init', array( __CLASS__, 'init' ) );
	}

	/**
	 * Load the plugin translation
	 */
	public static function init() {
		// Load translations
		load_plugin_textdomain( 'bea-plugin-boilerplate', false, BEA_PB_DIR . 'languages' );
	}
}