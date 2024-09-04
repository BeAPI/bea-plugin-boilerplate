<?php

namespace BEA\PB\Models;

/**
 * This class pupose is to manipulate and access to method of the WP_User class
 * It's not mandatory to extend from this class and you can se it as it is
 *
 * Class User
 * @package BEA\PB\Models
 */
class User {

	/**
	 * The WP User object
	 * @var \WP_User
	 */
	public $user;

	/**
	 * The user id
	 * @var int
	 */
	protected $ID;

	/**
	 * All ACF fields
	 * @var array
	 */
	protected $fields;

	/**
	 *
	 * @param \WP_User $object
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct( \WP_User $user_obj ) {

		if ( ! $user_obj->exists() ) {
			throw new \InvalidArgumentException( 'User does not exist' );
		}

		$this->user = $user_obj;
		$this->ID   = $user_obj->ID;
	}

	/**
	 * Create user
	 *
	 * @param array       $args
	 * @param string|null $user_email
	 *
	 * @return \WP_Error|User
	 *
	 * @deprecated 2.1.3 $user_email Use first argument as array.
	 *
	 * @author     Alexandre Sadowski|Romain DORR
	 */
	public static function create( array $args, $user_email = null ) {
		if ( null !== $user_email ) {
			_deprecated_argument( __FUNCTION__, '2.1.3', esc_html__( 'Use first argument as array', 'bea-plugin-boilerplate' ) );
			$args = [
				'user_name'  => $args,
				'user_email' => $user_email,
			];
		}

		return self::create_user( $args );
	}

	/**
	 * User creation method
	 *
	 * @param array $args
	 *
	 * @return User|\WP_Error
	 *
	 * @author Alexandre Sadowski|Romain DORR
	 */
	protected static function create_user( array $args ) {
		$random_password = wp_generate_password( 12, false );

		$defaults = [
			'user_pass' => $random_password,
		];
		$userdata = wp_parse_args( $args, $defaults );

		$user_id = wp_insert_user( $userdata );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		return new self( new \WP_User( $user_id ) );
	}

	/**
	 * @return int
	 */
	public function get_id(): int {
		return $this->ID;
	}

	/**
	 * @return \WP_User
	 */
	public function get_user(): \WP_User {
		return $this->user;
	}

	/**
	 * Retrieve the avatar `<img>` tag for a user, email address, MD5 hash, comment, or post.
	 *
	 * @param int $size Optional. Height and width of the avatar image file in pixels. Default 96.
	 * @param string $default Optional. URL for the default image or a default type. Accepts '404' (return a 404 instead of a default image), 'retro' (8bit), 'monsterid'
	 *                            (monster), 'wavatar' (cartoon face), 'indenticon' (the "quilt"),
	 *                            'mystery', 'mm', or 'mysterman' (The Oyster Man), 'blank' (transparent GIF),
	 *                            or 'gravatar_default' (the Gravatar logo). Default is the value of the 'avatar_default' option, with a fallback of 'mystery'.
	 *
	 * @param string $alt Optional. Alternative text to use in &lt;img&gt; tag. Default empty.
	 * @param array $args {
	 *     Optional. Extra arguments to retrieve the avatar.
	 *
	 * @type int $height Display height of the avatar in pixels. Defaults to $size.
	 * @type int $width Display width of the avatar in pixels. Defaults to $size.
	 * @type bool $force_default Whether to always show the default image, never the Gravatar. Default false.
	 * @type string $rating What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are
	 *                                       judged in that order. Default is the value of the 'avatar_rating' option.
	 * @type string $scheme URL scheme to use. See set_url_scheme() for accepted values.
	 *                                       Default null.
	 * @type array|string $class Array or string of additional classes to add to the &lt;img&gt; element.
	 *                                       Default null.
	 * @type bool $force_display Whether to always show the avatar - ignores the show_avatars option.
	 *                                       Default false.
	 * @type string $extra_attr HTML attributes to insert in the IMG element. Is not sanitized. Default empty.
	 * }
	 * @return false|string `<img>` tag for the user's avatar. False on failure.
	 */
	public function get_avatar( $size = 96, $default_url = '', $alt = '', $args = null ) {
		return get_avatar( $this->get_id(), $size, $default_url, $alt, $args );
	}

	/**
	 * Get first name
	 *
	 * @return bool|string
	 */
	public function get_first_name() {
		$first_name = $this->user->get( 'first_name' );

		return ! empty( $first_name ) ? $first_name : false;
	}

	/**
	 * Get last name
	 *
	 * @return bool|string
	 */
	public function get_last_name() {
		$last_name = $this->user->get( 'last_name' );

		return ! empty( $last_name ) ? $last_name : false;
	}

	/**
	 * Get email of user
	 *
	 * @return bool|string
	 */
	public function get_email() {
		$user_email = $this->user->get( 'user_email' );

		return ! empty( $user_email ) ? $user_email : false;
	}

	/**
	 * Check capability of user
	 *
	 * @param string $capability
	 *
	 * @return mixed
	 */
	public function has_cap( string $capability ) {

		$args = array_slice( func_get_args(), 1 );
		$args = array_merge( [ $capability ], $args );

		return call_user_func_array( [ $this->get_user(), 'has_cap' ], $args );
	}

	/**
	 * Provided the meta value of meta key given
	 *
	 * @param string $key
	 * @param bool   $format : format or not, specific to ACF
	 *
	 * @return array|false|mixed
	 */
	public function get_meta( string $key, $format = true ) {
		if ( empty( $key ) ) {
			return false;
		}

		// Check ACF
		if ( ! function_exists( 'get_field' ) ) {
			return get_user_meta( $this->get_id(), $key, true );
		}

		return get_field( $key, 'user_' . $this->get_id(), $format );
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
			return $this->{'update_meta_' . $key}( $value );
		}

		return $this->update_user_meta( $key, $value );
	}

	/**
	 * Really update the value
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return bool|int
	 */
	protected function update_user_meta( string $key, $value = '' ) {
		if ( ! function_exists( 'update_field' ) ) {
			return update_user_meta( $this->get_id(), $key, $value );
		}

		return update_field( $key, $value, 'user_' . $this->get_id() );
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

		$groups = acf_get_field_groups( [ 'user_role' => 'all' ] );

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
	 * Delete entirely the current object and disconnect it.
	 *
	 * @param int $reassign , Reassign posts and links to new User ID
	 *
	 * @return bool|\WP_Error
	 */
	public function delete( $reassign = null ) {
		return wp_delete_user( $this->get_id(), $reassign );
	}

	/**
	 * Get the current model permalink
	 *
	 * @param array $args
	 *
	 * @return string|bool
	 */
	public function get_permalink( $args = [] ) {
		$url = get_the_author_meta( 'url', $this->get_id() );

		return ( ! $url ) ? add_query_arg( $args, $url ) : false;
	}
}
