<?php
/*
 Plugin Name: BEA Plugin Name
 Version: 1.0.0
 Version Boilerplate: 3.0.0
 Plugin URI: http://www.beapi.fr
 Description: Your plugin description
 Author: BE API Technical team
 Author URI: http://www.beapi.fr
 Domain Path: languages
 Text Domain: bea-plugin-boilerplate

 ----

 Copyright 2015 BE API Technical team (human@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin tables
global $wpdb;
$wpdb->tables[]     = 'sample_table';
$wpdb->sample_table = $wpdb->prefix . 'sample_table';

// Plugin constants
define( 'BEA_PB_VERSION', '1.0.0' );
define( 'BEA_PB_MIN_PHP_VERSION', '5.4' );
define( 'BEA_PB_VIEWS_FOLDER_NAME', 'bea-pb' );
define( 'BEA_PB_CPT_NAME', 'custom_post_type' );
define( 'BEA_PB_TAXO_NAME', 'custom_taxonomy' );

// Plugin URL and PATH
define( 'BEA_PB_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_PB_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Do not deactivate plugin but stop interpretation of the plugin
 * Skip the plugin loading
 */
if( ! class_exists( '\BEA\Autoloader' ) ) {
	if( is_admin() ) {
		trigger_error( __( 'Plugin Boilerplate requires the BEA\Autoloader class to be activated.', 'bea-plugin-boilerplate' ) );
	}
	return;
}

// possibly display a notice, trigger error

/**
 * Check the PHP version defined on the plugin constant
 */
if ( version_compare( PHP_VERSION, BEA_PB_MIN_PHP_VERSION, '<' ) ) {
	require_once( BEA_PB_DIR . 'compat.php' );
	// possibly display a notice, trigger error
	add_action( 'admin_init', array( 'BEA\PB\Compatibility', 'admin_init' ) );
	// stop execution of this file
	return;
}

/**
 * Autoload all the things \o/
 */
require_once BEA_PB_DIR . 'autoload.php';

/**
 * Plugin activation/deactivation hooks
 *
 */
register_activation_hook( __FILE__, array( '\BEA\PB\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\BEA\PB\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', 'init_bea_pb_plugin' );
/**
 * Init the plugin
 */
function init_bea_pb_plugin() {
	// Client
	\BEA\PB\Main::get_instance();

	// Admin
	if ( is_admin() ) {
		\BEA\PB\Admin\Main::get_instance();
	}

	// Widgets
	add_action( 'widgets_init', function () {
		register_widget( '\BEA\PB\Widgets\Main' );
	} );
}
