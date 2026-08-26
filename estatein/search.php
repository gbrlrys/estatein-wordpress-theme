<?php
/**
 * Search results.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $wp_query;
?>

<section class="page-hero">
	<div class="container">
		<div class="page-hero__inner">
			<?php estatein_breadcrumbs(); ?>
			<h1>
				<?php
				printf(
					/* translators: %s: search term. */
					esc_html__( 'Results for “%s”', 'estatein' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>
			<p class="lead">
				<?php
				printf(
					/* translators: %s: number of results. */
					esc_html( _n( '%s match found.', '%s matches found.', (int) $wp_query->found_posts, 'estatein' ) ),
					esc_html( number_format_i18n( (int) $wp_query->found_posts ) )
				);
				?>
			</p>
			<div class="mt-6" style="max-width:520px"><?php get_search_form(); ?></div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>

			<div class="grid grid--3">
				<?php
				$estatein_i = 0;
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'card post-card' ); ?> data-reveal style="--reveal-delay: <?php echo esc_attr( min( $estatein_i * 70, 420 ) ); ?>ms">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'estatein-card' ); ?>
							</a>
						<?php endif; ?>

						<p class="post-card__meta"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></p>

						<h2 class="card__title" style="margin:0;font-size:1.25rem">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
					</article>
					<?php
					$estatein_i++;
				endwhile;
				?>
			</div>

			<?php estatein_pagination(); ?>

		<?php else : ?>

			<div class="card text-center" style="padding:64px 24px">
				<h2><?php esc_html_e( 'Nothing matched that search', 'estatein' ); ?></h2>
				<p class="lead mt-6"><?php esc_html_e( 'Try a different term, or browse all properties.', 'estatein' ); ?></p>
				<p class="mt-6">
					<a class="btn btn--primary" href="<?php echo esc_url( estatein_properties_url() ); ?>">
						<?php esc_html_e( 'Browse properties', 'estatein' ); ?>
					</a>
				</p>
			</div>

		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
