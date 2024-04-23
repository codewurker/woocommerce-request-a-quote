<?php
/**
 * Field Attributes.
 *
 * Deal field attributes in metabox .
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
$rule_id = $post->ID;

// if our nonce isn't there, or we can't verify it, return
if ( empty( $_POST['afrfq_field_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['afrfq_field_nonce'] ) ), 'afrfq_fields_nonce_action' ) ) {
	die( esc_html__( 'Site Security Violated', 'addify_rfq' ) );
}

if ( isset( $_POST['afrfq_hide_products'] ) ) {
	update_post_meta( $rule_id, 'afrfq_hide_products', serialize( sanitize_meta( '', wp_unslash( $_POST['afrfq_hide_products'] ), '' ) ) );
} else {
	update_post_meta( $rule_id, 'afrfq_hide_products', '' );
}



if ( isset( $_POST['afrfq_hide_categories'] ) ) {
	update_post_meta( $rule_id, 'afrfq_hide_categories', serialize( sanitize_meta( '', wp_unslash( $_POST['afrfq_hide_categories'] ), '' ) ) );
} else {
	update_post_meta( $rule_id, 'afrfq_hide_categories', '' );
}

if ( isset( $_POST['afrfq_apply_on_all_products'] ) ) {
	update_post_meta( $rule_id, 'afrfq_apply_on_all_products', sanitize_text_field( wp_unslash( $_POST['afrfq_apply_on_all_products'] ) ) );
} else {

	update_post_meta( $rule_id, 'afrfq_apply_on_all_products', 'no' );
}

if ( isset( $_POST['afrfq_apply_on_oos_products'] ) ) {
	update_post_meta( $rule_id, 'afrfq_apply_on_oos_products', sanitize_text_field( wp_unslash( $_POST['afrfq_apply_on_oos_products'] ) ) );
} else {

	update_post_meta( $rule_id, 'afrfq_apply_on_oos_products', 'no' );
}

if ( isset( $_POST['afrfq_hide_user_role'] ) ) {
	update_post_meta( $rule_id, 'afrfq_hide_user_role', serialize( sanitize_meta( '', wp_unslash( $_POST['afrfq_hide_user_role'] ), '' ) ) );
} else {
	update_post_meta( $rule_id, 'afrfq_hide_user_role', '' );
}



if ( isset( $_POST['afrfq_is_hide_price'] ) ) {
	update_post_meta( $rule_id, 'afrfq_is_hide_price', sanitize_text_field( wp_unslash( $_POST['afrfq_is_hide_price'] ) ) );
}

if ( isset( $_POST['afrfq_hide_price_text'] ) ) {
	update_post_meta( $rule_id, 'afrfq_hide_price_text', sanitize_text_field( wp_unslash( $_POST['afrfq_hide_price_text'] ) ) );
}

if ( isset( $_POST['afrfq_is_hide_addtocart'] ) ) {
	update_post_meta( $rule_id, 'afrfq_is_hide_addtocart', sanitize_text_field( wp_unslash( $_POST['afrfq_is_hide_addtocart'] ) ) );
}

if ( isset( $_POST['afrfq_custom_button_text'] ) ) {
	update_post_meta( $rule_id, 'afrfq_custom_button_text', sanitize_text_field( wp_unslash( $_POST['afrfq_custom_button_text'] ) ) );
}

if ( isset( $_POST['afrfq_custom_button_link'] ) ) {
	update_post_meta( $rule_id, 'afrfq_custom_button_link', sanitize_text_field( wp_unslash( $_POST['afrfq_custom_button_link'] ) ) );
}
