<?php

namespace BEA\PB\Traits;

/**
 * Optional Posts 2 Posts helpers for post models.
 */
trait P2p_Aware {

	/**
	 * Connect the current object to another object.
	 *
	 * @param int    $object_id       Connected object ID.
	 * @param string $connection_type Posts 2 Posts connection type.
	 * @param array  $metas           Optional connection meta.
	 *
	 * @return bool|int|\WP_Error
	 */
	protected function connect( int $object_id, string $connection_type, $metas = [] ) {
		if ( ! function_exists( 'p2p_type' ) ) {
			return false;
		}

		return p2p_type( $connection_type )->connect( $this->get_id(), $object_id, $metas );
	}

	/**
	 * Disconnect the current object from another object.
	 *
	 * @param int    $object_id       Connected object ID.
	 * @param string $connection_type Posts 2 Posts connection type.
	 *
	 * @return bool|int|\WP_Error
	 */
	protected function disconnect( int $object_id, string $connection_type ) {
		if ( ! function_exists( 'p2p_type' ) ) {
			return false;
		}

		return p2p_type( $connection_type )->disconnect( $this->get_id(), $object_id );
	}
}
