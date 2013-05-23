<?php
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');
?>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">	<?php _e('Title', 'bea-plugin-boilerplate'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
</p>

<!-- TODO -->