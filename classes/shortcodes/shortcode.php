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
	 * The shortcode TAG
	 */
	const TAG = '';

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
		add_shortcode( self::TAG, array( get_class( __CLASS__ ), 'render' ) );
	}

	/**
	 * Combine the attributes gives us whit defaults attributes
	 *
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function attributes( $attributes = array() ) {
		return shortcode_atts( $this->defaults, $attributes, self::TAG );
	}

    /**
     * Display shortcode content
     *
     * @param array $attributes
     * @param string $content
     *
     * @return string
     */
	public abstract function render( $attributes = array(), $content = '' );

}
