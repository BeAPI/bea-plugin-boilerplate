<?php

namespace BEA\PB;

/**
 * Base cron handler with lock files and optional logging.
 */
abstract class Cron {

	/**
	 * Type for the log and lock filename.
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 * @var object|null
	 */
	private $log;

	/**
	 * Process the cron.
	 *
	 * @return mixed
	 */
	abstract public function process();

	/**
	 * Check if a lock file exists.
	 *
	 * @return bool
	 */
	public function is_locked() {
		clearstatcache();

		return self::get_filesystem()->is_file( $this->get_lock_file_path() );
	}

	/**
	 * Create the lock file.
	 *
	 * @return bool
	 */
	public function create_lock_file() {
		return self::get_filesystem()->touch( $this->get_lock_file_path() );
	}

	/**
	 * Delete the lock file.
	 *
	 * @return bool
	 */
	public function delete_lock_file() {
		return $this->is_locked() ? self::get_filesystem()->delete( $this->get_lock_file_path() ) : true;
	}

	/**
	 * Get the lock file path.
	 *
	 * @return string
	 */
	private function get_lock_file_path() {
		$filesystem = self::get_filesystem();
		$base       = is_multisite() ? 'lock-cron-' . get_current_blog_id() . '-' : '.lock-cron-';
		$name       = $base . $this->type;

		return $filesystem->wp_content_dir() . '/' . sanitize_file_name( $name );
	}

	/**
	 * @return \WP_Filesystem_Direct
	 */
	private static function get_filesystem() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

		return new \WP_Filesystem_Direct( new \StdClass() );
	}

	/**
	 * @throws \Exception When the log type is missing.
	 */
	private function get_log_filename(): string {
		if ( empty( $this->type ) ) {
			throw new \Exception( 'No log type property, this needs to be set.' );
		}

		return 'cron-' . $this->type;
	}

	/**
	 * @param bool $extension Whether to append the .log extension.
	 */
	private function get_log_file_path( $extension = true ): string {
		return WP_CONTENT_DIR . '/' . sanitize_file_name( $this->get_log_filename() ) . ( true === $extension ? '.log' : '' );
	}

	/**
	 * Log a message for the current cron type.
	 *
	 * @param string $message Message to write.
	 * @param string $type    Optional log level for Bea_Log.
	 */
	protected function add_log( string $message, string $type = 'info' ): void {
		if ( class_exists( '\Bea_Log' ) ) {
			if ( ! is_a( $this->log, '\Bea_Log' ) ) {
				$this->log = new \Bea_Log( $this->get_log_file_path( false ), '.log' );
			}

			$gravity = defined( '\Bea_Log::gravity_7' ) ? \Bea_Log::gravity_7 : $type;
			$this->log->log_this( $message, $gravity );
		} else {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( '[bea-pb][cron:%s] %s', $this->type, $message ) );
		}
	}
}
