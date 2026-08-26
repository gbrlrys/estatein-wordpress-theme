<?php
/**
 * Search form.
 *
 * @package Estatein
 */

defined( 'ABSPATH' ) || exit;

$estatein_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:flex;gap:12px">
	<label class="sr-only" for="<?php echo esc_attr( $estatein_id ); ?>">
		<?php esc_html_e( 'Search this site', 'estatein' ); ?>
	</label>
	<input
		class="input"
		type="search"
		id="<?php echo esc_attr( $estatein_id ); ?>"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Search…', 'estatein' ); ?>"
	>
	<button type="submit" class="btn btn--primary">
		<?php esc_html_e( 'Search', 'estatein' ); ?>
	</button>
</form>
