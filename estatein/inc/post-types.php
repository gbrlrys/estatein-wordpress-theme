<?php
/**
 * Custom post types and taxonomies.
 *
 * The design has five repeating content shapes. Each becomes its own post type
 * so an editor gets a purpose-built list screen instead of a pile of pages.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register post types.
 */
function estatein_register_post_types() {

	/* --- Properties ----------------------------------------------------- */
	register_post_type(
		'property',
		array(
			'labels'        => estatein_cpt_labels( __( 'Property', 'estatein' ), __( 'Properties', 'estatein' ) ),
			'public'        => true,
			'has_archive'   => true,
			'rewrite'       => array( 'slug' => 'properties', 'with_front' => false ),
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 20,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
			'show_in_rest'  => true,
			'rest_base'     => 'properties',
		)
	);

	/* --- Services ------------------------------------------------------- */
	register_post_type(
		'service',
		array(
			'labels'       => estatein_cpt_labels( __( 'Service', 'estatein' ), __( 'Services', 'estatein' ) ),
			'public'       => true,
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'services', 'with_front' => false ),
			'menu_icon'    => 'dashicons-portfolio',
			'menu_position' => 21,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest' => true,
		)
	);

	/* --- Testimonials --------------------------------------------------- */
	register_post_type(
		'testimonial',
		array(
			'labels'       => estatein_cpt_labels( __( 'Testimonial', 'estatein' ), __( 'Testimonials', 'estatein' ) ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-format-quote',
			'menu_position' => 22,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'show_in_rest' => true,
		)
	);

	/* --- Team members --------------------------------------------------- */
	register_post_type(
		'team_member',
		array(
			'labels'       => estatein_cpt_labels( __( 'Team Member', 'estatein' ), __( 'Team', 'estatein' ) ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-groups',
			'menu_position' => 23,
			'supports'     => array( 'title', 'thumbnail', 'editor', 'page-attributes' ),
			'show_in_rest' => true,
		)
	);

	/* --- FAQs ----------------------------------------------------------- */
	register_post_type(
		'faq',
		array(
			'labels'       => estatein_cpt_labels( __( 'FAQ', 'estatein' ), __( 'FAQs', 'estatein' ) ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-editor-help',
			'menu_position' => 24,
			'supports'     => array( 'title', 'editor', 'page-attributes' ),
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'estatein_register_post_types' );

/**
 * Register taxonomies for properties.
 */
function estatein_register_taxonomies() {
	register_taxonomy(
		'property_type',
		'property',
		array(
			'labels'            => estatein_tax_labels( __( 'Property Type', 'estatein' ), __( 'Property Types', 'estatein' ) ),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'property-type', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'property_location',
		'property',
		array(
			'labels'            => estatein_tax_labels( __( 'Location', 'estatein' ), __( 'Locations', 'estatein' ) ),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'location', 'with_front' => false ),
		)
	);

	register_taxonomy(
		'property_status',
		'property',
		array(
			'labels'            => estatein_tax_labels( __( 'Status', 'estatein' ), __( 'Statuses', 'estatein' ) ),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'status', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'estatein_register_taxonomies' );

/**
 * Build a full post-type label set from a singular/plural pair.
 *
 * @param string $single Singular name.
 * @param string $plural Plural name.
 * @return array
 */
function estatein_cpt_labels( $single, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $single,
		'menu_name'          => $plural,
		'add_new'            => __( 'Add New', 'estatein' ),
		/* translators: %s: singular post type name. */
		'add_new_item'       => sprintf( __( 'Add New %s', 'estatein' ), $single ),
		/* translators: %s: singular post type name. */
		'edit_item'          => sprintf( __( 'Edit %s', 'estatein' ), $single ),
		/* translators: %s: singular post type name. */
		'new_item'           => sprintf( __( 'New %s', 'estatein' ), $single ),
		/* translators: %s: singular post type name. */
		'view_item'          => sprintf( __( 'View %s', 'estatein' ), $single ),
		/* translators: %s: plural post type name. */
		'search_items'       => sprintf( __( 'Search %s', 'estatein' ), $plural ),
		/* translators: %s: lowercase plural post type name. */
		'not_found'          => sprintf( __( 'No %s found', 'estatein' ), strtolower( $plural ) ),
		/* translators: %s: lowercase plural post type name. */
		'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'estatein' ), strtolower( $plural ) ),
		'all_items'          => $plural,
	);
}

/**
 * Build a taxonomy label set.
 *
 * @param string $single Singular name.
 * @param string $plural Plural name.
 * @return array
 */
function estatein_tax_labels( $single, $plural ) {
	return array(
		'name'          => $plural,
		'singular_name' => $single,
		/* translators: %s: plural taxonomy name. */
		'search_items'  => sprintf( __( 'Search %s', 'estatein' ), $plural ),
		'all_items'     => $plural,
		/* translators: %s: singular taxonomy name. */
		'edit_item'     => sprintf( __( 'Edit %s', 'estatein' ), $single ),
		/* translators: %s: singular taxonomy name. */
		'add_new_item'  => sprintf( __( 'Add New %s', 'estatein' ), $single ),
		'menu_name'     => $plural,
	);
}

/**
 * Show 9 properties per archive page (a clean 3×3 grid).
 *
 * @param WP_Query $query Main query.
 */
function estatein_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'property' ) || $query->is_tax( array( 'property_type', 'property_location', 'property_status' ) ) ) {
		$query->set( 'posts_per_page', 9 );
	}
}
add_action( 'pre_get_posts', 'estatein_archive_query' );

/**
 * Flush rewrite rules once after the theme is activated.
 *
 * Registering post types alone does not create their permalink rules.
 */
function estatein_flush_rewrites() {
	estatein_register_post_types();
	estatein_register_taxonomies();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'estatein_flush_rewrites' );

/**
 * Add a Price column to the Properties list table.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function estatein_property_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['estatein_price'] = __( 'Price', 'estatein' );
		}
	}
	return $new;
}
add_filter( 'manage_property_posts_columns', 'estatein_property_columns' );

/**
 * Render the Price column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function estatein_property_column_content( $column, $post_id ) {
	if ( 'estatein_price' === $column ) {
		$price = estatein_field( 'price', $post_id );
		echo $price ? esc_html( estatein_format_price( $price ) ) : '—';
	}
}
add_action( 'manage_property_posts_custom_column', 'estatein_property_column_content', 10, 2 );
