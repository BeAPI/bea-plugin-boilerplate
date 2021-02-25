<?php

namespace BEA\PB\Shortcodes;

/**
 * This class is the base class of Shortcode
 * It have to be used as base for all Shortcodes
 *
 * Class Shortcode
 *
 * @package BEA\PB\Shortcodes
 * @since   2.1.0
 */
abstract class Shortcode {

	/**
	 * The shortcode [tag]
	 * @since   2.1.0
	 */
	protected $tag = '';

	/**
	 * List of supported attributes and their defaults
	 *
	 * @var array
	 * @since   2.1.0
	 */
	protected $defaults = array();

	/**
	 * Create a shortcode
	 *
	 * @since   2.1.0
	 */
	public function add() {
		add_shortcode( $this->tag, array( $this, 'render' ) );
	}

	/**
	 * Combine the attributes gives us whit defaults attributes
	 *
	 * @since   2.1.0
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
	 * @since   2.1.0
	 *
	 * @param array $attributes
	 * @param string $content
	 *
	 * @return string
	 */
	abstract public function render( $attributes = array(), $content = '' );

}
