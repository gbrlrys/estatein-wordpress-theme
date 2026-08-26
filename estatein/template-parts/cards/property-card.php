<?php
/**
 * Property card.
 *
 * @param array $args {
 *     @type array $item  Normalised property (see estatein_normalise_property).
 *     @type int   $index Position, used to stagger the reveal animation.
 * }
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$item = isset( $args['item'] ) ? $args['item'] : array();

if ( empty( $item['title'] ) ) {
	return;
}

$index = isset( $args['index'] ) ? (int) $args['index'] : 0;
$url   = ! empty( $item['url'] ) ? $item['url'] : '';
$price = isset( $item['price'] ) ? $item['price'] : 0;
?>

<article
	class="card property-card"
	data-reveal
	style="--reveal-delay: <?php echo esc_attr( min( $index * 70, 420 ) ); ?>ms"
>
	<?php if ( ! empty( $item['image'] ) ) : ?>
		<div class="property-card__media">
			<img
				src="<?php echo esc_url( $item['image'] ); ?>"
				alt="<?php echo esc_attr( $item['title'] ); ?>"
				width="720" height="450"
				loading="<?php echo 0 === $index ? 'eager' : 'lazy'; ?>"
				decoding="async"
			>
			<?php if ( ! empty( $item['badge'] ) ) : ?>
				<span class="property-card__badge"><?php echo esc_html( $item['badge'] ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="property-card__body">
		<h3 class="card__title" style="margin:0">
			<?php if ( $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $item['title'] ); ?>
			<?php endif; ?>
		</h3>

		<?php if ( ! empty( $item['address'] ) ) : ?>
			<p class="property-card__excerpt" style="display:flex;gap:6px;align-items:center;color:var(--grey-50);font-size:.875rem">
				<?php estatein_icon( 'map-pin', array( 'size' => 15 ) ); ?>
				<span><?php echo esc_html( $item['address'] ); ?></span>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $item['excerpt'] ) ) : ?>
			<p class="property-card__excerpt"><?php echo esc_html( wp_strip_all_tags( $item['excerpt'] ) ); ?></p>
		<?php endif; ?>

		<?php
		// Spec pills. Seed items carry the same keys, so this works either way.
		$specs = array(
			array( 'icon' => 'bed',  'value' => isset( $item['bedrooms'] ) ? $item['bedrooms'] : 0,  'suffix' => __( 'Bed', 'estatein' ) ),
			array( 'icon' => 'bath', 'value' => isset( $item['bathrooms'] ) ? $item['bathrooms'] : 0, 'suffix' => __( 'Bath', 'estatein' ) ),
			array( 'icon' => 'area', 'value' => isset( $item['area'] ) ? $item['area'] : 0,           'suffix' => __( 'sq ft', 'estatein' ) ),
		);
		?>
		<ul class="property-card__specs">
			<?php foreach ( $specs as $spec ) : ?>
				<?php if ( ! $spec['value'] ) { continue; } ?>
				<li class="pill">
					<?php estatein_icon( $spec['icon'] ); ?>
					<span><?php echo esc_html( number_format_i18n( (float) $spec['value'] ) . ' ' . $spec['suffix'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="property-card__footer">
			<div>
				<span class="property-card__price-label"><?php esc_html_e( 'Price', 'estatein' ); ?></span>
				<span class="property-card__price" title="<?php echo esc_attr( estatein_format_price( $price ) ); ?>">
					<?php echo esc_html( estatein_format_price( $price, true ) ); ?>
				</span>
			</div>

			<?php if ( $url ) : ?>
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $url ); ?>">
					<?php esc_html_e( 'View', 'estatein' ); ?>
					<span class="sr-only"><?php echo esc_html( $item['title'] ); ?></span>
					<?php estatein_icon( 'arrow-right', array( 'size' => 16, 'class' => 'btn__icon btn__icon--arrow' ) ); ?>
				</a>
			<?php else : ?>
				<span class="btn btn--sm" aria-hidden="true"><?php esc_html_e( 'View', 'estatein' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</article>
