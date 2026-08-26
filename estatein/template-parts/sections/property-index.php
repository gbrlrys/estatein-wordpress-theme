<?php
/**
 * Property index: filter bar + results grid + pagination.
 *
 * Shared by the Properties page template and the property archive.
 *
 * Both routes have to render this because a CPT registered with
 * `rewrite => 'properties'` and `has_archive => true` collides with a page of
 * the same slug, and WordPress resolves that collision in favour of the
 * archive. Rather than fight the router, both entry points render this part so
 * the visitor gets the same experience either way.
 *
 * Builds its own query from the query string so results stay bookmarkable and
 * the form works without JavaScript.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

// --- Read filters (all optional, all public). -----------------------------
// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only public filters.
$estatein_search   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
$estatein_type     = isset( $_GET['type'] ) ? sanitize_title( wp_unslash( $_GET['type'] ) ) : '';
$estatein_location = isset( $_GET['location'] ) ? sanitize_title( wp_unslash( $_GET['location'] ) ) : '';
$estatein_max      = isset( $_GET['max'] ) ? absint( $_GET['max'] ) : 0;
// phpcs:enable WordPress.Security.NonceVerification.Recommended

$estatein_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

// Where the filter form posts back to — the current page or the archive.
$estatein_action = is_page() ? get_permalink() : get_post_type_archive_link( 'property' );

// --- Build the query. ------------------------------------------------------
$estatein_args = array(
	'post_type'      => 'property',
	'post_status'    => 'publish',
	'posts_per_page' => 9,
	'paged'          => $estatein_paged,
);

if ( $estatein_search ) {
	$estatein_args['s'] = $estatein_search;
}

$estatein_tax = array();

if ( $estatein_type ) {
	$estatein_tax[] = array( 'taxonomy' => 'property_type', 'field' => 'slug', 'terms' => $estatein_type );
}

if ( $estatein_location ) {
	$estatein_tax[] = array( 'taxonomy' => 'property_location', 'field' => 'slug', 'terms' => $estatein_location );
}

if ( count( $estatein_tax ) > 1 ) {
	$estatein_tax['relation'] = 'AND';
}

if ( $estatein_tax ) {
	$estatein_args['tax_query'] = $estatein_tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}

if ( $estatein_max ) {
	$estatein_args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		array(
			'key'     => 'price',
			'value'   => $estatein_max,
			'type'    => 'NUMERIC',
			'compare' => '<=',
		),
	);
}

$estatein_query    = new WP_Query( $estatein_args );
$estatein_has_real = $estatein_query->have_posts();
$estatein_filtered = ( $estatein_search || $estatein_type || $estatein_location || $estatein_max );

/*
 * With nothing published yet, show the seed set so the page reads as designed.
 * Filters are hidden in that state — there is nothing real to filter.
 */
$estatein_any_published = (bool) wp_count_posts( 'property' )->publish;

$estatein_items = $estatein_has_real
	? array_map( 'estatein_normalise_property', $estatein_query->posts )
	: ( $estatein_any_published ? array() : estatein_default_properties() );

$estatein_types     = get_terms( array( 'taxonomy' => 'property_type', 'hide_empty' => true ) );
$estatein_locations = get_terms( array( 'taxonomy' => 'property_location', 'hide_empty' => true ) );
?>

<section class="section" aria-labelledby="results-heading">
	<div class="container">

		<?php if ( $estatein_any_published ) : ?>
			<!-- Filter bar — only meaningful once real listings exist. -->
			<form class="search-bar" method="get" action="<?php echo esc_url( $estatein_action ); ?>" role="search" data-reveal>
				<div class="field">
					<label class="sr-only" for="filter-q"><?php esc_html_e( 'Search properties', 'estatein' ); ?></label>
					<input
						class="input" type="search" id="filter-q" name="q"
						value="<?php echo esc_attr( $estatein_search ); ?>"
						placeholder="<?php esc_attr_e( 'Search by name or keyword', 'estatein' ); ?>"
					>
				</div>

				<div class="field">
					<label class="sr-only" for="filter-type"><?php esc_html_e( 'Property type', 'estatein' ); ?></label>
					<select class="select" id="filter-type" name="type">
						<option value=""><?php esc_html_e( 'Any type', 'estatein' ); ?></option>
						<?php if ( $estatein_types && ! is_wp_error( $estatein_types ) ) : ?>
							<?php foreach ( $estatein_types as $estatein_term ) : ?>
								<option value="<?php echo esc_attr( $estatein_term->slug ); ?>" <?php selected( $estatein_type, $estatein_term->slug ); ?>>
									<?php echo esc_html( $estatein_term->name ); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="field">
					<label class="sr-only" for="filter-location"><?php esc_html_e( 'Location', 'estatein' ); ?></label>
					<select class="select" id="filter-location" name="location">
						<option value=""><?php esc_html_e( 'Any location', 'estatein' ); ?></option>
						<?php if ( $estatein_locations && ! is_wp_error( $estatein_locations ) ) : ?>
							<?php foreach ( $estatein_locations as $estatein_term ) : ?>
								<option value="<?php echo esc_attr( $estatein_term->slug ); ?>" <?php selected( $estatein_location, $estatein_term->slug ); ?>>
									<?php echo esc_html( $estatein_term->name ); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="field">
					<label class="sr-only" for="filter-max"><?php esc_html_e( 'Maximum price', 'estatein' ); ?></label>
					<select class="select" id="filter-max" name="max">
						<option value=""><?php esc_html_e( 'Any price', 'estatein' ); ?></option>
						<?php foreach ( array( 400000, 600000, 800000, 1000000, 2000000 ) as $estatein_bracket ) : ?>
							<option value="<?php echo esc_attr( $estatein_bracket ); ?>" <?php selected( $estatein_max, $estatein_bracket ); ?>>
								<?php
								printf(
									/* translators: %s: formatted price ceiling. */
									esc_html__( 'Up to %s', 'estatein' ),
									esc_html( estatein_format_price( $estatein_bracket, true ) )
								);
								?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<button type="submit" class="btn btn--primary">
					<?php estatein_icon( 'search', array( 'size' => 18, 'class' => 'btn__icon' ) ); ?>
					<?php esc_html_e( 'Search', 'estatein' ); ?>
				</button>
			</form>
		<?php endif; ?>

		<!-- Results heading -->
		<div class="section-head<?php echo $estatein_any_published ? ' mt-8' : ''; ?>">
			<div class="section-head__text">
				<h2 id="results-heading" style="font-size:1.5rem">
					<?php
					if ( $estatein_has_real ) {
						printf(
							/* translators: %s: number of matching properties. */
							esc_html( _n( '%s property found', '%s properties found', $estatein_query->found_posts, 'estatein' ) ),
							esc_html( number_format_i18n( $estatein_query->found_posts ) )
						);
					} else {
						esc_html_e( 'Discover a world of possibilities', 'estatein' );
					}
					?>
				</h2>
				<?php if ( ! $estatein_any_published ) : ?>
					<p class="lead">
						<?php esc_html_e( 'A preview of the kind of homes we list. Publish a property to replace these with your own.', 'estatein' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<?php if ( $estatein_filtered ) : ?>
				<div class="section-head__action">
					<a class="btn btn--sm" href="<?php echo esc_url( $estatein_action ); ?>">
						<?php esc_html_e( 'Clear filters', 'estatein' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $estatein_items ) : ?>
			<div class="grid grid--3">
				<?php foreach ( $estatein_items as $estatein_i => $estatein_item ) : ?>
					<?php get_template_part( 'template-parts/cards/property-card', null, array( 'item' => $estatein_item, 'index' => $estatein_i ) ); ?>
				<?php endforeach; ?>
			</div>

			<?php
			if ( $estatein_has_real && $estatein_query->max_num_pages > 1 ) {
				echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'estatein' ) . '">';
				echo wp_kses_post(
					paginate_links(
						array(
							'total'     => $estatein_query->max_num_pages,
							'current'   => $estatein_paged,
							'mid_size'  => 1,
							'prev_text' => esc_html__( 'Previous', 'estatein' ),
							'next_text' => esc_html__( 'Next', 'estatein' ),
						)
					)
				);
				echo '</nav>';
			}
			?>
		<?php else : ?>
			<div class="card text-center" style="padding:56px 24px">
				<h3><?php esc_html_e( 'No properties match those filters', 'estatein' ); ?></h3>
				<p class="lead mt-6">
					<?php esc_html_e( 'Try widening your price range or clearing a filter — or tell us what you are after and we will search off-market.', 'estatein' ); ?>
				</p>
				<p class="mt-6">
					<a class="btn btn--primary" href="<?php echo esc_url( estatein_contact_url() ); ?>">
						<?php esc_html_e( 'Tell us what you need', 'estatein' ); ?>
					</a>
				</p>
			</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>
</section>
