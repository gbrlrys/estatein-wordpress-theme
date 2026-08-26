<?php
/**
 * Testimonial card.
 *
 * @param array $args {
 *     @type array $item  Normalised testimonial.
 *     @type int   $index Position, used to stagger the reveal.
 * }
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$item = isset( $args['item'] ) ? $args['item'] : array();

if ( empty( $item['quote'] ) ) {
	return;
}

$index = isset( $args['index'] ) ? (int) $args['index'] : 0;
?>

<figure
	class="card testimonial"
	style="margin:0; --reveal-delay: <?php echo esc_attr( min( $index * 70, 420 ) ); ?>ms"
	data-reveal
>
	<?php estatein_rating( isset( $item['rating'] ) ? $item['rating'] : 5 ); ?>

	<?php if ( ! empty( $item['headline'] ) ) : ?>
		<h3 class="card__title" style="margin:0"><?php echo esc_html( $item['headline'] ); ?></h3>
	<?php endif; ?>

	<blockquote class="testimonial__quote" style="margin:0">
		&ldquo;<?php echo esc_html( $item['quote'] ); ?>&rdquo;
	</blockquote>

	<figcaption class="testimonial__person">
		<?php if ( ! empty( $item['avatar'] ) ) : ?>
			<img
				class="testimonial__avatar"
				src="<?php echo esc_url( $item['avatar'] ); ?>"
				alt=""
				width="44" height="44"
				loading="lazy" decoding="async"
			>
		<?php endif; ?>
		<span>
			<span class="testimonial__name"><?php echo esc_html( $item['name'] ); ?></span>
			<?php if ( ! empty( $item['location'] ) ) : ?>
				<span class="testimonial__meta" style="display:block"><?php echo esc_html( $item['location'] ); ?></span>
			<?php endif; ?>
		</span>
	</figcaption>
</figure>
