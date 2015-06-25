<?php
namespace BEA_PB;

class Main {

	public function __construct() {
		add_action( 'init', array( __CLASS__, 'init' ) );
	}

	public static function init() {
		// Load translations
		load_plugin_textdomain( 'bea-plugin-boilerplate', false, basename( BEA_PB_DIR ) . '/languages' );
	}
}