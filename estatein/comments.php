<?php
/**
 * Comments template.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

// Never expose comments on a password-protected post to people without the password.
if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area mt-8">

	<?php if ( have_comments() ) : ?>
		<h2 class="widget-title" style="font-size:1.5rem;margin-bottom:24px">
			<?php
			$estatein_count = get_comments_number();
			printf(
				/* translators: %s: comment count. */
				esc_html( _n( '%s Comment', '%s Comments', $estatein_count, 'estatein' ) ),
				esc_html( number_format_i18n( $estatein_count ) )
			);
			?>
		</h2>

		<ol class="comment-list" style="display:flex;flex-direction:column;gap:16px">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 44,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => esc_html__( 'Previous', 'estatein' ),
				'next_text' => esc_html__( 'Next', 'estatein' ),
			)
		);
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="text-muted mt-6"><?php esc_html_e( 'Comments are closed.', 'estatein' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'         => 'card',
			'title_reply_before' => '<h2 class="widget-title" style="font-size:1.5rem">',
			'title_reply_after'  => '</h2>',
			'comment_field'      => sprintf(
				'<p class="field"><label class="field__label" for="comment">%1$s</label><textarea class="textarea" id="comment" name="comment" required></textarea></p>',
				esc_html__( 'Comment', 'estatein' )
			),
			'class_submit'       => 'btn btn--primary',
		)
	);
	?>

</section>
