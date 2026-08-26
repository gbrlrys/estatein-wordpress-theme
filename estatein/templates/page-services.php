<?php
/**
 * Template Name: Services
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_steps = estatein_default_steps();
?>

<?php
get_template_part(
	'template-parts/sections/page-hero',
	null,
	array(
		'title' => get_the_title(),
		'text'  => __( 'From a first valuation to managing a portfolio of forty units — here is everything we do, and what it costs you.', 'estatein' ),
	)
);
?>

<?php
get_template_part(
	'template-parts/sections/services',
	null,
	array(
		'title' => __( 'Elevate Your Property Experience', 'estatein' ),
		'text'  => __( 'Six services, each run by a specialist team rather than a generalist agent.', 'estatein' ),
		'limit' => 6,
	)
);
?>

<!-- Editor content, if the page has any -->
<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php if ( trim( get_the_content() ) ) : ?>
			<section class="section rule">
				<div class="container">
					<div class="entry-content mx-auto" data-reveal><?php the_content(); ?></div>
				</div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
<?php endif; ?>

<!-- Unlock property value -->
<section class="section rule">
	<div class="container">
		<div class="split split--reverse">
			<div class="split__media" data-reveal>
				<img
					src="<?php echo esc_url( estatein_img( 'interior-01.jpg' ) ); ?>"
					alt="<?php esc_attr_e( 'A bright, well-presented living room', 'estatein' ); ?>"
					width="900" height="675"
					loading="lazy" decoding="async"
				>
			</div>

			<div class="split__body" data-reveal style="--reveal-delay:100ms">
				<?php estatein_icon( 'sparkle', array( 'class' => 'eyebrow-star' ) ); ?>
				<h2><?php esc_html_e( 'Unlock the Value of Your Property', 'estatein' ); ?></h2>
				<p class="lead">
					<?php esc_html_e( 'Most homes sell for less than they should because of decisions made before the listing goes live. Our valuation service tells you which improvements pay for themselves and which ones do not.', 'estatein' ); ?>
				</p>

				<ul class="grid" style="gap:12px">
					<?php
					$estatein_points = array(
						__( 'Free appraisal based on comparable local sales', 'estatein' ),
						__( 'Room-by-room notes on what to fix before listing', 'estatein' ),
						__( 'Professional photography and floor plans included', 'estatein' ),
						__( 'No tie-in period and no withdrawal fee', 'estatein' ),
					);
					foreach ( $estatein_points as $estatein_point ) :
						?>
						<li style="display:flex;gap:12px;align-items:flex-start;color:var(--grey-75)">
							<span style="color:var(--purple-60);flex:none;margin-top:2px"><?php estatein_icon( 'check', array( 'size' => 18 ) ); ?></span>
							<span><?php echo esc_html( $estatein_point ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<p class="mt-8">
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( estatein_contact_url() ); ?>">
						<?php esc_html_e( 'Book a Free Valuation', 'estatein' ); ?>
						<?php estatein_icon( 'arrow-right', array( 'size' => 18, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
</section>

<!-- Process -->
<section class="section rule" aria-labelledby="how-heading">
	<div class="container">
		<?php
		estatein_section_head(
			array(
				'title' => __( 'How We Work', 'estatein' ),
				'text'  => __( 'A predictable four-step process, whichever service you use.', 'estatein' ),
			)
		);
		?>
		<h2 id="how-heading" class="sr-only"><?php esc_html_e( 'How we work', 'estatein' ); ?></h2>

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

<?php get_template_part( 'template-parts/sections/faq', null, array( 'limit' => 4 ) ); ?>

<?php get_template_part( 'template-parts/sections/cta' ); ?>

<?php
get_footer();
