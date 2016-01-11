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
	protected $tag = '';

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
		add_shortcode( $this->tag, array( get_class( $this ), 'render' ) );
	}

	/**
	 * Combine the attributes gives us whit defaults attributes
	 *
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function attributes( $attributes = array() ) {
		return shortcode_atts( $this->defaults, $attributes, $this->tag );
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
