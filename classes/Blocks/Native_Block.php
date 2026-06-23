<?php

namespace BEA\PB\Blocks;

/**
 * Register a native block from block.json metadata.
 */
abstract class Native_Block implements Dynamic_Block_Interface {

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
		$block_path = sprintf(
			'%sassets/blocks/%s/',
			BEA_PB_DIR,
			sanitize_file_name( $this->get_slug() )
		);

		if ( ! file_exists( $block_path . 'block.json' ) ) {
			return;
		}

		register_block_type(
			$block_path,
			[
				'render_callback' => [ $this, 'render' ],
			]
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_block_args(): array {
		return [];
	}
}
