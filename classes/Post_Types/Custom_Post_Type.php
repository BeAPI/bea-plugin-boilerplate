<?php

namespace BEA\PB\Post_Types;

use BEA\PB\Models\Custom_Post_Type_Model;

/**
 * Example custom post type registration.
 */
class Custom_Post_Type {

	/**
	 * Register the example post type and taxonomy.
	 */
	public static function register(): void {
		register_post_type(
			BEA_PB_CPT_NAME,
			[
				'labels'       => [
					'name'          => __( 'Custom post types', 'bea-plugin-boilerplate' ),
					'singular_name' => __( 'Custom post type', 'bea-plugin-boilerplate' ),
				],
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-admin-post',
				'supports'     => [ 'title', 'editor', 'thumbnail' ],
				'rewrite'      => [
					'slug' => BEA_PB_CPT_NAME,
				],
				'model_class'  => Custom_Post_Type_Model::class,
			]
		);

		register_taxonomy(
			BEA_PB_TAXO_NAME,
			BEA_PB_CPT_NAME,
			[
				'labels'       => [
					'name'          => __( 'Custom taxonomies', 'bea-plugin-boilerplate' ),
					'singular_name' => __( 'Custom taxonomy', 'bea-plugin-boilerplate' ),
				],
				'public'       => true,
				'show_in_rest' => true,
				'rewrite'      => [
					'slug' => BEA_PB_TAXO_NAME,
				],
			]
		);
	}
}
