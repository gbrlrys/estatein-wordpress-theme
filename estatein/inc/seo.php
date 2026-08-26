<?php
/**
 * Baseline SEO.
 *
 * Deliberately lightweight: meta description, canonical, Open Graph/Twitter
 * cards and JSON-LD. Every output is guarded by a filter or a plugin check so
 * installing Yoast or Rank Math later does not produce duplicate tags.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a dedicated SEO plugin handling meta output?
 *
 * @return bool
 */
function estatein_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' )          // Yoast.
		|| class_exists( 'RankMath' )          // Rank Math.
		|| defined( 'AIOSEO_VERSION' )         // All in One SEO.
		|| defined( 'SEOPRESS_VERSION' );      // SEOPress.
}

/**
 * Build a description for the current view.
 *
 * @return string
 */
function estatein_meta_description() {
	$description = '';

	if ( is_singular() ) {
		$post = get_queried_object();

		if ( has_excerpt( $post ) ) {
			$description = get_the_excerpt( $post );
		} else {
			$description = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
		}
	} elseif ( is_post_type_archive() ) {
		$obj         = get_queried_object();
		$description = ( $obj && ! empty( $obj->description ) ) ? $obj->description : get_bloginfo( 'description' );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$description = wp_strip_all_tags( term_description() );
	}

	if ( ! $description ) {
		$description = get_bloginfo( 'description' );
	}

	return trim( wp_strip_all_tags( $description ) );
}

/**
 * The best available share image for the current view.
 *
 * @return string
 */
function estatein_share_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$url = get_the_post_thumbnail_url( get_the_ID(), 'estatein-wide' );
		if ( $url ) {
			return $url;
		}
	}

	$custom = estatein_option( 'default_share_image', '' );
	if ( $custom ) {
		return $custom;
	}

	return estatein_img( 'hero-home.jpg' );
}

/**
 * Output meta description, canonical, OG and Twitter tags.
 */
function estatein_head_meta() {
	if ( estatein_seo_plugin_active() ) {
		return;
	}

	$description = estatein_meta_description();
	$title       = wp_get_document_title();
	$image       = estatein_share_image();

	$canonical = '';
	if ( is_singular() ) {
		$canonical = get_permalink();
	} elseif ( is_post_type_archive() ) {
		$canonical = get_post_type_archive_link( get_query_var( 'post_type' ) );
	} elseif ( is_home() ) {
		$canonical = home_url( '/' );
	}

	echo "\n<!-- Estatein SEO -->\n";

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	}

	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( 'post' ) ? 'article' : 'website' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );

	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $canonical ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
	}

	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}

	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );

	if ( $description ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	echo "<!-- /Estatein SEO -->\n\n";
}
add_action( 'wp_head', 'estatein_head_meta', 5 );

/**
 * Structured data.
 *
 * RealEstateAgent for the organisation, plus Residence/Offer on a property and
 * FAQPage wherever the accordion is rendered.
 */
function estatein_json_ld() {
	$graph = array();

	$graph[] = array(
		'@type'       => 'RealEstateAgent',
		'@id'         => home_url( '/#organization' ),
		'name'        => get_bloginfo( 'name' ),
		'url'         => home_url( '/' ),
		'description' => get_bloginfo( 'description' ),
		'image'       => estatein_img( 'hero-home.jpg' ),
		'telephone'   => estatein_option( 'contact_phone', '+1 (555) 000-0000' ),
		'email'       => estatein_option( 'contact_email', get_option( 'admin_email' ) ),
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => estatein_option( 'contact_street', '2158 Mount Tabor' ),
			'addressLocality' => estatein_option( 'contact_city', 'Los Angeles' ),
			'addressRegion'   => estatein_option( 'contact_region', 'CA' ),
			'addressCountry'  => estatein_option( 'contact_country', 'US' ),
		),
	);

	if ( is_singular( 'property' ) ) {
		$price = (float) estatein_field( 'price' );

		$property = array(
			'@type'       => 'Residence',
			'name'        => get_the_title(),
			'url'         => get_permalink(),
			'description' => estatein_meta_description(),
			'image'       => estatein_share_image(),
		);

		$rooms = (int) estatein_field( 'bedrooms' );
		if ( $rooms ) {
			$property['numberOfRooms'] = $rooms;
		}

		$area = (float) estatein_field( 'area' );
		if ( $area ) {
			$property['floorSize'] = array(
				'@type'    => 'QuantitativeValue',
				'value'    => $area,
				'unitCode' => 'FTK',
			);
		}

		if ( $price > 0 ) {
			$property['offers'] = array(
				'@type'         => 'Offer',
				'price'         => $price,
				'priceCurrency' => estatein_option( 'currency_code', 'USD' ),
				'availability'  => 'https://schema.org/InStock',
			);
		}

		$graph[] = $property;
	}

	if ( is_front_page() || is_page_template( 'templates/page-contact.php' ) ) {
		$faqs   = estatein_get_faqs( 6 );
		$entity = array();

		foreach ( $faqs as $faq ) {
			$entity[] = array(
				'@type'          => 'Question',
				'name'           => $faq['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $faq['answer'],
				),
			);
		}

		if ( $entity ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'mainEntity' => $entity,
			);
		}
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}
add_action( 'wp_head', 'estatein_json_ld', 20 );

/**
 * Keep search-engine-unfriendly views out of the index.
 */
function estatein_noindex_meta() {
	if ( is_search() || is_404() || is_paged() && is_home() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}
}
add_action( 'wp_head', 'estatein_noindex_meta', 4 );

/**
 * Add the property post type to the WordPress core sitemap.
 *
 * @param array $post_types Included post types.
 * @return array
 */
function estatein_sitemap_post_types( $post_types ) {
	foreach ( array( 'property', 'service' ) as $type ) {
		$object = get_post_type_object( $type );
		if ( $object ) {
			$post_types[ $type ] = $object;
		}
	}
	return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'estatein_sitemap_post_types' );
