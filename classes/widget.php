<?php
class BEA_PB_Widget extends WP_Widget {
	
	public function __construct() {
		parent::__construct( 'widget-bea-pb', __('Widget title', 'bea-plugin-boilerplate'),
			array( 'classname' => 'widget-bea-pb', 'description' => __('Widget description', 'bea-plugin-boilerplate' ) )
		);
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		
		// Get data from instance
		$title = $instance['title'];
		// TODO
		
		echo $before_widget;
		
		// Display the widget, allow take template from child or parent theme
		if ( is_file(STYLESHEETPATH .'/widget-views/bea-plugin-boilerplate-widget.php') ) { // Use custom template from child theme
			include( STYLESHEETPATH .'/widget-views/bea-plugin-boilerplate-widget.php' );
		} elseif ( is_file(TEMPLATEPATH .'/widget-views/bea-plugin-boilerplate-widget.php' ) ) { // Use custom template from parent theme
			include( TEMPLATEPATH .'/widget-views/bea-plugin-boilerplate-widget.php' );
		} else { // Use builtin temlate
			include( BEA_PB_DIR . 'views/client/widget.php' );
		}
		
		echo $after_widget;
		
		return true;
	}
	

	
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 		= stripslashes($new_instance['title']);
		// TODO
		
		return $instance;
	}
	
	public function form( $instance ) {
		// TODO
		$defaults = array( 'title' => __('Sample title', 'bea-plugin-boilerplate') );
		
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		include( BEA_PB_DIR . 'views/admin/widget.php' );
	}
}