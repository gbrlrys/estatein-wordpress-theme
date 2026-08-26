<?php
/**
 * Template Name: About Us
 *
 * Our journey, values, the client process, the team, and closing CTA.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_values = estatein_default_values();
$estatein_steps  = estatein_default_steps();
$estatein_stats  = estatein_default_stats();
$estatein_team   = estatein_get_team( 6 );
?>

<?php
get_template_part(
	'template-parts/sections/page-hero',
	null,
	array(
		'title' => get_the_title(),
		'text'  => __( 'Founded on the belief that buying a home should feel clear rather than confusing, we have spent sixteen years learning how to do this properly.', 'estatein' ),
	)
);
?>

<!-- Our journey -->
<section class="section">
	<div class="container">
		<div class="split">
			<div class="split__media" data-reveal>
				<img
					src="<?php echo esc_url( estatein_img( 'about-team.jpg' ) ); ?>"
					alt="<?php esc_attr_e( 'The interior of a home we recently sold', 'estatein' ); ?>"
					width="1100" height="825"
					loading="lazy" decoding="async"
				>
			</div>

			<div class="split__body" data-reveal style="--reveal-delay:100ms">
				<?php estatein_icon( 'sparkle', array( 'class' => 'eyebrow-star' ) ); ?>
				<h2><?php esc_html_e( 'Our Journey', 'estatein' ); ?></h2>

				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php if ( get_the_content() ) : ?>
							<div class="entry-content"><?php the_content(); ?></div>
						<?php else : ?>
							<p class="lead">
								<?php esc_html_e( 'Estatein began in 2008 with two agents, one office and a stubborn conviction that people deserved straight answers about the biggest purchase of their lives. That has not changed — we have just grown enough to help more of them.', 'estatein' ); ?>
							</p>
							<p class="lead">
								<?php esc_html_e( 'Today we operate across six cities with a team of thirty, and we still turn down listings we do not believe in.', 'estatein' ); ?>
							</p>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>

				<ul class="stats mt-6">
					<?php foreach ( $estatein_stats as $estatein_stat ) : ?>
						<li class="stat">
							<p class="stat__value"><?php echo esc_html( $estatein_stat['value'] ); ?></p>
							<p class="stat__label"><?php echo esc_html( $estatein_stat['label'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- Values -->
<section class="section rule" aria-labelledby="values-heading">
	<div class="container">
		<?php
		estatein_section_head(
			array(
				'title' => __( 'Our Values', 'estatein' ),
				'text'  => __( 'Four principles that decide how we behave when a deal gets complicated.', 'estatein' ),
			)
		);
		?>
		<h2 id="values-heading" class="sr-only"><?php esc_html_e( 'Our values', 'estatein' ); ?></h2>

		<div class="grid grid--4">
			<?php foreach ( $estatein_values as $estatein_i => $estatein_value ) : ?>
				<article class="card icon-card" data-reveal style="--reveal-delay: <?php echo esc_attr( min( $estatein_i * 70, 420 ) ); ?>ms">
					<span class="icon-card__icon" aria-hidden="true"><?php estatein_icon( $estatein_value['icon'] ); ?></span>
					<h3 class="card__title" style="margin:0"><?php echo esc_html( $estatein_value['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $estatein_value['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- Process -->
<section class="section rule" aria-labelledby="process-heading">
	<div class="container">
		<?php
		estatein_section_head(
			array(
				'title' => __( 'Navigating the Estatein Experience', 'estatein' ),
				'text'  => __( 'What actually happens between your first message and the day you get the keys.', 'estatein' ),
			)
		);
		?>
		<h2 id="process-heading" class="sr-only"><?php esc_html_e( 'Our process', 'estatein' ); ?></h2>

		<ol class="grid grid--4">
			<?php foreach ( $estatein_steps as $estatein_i => $estatein_step ) : ?>
				<li class="card step" data-reveal style="--reveal-delay: <?php echo esc_attr( min( $estatein_i * 70, 420 ) ); ?>ms">
					<span class="step__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $estatein_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3 class="card__title" style="margin:0"><?php echo esc_html( $estatein_step['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( $estatein_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<!-- Team -->
<?php if ( $estatein_team ) : ?>
	<section class="section rule" aria-labelledby="team-heading">
		<div class="container">
			<?php
			estatein_section_head(
				array(
					'title' => __( 'Meet the Team', 'estatein' ),
					'text'  => __( 'The people you will actually be dealing with — not a call centre.', 'estatein' ),
				)
			);
			?>
			<h2 id="team-heading" class="sr-only"><?php esc_html_e( 'Meet the team', 'estatein' ); ?></h2>

			<div class="grid grid--3">
				<?php foreach ( $estatein_team as $estatein_i => $estatein_member ) : ?>
					<?php get_template_part( 'template-parts/cards/team-card', null, array( 'item' => $estatein_member, 'index' => $estatein_i ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/sections/testimonials' ); ?>

<?php get_template_part( 'template-parts/sections/cta' ); ?>

<?php
get_footer();
