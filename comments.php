<?php
/**
 * Comments template: comment list, pagination and the comment form.
 *
 * @package j-shop
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			printf(
				/* translators: %s: number of comments. */
				esc_html( _n( '%s comment', '%s comments', get_comments_number(), 'j-shop' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			);
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'j-shop' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
