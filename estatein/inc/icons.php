<?php
/**
 * Inline SVG icon set.
 *
 * Icons are inlined rather than loaded from a sprite or icon font: there is no
 * extra request, they inherit `currentColor`, and they can be styled per
 * instance. Every icon is decorative by default (`aria-hidden`); pass a
 * `label` when the icon is the only content of a control.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return the raw path data for an icon.
 *
 * @param string $name Icon key.
 * @return string Inner SVG markup, or empty string if unknown.
 */
function estatein_icon_path( $name ) {
	$icons = array(
		// --- Navigation / chrome -------------------------------------------
		'arrow-right'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left'   => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
		'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'plus'         => '<path d="M12 5v14M5 12h14"/>',
		'search'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/>',
		'check'        => '<path d="m20 6-11 11-5-5"/>',
		'close'        => '<path d="M18 6 6 18M6 6l12 12"/>',

		// --- Contact --------------------------------------------------------
		'mail'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 7 8.5 6 8.5-6"/>',
		'phone'        => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.2a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
		'map-pin'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',

		// --- Property specs -------------------------------------------------
		'bed'          => '<path d="M3 18v-8m0 4h18v4M3 14V7a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v7"/><path d="M21 14v-2a3 3 0 0 0-3-3h-6"/>',
		'bath'         => '<path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-3Z"/><path d="M6 12V6a2 2 0 0 1 3.5-1.3"/><path d="M7 21l-1 1M18 21l1 1"/>',
		'area'         => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18"/>',
		'car'          => '<path d="M5 17h14M6.5 17V19a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-2M20.5 17V19a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1v-2"/><path d="M3 17v-4l2-5a2 2 0 0 1 1.9-1.3h10.2A2 2 0 0 1 19 8l2 5v4Z"/>',
		'tag'          => '<path d="M3 11V4a1 1 0 0 1 1-1h7l9.5 9.5a1.5 1.5 0 0 1 0 2.1l-6.9 6.9a1.5 1.5 0 0 1-2.1 0L3 11Z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
		'calendar'     => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',

		// --- Marks / decoration --------------------------------------------
		'star'         => '<path d="m12 3 2.6 5.6 6.1.8-4.5 4.2 1.2 6.1L12 16.8 6.6 19.7l1.2-6.1L3.3 9.4l6.1-.8L12 3Z" fill="currentColor" stroke="none"/>',
		'sparkle'      => '<path d="M12 1.5c0 5.8 4.7 10.5 10.5 10.5C16.7 12 12 16.7 12 22.5 12 16.7 7.3 12 1.5 12 7.3 12 12 7.3 12 1.5Z" fill="currentColor" stroke="none"/>',
		'quote'        => '<path d="M9 11H5.5A2.5 2.5 0 0 1 3 8.5v-1A2.5 2.5 0 0 1 5.5 5h1A2.5 2.5 0 0 1 9 7.5V15a4 4 0 0 1-4 4M21 11h-3.5A2.5 2.5 0 0 1 15 8.5v-1A2.5 2.5 0 0 1 17.5 5h1A2.5 2.5 0 0 1 21 7.5V15a4 4 0 0 1-4 4"/>',

		// --- Services / values ---------------------------------------------
		'building'     => '<path d="M4 21V5a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v16"/><path d="M15 9h3a2 2 0 0 1 2 2v10M2 21h20"/><path d="M8 7h3M8 11h3M8 15h3"/>',
		'shield'       => '<path d="M12 22s8-3.5 8-9.5V5.5L12 2 4 5.5V12.5C4 18.5 12 22 12 22Z"/><path d="m9 12 2 2 4-4"/>',
		'chart'        => '<path d="M3 21h18M7 21V11M12 21V4M17 21v-6"/>',
		'scale'        => '<path d="M12 3v18M7 21h10M3 8l4-4 4 4M3 8a4 4 0 0 0 8 0M13 8l4-4 4 4M13 8a4 4 0 0 0 8 0"/>',
		'key'          => '<circle cx="8" cy="15" r="4"/><path d="m11 12 8-8 2 2-2 2 2 2-2 2-2-2-2 2"/>',
		'users'        => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
		'heart'        => '<path d="M20.8 5.6a5 5 0 0 0-7.1 0L12 7.3l-1.7-1.7a5 5 0 1 0-7.1 7.1l8.8 8.8 8.8-8.8a5 5 0 0 0 0-7.1Z"/>',
		'globe'        => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>',
		'sliders'      => '<path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/>',

		// --- Social ---------------------------------------------------------
		'facebook'     => '<path d="M14 9h3V6h-3a4 4 0 0 0-4 4v2H8v3h2v7h3v-7h2.5l.5-3H13v-2a1 1 0 0 1 1-1Z" fill="currentColor" stroke="none"/>',
		'x'            => '<path d="M4 3h4.2l4.3 5.9L17.6 3H21l-6.9 7.8L21.4 21h-4.2l-4.6-6.3L7 21H3.6l7.2-8.2L4 3Z" fill="currentColor" stroke="none"/>',
		'linkedin'     => '<path d="M6.9 8.5H4V21h2.9V8.5ZM5.4 3a1.8 1.8 0 1 0 0 3.5 1.8 1.8 0 0 0 0-3.5ZM20 13.6c0-3.3-1.8-4.8-4.1-4.8-1.9 0-2.7 1-3.2 1.8V8.5H9.8V21h2.9v-6.9c0-1.5.8-2.4 2-2.4s1.9.8 1.9 2.4V21H20v-7.4Z" fill="currentColor" stroke="none"/>',
		'instagram'    => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor" stroke="none"/>',
		'youtube'      => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="m10.5 9.5 5 2.5-5 2.5v-5Z" fill="currentColor" stroke="none"/>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Render an inline SVG icon.
 *
 * @param string $name  Icon key from estatein_icon_path().
 * @param array  $args  {
 *     @type string $class Extra CSS classes.
 *     @type int    $size  Square size in px (sets width/height attributes).
 *     @type string $label Accessible name. When set the icon is exposed to AT.
 * }
 * @return string Escaped-safe SVG markup.
 */
function estatein_get_icon( $name, $args = array() ) {
	$path = estatein_icon_path( $name );
	if ( '' === $path ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'class' => '',
			'size'  => 24,
			'label' => '',
		)
	);

	$a11y = $args['label']
		? ' role="img" aria-label="' . esc_attr( $args['label'] ) . '"'
		: ' aria-hidden="true" focusable="false"';

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"%3$s>%4$s</svg>',
		esc_attr( $args['class'] ),
		absint( $args['size'] ),
		$a11y,
		$path // Trusted: from the internal map above, never user input.
	);
}

/**
 * Echo an inline SVG icon.
 *
 * @param string $name Icon key.
 * @param array  $args See estatein_get_icon().
 */
function estatein_icon( $name, $args = array() ) {
	echo estatein_get_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup built internally.
}

/**
 * The Estatein wordmark.
 *
 * Uses the site logo when one is set in the Customizer, otherwise falls back
 * to the SVG mark + site name so a fresh install still looks intentional.
 *
 * @return string
 */
function estatein_get_logo() {
	if ( has_custom_logo() ) {
		return get_custom_logo();
	}

	$mark = '<svg class="brand__mark" viewBox="0 0 32 32" fill="none" aria-hidden="true" focusable="false">'
		. '<rect width="32" height="32" rx="9" fill="currentColor"/>'
		. '<path d="M16 7.5 24 13v11.5h-5.6v-6.2h-4.8v6.2H8V13l8-5.5Z" fill="#fff"/>'
		. '</svg>';

	return sprintf(
		'<a class="brand" href="%1$s" rel="home">%2$s<span>%3$s</span></a>',
		esc_url( home_url( '/' ) ),
		$mark,
		esc_html( get_bloginfo( 'name' ) )
	);
}
