<?php
/*
Plugin Name: BEA Plugin Name
Version: 1.0.0
Version Boilerplate: 3.6.0
Plugin URI: https://beapi.fr
Description: Your plugin description
Author: Be API Technical team
Author URI: https://beapi.fr
Domain Path: languages
Text Domain: bea-plugin-boilerplate
Requires at least: 6.0
Requires PHP: 8.0
Requires Plugins: advanced-custom-fields

----

Copyright 2021-2026 Be API Technical team (human@beapi.fr)

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

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Plugin constants
define( 'BEA_PB_VERSION', '1.0.0' );
define( 'BEA_PB_VIEWS_FOLDER_NAME', 'bea-pb' );
define( 'BEA_PB_CPT_NAME', 'custom_post_type' );
define( 'BEA_PB_TAXO_NAME', 'custom_taxonomy' );

// Plugin URL and PATH
define( 'BEA_PB_URL', plugin_dir_url( __FILE__ ) );
define( 'BEA_PB_DIR', plugin_dir_path( __FILE__ ) );
define( 'BEA_PB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once BEA_PB_DIR . 'inc/autoload.php';

if ( ! bea_pb_load_composer_autoload() ) {
	add_action(
		'admin_notices',
		static function (): void {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html__(
					'BEA Plugin Boilerplate requires Composer autoloading. Register the plugin PSR-4 namespace in your Bedrock root composer.json (then run composer dump-autoload), or run composer install in the plugin directory when developing this repository standalone.',
					'bea-plugin-boilerplate'
				)
			);
		}
	);

	return;
}

register_activation_hook( __FILE__, [ \BEA\PB\Plugin::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ \BEA\PB\Plugin::class, 'deactivate' ] );

add_action( 'plugins_loaded', 'init_bea_pb_plugin' );
/**
 * Init the plugin
 */
function init_bea_pb_plugin(): void {
	\BEA\PB\Main::get_instance();
	\BEA\PB\Blocks::get_instance();
}
