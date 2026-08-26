<?php
/**
 * 404 — page not found.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="section">
	<div class="container">
		<div class="card text-center mx-auto" style="max-width:720px;padding:clamp(40px,6vw,72px) 24px;align-items:center">
			<p class="text-accent" style="font-size:clamp(3rem,8vw,5rem);font-weight:700;line-height:1;letter-spacing:-.03em">404</p>

			<h1 class="mt-6"><?php esc_html_e( 'We could not find that page', 'estatein' ); ?></h1>

			<p class="lead mt-6" style="max-width:46ch">
				<?php esc_html_e( 'The link may be out of date, or the listing may have been sold. Try a search, or start from one of these.', 'estatein' ); ?>
			</p>

			<div class="mt-8" style="width:100%;max-width:460px"><?php get_search_form(); ?></div>

			<div class="mt-8" style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center">
				<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'estatein' ); ?></a>
				<a class="btn" href="<?php echo esc_url( estatein_properties_url() ); ?>"><?php esc_html_e( 'Properties', 'estatein' ); ?></a>
				<a class="btn" href="<?php echo esc_url( estatein_contact_url() ); ?>"><?php esc_html_e( 'Contact', 'estatein' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
