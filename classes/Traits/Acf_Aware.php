<?php

namespace BEA\PB\Traits;

/**
 * Optional ACF helpers for post models.
 */
trait Acf_Aware {

	/**
	 * All ACF fields mapped as name => key.
	 *
	 * @var array<string, string>|null
	 */
	protected $fields;

	/**
	 * Get a post meta value, using ACF when available.
	 *
	 * @param string $key    Meta or ACF field key/name.
	 * @param bool   $format Whether ACF should format the value.
	 *
	 * @return mixed
	 */
	protected function get_acf_meta( string $key, $format = true ) {
		if ( empty( $key ) ) {
			return false;
		}

		if ( ! function_exists( 'get_field' ) ) {
			return get_post_meta( $this->get_id(), $key, true );
		}

		return get_field( $key, $this->get_id(), $format );
	}

	/**
	 * Update a post meta value, using ACF when available.
	 *
	 * @param string $key   Meta or ACF field key/name.
	 * @param mixed  $value Value to store.
	 *
	 * @return bool|int
	 */
	protected function update_acf_meta( string $key, $value = '' ) {
		if ( empty( $key ) ) {
			return false;
		}

		if ( ! function_exists( 'update_field' ) ) {
			return update_post_meta( $this->get_id(), $key, $value );
		}

		return update_field( $key, $value, $this->get_id() );
	}

	/**
	 * Get ACF fields attached to the model post type.
	 *
	 * @return array<string, string>
	 */
	protected function get_acf_fields(): array {
		if ( ! is_null( $this->fields ) ) {
			return $this->fields;
		}

		if ( ! function_exists( 'acf_get_field_groups' ) ) {
			return [];
		}

		$groups = acf_get_field_groups( [ 'post_type' => $this->post_type ] );

		if ( empty( $groups ) ) {
			return [];
		}

		$fields     = [];
		$acf_fields = [];

		foreach ( $groups as $group ) {
			$fields += acf_get_fields( $group );
		}

		foreach ( $fields as $field ) {
			$acf_fields[ $field['name'] ] = $field['key'];
		}

		$this->fields = $acf_fields;

		return $acf_fields;
	}
}
