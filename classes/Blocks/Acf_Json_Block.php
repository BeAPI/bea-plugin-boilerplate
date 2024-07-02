<?php

namespace BEA\PB\Blocks;

abstract class Acf_Json_Block extends Acf_Block {

	/**
	 * @inheritDoc
	 */
	public function register(): void {

		$block_path = sprintf(
			'%sassets/blocks/%s/',
			BEA_PB_DIR,
			sanitize_file_name( $this->get_slug() )
		);

		if ( ! file_exists( $block_path ) ) {
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
	 * Block registration args loaded from `block.json` file.
	 *
	 * @return array
	 */
	public function get_block_args(): array {
		return [];
	}
}
