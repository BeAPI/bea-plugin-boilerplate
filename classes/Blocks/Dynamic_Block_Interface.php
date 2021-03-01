<?php

namespace BEA\PB\Blocks;

interface Dynamic_Block_Interface extends Block_Interface {

	/**
	 * Block's render callback.
	 *
	 * @param array $attributes
	 * @param string $content
	 *
	 * @return string
	 */
	public function render( array $attributes, string $content ): string;
}
