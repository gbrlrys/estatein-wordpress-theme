<?php
/**
 * Navigation: accessible walker + a sensible fallback menu.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nav walker that adds the ARIA attributes WordPress leaves out.
 */
class Estatein_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Mark sub-menus as lists for assistive tech.
	 *
	 * @param string   $output Menu HTML, by reference.
	 * @param int      $depth  Current depth.
	 * @param stdClass $args   Menu args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"sub-menu\">\n";
	}

	/**
	 * Render one item, adding aria-current and submenu affordances.
	 *
	 * @param string   $output Menu HTML, by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Current depth.
	 * @param stdClass $args   Menu args.
	 * @param int      $id     Menu ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$has_children = in_array( 'menu-item-has-children', $classes, true );

		$class_names = join( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );
		$output     .= sprintf( '<li class="%s">', esc_attr( $class_names ) );

		$current = in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true );

		$atts = array(
			'href'         => ! empty( $item->url ) ? $item->url : '#',
			'title'        => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target'       => ! empty( $item->target ) ? $item->target : '',
			'rel'          => ! empty( $item->xfn ) ? $item->xfn : '',
			'aria-current' => $current ? 'page' : '',
		);

		// Never leak a referrer or allow tab-nabbing on new-window links.
		if ( '_blank' === $atts['target'] && empty( $atts['rel'] ) ) {
			$atts['rel'] = 'noopener noreferrer';
		}

		if ( $has_children && 0 === $depth ) {
			$atts['aria-haspopup'] = 'true';
		}

		$attributes = '';
		foreach ( $atts as $key => $value ) {
			if ( '' !== $value && false !== $value ) {
				$value       = ( 'href' === $key ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $key . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );

		$item_output  = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
		if ( $has_children && 0 === $depth ) {
			$item_output .= estatein_get_icon( 'chevron-down', array( 'size' => 14, 'class' => 'menu-caret' ) );
		}
		$item_output .= '</a>';
		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/**
 * Fallback navigation.
 *
 * Shown when no menu has been assigned to the `primary` location, so a fresh
 * install still gets a working, design-accurate header. Each link resolves to
 * a real page when one exists and is skipped when it does not.
 */
function estatein_fallback_menu() {
	$links = estatein_primary_links();

	echo '<ul>';
	foreach ( $links as $link ) {
		$is_current = untrailingslashit( $link['url'] ) === untrailingslashit( home_url( add_query_arg( array() ) ) );

		printf(
			'<li class="%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
			esc_attr( trim( ( $is_current ? 'is-current ' : '' ) . ( ! empty( $link['cta'] ) ? 'menu-cta' : '' ) ) ),
			esc_url( $link['url'] ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $link['label'] )
		);
	}
	echo '</ul>';
}

/**
 * The canonical primary-navigation link set.
 *
 * Used by the fallback menu and the footer so both stay in sync.
 *
 * @return array[] Each item: label, url, cta (bool).
 */
function estatein_primary_links() {
	$map = array(
		'about'      => __( 'About Us', 'estatein' ),
		'properties' => __( 'Properties', 'estatein' ),
		'services'   => __( 'Services', 'estatein' ),
	);

	$links = array(
		array(
			'label' => __( 'Home', 'estatein' ),
			'url'   => home_url( '/' ),
			'cta'   => false,
		),
	);

	foreach ( $map as $slug => $label ) {
		$page = get_page_by_path( $slug );

		// Properties has a post-type archive even when no page exists.
		if ( ! $page && 'properties' === $slug ) {
			$archive = get_post_type_archive_link( 'property' );
			if ( $archive ) {
				$links[] = array( 'label' => $label, 'url' => $archive, 'cta' => false );
			}
			continue;
		}

		if ( $page ) {
			$links[] = array( 'label' => $label, 'url' => get_permalink( $page ), 'cta' => false );
		}
	}

	$contact = get_page_by_path( 'contact' );
	$links[] = array(
		'label' => __( 'Contact Us', 'estatein' ),
		'url'   => $contact ? get_permalink( $contact ) : home_url( '/contact/' ),
		'cta'   => true,
	);

	/**
	 * Filter the fallback navigation links.
	 *
	 * @param array[] $links Link definitions.
	 */
	return apply_filters( 'estatein_primary_links', $links );
}

/**
 * The URL the header "Contact Us" button points at.
 *
 * @return string
 */
function estatein_contact_url() {
	$contact = get_page_by_path( 'contact' );
	return $contact ? get_permalink( $contact ) : home_url( '/contact/' );
}

/**
 * The URL of the properties index.
 *
 * @return string
 */
function estatein_properties_url() {
	$page = get_page_by_path( 'properties' );
	if ( $page ) {
		return get_permalink( $page );
	}

	$archive = get_post_type_archive_link( 'property' );
	return $archive ? $archive : home_url( '/properties/' );
}
