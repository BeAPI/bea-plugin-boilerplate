<?php

namespace BEA\PB\Blocks;

abstract class Dynamic_Block implements Dynamic_Block_Interface {

	/**
	 * @inheritDoc
	 */
	public function init(): void {

		if ( empty( $this->get_slug() ) ) {
			throw new \BadMethodCallException( sprintf( 'Missing slug for block %s', static::class ) );
		}

		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * @inheritDoc
	 */
	public function register(): void {

		$args                    = $this->get_block_args();
		$args['render_callback'] = [ $this, 'render' ];

		register_block_type( $this->get_slug(), $args );
	}
}
