<?php
/**
 * FAQ accordion.
 *
 * Built on native <button aria-expanded> + a labelled region rather than
 * <details>, so the open/close transition can be animated consistently across
 * browsers. Without JavaScript every panel stays open and readable.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$items = estatein_get_faqs( isset( $args['limit'] ) ? (int) $args['limit'] : 6 );

if ( ! $items ) {
	return;
}

$title = isset( $args['title'] ) ? $args['title'] : __( 'Frequently Asked Questions', 'estatein' );
$text  = isset( $args['text'] ) ? $args['text'] : __( 'Answers to the questions we are asked most. If yours is not here, the team is one message away.', 'estatein' );
?>

<section class="section rule" aria-labelledby="faq-heading">
	<div class="container">

		<?php
		ob_start();
		?>
		<a class="btn" href="<?php echo esc_url( estatein_contact_url() ); ?>">
			<?php esc_html_e( 'Ask a Question', 'estatein' ); ?>
			<?php estatein_icon( 'arrow-right', array( 'size' => 16, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
		</a>
		<?php
		$action = ob_get_clean();

		estatein_section_head(
			array(
				'title'  => $title,
				'text'   => $text,
				'action' => $action,
			)
		);
		?>

		<h2 id="faq-heading" class="sr-only"><?php echo esc_html( $title ); ?></h2>

		<div class="faq-list" data-faq>
			<?php foreach ( $items as $i => $item ) : ?>
				<?php
				$panel_id   = 'faq-panel-' . $i;
				$trigger_id = 'faq-trigger-' . $i;
				?>
				<div class="faq-item" data-reveal style="--reveal-delay: <?php echo esc_attr( min( $i * 60, 360 ) ); ?>ms">
					<h3 style="margin:0">
						<button
							type="button"
							class="faq-item__trigger"
							id="<?php echo esc_attr( $trigger_id ); ?>"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						>
							<span><?php echo esc_html( $item['question'] ); ?></span>
							<span class="faq-item__icon" aria-hidden="true">
								<?php estatein_icon( 'plus', array( 'size' => 16 ) ); ?>
							</span>
						</button>
					</h3>

					<div
						class="faq-item__panel"
						id="<?php echo esc_attr( $panel_id ); ?>"
						role="region"
						aria-labelledby="<?php echo esc_attr( $trigger_id ); ?>"
					>
						<div class="faq-item__panel-inner">
							<p><?php echo esc_html( $item['answer'] ); ?></p>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
