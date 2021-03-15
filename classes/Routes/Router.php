<?php

namespace BEA\PB\Routes;

class Router {

	/**
	 * @var array the rewrite elements
	 */
	private static $rewrite_elements = [];

	/**
	 * Launch all the filters and actions needed
	 */
	public function __construct() {
		/**
		 * This array is built like that
		 *  internal_query_element => displayed_query_element
		 *
		 * Like
		 *
		 * 'registration' => 'account-creation',
		 *
		 */
		self::$rewrite_elements = [];
	}

	/**
	 * Return the rewrite elements registered
	 *
	 * @return array
	 * @author Nicolas Juen
	 */
	public static function get_rewrite_elements() {
		return self::$rewrite_elements;
	}

	/**
	 * Get the permalink rewrite element for the given post_type
	 *
	 * @param $post_type
	 *
	 * @return string
	 * @author Nicolas Juen
	 */
	public static function get_post_type_permalink_rewrite( $post_type ) {
		/**
		 * @var \WP_Rewrite $wp_rewrite
		 */
		global $wp_rewrite;

		if ( 'page' === $post_type ) {
			$post_type_permastruct = $wp_rewrite->get_page_permastruct();
		} else {
			$post_type_permastruct = $wp_rewrite->get_extra_permastruct( $post_type );
		}

		// Get the permastruct for single post_type
		$results = preg_match_all( '/%.+?%/', $post_type_permastruct, $tokens );

		if ( false === $results || empty( $tokens ) ) {
			return '';
		}

		return str_replace( $wp_rewrite->rewritecode, $wp_rewrite->rewritereplace, $post_type_permastruct );
	}

	/**
	 * Get a url based on query var + params if needed
	 *
	 * @param $query_var (string): the query var  to make the url with
	 * @param $params (array): the params to add at the end of the url
	 *
	 * @return false|string : the url rewrited
	 * @author Nicolas Juen
	 */
	public static function get_url( $query_var, $params = [] ) {
		// Get the slug
		$slug = self::rewrite_slug( $query_var );

		// If empty return false
		if ( empty( $slug ) ) {
			return false;
		}

		if ( ! isset( $params ) || empty( $params ) ) {
			return trailingslashit( home_url( $slug ) );
		}

		return add_query_arg( $params, trailingslashit( home_url( $slug ) ) );
	}

	/**
	 * Make a complex url with multiple slugs
	 *
	 * @param array $slugs  : the query vars to make the url with
	 * @param array $params : the params to add at the end of the url
	 *
	 * @return false|string : the url rewrited
	 * @author Nicolas Juen
	 */
	public static function get_url_complex( array $slugs, $params = [] ) {
		if ( empty( $slugs ) ) {
			return '';
		}

		// if not array, make normal url
		if ( 1 === count( $slugs ) ) {
			return self::get_url( $slugs[0] );
		}

		$out_slugs = [];
		foreach ( $slugs as $key => $slug ) {
			$t_slug = self::rewrite_slug( $slug );
			if ( ! empty( $t_slug ) ) {
				$out_slugs[] = $t_slug;
				continue;
			}

			$out_slugs[] = $slug;
		}

		if ( ! isset( $params ) || empty( $params ) ) {
			return trailingslashit( home_url( implode( '/', $out_slugs ) ) );
		}

		return add_query_arg( $params, trailingslashit( home_url( implode( '/', $out_slugs ) ) ) );
	}

	/**
	 * Get a url based on query var
	 *
	 * @param $query_var (string): the query var  to make the url with
	 *
	 * @return false|string : the url rewrited
	 * @author Nicolas Juen
	 */
	public static function rewrite_slug( $query_var = '' ) {
		if ( ! isset( $query_var ) || empty( $query_var ) || ! isset( self::$rewrite_elements[ $query_var ] ) ) {
			return '';
		}

		return self::$rewrite_elements[ $query_var ];
	}
}
