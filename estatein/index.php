<?php
/**
 * Main template — the fallback WordPress uses when nothing more specific
 * matches. Also serves the blog index.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_title = __( 'Insights & Market Notes', 'estatein' );
$estatein_text  = __( 'Practical writing on buying, selling and holding property — from the people doing it every day.', 'estatein' );

if ( is_home() && get_option( 'page_for_posts' ) ) {
	$estatein_title = get_the_title( get_option( 'page_for_posts' ) );
}
?>

<?php
get_template_part(
	'template-parts/sections/page-hero',
	null,
	array( 'title' => $estatein_title, 'text' => $estatein_text )
);
?>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>

			<div class="grid grid--3">
				<?php
				$estatein_i = 0;
				while ( have_posts() ) :
					the_post();
					?>
					<article
						id="post-<?php the_ID(); ?>"
						<?php post_class( 'card post-card' ); ?>
						data-reveal
						style="--reveal-delay: <?php echo esc_attr( min( $estatein_i * 70, 420 ) ); ?>ms"
					>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'estatein-card', array( 'loading' => $estatein_i < 3 ? 'eager' : 'lazy' ) ); ?>
							</a>
						<?php endif; ?>

						<div class="post-card__meta">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							<span aria-hidden="true">·</span>
							<span><?php echo esc_html( estatein_reading_time() ); ?></span>
						</div>

						<h2 class="card__title" style="margin:0;font-size:1.25rem">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>

						<p style="margin-top:auto;padding-top:16px">
							<a class="btn btn--sm" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'Read article', 'estatein' ); ?>
								<span class="sr-only"><?php the_title(); ?></span>
								<?php estatein_icon( 'arrow-right', array( 'size' => 15, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
							</a>
						</p>
					</article>
					<?php
					$estatein_i++;
				endwhile;
				?>
			</div>

			<?php estatein_pagination(); ?>

		<?php else : ?>

			<div class="card text-center" style="padding:64px 24px">
				<h2><?php esc_html_e( 'Nothing published yet', 'estatein' ); ?></h2>
				<p class="lead mt-6"><?php esc_html_e( 'Articles will appear here as soon as the first one goes live.', 'estatein' ); ?></p>
				<p class="mt-6">
					<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php esc_html_e( 'Back to home', 'estatein' ); ?>
					</a>
				</p>
			</div>

		<?php endif; ?>
	</div>
</section>

<?php get_template_part( 'template-parts/sections/cta' ); ?>

<?php
get_footer();
