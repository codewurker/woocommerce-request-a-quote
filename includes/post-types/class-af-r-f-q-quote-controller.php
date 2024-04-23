<?php

defined( 'ABSPATH' ) || exit;

class AF_R_F_Q_Quote_Controller {

	public $ID;

	public $post;

	public function __construct( $id = 0 ) {
		$this->ID = $id;

		$this->post = get_post( $this->ID );
	}

	public function get_id() {
		return $this->ID;
	}

	public function get_status() {
		return get_post_meta( $this->ID, 'quote_status', true );
	}

	public function get_customer_info() {

		$quote_id = $this->ID;

		$customer_info = array();

		$quote_fiels_obj = new AF_R_F_Q_Quote_Fields();
		$quote_fields    = (array) $quote_fiels_obj->afrfq_get_fields_enabled();

		if ( empty( $quote_fields ) ) {
			return $customer_info;
		}

		foreach ( $quote_fields as $key => $field ) {

			$field_id = $field->ID;

			$afrfq_field_name = get_post_meta( $field_id, 'afrfq_field_name', true );
			$afrfq_field_type = get_post_meta( $field_id, 'afrfq_field_type', true );
			$field_data       = get_post_meta( $quote_id, $afrfq_field_name, true );

			if ( is_array( $field_data ) ) {
				$field_data = implode( ', ', $field_data );
			}

			if ( 'terms_cond' == $afrfq_field_type ) {
				continue;
			}

			if ( in_array( $afrfq_field_type, array( 'select', 'radio', 'mutliselect' ), true ) ) {
				$field_data = ucwords( $field_data );
			}

			if ( 'file' == $afrfq_field_type && ! empty( $field_data ) ) {
				$field_data = esc_url( AFRFQ_URL . '/uploads/' . $field_data );
			}

			$customer_info[ $afrfq_field_name ] = $field_data;

		}

		return $customer_info;
	}

	public function get_items() {

		$quote_items    = array();
		$quote_contents = get_post_meta( $this->ID, 'quote_contents', true );

		foreach ( $quote_contents as $quote_item_key => $quote_item ) {

			if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
				continue;
			}

			$product    = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
			$product_id = $product->get_id();

			$quantity      = $quote_item['quantity'];
			$price         = empty( $quote_item['addons_price'] ) ? $product->get_price() : $quote_item['addons_price'];
			$price         = empty( $quote_item['role_base_price'] ) ? $price : $quote_item['role_base_price'];
			$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

			$quote_item['_offered_total'] = $offered_price * intval( $quantity );

			if ( $product->is_taxable() ) {

				if ( ! wc_prices_include_tax() ) {
					$product_subtotal = wc_get_price_including_tax(
						$product,
						array(
							'qty'   => $quantity,
							'price' => $price,
						)
					);
				} else {
					$product_subtotal = wc_get_price_excluding_tax(
						$product,
						array(
							'qty'   => $quantity,
							'price' => $price,
						)
					);
				}

				$difference_price = ( $price * $quantity ) - $product_subtotal;

				if ( $difference_price < 0 ) {
					$difference_price = $difference_price * -1;
				}

				$quote_item['_subtotal']  = $price * $quantity;
				$quote_item['_tax_total'] = $difference_price;

			} else {

				$quote_item['_subtotal']  = $price * $quantity;
				$quote_item['_tax_total'] = 0;
			}

			unset( $quote_item['data'] );
			unset( $quote_item['data_hash'] );

			$quote_items[ $product_id ] = $quote_item;
		}

		return $quote_items;
	}

	public function get_totals() {

		$quote_contents = get_post_meta( $this->ID, 'quote_contents', true );

		if ( ! isset( $af_quote ) ) {
			$af_quote = new AF_R_F_Q_Quote( $quote_contents );
		}

		return $af_quote->get_calculated_totals( $quote_contents, $this->ID );
	}

	public function save() {
	}
}
