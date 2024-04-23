<?php
/**
 * REST API role based pricing controller
 *
 * Handles requests to the /role_based_pricing/product endpoint.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Request a Quote Quotes controller class.
 *
 * @extends WC_REST_CRUD_Controller
 */
class AF_R_F_Q_Rest_Quotes_Controller extends WC_REST_CRUD_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'af_quotes';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'addify_quote';


	/**
	 * Role based pricing actions.
	 */
	public function __construct() {
	}

	/**
	 * Register the routes for role based pricing.
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_quotes' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_quote' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Unique identifier for the resource.', 'addify_rfq' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_quote' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param(
							array(
								'default' => 'view',
							)
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_quote' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_quote' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default'     => false,
							'description' => __( 'Whether to bypass trash and force deletion.', 'addify_rfq' ),
							'type'        => 'boolean',
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	public function get_quote_data( $object, $request ) {

		$quote_data = array();

		$quote_data['quote_id']          = $object->ID;
		$quote_data['title']             = $object->post->post_title;
		$quote_data['date_created']      = $object->post->post_date;
		$quote_data['date_created_gmt']  = $object->post->post_date_gmt;
		$quote_data['date_modified']     = $object->post->post_modified;
		$quote_data['date_modified_gmt'] = $object->post->post_modified_gmt;
		$quote_data['post_status']       = $object->post->post_status;
		$quote_data['customer']          = get_post_meta( $object->ID, '_customer_user', true );
		$quote_data['quote_status']      = $object->get_status();
		$quote_data['customer_ifo']      = $object->get_customer_info();
		$quote_data['items']             = $object->get_items();
		$quote_data['totals']            = $object->get_totals();

		return $quote_data;
	}

	public function get_quotes( $request ) {

		$quote_ids = $this->get_quote_ids( $request );

		$quotes_data = array();

		foreach ( $quote_ids as $quote_id ) {

			$object = $this->get_object( $quote_id );

			if ( $object && 0 !== $object->get_id() && ! wc_rest_check_post_permissions( $this->post_type, 'read', $object->get_id() ) ) {
				continue;
			}

			$quotes_data[ $quote_id ] = $this->get_quote_data( $object, $request );
		}

		return rest_ensure_response( $quotes_data );
	}

	public function create_quote( $request ) {

		if ( ! empty( $request['id'] ) ) {
			/* translators: %s: post type */
			return new WP_Error( "woocommerce_rest_{$this->post_type}_exists", sprintf( __( 'Cannot create existing %s.', 'addify_rfq' ), $this->post_type ), array( 'status' => 400 ) );
		}

		$object = $this->save_quote( $request );

		$request->set_param( 'context', 'edit' );
		$response_data = $this->get_quote_data( $object, $request );
		$response      = rest_ensure_response( $response_data );
		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $object->get_id() ) ) );

		return $response;
	}

	public function get_quote( $request ) {

		$quote_id = isset( $request['id'] ) ? intval( $request['id'] ) : 0;

		if ( $quote_id && 'addify_quote' !== get_post_type( $quote_id ) ) {

			/* translators: %s: post type */
			return new WP_Error( 'woocommerce_invalid_quote_id', sprintf( __( 'Invalid Quote ID %s.', 'addify_rfq' ), $quote_id ), array( 'status' => 400 ) );
		}

		$object = $this->get_object( $quote_id );

		if ( $object && is_a( $object->post, 'WP_Post' ) ) {
			$data     = $this->get_quote_data( $object, $request );
			$response = rest_ensure_response( $data );

			return $response;
		}
	}

	public function update_quote( $request ) {

		$quote_id = isset( $request['id'] ) ? intval( $request['id'] ) : 0;

		if ( $quote_id && 'addify_quote' !== get_post_type( $quote_id ) ) {

			/* translators: %s: post type */
			return new WP_Error( 'woocommerce_invalid_quote_id', sprintf( __( 'Invalid Quote ID %s.', 'addify_rfq' ), $quote_id ), array( 'status' => 400 ) );
		}

		if ( $quote_id ) {

			$object = $this->save_quote( $request );

			$request->set_param( 'context', 'edit' );
			$response_data = $this->get_quote_data( $object, $request );
			$response      = rest_ensure_response( $response_data );

			return $response;
		}
	}

	public function delete_quote( $request ) {

		$quote_id = isset( $request['id'] ) ? intval( $request['id'] ) : 0;

		if ( $quote_id && 'addify_quote' !== get_post_type( $quote_id ) ) {

			/* translators: %s: post type */
			return new WP_Error( 'woocommerce_invalid_quote_id', sprintf( __( 'Invalid Quote ID %s.', 'addify_rfq' ), $quote_id ), array( 'status' => 400 ) );
		}

		if ( $quote_id ) {

			$force  = isset( $request['force'] ) ? (bool) $request['force'] : false;
			$object = $this->get_object( (int) $request['id'] );
			$result = false;

			if ( ! $object || 0 === $object->get_id() ) {
				return new WP_Error( "woocommerce_rest_{$this->post_type}_invalid_id", __( 'Invalid ID.', 'addify_rfq' ), array( 'status' => 404 ) );
			}

			$supports_trash = true;

			if ( ! wc_rest_check_post_permissions( $this->post_type, 'delete', $object->get_id() ) ) {
				/* translators: %s: post type */
				return new WP_Error( "woocommerce_rest_user_cannot_delete_{$this->post_type}", sprintf( __( 'Sorry, you are not allowed to delete %s.', 'addify_rfq' ), $this->post_type ), array( 'status' => rest_authorization_required_code() ) );
			}

			$request->set_param( 'context', 'edit' );

			$data = $this->get_quote_data( $object, $request );

			$result = false;

			if ( $force ) {
				$result = wp_trash_post( $quote_id );
			} else {
				$result = wp_delete_post( $quote_id, $force );
			}

			if ( ! $result ) {
				/* translators: %s: post type */
				return new WP_Error( 'woocommerce_rest_cannot_delete', sprintf( __( 'The %s cannot be deleted.', 'addify_rfq' ), $this->post_type ), array( 'status' => 500 ) );
			}

			return rest_ensure_response( $data );
		}
	}

	public function save_quote( $request ) {

		try {

			if ( empty( $request['id'] ) ) {
				$args = array(
					'post_type'    => 'addify_quote',
					'post_status'  => 'publish',
					'post_title'   => isset( $request['title'] ) ? sanitize_text_field( $request['title'] ) : '',
					'post_content' => isset( $request['content'] ) ? sanitize_text_field( $request['content'] ) : '',
				);

				$post_id = wp_insert_post( $args );
				$object  = $this->get_object( $post_id );

			} else {

				$object = $this->get_object( $request['id'] );
			}

			$quote_id = $object->get_id();

			$customer_info = isset( $request['customer_info'] ) ? (array) $request['customer_info'] : array();

			if ( ! empty( $customer_info ) ) {
				// Update customer info.
				$af_fields_obj = new AF_R_F_Q_Quote_Fields();
				$quote_fields  = $af_fields_obj->afrfq_get_fields_enabled();

				foreach ( $quote_fields as $key => $field ) {

					$field_id          = $field->ID;
					$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
					$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
					$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );

					if ( isset( $customer_info[ $afrfq_field_name ] ) && ! empty( $customer_info[ $afrfq_field_name ] ) ) {

						if ( 'file' === $afrfq_field_type ) {

							continue;

						} else {

							update_post_meta( $quote_id, $afrfq_field_name, $customer_info[ $afrfq_field_name ] );
						}
					}
				}
			}

			if ( isset( $request['customer'] ) ) {
				update_post_meta( $quote_id, '_customer_user', $request['customer'] );
			}

			if ( empty( $request['id'] ) ) {

				$user = get_user_by( 'id', get_post_meta( $quote_id, '_customer_user', true ) );

				if ( is_a( $user, 'WP_User' ) ) {
					$customer_name = isset( $request['afrfq_name_field'] ) ? $request['afrfq_name_field'] : $user->display_name;
				} else {
					$customer_name = isset( $request['afrfq_name_field'] ) ? $request['afrfq_name_field'] : 'Guest';
				}

				$post_title = $customer_name . '( ' . $quote_id . ' )';
				$my_post    = array(
					'ID'         => $quote_id,
					'post_title' => $post_title,
				);

				wp_update_post( $my_post );
			}

			// Update Quote.
			$quote_items    = isset( $request['items'] ) ? $request['items'] : ( array() );
			$quote_contents = get_post_meta( $object->get_id(), 'quote_contents', true );

			if ( ! empty( $quote_items ) ) {

				foreach ( $quote_items as $quote_item_key => $quote_item ) {

					$key          = isset( $quote_item['key'] ) ? $quote_item['key'] : '';
					$product_id   = isset( $quote_item['product_id'] ) ? intval( $quote_item['product_id'] ) : 0;
					$variation_id = isset( $quote_item['variation_id'] ) ? intval( $quote_item['variation_id'] ) : 0;
					$product_id   = empty( $product_id ) && ! empty( $variation_id ) ? wp_get_post_parent_id( $variation_id ) : $product_id;
					$product      = ! empty( $variation_id ) ? wc_get_product( $variation_id ) : wc_get_product( $product_id );

					if ( $product ) {

						$variation     = isset( $quote_item['variation'] ) ? intval( $quote_item['variation'] ) : array();
						$quantity      = isset( $quote_item['quantity'] ) ? intval( $quote_item['quantity'] ) : 1;
						$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $product->get_price();
						$price         = isset( $quote_item['price'] ) ? floatval( $quote_item['price'] ) : $product->get_price();
					}

					if ( isset( $quote_contents[ $key ] ) ) {

						if ( ! empty( $product_id ) ) {
							$quote_contents[ $key ]['product_id'] = $product_id;
						}

						if ( ! empty( $variation_id ) ) {
							$quote_contents[ $key ]['variation_id'] = $variation_id;
						}

						if ( ! empty( $variation ) ) {
							$quote_contents[ $key ]['variation'] = $variation;
						}

						if ( ! empty( $quantity ) ) {
							$quote_contents[ $key ]['quantity'] = $quantity;
						}

						if ( ! empty( $offered_price ) ) {
							$quote_contents[ $key ]['offered_price'] = $offered_price;
						}

						if ( ! empty( $price ) ) {
							$quote_contents[ $key ]['price'] = $price;
						}
					} else {

						$quote_contents[ $key ] = array(
							array(
								'key'           => $key,
								'product_id'    => $product_id,
								'variation_id'  => $variation_id,
								'variation'     => $variation,
								'quantity'      => $quantity,
								'offered_price' => $offered_price,
								'data'          => $product,
								'data_hash'     => wc_get_cart_item_data_hash( $product_data ),
							),
						);
					}
				}

				update_post_meta( $object->get_id(), 'quote_contents', $quote_contents );
			}

			if ( isset( $request['afrfq_shipping_cost'] ) ) {
				update_post_meta( $object->get_id(), 'afrfq_shipping_cost', sanitize_text_field( wp_unslash( $request['afrfq_shipping_cost'] ) ) );
			}

			do_action( 'addify_quote_contents_updated', $object->get_id() );

			if ( isset( $request['quote_status'] ) ) {

				$old_status = get_post_meta( $object->get_id(), 'quote_status', true );
				update_post_meta( $object->get_id(), 'old_status', $request['quote_status'] );
				update_post_meta( $object->get_id(), 'quote_status', $request['quote_status'] );

				do_action( 'addify_rfq_quote_status_updated', $object->get_id(), $request['quote_status'], $old_status );
			}

			return $object;

		} catch ( WC_Data_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), $e->getErrorData() );
		} catch ( WC_REST_Exception $e ) {
			return new WP_Error( $e->getErrorCode(), $e->getMessage(), array( 'status' => $e->getCode() ) );
		}
	}

	public function get_quote_ids( $request ) {

		$posts_per_page = isset( $request['posts_per_page'] ) ? intval( $request['posts_per_page'] ) : 10;

		$args = array(
			'post_type'      => 'addify_quote',
			'post_status'    => 'publish',
			'paged'          => isset( $request['paged'] ) ? $request['paged'] : 1,
			'posts_per_page' => $posts_per_page,
			'fields'         => 'ids',
		);

		return get_posts( $args );
	}

	protected function get_object( $quote_id ) {

		return new AF_R_F_Q_Quote_Controller( $quote_id );
	}

	/**
	 * Get the Product's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'Product role based pricing rules',
			'type'       => 'object',
			'properties' => array(
				'id'                => array(
					'description' => __( 'Unique identifier for the resource.', 'addify_rfq' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'title'             => array(
					'description' => __( 'Rule title.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created'      => array(
					'description' => __( "The date the product was created, in the site's timezone.", 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created_gmt'  => array(
					'description' => __( 'The date the product was created, as GMT.', 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_modified'     => array(
					'description' => __( "The date the product was last modified, in the site's timezone.", 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_modified_gmt' => array(
					'description' => __( 'The date the product was last modified, as GMT.', 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'post_status'       => array(
					'description' => __( 'Rule status (post status).', 'addify_rfq' ),
					'type'        => 'string',
					'default'     => 'publish',
					'enum'        => array_merge( array_keys( get_post_statuses() ), array( 'future' ) ),
					'context'     => array( 'view', 'edit' ),
				),
				'customer'          => array(
					'description' => __( 'Customer ID/User ID.', 'addify_rfq' ),
					'type'        => 'number',
					'default'     => 'publish',
					'context'     => array( 'view', 'edit' ),
				),
				'customers_info'    => array(
					'description' => __( 'Customer Details filled by customer customer when submitting the form.', 'addify_rfq' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'array',
						'properties' => array(
							'customer_id' => array(
								'description' => __( 'User ID.', 'addify_rfq' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
				'items'             => array(
					'description' => __( 'List of quote items along with Quantity and other data.', 'addify_rfq' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'key'           => array(
								'description' => __( 'Key of item.', 'addify_rfq' ),
								'type'        => 'string',
								'context'     => array( 'view', 'edit' ),
							),
							'product_id'    => array(
								'description' => __( 'Product ID.', 'addify_rfq' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'variation_id'  => array(
								'description' => __( 'Variation ID.', 'addify_rfq' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'variation'     => array(
								'description' => __( 'Variation.', 'addify_rfq' ),
								'type'        => 'array',
								'context'     => array( 'view', 'edit' ),
							),
							'quantity'      => array(
								'description' => __( 'Quantity.', 'addify_rfq' ),
								'type'        => 'integer',
								'context'     => array( 'view', 'edit' ),
							),
							'price'         => array(
								'description' => __( 'Price of item.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'offered_price' => array(
								'description' => __( 'Offered Price.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'subtotal'      => array(
								'description' => __( 'Subtotal of item.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'offered_total' => array(
								'description' => __( 'Offered price subtotal.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'tax_total'     => array(
								'description' => __( 'Total tax of item.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'total'         => array(
								'description' => __( 'Total of product.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
						),
						'required'   => array(
							'key',
							'product_id',
						),
					),
				),
				'totals'            => array(
					'description' => __( 'Totals of quote.', 'addify_rfq' ),
					'type'        => 'object',
					'context'     => array( 'view', 'edit' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'subtotal'      => array(
								'description' => __( 'Subtotal of Quote.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'offered_total' => array(
								'description' => __( 'Offered price subtotal.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'tax_total'     => array(
								'description' => __( 'Total tax.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
							'total'         => array(
								'description' => __( 'Total of Quote.', 'addify_rfq' ),
								'type'        => 'number',
								'context'     => array( 'view', 'edit' ),
							),
						),
					),
				),
			),
		);
		return $this->add_additional_fields_schema( $schema );
	}
}
