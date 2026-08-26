<?php
/**
 * "Our valued clients" logo wall.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$clients = estatein_default_clients();

if ( ! $clients ) {
	return;
}
?>

<section class="section section--tight rule" aria-labelledby="clients-heading">
	<div class="container">
		<?php
		estatein_section_head(
			array(
				'title' => __( 'Our Valued Clients', 'estatein' ),
				'text'  => __( 'Developers, funds and private owners who trust us with their portfolios.', 'estatein' ),
			)
		);
		?>

		<h2 id="clients-heading" class="sr-only"><?php esc_html_e( 'Our valued clients', 'estatein' ); ?></h2>

		<ul class="logo-wall" data-reveal>
			<?php foreach ( $clients as $client ) : ?>
				<li class="logo-wall__item"><?php echo esc_html( $client ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
