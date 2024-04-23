<?php
/**
 * REST API role based pricing controller
 *
 * Handles requests to the /role_based_rules endpoint.
 *
 * @package Addify\RestApi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API role based pricing controller class.
 *
 * @package Addify\RestApi
 * @extends WC_REST_CRUD_Controller
 */
class AF_R_F_Q_Rest_Rules_Controller extends WC_REST_CRUD_Controller {

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
	protected $rest_base = 'af_rfq';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'addify_rfq';

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
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
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
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param(
							array(
								'default' => 'view',
							)
						),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Check if a given request has access to read an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$object = $this->get_object( (int) $request['id'] );

		if ( $object && 0 !== $object->ID && ! wc_rest_check_post_permissions( $this->post_type, 'read', $object->ID ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}

	/**
	 * Prepare a single product output for response.
	 *
	 * @param WC_Data         $object  Object data.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @since  3.0.0
	 * @return WP_REST_Response
	 */
	public function prepare_object_for_response( $object, $request ) {
		$context       = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$this->request = $request;
		$data          = $this->get_rule_data( $object, $context, $request );

		$data     = $this->filter_response_by_context( $data, $context );
		$response = rest_ensure_response( $data );
		/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type,
		 * refers to object type being prepared for the response.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $object   Object data.
		 * @param WP_REST_Request  $request  Request object.
		 */
		return apply_filters( "woocommerce_rest_prepare_{$this->post_type}_object", $response, $object, $request );
	}

	public function get_rule_data( $object, $context, $request ) {

		$rule_data = array();

		$rule_data['id']                = $object->get_id();
		$rule_data['title']             = $object->post->post_title;
		$rule_data['priority']          = $object->post->menu_order;
		$rule_data['date_created']      = $object->post->post_date;
		$rule_data['date_created_gmt']  = $object->post->post_date_gmt;
		$rule_data['date_modified']     = $object->post->post_modified;
		$rule_data['date_modified_gmt'] = $object->post->post_modified_gmt;
		$rule_data['post_status']       = $object->post->post_status;
		$rule_data['all_products']      = get_post_meta( $object->get_id(), 'afrfq_apply_on_all_products', true );
		$rule_data['products']          = get_post_meta( $object->get_id(), 'afrfq_hide_products', true );
		$rule_data['categories']        = get_post_meta( $object->get_id(), 'afrfq_hide_categories', true );
		$rule_data['user_roles']        = get_post_meta( $object->get_id(), 'afrfq_hide_user_role', true );
		$rule_data['hide_price']        = get_post_meta( $object->get_id(), 'afrfq_is_hide_price', true );
		$rule_data['hide_price_text']   = get_post_meta( $object->get_id(), 'afrfq_hide_price_text', true );
		$rule_data['hide_addtocart']    = get_post_meta( $object->get_id(), 'afrfq_is_hide_addtocart', true );

		$rule_data['hide_addtocart_text'] = get_post_meta( $object->get_id(), 'afrfq_custom_button_text', true );
		$rule_data['contact_7_form']      = get_post_meta( $object->get_id(), 'afrfq_contact7_form', true );
		$rule_data['custom_button_link']  = get_post_meta( $object->get_id(), 'afrfq_custom_button_link', true );

		return $rule_data;
	}

	protected function get_object( $rule_id ) {

		return new AF_R_F_Q_Quote_Controller( $rule_id );
	}

	/**
	 * Get the Product's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->post_type,
			'type'       => 'object',
			'properties' => array(
				'id'                  => array(
					'description' => __( 'Unique identifier for the resource.', 'addify_rfq' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'title'               => array(
					'description' => __( 'Rule title.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'priority'            => array(
					'description' => __( 'Rule Priority.', 'addify_rfq' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created'        => array(
					'description' => __( "The date the product was created, in the site's timezone.", 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_created_gmt'    => array(
					'description' => __( 'The date the product was created, as GMT.', 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
				),
				'date_modified'       => array(
					'description' => __( "The date the product was last modified, in the site's timezone.", 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'date_modified_gmt'   => array(
					'description' => __( 'The date the product was last modified, as GMT.', 'addify_rfq' ),
					'type'        => 'date-time',
					'context'     => array( 'view', 'edit' ),
					'readonly'    => true,
				),
				'post_status'         => array(
					'description' => __( 'Rule status (post status).', 'addify_rfq' ),
					'type'        => 'string',
					'default'     => 'publish',
					'enum'        => array_merge( array_keys( get_post_statuses() ), array( 'future' ) ),
					'context'     => array( 'view', 'edit' ),
				),
				'all_products'        => array(
					'description' => __( 'Is rule applies on all products.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'products'            => array(
					'description' => __( 'Rules applies on products.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'categories'          => array(
					'description' => __( 'Rule applies on categories.', 'addify_rfq' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
				),
				'user_roles'          => array(
					'description' => __( '.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'hide_price'          => array(
					'description' => __( 'Is price of product is hidden?', 'addify_rfq' ),
					'type'        => 'bool',
					'context'     => array( 'view', 'edit' ),
				),
				'hide_price_text'     => array(
					'description' => __( 'Text to replace add price of products.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'hide_addtocart'      => array(
					'description' => __( 'Is add to cart button hidden?', 'addify_rfq' ),
					'type'        => 'bool',
					'context'     => array( 'view', 'edit' ),
				),
				'hide_addtocart_text' => array(
					'description' => __( 'Text to replace add to cart button.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'contact_7_form'      => array(
					'description' => __( 'contact 7 form ID.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
				'custom_button_link'  => array(
					'description' => __( 'Link for custom button.', 'addify_rfq' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),

			),
		);
		return $this->add_additional_fields_schema( $schema );
	}

	public function get_items( $request ) {

		$rules_ids = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'addify_rfq',
				'post_status' => 'publish',
			)
		);

		$data = array();

		foreach ( $rules_ids as $rule_id ) {

			$object = $this->get_object( $rule_id );

			if ( ! wc_rest_check_post_permissions( $this->post_type, 'read', $object->ID ) ) {
				continue;
			}

			$data[] = $this->prepare_object_for_response( $object, $request );
		}

		$response = rest_ensure_response( $data );

		return $response;
	}

	public function get_item( $request ) {

		$object = $this->get_object( (int) $request['id'] );

		if ( ! $object || 0 === $object->ID ) {
			return new WP_Error( "woocommerce_rest_{$this->post_type}_invalid_id", __( 'Invalid ID.', 'addify_rfq' ), array( 'status' => 404 ) );
		}

		$data     = $this->prepare_object_for_response( $this->get_object( $object ), $request );
		$response = rest_ensure_response( $data );

		return $response;
	}
}
