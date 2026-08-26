<?php
/**
 * Single blog post.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<section class="page-hero">
		<div class="container">
			<div class="page-hero__inner">
				<?php estatein_breadcrumbs(); ?>
				<h1 data-reveal><?php the_title(); ?></h1>

				<div class="post-card__meta mt-6" style="font-size:.9375rem">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span aria-hidden="true">·</span>
					<span><?php echo esc_html( estatein_reading_time() ); ?></span>
					<span aria-hidden="true">·</span>
					<span><?php echo esc_html( get_the_author() ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<article class="section" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="container">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure style="border-radius:var(--r-lg);overflow:hidden;border:1px solid var(--border);margin:0 0 48px">
					<?php the_post_thumbnail( 'estatein-wide', array( 'fetchpriority' => 'high' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="entry-content mx-auto">
				<?php the_content(); ?>
			</div>

			<?php
			$estatein_tags = get_the_tag_list( '', '', '' );
			if ( $estatein_tags ) :
				?>
				<div class="mx-auto measure mt-8" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
					<span class="text-muted" style="font-size:.875rem"><?php esc_html_e( 'Tagged:', 'estatein' ); ?></span>
					<?php echo wp_kses_post( get_the_tag_list( '<span class="pill">', '</span><span class="pill">', '</span>' ) ); ?>
				</div>
			<?php endif; ?>

			<nav class="mx-auto measure mt-8" aria-label="<?php esc_attr_e( 'Post navigation', 'estatein' ); ?>" style="display:flex;gap:16px;justify-content:space-between;flex-wrap:wrap">
				<?php
				$estatein_prev = get_previous_post();
				$estatein_next = get_next_post();

				if ( $estatein_prev ) {
					printf(
						'<a class="btn" href="%s">%s<span>%s</span></a>',
						esc_url( get_permalink( $estatein_prev ) ),
						estatein_get_icon( 'arrow-left', array( 'size' => 16, 'class' => 'btn__icon' ) ),
						esc_html__( 'Previous', 'estatein' )
					);
				}

				if ( $estatein_next ) {
					printf(
						'<a class="btn" href="%s"><span>%s</span>%s</a>',
						esc_url( get_permalink( $estatein_next ) ),
						esc_html__( 'Next', 'estatein' ),
						estatein_get_icon( 'arrow-right', array( 'size' => 16, 'class' => 'btn__icon btn__icon--arrow' ) )
					);
				}
				?>
			</nav>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="section section--flush-top"><div class="container"><div class="mx-auto measure">';
		comments_template();
		echo '</div></div></div>';
	}
	?>

	<?php
endwhile;

get_template_part( 'template-parts/sections/cta' );

get_footer();
