<?php

namespace BEA\PB\Routes;

/**
 * Helper for custom rewrite slugs and URLs.
 */
class Router {

	/**
	 * @var array<string, string>
	 */
	private static $rewrite_elements = [];

	/**
	 * Boot rewrite elements from a filter.
	 */
	public static function boot(): void {
		/**
		 * Filter rewrite elements.
		 *
		 * @param array<string, string> $rewrite_elements Internal query var => public slug.
		 */
		self::$rewrite_elements = apply_filters(
			'bea_pb_rewrite_elements',
			[
				'example-page' => 'example-page',
			]
		);
	}

	/**
	 * Register rewrite elements manually.
	 *
	 * @param array<string, string> $elements Internal query var => public slug.
	 */
	public static function register_rewrite_elements( array $elements ): void {
		self::$rewrite_elements = array_merge( self::$rewrite_elements, $elements );
	}

	/**
	 * Return the rewrite elements registered.
	 *
	 * @return array<string, string>
	 */
	public static function get_rewrite_elements() {
		return self::$rewrite_elements;
	}

	/**
	 * Get the permalink rewrite element for the given post_type.
	 */
	public static function get_post_type_permalink_rewrite( string $post_type ) {
		global $wp_rewrite;

		if ( 'page' === $post_type ) {
			$post_type_permastruct = $wp_rewrite->get_page_permastruct();
		} else {
			$post_type_permastruct = $wp_rewrite->get_extra_permastruct( $post_type );
		}

		$results = preg_match_all( '/%.+?%/', $post_type_permastruct, $tokens );

		if ( false === $results || empty( $tokens ) ) {
			return '';
		}

		return str_replace( $wp_rewrite->rewritecode, $wp_rewrite->rewritereplace, $post_type_permastruct );
	}

	/**
	 * Get a URL based on query var and optional params.
	 *
	 * @param array<string, scalar|null> $params Query args.
	 *
	 * @return false|string
	 */
	public static function get_url( string $query_var, array $params = [] ) {
		$slug = self::rewrite_slug( $query_var );

		if ( empty( $slug ) ) {
			return false;
		}

		if ( empty( $params ) ) {
			return trailingslashit( home_url( $slug ) );
		}

		return add_query_arg( $params, trailingslashit( home_url( $slug ) ) );
	}

	/**
	 * Build a URL from multiple rewrite slugs.
	 *
	 * @param array<int, string>         $slugs  Query vars or raw slugs.
	 * @param array<string, scalar|null> $params Query args.
	 *
	 * @return false|string
	 */
	public static function get_url_complex( array $slugs, $params = [] ) {
		if ( empty( $slugs ) ) {
			return '';
		}

		if ( 1 === count( $slugs ) ) {
			return self::get_url( $slugs[0], $params );
		}

		$out_slugs = [];
		foreach ( $slugs as $slug ) {
			$mapped_slug = self::rewrite_slug( $slug );
			$out_slugs[] = ! empty( $mapped_slug ) ? $mapped_slug : $slug;
		}

		if ( empty( $params ) ) {
			return trailingslashit( home_url( implode( '/', $out_slugs ) ) );
		}

		return add_query_arg( $params, trailingslashit( home_url( implode( '/', $out_slugs ) ) ) );
	}

	/**
	 * Resolve a rewrite slug from a query var.
	 */
	public static function rewrite_slug( string $query_var = '' ) {
		if ( empty( $query_var ) || ! isset( self::$rewrite_elements[ $query_var ] ) ) {
			return '';
		}

		return self::$rewrite_elements[ $query_var ];
	}
}
