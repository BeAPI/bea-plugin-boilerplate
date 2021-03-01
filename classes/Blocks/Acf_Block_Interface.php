<?php

namespace BEA\PB\Blocks;

interface Acf_Block_Interface extends Block_Interface {

	/**
	 * Block's render callback.
	 *
	 * @param array $block
	 * @param string $content
	 * @param false $is_preview
	 * @param int $post_id
	 */
	public function render( array $block, $content = '', $is_preview = false, $post_id = 0 ): void;

	/**
	 * Get block's render args.
	 *
	 * @param array $block
	 * @param string $content
	 * @param false $is_preview
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_block_data( array $block, $content = '', $is_preview = false, $post_id = 0 ): array;

	/**
	 * Validate block's data before render.
	 *
	 * @param array $block
	 * @param string $content
	 * @param false $is_preview
	 * @param int $post_id
	 *
	 * @return \WP_Error
	 */
	public function validate( array $block, $content = '', $is_preview = false, $post_id = 0 ): \WP_Error;
}
