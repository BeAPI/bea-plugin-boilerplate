<?php
namespace BEA\PB;

/**
 * The purpose of the plugin class is to have the methods for
 *  - activation actions
 *  - deactivation actions
 *  - uninstall actions
 *
 * Class Plugin
 * @package BEA\PB
 */
class Plugin {
	/**
	 * Use the trait
	 */
	use Singleton;

	public static function activate() {
		global $wpdb;

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		// Add one library admin function for next function
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Data table
		maybe_create_table( $wpdb->sample_table, "CREATE TABLE IF NOT EXISTS `{$wpdb->sample_table}` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`post_id` bigint(20) NOT NULL,
			PRIMARY KEY (`id`)
		) $charset_collate AUTO_INCREMENT=1;" );
	}

	public static function deactivate() {

	}
}
