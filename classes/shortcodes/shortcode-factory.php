<?php namespace BEA\PB\Shortcodes;

class Shortcode_Factory {

	public static function create( $classname ) {
		$namespace = 'BEA\PB\Shortcodes\\';
		if( ! class_exists( $namespace . $classname ) ) {
			return false;
		}

		$class = call_user_func( array ( $namespace . $classname, 'get_instance' ) );
		if( ! is_subclass_of( $class, $namespace . 'Shortcode' ) || ! is_callable( array( $class, 'add' ) ) ) {
			return false;
		}

		call_user_func( array( $class, 'add' ) );
		return true;
	}

}