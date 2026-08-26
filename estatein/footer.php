<?php
/**
 * Site footer.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_socials = array(
	'facebook'  => estatein_option( 'social_facebook', '' ),
	'x'         => estatein_option( 'social_x', '' ),
	'linkedin'  => estatein_option( 'social_linkedin', '' ),
	'instagram' => estatein_option( 'social_instagram', '' ),
);
?>
</main><!-- #main -->

<footer class="site-footer">

	<!-- Newsletter -->
	<div class="container">
		<div class="footer-cta">
			<div class="footer-cta__title">
				<h2 id="newsletter-heading"><?php esc_html_e( 'Stay ahead of the market', 'estatein' ); ?></h2>
				<p class="lead" style="margin-top:8px">
					<?php esc_html_e( 'New listings, market notes and buying guides. One email a month, no filler.', 'estatein' ); ?>
				</p>
			</div>

			<form
				class="newsletter"
				method="post"
				action="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>"
				data-estatein-form="newsletter"
				aria-labelledby="newsletter-heading"
			>
				<?php wp_nonce_field( 'estatein_form', 'estatein_nonce' ); ?>
				<input type="hidden" name="form_type" value="newsletter">
				<input type="hidden" name="estatein_form_submit" value="1">

				<?php // Honeypot — hidden from people, tempting to bots. ?>
				<div class="sr-only" aria-hidden="true">
					<label for="nl-website"><?php esc_html_e( 'Leave this field empty', 'estatein' ); ?></label>
					<input type="text" id="nl-website" name="estatein_website" tabindex="-1" autocomplete="off">
				</div>

				<div class="input-wrap">
					<?php estatein_icon( 'mail' ); ?>
					<label class="sr-only" for="newsletter-email"><?php esc_html_e( 'Email address', 'estatein' ); ?></label>
					<input
						class="input"
						type="email"
						id="newsletter-email"
						name="email"
						required
						autocomplete="email"
						placeholder="<?php esc_attr_e( 'Enter your email', 'estatein' ); ?>"
					>
				</div>

				<button type="submit" class="btn btn--primary">
					<?php esc_html_e( 'Subscribe', 'estatein' ); ?>
				</button>

				<p class="form-note" data-form-status role="status" hidden></p>
			</form>
		</div>
	</div>

	<!-- Link columns -->
	<div class="container">
		<div class="footer-grid">

			<div class="footer-col footer-about">
				<?php echo estatein_get_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p>
					<?php
					echo esc_html(
						estatein_option(
							'footer_blurb',
							__( 'Helping people find, finance and furnish the places they live since 2008. Independent, and proud of it.', 'estatein' )
						)
					);
					?>
				</p>

				<ul class="socials">
					<?php foreach ( $estatein_socials as $estatein_network => $estatein_url ) : ?>
						<?php if ( $estatein_url ) : ?>
							<li>
								<a href="<?php echo esc_url( $estatein_url ); ?>" rel="noopener noreferrer" target="_blank">
									<?php estatein_icon( $estatein_network ); ?>
									<span class="sr-only"><?php echo esc_html( ucfirst( $estatein_network ) ); ?></span>
								</a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="footer-col">
				<h2 class="footer-col__title"><?php esc_html_e( 'Explore', 'estatein' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer-explore' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer-explore', 'container' => false, 'depth' => 1 ) );
				} else {
					echo '<ul>';
					foreach ( estatein_primary_links() as $estatein_link ) {
						printf(
							'<li><a href="%s">%s</a></li>',
							esc_url( $estatein_link['url'] ),
							esc_html( $estatein_link['label'] )
						);
					}
					echo '</ul>';
				}
				?>
			</div>

			<div class="footer-col">
				<h2 class="footer-col__title"><?php esc_html_e( 'Services', 'estatein' ); ?></h2>
				<ul>
					<?php foreach ( array_slice( estatein_get_services( 4 ), 0, 4 ) as $estatein_service ) : ?>
						<li>
							<a href="<?php echo esc_url( ! empty( $estatein_service['url'] ) ? $estatein_service['url'] : home_url( '/services/' ) ); ?>">
								<?php echo esc_html( $estatein_service['title'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="footer-col">
				<h2 class="footer-col__title"><?php esc_html_e( 'Company', 'estatein' ); ?></h2>
				<?php
				if ( has_nav_menu( 'footer-company' ) ) {
					wp_nav_menu( array( 'theme_location' => 'footer-company', 'container' => false, 'depth' => 1 ) );
				} else {
					?>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About Us', 'estatein' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'Blog', 'estatein' ); ?></a></li>
						<li><a href="<?php echo esc_url( estatein_contact_url() ); ?>"><?php esc_html_e( 'Contact', 'estatein' ); ?></a></li>
					</ul>
					<?php
				}
				?>
			</div>

			<div class="footer-col">
				<h2 class="footer-col__title"><?php esc_html_e( 'Contact', 'estatein' ); ?></h2>
				<ul>
					<?php $estatein_phone = estatein_option( 'contact_phone', '+1 (555) 000-0000' ); ?>
					<li>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $estatein_phone ) ); ?>">
							<?php echo esc_html( $estatein_phone ); ?>
						</a>
					</li>
					<?php $estatein_email = estatein_option( 'contact_email', get_option( 'admin_email' ) ); ?>
					<li>
						<a href="mailto:<?php echo esc_attr( $estatein_email ); ?>"><?php echo esc_html( $estatein_email ); ?></a>
					</li>
					<li><?php echo esc_html( estatein_option( 'contact_address', '2158 Mount Tabor, Los Angeles, CA' ) ); ?></li>
				</ul>
			</div>

		</div>
	</div>

	<!-- Bottom bar -->
	<div class="container">
		<div class="footer-bottom">
			<p>
				<?php
				printf(
					/* translators: 1: year, 2: site name. */
					esc_html__( '© %1$s %2$s. All rights reserved.', 'estatein' ),
					esc_html( gmdate( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</p>

			<?php
			if ( has_nav_menu( 'footer-legal' ) ) {
				wp_nav_menu( array( 'theme_location' => 'footer-legal', 'container' => false, 'depth' => 1 ) );
			} else {
				?>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'estatein' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'estatein' ); ?></a></li>
				</ul>
				<?php
			}
			?>
		</div>
	</div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
