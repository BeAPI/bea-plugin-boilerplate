<?php
namespace BEA\PB;
/**
 * The purpose of the API class is to have the basic reusable methods like :
 *  - Template include
 *  - Template searcher
 *  - Date formatting
 *
 * You can put here all of the tools you use in the project but not
 * limited to an object or a context.
 * It's recommended to use static methods for simple accessing to the methods
 * and stick to the non context methods
 *
 * Class API
 * @package BEA\PB
 */
class Helpers {

	/**
	 * Use the trait
	 */
	use Singleton;

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

		$path = apply_filters( 'BEA/Helpers/locate_template/templates', array( 'views/' . BEA_PB_VIEWS_FOLDER_NAME . '/' . $tpl . '.php' ), $tpl, __NAMESPACE__ );

		// Locate from the theme
		$located = locate_template( $path, false, false );
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
	 * Include the template given
	 *
	 * @param string $tpl : the template name to load
	 *
	 * @return bool
	 */
	public static function include_template( $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		$tpl_path = self::locate_template( $tpl );
		if ( false === $tpl_path ) {
			return false;
		}

		include( $tpl_path );

		return true;
	}

	/**
	 * Load the template given and return a view to be render
	 *
	 * @param string $tpl : the template name to load
	 *
	 * @return \Closure|false
	 */
	public static function load_template( $tpl ) {
		if ( empty( $tpl ) ) {
			return false;
		}

		$tpl_path = self::locate_template( $tpl );
		if ( false === $tpl_path ) {
			return false;
		}

		return function( $data ) use ( $tpl_path ) {
			if ( ! is_array( $data ) ) {
				$data = array( 'data' => $data );
			}
			extract( $data,  EXTR_OVERWRITE );
			include( $tpl_path );
		};
	}

	/**
	 * Render a view
	 *
	 * @param string $tpl : the template's name
	 * @param array  $data : the template's data
	 */
	public static function render( $tpl, $data = array() ) {
		$view = self::load_template( $tpl );
		false !== $view ? $view( $data ) : '';
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
