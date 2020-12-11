<?php

namespace BEA\PB\Blocks;

use BEA\PB\Blocks\Block\Testimonials;
use BEA\PB\Singleton;
use Exception;
use function add_action;
use function error_log;
use function function_exists;

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
		add_action( 'acf/init', [ $this, 'register_blocks' ], 1 );
		add_action( 'BEA/Helpers/locate_template/templates', [ $this, 'block_templates' ], 10, 2 );
	}

	/**
	 * Register all the blocs for the ACF blocks
	 *
	 * @author Nicolas JUEN
	 */
	public function register_blocks(): void {
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}
		/**
		 * Here enter all the blocks class names you need to instanciate
		 * This have to be instances of \BEA\PB\Block_Interface
		 */
		$blocks = [];

		$blocks = apply_filters( 'BEA/PB/Blocks', $blocks );

		array_map(
			static function ( string $block ) {
				try {
					/**
					 * @var $block_class Block_Interface
					 */
					$block_class = new $block();
					$block_class->init();
				} catch ( Exception $e ) {
					error_log( "Block $block does not exists : " . $e->getMessage() );
				}
			},
			$blocks
		);
	}

	/**
	 * Add the gutenberg template for beapi-frontend-framework
	 *
	 * @param array  $templates
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