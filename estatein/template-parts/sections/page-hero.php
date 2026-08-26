<?php
/**
 * Inner-page hero with breadcrumbs.
 *
 * @param array $args {
 *     @type string $title Page title. Defaults to the queried title.
 *     @type string $text  Supporting copy.
 * }
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$title = isset( $args['title'] ) && $args['title'] ? $args['title'] : get_the_title();
$text  = isset( $args['text'] ) ? $args['text'] : '';
?>

<section class="page-hero">
	<div class="container">
		<div class="page-hero__inner">
			<?php estatein_breadcrumbs(); ?>
			<h1 data-reveal><?php echo esc_html( $title ); ?></h1>
			<?php if ( $text ) : ?>
				<p class="lead" data-reveal style="--reveal-delay:80ms"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
