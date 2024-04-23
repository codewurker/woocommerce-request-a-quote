<?php
/**
 * Addify Add to Quote
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * AF_R_F_Q_Quote class.
 */
class AF_R_F_Q_Quote {

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_contents = array();

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_subtotal = 0;

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_tax_total = 0;

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $offered_total = 0;

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_total = 0;
	/**
	 * Total defaults used to reset.
	 *
	 * @var array
	 */
	protected $default_totals = array(
		'subtotal'            => 0,
		'subtotal_tax'        => 0,
		'shipping_total'      => 0,
		'shipping_tax'        => 0,
		'shipping_taxes'      => array(),
		'discount_total'      => 0,
		'discount_tax'        => 0,
		'cart_contents_total' => 0,
		'cart_contents_tax'   => 0,
		'cart_contents_taxes' => array(),
		'fee_total'           => 0,
		'fee_tax'             => 0,
		'fee_taxes'           => array(),
		'total'               => 0,
		'total_tax'           => 0,
	);
	/**
	 * Store calculated totals.
	 *
	 * @var array
	 */
	protected $totals = array();

	/**
	 * Constructor for the Add_To_Quote class. Loads quote contents.
	 *
	 * @param object $quote_contents_arr returns $quote_contents_arr.
	 */
	public function __construct( $quote_contents_arr = array() ) {

		$this->quote_contents = $quote_contents_arr;

		if ( empty( $this->quote_contents ) && isset( WC()->session ) ) {
			$this->quote_contents = (array) WC()->session->get( 'quotes' );
		}
	}

	/**
	 * Get subtotal.
	 *
	 * @since 3.2.0
	 * @return float
	 */
	public function get_subtotal_tax() {
		return apply_filters( 'addify_quote_' . __FUNCTION__, $this->get_totals_var( 'subtotal_tax' ) );
	}

	/**
	 * Get a total.
	 *
	 * @since 3.2.0
	 * @param string $key Key of element in $totals array.
	 * @return mixed
	 */
	protected function get_totals_var( $key ) {
		return isset( $this->totals[ $key ] ) ? $this->totals[ $key ] : $this->default_totals[ $key ];
	}

	/**
	 * Returns 'incl' if tax should be included in cart, otherwise returns 'excl'.
	 *
	 * @return string
	 */
	public function get_tax_price_display_mode() {
		if ( WC()->customer && WC()->customer->get_is_vat_exempt() ) {
			return 'excl';
		}

		return get_option( 'woocommerce_tax_display_cart' );
	}

	/**
	 * Return whether or not the cart is displaying prices including tax, rather than excluding tax.
	 *
	 * @since 3.3.0
	 * @return bool
	 */
	public function display_prices_including_tax() {
		return apply_filters( 'addify_quote_' . __FUNCTION__, 'incl' === $this->get_tax_price_display_mode() );
	}

	/**
	 * Get the product row price per item.
	 *
	 * @param object $product Product object.
	 *
	 * @param array  $args formatted args.
	 *
	 * @param array  $view returns view.
	 */
	public function get_product_price( $product, $args = array(), $view = 'view' ) {

		if ( $this->display_prices_including_tax() ) {
			$product_price = wc_get_price_including_tax( $product, $args );
		} else {
			$product_price = wc_get_price_excluding_tax( $product, $args );
		}

		if ( 'edit' == $view ) {
			return $product_price;
		}

		$price_suffix = 'incl' === get_option( 'woocommerce_tax_display_cart' ) ? wc()->countries->inc_tax_or_vat() : '';

		$price_suffix = '<small>' . $price_suffix . '</small>';

		return apply_filters( 'addify_quote_product_price', wc_price( $product_price ) . ' ' . $price_suffix, $product );
	}

	/**
	 * Get the quote subtotal.
	 */
	public function calculate_totals() {

		if ( is_admin() ) {
			return;
		}

		$this->quote_subtotal  = 0;
		$this->quote_tax_total = 0;
		$this->quote_total     = 0;
		$this->_offered_total  = 0;

		if ( empty( $this->quote_contents ) && isset( WC()->session ) ) {
			$this->quote_contents = WC()->session->get( 'quotes' );
		}

		foreach ( (array) $this->quote_contents as $quote_item_key => $quote_item ) {

			if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
				return;
			}

			$product       = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
			$quantity      = $quote_item['quantity'];
			$price         = $product->get_price();
			$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

			$this->_offered_total += $offered_price * intval( $quantity );

			if ( $product->is_taxable() ) {

				if ( ! wc_prices_include_tax() ) {
					$product_subtotal = wc_get_price_including_tax( $product, array( 'qty' => $quantity ) );
				} else {
					$product_subtotal = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );

				}

				$difference_price = ( $price * $quantity ) - $product_subtotal;

				if ( $difference_price < 0 ) {

					$difference_price = $difference_price * -1;
				}

				$this->quote_subtotal  += $price * $quantity;
				$this->quote_tax_total += $difference_price;

			} else {
				$product_subtotal       = $price * $quantity;
				$this->quote_subtotal  += $product_subtotal;
				$this->quote_tax_total += 0;
			}
		}

		$this->quote_total = $this->quote_subtotal + $this->quote_tax_total;
	}

	/**
	 * Get the quote subtotal.
	 *
	 * @param object $contents returns $contents.
	 *
	 * @param object $quote_id  return $quote_id.
	 */
	public function get_calculated_totals( $contents = array(), $quote_id = 0 ) {

		$_shipping_cost = 0;

		if ( ! empty( $quote_id ) ) {
			$_shipping_cost = get_post_meta( $quote_id, 'afrfq_shipping_cost', true );
		}

		$quote_totals = array(
			'_subtotal'       => 0,
			'_offered_total'  => 0,
			'_tax_total'      => 0,
			'_shipping_total' => floatval( $_shipping_cost ),
			'_total'          => 0,
		);

		if ( empty( $contents ) ) {

			if ( isset( WC()->session ) ) {
				$contents = WC()->session->get( 'quotes' );
			}

			if ( empty( $contents ) ) {
				return $quote_totals;
			}
		}

		foreach ( $contents as $quote_item_key => $quote_item ) {

			if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
				continue;
			}

			$product       = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
			$quantity      = $quote_item['quantity'];
			$price         = empty( $quote_item['addons_price'] ) ? $product->get_price() : $quote_item['addons_price'];
			$price         = empty( $quote_item['role_base_price'] ) ? $price : $quote_item['role_base_price'];
			$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

			$quote_totals['_offered_total'] += $offered_price * intval( $quantity );

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

				$quote_totals['_subtotal']  += $price * $quantity;
				$quote_totals['_tax_total'] += $difference_price;

			} else {

				$product_subtotal            = $price * $quantity;
				$quote_totals['_subtotal']  += $product_subtotal;
				$quote_totals['_tax_total'] += 0;
			}
		}

		if ( 'yes' == get_option( 'woocommerce_prices_include_tax' ) ) {
			$quote_totals['_total'] = $quote_totals['_subtotal'] + $quote_totals['_shipping_total'];
		} else {
			$quote_totals['_total'] = $quote_totals['_subtotal'] + $quote_totals['_tax_total'] + $quote_totals['_shipping_total'];
		}

		return $quote_totals;
	}

	/**
	 * Get the product row price per item.
	 */
	public function get_quote_subtotal() {

		$quote_subtotal = wc_price( $this->quote_subtotal );

		return apply_filters( 'addify_quote_subtotal', $quote_subtotal, $this->quote_contents );
	}

	/**
	 * Get the product row price per item.
	 */
	public function get_quote_offered_subtotal() {

		$quote_subtotal = wc_price( $this->_offered_total );

		return apply_filters( 'addify_quote_offered_total', $quote_subtotal, $this->quote_contents );
	}

	/**
	 * Get the product row price per item.
	 */
	public function get_quote_total() {

		$quote_total = wc_price( $this->quote_total );

		return apply_filters( 'addify_quote_total', $quote_total, $this->quote_contents );
	}

	/**
	 * Get the product row price per item.
	 */
	public function get_quote_tax_total() {

		$quote_tax_total = wc_price( $this->quote_tax_total );

		return apply_filters( 'addify_quote_tax_total', $quote_tax_total, $this->quote_contents );
	}

	/**
	 * Get the product row price per item.
	 */
	public function get__offered_total() {

		$offered_total = wc_price( $this->_offered_total );

		return apply_filters( 'addify_quote_tax_total', $offered_total, $this->quote_contents );
	}

	public function add_tax_product_price( $price, $product ) {

		$return_price = $price;

		if ( wc_tax_enabled() ) {

			if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {

				$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
				$taxes     = WC_Tax::calc_tax( $price, $tax_rates, false );

				if ( 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ) {
					$taxes_total = array_sum( $taxes );
				} else {
					$taxes_total = array_sum( array_map( 'wc_round_tax_total', $taxes ) );
				}

				$return_price = round( $price + $taxes_total, wc_get_price_decimals() );

				return $return_price;
			}
		}

		return $return_price;
	}

	/**
	 * Get the product row subtotal.
	 *
	 * Gets the tax etc to avoid rounding issues.
	 *
	 * When on the checkout (review order), this will get the subtotal based on the customer's tax rate rather than the base rate.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @param int        $price returns price.
	 *
	 * @param object     $quantity returns quantity.
	 */
	public function get_product_offered_subtotal( $product, $price, $quantity ) {

		if ( $product->is_taxable() ) {

			if ( $this->display_prices_including_tax() ) {
				$row_price        = $this->add_tax_product_price( $price, $product );
				$product_subtotal = wc_price( $row_price );

				if ( ! wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			} else {
				$row_price        = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
				$product_subtotal = wc_price( $row_price );

				if ( wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			}
		} else {
			$row_price        = $price * $quantity;
			$product_subtotal = wc_price( $row_price );
		}

		return apply_filters( 'addify_quote_product_subtotal', $product_subtotal, $product, $quantity, $this );
	}


	/**
	 * Get the product row subtotal.
	 *
	 * Gets the tax etc to avoid rounding issues.
	 *
	 * When on the checkout (review order), this will get the subtotal based on the customer's tax rate rather than the base rate.
	 *
	 * @param WC_Product $product Product object.
	 *
	 * @param object     $quantity returns quantity.
	 *
	 * @param array      $args returns args.
	 */
	public function get_product_subtotal( $product, $quantity, $args = array() ) {
		$price = empty( $args['price'] ) ? $product->get_price() : $args['price'];

		if ( $product->is_taxable() ) {

			if ( $this->display_prices_including_tax() ) {
				$row_price        = wc_get_price_including_tax( $product, $args );
				$product_subtotal = wc_price( $row_price );

				if ( ! wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}
			} else {
				$row_price        = wc_get_price_excluding_tax( $product, $args );
				$product_subtotal = wc_price( $row_price );

				if ( wc_prices_include_tax() && $this->quote_tax_total > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			}
		} else {
			$row_price        = $price * $quantity;
			$product_subtotal = wc_price( $row_price );
		}

		return apply_filters( 'addify_quote_product_subtotal', $product_subtotal, $product, $quantity, $this );
	}

	public function check_quote_availability_for_variation( $variation_id ) {

		if ( empty( $variation_id ) ) {
			return true;
		}

		$variation_avaiable = get_post_meta( $variation_id, 'disable_rfq', true );

		if ( ! empty( $variation_avaiable ) && 'show' !== $variation_avaiable ) {
			return false;
		}

		$variation = wc_get_product( $variation_id );

		if ( ! $variation->is_in_stock() && 'yes' !== get_option( 'enable_o_o_s_products' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Add a product to the Quote.
	 *
	 * @throws Exception Plugins can throw an exception to prevent adding to quote.
	 * @param object $form_data returns form_data.
	 * @param int    $product_id contains the id of the product to add to the quote.
	 * @param int    $quantity contains the quantity of the item to add.
	 * @param int    $variation_id ID of the variation being added to the quote.
	 * @param array  $variation attribute values.
	 * @param array  $quote_item_data extra quote item data we want to pass into the item.
	 * @param int    $return_contents returns contents.
	 */
	public function add_to_quote( $form_data, $product_id = 0, $quantity = 1, $variation_id = 0, $variation = array(), $quote_item_data = array(), $return_contents = false ) {
		try {

			$product_id   = absint( $product_id );
			$variation_id = absint( $variation_id );

			// Ensure we don't add a variation to the quote directly by variation ID.
			if ( 'product_variation' === get_post_type( $product_id ) ) {
				$variation_id = $product_id;
				$product_id   = wp_get_post_parent_id( $variation_id );
			}

			$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
			$quantity     = apply_filters( 'addify_add_to_quote_quantity', $quantity, $product_id );

			if ( $quantity <= 0 || ! $product_data || 'trash' === $product_data->get_status() ) {
				return false;
			}

			if ( $product_data->is_type( 'variation' ) ) {
				$missing_attributes = array();
				$parent_data        = wc_get_product( $product_data->get_parent_id() );

				$variation_attributes = $product_data->get_variation_attributes();
				// Filter out 'any' variations, which are empty, as they need to be explicitly specified while adding to quote.
				$variation_attributes = array_filter( $variation_attributes );

				// Gather posted attributes.
				$posted_attributes = array();
				foreach ( $parent_data->get_attributes() as $attribute ) {
					if ( ! $attribute['is_variation'] ) {
						continue;
					}
					$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );

					if ( isset( $variation[ $attribute_key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( $attribute['is_taxonomy'] ) {
							// Don't use wc_clean as it destroys sanitized characters.
							$value = sanitize_title( wp_unslash( $variation[ $attribute_key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						} else {
							$value = html_entity_decode( wc_clean( wp_unslash( $variation[ $attribute_key ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						}

						// Don't include if it's empty.
						if ( ! empty( $value ) || '0' === $value ) {
							$posted_attributes[ $attribute_key ] = $value;
						}
					}
				}

				// Merge variation attributes and posted attributes.
				$posted_and_variation_attributes = array_merge( $variation_attributes, $posted_attributes );

				// If no variation ID is set, attempt to get a variation ID from posted attributes.
				if ( empty( $variation_id ) ) {
					$data_store   = WC_Data_Store::load( 'product' );
					$variation_id = $data_store->find_matching_product_variation( $parent_data, $posted_attributes );
				}

				// Do we have a variation ID?
				if ( empty( $variation_id ) ) {
					throw new Exception( __( 'Please choose product options&hellip;', 'addify_rfq' ) );
				}

				// Do we have a variation ID?
				if ( ! $this->check_quote_availability_for_variation( $variation_id ) ) {
					throw new Exception( __( 'Quote is not permitted for selected variation &hellip;', 'addify_rfq' ) );
				}

				// Check the data we have is valid.
				$variation_data = wc_get_product_variation_attributes( $variation_id );
				$attributes     = array();

				foreach ( $parent_data->get_attributes() as $attribute ) {
					if ( ! $attribute['is_variation'] ) {
						continue;
					}

					// Get valid value from variation data.
					$attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
					$valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ] : '';

					/**
					 * If the attribute value was posted, check if it's valid.
					 *
					 * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
					 */
					if ( isset( $posted_and_variation_attributes[ $attribute_key ] ) ) {
						$value = $posted_and_variation_attributes[ $attribute_key ];

						// Allow if valid or show error.
						if ( $valid_value === $value ) {
							$attributes[ $attribute_key ] = $value;
						} elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs(), true ) ) {
							// If valid values are empty, this is an 'any' variation so get all possible values.
							$attributes[ $attribute_key ] = $value;
						} else {
							/* translators: %s: Attribute name. */
							throw new Exception( sprintf( __( 'Invalid value posted for %s', 'addify_rfq' ), wc_attribute_label( $attribute['name'] ) ) );
						}
					} elseif ( '' === $valid_value ) {
						$missing_attributes[] = wc_attribute_label( $attribute['name'] );
					}

					$variation = $attributes;
				}
				if ( ! empty( $missing_attributes ) && ! is_admin() ) {
					/* translators: %s: Attribute name. */
					throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'addify_rfq' ), wc_format_list_of_items( $missing_attributes ) ) );
				}
			}

			// Load quote item data - may be added by other plugins.
			$quote_item_data = (array) apply_filters( 'addify_add_quote_item_data', $quote_item_data, $product_id, $variation_id, $quantity, $form_data );

			// Generate a ID based on product ID, variation ID, variation data, and other quote item data.
			$quote_id = $this->generate_quote_id( $product_id, $variation_id, $variation, $quote_item_data );

			// Find the quote item key in the existing quote.
			$quote_item_key = $this->find_product_in_quote( $quote_id );

			// Force quantity to 1 if sold individually and check for existing item in quote.
			if ( $product_data->is_sold_individually() ) {
				$quantity       = apply_filters( 'addify_add_to_quote_sold_individually_quantity', 1, $quantity, $product_id, $variation_id, $quote_item_data );
				$found_in_quote = apply_filters( 'addify_add_to_quote_sold_individually_found_in_quote', $quote_item_key && $this->quote_contents[ $quote_item_key ]['quantity'] > 0, $product_id, $variation_id, $quote_item_data, $quote_id );

				if ( $found_in_quote ) {
					/* translators: %s: product name */
					$message = sprintf( __( 'You cannot add another "%s" to your quote.', 'addify_rfq' ), $product_data->get_name() );

					/**
					 * Filters message about more than 1 product being added to quote.
					 *
					 * @since 4.5.0
					 * @param string     $message Message.
					 * @param WC_Product $product_data Product data.
					 */
					$message = apply_filters( 'addify_quote_product_cannot_add_another_message', $message, $product_data );

					throw new Exception( sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', '', __( 'View Quote', 'addify_rfq' ), $message ) );
				}
			}

			if ( ! $product_data->is_purchasable() ) {
				$message = __( 'Sorry, this product cannot be purchased.', 'addify_rfq' );
				/**
				 * Filters message about product unable to be purchased.
				 *
				 * @since 3.8.0
				 * @param string     $message Message.
				 * @param WC_Product $product_data Product data.
				*/

				$message = apply_filters( 'addify_quote_product_cannot_be_purchased_message', $message, $product_data );
				throw new Exception( $message );
			}

			// If quote_item_key is set, the item is already in the quote.
			if ( ! empty( $quote_item_key ) ) {

				$this->quote_contents[ $quote_item_key ]['quantity'] += intval( $quantity );

			} else {
				$quote_item_key = $quote_id;

				$incr_offered_price = floatval( get_option( 'afrfq_enable_off_price_increase' ) );

				$offered_price = $product_data->get_price();

				$args          = array(
					'qty'   => 1,
					'price' => $offered_price,
				);
				$offered_price = $this->get_product_price( $product_data, $args, 'edit' );

				if ( ! empty( $incr_offered_price ) ) {

					$offered_price += ( $incr_offered_price * $product_data->get_price() ) / 100;
				}

				// Add item after merging with $quote_item_data - hook to allow plugins to modify quote item.
				$this->quote_contents[ $quote_item_key ] = apply_filters(
					'addify_add_quote_item',
					array_merge(
						$quote_item_data,
						array(
							'key'           => $quote_item_key,
							'product_id'    => $product_id,
							'variation_id'  => $variation_id,
							'variation'     => $variation,
							'quantity'      => $quantity,
							'offered_price' => $offered_price,
							'data'          => $product_data,
							'data_hash'     => wc_get_cart_item_data_hash( $product_data ),
						)
					),
					$quote_item_key
				);
			}

			$this->quote_contents = apply_filters( 'addify_quote_contents_changed', $this->quote_contents );

			if ( $return_contents ) {
				return $this->quote_contents;
			} else {

				if ( is_user_logged_in() ) {
					update_user_meta( get_current_user_id(), 'addify_quote', $this->quote_contents );
				}

				wc()->session->set( 'quotes', $this->quote_contents );

				do_action( 'addify_quote_session_changed' );
			}

			do_action( 'addify_add_to_quote', $quote_item_key, $product_id, $quantity, $variation_id, $variation, $quote_item_data );

			return $quote_item_key;

		} catch ( Exception $e ) {
			if ( $e->getMessage() && ! is_admin() ) {
				wc_add_notice( $e->getMessage(), 'error' );
			}
			return false;
		}
	}

	/**
	 * Generate a unique ID for the quote item being added.
	 *
	 * @param int   $product_id - id of the product the key is being generated for.
	 * @param int   $variation_id of the product the key is being generated for.
	 * @param array $variation data for the quote item.
	 * @param array $quote_item_data other quote item data passed which affects this items uniqueness in the quote.
	 * @return string quote item key
	 */
	public function generate_quote_id( $product_id, $variation_id = 0, $variation = array(), $quote_item_data = array() ) {
		$id_parts = array( $product_id );

		if ( $variation_id && 0 !== $variation_id ) {
			$id_parts[] = $variation_id;
		}

		if ( is_array( $variation ) && ! empty( $variation ) ) {
			$variation_key = '';
			foreach ( $variation as $key => $value ) {
				$variation_key .= trim( $key ) . trim( $value );
			}
			$id_parts[] = $variation_key;
		}

		if ( is_array( $quote_item_data ) && ! empty( $quote_item_data ) ) {
			$quote_item_data_key = '';
			foreach ( $quote_item_data as $key => $value ) {
				if ( is_array( $value ) || is_object( $value ) ) {
					$value = http_build_query( $value );
				}
				$quote_item_data_key .= trim( $key ) . trim( $value );

			}
			$id_parts[] = $quote_item_data_key;
		}

		return apply_filters( 'addify_quote_id', md5( implode( '_', $id_parts ) ), $product_id, $variation_id, $variation, $quote_item_data );
	}




	/**
	 * Check if product is in the quote and return quote item key.
	 *
	 * Cart item key will be unique based on the item and its properties, such as variations.
	 *
	 * @param mixed $quote_id id of product to find in the quote.
	 * @return string quote item key
	 */
	public function find_product_in_quote( $quote_key = false ) {
		if ( false !== $quote_key ) {
			if ( is_array( $this->quote_contents ) && isset( $this->quote_contents[ $quote_key ] ) ) {
				return $quote_key;
			}
		}
		return '';
	}

	/**
	 * Convert quote to order.
	 *
	 * @param mixed $quote_id id of quote to convert.
	 */
	public function convert_quote_to_order( $quote_id = false ) {

		if ( false === $quote_id ) {
			wc_add_notice( __( 'Quote ID is required to convert a quote to order.', 'addify_rfq' ), 'error' );
			return false;
		}

		$quote_contents = get_post_meta( $quote_id, 'quote_contents', true );

		if ( empty( $quote_contents ) ) {
			wc_add_notice( __( 'Quote Contents are empty.', 'addify_rfq' ), 'error' );
			return false;
		}

		$quotes      = $quote_contents;
		$quote_order = wc_create_order();

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

			$product->set_price( $offered_price );

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

					/*
					 * If there is an add-on price, add the price of the add-on
					 * to the label name.
					 */
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

		$customer_id = get_post_meta( $quote_id, '_customer_user', true );

		$quote_user = get_user_by( 'id', $customer_id );

		$af_fields_obj = new AF_R_F_Q_Quote_Fields();

		$billing_address  = $af_fields_obj->afrfq_get_billing_data( $quote_id );
		$shipping_address = $af_fields_obj->afrfq_get_shipping_data( $quote_id );

		$quote_order->set_address( $billing_address, 'billing' );
		$quote_order->set_address( $shipping_address, 'shipping' );
		$quote_order->set_customer_id( intval( $customer_id ) );
		$quote_order->calculate_totals(); // updating totals.

		$quote_order->save(); // Save the order data.

		$current_user = wp_get_current_user();

		$current_user = isset( $current_user->ID ) ? (string) $current_user->user_login : get_post_meta( $quote_id, 'afrfq_name_field', true );

		update_post_meta( $quote_id, 'quote_status', 'af_converted' );
		update_post_meta( $quote_id, 'converted_by_user', $current_user );
		update_post_meta( $quote_id, 'converted_by', __( 'Customer', 'addify_rfq' ) );

		do_action( 'addify_rfq_quote_converted_to_order', $quote_order->get_id(), $quote_id );

		do_action( 'addify_rfq_send_quote_email_to_customer', $quote_id );
		do_action( 'addify_rfq_send_quote_email_to_admin', $quote_id );
		wc_add_notice(
			sprintf(
			/* translators: 1: Quote ID, 2: Order ID */
			__( 'Your Quote# %1$s has been converted to Order# %2$s.', 'addify_rfq' ),
			$quote_id,
			$quote_order->get_id()
			),
			'success'
		);
		// wc_add_notice( sprintf( __( 'Your Quote# %1$s has been converted to Order# %2$s.', 'addify_rfq' ), $quote_id, $quote_order->get_id() ), 'success' );
		
		wp_safe_redirect( $quote_order->get_view_order_url() );
		exit;
	}

	/**
	 * Convert quote to order.
	 *
	 * @param object $post_data returns post data.
	 */
	public function insert_new_quote( $post_data = array() ) {

		if ( empty( $post_data ) || ! is_array( $post_data ) ) {
			if ( ! is_admin() ) {
				wc_add_notice( __( 'Post data should not be empty to create a quote.', 'addify_rfq' ), 'error' );
			}
			return;
		}

		if ( empty( wc()->session->get( 'quotes' ) ) ) {
			if ( ! is_admin() ) {
				wc_add_notice( __( 'No item found in quote basket.', 'addify_rfq' ), 'error' );
			}
			return;
		}

		$af_fields_obj = new AF_R_F_Q_Quote_Fields();
		$validation    = $af_fields_obj->afrfq_validate_fields_data( $post_data );

		if ( is_array( $validation ) ) {

			foreach ( $validation as $key => $message ) {

				if ( empty( $message ) ) {
					continue;
				}
				wc_add_notice( $message, 'error' );
			}
			return;
		}

		try {

			$quotes = WC()->session->get( 'quotes' );

			foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

				if ( isset( $post_data['quote_qty'][ $quote_item_key ] ) ) {
					$quotes[ $quote_item_key ]['quantity'] = intval( $post_data['quote_qty'][ $quote_item_key ] );
				}
			}

			WC()->session->set( 'quotes', $quotes );

			do_action( 'addify_quote_session_changed' );

			$quote = WC()->session->get( 'quotes' );

			$quote_args = array(
				'post_title'   => '',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'addify_quote',
			);

			$quote_id = wp_insert_post( $quote_args );

			$user = wp_get_current_user();

			if ( is_a( $user, 'WP_User' ) ) {
				$customer_name = isset( $data['afrfq_name_field'] ) ? $data['afrfq_name_field'] : $user->display_name;
			} else {
				$customer_name = isset( $data['afrfq_name_field'] ) ? $data['afrfq_name_field'] : 'Guest';
			}

			$post_title = $customer_name . '( ' . $quote_id . ' )';
			$my_post    = array(
				'ID'         => $quote_id,
				'post_title' => $post_title,
			);

			wp_update_post( $my_post );

			// Save Quote Meta.
			add_post_meta( $quote_id, 'quote_contents', WC()->session->get( 'quotes' ) );
			add_post_meta( $quote_id, '_customer_user', get_current_user_id() );
			add_post_meta( $quote_id, 'quote_status', 'af_pending' );

			$quote_fields = $af_fields_obj->afrfq_get_fields_enabled();

			if ( ! empty( $quote_fields ) && is_array( $quote_fields ) ) {

				foreach ( $quote_fields as $key => $field ) {

					$field_id          = $field->ID;
					$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
					$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
					$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );

					if ( isset( $post_data[ $afrfq_field_name ] ) && ! empty( $post_data[ $afrfq_field_name ] ) ) {

						if ( 'file' === $afrfq_field_type ) {

							$file        = isset( $post_data[ $afrfq_field_name ]['name'] ) ? time() . sanitize_text_field( $post_data[ $afrfq_field_name ]['name'] ) : time();
							$target_path = AFRFQ_UPLOAD_DIR . $file;

							if ( isset( $post_data[ $afrfq_field_name ]['tmp_name'] ) ) {

								if ( move_uploaded_file( sanitize_text_field( $post_data[ $afrfq_field_name ]['tmp_name'] ), $target_path ) ) {

									update_post_meta( $quote_id, $afrfq_field_name, $file );
								}
							}
						} else {

							update_post_meta( $quote_id, $afrfq_field_name, $post_data[ $afrfq_field_name ] );
						}
					}
				}
			}

			if ( is_user_logged_in() ) {
				update_user_meta( get_current_user_id(), 'addify_quote', '' );
			}

			WC()->session->set( 'quotes', null );
			wc()->session->set( 'quote_fields_data', null );

			do_action( 'addify_quote_created', $quote_id );
			do_action( 'addify_rfq_send_quote_email_to_customer', $quote_id );
			do_action( 'addify_rfq_send_quote_email_to_admin', $quote_id );

			if ( ! empty( get_option( 'afrfq_success_message' ) ) ) {

				wc_add_notice( get_option( 'afrfq_success_message' ), 'success' );

			} else {

				wc_add_notice( __( 'Your quote has been submitted successfully.', 'addify_rfq' ), 'success' );

			}

			if ( 'yes' === get_option( 'afrfq_redirect_after_submission' ) && ! empty( get_option( 'afrfq_redirect_url' ) ) ) {
				wp_safe_redirect( esc_url( get_option( 'afrfq_redirect_url' ) ) );
				exit;
			}

			?><input type="hidden" id="hiddenField" value="<?php echo esc_attr( $quote_id ); ?>">
				<?php

		} catch ( Exception $e ) {
			echo esc_html( $e->getMessage() );
		}
	}
}