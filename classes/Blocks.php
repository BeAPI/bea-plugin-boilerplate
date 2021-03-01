<?php

namespace BEA\PB;

use BEA\PB\Blocks\Block_Interface;

/**
 * This class is for :
 * - Registering blocks
 * - Registering block category
 *
 * Class Blocks
 * @package BEA\PB
 */
class Blocks {

	use Singleton;

	public function init() {
		add_action( 'init', [ $this, 'register_blocks' ], 1 );
		add_action( 'beapi_helpers_locate_template_templates', [ $this, 'block_templates' ], 10, 2 );
	}

	public function register_blocks(): void {

		/**
		 * Here enter all the blocks class names you need to instantiate
		 * This have to be instances of \BEA\PB\Block_Interface
		 */
		$blocks = [];

		$blocks = apply_filters( 'bea_pb_blocks', $blocks );

		array_map(
			static function ( string $block ) {
				/* @var $klass Block_Interface */
				$klass = new $block();
				$klass->init();
			},
			$blocks
		);
	}

	/**
	 * Add the gutenberg template for beapi-frontend-framework
	 *
	 * @param array $templates
	 * @param string $template
	 *
	 * @return array
	 * @author Nicolas JUEN
	 */
	public function block_templates( array $templates, string $template ) {
		$templates[] = 'components/gutenberg/bea-plugin-boilerplate/' . $template . '.php';

		return $templates;
	}
}
