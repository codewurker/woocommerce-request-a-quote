<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'AF_R_F_Q_Product_Addon' ) ) {

	class AF_R_F_Q_Product_Addon {

		public function __construct() {

			// Add woocommerce product addon compatibility.
			add_filter( 'addify_add_quote_item_data', array( $this, 'add_cart_item_data' ), 10, 5 );
			add_filter( 'addify_add_quote_item', array( $this, 'add_cart_item' ), 30, 1 );
			// Validate when adding to cart.
			add_filter( 'addify_add_to_quote_validation', array( $this, 'validate_add_cart_item' ), 999, 4 );

			add_action( 'addify_quote_session_changed', array( $this, 'update_session_quote_addon_price' ), 20 );

			add_action( 'addify_quote_contents_updated', array( $this, 'update_quote_addon_price' ), 20, 1 );
		}

		public function update_quote_addon_price( $quote_id, $post_data = array() ) {

			if ( ! class_exists( 'WC_Product_Addons_Helper' ) || empty( $quote_id ) ) {
				return;
			}

			$quote_contents = get_post_meta( $quote_id, 'quote_contents', true );

			$updated_quote = array();
			foreach ( $quote_contents as $quote_item_key => $cart_item_data ) {

				if ( ! isset( $cart_item_data['data'] ) || ! is_object( $cart_item_data['data'] ) ) {
					continue;
				}

				$quantity = $cart_item_data['quantity'];

				if ( ! empty( $cart_item_data['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item_data ) ) {
					$price = (float) $cart_item_data['data']->get_price( 'edit' );

					// Compatibility with Smart Coupons self declared gift amount purchase.
					if ( empty( $price ) && ! empty( $post_data['credit_called'] ) ) {

						if ( isset( $post_data['credit_called'][ $cart_item_data['data']->get_id() ] ) ) {
							$price = (float) $post_data['credit_called'][ $cart_item_data['data']->get_id() ];
						}
					}

					if ( empty( $price ) && ! empty( $cart_item_data['credit_amount'] ) ) {
						$price = (float) $cart_item_data['credit_amount'];
					}

					// Save the price before price type calculations to be used later.
					$cart_item_data['addons_price_before_calc'] = (float) $price;

					foreach ( $cart_item_data['addons'] as $addon ) {
						$price_type  = $addon['price_type'];
						$addon_price = $addon['price'];

						switch ( $price_type ) {
							case 'percentage_based':
								$price += (float) ( $cart_item_data['data']->get_price( 'edit' ) * ( $addon_price / 100 ) );
								break;
							case 'flat_fee':
								$price += (float) ( $addon_price / $quantity );
								break;
							default:
								$price += (float) $addon_price;
								break;
						}
					}

					$cart_item_data['data']->set_price( $price );

					if ( ! is_admin() ) {
						$cart_item_data['offered_price'] = $cart_item_data['offered_price'] > $price ? $price : $cart_item_data['offered_price'];
					}

					$cart_item_data['addons_price'] = $cart_item_data['data']->get_price( 'edit' );
				}

				$updated_quote[ $quote_item_key ] = $cart_item_data;
			}

			update_post_meta( $quote_id, 'quote_contents', $updated_quote );
		}

		public function update_session_quote_addon_price( $post_data = array() ) {

			if ( ! class_exists( 'WC_Product_Addons_Helper' ) ) {
				return;
			}

			$updated_quote = array();
			foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $cart_item_data ) {

				if ( ! isset( $cart_item_data['data'] ) || ! is_object( $cart_item_data['data'] ) ) {
					continue;
				}

				$quantity = $cart_item_data['quantity'];

				if ( ! empty( $cart_item_data['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item_data ) ) {
					$price = (float) $cart_item_data['data']->get_price( 'edit' );

					// Compatibility with Smart Coupons self declared gift amount purchase.
					if ( empty( $price ) && ! empty( $post_data['credit_called'] ) ) {

						if ( isset( $post_data['credit_called'][ $cart_item_data['data']->get_id() ] ) ) {
							$price = (float) $post_data['credit_called'][ $cart_item_data['data']->get_id() ];
						}
					}

					if ( empty( $price ) && ! empty( $cart_item_data['credit_amount'] ) ) {
						$price = (float) $cart_item_data['credit_amount'];
					}

					// Save the price before price type calculations to be used later.
					$cart_item_data['addons_price_before_calc'] = (float) $price;

					foreach ( $cart_item_data['addons'] as $addon ) {
						$price_type  = $addon['price_type'];
						$addon_price = $addon['price'];

						switch ( $price_type ) {
							case 'percentage_based':
								$price += (float) ( $cart_item_data['data']->get_price( 'edit' ) * ( $addon_price / 100 ) );
								break;
							case 'flat_fee':
								$price += (float) ( $addon_price / $quantity );
								break;
							default:
								$price += (float) $addon_price;
								break;
						}
					}

					$cart_item_data['data']->set_price( $price );

					if ( empty( $cart_item_data['addons_price'] ) ) {

						$incr_offered_price = floatval( get_option( 'afrfq_enable_off_price_increase' ) );

						$offered_price = $price;

						if ( ! empty( $incr_offered_price ) ) {

							$offered_price += ( $incr_offered_price * $product_data->get_price() ) / 100;
						}

						$cart_item_data['offered_price'] = $offered_price;
					}

					$cart_item_data['addons_price'] = $cart_item_data['data']->get_price( 'edit' );
				}

				$updated_quote[ $quote_item_key ] = $cart_item_data;
			}

			wc()->session->set( 'quotes', $updated_quote );
		}

		public function validate_add_cart_item( $passed, $product_id, $qty, $post_data = array() ) {

			if ( ! class_exists( 'WC_Product_Addons_Helper' ) ) {
				return $passed;
			}

			$product_addons = WC_Product_Addons_Helper::get_product_addons( $product_id );

			if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
				include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/abstract-wc-product-addons-field.php';

				foreach ( $product_addons as $addon ) {
					// If type is heading, skip.
					if ( 'heading' === $addon['type'] ) {
						continue;
					}

					$value = isset( $post_data[ 'addon-' . $addon['field_name'] ] ) ? $post_data[ 'addon-' . $addon['field_name'] ] : '';

					if ( is_array( $value ) ) {
						$value = array_map( 'stripslashes', $value );
					} else {
						$value = stripslashes( $value );
					}

					switch ( $addon['type'] ) {
						case 'checkbox':
							include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php';
							$field = new WC_Product_Addons_Field_List( $addon, $value );
							break;
						case 'multiple_choice':
							switch ( $addon['display'] ) {
								case 'radiobutton':
									include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php';
									$field = new WC_Product_Addons_Field_List( $addon, $value );
									break;
								case 'images':
								case 'select':
									include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-select.php';
									$field = new WC_Product_Addons_Field_Select( $addon, $value );
									break;
							}
							break;
						case 'custom_text':
						case 'custom_textarea':
						case 'custom_price':
						case 'input_multiplier':
							include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-custom.php';
							$field = new WC_Product_Addons_Field_Custom( $addon, $value );
							break;
						case 'file_upload':
							include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-file-upload.php';
							$field = new WC_Product_Addons_Field_File_Upload( $addon, $value );
							break;
						default:
							// Continue to the next field in case the type is not recognized (instead of causing a fatal error).
							continue 2;
					}

					$data = $field->validate();

					if ( is_wp_error( $data ) ) {
						wc_add_notice( $data->get_error_message(), 'error' );
						return false;
					}

					do_action( 'woocommerce_validate_posted_addon_data', $addon );
				}
			}

			return $passed;
		}

		public function add_cart_item( $cart_item_data, $post_data = array() ) {

			if ( ! class_exists( 'WC_Product_Addons_Helper' ) ) {
				return $cart_item_data;
			}

			$quantity = $cart_item_data['quantity'];

			if ( ! empty( $cart_item_data['addons'] ) && apply_filters( 'woocommerce_product_addons_adjust_price', true, $cart_item_data ) ) {
				$price = (float) $cart_item_data['data']->get_price( 'edit' );

				// Compatibility with Smart Coupons self declared gift amount purchase.
				if ( empty( $price ) && ! empty( $post_data['credit_called'] ) ) {

					if ( isset( $post_data['credit_called'][ $cart_item_data['data']->get_id() ] ) ) {
						$price = (float) $post_data['credit_called'][ $cart_item_data['data']->get_id() ];
					}
				}

				if ( empty( $price ) && ! empty( $cart_item_data['credit_amount'] ) ) {
					$price = (float) $cart_item_data['credit_amount'];
				}

				// Save the price before price type calculations to be used later.
				$cart_item_data['addons_price_before_calc'] = (float) $price;

				foreach ( $cart_item_data['addons'] as $addon ) {
					$price_type  = $addon['price_type'];
					$addon_price = $addon['price'];

					switch ( $price_type ) {
						case 'percentage_based':
							$price += (float) ( $cart_item_data['data']->get_price( 'edit' ) * ( $addon_price / 100 ) );
							break;
						case 'flat_fee':
							$price += (float) ( $addon_price / $quantity );
							break;
						default:
							$price += (float) $addon_price;
							break;
					}
				}

				$cart_item_data['data']->set_price( $price );
			}

			$cart_item_data['offered_price'] = $cart_item_data['data']->get_price( 'edit' );
			$cart_item_data['addons_price']  = $cart_item_data['data']->get_price( 'edit' );

			return $cart_item_data;
		}

		public function add_cart_item_data( $cart_item_data, $product_id, $variation_id = 0, $quantity = 1, $post_data = array() ) {

			if ( ! class_exists( 'WC_Product_Addons_Helper' ) ) {
				return $cart_item_data;
			}

			$product_addons = WC_Product_Addons_Helper::get_product_addons( $product_id );

			if ( empty( $cart_item_data['addons'] ) ) {
				$cart_item_data['addons'] = array();
			}

			if ( is_array( $product_addons ) && ! empty( $product_addons ) ) {
				include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/abstract-wc-product-addons-field.php';

				foreach ( $product_addons as $addon ) {
					// If type is heading, skip.
					if ( 'heading' === $addon['type'] ) {
						continue;
					}

					$value = isset( $post_data[ 'addon-' . $addon['field_name'] ] ) ? $post_data[ 'addon-' . $addon['field_name'] ] : '';

					if ( is_array( $value ) ) {
						$value = array_map( 'stripslashes', $value );
					} else {
						$value = stripslashes( $value );
					}

					switch ( $addon['type'] ) {
						case 'checkbox':
							include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php';
							$field = new WC_Product_Addons_Field_List( $addon, $value );
							break;
						case 'multiple_choice':
							switch ( $addon['display'] ) {
								case 'radiobutton':
									include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-list.php';
									$field = new WC_Product_Addons_Field_List( $addon, $value );
									break;
								case 'images':
								case 'select':
									include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-select.php';
									$field = new WC_Product_Addons_Field_Select( $addon, $value );
									break;
							}
							break;
						case 'custom_text':
						case 'custom_textarea':
						case 'custom_price':
						case 'input_multiplier':
							include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-custom.php';
							$field = new WC_Product_Addons_Field_Custom( $addon, $value );
							break;
						case 'file_upload':
							include_once WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/fields/class-wc-product-addons-field-file-upload.php';
							$field = new WC_Product_Addons_Field_File_Upload( $addon, $value );
							break;
					}

					$data = $field->get_cart_item_data();

					if ( is_wp_error( $data ) ) {
						// Throw exception for add_to_cart to pickup.
						throw new Exception( esc_attr( $data->get_error_message() ) );
					} elseif ( $data ) {
						$cart_item_data['addons'] = array_merge( $cart_item_data['addons'], apply_filters( 'woocommerce_product_addon_cart_item_data', $data, $addon, $product_id, $post_data ) );
					}
				}
			}

			return $cart_item_data;
		}
	}

	new AF_R_F_Q_Product_Addon();
}
