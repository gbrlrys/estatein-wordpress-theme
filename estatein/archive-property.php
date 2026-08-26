<?php
/**
 * Property archive and taxonomy listings.
 *
 * The post type archive shares its slug with the Properties page, and
 * WordPress resolves that in favour of the archive — so this template renders
 * the same filterable index the page template does. Taxonomy archives keep the
 * simpler pre-filtered grid, since the term is already the filter.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_is_tax = is_tax();
$estatein_title  = $estatein_is_tax ? single_term_title( '', false ) : post_type_archive_title( '', false );
$estatein_text   = __( 'Every listing has been visited and verified by an Estatein agent.', 'estatein' );

if ( $estatein_is_tax ) {
	$estatein_desc = wp_strip_all_tags( term_description() );
	if ( $estatein_desc ) {
		$estatein_text = $estatein_desc;
	}
}

get_template_part(
	'template-parts/sections/page-hero',
	null,
	array( 'title' => $estatein_title, 'text' => $estatein_text )
);

if ( ! $estatein_is_tax ) :

	// Post type archive: full filterable index, shared with the page template.
	get_template_part( 'template-parts/sections/property-index' );

else :
	?>

	<section class="section">
		<div class="container">
			<?php if ( have_posts() ) : ?>

				<div class="grid grid--3">
					<?php
					$estatein_i = 0;
					while ( have_posts() ) :
						the_post();
						get_template_part(
							'template-parts/cards/property-card',
							null,
							array( 'item' => estatein_normalise_property( get_post() ), 'index' => $estatein_i )
						);
						$estatein_i++;
					endwhile;
					?>
				</div>

				<?php estatein_pagination(); ?>

			<?php else : ?>

				<div class="card text-center" style="padding:64px 24px">
					<h2><?php esc_html_e( 'No listings here yet', 'estatein' ); ?></h2>
					<p class="lead mt-6"><?php esc_html_e( 'Tell us what you are after and we will search off-market on your behalf.', 'estatein' ); ?></p>
					<p class="mt-6">
						<a class="btn btn--primary" href="<?php echo esc_url( estatein_contact_url() ); ?>">
							<?php esc_html_e( 'Register your requirements', 'estatein' ); ?>
						</a>
					</p>
				</div>

			<?php endif; ?>
		</div>
	</section>

	<?php
endif;

get_template_part( 'template-parts/sections/cta' );

get_footer();
