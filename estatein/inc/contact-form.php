<?php
/**
 * Contact and newsletter form handling.
 *
 * Submissions post to admin-ajax over fetch(), with a nonce, honeypot and a
 * per-IP rate limit. Every message is also stored as a private CPT entry so an
 * enquiry is never lost to a bounced email.
 *
 * The markup posts normally when JavaScript is unavailable — see the
 * non-AJAX handler at the bottom — so the form is never a dead end.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

/**
 * Store enquiries as a private post type.
 */
function estatein_register_enquiry_cpt() {
	register_post_type(
		'estatein_enquiry',
		array(
			'labels'          => array(
				'name'          => __( 'Enquiries', 'estatein' ),
				'singular_name' => __( 'Enquiry', 'estatein' ),
				'menu_name'     => __( 'Enquiries', 'estatein' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'estatein_register_enquiry_cpt' );

/**
 * Validate and process a form submission.
 *
 * @param array $data Raw $_POST data.
 * @return array{success:bool,message:string,errors:array}
 */
function estatein_process_submission( $data ) {
	$errors = array();

	// Honeypot: a real user never fills a hidden field.
	if ( ! empty( $data['estatein_website'] ) ) {
		// Return success so bots do not learn they were caught.
		return array( 'success' => true, 'message' => __( 'Thank you — your message has been sent.', 'estatein' ), 'errors' => array() );
	}

	$form_type = isset( $data['form_type'] ) ? sanitize_key( $data['form_type'] ) : 'contact';

	$name    = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
	$email   = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
	$phone   = isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '';
	$subject = isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : '';
	$message = isset( $data['message'] ) ? sanitize_textarea_field( $data['message'] ) : '';

	if ( ! $email || ! is_email( $email ) ) {
		$errors['email'] = __( 'Please enter a valid email address.', 'estatein' );
	}

	if ( 'newsletter' !== $form_type ) {
		if ( '' === $name ) {
			$errors['name'] = __( 'Please tell us your name.', 'estatein' );
		}
		if ( '' === $message ) {
			$errors['message'] = __( 'Please enter a message.', 'estatein' );
		}
	}

	if ( $errors ) {
		return array(
			'success' => false,
			'message' => __( 'Please correct the highlighted fields.', 'estatein' ),
			'errors'  => $errors,
		);
	}

	// Simple per-IP throttle: 5 submissions per 10 minutes.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key = 'estatein_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );

	if ( $hits >= 5 ) {
		return array(
			'success' => false,
			'message' => __( 'Too many submissions. Please try again in a few minutes.', 'estatein' ),
			'errors'  => array(),
		);
	}
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

	// Persist the enquiry.
	$title = 'newsletter' === $form_type
		/* translators: %s: subscriber email address. */
		? sprintf( __( 'Newsletter: %s', 'estatein' ), $email )
		/* translators: 1: sender name, 2: subject line. */
		: sprintf( __( '%1$s — %2$s', 'estatein' ), $name, $subject ? $subject : __( 'Website enquiry', 'estatein' ) );

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'estatein_enquiry',
			'post_status'  => 'private',
			'post_title'   => $title,
			'post_content' => $message,
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'email', $email );
		update_post_meta( $post_id, 'name', $name );
		update_post_meta( $post_id, 'phone', $phone );
		update_post_meta( $post_id, 'form_type', $form_type );
	}

	// Notify the site owner.
	$to      = estatein_option( 'contact_email', get_option( 'admin_email' ) );
	$body    = sprintf(
		"Name: %s\nEmail: %s\nPhone: %s\nSubject: %s\n\n%s",
		$name,
		$email,
		$phone,
		$subject,
		$message
	);
	$headers = array( 'Reply-To: ' . $email );

	wp_mail( $to, $title, $body, $headers );

	return array(
		'success' => true,
		'message' => 'newsletter' === $form_type
			? __( 'You are subscribed. Welcome aboard.', 'estatein' )
			: __( 'Thank you — we will be in touch within one business day.', 'estatein' ),
		'errors'  => array(),
	);
}

/**
 * AJAX endpoint (available to logged-in and logged-out visitors).
 */
function estatein_ajax_submit() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'estatein_form' ) ) {
		wp_send_json_error(
			array( 'message' => __( 'Your session expired. Please reload the page and try again.', 'estatein' ), 'errors' => array() ),
			403
		);
	}

	$result = estatein_process_submission( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitised inside the handler.

	if ( $result['success'] ) {
		wp_send_json_success( $result );
	}

	wp_send_json_error( $result, 422 );
}
add_action( 'wp_ajax_estatein_submit', 'estatein_ajax_submit' );
add_action( 'wp_ajax_nopriv_estatein_submit', 'estatein_ajax_submit' );

/**
 * Non-JavaScript fallback: handle a normal POST, then redirect.
 *
 * Keeps the form usable without JS and avoids a re-submit on refresh.
 */
function estatein_handle_post_submit() {
	if ( empty( $_POST['estatein_form_submit'] ) ) {
		return;
	}

	$nonce = isset( $_POST['estatein_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['estatein_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'estatein_form' ) ) {
		return;
	}

	$result   = estatein_process_submission( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Sanitised inside the handler.
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	wp_safe_redirect(
		add_query_arg(
			'estatein_sent',
			$result['success'] ? '1' : '0',
			remove_query_arg( 'estatein_sent', $redirect )
		) . '#contact-form'
	);
	exit;
}
add_action( 'template_redirect', 'estatein_handle_post_submit' );

/**
 * Render the no-JS success/error banner after a redirect.
 */
function estatein_form_notice() {
	if ( ! isset( $_GET['estatein_sent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display flag.
		return;
	}

	$ok = '1' === sanitize_text_field( wp_unslash( $_GET['estatein_sent'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	printf(
		'<p class="form-note %1$s" role="status">%2$s</p>',
		$ok ? 'form-note--ok' : 'form-note--err',
		esc_html(
			$ok
				? __( 'Thank you — we will be in touch within one business day.', 'estatein' )
				: __( 'Please check the form and try again.', 'estatein' )
		)
	);
}

/**
 * Show enquiry details in the admin list table.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function estatein_enquiry_columns( $columns ) {
	unset( $columns['date'] );
	$columns['estatein_email'] = __( 'Email', 'estatein' );
	$columns['estatein_type']  = __( 'Type', 'estatein' );
	$columns['date']           = __( 'Received', 'estatein' );
	return $columns;
}
add_filter( 'manage_estatein_enquiry_posts_columns', 'estatein_enquiry_columns' );

/**
 * Render enquiry columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function estatein_enquiry_column_content( $column, $post_id ) {
	if ( 'estatein_email' === $column ) {
		$email = get_post_meta( $post_id, 'email', true );
		echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '—';
	}

	if ( 'estatein_type' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'form_type', true ) ?: 'contact' );
	}
}
add_action( 'manage_estatein_enquiry_posts_custom_column', 'estatein_enquiry_column_content', 10, 2 );
