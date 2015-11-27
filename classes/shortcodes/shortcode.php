<?php
namespace BEA\PB\Shortcodes;

use BEA\PB\Singleton;


/**
 * This class is the base class of Shortcode
 *
 * Class Shortcode
 * @package BEA\PB\Shortcodes
 */
abstract class Shortcode {

	use Singleton;

	/**
	 * The shortcode Tag
	 */
	const tag = '';

	/**
	 * List of supported attributes and their defaults
	 *
	 * @var array
	 */
	private $defaults = array();


	/**
	 * Create a shortCode
	 */
	public function add() {
		add_shortcode( self::tag, array( get_class( __CLASS__ ), 'render' ) );
	}

	/**
	 * Combine the attributes gives us whit defaults attributes
	 *
	 * @param $attributes .
	 *
	 * @return mixed
	 */
	public function attributes( $attributes ) {
		return shortcode_atts( $this->defaults, $attributes, self::tag );
	}

	/**
	 * Display shortcode content
	 *
	 * @return mixed
	 */
	public abstract function render();

}
