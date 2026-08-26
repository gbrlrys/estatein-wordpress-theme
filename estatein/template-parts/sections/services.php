<?php
/**
 * Services grid.
 *
 * @param array $args {
 *     @type string $title Section title.
 *     @type string $text  Supporting copy.
 *     @type int    $limit How many services to show.
 * }
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$limit = isset( $args['limit'] ) ? (int) $args['limit'] : 6;
$items = estatein_get_services( $limit );

if ( ! $items ) {
	return;
}

$title = isset( $args['title'] ) ? $args['title'] : __( 'Our Services', 'estatein' );
$text  = isset( $args['text'] ) ? $args['text'] : __( 'Whether you are buying your first home or managing a portfolio, there is a team here for that part of the job.', 'estatein' );
?>

<section class="section rule" aria-labelledby="services-heading">
	<div class="container">
		<?php
		estatein_section_head(
			array(
				'title' => $title,
				'text'  => $text,
			)
		);
		?>

		<h2 id="services-heading" class="sr-only"><?php echo esc_html( $title ); ?></h2>

		<div class="grid grid--3">
			<?php foreach ( $items as $i => $item ) : ?>
				<article
					class="card icon-card"
					data-reveal
					style="--reveal-delay: <?php echo esc_attr( min( $i * 70, 420 ) ); ?>ms"
				>
					<span class="icon-card__icon" aria-hidden="true">
						<?php estatein_icon( ! empty( $item['icon'] ) ? $item['icon'] : 'building' ); ?>
					</span>

					<h3 class="card__title" style="margin:0"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="card__text"><?php echo esc_html( wp_strip_all_tags( $item['text'] ) ); ?></p>

					<?php if ( ! empty( $item['url'] ) ) : ?>
						<p style="margin-top:auto;padding-top:16px">
							<a class="btn btn--sm" href="<?php echo esc_url( $item['url'] ); ?>">
								<?php esc_html_e( 'Learn More', 'estatein' ); ?>
								<span class="sr-only"><?php echo esc_html( $item['title'] ); ?></span>
								<?php estatein_icon( 'arrow-right', array( 'size' => 15, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
							</a>
						</p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
