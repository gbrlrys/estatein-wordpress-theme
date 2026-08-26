<?php
/**
 * Closing call-to-action banner.
 *
 * @param array $args {
 *     @type string $title Heading.
 *     @type string $text  Supporting copy.
 * }
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$title = isset( $args['title'] ) ? $args['title'] : __( 'Start your real estate journey today', 'estatein' );
$text  = isset( $args['text'] ) ? $args['text'] : __( 'Your dream property is closer than you think. Tell us what you are looking for and we will do the searching.', 'estatein' );
?>

<section class="section section--tight">
	<div class="container">
		<div class="cta-banner" data-reveal>
			<div class="cta-banner__text">
				<?php estatein_icon( 'sparkle', array( 'class' => 'eyebrow-star' ) ); ?>
				<h2><?php echo esc_html( $title ); ?></h2>
				<p class="lead"><?php echo esc_html( $text ); ?></p>
			</div>

			<div class="cta-banner__actions">
				<a class="btn btn--primary btn--lg" href="<?php echo esc_url( estatein_contact_url() ); ?>">
					<?php esc_html_e( 'Get in Touch', 'estatein' ); ?>
					<?php estatein_icon( 'arrow-right', array( 'size' => 18, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
				</a>
				<a class="btn btn--lg" href="<?php echo esc_url( estatein_properties_url() ); ?>">
					<?php esc_html_e( 'Browse Listings', 'estatein' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
