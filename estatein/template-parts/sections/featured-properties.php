<?php
/**
 * Featured properties carousel.
 *
 * @param array $args {
 *     @type string $title Section title.
 *     @type string $text  Supporting copy.
 *     @type int    $limit How many to show.
 * }
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$title = isset( $args['title'] ) ? $args['title'] : __( 'Featured Properties', 'estatein' );
$text  = isset( $args['text'] ) ? $args['text'] : __( 'Explore our hand-picked selection of homes currently on the market. Each one has been visited, photographed and verified by our team.', 'estatein' );
$limit = isset( $args['limit'] ) ? (int) $args['limit'] : 6;

$properties = estatein_get_properties( $limit );

if ( ! $properties ) {
	return;
}
?>

<section class="section rule" aria-labelledby="featured-heading">
	<div class="container">

		<?php
		ob_start();
		?>
		<a class="btn" href="<?php echo esc_url( estatein_properties_url() ); ?>">
			<?php esc_html_e( 'View All Properties', 'estatein' ); ?>
			<?php estatein_icon( 'arrow-right', array( 'size' => 16, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
		</a>
		<?php
		$action = ob_get_clean();

		estatein_section_head(
			array(
				'title'  => $title,
				'text'   => $text,
				'action' => $action,
			)
		);
		?>

		<h2 id="featured-heading" class="sr-only"><?php echo esc_html( $title ); ?></h2>

		<div class="track" id="featured-track" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Featured properties', 'estatein' ); ?>">
			<?php foreach ( $properties as $i => $item ) : ?>
				<?php get_template_part( 'template-parts/cards/property-card', null, array( 'item' => $item, 'index' => $i ) ); ?>
			<?php endforeach; ?>
		</div>

		<div class="mt-6" style="display:flex;justify-content:flex-end">
			<?php estatein_carousel_nav( 'featured-track', count( $properties ) ); ?>
		</div>

	</div>
</section>
