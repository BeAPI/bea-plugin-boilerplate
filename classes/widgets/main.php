<?php

namespace BEA\PB\Widgets;

use BEA\PB\Helpers;

/**
 * Example of a Widget class well done
 *
 * Class Main
 * @package BEA\PB\Widgets
 */
class Main extends \WP_Widget {

	/**
	 * The args used for the widget
	 *
	 * @var array
	 */
	private $_args;

	/**
	 * The current widget instance
	 *
	 * @var
	 */
	private $_instance;

	public function __construct() {
		parent::__construct( 'widget-bea-pb', __( 'Widget title', 'bea-plugin-boilerplate' ),
			array(
				'classname'   => 'widget-bea-pb',
				'description' => __( 'Widget description', 'bea-plugin-boilerplate' ),
			)
		);
	}

	/**
	 * Display the widget instance
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return bool
	 */
	public function widget( $args, $instance ) {

		// Make the args
		$this->_instance = $instance;
		$this->_args     = $args;

		// unset vars
		unset( $instance );
		unset( $args );

		// Display header
		$this->the_header();

		include( Helpers::locate_template( 'bea-plugin-boilerplate-widget' ) );

		// Display footer
		$this->the_footer();

		return true;
	}

	/**
	 * Method for updating the form data
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Form display
	 *
	 * @param array $instance
	 *
	 * @return bool
	 */
	public function form( $instance ) {
		// TODO
		$defaults = array( 'title' => __( 'Sample title', 'bea-plugin-boilerplate' ) );

		$instance = wp_parse_args( (array) $instance, $defaults );

		include( BEA_PB_DIR . 'views/admin/widget.php' );

		return true;
	}

	/**
	 * Display the before widget data
	 */
	public function the_header() {
		echo isset( $this->_args['before_widget'] ) ? $this->_args['before_widget'] : '';
	}

	/**
	 * Display the after widget data
	 */
	public function the_footer() {
		echo isset( $this->_args['after_widget'] ) ? $this->_args['after_widget'] : '';
	}

	/**
	 * Display the title of the instance
	 */
	public function the_title() {
		if ( ! isset( $this->_instance['title'] ) || empty( $this->_instance['title'] ) ) {
			return;
		}

		echo $this->_args['before_title'] . $this->_instance['title'] . $this->_args['after_title'];
	}
}
