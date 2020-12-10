<?php

namespace BEA\PB\Blocks;

use BadFunctionCallException;
use BEA\PB\Helpers;
use const BEA_PB_DIR;

abstract class Block implements Block_Interface {

	/**
	 * @var string : The block slug for ACF registration acf/{slug}
	 */
	protected $slug;

	/**
	 * @inheritDoc
	 */
	public function init(): void {

		if ( empty( $this->slug ) ) {
			throw new BadFunctionCallException( 'The block ' . self::class . ' is missing a slug.' );
		}

		$this->maybe_load_fields();

		add_action( 'acf/init', [ $this, 'register' ] );
	}

	/**
	 * @inheritDoc
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Try to load ACF fields for the block.
	 */
	public function maybe_load_fields(): void {
		$field_name = "{$this->slug}.php";
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
				'title'    => $this->slug,
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

		$args['name']            = $this->slug;
		$args['render_callback'] = [ $this, 'render' ];

		acf_register_block_type( $args );
	}

	/**
	 * @inheritDoc
	 */
	public function render( array $block, $content = '', $is_preview = false, $post_id = 0 ): void {
		$render = new Block_Render( $this, $block, $content, $is_preview, $post_id );
		$this->validate( $render );
		if ( ! $render->is_block_valid() ) {
			if ( ! is_admin() ) {
				return;
			}

			$tpl = Helpers::load_template( 'template-admin-block-invalid' );
			if ( ! empty( $tpl ) ) {
				$tpl( [ 'error_messages' => $render->get_error() ] );
			}

			return;
		}

		$tpl_slug = "template-{$this->slug}";

		$tpl = Helpers::load_template( $tpl_slug );
		if ( empty( $tpl ) ) {
			return;
		}

		$tpl( $this->get_block_data( $render ) );
	}
}