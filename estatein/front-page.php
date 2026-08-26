<?php
/**
 * Home page.
 *
 * Composed entirely from section partials so the same blocks can be reused on
 * inner pages without duplication.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php get_template_part( 'template-parts/sections/hero' ); ?>

<?php
get_template_part(
	'template-parts/sections/featured-properties',
	null,
	array( 'limit' => 6 )
);
?>

<?php get_template_part( 'template-parts/sections/clients' ); ?>

<?php
get_template_part(
	'template-parts/sections/services',
	null,
	array(
		'title' => __( 'How We Can Help', 'estatein' ),
		'limit' => 3,
	)
);
?>

<?php get_template_part( 'template-parts/sections/testimonials' ); ?>

<?php get_template_part( 'template-parts/sections/faq' ); ?>

<?php get_template_part( 'template-parts/sections/cta' ); ?>

<?php
get_footer();
