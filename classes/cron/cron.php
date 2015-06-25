<?php
namespace BEA_PB;
abstract class Cron {

	/**
	 * Type for the log filename
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * @var $log \Bea_Log
	 */
	private $log;

	/**
	 * @var $filesystem \WP_Filesystem_Direct
	 */
	protected $filesystem;

	/**
	 * Process the cron
	 *
	 * @return mixed
	 */
	abstract public function process();

	/**
	 * Check if locked file exist
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function is_locked( $name ) {
		clearstatcache();

		return self::get_filesystem()->is_file( self::get_lock_file_path( $name ) );
	}

	/**
	 * Create the .lock file
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function create_lock_file( $name ) {
		return self::get_filesystem()->touch( self::get_lock_file_path( $name ) );
	}

	/**
	 * Delete lock file
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function delete_lock_file( $name ) {
		// Delete the lock file
		return self::is_locked( $name ) ? self::get_filesystem()->delete( self::get_lock_file_path( $name ) ) : true;
	}

	/**
	 * Get lock file
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	private static function get_lock_file_path( $name ) {
		// Get the file system
		$filesystem = self::get_filesystem();

		// Base filename
		$base = is_multisite() ? 'lock-cron-' . get_current_blog_id() . '-' : '.lock-cron-';

		// Create the file name
		$name = ! empty( $name ) ? $base . $name : $base;

		// Return the lock file path
		return $filesystem->wp_content_dir() . '/' . sanitize_file_name( $name );
	}

	/**
	 * Get the file system of WP
	 *
	 * @return \WP_Filesystem_Direct
	 * @author Nicolas Juen
	 */
	private static function get_filesystem() {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
		$filesystem = new \WP_Filesystem_Direct( new \StdClass() );

		return $filesystem;
	}

	/**
	 * Return the filename
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function get_log_filename() {
		if ( empty( $this->type ) ) {
			throw new \Exception( 'No log type property, this needs to be set.' );
		}

		return 'cron-' . $this->type;
	}

	/**
	 * Get the log filepath
	 *
	 * @param bool $extension
	 *
	 * @return string
	 */
	private function get_log_file_path( $extension = true ) {
		return WP_CONTENT_DIR . '/' . sanitize_file_name( $this->get_log_filename() ) . ( true === $extension ? '.log' : '' );
	}

	/**
	 * Add all the log
	 *
	 * @param string $message
	 *
	 */
	protected function add_log( $message ) {
		// Log if bea log or not
		if ( class_exists( '\Bea_Log' ) ) {
			if ( ! is_a( $this->log, '\Bea_Log' ) ) {
				$this->log = new \Bea_Log( $this->get_log_file_path( false ), '.log' );
			}
			$this->log->log_this( $message );
		} else {
			error_log( date( '[d-m-Y H:i:s]' ) . $message . "\n", 3, $this->get_log_file_path() );
		}

		echo date( '[d-m-Y H:i:s]' ) . $message . "\n";
	}
}