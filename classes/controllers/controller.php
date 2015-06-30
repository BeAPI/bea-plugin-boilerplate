<?php
namespace BEA\PB;

use BEA\PB\Routes\Router;

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
abstract class Controller extends Singleton {
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
	public function get_form_url( $args = array() ) {
		return Router::get_url_complex( array( $this->page_slug ), $args );
	}

	/**
	 * Redirect to the form url with the data
	 *
	 * @param array $args
	 *
	 * @author Nicolas Juen
	 */
	protected function redirect( $args = array() ) {
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
		$classes = array_filter( get_declared_classes(), array( __CLASS__, 'filter_classes' ) );

		// Check there is classes
		if ( empty( $classes ) ) {
			return new \WP_Error( 'no-controller', 'No controller found' );
		}

		// Get the filtered controller
		$class = reset( $classes );

		// Give the controller full
		return $class::get_instance();
	}

	/**
	 * Get among all classes the right one
	 *
	 * @param $class
	 *
	 * @return bool
	 * @author Nicolas Juen
	 */
	public static function filter_classes( $class ) {
		if ( false === is_subclass_of( $class, '\BEA\PB\Controller', true ) ) {
			return false;
		}

		return $class::get_instance()->is_page();
	}

	/**
	 * Get all the default data for the controller
	 * Like the form data
	 *
	 * @return array
	 * @author Nicolas Juen
	 */
	public function get_default_data() {
		return array();
	}
}