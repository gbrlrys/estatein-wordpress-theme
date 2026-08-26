<?php
/**
 * Theme setup: supports, menus, image sizes, content width.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function estatein_setup() {
	load_theme_textdomain( 'estatein', ESTATEIN_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// Output clean HTML5 markup rather than WordPress' legacy XHTML.
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	// The design is dark-only; tell the block editor so previews match.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_theme_support( 'dark-editor-style' );

	// Expose the palette to the block editor colour picker.
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __( 'Purple', 'estatein' ),
				'slug'  => 'purple',
				'color' => '#703BF7',
			),
			array(
				'name'  => __( 'Light Purple', 'estatein' ),
				'slug'  => 'purple-light',
				'color' => '#A685FA',
			),
			array(
				'name'  => __( 'Black', 'estatein' ),
				'slug'  => 'black',
				'color' => '#000000',
			),
			array(
				'name'  => __( 'Surface', 'estatein' ),
				'slug'  => 'surface',
				'color' => '#141414',
			),
			array(
				'name'  => __( 'Border', 'estatein' ),
				'slug'  => 'border',
				'color' => '#262626',
			),
			array(
				'name'  => __( 'Muted', 'estatein' ),
				'slug'  => 'muted',
				'color' => '#999999',
			),
			array(
				'name'  => __( 'White', 'estatein' ),
				'slug'  => 'white',
				'color' => '#FFFFFF',
			),
		)
	);
	add_theme_support( 'disable-custom-colors' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu (header)', 'estatein' ),
			'footer-explore' => __( 'Footer — Explore', 'estatein' ),
			'footer-company' => __( 'Footer — Company', 'estatein' ),
			'footer-legal'   => __( 'Footer — Legal (bottom bar)', 'estatein' ),
		)
	);

	/*
	 * Crops used across the design. Registering them keeps WordPress from
	 * serving a 2000px original into a 400px card slot.
	 */
	add_image_size( 'estatein-card', 720, 450, true );      // Property / post cards.
	add_image_size( 'estatein-hero', 1200, 1020, true );    // Home hero portrait.
	add_image_size( 'estatein-wide', 1440, 810, true );     // Gallery lead image.
	add_image_size( 'estatein-square', 480, 480, true );    // Team portraits.
	add_image_size( 'estatein-avatar', 120, 120, true );    // Testimonial avatars.
}
add_action( 'after_setup_theme', 'estatein_setup' );

/**
 * Content width used by oEmbeds and wide images.
 */
function estatein_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'estatein_content_width', 1248 );
}
add_action( 'after_setup_theme', 'estatein_content_width', 0 );

/**
 * Give the custom crops friendly names in the media picker.
 *
 * @param array $sizes Existing size choices.
 * @return array
 */
function estatein_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'estatein-card' => __( 'Card (720×450)', 'estatein' ),
			'estatein-wide' => __( 'Wide (1440×810)', 'estatein' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'estatein_image_size_names' );

/**
 * Register widget areas.
 */
function estatein_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'estatein' ),
			'id'            => 'sidebar-blog',
			'description'   => __( 'Shown beside blog archives and single posts.', 'estatein' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'estatein_widgets_init' );

/**
 * Add a `no-js` class that JS immediately removes.
 *
 * Lets CSS provide a working no-JavaScript baseline (revealed content,
 * open accordions) without a flash for the 99% who do have JS.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function estatein_body_classes( $classes ) {
	$classes[] = 'no-js';

	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'estatein_body_classes' );

/**
 * Trim the default excerpt and use a typographic ellipsis.
 */
add_filter( 'excerpt_length', function () { return 22; }, 9 );
add_filter( 'excerpt_more', function () { return '&hellip;'; } );

/* -------------------------------------------------------------------------
 * Front-end housekeeping — small wins for page weight and privacy.
 * ---------------------------------------------------------------------- */

// Remove the emoji detection script + stylesheet (~10KB, unused here).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

// Drop generator/oEmbed discovery noise from <head>.
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );

/**
 * Prevent WordPress' block library CSS from loading when no blocks are used.
 */
function estatein_dequeue_block_library() {
	if ( ! is_singular() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'estatein_dequeue_block_library', 100 );
