<?php
/**
 * Testimonials carousel.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$items = estatein_get_testimonials( isset( $args['limit'] ) ? (int) $args['limit'] : 6 );

if ( ! $items ) {
	return;
}
?>

<section class="section rule" aria-labelledby="testimonials-heading">
	<div class="container">

		<?php
		estatein_section_head(
			array(
				'title' => __( 'What Our Clients Say', 'estatein' ),
				'text'  => __( 'Read the experiences of people who found their home through Estatein. Their words tell our story better than we can.', 'estatein' ),
			)
		);
		?>

		<h2 id="testimonials-heading" class="sr-only"><?php esc_html_e( 'Client testimonials', 'estatein' ); ?></h2>

		<div class="track" id="testimonial-track" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Client testimonials', 'estatein' ); ?>">
			<?php foreach ( $items as $i => $item ) : ?>
				<?php get_template_part( 'template-parts/cards/testimonial-card', null, array( 'item' => $item, 'index' => $i ) ); ?>
			<?php endforeach; ?>
		</div>

		<div class="mt-6" style="display:flex;justify-content:flex-end">
			<?php estatein_carousel_nav( 'testimonial-track', count( $items ) ); ?>
		</div>

	</div>
</section>
