<?php
/**
 * Our helper functions to use across the plugin.
 *
 * @package WooBetterReviews
 */

// Declare our namespace.
namespace LiquidWeb\WooBetterReviews\Helpers;

// Set our aliases.
use LiquidWeb\WooBetterReviews as Core;

/**
 * Set the key / value pair for all our custom tables.
 *
 * @param  boolean $keys  Whether to return just the keys.
 *
 * @return array
 */
function get_table_args( $keys = false ) {

	// Set up our array.
	$tables = array(
		'content'      => __( 'Review Content', 'woo-better-reviews' ),
		'authormeta'   => __( 'Author Meta', 'woo-better-reviews' ),
		'ratings'      => __( 'Review Ratings', 'woo-better-reviews' ),
		'attributes'   => __( 'Product Attributes', 'woo-better-reviews' ),
		'charstcs'     => __( 'Author Characteristics', 'woo-better-reviews' ),
		'authorsetup'  => __( 'Author Setup', 'woo-better-reviews' ),
		'productsetup' => __( 'Product Setup', 'woo-better-reviews' ),
	);

	// Either return the full array, or just the keys if requested.
	return ! $keys ? $tables : array_keys( $tables );
}

/**
 * Compare the table name to our allowed items.
 *
 * @param  string $table_name  The name (slug) of the table.
 *
 * @return boolean
 */
function maybe_valid_table( $table_name = '' ) {

	// Make sure we have a table name.
	if ( empty( $table_name ) ) {
		return false;
	}

	// Fetch my tables.
	$tables = get_table_args( true );

	// Return the result.
	return in_array( $table_name, $tables ) ? true : false;
}


/**
 * Return our base link, with function fallbacks.
 *
 * @param  string $menu_slug  Which menu slug to use. Defaults to the primary.
 *
 * @return string
 */
function get_admin_menu_link( $menu_slug = '' ) {

	// Bail if we aren't on the admin side.
	if ( ! is_admin() ) {
		return false;
	}

	// Set my slug.
	$menu_slug  = ! empty( $menu_slug ) ? trim( $menu_slug ) : trim( Core\SETTINGS_ANCHOR );

	// Build out the link if we don't have our function.
	if ( ! function_exists( 'menu_page_url' ) ) {

		// Set up my args.
		$setup  = array( 'page' => $menu_slug );

		// Return the link with our args.
		return add_query_arg( $setup, admin_url( 'admin.php' ) );
	}

	// Return using the function.
	return menu_page_url( $menu_slug, false );
}

/**
 * Handle our redirect within the admin settings page.
 *
 * @param  array   $custom_args  The query args to include in the redirect.
 * @param  string  $menu_slug    Which menu slug to use. Defaults to the primary.
 * @param  boolean $response     Whether to include a response code.
 *
 * @return void
 */
function admin_page_redirect( $custom_args = array(), $menu_slug = '', $response = true ) {

	// Don't redirect if we didn't pass any args.
	if ( empty( $custom_args ) ) {
		return;
	}

	// Set my slug.
	$redirect_slug  = ! empty( $menu_slug ) ? trim( $menu_slug ) : trim( Core\SETTINGS_ANCHOR );

	// Handle the setup.
	$redirect_args  = wp_parse_args( $custom_args, array( 'page' => $redirect_slug ) );

	// Add the default args we need in the return.
	if ( $response ) {
		$redirect_args  = wp_parse_args( array( 'wbr-action-complete' => 1 ), $redirect_args );
	}

	// Now set my redirect link.
	$redirect_link  = add_query_arg( $redirect_args, admin_url( 'admin.php' ) );

	// Do the redirect.
	wp_safe_redirect( $redirect_link );
	exit;
}

/**
 * Check an code and (usually an error) return the appropriate text.
 *
 * @param  string $code  The code provided.
 *
 * @return string
 */
function get_admin_notice_text( $code = '' ) {

	// Return if we don't have an error code.
	if ( empty( $code ) ) {
		return __( 'There was an error with your request.', 'woo-better-reviews' );
	}

	// Handle my different error codes.
	switch ( esc_attr( strtolower( $code ) ) ) {

		case 'attribute-updated' :
			return __( 'The attribute has been updated.', 'woo-better-reviews' );
			break;

		case 'attribute-unchanged' :
			return __( 'No changes were requested.', 'woo-better-reviews' );
			break;

		case 'missing-posted-args' :
			return __( 'The required attribute arguments were not posted.', 'woo-better-reviews' );
			break;

		case 'missing-attribute-args' :
			return __( 'The required attribute arguments were not provided.', 'woo-better-reviews' );
			break;

		case 'missing-formatted-args' :
			return __( 'The attribute arguments could not be formatted.', 'woo-better-reviews' );
			break;

		case 'attribute-update-failed' :
			return __( 'The attribute could not be updated at this time.', 'woo-better-reviews' );
			break;

		case 'unknown' :
		case 'unknown-error' :
			return __( 'There was an unknown error with your request.', 'woo-better-reviews' );
			break;

		default :
			return __( 'There was an error with your request.', 'woo-better-reviews' );
			break;

		// End all case breaks.
	}
}