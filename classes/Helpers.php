<?php

namespace BEA\PB;

/**
 * View and formatting helpers.
 */
class Helpers {

	/**
	 * Cached template paths keyed by template slug.
	 *
	 * @var array<string, string|false>
	 */
	private static $located_templates = [];

	/**
	 * Locate template in the theme or plugin if needed.
	 *
	 * @param string $tpl Template name without extension.
	 *
	 * @return bool|string
	 */
	public static function locate_template( string $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		if ( array_key_exists( $tpl, self::$located_templates ) ) {
			return self::$located_templates[ $tpl ];
		}

		$path = apply_filters( 'beapi_helpers_locate_template_templates', [ 'views/' . BEA_PB_VIEWS_FOLDER_NAME . '/' . $tpl . '.php' ], $tpl, __NAMESPACE__ );

		$located = locate_template( $path, false, false );
		if ( ! empty( $located ) ) {
			self::$located_templates[ $tpl ] = $located;

			return $located;
		}

		if ( is_file( BEA_PB_DIR . 'views/' . $tpl . '.php' ) ) {
			self::$located_templates[ $tpl ] = BEA_PB_DIR . 'views/' . $tpl . '.php';

			return self::$located_templates[ $tpl ];
		}

		self::$located_templates[ $tpl ] = false;

		return false;
	}

	/**
	 * Include the template given.
	 *
	 * @param string               $tpl  Template name.
	 * @param array<string, mixed> $data Template data.
	 *
	 * @return bool
	 */
	public static function include_template( string $tpl, array $data = [] ): bool {
		if ( empty( $tpl ) ) {
			return false;
		}

		$tpl_path = self::locate_template( $tpl );
		if ( false === $tpl_path ) {
			return false;
		}

		$view_data = $data;
		include $tpl_path;

		return true;
	}

	/**
	 * Load the template given and return a view renderer.
	 *
	 * @param string $tpl Template name.
	 *
	 * @return \Closure|false
	 */
	public static function load_template( string $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		$tpl_path = self::locate_template( $tpl );
		if ( false === $tpl_path ) {
			return false;
		}

		return static function ( $data ) use ( $tpl_path ) {
			if ( ! is_array( $data ) ) {
				$data = [ 'data' => $data ];
			}

			$view_data = $data;
			include $tpl_path;
		};
	}

	/**
	 * Render a view.
	 *
	 * @param string               $tpl  Template name.
	 * @param array<string, mixed> $data Template data.
	 */
	public static function render( string $tpl, $data = [] ): void {
		$view = self::load_template( $tpl );
		if ( false !== $view ) {
			$view( is_array( $data ) ? $data : [ 'data' => $data ] );
		}
	}

	/**
	 * Transform a date to a given format if possible.
	 *
	 * @param string $date        Date to transform.
	 * @param string $from_format Source format.
	 * @param string $to_format   Target format.
	 */
	public static function format_date( string $date, string $from_format, string $to_format ): string {
		$date = \DateTime::createFromFormat( $from_format, $date );
		if ( false === $date ) {
			return '';
		}

		return self::datetime_wp_date( $to_format, $date );
	}

	/**
	 * Format a date using WordPress i18n helpers.
	 */
	public static function datetime_wp_date( string $format, \DateTime $date ): string {
		return wp_date( $format, $date->format( 'U' ) );
	}
}
