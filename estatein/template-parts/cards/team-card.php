<?php
/**
 * Team member card.
 *
 * @param array $args {
 *     @type array $item  Normalised team member.
 *     @type int   $index Position, used to stagger the reveal.
 * }
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$item = isset( $args['item'] ) ? $args['item'] : array();

if ( empty( $item['name'] ) ) {
	return;
}

$index = isset( $args['index'] ) ? (int) $args['index'] : 0;
?>

<article
	class="card team-card"
	data-reveal
	style="--reveal-delay: <?php echo esc_attr( min( $index * 70, 420 ) ); ?>ms"
>
	<?php if ( ! empty( $item['photo'] ) ) : ?>
		<img
			class="team-card__photo"
			src="<?php echo esc_url( $item['photo'] ); ?>"
			alt="<?php echo esc_attr( $item['name'] ); ?>"
			width="240" height="240"
			loading="lazy" decoding="async"
		>
	<?php endif; ?>

	<div>
		<h3 class="card__title" style="margin:0 0 2px"><?php echo esc_html( $item['name'] ); ?></h3>
		<?php if ( ! empty( $item['role'] ) ) : ?>
			<p class="team-card__role"><?php echo esc_html( $item['role'] ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $item['linkedin'] ) ) : ?>
		<ul class="socials" style="margin-top:0">
			<li>
				<a href="<?php echo esc_url( $item['linkedin'] ); ?>" rel="noopener noreferrer" target="_blank">
					<?php estatein_icon( 'linkedin' ); ?>
					<span class="sr-only">
						<?php
						printf(
							/* translators: %s: team member name. */
							esc_html__( '%s on LinkedIn', 'estatein' ),
							esc_html( $item['name'] )
						);
						?>
					</span>
				</a>
			</li>
		</ul>
	<?php endif; ?>
</article>
