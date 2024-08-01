<?php

namespace BEA\PB\Models;

/**
 * The purpose of this model is to use methods from \WP_post and implement custom ones
 * This model is only for the post_types
 *
 * Use a maximum of the internal methods for implementing yours.
 *
 * If you want update a meta then use ->update_meta('the_key', 'data');
 * The class tries to launch the method :
 *  ->update_meta_the_key()
 *
 * Class Model
 * @package BEA\PB\Models
 */
abstract class Model {
	/**
	 * The post type for the current model
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * The element id
	 * @var int
	 */
	protected $ID;

	/**
	 * The WordPress object
	 *
	 * @var \WP_Post
	 */
	public $wp_object;

	/**
	 * All ACF fields
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Create a new model
	 *
	 * @param \WP_Post $object
	 *
	 * @throws \Exception
	 */
	public function __construct( \WP_Post $post_obj ) {

		if ( $post_obj->post_type !== $this->post_type ) {
			throw new \InvalidArgumentException( sprintf( '%s post type does not match model post type %s', esc_html( $post_obj->post_type ), esc_html( $this->post_type ) ) );
		}

		$this->wp_object = $post_obj;
		$this->ID        = $post_obj->ID;
	}

	/**
	 * @param \WP_Post $object
	 *
	 * @return object|\WP_Error
	 */
	public static function get_model( \WP_Post $post_obj ) {
		$post_type = get_post_type_object( $post_obj->post_type );

		if ( empty( $post_type->model_class ) || ! class_exists( $post_type->model_class ) ) {
			return new \WP_Error( 'fail_model_find', sprintf( 'Fail to find model for post_type %s', get_post_type( $post_obj ) ) );
		}

		try {
			$final_class = new $post_type->model_class( $post_obj );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'fail_model_instantiation', sprintf( 'Fail to instantiate model for post_type %s', get_post_type( $post_obj ) ) );
		}

		// Give the model
		return $final_class;
	}

	/**
	 * Get among all classes the right one
	 *
	 * @param string $class
	 *
	 * @return array|null
	 */
	public static function filter_classes( string $class_name ): ?array {
		if ( false === is_subclass_of( $class_name, __CLASS__ ) ) {
			return null;
		}

		return get_class_vars( $class_name );
	}

	/**
	 * @return int
	 */
	public function get_id(): int {
		return $this->ID;
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return get_the_title( $this->wp_object );
	}

	/**
	 * @return false|string
	 */
	public function get_post_type() {
		return get_post_type( $this->wp_object );
	}

	/**
	 * @return \WP_Post
	 */
	public function get_object(): \WP_Post {
		return $this->wp_object;
	}

	/**
	 * Provided the meta value of meta key given
	 *
	 * @param string $key    : the ACF or the meta key to get the data from
	 * @param bool   $format : format or not, specific to ACF
	 *
	 * @return array|false|mixed
	 */
	public function get_meta( string $key, $format = true ) {
		if ( empty( $key ) ) {
			return false;
		}

		// Get all ACF fields
		$fields = $this->get_fields();

		// Check ACF
		if ( ! function_exists( '\get_field' ) || ( ! in_array( $key, $fields, true ) && ! isset( $fields[ $key ] ) ) ) {
			return $this->wp_object->{$key};
		}

		// On ACF given key
		$key = in_array( $key, $fields, true ) ? $key : $fields[ $key ];

		return \get_field( $key, $this->get_id(), $format );
	}

	/**
	 * Update a post meta value
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return bool|int
	 */
	public function update_meta( string $key, $value = '' ) {
		if ( empty( $key ) ) {
			return false;
		}

		// Check if model implement a method for this particular meta
		if ( method_exists( $this, 'update_meta_' . $key ) ) {
			return call_user_func( [ $this, 'update_meta_' . $key ], $value );
		}

		return $this->update_content_meta( $key, $value );
	}

	/**
	 * Really update the value
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return bool|int
	 */
	protected function update_content_meta( string $key, $value = '' ) {
		if ( ! function_exists( '\update_field' ) ) {
			return update_post_meta( $this->get_id(), $key, $value );
		}

		// Get the fields and use the ACF ones
		$fields = $this->get_fields();
		$key    = isset( $fields[ $key ] ) ? $fields[ $key ] : $key;

		return \update_field( $key, $value, $this->get_id() );
	}

	/**
	 * Set the model terms
	 *
	 * @param string|int|array $terms
	 * @param string           $taxonomy
	 * @param bool             $append
	 *
	 * @return array|\WP_Error
	 */
	public function set_terms( $terms, string $taxonomy, $append = false ) {
		return wp_set_object_terms( $this->get_id(), $terms, $taxonomy, $append );
	}

	/**
	 * Get the terms for the model
	 *
	 * @param string $taxonomy
	 * @param array  $args
	 *
	 * @return \WP_Term[]|\WP_Error
	 */
	public function get_terms( string $taxonomy, array $args = [] ) {
		if ( empty( $args ) ) {
			return get_the_terms( $this->get_id(), $taxonomy );
		}

		return wp_get_object_terms( $this->get_id(), $taxonomy, $args );
	}

	/**
	 * Get the first terms
	 *
	 * @param string $taxonomy
	 * @param array $args
	 *
	 * @return \WP_Term|null
	 */
	public function get_first_term( string $taxonomy, array $args = [] ): ?\WP_Term {
		$terms = $this->get_terms( $taxonomy, $args );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return null;
		}

		return reset( $terms );
	}

	/**
	 * Check if the current object is in term
	 *
	 * @param string $taxonomy
	 *
	 * @return bool|\WP_Error
	 */
	public function has_terms( string $taxonomy ) {
		return is_object_in_term( $this->get_id(), $taxonomy );
	}

	/**
	 * @param string $size
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function get_thumbnail( string $size, array $attributes = [] ): string {
		return get_the_post_thumbnail( $this->get_id(), $size, $attributes );
	}

	/**
	 * Return the post thumbnail ID
	 *
	 * @return int|false
	 */
	public function get_thumbnail_id() {
		return get_post_thumbnail_id( $this->get_id() );
	}

	/**
	 * Set the thumbnail for the current object
	 *
	 * @param int $id
	 *
	 * @return int|bool
	 */
	public function set_thumbnail( int $id ) {
		return set_post_thumbnail( $this->wp_object, $id );
	}

	/**
	 * Check the current object has a thumbnail
	 *
	 * @return bool
	 */
	public function has_thumbnail(): bool {
		return has_post_thumbnail( $this->get_id() );
	}

	/**
	 * Get the fields acf like name => key
	 *
	 * @return array
	 */
	protected function get_fields(): array {
		if ( ! is_null( $this->fields ) ) {
			return $this->fields;
		}

		$groups = \acf_get_field_groups( [ 'post_type' => $this->post_type ] );

		if ( empty( $groups ) ) {
			return [];
		}
		$fields = [];
		foreach ( $groups as $group ) {
			$fields += acf_get_fields( $group );
		}

		$acf_fields = [];
		/** @psalm-suppress PossiblyInvalidIterator */
		foreach ( $fields as $field ) {
			$acf_fields[ $field['name'] ] = $field['key'];
		}

		// Set the object available fields
		$this->fields = $acf_fields;

		return $acf_fields;
	}

	/**
	 * Connect the current object to another object
	 *
	 * @param int $object_id
	 * @param string $connection_type
	 * @param array $metas : metas to set on the creation
	 *
	 * @return bool|int|\WP_Error
	 */
	protected function connect( int $object_id, string $connection_type, $metas = [] ) {
		if ( ! function_exists( 'p2p_type' ) ) {
			return false;
		}

		return p2p_type( $connection_type )->connect( $this->get_id(), $object_id, $metas );
	}

	/**
	 * Disconnect the current object to another object
	 *
	 * @param int    $object_id
	 * @param string $connection_type
	 *
	 * @return bool|int|\WP_Error
	 */
	protected function disconnect( int $object_id, string $connection_type ) {
		if ( ! function_exists( 'p2p_type' ) ) {
			return false;
		}

		// Delete connection
		return p2p_type( $connection_type )->disconnect( $this->get_id(), $object_id );
	}

	/**
	 *
	 * Update an object
	 *
	 * @param array $data
	 *
	 * @return \WP_Error|Model
	 *
	 */
	public function update( array $data ) {
		return $this->update_content( $data );
	}

	/**
	 *
	 * Really update object
	 *
	 * @param array $data
	 *
	 * @return \WP_Error|Model
	 */
	protected function update_content( array $data ) {
		if ( empty( $data ) ) {
			return new \WP_Error( 'nodata', __( 'No data', 'bea-plugin-boilerplate' ) );
		}

		// Get the defaults
		$defaults = $this->get_all_data();

		// Get all the data
		$data = wp_parse_args( $data, $defaults );

		// Set ID
		$data['ID']        = $this->get_id();
		$data['post_type'] = $this->post_type;

		// Filter post keys
		$post_data = self::filter_post_array( $data );

		$post_edit = wp_update_post( $post_data, true );

		if ( is_wp_error( $post_edit ) ) {
			return $post_edit;
		}

		// Update the \WP_Post object
		$this->wp_object = get_post( $this->ID );

		return $this;
	}

	/**
	 * Delete entirely the current object and disconnect it.
	 *
	 * @param bool $force_delete
	 *
	 * @return array|bool|\WP_Post
	 */
	public function delete( $force_delete = false ) {
		return wp_delete_post( $this->get_id(), $force_delete );
	}

	/**
	 * Get the current model permalink
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_permalink( $args = [] ) {
		return add_query_arg( $args, get_permalink( $this->get_object() ) );
	}

	/**
	 * Get all the data for the current object
	 *
	 * @return array
	 */
	public function get_all_data() {
		// Get the post data
		$data = (array) $this->get_object();

		// Get the keys
		$wp_keys = array_filter( get_post_custom_keys( $this->get_id() ), [ $this, 'filter_post_meta_keys' ] );

		// Add the ACF fields
		foreach ( $this->get_fields() as $key => $acf_key ) {
			$data[ $key ] = $this->get_meta( $acf_key );
		}

		// Add the normal fields
		foreach ( $wp_keys as $key ) {
			$data[ $key ] = $this->get_meta( $key );
		}

		// Add the taxonomies
		foreach ( get_object_taxonomies( $this->get_object() ) as $tax ) {
			$data[ $tax ] = $this->get_terms( $tax, [ 'fields' => 'ids' ] );
		}

		return $data;
	}

	/**
	 * Filter all the post fields
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public static function filter_post_array( array $data ): array {
		return array_intersect_key(
			$data,
			array_flip(
				array_filter(
					array_keys( $data ),
					[
						__CLASS__,
						'filter_post_keys',
					]
				)
			)
		);
	}

	/**
	 * Return true on allowed post key
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public static function filter_post_keys( string $key ): bool {
		$keys = array_keys( get_class_vars( '\WP_Post' ) );

		// Add missing post fields
		$keys[] = 'import_id';
		$keys[] = 'context';
		$keys[] = 'tags_input';
		$keys[] = 'tax_input';
		$keys[] = 'post_category';

		return in_array( $key, $keys, true );
	}

	/**
	 * Remove unwanted fields from WP database
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function filter_post_meta_keys( string $key ): bool {
		$fields = $this->get_fields();

		return ( strpos( $key, '_' ) !== 0 && ! isset( $fields[ $key ] ) );
	}
}
