<?php

namespace BEA\PB;

class Compatibility {
	/**
	 * admin_init hook callback
	 *
	 * @since 0.1
	 */
	public static function admin_init() : void {
		// Not on ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Check activation
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		// Load the textdomain
		load_plugin_textdomain( 'bea-plugin-boilerplate', false, BEA_PB_PLUGIN_DIRNAME . '/languages' );

		//phpcs:ignore
		trigger_error( sprintf( __( 'Plugin Boilerplate requires PHP version %s or greater to be activated.', 'bea-plugin-boilerplate' ), BEA_PB_MIN_PHP_VERSION ) );

		// Deactive self
		deactivate_plugins( BEA_PB_DIR . 'bea-plugin-boilerplate.php' );

		//phpcs:ignore
		unset( $_GET['activate'] );

		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
	}

	/**
	 * Notify the user about the incompatibility issue.
	 */
	public static function admin_notices(): void {
		echo '<div class="notice error is-dismissible">';
		/* translators: %1$s: PHP min version %2$s: Current PHP version */
		echo '<p>' . esc_html( sprintf( __( 'Plugin Boilerplate require PHP version %1$s or greater to be activated. Your server is currently running PHP version %2$s.', 'bea-plugin-boilerplate' ), BEA_PB_MIN_PHP_VERSION, PHP_VERSION ) ) . '</p>';
		echo '</div>';
	}
}
