<?php

namespace BEA\PB\Blocks;

use BEA\PB\Helpers;

abstract class Acf_Block implements Acf_Block_Interface {

	/**
	 * @inheritDoc
	 */
	public function init(): void {

		if ( empty( $this->get_slug() ) ) {
			throw new \BadMethodCallException( sprintf( 'Missing slug for block %s', static::class ) );
		}

		$this->maybe_load_fields();

		add_action( 'acf/init', [ $this, 'register' ] );
	}

	/**
	 * Load ACF fields for the block.
	 */
	public function maybe_load_fields(): void {

		$field_name = "{$this->get_slug()}.php";
		$field_name = sanitize_file_name( $field_name );

		$field_path = BEA_PB_DIR . 'assets/acf/php/' . $field_name;

		if ( ! file_exists( $field_path ) ) {
			return;
		}

		require_once $field_path;
	}

	/**
	 * @inheritDoc
	 */
	public function register(): void {

		$args = wp_parse_args(
			$this->get_block_args(),
			[
				'title'    => $this->get_slug(),
				'category' => 'common',
				'mode'     => 'auto',
				'example'  => [
					'attributes' => [
						'mode' => 'preview',
						'data' => [
							'is_preview' => true,
						],
					],
				],
			]
		);

		$args['name']            = $this->get_slug();
		$args['render_callback'] = [ $this, 'render' ];

		\acf_register_block_type( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function render( array $block, $content = '', $is_preview = false, $post_id = 0 ): void {
		$errors = $this->validate( $block, $content, $is_preview, $post_id );
		if ( $errors->has_errors() ) {
			if ( ! is_admin() ) {
				return;
			}

			$tpl = Helpers::load_template( 'template-admin-block-invalid' );
			if ( ! empty( $tpl ) ) {
				$tpl( [ 'error_messages' => $errors ] );
			}

			return;
		}

		$tpl_slug = "block-{$this->get_slug()}";
		$tpl      = Helpers::load_template( $tpl_slug );
		if ( empty( $tpl ) ) {
			return;
		}

		$tpl( $this->get_block_data( $block, $content, $is_preview, $post_id ) );
	}

	/**
	 * @inheritDoc
	 */
	public function get_block_data( array $block, $content = '', $is_preview = false, $post_id = 0 ): array {

		// Create id attribute allowing for custom "anchor" value.
		$block_id = $this->get_slug() . '-' . $block['id'];
		if ( ! empty( $block['anchor'] ) ) {
			$block_id = $block['anchor'];
		}

		// Create class attribute allowing for custom "className" and "align" values.
		$class_name = $this->get_slug();
		if ( ! empty( $block['className'] ) ) {
			$class_name .= ' ' . $block['className'];
		}
		if ( ! empty( $block['align'] ) ) {
			$class_name .= ' align' . $block['align'];
		}

		return [
			'block_id'        => $block_id,
			'block_classname' => sanitize_html_class( $class_name ),
		];
	}
}
