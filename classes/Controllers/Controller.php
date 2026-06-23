<?php

namespace BEA\PB\Controllers;

use BEA\PB\Routes\Router;
use BEA\PB\Singleton;

/**
 * This class is the base class for the controllers
 * It allows you to get the current controller based on the query var
 * Basically you have to :
 *  - extend this class
 *  - fill the page_slug property
 *  - Add action on the wp tag and add the elements
 *
 * All the controllers needs to be implemented on all classes
 *
 * Class Controller
 * @package BEA\PB
 */
abstract class Controller {

	use Singleton;

	/**
	 * Registered controller class names.
	 *
	 * @var array<int, class-string<self>>
	 */
	private static $controllers = [];

	/**
	 * The page slug on the rewrite rule
	 *
	 * @var string
	 */
	protected $page_slug;

	/**
	 * The query var page slug to check
	 * This is the same slug as the slug used on the rewrite
	 * Like in the hm_rewrite rule:
	 *
	 *    'query' => 'index.php?registration=true&step=1&bea_pb_page=registration',
	 * Here the "bea_pb_page" is the slug to check on
	 *
	 * @var string
	 */
	protected $page_query_var = 'bea_pb_page';

	/**
	 * Register the controller class for current-page resolution.
	 *
	 * @param class-string<self> $class_name Controller class name.
	 */
	public static function register_controller( string $class_name ): void {
		if ( ! is_subclass_of( $class_name, self::class, true ) ) {
			return;
		}

		if ( in_array( $class_name, self::$controllers, true ) ) {
			return;
		}

		self::$controllers[] = $class_name;
	}

	/**
	 * Return registered controller class names.
	 *
	 * @return array<int, class-string<self>>
	 */
	public static function get_registered_controllers(): array {
		/**
		 * Filter registered controller classes.
		 *
		 * @param array<int, class-string<self>> $controllers Controller class names.
		 */
		return apply_filters( 'bea_pb_controllers', self::$controllers );
	}

	/**
	 * Register the concrete controller when the singleton boots.
	 */
	protected function init(): void {
		self::register_controller( static::class );
	}

	/**
	 * Check if the current page rewrited is the right page to execute or not methods
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	protected function is_page() {
		return get_query_var( $this->page_query_var, null ) === $this->page_slug;
	}

	/**
	 * Return the form url base
	 * Dashboard/$this->page_slug/
	 *
	 * @param array $args arg to add
	 *
	 * @return false|string
	 * @author Nicolas Juen
	 */
	public function get_form_url( $args = [] ) {
		return Router::get_url_complex( [ $this->page_slug ], $args );
	}

	/**
	 * Redirect to the form url with the data
	 *
	 * @param array $args
	 *
	 * @author Nicolas Juen
	 */
	protected function redirect( $args = [] ): void {
		wp_safe_redirect( $this->get_form_url( $args ) );
		exit;
	}

	/**
	 * Get among all the controller the right one for the current page
	 *
	 * @author Nicolas Juen
	 * @return \WP_Error|self
	 */
	public static function get_current_controller() {
		foreach ( self::get_registered_controllers() as $class_name ) {
			$controller = $class_name::get_instance();

			if ( $controller->is_page() ) {
				return $controller;
			}
		}

		return new \WP_Error( 'no-controller', 'No controller found' );
	}

	/**
	 * Get all the default data for the controller
	 * Like the form data
	 *
	 * @return array
	 * @author Nicolas Juen
	 */
	public function get_default_data() {
		return [];
	}
}
