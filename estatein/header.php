<?php
/**
 * Document head and site header.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'estatein' ); ?></a>

<header class="site-header" id="site-header">
	<div class="container site-header__inner">

		<?php echo estatein_get_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Internal markup. ?>

		<button
			type="button"
			class="nav-toggle"
			id="nav-toggle"
			aria-expanded="false"
			aria-controls="primary-nav"
		>
			<span class="nav-toggle__bars" aria-hidden="true">
				<span></span><span></span><span></span>
			</span>
			<span class="sr-only"><?php esc_html_e( 'Toggle navigation', 'estatein' ); ?></span>
		</button>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'estatein' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'depth'          => 2,
						'walker'         => new Estatein_Nav_Walker(),
						'fallback_cb'    => false,
					)
				);
			} else {
				estatein_fallback_menu();
			}
			?>
		</nav>

		<a class="btn btn--primary header-cta" href="<?php echo esc_url( estatein_contact_url() ); ?>">
			<?php esc_html_e( 'Contact Us', 'estatein' ); ?>
		</a>

	</div>
</header>

<main id="main" class="site-main">
