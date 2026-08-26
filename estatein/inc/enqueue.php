<?php
/**
 * Asset loading.
 *
 * Two files ship to the browser: one stylesheet and one deferred script.
 * Both are versioned with filemtime so a deploy busts the cache without a
 * manual version bump.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version string for a theme-relative asset.
 *
 * @param string $rel Path relative to the theme root.
 * @return string
 */
function estatein_asset_version( $rel ) {
	$path = ESTATEIN_DIR . '/' . ltrim( $rel, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : ESTATEIN_VERSION;
}

/**
 * Enqueue front-end assets.
 */
function estatein_enqueue_assets() {
	// Main stylesheet. The theme header (style.css) is intentionally not loaded.
	wp_enqueue_style(
		'estatein',
		ESTATEIN_URI . '/assets/css/main.css',
		array(),
		estatein_asset_version( 'assets/css/main.css' )
	);

	// Behaviour: nav, accordion, carousels, reveals, form validation.
	wp_enqueue_script(
		'estatein',
		ESTATEIN_URI . '/assets/js/main.js',
		array(),
		estatein_asset_version( 'assets/js/main.js' ),
		true
	);

	// Endpoint + nonce for the AJAX contact/newsletter forms.
	wp_localize_script(
		'estatein',
		'estateinData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'estatein_form' ),
			'i18n'    => array(
				'required' => __( 'This field is required.', 'estatein' ),
				'email'    => __( 'Please enter a valid email address.', 'estatein' ),
				'sending'  => __( 'Sending…', 'estatein' ),
				'error'    => __( 'Something went wrong. Please try again.', 'estatein' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'estatein_enqueue_assets' );

/**
 * Ship the script with `defer` so it never blocks the parser.
 *
 * @param string $tag    Script tag HTML.
 * @param string $handle Script handle.
 * @return string
 */
function estatein_defer_script( $tag, $handle ) {
	if ( 'estatein' === $handle && false === strpos( $tag, 'defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'estatein_defer_script', 10, 2 );

/**
 * Preload the two self-hosted font files and declare the @font-face rules.
 *
 * Urbanist is a variable font, so a single file per subset covers weights
 * 400–700. Preloading avoids the invisible-text gap on first paint.
 */
function estatein_font_head() {
	$latin = ESTATEIN_URI . '/assets/fonts/urbanist-latin.woff2';
	$ext   = ESTATEIN_URI . '/assets/fonts/urbanist-latin-ext.woff2';
	?>
	<link rel="preload" href="<?php echo esc_url( $latin ); ?>" as="font" type="font/woff2" crossorigin>
	<style id="estatein-fonts">
		/* Urbanist variable — SIL Open Font License 1.1 */
		@font-face {
			font-family: 'Urbanist';
			font-style: normal;
			font-weight: 100 900;
			font-display: swap;
			src: url('<?php echo esc_url( $latin ); ?>') format('woff2');
			unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
		}
		@font-face {
			font-family: 'Urbanist';
			font-style: normal;
			font-weight: 100 900;
			font-display: swap;
			src: url('<?php echo esc_url( $ext ); ?>') format('woff2');
			unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'estatein_font_head', 1 );

/**
 * Theme colour for mobile browser chrome.
 */
function estatein_theme_color_meta() {
	echo '<meta name="theme-color" content="#000000">' . "\n";
	echo '<meta name="color-scheme" content="dark">' . "\n";
}
add_action( 'wp_head', 'estatein_theme_color_meta', 2 );

/**
 * Native lazy-loading + async decoding on content images.
 *
 * WordPress adds `loading="lazy"` itself since 5.5; this also sets
 * `decoding="async"` which keeps image decode off the main thread.
 *
 * @param array $attr Attributes for the image markup.
 * @return array
 */
function estatein_image_attributes( $attr ) {
	if ( empty( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'estatein_image_attributes' );

/**
 * Load the block editor stylesheet for the admin colour scheme.
 */
function estatein_admin_assets() {
	wp_enqueue_style(
		'estatein-admin',
		ESTATEIN_URI . '/assets/css/admin.css',
		array(),
		estatein_asset_version( 'assets/css/admin.css' )
	);
}
add_action( 'admin_enqueue_scripts', 'estatein_admin_assets' );
