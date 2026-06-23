<?php

require dirname( __DIR__ ) . '/vendor/autoload.php';

define( 'BEA_PB_VERSION', '1.0.0' );
define( 'BEA_PB_VIEWS_FOLDER_NAME', 'bea-pb' );
define( 'BEA_PB_CPT_NAME', 'custom_post_type' );
define( 'BEA_PB_TAXO_NAME', 'custom_taxonomy' );
define( 'BEA_PB_URL', 'https://example.test/wp-content/plugins/bea-plugin-boilerplate/' );
define( 'BEA_PB_DIR', dirname( __DIR__ ) . '/' );
define( 'BEA_PB_PLUGIN_BASENAME', 'bea-plugin-boilerplate/bea-plugin-boilerplate.php' );

if ( ! class_exists( 'WP_User' ) ) {
	/**
	 * Minimal WP_User stub for unit tests.
	 */
	class WP_User {
		/**
		 * @var int
		 */
		public $ID;

		/**
		 * @param bool $exists Whether the user exists.
		 */
		public function exists() {
			return true;
		}

		/**
		 * @param string $key User meta key.
		 */
		public function get( $key ) {
			unset( $key );

			return '';
		}
	}
}
