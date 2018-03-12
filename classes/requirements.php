<?php namespace BEA\PB;

class Requirements {

	use Singleton;

	public $satsify_requiremeents = true;

	public function init() {
		add_action( 'admin_init', [ $this, 'check_requirements' ] );
	}

	/**
	 * All about requirements checks
	 *
	 * @since 2.1.8
	 *
	 * @return bool
	 */
	public function check_requirements() {
		// Not on ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			do_action( 'bea_pb_load' );
			return true;
		}

		// Check activation
		if ( ! current_user_can( 'activate_plugins' ) ) {
			do_action( 'bea_pb_load' );
			return true;
		}

		if ( version_compare( PHP_VERSION, BEA_PB_MIN_PHP_VERSION, '<' ) ) {
			$this->display_error( sprintf( __( 'Plugin Boilerplate require PHP version %s or greater to be activated. Your server is currently running PHP version %s.', 'bea-plugin-boilerplate' ), BEA_PB_MIN_PHP_VERSION, PHP_VERSION ) );
			return false;
		}

		// Maybe more checks

		do_action( 'bea_pb_load' );
		return true;
	}

	/**
	 * Display message and handle errors
	 *
	 * @since 2.1.8
	 */
	public function display_error( $message ) {
		$this->satsify_requiremeents = false;

		trigger_error( $message );

		add_action( 'admin_notices', function () use ($message) {
			printf('<div class="notice error is-dismissible"><p>%s</p></div>', $message );
		} );

		// Deactive self
		deactivate_plugins( BEA_PB_PLUGIN_MAIN_FILE_DIR );
		unset( $_GET['activate'] );
	}
}