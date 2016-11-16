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
	 * @var string  the post type for the current model
	 */
	protected $post_type;

	/**
	 * @var int the element id
	 */
	protected $ID;

	/**
	 * @var \WP_Post : the WordPress object
	 */
	public $wp_object;

	/**
	 * The user ACF fields
	 *
	 * @var array
	 */
	protected $fields = null;

	/**
	 * Create a new model
	 *
	 * @param \WP_Post $object
	 *
	 * @throws \Exception
	 */
	function __construct( \WP_Post $object ) {

		if ( get_post_type( $object ) !== $this->post_type ) {
			throw new \Exception( sprintf( '%s post type does not match model post type %s', get_post_type( $object ), $this->post_type ), 'mismatch_post_type' );
		}

		$this->wp_object = $object;
		$this->ID        = $object->ID;
	}

	/**
	 * @param \WP_Post $object
	 *
	 * @return \WP_Error|Model
	 */
	public static function get_model( \WP_Post $object ) {
		$post_type = get_post_type_object( $object->post_type );

		if ( empty( $post_type->model_class ) || ! class_exists( $post_type->model_class ) ) {
			return new \WP_Error( 'fail_model_find', sprintf( 'Fail to find model for post_type %s', get_post_type( $object ) ) );
		}

		try {
			$final_class = new $post_type->model_class( $object );
		} catch ( \Exception $e ) {
			return new \WP_Error( 'fail_model_instantiation', sprintf( 'Fail to instantiate model for post_type %s', get_post_type( $object ) ) );
		}

		// Give the model
		return $final_class;
	}

	/**
	 * Get among all classes the right one
	 *
	 * @param $class
	 *
	 * @return bool
	 */
	public static function filter_classes( $class ) {
		if ( false === is_subclass_of( $class, __NAMESPACE__ . '\\' . 'Model' )  ) {
			return false;
		}

		return get_class_vars( $class );
	}

	/**
	 * @return int
	 */
	public function get_ID() {
		return $this->ID;
	}

	/**
	 * @return string
	 */
	public function get_title() {
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
	public function get_object() {
		return $this->wp_object;
	}

	/**
	 * Provided the meta value of meta key given
	 *
	 * @param string $key : the ACF or the meta key to get the data from
	 * @param bool $format : format or not, specific to ACF
	 *
	 * @return bool
	 */
	public function get_meta( $key, $format = true ) {
		if ( empty( $key ) ) {
			return false;
		}

		// Get all ACF fields
		$fields = $this->get_fields();

		// Check ACF
		if ( ! in_array( $key, $fields ) && ! isset( $fields[ $key ] ) || ! function_exists( 'get_field' ) ) {
			return $this->wp_object->{$key};
		}
		
		// On ACF given key
		$key = in_array( $key, $fields ) ? $key : $fields[ $key ];

		return get_field( $key, $this->get_ID(), $format );
	}


	/**
	 * Update a post meta value
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return bool|int
	 */
	public function update_meta( $key, $value = '' ) {
		if ( empty( $key ) ) {
			return false;
		}

		// Check if model implement a method for this particular meta
		if ( method_exists( $this, 'update_meta_' . $key ) ) {
			return call_user_func( array( $this, 'update_meta_' . $key ), $value );
		}

		return $this->_update_meta( $key, $value );
	}

	/**
	 * Really update the value
	 *
	 * @param        $key
	 * @param string $value
	 *
	 * @return bool|int
	 */
	protected function _update_meta( $key, $value = '' ) {
		if ( ! function_exists( 'update_field' ) ) {
			return update_post_meta( $this->get_ID(), $key, $value );
		}

		// Get the fields and use the ACF ones
		$fields = $this->get_fields();
		$key    = isset( $fields[ $key ] ) ? $fields[ $key ] : $key;

		return update_field( $key, $value, $this->get_ID() );
	}

	/**
	 * Set the model terms
	 *
	 * @param      $terms
	 * @param      $taxonomy
	 * @param bool $append
	 *
	 * @return array|\WP_Error
	 */
	public function set_terms( $terms, $taxonomy, $append = false ) {
		return wp_set_object_terms( $this->get_ID(), $terms, $taxonomy, $append );
	}

	/**
	 * Get the terms for the model
	 *
	 * @param       $taxonomy
	 * @param array $args
	 *
	 * @return array|\WP_Error
	 */
	public function get_terms( $taxonomy, array $args = array() ) {
		$terms = get_object_term_cache( $this->get_ID(), $taxonomy, $args );
		if ( false === $terms ) {
			$terms = wp_get_object_terms( $this->get_ID(), $taxonomy, $args );
		}

		return $terms;
	}

	/**
	 * Get the first terms
	 *
	 * @param $taxonomy
	 * @param array $args
	 *
	 * @return bool|mixed
	 */
	public function get_first_term( $taxonomy, array $args = array() ) {
		$terms = $this->get_terms( $taxonomy, $args );

		if ( is_wp_error( $terms ) ) {
			return false;
		}

		return reset( $terms );
	}

	/**
	 * Check if the current object is in term
	 *
	 * @param $taxonomy
	 *
	 * @return bool|\WP_Error
	 */
	public function has_terms( $taxonomy ) {
		return is_object_in_term( $this->get_ID(), $taxonomy );
	}

	/**
	 * @param $size
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function get_thumbnail( $size, array $attributes = array() ) {
		return get_the_post_thumbnail( $this->get_ID(), $size, $attributes );
	}

	/**
	 * Return the post thumbnail ID
	 *
	 * @return int
	 */
	public function get_thumbnail_id() {
		return get_post_thumbnail_id( $this->get_ID() );
	}

	/**
	 * Set the thumbnail for the current object
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function set_thumbnail( $id ) {
		// Remove the attachment if here
		if ( has_post_thumbnail( $this->get_ID() ) ) {
			wp_delete_attachment( $this->get_thumbnail_id(), true );
		}

		return set_post_thumbnail( $this->wp_object, $id );
	}

	/**
	 * Check the current object has a thumbnail
	 *
	 * @return bool
	 */
	public function has_thumbnail() {
		return has_post_thumbnail( $this->get_ID() );
	}

	/**
	 * Get the fields acf like name => key
	 *
	 * @return array
	 */
	protected function get_fields() {
		if ( ! is_null( $this->fields ) ) {
			return $this->fields;
		}

		$groups = acf_get_field_groups( array( 'post_type' => $this->post_type ) );

		if ( empty( $groups ) ) {
			return array();
		}
		$fields = array();
		foreach ( $groups as $group ) {
			$fields += acf_get_fields( $group );
		}

		$acf_fields = array();
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
	 * @param $object_id
	 * @param $connection_type
	 * @param array $metas : metas to set on the creation
	 *
	 * @return bool|int|\WP_Error
	 */
	protected function connect( $object_id, $connection_type, $metas = array() ) {
		if ( ! function_exists( 'p2p_type' ) ) {
			return false;
		}

		return p2p_type( $connection_type )->connect( $this->get_ID(), $object_id, $metas );
	}

	/**
	 * Disconnect the current object to another object
	 *
	 * @param $object_id
	 * @param $connection_type
	 *
	 * @return bool|int|\WP_Error
	 */
	protected function disconnect( $object_id, $connection_type ) {
		if ( ! function_exists( 'p2p_type' ) ) {
			return false;
		}

		// Delete connection
		return p2p_type( $connection_type )->disconnect( $this->get_ID(), $object_id );
	}

	/**
	 *
	 * Update an object
	 *
	 * @param array $data
	 *
	 * @return \WP_Error|bool
	 *
	 */
	public function update( array $data ) {
		return $this->_update( $data );
	}

	/**
	 *
	 * Really update object
	 *
	 * @param array $data
	 *
	 * @return \WP_Error|bool
	 */
	protected function _update( array $data ) {
		if ( empty( $data ) || ! isset( $data ) ) {
			return new \WP_Error( 'nodata', __( 'No data', 'bea-plugin-boilerplate' ) );
		}

		// Get the defaults
		$defaults = $this->get_all_data();

		// Get all the data
		$data = wp_parse_args( $data, $defaults );

		// Set ID
		$data['ID']        = $this->get_ID();
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
		return wp_delete_post( $this->get_ID(), $force_delete );
	}

	/**
	 * Get the current model permalink
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_permalink( $args = array() ) {
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
		$wp_keys = array_filter( get_post_custom_keys( $this->get_ID() ), array( $this, 'filter_post_meta_keys' ) );

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
			$data[ $tax ] = $this->get_terms( $tax, array( 'fields' => 'ids' ) );
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
	public static function filter_post_array( $data ) {
		return array_intersect_key( $data, array_flip( array_filter( array_keys( $data ), array(
			__CLASS__,
			'filter_post_keys'
		) ) ) );
	}

	/**
	 * Return true on allowed post key
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public static function filter_post_keys( $key ) {
		$keys = array_keys( get_class_vars( '\WP_Post' ) );

		// Add missing post fields
		$keys[] = 'import_id';
		$keys[] = 'context';
		$keys[] = 'tags_input';
		$keys[] = 'tax_input';
		$keys[] = 'post_category';

		return in_array( $key, $keys );
	}

	/**
	 * Remove unwanted fields from WP database
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public function filter_post_meta_keys( $key ) {
		$fields = $this->get_fields();

		return ( substr( $key, 0, 1 ) !== '_' && ! isset( $fields[ $key ] ) );
	}
}
