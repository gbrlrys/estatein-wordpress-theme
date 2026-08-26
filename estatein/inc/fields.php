<?php
/**
 * Content fields.
 *
 * The theme is built to work with Advanced Custom Fields but never to *depend*
 * on it. Two things make that possible:
 *
 *   1. estatein_field() reads through get_field() when ACF is active and falls
 *      back to post meta otherwise — the templates only ever call this.
 *   2. When ACF is missing, a small native meta box supplies the same keys, so
 *      an editor can still fill in a property without installing a plugin.
 *
 * Field groups are registered in PHP (not imported from JSON) so the theme is
 * self-contained and version-controlled.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is ACF available?
 *
 * @return bool
 */
function estatein_has_acf() {
	return function_exists( 'get_field' );
}

/**
 * Read a custom field with a safe fallback.
 *
 * @param string   $key      Field name.
 * @param int|null $post_id  Post ID. Defaults to the current post.
 * @param mixed    $default  Value returned when the field is empty.
 * @return mixed
 */
function estatein_field( $key, $post_id = null, $default = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( ! $post_id ) {
		return $default;
	}

	$value = estatein_has_acf()
		? get_field( $key, $post_id )
		: get_post_meta( $post_id, $key, true );

	if ( null === $value || '' === $value || array() === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Read a theme-options field (ACF options page) with a filterable default.
 *
 * @param string $key     Option field name.
 * @param mixed  $default Fallback value.
 * @return mixed
 */
function estatein_option( $key, $default = '' ) {
	$value = '';

	if ( estatein_has_acf() && function_exists( 'acf_add_options_page' ) ) {
		$value = get_field( $key, 'option' );
	}

	if ( '' === $value || null === $value ) {
		$value = get_theme_mod( $key, '' );
	}

	if ( '' === $value || null === $value ) {
		$value = $default;
	}

	/**
	 * Filter a resolved theme option.
	 *
	 * @param mixed  $value Resolved value.
	 * @param string $key   Option key.
	 */
	return apply_filters( 'estatein_option', $value, $key );
}

/* =========================================================================
 * 1. ACF field-group registration
 * ====================================================================== */

/**
 * Register field groups when ACF is present.
 */
function estatein_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// --- Property details -------------------------------------------------
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_property',
			'title'    => __( 'Property Details', 'estatein' ),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'property' ) ) ),
			'position' => 'normal',
			'fields'   => array(
				array( 'key' => 'field_price', 'label' => __( 'Price (USD)', 'estatein' ), 'name' => 'price', 'type' => 'number', 'wrapper' => array( 'width' => '33' ), 'min' => 0 ),
				array( 'key' => 'field_bedrooms', 'label' => __( 'Bedrooms', 'estatein' ), 'name' => 'bedrooms', 'type' => 'number', 'wrapper' => array( 'width' => '33' ), 'min' => 0 ),
				array( 'key' => 'field_bathrooms', 'label' => __( 'Bathrooms', 'estatein' ), 'name' => 'bathrooms', 'type' => 'number', 'wrapper' => array( 'width' => '34' ), 'min' => 0 ),
				array( 'key' => 'field_area', 'label' => __( 'Area (sq ft)', 'estatein' ), 'name' => 'area', 'type' => 'number', 'wrapper' => array( 'width' => '33' ), 'min' => 0 ),
				array( 'key' => 'field_garages', 'label' => __( 'Parking spaces', 'estatein' ), 'name' => 'garages', 'type' => 'number', 'wrapper' => array( 'width' => '33' ), 'min' => 0 ),
				array( 'key' => 'field_year_built', 'label' => __( 'Year built', 'estatein' ), 'name' => 'year_built', 'type' => 'number', 'wrapper' => array( 'width' => '34' ) ),
				array( 'key' => 'field_address', 'label' => __( 'Address', 'estatein' ), 'name' => 'address', 'type' => 'text' ),
				array( 'key' => 'field_featured', 'label' => __( 'Feature on the homepage', 'estatein' ), 'name' => 'featured', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_gallery', 'label' => __( 'Gallery', 'estatein' ), 'name' => 'gallery', 'type' => 'gallery', 'return_format' => 'id' ),
				array(
					'key'          => 'field_amenities',
					'label'        => __( 'Amenities', 'estatein' ),
					'name'         => 'amenities',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Add amenity', 'estatein' ),
					'sub_fields'   => array(
						array( 'key' => 'field_amenity_label', 'label' => __( 'Label', 'estatein' ), 'name' => 'label', 'type' => 'text' ),
					),
				),
			),
		)
	);

	// --- Testimonial ------------------------------------------------------
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_testimonial',
			'title'    => __( 'Testimonial Details', 'estatein' ),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'testimonial' ) ) ),
			'fields'   => array(
				array( 'key' => 'field_t_name', 'label' => __( 'Client name', 'estatein' ), 'name' => 'client_name', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
				array( 'key' => 'field_t_location', 'label' => __( 'Location', 'estatein' ), 'name' => 'client_location', 'type' => 'text', 'wrapper' => array( 'width' => '50' ) ),
				array( 'key' => 'field_t_rating', 'label' => __( 'Rating (1–5)', 'estatein' ), 'name' => 'rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5 ),
			),
		)
	);

	// --- Team member ------------------------------------------------------
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_team',
			'title'    => __( 'Team Member Details', 'estatein' ),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'team_member' ) ) ),
			'fields'   => array(
				array( 'key' => 'field_tm_role', 'label' => __( 'Role', 'estatein' ), 'name' => 'role', 'type' => 'text' ),
				array( 'key' => 'field_tm_linkedin', 'label' => __( 'LinkedIn URL', 'estatein' ), 'name' => 'linkedin', 'type' => 'url' ),
			),
		)
	);

	// --- Service ----------------------------------------------------------
	acf_add_local_field_group(
		array(
			'key'      => 'group_estatein_service',
			'title'    => __( 'Service Details', 'estatein' ),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'service' ) ) ),
			'fields'   => array(
				array(
					'key'     => 'field_s_icon',
					'label'   => __( 'Icon', 'estatein' ),
					'name'    => 'icon',
					'type'    => 'select',
					'choices' => array(
						'building' => 'Building', 'chart' => 'Chart', 'scale' => 'Scale',
						'key' => 'Key', 'shield' => 'Shield', 'users' => 'Users',
						'globe' => 'Globe', 'heart' => 'Heart',
					),
					'default_value' => 'building',
				),
			),
		)
	);

	// --- Site-wide options ------------------------------------------------
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Estatein Settings', 'estatein' ),
				'menu_title' => __( 'Estatein', 'estatein' ),
				'menu_slug'  => 'estatein-settings',
				'icon_url'   => 'dashicons-admin-customizer',
				'position'   => 3,
				'capability' => 'edit_theme_options',
			)
		);

		acf_add_local_field_group(
			array(
				'key'      => 'group_estatein_options',
				'title'    => __( 'Contact & Social', 'estatein' ),
				'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'estatein-settings' ) ) ),
				'fields'   => array(
					array( 'key' => 'field_o_phone', 'label' => __( 'Phone', 'estatein' ), 'name' => 'contact_phone', 'type' => 'text' ),
					array( 'key' => 'field_o_email', 'label' => __( 'Email', 'estatein' ), 'name' => 'contact_email', 'type' => 'email' ),
					array( 'key' => 'field_o_address', 'label' => __( 'Address', 'estatein' ), 'name' => 'contact_address', 'type' => 'textarea', 'rows' => 3 ),
					array( 'key' => 'field_o_fb', 'label' => 'Facebook', 'name' => 'social_facebook', 'type' => 'url' ),
					array( 'key' => 'field_o_x', 'label' => 'X / Twitter', 'name' => 'social_x', 'type' => 'url' ),
					array( 'key' => 'field_o_li', 'label' => 'LinkedIn', 'name' => 'social_linkedin', 'type' => 'url' ),
					array( 'key' => 'field_o_ig', 'label' => 'Instagram', 'name' => 'social_instagram', 'type' => 'url' ),
				),
			)
		);
	}
}
add_action( 'acf/init', 'estatein_register_acf_fields' );

/* =========================================================================
 * 2. Native meta-box fallback (only when ACF is absent)
 * ====================================================================== */

/**
 * Fields mirrored by the native fallback, keyed by post type.
 *
 * @return array
 */
function estatein_fallback_fields() {
	return array(
		'property'    => array(
			'price'      => array( 'label' => __( 'Price (USD)', 'estatein' ), 'type' => 'number' ),
			'bedrooms'   => array( 'label' => __( 'Bedrooms', 'estatein' ), 'type' => 'number' ),
			'bathrooms'  => array( 'label' => __( 'Bathrooms', 'estatein' ), 'type' => 'number' ),
			'area'       => array( 'label' => __( 'Area (sq ft)', 'estatein' ), 'type' => 'number' ),
			'garages'    => array( 'label' => __( 'Parking spaces', 'estatein' ), 'type' => 'number' ),
			'year_built' => array( 'label' => __( 'Year built', 'estatein' ), 'type' => 'number' ),
			'address'    => array( 'label' => __( 'Address', 'estatein' ), 'type' => 'text' ),
			'featured'   => array( 'label' => __( 'Feature on homepage', 'estatein' ), 'type' => 'checkbox' ),
		),
		'testimonial' => array(
			'client_name'     => array( 'label' => __( 'Client name', 'estatein' ), 'type' => 'text' ),
			'client_location' => array( 'label' => __( 'Location', 'estatein' ), 'type' => 'text' ),
			'rating'          => array( 'label' => __( 'Rating (1–5)', 'estatein' ), 'type' => 'number' ),
		),
		'team_member' => array(
			'role'     => array( 'label' => __( 'Role', 'estatein' ), 'type' => 'text' ),
			'linkedin' => array( 'label' => __( 'LinkedIn URL', 'estatein' ), 'type' => 'url' ),
		),
		'service'     => array(
			'icon' => array( 'label' => __( 'Icon key (building, chart, scale, key, shield, users, globe, heart)', 'estatein' ), 'type' => 'text' ),
		),
	);
}

/**
 * Register the fallback meta boxes.
 */
function estatein_add_meta_boxes() {
	if ( estatein_has_acf() ) {
		return; // ACF owns the UI.
	}

	foreach ( array_keys( estatein_fallback_fields() ) as $post_type ) {
		add_meta_box(
			'estatein_details',
			__( 'Details', 'estatein' ),
			'estatein_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'estatein_add_meta_boxes' );

/**
 * Render the fallback meta box.
 *
 * @param WP_Post $post Current post.
 */
function estatein_render_meta_box( $post ) {
	$fields = estatein_fallback_fields();
	$set    = isset( $fields[ $post->post_type ] ) ? $fields[ $post->post_type ] : array();

	wp_nonce_field( 'estatein_save_meta', 'estatein_meta_nonce' );

	echo '<div class="estatein-meta">';
	foreach ( $set as $key => $config ) {
		$value = get_post_meta( $post->ID, $key, true );
		printf( '<p><label for="estatein-%1$s"><strong>%2$s</strong></label><br>', esc_attr( $key ), esc_html( $config['label'] ) );

		if ( 'checkbox' === $config['type'] ) {
			printf(
				'<input type="checkbox" id="estatein-%1$s" name="estatein[%1$s]" value="1" %2$s>',
				esc_attr( $key ),
				checked( $value, '1', false )
			);
		} else {
			printf(
				'<input type="%1$s" id="estatein-%2$s" name="estatein[%2$s]" value="%3$s" class="widefat">',
				esc_attr( $config['type'] ),
				esc_attr( $key ),
				esc_attr( $value )
			);
		}
		echo '</p>';
	}
	echo '</div>';
}

/**
 * Persist the fallback meta box.
 *
 * @param int $post_id Post being saved.
 */
function estatein_save_meta( $post_id ) {
	if ( ! isset( $_POST['estatein_meta_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['estatein_meta_nonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'estatein_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields    = estatein_fallback_fields();
	$post_type = get_post_type( $post_id );
	$set       = isset( $fields[ $post_type ] ) ? $fields[ $post_type ] : array();
	$submitted = isset( $_POST['estatein'] ) ? wp_unslash( $_POST['estatein'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitised per field below.

	foreach ( $set as $key => $config ) {
		if ( 'checkbox' === $config['type'] ) {
			update_post_meta( $post_id, $key, empty( $submitted[ $key ] ) ? '' : '1' );
			continue;
		}

		if ( ! isset( $submitted[ $key ] ) ) {
			continue;
		}

		$raw = $submitted[ $key ];

		switch ( $config['type'] ) {
			case 'number':
				$clean = '' === $raw ? '' : (string) floatval( $raw );
				break;
			case 'url':
				$clean = esc_url_raw( $raw );
				break;
			default:
				$clean = sanitize_text_field( $raw );
		}

		update_post_meta( $post_id, $key, $clean );
	}
}
add_action( 'save_post', 'estatein_save_meta' );
