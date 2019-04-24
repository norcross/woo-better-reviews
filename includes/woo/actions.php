<?php
/**
 * Handle action related Woo stuff.
 *
 * @package WooBetterReviews
 */

// Declare our namespace.
namespace LiquidWeb\WooBetterReviews\WooActions;

// Set our aliases.
use LiquidWeb\WooBetterReviews as Core;

/**
 * Start our engines.
 */
add_action( 'woocommerce_thankyou', __NAMESPACE__ . '\run_after_purchase_triggers' );
add_action( 'woocommerce_order_status_changed', __NAMESPACE__ . '\run_after_status_change_triggers', 10, 3 );

/**
 * Run the triggers set after an order is complete.
 *
 * @param  integer $order_id  The ID of the order just placed.
 *
 * @return void
 */
function run_after_purchase_triggers( $order_id ) {

	// @@todo include some sort of check for the setting i haven't added yet.

	// Bail without an order ID.
	if ( ! $order_id ) {
		return;
	}

	// Get an instance of the WC_Order object and then pull the data.
	$order_object   = wc_get_order( $order_id );
	$order_data     = $order_object->get_data();

	// First do the trigger with the entire order data.
	do_action( Core\AFTER_PURCHASE_TRIGGER . 'order_data', $order_data, $order_id );
	// wc_better_reviews_trigger_after_purchase_

	// If we have no actual line items, do that trigger.
	if ( empty( $order_data['line_items'] ) ) {

		// Do the trigger with the entire order data.
		do_action( Core\AFTER_PURCHASE_TRIGGER . 'order_data_no_items', $order_data, $order_id );
		// wc_better_reviews_trigger_after_purchase_

		// And be done.
		return;
	}

	// Do the action that contains the line items.
	do_action( Core\AFTER_PURCHASE_TRIGGER . 'order_line_items', $order_data['line_items'], $order_data, $order_id );
	// wc_better_reviews_trigger_after_purchase_

	// Now loop through each line item and pass that along.
	foreach ( $order_data['line_items'] as $item_key => $item_object ) {

		// Pull out a few variables to use in subsequent triggers.
		$product_obj    = $item_object->get_product();
		$product_data   = $product_obj->get_data();

		// Run the trigger that has the single bit of product data.
		do_action( Core\AFTER_PURCHASE_TRIGGER . 'order_single_line_item', $product_data, $order_data, $order_id );
		// wc_better_reviews_trigger_after_purchase_
	}

	// @@todo figure out if we need some sort of final action here.
}

/**
 * Run our triggers after an order status has changed.
 *
 * @param  integer $order_id    The order ID involving the change.
 * @param  string  $old_status  The current (existing) status.
 * @param  string  $new_status  The status being changed to.
 *
 * @return void
 */
function run_after_status_change_triggers( $order_id, $old_status, $new_status ) {

	// Bail without an order ID.
	if ( ! $order_id ) {
		return;
	}

	// Get an instance of the WC_Order object and then pull the data.
	$order_object   = wc_get_order( $order_id );
	$order_data     = $order_object->get_data();

	// Run an action that runs before each string check.
	do_action( Core\STATUS_CHANGE_TRIGGER . 'order_data', $order_data, $order_id );
	// wc_better_reviews_trigger_status_change_

	// Run an action trigger for the from and the to.
	do_action( Core\STATUS_CHANGE_TRIGGER . 'set_from_' . $old_status, $order_data, $order_id );
	do_action( Core\STATUS_CHANGE_TRIGGER . 'set_to_' . $new_status, $order_data, $order_id );
	// wc_better_reviews_trigger_status_change_
}
