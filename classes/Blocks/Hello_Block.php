<?php

namespace BEA\PB\Blocks;

/**
 * Example ACF block registered from block.json.
 */
class Hello_Block extends Acf_Json_Block {

	/**
	 * @inheritDoc
	 */
	public function get_slug(): string {
		return 'hello';
	}

	/**
	 * @inheritDoc
	 */
	public function validate( array $block, $content = '', $is_preview = false, $post_id = 0 ): \WP_Error {
		return new \WP_Error();
	}

	/**
	 * @inheritDoc
	 */
	public function get_block_data( array $block, $content = '', $is_preview = false, $post_id = 0 ): array {
		$data = parent::get_block_data( $block, $content, $is_preview, $post_id );

		$data['message'] = function_exists( 'get_field' )
			? get_field( 'message' )
			: '';

		if ( empty( $data['message'] ) ) {
			$data['message'] = __( 'Hello from BEA Plugin Boilerplate!', 'bea-plugin-boilerplate' );
		}

		return $data;
	}
}
