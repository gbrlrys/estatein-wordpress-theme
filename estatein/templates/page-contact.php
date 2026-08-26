<?php
/**
 * Template Name: Contact Us
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

$estatein_phone   = estatein_option( 'contact_phone', '+1 (555) 000-0000' );
$estatein_email   = estatein_option( 'contact_email', get_option( 'admin_email' ) );
$estatein_address = estatein_option( 'contact_address', '2158 Mount Tabor, Los Angeles, CA 90012' );

$estatein_channels = array(
	array(
		'icon'  => 'phone',
		'label' => __( 'Call us', 'estatein' ),
		'value' => $estatein_phone,
		'href'  => 'tel:' . preg_replace( '/[^0-9+]/', '', $estatein_phone ),
		'note'  => __( 'Mon–Fri, 9am – 6pm', 'estatein' ),
	),
	array(
		'icon'  => 'mail',
		'label' => __( 'Email us', 'estatein' ),
		'value' => $estatein_email,
		'href'  => 'mailto:' . $estatein_email,
		'note'  => __( 'We reply within one business day', 'estatein' ),
	),
	array(
		'icon'  => 'map-pin',
		'label' => __( 'Visit us', 'estatein' ),
		'value' => $estatein_address,
		'href'  => '',
		'note'  => __( 'Appointments recommended', 'estatein' ),
	),
);
?>

<?php
get_template_part(
	'template-parts/sections/page-hero',
	null,
	array(
		'title' => get_the_title(),
		'text'  => __( 'Tell us what you are looking for, or what you would like to sell. A real person reads every message.', 'estatein' ),
	)
);
?>

<!-- Contact channels -->
<section class="section section--tight">
	<div class="container">
		<div class="grid grid--3">
			<?php foreach ( $estatein_channels as $estatein_i => $estatein_channel ) : ?>
				<article class="card icon-card" data-reveal style="--reveal-delay: <?php echo esc_attr( $estatein_i * 80 ); ?>ms">
					<span class="icon-card__icon" aria-hidden="true"><?php estatein_icon( $estatein_channel['icon'] ); ?></span>
					<h2 class="card__title" style="margin:0;font-size:1.125rem"><?php echo esc_html( $estatein_channel['label'] ); ?></h2>

					<p style="font-size:1rem;color:var(--white)">
						<?php if ( $estatein_channel['href'] ) : ?>
							<a href="<?php echo esc_attr( $estatein_channel['href'] ); ?>" style="text-decoration:underline;text-underline-offset:3px">
								<?php echo esc_html( $estatein_channel['value'] ); ?>
							</a>
						<?php else : ?>
							<?php echo esc_html( $estatein_channel['value'] ); ?>
						<?php endif; ?>
					</p>

					<p class="card__text" style="margin-top:auto"><?php echo esc_html( $estatein_channel['note'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- Form -->
<section class="section rule" id="contact-form">
	<div class="container">
		<div class="split split--top">

			<div class="split__body" data-reveal>
				<?php estatein_icon( 'sparkle', array( 'class' => 'eyebrow-star' ) ); ?>
				<h2><?php esc_html_e( 'Let’s Make it Happen', 'estatein' ); ?></h2>
				<p class="lead">
					<?php esc_html_e( 'Fill in the form and we will match you with the agent who knows your area best. No call centres, no automated follow-ups.', 'estatein' ); ?>
				</p>

				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php if ( trim( get_the_content() ) ) : ?>
							<div class="entry-content mt-6"><?php the_content(); ?></div>
						<?php endif; ?>
					<?php endwhile; ?>
				<?php endif; ?>

				<div class="card mt-8" style="gap:12px">
					<?php estatein_icon( 'clock', array( 'size' => 22, 'class' => 'text-accent' ) ); ?>
					<h3 style="font-size:1.0625rem;margin:0"><?php esc_html_e( 'Office hours', 'estatein' ); ?></h3>
					<p class="card__text"><?php esc_html_e( 'Monday to Friday, 9:00am – 6:00pm. Saturday viewings by appointment.', 'estatein' ); ?></p>
				</div>
			</div>

			<div data-reveal style="--reveal-delay:100ms">
				<form
					class="card"
					method="post"
					action="<?php echo esc_url( get_permalink() ); ?>"
					data-estatein-form="contact"
					novalidate
					style="gap:20px;padding:clamp(24px,3vw,36px)"
				>
					<?php wp_nonce_field( 'estatein_form', 'estatein_nonce' ); ?>
					<input type="hidden" name="form_type" value="contact">
					<input type="hidden" name="estatein_form_submit" value="1">

					<?php // Honeypot. ?>
					<div class="sr-only" aria-hidden="true">
						<label for="cf-website"><?php esc_html_e( 'Leave this field empty', 'estatein' ); ?></label>
						<input type="text" id="cf-website" name="estatein_website" tabindex="-1" autocomplete="off">
					</div>

					<?php estatein_form_notice(); ?>

					<div class="grid grid--2" style="gap:16px">
						<div class="field">
							<label class="field__label" for="cf-name">
								<?php esc_html_e( 'Full name', 'estatein' ); ?> <span class="req" aria-hidden="true">*</span>
							</label>
							<input class="input" type="text" id="cf-name" name="name" required autocomplete="name" placeholder="<?php esc_attr_e( 'Jane Doe', 'estatein' ); ?>">
						</div>

						<div class="field">
							<label class="field__label" for="cf-email">
								<?php esc_html_e( 'Email', 'estatein' ); ?> <span class="req" aria-hidden="true">*</span>
							</label>
							<input class="input" type="email" id="cf-email" name="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'jane@example.com', 'estatein' ); ?>">
						</div>

						<div class="field">
							<label class="field__label" for="cf-phone"><?php esc_html_e( 'Phone', 'estatein' ); ?></label>
							<input class="input" type="tel" id="cf-phone" name="phone" autocomplete="tel" placeholder="<?php esc_attr_e( '+1 555 000 0000', 'estatein' ); ?>">
						</div>

						<div class="field">
							<label class="field__label" for="cf-subject"><?php esc_html_e( 'I am interested in', 'estatein' ); ?></label>
							<select class="select" id="cf-subject" name="subject">
								<option value="Buying"><?php esc_html_e( 'Buying a property', 'estatein' ); ?></option>
								<option value="Selling"><?php esc_html_e( 'Selling a property', 'estatein' ); ?></option>
								<option value="Renting"><?php esc_html_e( 'Renting', 'estatein' ); ?></option>
								<option value="Management"><?php esc_html_e( 'Property management', 'estatein' ); ?></option>
								<option value="Other"><?php esc_html_e( 'Something else', 'estatein' ); ?></option>
							</select>
						</div>
					</div>

					<div class="field">
						<label class="field__label" for="cf-message">
							<?php esc_html_e( 'Message', 'estatein' ); ?> <span class="req" aria-hidden="true">*</span>
						</label>
						<textarea class="textarea" id="cf-message" name="message" required placeholder="<?php esc_attr_e( 'Tell us a little about what you are looking for…', 'estatein' ); ?>"></textarea>
					</div>

					<label class="checkbox">
						<input type="checkbox" name="consent" required>
						<span>
							<?php
							printf(
								/* translators: %s: link to the privacy policy. */
								esc_html__( 'I agree to Estatein storing my details so they can respond. See the %s.', 'estatein' ),
								'<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '" style="color:var(--purple-60);text-decoration:underline">' . esc_html__( 'privacy policy', 'estatein' ) . '</a>'
							);
							?>
						</span>
					</label>

					<button type="submit" class="btn btn--primary btn--lg btn--block">
						<?php esc_html_e( 'Send Message', 'estatein' ); ?>
						<?php estatein_icon( 'arrow-right', array( 'size' => 18, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
					</button>

					<p class="form-note" data-form-status role="status" hidden></p>
				</form>
			</div>

		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/sections/faq', null, array( 'limit' => 5 ) ); ?>

<?php
get_footer();
