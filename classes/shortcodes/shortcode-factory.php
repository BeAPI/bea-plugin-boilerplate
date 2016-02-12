<?php namespace BEA\PB\Shortcodes;

/**
 * The purpose of this factory is to create shortcodes easily
 *
 * Class Shortcode_Factory
 * @package BEA\PB\Shortcodes
 * @since 2.1.0
 */
class Shortcode_Factory {

	/**
	 * Instantiate a shortcode with the given class
	 *
	 * @param $classname
	 *
	 * @since 2.1.0
	 * @return bool
	 */
	public static function create( $classname ) {
		if( empty( $classname ) || ! class_exists( __NAMESPACE__ . $classname ) ) {
			return false;
		}

		// Call the get_instance method for the given $classname
		$class = call_user_func( array ( __NAMESPACE__ . $classname, 'get_instance' ) );
		if( ! is_subclass_of( $class, __NAMESPACE__ . 'Shortcode' ) || ! is_callable( array( $class, 'add' ) ) ) {
			return false;
		}

		// Add shortcode
		call_user_func( array( $class, 'add' ) );
		return true;
	}

}