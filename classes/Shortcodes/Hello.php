<?php

namespace BEA\PB\Shortcodes;

use BEA\PB\Helpers;

/**
 * Example shortcode: [bea_hello name="World"].
 */
class Hello extends Shortcode {

	/**
	 * @var string
	 */
	protected $tag = 'bea_hello';

	/**
	 * @var array
	 */
	protected $defaults = [
		'name' => 'World',
	];

	/**
	 * @inheritDoc
	 */
	public function render( $attributes = [], $content = '' ) {
		$attributes = $this->attributes( $attributes );

		ob_start();
		Helpers::render( 'shortcode-hello', $attributes );

		return (string) ob_get_clean();
	}
}
