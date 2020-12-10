<?php

namespace BEA\PB\Blocks;

interface Block_Interface {
	/**
	 * Initialize the block.
	 *
	 * Child classes need to define their slug.
	 */
	public function init(): void;

	/**
	 * Prepare custom and default parameter before registering the new Gutenberg Block
	 */
	public function register(): void;

	/**
	 * ACF Callback for the render
	 *
	 * @param array  $block
	 * @param string $content
	 * @param bool  $is_preview
	 * @param int    $post_id
	 *
	 * @author Nicolas JUEN
	 */
	public function render( array $block, $content = '', $is_preview = false, $post_id = 0 ): void;
	/**
	 * Getter for the block Slug
	 *
	 * @return string
	 * @author Nicolas JUEN
	 */
	public function get_slug(): string;

	/**
	 * Get the block args for the acf register.
	 *
	 * @return array
	 * @author Nicolas JUEN
	 */
	public function get_block_args(): array;

	/**
	 * Get Block data based on the context.
	 *
	 * @param Block_Render $render
	 *
	 * @return array
	 * @author Nicolas JUEN
	 */
	public function get_block_data( Block_Render $render ): array;

	/**
	 * Validate the data based on context.
	 *
	 * @param Block_Render $render
	 *
	 * @author Nicolas JUEN
	 */
	public function validate( Block_Render $render ): void;
}
