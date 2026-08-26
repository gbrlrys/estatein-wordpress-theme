<?php
/**
 * Customizer settings.
 *
 * estatein_option() resolves in this order: ACF options page → theme mod →
 * default. Registering these controls means the site-wide values (contact
 * details, social links, hero copy) are editable without installing ACF —
 * closing the gap that otherwise left the footer falling back to the
 * WordPress admin email.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function estatein_customize_register( $wp_customize ) {

	// Live-preview the blogname/description the theme already renders.
	if ( isset( $wp_customize->get_setting( 'blogname' )->transport ) ) {
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	}

	$wp_customize->add_panel(
		'estatein_panel',
		array(
			'title'       => __( 'Estatein Settings', 'estatein' ),
			'description' => __( 'Contact details, social links and homepage copy used across the theme.', 'estatein' ),
			'priority'    => 30,
		)
	);

	/* --- Contact details ------------------------------------------------ */
	$wp_customize->add_section(
		'estatein_contact',
		array(
			'title'       => __( 'Contact Details', 'estatein' ),
			'panel'       => 'estatein_panel',
			'description' => __( 'Shown in the footer, on the contact page and in structured data.', 'estatein' ),
		)
	);

	$contact_fields = array(
		'contact_phone'   => array(
			'label'   => __( 'Phone number', 'estatein' ),
			'default' => '+1 (555) 000-0000',
			'type'    => 'text',
			'sanitize' => 'sanitize_text_field',
		),
		'contact_email'   => array(
			'label'    => __( 'Public email address', 'estatein' ),
			'default'  => '',
			'type'     => 'email',
			'sanitize' => 'sanitize_email',
			'description' => __( 'Leave blank to fall back to the site admin email — set a dedicated address to avoid publishing a personal one.', 'estatein' ),
		),
		'contact_address' => array(
			'label'    => __( 'Address', 'estatein' ),
			'default'  => '2158 Mount Tabor, Los Angeles, CA',
			'type'     => 'textarea',
			'sanitize' => 'sanitize_textarea_field',
		),
	);

	foreach ( $contact_fields as $key => $field ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $field['default'],
				'sanitize_callback' => $field['sanitize'],
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$key,
			array(
				'label'       => $field['label'],
				'section'     => 'estatein_contact',
				'type'        => $field['type'],
				'description' => isset( $field['description'] ) ? $field['description'] : '',
			)
		);
	}

	/* --- Social links --------------------------------------------------- */
	$wp_customize->add_section(
		'estatein_social',
		array(
			'title'       => __( 'Social Links', 'estatein' ),
			'panel'       => 'estatein_panel',
			'description' => __( 'Leave a field blank to hide that icon in the footer.', 'estatein' ),
		)
	);

	$networks = array(
		'social_facebook'  => 'Facebook',
		'social_x'         => 'X (Twitter)',
		'social_linkedin'  => 'LinkedIn',
		'social_instagram' => 'Instagram',
	);

	foreach ( $networks as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'estatein_social',
				'type'    => 'url',
			)
		);
	}

	/* --- Homepage copy -------------------------------------------------- */
	$wp_customize->add_section(
		'estatein_home',
		array(
			'title' => __( 'Homepage Hero', 'estatein' ),
			'panel' => 'estatein_panel',
		)
	);

	$wp_customize->add_setting(
		'hero_title',
		array(
			'default'           => __( 'Discover Your Dream Property with Estatein', 'estatein' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'hero_title',
		array(
			'label'   => __( 'Headline', 'estatein' ),
			'section' => 'estatein_home',
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'hero_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'hero_text',
		array(
			'label'   => __( 'Supporting paragraph', 'estatein' ),
			'section' => 'estatein_home',
			'type'    => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'hero_image_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hero_image_id',
			array(
				'label'     => __( 'Hero image', 'estatein' ),
				'section'   => 'estatein_home',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'footer_blurb',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'footer_blurb',
		array(
			'label'   => __( 'Footer description', 'estatein' ),
			'section' => 'estatein_home',
			'type'    => 'textarea',
		)
	);
}
add_action( 'customize_register', 'estatein_customize_register' );

/**
 * Live-preview script for the title and tagline.
 */
function estatein_customize_preview_js() {
	wp_add_inline_script(
		'customize-preview',
		"(function($){
			wp.customize('blogname', function(v){ v.bind(function(t){ $('.brand span').text(t); }); });
			wp.customize('blogdescription', function(v){ v.bind(function(t){ $('.footer-about p').first().text(t); }); });
		})(jQuery);"
	);
}
add_action( 'customize_preview_init', 'estatein_customize_preview_js' );
