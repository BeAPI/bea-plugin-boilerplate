<?php

namespace BEA\PB\Blocks;

interface Block_Interface {

	/**
	 * Init block.
	 */
	public function init(): void;

	/**
	 * Register block for Gutenberg.
	 */
	public function register(): void;

	/**
	 * Get block's slug.
	 *
	 * @return string
	 */
	public function get_slug(): string;

	/**
	 * Get block's registration args.
	 *
	 * @return array
	 */
	public function get_block_args(): array;
}
