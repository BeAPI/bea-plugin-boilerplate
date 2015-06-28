<?php
namespace BEA_PB;
Class API {
	/**
	 * Locate template in the theme or plugin if needed
	 *
	 * @param string $tpl : the tpl name, add automatically .php at the end of the file
	 *
	 * @return bool|string
	 */
	public static function locate_template( $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		// Locate from the theme
		$located = locate_template( array( '/views/' . BEA_PB_VIEWS_FOLDER_NAME . '/' . $tpl . '.php' ), false, false );
		if ( ! empty( $located ) ) {
			return $located;
		}

		// Locate on the files
		if ( is_file( BEA_PB_DIR . 'views/' . $tpl . '.php' ) ) {// Use builtin template
			return ( BEA_PB_DIR . 'views/' . $tpl . '.php' );
		}

		return false;
	}

	/**
	 * Load the template given
	 *
	 * @param string $tpl : the template name to load
	 *
	 * @return bool
	 */
	public static function load_template( $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		$tpl_path = self::locate_template( $tpl );
		if ( $tpl_path === false ) {
			return false;
		}

		include( $tpl_path );

		return true;
	}

	/**
	 * Transform a date to a given format if possible
	 *
	 * @param string $date : date to transform
	 * @param $from_format : the from date format
	 * @param $to_format : the format to transform in
	 *
	 * @return string the date formatted
	 */
	public static function format_date( $date, $from_format, $to_format ) {
		$date = \DateTime::createFromFormat( $from_format, $date );
		if ( false == $date ) {
			return '';
		}

		return self::datetime_i18n( $to_format, $date );
	}

	/**
	 * Format on i18n
	 *
	 * @param string $format
	 * @param \DateTime $date
	 *
	 * @return string
	 */
	public static function datetime_i18n( $format, \DateTime $date ) {
		return date_i18n( $format, $date->format( 'U' ) );
	}

}