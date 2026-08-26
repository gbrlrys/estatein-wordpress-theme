<?php
/**
 * Content resolution + design-accurate defaults.
 *
 * Every section renders from a normalised array rather than straight from
 * WP_Post objects. That gives two things:
 *
 *   1. Templates stay simple — one shape, whatever the source.
 *   2. A brand-new install still renders the design instead of a row of empty
 *      boxes. As soon as the editor publishes real content, the defaults stop
 *      being used automatically.
 *
 * The defaults are seed/demo data only. Nothing here overrides published
 * content, and each resolver is filterable.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL for a bundled demo image.
 *
 * @param string $file File name inside assets/img/.
 * @return string
 */
function estatein_img( $file ) {
	return ESTATEIN_URI . '/assets/img/' . ltrim( $file, '/' );
}

/* =========================================================================
 * Resolvers — real content first, defaults second.
 * ====================================================================== */

/**
 * Get properties, normalised.
 *
 * @param int   $limit Maximum items.
 * @param array $args  Extra WP_Query args (e.g. meta_query for featured).
 * @return array[]
 */
function estatein_get_properties( $limit = 6, $args = array() ) {
	$query = new WP_Query(
		array_merge(
			array(
				'post_type'              => 'property',
				'posts_per_page'         => $limit,
				'post_status'            => 'publish',
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
			),
			$args
		)
	);

	if ( ! $query->have_posts() ) {
		return array_slice( estatein_default_properties(), 0, $limit );
	}

	$items = array();
	foreach ( $query->posts as $post ) {
		$items[] = estatein_normalise_property( $post );
	}

	return $items;
}

/**
 * Convert a property post into the shape templates expect.
 *
 * @param WP_Post $post Property post.
 * @return array
 */
function estatein_normalise_property( $post ) {
	$type_terms = get_the_terms( $post->ID, 'property_type' );
	$badge      = ( $type_terms && ! is_wp_error( $type_terms ) ) ? $type_terms[0]->name : '';

	return array(
		'id'        => $post->ID,
		'title'     => get_the_title( $post ),
		'url'       => get_permalink( $post ),
		'excerpt'   => get_the_excerpt( $post ),
		'image'     => get_the_post_thumbnail_url( $post, 'estatein-card' ),
		'image_id'  => get_post_thumbnail_id( $post ),
		'price'     => estatein_field( 'price', $post->ID ),
		'bedrooms'  => estatein_field( 'bedrooms', $post->ID ),
		'bathrooms' => estatein_field( 'bathrooms', $post->ID ),
		'area'      => estatein_field( 'area', $post->ID ),
		'address'   => estatein_field( 'address', $post->ID ),
		'badge'     => $badge,
	);
}

/**
 * Get testimonials, normalised.
 *
 * @param int $limit Maximum items.
 * @return array[]
 */
function estatein_get_testimonials( $limit = 6 ) {
	$posts = get_posts(
		array(
			'post_type'      => 'testimonial',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
		)
	);

	if ( ! $posts ) {
		return array_slice( estatein_default_testimonials(), 0, $limit );
	}

	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'id'       => $post->ID,
			'headline' => get_the_title( $post ),
			'quote'    => wp_strip_all_tags( $post->post_content ),
			'name'     => estatein_field( 'client_name', $post->ID, get_the_title( $post ) ),
			'location' => estatein_field( 'client_location', $post->ID ),
			'rating'   => (int) estatein_field( 'rating', $post->ID, 5 ),
			'avatar'   => get_the_post_thumbnail_url( $post, 'estatein-avatar' ),
		);
	}

	return $items;
}

/**
 * Get FAQs, normalised.
 *
 * @param int $limit Maximum items.
 * @return array[]
 */
function estatein_get_faqs( $limit = 6 ) {
	$posts = get_posts(
		array(
			'post_type'      => 'faq',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		)
	);

	if ( ! $posts ) {
		return array_slice( estatein_default_faqs(), 0, $limit );
	}

	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'id'       => $post->ID,
			'question' => get_the_title( $post ),
			'answer'   => wp_strip_all_tags( $post->post_content ),
		);
	}

	return $items;
}

/**
 * Get team members, normalised.
 *
 * @param int $limit Maximum items.
 * @return array[]
 */
function estatein_get_team( $limit = 8 ) {
	$posts = get_posts(
		array(
			'post_type'      => 'team_member',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);

	if ( ! $posts ) {
		return array_slice( estatein_default_team(), 0, $limit );
	}

	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'id'       => $post->ID,
			'name'     => get_the_title( $post ),
			'role'     => estatein_field( 'role', $post->ID ),
			'photo'    => get_the_post_thumbnail_url( $post, 'estatein-square' ),
			'linkedin' => estatein_field( 'linkedin', $post->ID ),
		);
	}

	return $items;
}

/**
 * Get services, normalised.
 *
 * @param int $limit Maximum items.
 * @return array[]
 */
function estatein_get_services( $limit = 6 ) {
	$posts = get_posts(
		array(
			'post_type'      => 'service',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);

	if ( ! $posts ) {
		return array_slice( estatein_default_services(), 0, $limit );
	}

	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'id'    => $post->ID,
			'title' => get_the_title( $post ),
			'text'  => get_the_excerpt( $post ),
			'icon'  => estatein_field( 'icon', $post->ID, 'building' ),
			'url'   => get_permalink( $post ),
		);
	}

	return $items;
}

/* =========================================================================
 * Defaults
 * ====================================================================== */

/**
 * Seed properties.
 *
 * @return array[]
 */
function estatein_default_properties() {
	$items = array(
		array(
			'title'   => __( 'Seaside Serenity Villa', 'estatein' ),
			'excerpt' => __( 'A stunning 4-bedroom, 3-bathroom villa in a peaceful suburban neighborhood, moments from the shoreline.', 'estatein' ),
			'image'   => estatein_img( 'property-01.jpg' ),
			'price'   => 550000,
			'bedrooms' => 4, 'bathrooms' => 3, 'area' => 2500,
			'address' => 'Malibu, California',
			'badge'   => __( 'Villa', 'estatein' ),
		),
		array(
			'title'   => __( 'Metropolitan Haven', 'estatein' ),
			'excerpt' => __( 'A chic and fully-furnished 2-bedroom apartment with panoramic city views and concierge service.', 'estatein' ),
			'image'   => estatein_img( 'property-02.jpg' ),
			'price'   => 690000,
			'bedrooms' => 2, 'bathrooms' => 2, 'area' => 1450,
			'address' => 'Downtown, Chicago',
			'badge'   => __( 'Apartment', 'estatein' ),
		),
		array(
			'title'   => __( 'Rustic Retreat Cottage', 'estatein' ),
			'excerpt' => __( 'An enchanting 3-bedroom cottage on two acres of woodland, with a wraparound porch and stone fireplace.', 'estatein' ),
			'image'   => estatein_img( 'property-03.jpg' ),
			'price'   => 420000,
			'bedrooms' => 3, 'bathrooms' => 2, 'area' => 1800,
			'address' => 'Aspen, Colorado',
			'badge'   => __( 'Cottage', 'estatein' ),
		),
		array(
			'title'   => __( 'Panoramic Vista Estate', 'estatein' ),
			'excerpt' => __( 'A hillside estate with floor-to-ceiling glazing, infinity pool, and uninterrupted valley views.', 'estatein' ),
			'image'   => estatein_img( 'property-04.jpg' ),
			'price'   => 1250000,
			'bedrooms' => 5, 'bathrooms' => 4, 'area' => 4200,
			'address' => 'Beverly Hills, California',
			'badge'   => __( 'Estate', 'estatein' ),
		),
		array(
			'title'   => __( 'Modern Glass Residence', 'estatein' ),
			'excerpt' => __( 'Architect-designed family home combining warm timber, exposed concrete, and generous natural light.', 'estatein' ),
			'image'   => estatein_img( 'property-05.jpg' ),
			'price'   => 875000,
			'bedrooms' => 4, 'bathrooms' => 3, 'area' => 3100,
			'address' => 'Portland, Oregon',
			'badge'   => __( 'House', 'estatein' ),
		),
		array(
			'title'   => __( 'Suburban Family Home', 'estatein' ),
			'excerpt' => __( 'A welcoming 3-bedroom home on a quiet cul-de-sac, close to schools, parks and transit links.', 'estatein' ),
			'image'   => estatein_img( 'property-06.jpg' ),
			'price'   => 385000,
			'bedrooms' => 3, 'bathrooms' => 2, 'area' => 1950,
			'address' => 'Austin, Texas',
			'badge'   => __( 'House', 'estatein' ),
		),
		array(
			'title'   => __( 'Skyline Penthouse Loft', 'estatein' ),
			'excerpt' => __( 'A double-height loft with private roof terrace, chef’s kitchen, and secure underground parking.', 'estatein' ),
			'image'   => estatein_img( 'property-07.jpg' ),
			'price'   => 1450000,
			'bedrooms' => 3, 'bathrooms' => 3, 'area' => 2800,
			'address' => 'Manhattan, New York',
			'badge'   => __( 'Penthouse', 'estatein' ),
		),
		array(
			'title'   => __( 'Coastal Light Apartment', 'estatein' ),
			'excerpt' => __( 'Bright, minimalist two-bedroom apartment a short walk from the marina and waterfront dining.', 'estatein' ),
			'image'   => estatein_img( 'property-08.jpg' ),
			'price'   => 495000,
			'bedrooms' => 2, 'bathrooms' => 2, 'area' => 1240,
			'address' => 'San Diego, California',
			'badge'   => __( 'Apartment', 'estatein' ),
		),
		array(
			'title'   => __( 'Garden Court Townhouse', 'estatein' ),
			'excerpt' => __( 'A three-storey townhouse with landscaped courtyard, home office, and integrated smart systems.', 'estatein' ),
			'image'   => estatein_img( 'property-09.jpg' ),
			'price'   => 720000,
			'bedrooms' => 4, 'bathrooms' => 3, 'area' => 2350,
			'address' => 'Seattle, Washington',
			'badge'   => __( 'Townhouse', 'estatein' ),
		),
	);

	/*
	 * Give every seed item the keys a real property would have. Seed items have
	 * no permalink of their own, so their links point at the properties index —
	 * that keeps the pre-launch demo navigable instead of rendering dead cards.
	 * Real listings replace this with their own permalink.
	 */
	$fallback_url = estatein_properties_url();

	foreach ( $items as $i => $item ) {
		$items[ $i ] = array_merge(
			array( 'id' => 0, 'url' => $fallback_url, 'image_id' => 0 ),
			$item
		);
	}

	/**
	 * Filter the seed property list.
	 *
	 * @param array[] $items Seed properties.
	 */
	return apply_filters( 'estatein_default_properties', $items );
}

/**
 * Seed testimonials.
 *
 * @return array[]
 */
function estatein_default_testimonials() {
	$items = array(
		array(
			'headline' => __( 'Exceptional service!', 'estatein' ),
			'quote'    => __( 'Estatein made buying our first home genuinely enjoyable. Their team understood exactly what we were looking for and never once pushed us toward something that was not right.', 'estatein' ),
			'name'     => 'Wade Warren',
			'location' => 'USA, California',
			'rating'   => 5,
			'avatar'   => estatein_img( 'avatars/a2.jpg' ),
		),
		array(
			'headline' => __( 'Efficient and reliable', 'estatein' ),
			'quote'    => __( 'From the first viewing to the closing paperwork, every step was clear. We always knew what was happening and what came next.', 'estatein' ),
			'name'     => 'Emelie Thomson',
			'location' => 'USA, Florida',
			'rating'   => 5,
			'avatar'   => estatein_img( 'avatars/a1.jpg' ),
		),
		array(
			'headline' => __( 'Trusted advisors', 'estatein' ),
			'quote'    => __( 'They talked us out of one property and into a better one. That honesty is why we have already recommended them twice.', 'estatein' ),
			'name'     => 'John Mans',
			'location' => 'USA, Nevada',
			'rating'   => 5,
			'avatar'   => estatein_img( 'avatars/a4.jpg' ),
		),
		array(
			'headline' => __( 'Made selling painless', 'estatein' ),
			'quote'    => __( 'Our home was listed on a Thursday and under offer by the following week, above the price we expected.', 'estatein' ),
			'name'     => 'Sarah Nguyen',
			'location' => 'USA, Washington',
			'rating'   => 5,
			'avatar'   => estatein_img( 'avatars/a3.jpg' ),
		),
		array(
			'headline' => __( 'Genuinely knowledgeable', 'estatein' ),
			'quote'    => __( 'The market analysis they prepared was more thorough than anything two other agencies gave us.', 'estatein' ),
			'name'     => 'Marcus Reed',
			'location' => 'USA, Illinois',
			'rating'   => 4,
			'avatar'   => estatein_img( 'avatars/a5.jpg' ),
		),
		array(
			'headline' => __( 'Worth every penny', 'estatein' ),
			'quote'    => __( 'As an overseas investor I needed people I could trust on the ground. Estatein handled everything.', 'estatein' ),
			'name'     => 'Amelia Clarke',
			'location' => 'UK, London',
			'rating'   => 5,
			'avatar'   => estatein_img( 'avatars/a6.jpg' ),
		),
	);

	return apply_filters( 'estatein_default_testimonials', $items );
}

/**
 * Seed FAQs.
 *
 * @return array[]
 */
function estatein_default_faqs() {
	$items = array(
		array(
			'question' => __( 'How do I search for properties on Estatein?', 'estatein' ),
			'answer'   => __( 'Use the search bar on the Properties page to filter by location, property type, price range and number of bedrooms. You can combine filters, and every result links through to a full listing with photography, specifications and a direct enquiry form.', 'estatein' ),
		),
		array(
			'question' => __( 'What documents do I need to sell my property?', 'estatein' ),
			'answer'   => __( 'Typically proof of ownership, a recent utility bill, any warranties or building certificates, and identification. Your assigned agent will send a checklist tailored to your property and jurisdiction before the first viewing.', 'estatein' ),
		),
		array(
			'question' => __( 'How can I contact an Estatein agent?', 'estatein' ),
			'answer'   => __( 'Every listing has a direct enquiry form, or you can reach the team through the Contact page, by phone during office hours, or by email. We aim to respond to all enquiries within one business day.', 'estatein' ),
		),
		array(
			'question' => __( 'Do you handle rental properties as well as sales?', 'estatein' ),
			'answer'   => __( 'Yes. Our property management service covers tenant sourcing and screening, rent collection, maintenance coordination and compliance, whether you own a single unit or a portfolio.', 'estatein' ),
		),
		array(
			'question' => __( 'Is there a fee for an initial consultation?', 'estatein' ),
			'answer'   => __( 'No. The first consultation, including a market appraisal of your property, is free and carries no obligation to list with us.', 'estatein' ),
		),
		array(
			'question' => __( 'Can you help with financing and mortgages?', 'estatein' ),
			'answer'   => __( 'We work with a panel of independent mortgage advisers and can introduce you at no cost. We do not receive commission on referrals, so the advice you get is genuinely impartial.', 'estatein' ),
		),
	);

	return apply_filters( 'estatein_default_faqs', $items );
}

/**
 * Seed team.
 *
 * @return array[]
 */
function estatein_default_team() {
	$items = array(
		array( 'name' => 'Max Mitchell',   'role' => __( 'Founder & CEO', 'estatein' ),           'photo' => estatein_img( 'team/t1.jpg' ), 'linkedin' => '' ),
		array( 'name' => 'Sarah Johnson',  'role' => __( 'Head of Sales', 'estatein' ),           'photo' => estatein_img( 'team/t2.jpg' ), 'linkedin' => '' ),
		array( 'name' => 'David Brown',    'role' => __( 'Senior Property Advisor', 'estatein' ), 'photo' => estatein_img( 'team/t3.jpg' ), 'linkedin' => '' ),
		array( 'name' => 'Michael Turner', 'role' => __( 'Investment Analyst', 'estatein' ),      'photo' => estatein_img( 'team/t4.jpg' ), 'linkedin' => '' ),
		array( 'name' => 'Emily Carter',   'role' => __( 'Client Relations Lead', 'estatein' ),   'photo' => estatein_img( 'team/t5.jpg' ), 'linkedin' => '' ),
		array( 'name' => 'Olivia Bennett', 'role' => __( 'Legal Counsel', 'estatein' ),           'photo' => estatein_img( 'team/t6.jpg' ), 'linkedin' => '' ),
	);

	return apply_filters( 'estatein_default_team', $items );
}

/**
 * Seed services.
 *
 * @return array[]
 */
function estatein_default_services() {
	$items = array(
		array(
			'icon'  => 'building',
			'title' => __( 'Property Management', 'estatein' ),
			'text'  => __( 'Tenant sourcing, rent collection, maintenance and compliance — handled end to end so your asset stays profitable and problem-free.', 'estatein' ),
		),
		array(
			'icon'  => 'chart',
			'title' => __( 'Smart Investments', 'estatein' ),
			'text'  => __( 'Data-led guidance on where to buy and when to sell, built on local market analysis rather than guesswork.', 'estatein' ),
		),
		array(
			'icon'  => 'scale',
			'title' => __( 'Legal Expertise', 'estatein' ),
			'text'  => __( 'In-house counsel reviews every contract, so contracts, titles and disclosures are checked before you sign anything.', 'estatein' ),
		),
		array(
			'icon'  => 'key',
			'title' => __( 'Buying & Selling', 'estatein' ),
			'text'  => __( 'A dedicated agent from first viewing to handover, with clear communication at every stage of the transaction.', 'estatein' ),
		),
		array(
			'icon'  => 'shield',
			'title' => __( 'Property Valuation', 'estatein' ),
			'text'  => __( 'Free, no-obligation appraisals grounded in comparable sales, local demand and the condition of your property.', 'estatein' ),
		),
		array(
			'icon'  => 'globe',
			'title' => __( 'Relocation Support', 'estatein' ),
			'text'  => __( 'Moving cities or countries? We coordinate viewings, schooling research and settling-in support on your behalf.', 'estatein' ),
		),
	);

	return apply_filters( 'estatein_default_services', $items );
}

/**
 * Company values, used on the About page.
 *
 * @return array[]
 */
function estatein_default_values() {
	return apply_filters(
		'estatein_default_values',
		array(
			array( 'icon' => 'shield', 'title' => __( 'Trust', 'estatein' ),          'text' => __( 'Trust is the bedrock of every relationship we build. We are transparent about price, process and problems.', 'estatein' ) ),
			array( 'icon' => 'star',   'title' => __( 'Excellence', 'estatein' ),     'text' => __( 'We hold ourselves to a standard higher than the market expects, from photography to paperwork.', 'estatein' ) ),
			array( 'icon' => 'users',  'title' => __( 'Client-Centric', 'estatein' ), 'text' => __( 'Your goals set the brief. We advise, we never pressure, and we are happy to tell you not to buy.', 'estatein' ) ),
			array( 'icon' => 'heart',  'title' => __( 'Our Commitment', 'estatein' ), 'text' => __( 'We stay involved after the keys change hands, because a home is a decade-long decision.', 'estatein' ) ),
		)
	);
}

/**
 * The four-step client journey, used on the About page.
 *
 * @return array[]
 */
function estatein_default_steps() {
	return apply_filters(
		'estatein_default_steps',
		array(
			array( 'title' => __( 'Discovery Call', 'estatein' ),      'text' => __( 'We start by understanding your budget, timeline and non-negotiables — before showing you a single property.', 'estatein' ) ),
			array( 'title' => __( 'Curated Shortlist', 'estatein' ),   'text' => __( 'You receive a hand-picked shortlist, including off-market listings, with honest notes on each one.', 'estatein' ) ),
			array( 'title' => __( 'Viewings & Advice', 'estatein' ),   'text' => __( 'We attend every viewing with you, flagging the things that are easy to miss and costly to fix.', 'estatein' ) ),
			array( 'title' => __( 'Offer & Completion', 'estatein' ),  'text' => __( 'We negotiate on your behalf and manage the legal process through to a confirmed completion date.', 'estatein' ) ),
		)
	);
}

/**
 * Company statistics shown in the hero and About page.
 *
 * @return array[]
 */
function estatein_default_stats() {
	return apply_filters(
		'estatein_default_stats',
		array(
			array( 'value' => '200+', 'label' => __( 'Happy Customers', 'estatein' ) ),
			array( 'value' => '10k+', 'label' => __( 'Properties For Clients', 'estatein' ) ),
			array( 'value' => '16+',  'label' => __( 'Years of Experience', 'estatein' ) ),
		)
	);
}

/**
 * Client logos for the "Our Valued Clients" wall.
 *
 * @return string[]
 */
function estatein_default_clients() {
	return apply_filters(
		'estatein_default_clients',
		array( 'Zenith Group', 'Vertex Homes', 'Northstar', 'Arcadia', 'Blue Harbour', 'Meridian' )
	);
}
