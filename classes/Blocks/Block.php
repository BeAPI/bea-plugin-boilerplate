<?php

namespace BEA\PB\Blocks;

abstract class Block implements Block_Interface {

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

		register_block_type( $this->get_slug(), $this->get_block_args() );
	}
}
