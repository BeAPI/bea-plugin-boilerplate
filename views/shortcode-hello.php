<?php
/**
 * Hello shortcode template.
 *
 * @var array<string, mixed> $view_data
 */
?>
<p>
	<?php
	/* translators: %s: name passed to the shortcode. */
	echo esc_html( sprintf( __( 'Hello, %s!', 'bea-plugin-boilerplate' ), $view_data['name'] ) );
	?>
</p>
