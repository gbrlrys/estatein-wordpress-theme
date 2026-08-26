<?php
/**
 * Single property listing.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$estatein_price   = estatein_field( 'price' );
	$estatein_address = estatein_field( 'address' );
	$estatein_gallery = estatein_field( 'gallery', null, array() );

	// Specs shown in the grid. Empty values are skipped, so a sparse listing
	// still looks deliberate rather than broken.
	$estatein_specs = array(
		array( 'label' => __( 'Bedrooms', 'estatein' ),   'value' => estatein_field( 'bedrooms' ) ),
		array( 'label' => __( 'Bathrooms', 'estatein' ),  'value' => estatein_field( 'bathrooms' ) ),
		array( 'label' => __( 'Area', 'estatein' ),       'value' => estatein_field( 'area' ) ? number_format_i18n( (float) estatein_field( 'area' ) ) . ' ' . __( 'sq ft', 'estatein' ) : '' ),
		array( 'label' => __( 'Parking', 'estatein' ),    'value' => estatein_field( 'garages' ) ),
		array( 'label' => __( 'Year built', 'estatein' ), 'value' => estatein_field( 'year_built' ) ),
	);
	?>

	<section class="page-hero">
		<div class="container">
			<div class="page-hero__inner" style="max-width:none">
				<?php estatein_breadcrumbs(); ?>
				<h1 data-reveal><?php the_title(); ?></h1>

				<?php if ( $estatein_address ) : ?>
					<p class="lead" style="display:flex;gap:8px;align-items:center">
						<?php estatein_icon( 'map-pin', array( 'size' => 18 ) ); ?>
						<span><?php echo esc_html( $estatein_address ); ?></span>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<article class="section" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="container">

			<!-- Gallery -->
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="property-single__gallery" data-reveal>
					<figure>
						<?php the_post_thumbnail( 'estatein-wide', array( 'fetchpriority' => 'high' ) ); ?>
					</figure>

					<?php if ( is_array( $estatein_gallery ) && count( $estatein_gallery ) >= 2 ) : ?>
						<div class="property-single__gallery-side">
							<?php foreach ( array_slice( $estatein_gallery, 0, 2 ) as $estatein_image_id ) : ?>
								<figure><?php echo wp_get_attachment_image( $estatein_image_id, 'estatein-card', false, array( 'alt' => '' ) ); ?></figure>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="property-layout">

				<!-- Description + specs -->
				<div>
					<?php if ( array_filter( wp_list_pluck( $estatein_specs, 'value' ) ) ) : ?>
						<div class="spec-grid mb-6" data-reveal>
							<?php foreach ( $estatein_specs as $estatein_spec ) : ?>
								<?php if ( ! $estatein_spec['value'] ) { continue; } ?>
								<div class="spec">
									<p class="spec__label"><?php echo esc_html( $estatein_spec['label'] ); ?></p>
									<p class="spec__value"><?php echo esc_html( $estatein_spec['value'] ); ?></p>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<div class="entry-content mt-8">
						<h2><?php esc_html_e( 'About this property', 'estatein' ); ?></h2>
						<?php the_content(); ?>
					</div>

					<?php
					// Amenities repeater (ACF) — silently skipped when absent.
					$estatein_amenities = estatein_field( 'amenities', null, array() );
					if ( is_array( $estatein_amenities ) && $estatein_amenities ) :
						?>
						<div class="mt-8">
							<h2 style="font-size:1.5rem;margin-bottom:20px"><?php esc_html_e( 'Amenities', 'estatein' ); ?></h2>
							<ul class="grid grid--2" style="gap:12px">
								<?php foreach ( $estatein_amenities as $estatein_amenity ) : ?>
									<?php $estatein_label = is_array( $estatein_amenity ) ? ( isset( $estatein_amenity['label'] ) ? $estatein_amenity['label'] : '' ) : $estatein_amenity; ?>
									<?php if ( ! $estatein_label ) { continue; } ?>
									<li style="display:flex;gap:12px;align-items:center;color:var(--grey-75)">
										<span class="text-accent" style="flex:none"><?php estatein_icon( 'check', array( 'size' => 18 ) ); ?></span>
										<span><?php echo esc_html( $estatein_label ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<?php
					$estatein_terms = get_the_terms( get_the_ID(), 'property_type' );
					if ( $estatein_terms && ! is_wp_error( $estatein_terms ) ) :
						?>
						<div class="mt-8" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
							<span class="text-muted" style="font-size:.875rem"><?php esc_html_e( 'Type:', 'estatein' ); ?></span>
							<?php foreach ( $estatein_terms as $estatein_term ) : ?>
								<a class="pill" href="<?php echo esc_url( get_term_link( $estatein_term ) ); ?>">
									<?php echo esc_html( $estatein_term->name ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Sticky enquiry panel -->
				<aside class="property-aside" data-reveal style="--reveal-delay:100ms">
					<div>
						<p class="property-card__price-label"><?php esc_html_e( 'Asking price', 'estatein' ); ?></p>
						<p style="font-size:2rem;font-weight:700;letter-spacing:-.02em;line-height:1.1">
							<?php echo esc_html( estatein_format_price( $estatein_price ) ); ?>
						</p>
					</div>

					<hr>

					<p class="card__text">
						<?php esc_html_e( 'Book a viewing or ask a question — an agent who knows this property will reply personally.', 'estatein' ); ?>
					</p>

					<a class="btn btn--primary btn--block" href="<?php echo esc_url( estatein_contact_url() ); ?>">
						<?php esc_html_e( 'Book a Viewing', 'estatein' ); ?>
						<?php estatein_icon( 'arrow-right', array( 'size' => 18, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
					</a>

					<?php $estatein_phone = estatein_option( 'contact_phone', '+1 (555) 000-0000' ); ?>
					<a class="btn btn--block" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $estatein_phone ) ); ?>">
						<?php estatein_icon( 'phone', array( 'size' => 17, 'class' => 'btn__icon' ) ); ?>
						<?php echo esc_html( $estatein_phone ); ?>
					</a>
				</aside>

			</div>
		</div>
	</article>

	<!-- Related listings -->
	<?php
	$estatein_related = estatein_get_properties(
		3,
		array( 'post__not_in' => array( get_the_ID() ) )
	);

	if ( $estatein_related ) :
		?>
		<section class="section rule" aria-labelledby="related-heading">
			<div class="container">
				<?php
				estatein_section_head(
					array(
						'title' => __( 'You Might Also Like', 'estatein' ),
						'text'  => __( 'Similar listings currently available.', 'estatein' ),
					)
				);
				?>
				<h2 id="related-heading" class="sr-only"><?php esc_html_e( 'Related properties', 'estatein' ); ?></h2>

				<div class="grid grid--3">
					<?php foreach ( $estatein_related as $estatein_i => $estatein_item ) : ?>
						<?php get_template_part( 'template-parts/cards/property-card', null, array( 'item' => $estatein_item, 'index' => $estatein_i ) ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

get_template_part( 'template-parts/sections/cta' );

get_footer();
