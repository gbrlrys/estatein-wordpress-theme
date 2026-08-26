<?php
/**
 * Estatein theme bootstrap.
 *
 * Nothing but wiring lives here — each concern is a small file under inc/ so
 * the theme stays navigable as it grows.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

define( 'ESTATEIN_VERSION', '1.0.1' );
define( 'ESTATEIN_DIR', get_template_directory() );
define( 'ESTATEIN_URI', get_template_directory_uri() );

/**
 * Load a theme include.
 *
 * @param string $file Path relative to inc/, without extension.
 */
function estatein_require( $file ) {
	$path = ESTATEIN_DIR . '/inc/' . $file . '.php';
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

estatein_require( 'setup' );          // Theme supports, menus, image sizes.
estatein_require( 'enqueue' );        // Styles + scripts.
estatein_require( 'template-tags' );  // Reusable render helpers.
estatein_require( 'icons' );          // Inline SVG sprite helper.
estatein_require( 'post-types' );     // Properties, Testimonials, Team, FAQs.
estatein_require( 'fields' );         // ACF registration + safe field accessor.
estatein_require( 'customizer' );     // Site options without requiring ACF.
estatein_require( 'nav-walker' );     // Menu walker (adds CTA styling hook).
estatein_require( 'contact-form' );   // Contact + newsletter handlers.
estatein_require( 'seo' );            // Meta description, OG tags, JSON-LD.
estatein_require( 'content-defaults' ); // Design-accurate fallbacks pre-launch.
