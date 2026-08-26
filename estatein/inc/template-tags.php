<?php
/**
 * Template tags — small render helpers shared across templates.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Format a numeric price for display.
 *
 * Large values are abbreviated (1.2M / 850K) because the card layout only has
 * room for a short string; the full value stays in the title attribute.
 *
 * @param mixed $value    Raw price.
 * @param bool  $abbrev   Abbreviate millions/thousands.
 * @return string
 */
function estatein_format_price( $value, $abbrev = false ) {
	$value = (float) $value;

	if ( $value <= 0 ) {
		return __( 'Price on request', 'estatein' );
	}

	$currency = estatein_option( 'currency_symbol', '$' );

	if ( $abbrev ) {
		if ( $value >= 1000000 ) {
			return $currency . rtrim( rtrim( number_format( $value / 1000000, 2 ), '0' ), '.' ) . 'M';
		}
		if ( $value >= 1000 ) {
			return $currency . rtrim( rtrim( number_format( $value / 1000, 1 ), '0' ), '.' ) . 'K';
		}
	}

	return $currency . number_format( $value );
}

/**
 * Render a section heading block.
 *
 * @param array $args {
 *     @type string $title    Heading text (required).
 *     @type string $text     Supporting paragraph.
 *     @type string $action   Optional CTA markup placed on the right.
 *     @type string $tag      Heading level. Default h2.
 *     @type bool   $star     Show the sparkle mark above the title.
 *     @type string $class    Extra classes on the wrapper.
 * }
 */
function estatein_section_head( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'title'  => '',
			'text'   => '',
			'action' => '',
			'tag'    => 'h2',
			'star'   => true,
			'class'  => '',
		)
	);

	if ( ! $args['title'] ) {
		return;
	}

	$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
	?>
	<div class="section-head <?php echo esc_attr( $args['class'] ); ?>" data-reveal>
		<div class="section-head__text">
			<?php if ( $args['star'] ) : ?>
				<?php estatein_icon( 'sparkle', array( 'class' => 'eyebrow-star' ) ); ?>
			<?php endif; ?>
			<<?php echo esc_html( $tag ); ?>><?php echo wp_kses_post( $args['title'] ); ?></<?php echo esc_html( $tag ); ?>>
			<?php if ( $args['text'] ) : ?>
				<p class="lead"><?php echo wp_kses_post( $args['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $args['action'] ) : ?>
			<div class="section-head__action"><?php echo wp_kses_post( $args['action'] ); ?></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render a 5-star rating.
 *
 * @param int $rating Filled stars, 0–5.
 */
function estatein_rating( $rating ) {
	$rating = max( 0, min( 5, (int) $rating ) );
	printf(
		'<div class="rating" role="img" aria-label="%s">',
		/* translators: %d: star rating out of five. */
		esc_attr( sprintf( __( 'Rated %d out of 5', 'estatein' ), $rating ) )
	);
	for ( $i = 1; $i <= 5; $i++ ) {
		$class = $i <= $rating ? '' : 'is-empty';
		echo str_replace(
			'<svg ',
			'<svg class="' . esc_attr( $class ) . '" ',
			estatein_get_icon( 'star', array( 'size' => 18 ) )
		); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Internal markup.
	}
	echo '</div>';
}

/**
 * Accessible breadcrumb trail.
 */
function estatein_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = array(
		array(
			'label' => __( 'Home', 'estatein' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular( 'property' ) ) {
		$archive = get_post_type_archive_link( 'property' );
		if ( $archive ) {
			$items[] = array( 'label' => __( 'Properties', 'estatein' ), 'url' => $archive );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_post_type_archive() ) {
		$items[] = array( 'label' => post_type_archive_title( '', false ), 'url' => '' );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$items[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
	} elseif ( is_singular( 'post' ) ) {
		$blog = get_permalink( get_option( 'page_for_posts' ) );
		if ( $blog ) {
			$items[] = array( 'label' => __( 'Blog', 'estatein' ), 'url' => $blog );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_search() ) {
		$items[] = array( 'label' => __( 'Search results', 'estatein' ), 'url' => '' );
	} elseif ( is_404() ) {
		$items[] = array( 'label' => __( 'Not found', 'estatein' ), 'url' => '' );
	} elseif ( is_page() ) {
		$parent_id = wp_get_post_parent_id( get_the_ID() );
		if ( $parent_id ) {
			$items[] = array( 'label' => get_the_title( $parent_id ), 'url' => get_permalink( $parent_id ) );
		}
		$items[] = array( 'label' => get_the_title(), 'url' => '' );
	}

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'estatein' ) . '">';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $item ) {
		if ( $i > 0 ) {
			echo '<span class="sep" aria-hidden="true">/</span>';
		}
		if ( $item['url'] && $i !== $last ) {
			printf( '<a href="%s">%s</a>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
		} else {
			printf( '<span aria-current="page">%s</span>', esc_html( $item['label'] ) );
		}
	}
	echo '</nav>';
}

/**
 * Themed pagination for archives.
 */
function estatein_pagination() {
	$links = paginate_links(
		array(
			'type'      => 'array',
			'mid_size'  => 1,
			'prev_text' => estatein_get_icon( 'arrow-left', array( 'size' => 16 ) ) . '<span class="sr-only">' . esc_html__( 'Previous page', 'estatein' ) . '</span>',
			'next_text' => estatein_get_icon( 'arrow-right', array( 'size' => 16 ) ) . '<span class="sr-only">' . esc_html__( 'Next page', 'estatein' ) . '</span>',
		)
	);

	if ( empty( $links ) ) {
		return;
	}

	echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'estatein' ) . '">';
	foreach ( $links as $link ) {
		echo wp_kses_post( $link );
	}
	echo '</nav>';
}

/**
 * Carousel navigation arrows + counter.
 *
 * @param string $target  ID of the .track element the buttons control.
 * @param int    $total   Number of slides, for the counter.
 */
function estatein_carousel_nav( $target, $total = 0 ) {
	?>
	<div class="carousel-nav" data-carousel-nav="<?php echo esc_attr( $target ); ?>">
		<?php if ( $total ) : ?>
			<p class="carousel-nav__count">
				<b data-carousel-current>01</b>
				<span><?php echo esc_html__( 'of', 'estatein' ); ?></span>
				<span><?php echo esc_html( str_pad( (string) $total, 2, '0', STR_PAD_LEFT ) ); ?></span>
			</p>
		<?php endif; ?>
		<button type="button" class="icon-btn" data-carousel-prev aria-controls="<?php echo esc_attr( $target ); ?>">
			<?php estatein_icon( 'arrow-left', array( 'size' => 18 ) ); ?>
			<span class="sr-only"><?php esc_html_e( 'Previous', 'estatein' ); ?></span>
		</button>
		<button type="button" class="icon-btn" data-carousel-next aria-controls="<?php echo esc_attr( $target ); ?>">
			<?php estatein_icon( 'arrow-right', array( 'size' => 18 ) ); ?>
			<span class="sr-only"><?php esc_html_e( 'Next', 'estatein' ); ?></span>
		</button>
	</div>
	<?php
}

/**
 * Property spec pills (beds / baths / area).
 *
 * @param int|null $post_id Property ID.
 */
function estatein_property_specs( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	$specs = array(
		'bed'  => array(
			'value' => estatein_field( 'bedrooms', $post_id ),
			/* translators: %s: number of bedrooms. */
			'text'  => _n_noop( '%s Bedroom', '%s Bedrooms', 'estatein' ),
		),
		'bath' => array(
			'value' => estatein_field( 'bathrooms', $post_id ),
			/* translators: %s: number of bathrooms. */
			'text'  => _n_noop( '%s Bathroom', '%s Bathrooms', 'estatein' ),
		),
	);

	echo '<ul class="property-card__specs">';

	foreach ( $specs as $icon => $spec ) {
		if ( ! $spec['value'] ) {
			continue;
		}
		$n = (int) $spec['value'];
		printf(
			'<li class="pill">%s<span>%s</span></li>',
			estatein_get_icon( $icon ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( sprintf( translate_nooped_plural( $spec['text'], $n, 'estatein' ), number_format_i18n( $n ) ) )
		);
	}

	$area = estatein_field( 'area', $post_id );
	if ( $area ) {
		printf(
			'<li class="pill">%s<span>%s</span></li>',
			estatein_get_icon( 'area' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			/* translators: %s: floor area in square feet. */
			esc_html( sprintf( __( '%s sq ft', 'estatein' ), number_format_i18n( (float) $area ) ) )
		);
	}

	echo '</ul>';
}

/**
 * A safe, escaped "read more" label that names its target for screen readers.
 *
 * @param string $label Visible text.
 * @return string
 */
function estatein_sr_context( $label = '' ) {
	return sprintf(
		'<span class="sr-only"> %s</span>',
		esc_html( $label ? $label : get_the_title() )
	);
}

/**
 * Estimate reading time for a post.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function estatein_reading_time( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$words   = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
	$minutes = max( 1, (int) ceil( $words / 200 ) );

	/* translators: %d: estimated reading time in minutes. */
	return sprintf( _n( '%d min read', '%d min read', $minutes, 'estatein' ), $minutes );
}
