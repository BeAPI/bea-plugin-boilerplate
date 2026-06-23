<?php

namespace BEA\PB\Blocks;

use BEA\PB\Helpers;

/**
 * Example native block registered from block.json without ACF.
 */
class Quote_Block extends Native_Block {

	/**
	 * @inheritDoc
	 */
	public function get_slug(): string {
		return 'quote';
	}

	/**
	 * @inheritDoc
	 */
	public function render( array $attributes, string $content ): string {
		ob_start();
		Helpers::render(
			'block-quote',
			[
				'content' => $attributes['content'] ?? '',
				'author'  => $attributes['author'] ?? '',
			]
		);

		return (string) ob_get_clean();
	}
}
