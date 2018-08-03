<?php

namespace BEA\PB\Shortcodes;

/**
 * The purpose of this factory is to create shortcodes easily
 *
 * The usage of the class is something like this :
 *
 * Shortcode_Factory::register( 'My_Shortcode_Class' );
 *
 * My_Shortcode_Class have to be a child of Shortcode class
 *
 * Class Shortcode_Factory
 *
 * @package BEA\PB\Shortcodes
 * @since   2.1.0
 */
class Shortcode_Factory {

	/**
	 * Instantiate a shortcode with the given class
	 * Do not specify the namespace
	 *
	 * @param $class_name Shortcode the Shortcode ClassName to register
	 *
	 * @since 2.1.0
	 * @return \BEA\PB\Shortcodes\Shortcode|bool Instance of the Shortcode added or false on failure
	 */
	public static function register( $class_name ) {
		$class_name = __NAMESPACE__ . '\\' . $class_name;
		if ( empty( $class_name ) || ! class_exists( $class_name ) || ! is_subclass_of( $class_name, __NAMESPACE__ . '\\Shortcode' ) ) {
			return new \WP_Error( 'fail_shortcode_registration', sprintf( 'Fail to instantiate shortcode %s', $class_name ) );
		}

		/**
		 * Since the shortcodes are Singleton we only have to get the instance
		 * and call the add method
		 *
		 * @var Shortcode $class
		 */
		try {
			$class = $class_name::get_instance()->add();
		} catch ( \Exception $e ) {
			return new \WP_Error( 'fail_shortcode_instanciation', sprintf( 'Fail to instantiate shortcode with error %s', $e->getMessage() ) );
		}

		return $class;
	}
}
