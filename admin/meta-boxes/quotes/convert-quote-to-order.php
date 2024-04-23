<?php
/**
 * Customer information table for email.
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;

$quote_contents = get_post_meta( $post->ID, 'quote_contents', true );
$quotes         = $quote_contents;
$quote_order    = wc_create_order();

foreach ( $quote_contents as $quote_item_key => $quote_item ) {


	if ( isset( $quote_item['data'] ) ) {
		$product = $quote_item['data'];
	} else {
		continue;
	}

	if ( ! is_object( $product ) ) {
		continue;
	}

	$price         = $product->get_price();
	$qty_display   = $quote_item['quantity'];
	$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

	if ( 0 < $offered_price ) {

		$product->set_price( $offered_price );
	} else {

		$product->set_price( $price );
	}


	$item_id = $quote_order->add_product( $product, $qty_display );
	$item    = $quote_order->get_item( $item_id );

	if ( ! empty( $quote_item['addons'] ) && class_exists( 'WC_Product_Addons_Helper' ) ) {
		foreach ( $quote_item['addons'] as $addon ) {
			$key           = $addon['name'];
			$price_type    = $addon['price_type'];
			$product       = $item->get_product();
			$product_price = $product->get_price();

			/*
			 * For percentage based price type we want
			 * to show the calculated price instead of
			 * the price of the add-on itself and in this
			 * case its not a price but a percentage.
			 * Also if the product price is zero, then there
			 * is nothing to calculate for percentage so
			 * don't show any price.
			 */
			if ( $addon['price'] && 'percentage_based' === $price_type && 0 != $product_price ) {
				$addon_price = $product_price * ( $addon['price'] / 100 );
			} else {
				$addon_price = $addon['price'];
			}
			$price = html_entity_decode(
				wp_strip_all_tags( wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $addon_price, $product ) ) ),
				ENT_QUOTES,
				get_bloginfo( 'charset' )
			);

			if ( $addon['price'] && apply_filters( 'woocommerce_addons_add_price_to_name', true ) ) {
				$key .= ' (' . $price . ')';
			}

			if ( 'custom_price' === $addon['field_type'] ) {
				$addon['value'] = $addon['price'];
			}

			$item->add_meta_data( $key, $addon['value'] );
			$item->save();
		}
	}
}

$customer_name  = $post->afrfq_name_field;
$customer_email = $post->afrfq_email_field;

$customer_id = get_post_meta( $post->ID, '_customer_user', true );

$quote_user = get_user_by( 'id', $customer_id );

$af_fields_obj = new AF_R_F_Q_Quote_Fields();
$quote_fields  = (array) $af_fields_obj->afrfq_get_fields_enabled();


foreach ( $quote_fields as $quote_field ) {

	$field_label = get_post_meta( $quote_field->ID, 'afrfq_field_label', true );
	$quote_order->update_meta_data( $field_label, $form_data[ 'afrfq_field_' . $quote_field->ID ] );

}

$quote_order->update_meta_data( 'quote_id_for_this_order', $post->ID );

$order_note = esc_html__( 'This order has been created from ', 'addify_rfq' ) . '<a href="post.php?post=' . $post->ID . '&action=edit">quote # ' . $post->ID . '</a>';

// Add the note.
$quote_order->add_order_note( $order_note );


$billing_address  = $af_fields_obj->afrfq_get_billing_data( $post->ID );
$shipping_address = $af_fields_obj->afrfq_get_shipping_data( $post->ID );

$quote_order->set_address( $billing_address, 'billing' );
$quote_order->set_address( $shipping_address, 'shipping' );
$quote_order->set_customer_id( intval( $customer_id ) );

$quote_order->calculate_totals(); // updating totals.

$quote_order->save(); // Save the order data.

$current_admin = wp_get_current_user();

$current_admin = isset( $current_admin->ID ) ? (string) $current_admin->user_login : 'Admin';

update_post_meta( $post->ID, 'quote_status', 'af_converted' );
update_post_meta( $post->ID, 'converted_by_user', $current_admin );
update_post_meta( $post->ID, 'converted_by', __( 'Administrator', 'addify_rfq' ) );
update_post_meta( $post->ID, 'order_for_this_quote', $quote_order->get_id() );

do_action( 'addify_rfq_quote_converted_to_order', $quote_order->get_id(), $post->ID );
do_action( 'addify_rfq_send_quote_email_to_customer', $post->ID );
do_action( 'addify_rfq_send_quote_email_to_admin', $post->ID );
wp_safe_redirect( $quote_order->get_edit_order_url() );
exit;
