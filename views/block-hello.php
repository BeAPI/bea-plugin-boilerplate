<?php
/**
 * Hello block template.
 *
 * @var array<string, mixed> $view_data
 */
?>
<div id="<?php echo esc_attr( $view_data['block_id'] ); ?>" class="<?php echo esc_attr( $view_data['block_classname'] ); ?>">
	<p><?php echo esc_html( $view_data['message'] ); ?></p>
</div>
