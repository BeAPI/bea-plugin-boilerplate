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
trait Singleton {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * @return static
	 */
	final public static function get_instance() {
		return static::$instance ?? static::$instance = new static();
	}

	/**
	 * Constructor protected from the outside
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Add init function by default
	 * Implement this method in your child class
	 * If you want to have actions send at construct
	 */
	protected function init() {
	}

	/**
	 * prevent the instance from being cloned
	 *
	 * @throws \LogicException
	 */
	final public function __clone() {
		throw new \LogicException( 'A singleton must not be cloned!' );
	}

	/**
	 * prevent from being serialized
	 *
	 * @throws \LogicException
	 */
	final public function __sleep() {
		throw new \LogicException( 'A singleton must not be serialized!' );
	}

	/**
	 * prevent from being unserialized
	 *
	 * @throws \LogicException
	 */
	final public function __wakeup() {
		throw new \LogicException( 'A singleton must not be unserialized!' );
	}

	/**
	 * Destruct your instance
	 *
	 * @return void
	 */
	final public static function destroy(): void {
		static::$instance = null;
	}
}
