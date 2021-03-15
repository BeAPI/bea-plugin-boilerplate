<?php

namespace BEA\PB;

use \Bea_Log;

/**
 * This class needs Bea_Log to work
 * This class purpose is to handle cron process by :
 * - creating lock files
 * - Having a start and an end process methods
 *
 * Class Cron
 * @package BEA\PB
 */
abstract class Cron {

	/**
	 * Type for the log filename
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * @var \Bea_Log $log
	 */
	private $log;

	/**
	 * @var \WP_Filesystem_Direct $filesystem
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
	 * @return bool
	 */
	public function is_locked() {
		clearstatcache();

		return self::get_filesystem()->is_file( self::get_lock_file_path() );
	}

	/**
	 * Create the .lock file
	 *
	 * @return bool
	 */
	public function create_lock_file() {
		return self::get_filesystem()->touch( self::get_lock_file_path() );
	}

	/**
	 * Delete lock file
	 *
	 * @return bool
	 */
	public function delete_lock_file() {
		// Delete the lock file
		return self::is_locked() ? self::get_filesystem()->delete( self::get_lock_file_path() ) : true;
	}

	/**
	 * Get lock file
	 *
	 * @return string
	 */
	private function get_lock_file_path() {
		// Get the file system
		$filesystem = self::get_filesystem();

		// Base filename
		$base = is_multisite() ? 'lock-cron-' . get_current_blog_id() . '-' : '.lock-cron-';

		// Create the file name
		$name = $base . $this->type;

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
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
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
	 * Get the log file path
	 *
	 * @param bool $extension
	 *
	 * @return string
	 */
	private function get_log_file_path( $extension = true ) {
		return WP_CONTENT_DIR . '/' . sanitize_file_name( $this->get_log_filename() ) . ( true === $extension ? '.log' : '' );
	}

	/**
	 * Log a message for the current type
	 *
	 * @param $message : message to write on the log file
	 * @param $type : log level message
	 */
	protected function add_log( $message, $type = \Bea_Log::gravity_7 ) {
		// Log if bea log or not
		if ( ! is_a( $this->log, '\Bea_Log' ) ) {
			$this->log = new \Bea_Log( $this->get_log_file_path( false ), '.log' );
		}
		$this->log->log_this( $message, $type );

		//phpcs:ignore
		printf( '%s %s \n', date( '[d-m-Y H:i:s]' ), esc_html( $message ) );
	}
}
