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
				$tpl(
					[
						'block'          => $block,
						'error_messages' => $errors,
					]
				);
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

		// create align class
		if ( ! empty( $block['align'] ) ) {
			$class_name .= ' align' . $block['align'];
		}

		// create text color class
		if ( ! empty( $block['textColor'] ) ) {
			$class_name .= ' has-text-color has-' . $block['textColor'] . '-color';
		}

		// create background color class
		if ( ! empty( $block['backgroundColor'] ) ) {
			$class_name .= ' has-background has-' . $block['backgroundColor'] . '-background-color';
		}

		// create style attribute value
		$style = '';

		if ( ! empty( $block['style'] ) ) {
			$block_style = $block['style'];

			if ( ! empty( $block_style['spacing'] ) ) {
				$style .= $this->get_spacing_style( $block_style['spacing'] ) . ';';
			}

			if ( ! empty( $block_style['border'] ) ) {
				$style .= $this->get_border_style( $block_style['border'] );
			}
		}

		return [
			'id'               => $block['id'],
			'block_id'         => $block_id,
			'block_is_preview' => $is_preview,
			'block_classname'  => implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $class_name ) ) ),
			'block_content'    => $content,
			'block_post_id'    => $post_id,
			'block_style'      => $style,
		];
	}

	/**
	 * Get the CSS value for the block.
	 *
	 * @param string $value The value to get the CSS value for.
	 * @return string The CSS value.
	 */
	private function get_block_css_value( string $value ): string {
		if ( str_starts_with( $value, 'var:' ) ) {
			$value = str_replace( 'var:', 'var(--wp--', $value );
			$value = str_replace( '|', '--', $value );
			return $value . ')';
		}

		return $value;
	}

	/**
	 * Format the CSS property.
	 *
	 * @param string $prefix The prefix.
	 * @param string $property The property.
	 * @param string $suffix The suffix.
	 * @return string The formatted CSS property.
	 */
	private function format_css_property( string $prefix, string $property, string $suffix ): string {
		$property = preg_replace( '/([A-Z])/', '-$1', $property );
		$property = strtolower( $property );

		if ( ! empty( $prefix ) ) {
			$property = $prefix . '-' . $property;
		}

		if ( ! empty( $suffix ) ) {
			$property = $property . '-' . $suffix;
		}

		return $property;
	}

	/**
	 * Get the spacing style.
	 *
	 * @param array $spacing The spacing.
	 * @return string The spacing style.
	 */
	private function get_spacing_style( array $spacing ): string {
		$style_value = '';

		if ( empty( $spacing ) ) {
			return $style_value;
		}

		foreach ( $spacing as $type => $directions ) {
			foreach ( $directions as $direction => $value ) {
				$style_value .= $type . '-' . $direction . ': ' . $this->get_block_css_value( $value ) . ';';
			}
		}

		return $style_value;
	}

	/**
	 * Get the border style.
	 *
	 * @param array $border The border.
	 * @return string The border style.
	 */
	private function get_border_style( array $border ): string {
		$style_value = '';

		if ( empty( $border ) ) {
			return $style_value;
		}

		// handle border radius
		if ( ! empty( $border['radius'] ) ) {
			if ( is_array( $border['radius'] ) ) {
				foreach ( $border['radius'] as $direction => $radius ) {
					$style_value .= $this->format_css_property( 'border', $direction, 'radius' ) . ': ' . $this->get_block_css_value( $radius ) . ';';
				}
			} else {
				$style_value .= 'border-radius: ' . $this->get_block_css_value( $border['radius'] ) . ';';
			}
		}

		// handle global border value
		if ( ! empty( $border['width'] ) ) {
			$style_value .= 'border-width: ' . $this->get_block_css_value( $border['width'] ) . ';';
		}

		if ( ! empty( $border['color'] ) ) {
			$style_value .= 'border-color: ' . $this->get_block_css_value( $border['color'] ) . ';';
		}

		if ( empty( $border['style'] ) && ! empty( $border['width'] ) && ! empty( $border['color'] ) ) {
			$border['style'] = 'solid';
		}

		if ( ! empty( $border['style'] ) ) {
			$style_value .= 'border-style: ' . $this->get_block_css_value( $border['style'] ) . ';';
		}

		// handle border by direction
		foreach ( $border as $direction => $params ) {
			if ( empty( $params ) || ! in_array( $direction, [ 'top', 'right', 'bottom', 'left' ] ) ) {
				continue;
			}

			$border_props_values = [];

			if ( ! empty( $params['color'] ) ) {
				$border_props_values[] = $this->get_block_css_value( $params['color'] );
			}

			if ( ! empty( $params['width'] ) ) {
				$border_props_values[] = $params['width'];
			}

			if ( empty( $params['style'] ) && ! empty( $params['width'] ) && ! empty( $params['color'] ) ) {
				$params['style'] = 'solid';
			}

			if ( ! empty( $params['style'] ) ) {
				$border_props_values[] = $params['style'];
			}

			$style_value .= 'border-' . $direction . ': ' . implode( ' ', $border_props_values ) . ';';
		}

		return $style_value;
	}
}
