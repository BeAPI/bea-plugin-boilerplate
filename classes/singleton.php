<?php
namespace BEA\PB;

/**
 * Singleton base class for having singleton implementation
 * This allows you to have only one instance of the needed object
 * You can get the instance with
 *     $class = My_Class::get_instance();
 *
 * /!\ The get_instance method have to be implemented !
 *
 * Class Singleton
 * @package BEA\PB
 */
abstract class Singleton {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * Constructor protected from the outside
	 */
	protected function __construct() {}

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