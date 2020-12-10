<?php

namespace BEA\PB\Blocks;

class Block_Render {
	protected $block;
	protected $acf_block;
	protected $content;
	protected $is_preview;
	protected $post_id;
	protected $errors;

	public function __construct( Block_Interface $block, array $acf_block, string $content, bool $is_preview, int $post_id ) {
		$this->errors     = new Block_Errors();
		$this->block      = $block;
		$this->acf_block  = $acf_block;
		$this->content    = $content;
		$this->is_preview = $is_preview;
		$this->post_id    = $post_id;
	}

	/**
	 * Render the block in preview mode.
	 *
	 * If the context is for the preview, render the template with his screenshot
	 *
	 * @return bool
	 */
	public function is_preview(): bool {
		return $this->is_preview;
	}

	/**
	 * Block validation callback.
	 *
	 * @return bool
	 */
	public function is_block_valid(): bool {
		return ( ! count( $this->get_error() ) ) > 0;
	}

	/**
	 * TODO: function description
	 **
	 * @return array
	 * @author Nicolas JUEN
	 */
	public function get_block_data(): array {
		// Create id attribute allowing for custom "anchor" value.
		$block_id = $this->block->get_slug() . '-' . $this->acf_block['id'];
		if ( ! empty( $this->acf_block['anchor'] ) ) {
			$block_id = $this->acf_block['anchor'];
		}

		// Create class attribute allowing for custom "className" and "align" values.
		$class_name = $this->block->get_slug();
		if ( ! empty( $this->acf_block['className'] ) ) {
			$class_name .= ' ' . $this->acf_block['className'];
		}
		if ( ! empty( $this->acf_block['align'] ) ) {
			$class_name .= ' align' . $this->acf_block['align'];
		}

		return [
			'id'               => $this->acf_block['id'],
			'block_id'         => $block_id,
			'block_is_preview' => $this->is_preview(),
			'block_classname'  => sanitize_html_class( $class_name ),
		];
	}

	/**
	 * Get the current error objet, or new instance.
	 *
	 * @return Block_Errors
	 * @author Nicolas JUEN
	 */
	public function get_error() {
		return $this->errors;
	}

	public function get_content() {
		return $this->content;
	}

	public function get_block() {
		return $this->acf_block;
	}

	public function get_post_id() {
		return $this->post_id;
	}

}