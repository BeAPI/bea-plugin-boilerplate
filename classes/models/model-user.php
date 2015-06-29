<?php
namespace BEA\PB\Models;

class User {

	/**
	 * @var \WP_User : the User object
	 */
	public $user;

	/**
	 * @var int the element id
	 */
	protected $ID;

	/**
	 * @var bool
	 */
	protected $is = false;

	/**
	 *
	 * @param \WP_User $object
	 *
	 */
	function __construct( \WP_User $object ) {
		if ( empty( $object ) ) {
			return false;
		}

		if ( is_null( $object ) || !$object->exists() ) {
			return false;
		}

		$this->user = $object;
		$this->ID        = $object->ID;
		$this->is        = true;

		return;
	}

	/**
	 * Check if the object is rightly instaciated
	 *
	 * @return bool
	 */
	public function is() {
		return $this->is;
	}

	/**
	 * Create user
	 *
	 * @param string $user_name
	 * @param string $user_email
	 *
	 * @return false or user model
	 *
	 * @author Alexandre Sadowski
	 */
	public static function create( $user_name, $user_email ) {
		return self::_create( $user_name, $user_email );
	}

	/**
	 * User creation method
	 *
	 *
	 * @param $user_name
	 * @param $user_email
	 *
	 * @return User|bool
	 */
	protected static function _create( $user_name, $user_email ){
		$random_password = wp_generate_password( 12, false );
		$user_id         = wp_create_user( $user_name, $random_password, $user_email );

		if ( is_wp_error( $user_id ) ) {
			return false;
		}

		return new self( new \WP_User($user_id) );
	}

	/**
	 * @return int
	 */
	public function get_ID() {
		return $this->ID;
	}


	/**
	 * @return \WP_User
	 */
	public function get_user() {
		return $this->user;
	}

	/**
	 * Retrieve the avatar `<img>` tag for a user, email address, MD5 hash, comment, or post.
	 *
	 * @param mixed $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash, user email, \WP_User object, \WP_Post object, or comment object.
	 * @param int    $size       Optional. Height and width of the avatar image file in pixels. Default 96.
	 * @param string $default    Optional. URL for the default image or a default type. Accepts '404' (return a 404 instead of a default image), 'retro' (8bit), 'monsterid'
	 * 							(monster), 'wavatar' (cartoon face), 'indenticon' (the "quilt"),
	 *							'mystery', 'mm', or 'mysterman' (The Oyster Man), 'blank' (transparent GIF),
	 *							or 'gravatar_default' (the Gravatar logo). Default is the value of the 'avatar_default' option, with a fallback of 'mystery'.
	 *
	 * @param string $alt        Optional. Alternative text to use in &lt;img&gt; tag. Default empty.
	 * @param array  $args       {
	 *     Optional. Extra arguments to retrieve the avatar.
	 *
	 *     @type int          $height        Display height of the avatar in pixels. Defaults to $size.
	 *     @type int          $width         Display width of the avatar in pixels. Defaults to $size.
	 *     @type bool         $force_default Whether to always show the default image, never the Gravatar. Default false.
	 *     @type string       $rating        What rating to display avatars up to. Accepts 'G', 'PG', 'R', 'X', and are
	 *                                       judged in that order. Default is the value of the 'avatar_rating' option.
	 *     @type string       $scheme        URL scheme to use. See set_url_scheme() for accepted values.
	 *                                       Default null.
	 *     @type array|string $class         Array or string of additional classes to add to the &lt;img&gt; element.
	 *                                       Default null.
	 *     @type bool         $force_display Whether to always show the avatar - ignores the show_avatars option.
	 *                                       Default false.
	 *     @type string       $extra_attr    HTML attributes to insert in the IMG element. Is not sanitized. Default empty.
	 * }
	 * @return false|string `<img>` tag for the user's avatar. False on failure.
	 */
	public function get_avatar( $size = 96, $default = '', $alt = '', $args = null ) {
		return get_avatar( $this->get_ID(), $size, $default, $alt, $args );
	}

	/**
	 * Get first name
	 *
	 * @return bool|string
	 */
	public function get_first_name( ) {
		$first_name = $this->user->get( 'first_name' );
		return !empty( $first_name ) ? $first_name : false;
	}

	/**
	 * Get last name
	 *
	 * @return bool|string
	 */
	public function get_last_name( ) {
		$last_name = $this->user->get( 'last_name' );
		return !empty( $last_name ) ? $last_name : false;
	}


	/**
	 * Get email of user
	 *
	 * @return bool|string
	 */
	public function get_email( ) {
		$user_email = $this->user->get( 'user_email' );
		return !empty( $user_email ) ? $user_email : false;
	}

	/**
	* Check capability of user
	*
	* @return bool
	*/
	public function has_cap( $cap ){
		return $this->user->has_cap( $cap );
	}

	/**
	 * Provided the meta value of meta key given
	 *
	 * @param string $key
	 * @param bool $format : format or not, specific to ACF
	 *
	 * @return bool
	 */
	public function get_meta( $key, $format = true ) {
		if ( empty( $key ) || ! $this->is() ) {
			return false;
		}

		// Get all ACF fields
		$fields = $this->get_fields();

		// Check ACF
		if ( ! isset( $fields[ $key ] ) || ! function_exists( 'get_field' ) ) {
			return $this->user->{$key};
		}

		return get_field( $fields[ $key ], 'user_'.$this->get_ID(), $format );
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
		if ( empty( $key ) || ! $this->is() ) {
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
			return update_user_meta( $this->get_ID(), $key, $value );
		}

		// Get the fields and use the ACF ones
		$fields = $this->get_fields();
		$key    = isset( $fields[ $key ] ) ? $fields[ $key ] : $key;

		return update_field( $key, $value, 'user_'.$this->get_ID() );
	}

	/**
	 * Get the fields acf like name => key
	 *
	 * @return array
	 */
	protected function get_fields( ) {
		if ( ! is_null( $this->fields ) ) {
			return $this->fields;
		}

		$groups = acf_get_field_groups( array( 'user_role' => 'all' ) );

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
	 * Delete entirely the current object and disconnect it.
	 *
	 * @param int $reassign, Reassign posts and links to new User ID
	 *
	 * @return bool|\WP_Error
	 */
	public function delete( $reassign = null ) {
		if ( false === $this->is() ) {
			return new \WP_Error( 'nodata', __( 'Impossible to delete the current user', 'bea-pb' ) );
		}

		return (bool) wp_delete_user( $this->get_ID(), $reassign );
	}

	/**
	 * Get the current model permalink
	 *
	 * @param array $args
	 *
	 * @return string|bool
	 */
	public function get_permalink( $args = array() ) {
		$url = get_the_author_meta('url', $this->get_ID() );
		if( $url ){
			return add_query_arg( $args, $url );
		}
		return false;
	}

}