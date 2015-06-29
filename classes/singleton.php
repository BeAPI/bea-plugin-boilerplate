<?php
namespace BEA\PB;

class Singleton {

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @return self
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * prevent the instance from being cloned
	 *
	 * @return void
	 */
	private function __clone() {
	}

	/**
	 * prevent from being unserialized
	 *
	 * @return void
	 */
	private function __wakeup() {
	}
}