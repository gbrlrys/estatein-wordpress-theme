<?php
/**
 * Template Name: Properties
 *
 * Search + filter bar over a paginated grid of listings.
 *
 * Note: when the `property` post type archive shares this page's slug,
 * WordPress serves archive-property.php instead. Both render the same
 * property-index part, so the visitor sees the same page either way.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part(
	'template-parts/sections/page-hero',
	null,
	array(
		'title' => get_the_title(),
		'text'  => __( 'Every listing below has been visited and verified by an Estatein agent. Filter by what matters to you.', 'estatein' ),
	)
);

get_template_part( 'template-parts/sections/property-index' );

get_template_part( 'template-parts/sections/cta' );

get_footer();
