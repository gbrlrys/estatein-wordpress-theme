<?php
/**
 * Home hero.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$stats = estatein_default_stats();
?>

<section class="hero">
	<div class="container">
		<div class="hero__grid">

			<div class="hero__body">
				<h1 class="hero__title" data-reveal>
					<?php
					echo wp_kses_post(
						estatein_option(
							'hero_title',
							__( 'Discover Your Dream Property with Estatein', 'estatein' )
						)
					);
					?>
				</h1>

				<p class="lead hero__text" data-reveal style="--reveal-delay:80ms">
					<?php
					echo esc_html(
						estatein_option(
							'hero_text',
							__( 'Your journey to finding the right home begins here. Explore our curated listings, and let us help you find a place you will not want to leave.', 'estatein' )
						)
					);
					?>
				</p>

				<div class="hero__actions" data-reveal style="--reveal-delay:160ms">
					<a class="btn btn--lg" href="<?php echo esc_url( estatein_contact_url() ); ?>">
						<?php esc_html_e( 'Learn More', 'estatein' ); ?>
					</a>
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( estatein_properties_url() ); ?>">
						<?php esc_html_e( 'Browse Properties', 'estatein' ); ?>
						<?php estatein_icon( 'arrow-right', array( 'size' => 18, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
					</a>
				</div>

				<ul class="stats" data-reveal style="--reveal-delay:240ms">
					<?php foreach ( $stats as $stat ) : ?>
						<li class="stat">
							<p class="stat__value"><?php echo esc_html( $stat['value'] ); ?></p>
							<p class="stat__label"><?php echo esc_html( $stat['label'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="hero__media" data-reveal style="--reveal-delay:120ms">
				<?php
				$hero_id = (int) estatein_option( 'hero_image_id', 0 );

				if ( $hero_id ) {
					echo wp_get_attachment_image(
						$hero_id,
						'estatein-hero',
						false,
						array(
							'alt'           => '',
							'fetchpriority' => 'high',
						)
					);
				} else {
					?>
					<img
						src="<?php echo esc_url( estatein_img( 'hero-home.jpg' ) ); ?>"
						alt="<?php esc_attr_e( 'A modern home at dusk', 'estatein' ); ?>"
						width="1100" height="825"
						fetchpriority="high"
						decoding="async"
					>
					<?php
				}
				?>
			</div>

		</div>
	</div>
</section>
