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

if ( ! is_object( $post ) ) {
	return;
}

$field_id = $post->ID;

if ( $validation ) {

	if ( isset( $form_data['afrfq_field_name'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_name', $form_data['afrfq_field_name'] );
	}

	if ( isset( $form_data['afrfq_field_type'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_type', $form_data['afrfq_field_type'] );
	}

	if ( isset( $form_data['afrfq_field_label'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_label', $form_data['afrfq_field_label'] );
	}

	if ( isset( $form_data['afrfq_field_value'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_value', $form_data['afrfq_field_value'] );
	}


	if ( isset( $form_data['afrfq_field_placeholder'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_placeholder', $form_data['afrfq_field_placeholder'] );
	}

	if ( isset( $form_data['afrfq_file_types'] ) ) {
		update_post_meta( $field_id, 'afrfq_file_types', strtolower( trim( preg_replace( '/[\t\n\r\s]+/', ' ', $form_data['afrfq_file_types'] ) ) ) );
	}

	if ( isset( $form_data['afrfq_file_size'] ) ) {
		update_post_meta( $field_id, 'afrfq_file_size', $form_data['afrfq_file_size'] );
	}

	if ( isset( $form_data['afrfq_field_options'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_options', array_unique( array_filter( $form_data['afrfq_field_options'] ) ) );
	}

	if ( isset( $form_data['afrfq_field_enable'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_enable', $form_data['afrfq_field_enable'] );
	}
	if ( isset( $form_data['afrfq_field_terms'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_terms', $form_data['afrfq_field_terms'] );
	}

	if ( isset( $form_data['afrfq_field_required'] ) ) {
		update_post_meta( $field_id, 'afrfq_field_required', $form_data['afrfq_field_required'] );
	} else {
		update_post_meta( $field_id, 'afrfq_field_required', 'no' );
	}
}
