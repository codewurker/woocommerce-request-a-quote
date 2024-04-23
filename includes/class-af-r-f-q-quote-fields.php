<?php
/**
 * Addify Quotes fields
 *
 * The Addify Quotes fields class stores quote fields data and validate it.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * AF_R_F_Q_Quote_Fields class.
 */
class AF_R_F_Q_Quote_Fields {

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_fields = array();

	/**
	 * Constructor for the Add_To_Quote class. Loads quote contents.
	 */
	public function __construct() {
		$this->quote_fields = $this->afrfq_get_fields_enabled();
	}

	/**
	 * Get all fields those are enabled.
	 *
	 * @param object $field_id returns feild id.
	 *
	 * @param object $user_id returns user id.
	 */
	public function get_field_default_value( $field_id, $user_id ) {

		$field_meta_key = get_post_meta( (int) $field_id, 'afrfq_field_value', true );

		if ( empty( $field_meta_key ) ) {
			return '';
		}

		if ( empty( intval( $user_id ) ) ) {
			return '';
		}

		$user = get_user_by( 'id', $user_id );

		if ( in_array( $field_meta_key, array( 'email', 'user_login', 'user_nicename', 'display_name' ), true ) ) {
			switch ( $field_meta_key ) {
				case 'email':
					return $user->user_email;
				case 'user_login':
					return $user->user_login;
				case 'user_nicename':
					return $user->user_nicename;
				case 'display_name':
					return $user->display_name;
				default:
					return '';
			}
		}

		$data = (string) get_user_meta( (int) $user_id, $field_meta_key, true );

		if ( 'billing_country' === $field_meta_key || 'shipping_country' === $field_meta_key ) {

			if ( isset( WC()->countries->countries[ $data ] ) ) {
				return strtolower( WC()->countries->countries[ $data ] );
			}
		}

		if ( 'billing_state' === $field_meta_key || 'shipping_state' === $field_meta_key ) {

			if ( isset( WC()->countries->states[ $data ] ) ) {
				return strtolower( WC()->countries->states[ $data ] );
			}
		}

		return $data;
	}

	/**
	 * Get all fields those are enabled.
	 */
	public function afrfq_get_fields_enabled() {

		$args = array(
			'post_type'        => 'addify_rfq_fields',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'meta_query'       => array(
				array(
					'key'     => 'afrfq_field_enable',
					'value'   => 'enable',
					'compare' => '=',
				),
			),
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
		);

		$the_query = new WP_Query( $args );

		return $the_query->get_posts();
	}

	/**
	 * Get all fields those are enabled.
	 *
	 * @param object $field_name returns feild name.
	 */
	public function afrfq_get_field_by_field_name( $field_name ) {

		$args = array(
			'post_type'        => 'addify_rfq_fields',
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'meta_query'       => array(
				array(
					'key'     => 'afrfq_field_name',
					'value'   => $field_name,
					'compare' => '=',
				),
			),
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'fields'           => 'ids',
		);

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {

			return current( $the_query->get_posts() );
		}
		return false;
	}

	/**
	 * Get all fields those are enabled.
	 *
	 * @param object $field_name returns feild name.
	 *
	 * @param object $field_id returns feild id.
	 */
	public function afrfq_validate_field_name( $field_name, $field_id ) {

		$args = array(
			'post_type'        => 'addify_rfq_fields',
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'posts_per_page'   => -1,
			'meta_query'       => array(
				array(
					'key'     => 'afrfq_field_name',
					'value'   => $field_name,
					'compare' => '=',
				),
			),
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'fields'           => 'ids',
		);

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {

			$fields_ids = $the_query->get_posts();

			if ( 1 > count( $fields_ids ) ) {
				return false;
			} elseif ( 1 === count( $fields_ids ) && current( $fields_ids ) !== $field_id ) {
				return false;
			} else {
				return true;
			}
		}
		return true;
	}

	public function afrfq_get_shipping_data( $quote_id ) {

		$fields = $this->afrfq_get_fields_enabled();

		$quote_user = get_post_meta( $quote_id, '_customer_user', true );

		if ( ! empty( intval( $quote_user ) ) ) {
			$customer = new WC_Customer( $quote_user );
		} else {
			$customer = new WC_Customer();
		}

		$shipping_address = (array) $customer->get_shipping();

		$shipping_keys = array(
			'shipping_first_name',
			'shipping_last_name',
			'shipping_company',
			'shipping_email',
			'shipping_phone',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_state',
			'shipping_postcode',
			'shipping_country',
		);

		foreach ( $fields as $key => $field ) {

			$field_id = $field->ID;

			$default          = get_post_meta( (int) $field_id, 'afrfq_field_value', true );
			$afrfq_field_name = get_post_meta( (int) $field_id, 'afrfq_field_name', true );
			$value            = get_post_meta( (int) $quote_id, $afrfq_field_name, true );
			if ( empty( $default ) ) {
				continue;
			}

			if ( ! in_array( $default, $shipping_keys, true ) ) {
				continue;
			}

			$data_key = str_replace( 'shipping_', '', $default );

			if ( 'shipping_country' === $default ) {

				foreach ( WC()->countries->countries as $code => $country ) {
					if ( strtolower( $country ) === $value ) {
						$shipping_address[ $data_key ] = $code;
						break;
					}
				}
				continue;

			} elseif ( 'shipping_state' === $default ) {

				foreach ( WC()->countries->states as $code => $country ) {
					if ( strtolower( $country ) === $value ) {
						$shipping_address[ $data_key ] = $code;
						break;
					}
				}
				continue;

			} else {

				$shipping_address[ $data_key ] = $value;
			}
		}

		return $shipping_address;
	}

	public function afrfq_get_billing_data( $quote_id ) {

		$fields = $this->afrfq_get_fields_enabled();

		$quote_user = get_post_meta( $quote_id, '_customer_user', true );

		if ( ! empty( intval( $quote_user ) ) ) {
			$customer = new WC_Customer( $quote_user );
		} else {
			$customer = new WC_Customer();
		}

		$billing_address = (array) $customer->get_billing();

		$billing_keys = array(
			'billing_first_name',
			'billing_last_name',
			'billing_company',
			'billing_email',
			'billing_phone',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_country',
		);

		foreach ( $fields as $key => $field ) {

			$field_id = $field->ID;

			$default          = get_post_meta( (int) $field_id, 'afrfq_field_value', true );
			$afrfq_field_name = get_post_meta( (int) $field_id, 'afrfq_field_name', true );
			$value            = get_post_meta( (int) $quote_id, $afrfq_field_name, true );
			if ( empty( $default ) ) {
				continue;
			}

			if ( ! in_array( $default, $billing_keys, true ) ) {
				continue;
			}

			$data_key = str_replace( 'billing_', '', $default );

			if ( 'billing_country' === $default ) {

				foreach ( WC()->countries->countries as $code => $country ) {
					if ( strtolower( $country ) === $value ) {
						$billing_address[ $data_key ] = $code;
						break;
					}
				}
				continue;

			} elseif ( 'billing_state' === $default ) {

				foreach ( WC()->countries->states as $code => $country ) {
					if ( strtolower( $country ) === $value ) {
						$billing_address[ $data_key ] = $code;
						break;
					}
				}
				continue;

			} else {

				$billing_address[ $data_key ] = $value;
			}
		}

		if ( empty( $billing_address['email'] ) || ! is_email( $billing_address['email'] ) ) {
			$billing_address['email'] = $this->afrfq_get_user_email( $quote_id );
		}

		if ( empty( $billing_address['first_name'] ) ) {
			$billing_address['first_name'] = $this->afrfq_get_user_name( $quote_id );
		}

		return $billing_address;
	}

	public function get_field_by_deafult_value( $default_value = '' ) {
		$fields = (array) $this->afrfq_get_fields_enabled();

		foreach ( $fields as $key => $field ) {
			$field_id = $field->ID;

			$field_value = get_post_meta( $field_id, 'afrfq_field_value', true );

			if ( $field_value === $default_value ) {
				return $field_id;
			}
		}
	}

	public function get_field_by_type( $type = '' ) {

		$fields = (array) $this->afrfq_get_fields_enabled();

		foreach ( $fields as $key => $field ) {
			$field_id = $field->ID;

			$field_type = get_post_meta( $field_id, 'afrfq_field_type', true );

			if ( $type === $field_type ) {
				return $field_id;
			}
		}
	}

	/**
	 * Validate Fields.
	 *
	 * @param object $quote_id returns $quote_id.
	 *
	 * @param object $billing retrns $billing.
	 */
	public function afrfq_get_user_email( $quote_id, $billing = false ) {

		$user_id = get_post_meta( $quote_id, '_customer_user', true );

		if ( empty( intval( $user_id ) ) ) {

			$billling_field = $this->get_field_by_deafult_value( 'billing_email' );

			if ( ! empty( $billling_field ) ) {

				$field_name  = get_post_meta( $billling_field, 'afrfq_field_name', true );
				$field_value = get_post_meta( $quote_id, $field_name, true );

				if ( ! empty( $field_value ) && is_email( $field_value ) ) {
					return $field_value;
				}
			}

			$email_field = $this->get_field_by_deafult_value( 'email' );

			if ( ! empty( $email_field ) ) {

				$field_name  = get_post_meta( $email_field, 'afrfq_field_name', true );
				$field_value = get_post_meta( $quote_id, $field_name, true );

				if ( ! empty( $field_value ) && is_email( $field_value ) ) {
					return $field_value;
				}
			}

			$email_field = $this->get_field_by_type( 'email' );

			if ( ! empty( $email_field ) ) {

				$field_name  = get_post_meta( $email_field, 'afrfq_field_name', true );
				$field_value = get_post_meta( $quote_id, $field_name, true );

				if ( ! empty( $field_value ) && is_email( $field_value ) ) {
					return $field_value;
				}
			}
		} else {

			$user = get_user_by( 'id', $user_id );

			if ( is_object( $user ) ) {

				if ( ! $billing ) {

					return $user->user_email;
				}

				$billling_field = $this->get_field_by_deafult_value( 'billing_email' );

				if ( ! empty( $billling_field ) ) {

					$field_name  = get_post_meta( $billling_field, 'afrfq_field_name', true );
					$field_value = get_post_meta( $quote_id, $field_name, true );

					if ( ! empty( $field_value ) && is_email( $field_value ) ) {
						return $field_value;
					}
				}

				if ( ! empty( get_user_meta( $user_id, 'billing_email', true ) ) ) {

					$billing_email = get_user_meta( $user_id, 'billing_email', true );

					if ( ! empty( $billing_email ) && is_email( $billing_email ) ) {
						return $billing_email;
					}
				}

				$email_field = $this->get_field_by_type( 'email' );

				if ( ! empty( $email_field ) ) {

					$field_name  = get_post_meta( $email_field, 'afrfq_field_name', true );
					$field_value = get_post_meta( $quote_id, $field_name, true );

					if ( ! empty( $field_value ) && is_email( $field_value ) ) {
						return $field_value;
					}
				}

				return $user->user_email;
			}
		}
	}

	/**
	 * Validate Fields.
	 *
	 * @param object $quote_id returns $quote_id.
	 */
	public function afrfq_get_user_name( $quote_id ) {

		$user_id = get_post_meta( $quote_id, '_customer_user', true );

		$name_field = $this->get_field_by_deafult_value( 'display_name' );

		if ( ! empty( $name_field ) ) {

			$field_name  = get_post_meta( $name_field, 'afrfq_field_name', true );
			$field_value = get_post_meta( $quote_id, $field_name, true );

			if ( ! empty( $field_value ) ) {
				return $field_value;
			}
		}

		$user = get_user_by( 'id', $user_id );

		if ( is_object( $user ) ) {

			return $user->display_name;
		}
	}

	public function captcha_check( $res ) {

		$secret = get_option( 'afrfq_secret_key' );

		if ( empty( $secret ) ) {
			return false;
		}

		$verify_response = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $res );

		if ( is_array( $verify_response ) && isset( $verify_response['body'] ) ) {
			$response_data = json_decode( $verify_response['body'] );
		} else {
			$response_data = json_decode( (string) $verify_response );
		}

		if ( isset( $response_data->success ) && isset( $response_data->success ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Validate Fields.
	 *
	 * @param object $data returns $data.
	 */
	public function afrfq_validate_fields_data( $data ) {

		$messages = array();

		if ( 'yes' == get_option( 'afrfq_enable_captcha' ) ) {

			if ( isset( $data['g-recaptcha-response'] ) && '' != $data['g-recaptcha-response'] ) {

				$ccheck = $this->captcha_check( $data['g-recaptcha-response'] );

				if ( '' == $ccheck ) {

					$messages[] = esc_html__( 'Invalid reCaptcha.', 'addify_b2b' );
				}
			} else {

				$messages[] = esc_html__( 'reCaptcha is required.', 'addify_b2b' );
			}
		}

		$fields = $this->afrfq_get_fields_enabled();

		if ( empty( $fields ) ) {
			return true;
		}

		foreach ( $fields as $field_post ) {

			$field = $field_post->ID;

			$required = get_post_meta( (int) $field, 'afrfq_field_required', true );
			$name     = get_post_meta( (int) $field, 'afrfq_field_name', true );
			$type     = get_post_meta( (int) $field, 'afrfq_field_type', true );
			$label    = get_post_meta( (int) $field, 'afrfq_field_label', true );

			if ( 'yes' == $required && empty( $data[ $name ] ) ) {
				/* translators: %s: label */
				$messages[] = sprintf( __( '%s is required field', 'addify_b2b' ), $label );
			}
		}

		foreach ( $data as $key => $value ) {

			$field = $this->afrfq_get_field_by_field_name( $key );

			if ( ! $field ) {
				continue;
			}

			$required = get_post_meta( (int) $field, 'afrfq_field_required', true );
			$label    = get_post_meta( (int) $field, 'afrfq_field_label', true );
			$type     = get_post_meta( (int) $field, 'afrfq_field_type', true );
			$default  = get_post_meta( (int) $field, 'afrfq_field_value', true );
			$name     = get_post_meta( (int) $field, 'afrfq_field_name', true );
			$size     = get_post_meta( (int) $field, 'afrfq_file_size', true );

			$afrfq_file_types = get_post_meta( $field, 'afrfq_file_types', true );

			if ( 'email' === $type && ! is_email( $value ) ) {
				/* translators: %s: label */
				$messages[] = sprintf( __( '%s is not a valid email address', 'addify_b2b' ), $label );
				continue;
			}

			if ( 'file' === $type && isset( $data[ $name ] ) && ! empty( $data[ $name ]['name'] ) ) {

				$types = array();
				foreach ( (array) explode( ',', $afrfq_file_types ) as $file_type ) {
					$types[] = strtolower( $file_type );
					$types[] = strtoupper( $file_type );
				}

				$file_name      = $data[ $name ]['name'];
				$file_extension = explode( '.', $file_name );
				$file_extension = (string) end( $file_extension );
				$file_extension = strtolower( $file_extension );

				if ( isset( $data[ $name ]['name'] ) && ! in_array( $file_extension, $types ) ) {
					/* translators: %s: label */
					$messages[] = sprintf( __( 'Selected file type is not allowed for %1$s. Allowed extensions of file are %2$s', 'addify_b2b' ), $label, $afrfq_file_types );
					continue;
				}

				if ( isset( $data[ $name ]['size'] ) && $data[ $name ]['size'] > $size ) {
					/* translators: %s: label */
					$messages[] = sprintf( __( '%1$s size is too large. Max size for file is %2$s KB', 'addify_b2b' ), $label, round( $size / 1000, 2 ) );
					continue;
				}
			}

			if ( 'number' === $type && ( 'billing_phone' === $default || 'shipping_phone' === $default ) ) {

				if ( ! preg_match( '/^[0-9-+\s()]*$/', $value ) ) {
					/* translators: %s: label */
					$messages[] = sprintf( __( '%s is not a valid phone number.', 'addify_b2b' ), $label );
					continue;
				}
			}
		}

		if ( ! empty( $messages ) ) {
			return $messages;
		}

		return true;
	}

	/**
	 * Migrate previous version fields to newer dynamic rule based fields.
	 */
	public function afrfq_migrate_fields_enabled_to_rules() {

		if ( ! empty( get_option( 'afrfq_fields' ) ) && 'migrated' !== get_option( 'afrfq_fields' ) ) {

			$af_fields = (array) unserialize( get_option( 'afrfq_fields' ) );

			try {
				foreach ( $af_fields as $key => $field ) {

					if ( ! is_array( $field ) ) {
						continue;
					}

					$enable_field = isset( $field['enable_field'] ) ? $field['enable_field'] : '';

					if ( 'yes' !== $enable_field ) {
						continue;
					}

					$field_required     = isset( $field['field_required'] ) ? $field['field_required'] : '';
					$field_label        = isset( $field['field_label'] ) ? $field['field_label'] : '';
					$field_sort_order   = isset( $field['field_sort_order'] ) ? $field['field_sort_order'] : '';
					$field_key          = isset( $field['field_key'] ) ? $field['field_key'] : '';
					$file_allowed_types = isset( $field['file_allowed_types'] ) ? $field['file_allowed_types'] : '';

					$field_id = wp_insert_post(
						array(
							'post_title'  => $field_label,
							'post_type'   => 'addify_rfq_fields',
							'menu_order'  => $field_sort_order,
							'post_status' => 'publish',
						)
					);

					$type    = 'text';
					$default = '';

					switch ( $field_key ) {
						case 'afrfq_message_field':
							$type = 'textarea';
							break;
						case 'afrfq_email_field':
							$default = 'email';
							$type    = 'email';
							break;
						case 'afrfq_name_field':
							$default = 'display_name';
							$type    = 'text';
							break;
						case 'afrfq_file_field':
							$type = 'file';
							break;
						default:
							$type = 'text';
							break;
					}

					update_post_meta( $field_id, 'afrfq_field_value', $default );
					update_post_meta( $field_id, 'afrfq_field_name', $field_key );
					update_post_meta( $field_id, 'afrfq_field_type', $type );
					update_post_meta( $field_id, 'afrfq_field_label', $field_label );
					update_post_meta( $field_id, 'afrfq_field_placeholder', $field_label );
					update_post_meta( $field_id, 'afrfq_field_options', array() );
					update_post_meta( $field_id, 'afrfq_field_enable', 'enable' );
					update_post_meta( $field_id, 'afrfq_field_required', $field_required );
					update_post_meta( $field_id, 'afrfq_file_size', '10000000' );
					update_post_meta( $field_id, 'afrfq_file_types', strtolower( trim( preg_replace( '/[\t\n\r\s]+/', ' ', $file_allowed_types ) ) ) );
				}

				update_option( 'afrfq_fields', 'migrated' );

			} catch ( Exception $ex ) {
				echo esc_html( $ex->getMessage() );
			}
		}

		$this->quote_fields = $this->afrfq_get_fields_enabled();
	}
}
