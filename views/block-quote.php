<?php
/**
 * Quote block template.
 *
 * @var array<string, mixed> $view_data
 */
?>
<blockquote class="bea-pb-quote">
	<p><?php echo esc_html( $view_data['content'] ); ?></p>
	<?php if ( ! empty( $view_data['author'] ) ) : ?>
		<cite><?php echo esc_html( $view_data['author'] ); ?></cite>
	<?php endif; ?>
</blockquote>
