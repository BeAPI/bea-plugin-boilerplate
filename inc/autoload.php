<?php
/**
 * Load plugin classes via Bedrock/root Composer or a local vendor tree.
 *
 * @return bool Whether the plugin main class is available.
 */
function bea_pb_load_composer_autoload(): bool {
	if ( class_exists( 'BEA\\PB\\Main' ) ) {
		return true;
	}

	$local_autoload = BEA_PB_DIR . 'vendor/autoload.php';

	if ( is_readable( $local_autoload ) ) {
		require_once $local_autoload;
	}

	return class_exists( 'BEA\\PB\\Main' );
}
