<?php
namespace BEA\PB\Shortcodes;
use BEA\PB\Singleton;


/**
 *
 * Class Singleton
 * @package BEA\PB
 */
abstract class Shortcode {

	use Singleton;

	const shortcode_tag = '';

	private static $defaults = array();

	/**
	 * Init the shortcode tag
	 *
	 * @author Nicolas Juen
	 */
	public function add_shortcode() {
		add_shortcode( $this->shortcode_tag, array( get_class(__CLASS__), 'do_shortcode' ) );
	}

	public function shortcode_atts( $attributes ) {
		return shortcode_atts( $this->$defaults, $attributes, self::shortcode_tag );
	}

	public abstract function do_shortcode();

}
