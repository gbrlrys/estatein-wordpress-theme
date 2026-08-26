<?php
/**
 * Default page template.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<?php get_template_part( 'template-parts/sections/page-hero', null, array( 'title' => get_the_title() ) ); ?>

	<article class="section" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="container">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="mb-6" style="border-radius:var(--r-lg);overflow:hidden;border:1px solid var(--border);margin:0 0 40px">
					<?php the_post_thumbnail( 'estatein-wide' ); ?>
				</figure>
			<?php endif; ?>

			<div class="entry-content mx-auto" data-reveal>
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<nav class="pagination" aria-label="' . esc_attr__( 'Page sections', 'estatein' ) . '">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="section section--flush-top"><div class="container">';
		comments_template();
		echo '</div></div>';
	}
	?>

	<?php
endwhile;

get_template_part( 'template-parts/sections/cta' );

get_footer();
