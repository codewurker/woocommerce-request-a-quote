<?php
/**
 * Addify Request a Quote Email Controller.
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'AF_R_F_Q_Main' ) ) {

			/**
			 * AF_R_F_Q_Main.
			 */
	class AF_R_F_Q_Main {

			/**
			 * Qoute Rules.
			 *
			 * @var $quote_rules.
			 */
		public $quote_rules;

			/**
			 * Rule applied.
			 *
			 * @var $rule_applied.
			 */
		public static $rule_applied;

		/**
		 * Construct.
		 */
		public function __construct() {

			add_action(
				'wp_loaded',
				function () {
					$this->quote_rules = $this->afrfq_get_quote_rules();
				}
			);

			self::$rule_applied = array();

			// Hide price for selected products.
			add_filter( 'woocommerce_get_price_html', array( $this, 'afrfq_remove_woocommerce_price_html' ), 100, 2 );

			// Process and initialize the hooks.
			add_action( 'wp_loaded', array( $this, 'afrfq_add_archive_page_hooks' ) );
			add_action( 'wp_loaded', array( $this, 'afrfq_add_product_page_hooks' ) );

			// Display add to quote after add to cart button.
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'afrfq_custom_button_add_replacement' ), 30 );

			// Load and update saved quotes of registered users.
			add_action( 'wp_login', array( $this, 'afrfq_update_quote_data_after_login' ), 100, 2 );

			// Add woocommerce product addon compatibility.
			add_filter( 'woocommerce_product_addons_show_grand_total', array( $this, 'hide_price_product_addon' ), 100, 2 );

			// Blocks Compatibility.
			add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'wc_add_date_to_gutenberg_block' ), 10, 3 );
		}


		public function wc_add_date_to_gutenberg_block( $html, $data, $product ) {

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_price      = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
				$afrfq_hide_price_text    = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );
				$afrfq_is_hide_addtocart  = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );
				$afrfq_custom_button_link = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_link', true );

				$istrue = false;

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}
			}

			return $html;
		}

		public function hide_price_product_addon( $visible, $product ) {

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_price      = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
				$afrfq_hide_price_text    = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );
				$afrfq_is_hide_addtocart  = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );
				$afrfq_custom_button_link = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_link', true );

				$istrue = false;

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				if ( 'yes' === $afrfq_is_hide_price ) {
					return false;
				}
			}

			return $visible;
		}

		public function afrfq_get_quote_rules() {

			$args = array(
				'post_type'        => 'addify_rfq',
				'post_status'      => 'publish',
				'numberposts'      => -1,
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'suppress_filters' => false,

			);
			return get_posts( $args );
		}

		public function afrfq_show_rfq_out_of_stock( $html, $product ) {

			if ( $product->is_in_stock() ) {
				return $html;
			}

			if ( 'yes' !== get_option( 'enable_o_o_s_products' ) ) {
				return $html;
			}

			if ( 'simple' !== $product->get_type() ) {
				return $html;
			}

			wc_get_template(
				'product/simple-out-of-stock.php',
				array(),
				'/woocommerce/addify/rfq/',
				AFRFQ_PLUGIN_DIR . 'templates/'
			);
		}

		public function afrfq_load_quote_from_session() {

			if ( isset( wc()->session ) && empty( wc()->session->get( 'quotes' ) ) ) {

				if ( is_user_logged_in() ) {

					$quotes = get_user_meta( get_current_user_id(), 'addify_quote', true );

					if ( ! empty( $quotes ) ) {
						wc()->session->set( 'quotes', $quotes );
					}
				}
			}
		}

		public function afrfq_update_quote_data_after_login( $user_login, $user ) {

			$saved_quotes   = (array) get_user_meta( $user->ID, 'addify_quote', true );
			$session_quotes = (array) WC()->session->get( 'quotes' );
			$final_quotes   = $session_quotes;

			// Merge saved quotes and session quotes.
			foreach ( (array) $saved_quotes as $key => $value ) {

				if ( ! isset( $final_quotes[ $key ] ) && ! empty( $value ) ) {
					$final_quotes[ $key ] = $value;
				}
			}

			// Filter quotes.
			foreach ( $final_quotes as $key => $value ) {
				if ( empty( $value ) ) {
					unset( $final_quotes[ $key ] );
				}
			}

			update_user_meta( $user->ID, 'addify_quote', $final_quotes );
			WC()->session->set( 'quotes', $final_quotes );
		}

		public function afrfq_add_product_page_hooks() {

			$sol2_array = array( get_option( 'afrfq_enable_elementor_compt' ), get_option( 'afrfq_enable_divi_compt' ), get_option( 'afrfq_enable_solution2' ) );

			if ( in_array( 'yes', $sol2_array, true ) ) {

				add_action( 'woocommerce_simple_add_to_cart', array( $this, 'afrfq_custom_product_button_elementor' ), 1, 0 );
				add_action( 'woocommerce_variable_add_to_cart', array( $this, 'afrfq_custom_product_button_elementor' ), 1, 0 );
			} else {

				add_action( 'woocommerce_single_product_summary', array( $this, 'afrfq_custom_product_button' ), 1, 0 );
			}
		}

		public function afrfq_custom_product_button_elementor() {

			global $user, $product;

			$quote_button = false;

			if ( ( ! $product->is_in_stock() ) && 'yes' !== get_option( 'enable_o_o_s_products' ) ) {

				return;
			}

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_price      = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
				$afrfq_hide_price_text    = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );
				$afrfq_is_hide_addtocart  = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );

				$istrue = false;

				if ( $quote_button && in_array( $afrfq_is_hide_addtocart, array( 'replace', 'addnewbutton' ), true ) ) {
					continue;
				}

				$afrfq_apply_on_oos_products = get_post_meta( intval( $rule->ID ), 'afrfq_apply_on_oos_products', true );

				if ( $product->is_in_stock() ) {

					if ( 'yes' == $afrfq_apply_on_oos_products ) {
						continue;
					}
				}

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				if ( 'replace' === $afrfq_is_hide_addtocart || 'replace_custom' === $afrfq_is_hide_addtocart ) {

					$quote_button = true;

					if ( 'variable' === $product->get_type() ) {
						remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
						add_action( 'woocommerce_single_variation', array( $this, 'afrfq_custom_button_replacement' ), 20 );
					} else {
						remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
						add_action( 'woocommerce_simple_add_to_cart', array( $this, 'afrfq_custom_button_replacement' ), 30 );
					}
				}
				continue;
			}
		}

		public function afrfq_add_archive_page_hooks() {

			// Replace add to cart button with custom button on shop page.
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'afrfq_replace_loop_add_to_cart_link' ), 10, 2 );

			// Replace add to cart button with custom button on shop page.
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'afrfq_custom_add_to_quote_button' ), 20, 2 );
		}

		public function afrfq_remove_woocommerce_price_html( $price, $product ) {

			if ( 'variation' === $product->get_type() ) {
				$product_id = $product->get_parent_id();
				$product    = wc_get_product( $product_id );
			}

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_price   = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
				$afrfq_hide_price_text = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );

				if ( 'yes' !== $afrfq_is_hide_price ) {
					continue;
				}

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				return $afrfq_hide_price_text;
			}

			return $price;
		}

		public function check_required_addons( $product_id ) {
			// No parent add-ons, but yes to global.
			if ( in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$addons = WC_Product_Addons_Helper::get_product_addons( $product_id );

				if ( ! empty( $addons ) ) {
					return true;
				}
			}

			return false;
		}

		public function afrfq_check_rule_for_product( $product_id, $rule_id ) {

			$afrfq_rule_type         = get_post_meta( intval( $rule_id ), 'afrfq_rule_type', true );
			$afrfq_hide_products     = (array) unserialize( get_post_meta( intval( $rule_id ), 'afrfq_hide_products', true ) );
			$afrfq_hide_categories   = (array) unserialize( get_post_meta( intval( $rule_id ), 'afrfq_hide_categories', true ) );
			$afrfq_hide_user_role    = (array) unserialize( get_post_meta( intval( $rule_id ), 'afrfq_hide_user_role', true ) );
			$applied_on_all_products = get_post_meta( $rule_id, 'afrfq_apply_on_all_products', true );

			if ( ! is_user_logged_in() ) {

				if ( ! in_array( 'guest', (array) $afrfq_hide_user_role, true ) && 'afrfq_for_guest_users' !== $afrfq_rule_type ) {

					return false;
				}
			} else {

				$curr_user      = wp_get_current_user();
				$curr_user_role = current( $curr_user->roles );

				if ( ! in_array( $curr_user_role, (array) $afrfq_hide_user_role, true ) ) {
					return false;
				}
			}

			if ( 'yes' === $applied_on_all_products ) {
				return true;
			}

			if ( in_array( $product_id, $afrfq_hide_products ) ) {
				return true;
			}

			foreach ( $afrfq_hide_categories as $cat ) {

				if ( ! empty( $cat ) && has_term( $cat, 'product_cat', $product_id ) ) {

					return true;
				}
			}

			return false;
		}

		public function afrfq_replace_loop_add_to_cart_link( $html, $product ) {

			$pageurl = get_page_link( get_option( 'addify_atq_page_id', true ) );

			$cart_txt = $html;

			if ( 'simple' !== $product->get_type() ) {
				return $html;
			}

			if ( ( ! $product->is_in_stock() ) && 'yes' !== get_option( 'enable_o_o_s_products' ) ) {

				return $html;
			}

			$quote_button = false;

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_price         = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
				$afrfq_hide_price_text       = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );
				$afrfq_is_hide_addtocart     = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text    = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );
				$afrfq_custom_button_link    = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_link', true );
				$afrfq_apply_on_oos_products = get_post_meta( intval( $rule->ID ), 'afrfq_apply_on_oos_products', true );

				$istrue = false;

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				if ( $product->is_in_stock() ) {

					if ( 'yes' == $afrfq_apply_on_oos_products ) {
						continue;
					}
				}

				if ( $quote_button && in_array( $afrfq_is_hide_addtocart, array( 'replace', 'addnewbutton' ), true ) ) {
					continue;
				}

				if ( $this->check_required_addons( $product->get_id() ) ) {
					// WooCommerce Product Add-ons compatibility
					return $html;

				} elseif ( 'replace' === $afrfq_is_hide_addtocart || 'replace_custom' === $afrfq_is_hide_addtocart ) {

						return '';
				}
			}

			return $cart_txt;
		}

		public function afrfq_custom_add_to_quote_button( $html, $product ) {

			global $user, $product;

			$pageurl = get_page_link( get_option( 'addify_atq_page_id', true ) );

			if ( ( ! $product->is_in_stock() ) && 'yes' !== get_option( 'enable_o_o_s_products' ) ) {

				return $html;
			}

			$quote_button = false;

			ob_start();

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_price         = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
				$afrfq_hide_price_text       = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );
				$afrfq_is_hide_addtocart     = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text    = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );
				$afrfq_custom_button_link    = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_link', true );
				$afrfq_apply_on_oos_products = get_post_meta( intval( $rule->ID ), 'afrfq_apply_on_oos_products', true );

				$istrue = false;

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				if ( $product->is_in_stock() ) {

					if ( 'yes' == $afrfq_apply_on_oos_products ) {
						continue;
					}
				}

				if ( $quote_button && in_array( $afrfq_is_hide_addtocart, array( 'replace', 'addnewbutton' ), true ) ) {
					continue;
				}

				if ( $this->check_required_addons( $product->get_id() ) ) {

					return apply_filters( 'addons_add_to_cart_text', __( 'Select options', 'woocommerce-product-addons' ) );
				} elseif ( ( 'replace' == $afrfq_is_hide_addtocart || 'addnewbutton' === $afrfq_is_hide_addtocart ) && 'simple' === $product->get_type() ) {

						$quote_button = true;
						echo '<a href="javascript:void(0)" rel="nofollow" data-product_id="' . intval( $product->get_ID() ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" class="afrfqbt button add_to_cart_button product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';

				} elseif ( ( 'replace_custom' == $afrfq_is_hide_addtocart || 'addnewbutton_custom' === $afrfq_is_hide_addtocart ) && 'simple' === $product->get_type() ) {

					if ( ! empty( $afrfq_custom_button_text ) ) {
						echo '<a href="' . esc_url( $afrfq_custom_button_link ) . '" rel="nofollow"  class=" button add_to_cart_button product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
					}
				}
			}

			return $html . ob_get_clean();
		}

		public function afrfq_custom_product_button() {

			global $user, $product;

			if ( ( ! $product->is_in_stock() ) && 'yes' !== get_option( 'enable_o_o_s_products' ) ) {
				return;
			}

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_addtocart  = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );

				$istrue = false;

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				$afrfq_apply_on_oos_products = get_post_meta( intval( $rule->ID ), 'afrfq_apply_on_oos_products', true );

				if ( $product->is_in_stock() ) {

					if ( 'yes' == $afrfq_apply_on_oos_products ) {
						continue;
					}
				}

				if ( 'replace' === $afrfq_is_hide_addtocart || 'replace_custom' === $afrfq_is_hide_addtocart ) {

					if ( 'variable' === $product->get_type() ) {

						remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
						add_action( 'woocommerce_single_variation', array( $this, 'afrfq_custom_button_replacement' ), 30 );
						return;
					} else {

						remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
						add_action( 'woocommerce_simple_add_to_cart', array( $this, 'afrfq_custom_button_replacement' ), 30 );
						return;
					}
				}
			}
		}

		public function afrfq_custom_button_replacement() {

			$pageurl = get_page_link( get_option( 'addify_atq_page_id', true ) );

			global $user, $product;

			if ( ! $product->is_in_stock() && 'yes' !== get_option( 'enable_o_o_s_products' ) ) {
				return;
			}

			$quote_button = false;

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_price      = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_price', true );
				$afrfq_hide_price_text    = get_post_meta( intval( $rule->ID ), 'afrfq_hide_price_text', true );
				$afrfq_is_hide_addtocart  = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );
				$afrfq_custom_button_link = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_link', true );

				$afrfq_apply_on_oos_products = get_post_meta( intval( $rule->ID ), 'afrfq_apply_on_oos_products', true );

				$istrue = false;

				if ( $product->is_in_stock() ) {

					if ( 'yes' == $afrfq_apply_on_oos_products ) {
						continue;
					}
				}

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				if ( $quote_button && in_array( $afrfq_is_hide_addtocart, array( 'replace', 'addnewbutton' ), true ) ) {
					continue;
				}

				if ( 'variable' === $product->get_type() ) {

					$disable_class = 'disabled wc-variation-selection-needed';
				} else {
					$disable_class = '';
				}

				$args = array(
					'afrfq_custom_button_text' => $afrfq_custom_button_text,
					'afrfq_custom_button_link' => $afrfq_custom_button_link,
				);

				if ( 'addnewbutton' === $afrfq_is_hide_addtocart || 'replace' === $afrfq_is_hide_addtocart ) {

					$quote_button = true;

					array_push( self::$rule_applied, $rule->ID );

					if ( 'simple' === $product->get_type() ) {

						wc_get_template(
							'product/simple.php',
							$args,
							'/woocommerce/addify/rfq/',
							AFRFQ_PLUGIN_DIR . 'templates/'
						);

					} else {

						wc_get_template(
							'product/variable.php',
							$args,
							'/woocommerce/addify/rfq/',
							AFRFQ_PLUGIN_DIR . 'templates/'
						);
					}

					return;

				} elseif ( 'addnewbutton_custom' === $afrfq_is_hide_addtocart || 'replace_custom' === $afrfq_is_hide_addtocart ) {

					array_push( self::$rule_applied, $rule->ID );

					if ( ! empty( $afrfq_custom_button_text ) ) {

						wc_get_template(
							'product/custom-button.php',
							$args,
							'/woocommerce/addify/rfq/',
							AFRFQ_PLUGIN_DIR . 'templates/'
						);
					}

					return;
				}
			}
		}

		public function afrfq_custom_button_add_replacement() {

			$pageurl = get_page_link( get_option( 'addify_atq_page_id', true ) );

			global $user, $product;

			$quote_button = false;

			if ( did_action( 'addify_after_add_to_quote_button' ) ) {
				$quote_button = true;
			}

			foreach ( $this->quote_rules as $rule ) {

				$afrfq_is_hide_addtocart  = get_post_meta( intval( $rule->ID ), 'afrfq_is_hide_addtocart', true );
				$afrfq_custom_button_text = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_text', true );
				$afrfq_custom_button_link = get_post_meta( intval( $rule->ID ), 'afrfq_custom_button_link', true );

				$istrue = false;

				if ( ! $this->afrfq_check_rule_for_product( $product->get_id(), $rule->ID ) ) {
					continue;
				}

				if ( in_array( $rule->ID, self::$rule_applied ) ) {
					continue;
				}

				$afrfq_apply_on_oos_products = get_post_meta( intval( $rule->ID ), 'afrfq_apply_on_oos_products', true );

				if ( $product->is_in_stock() ) {

					if ( 'yes' == $afrfq_apply_on_oos_products ) {
						continue;
					}
				}

				if ( $quote_button && in_array( $afrfq_is_hide_addtocart, array( 'replace', 'addnewbutton' ), true ) ) {
					continue;
				}

				if ( 'variable' === $product->get_type() ) {

					$disable_class = 'disabled wc-variation-selection-needed';
				} else {
					$disable_class = '';
				}

				if ( 'addnewbutton' === $afrfq_is_hide_addtocart || 'replace' === $afrfq_is_hide_addtocart ) {
					$quote_button = true;
					echo '<a href="javascript:void(0)" rel="nofollow" data-product_id="' . intval( $product->get_ID() ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" class="afrfqbt_single_page single_add_to_cart_button button alt  ' . esc_attr( $disable_class ) . '   product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
					array_push( self::$rule_applied, $rule->ID );

				} elseif ( 'addnewbutton_custom' === $afrfq_is_hide_addtocart || 'replace_custom' === $afrfq_is_hide_addtocart ) {

					if ( ! empty( $afrfq_custom_button_text ) ) {

						echo '<a href="' . esc_url( $afrfq_custom_button_link ) . '" rel="nofollow" class="button product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';

					}
					array_push( self::$rule_applied, $rule->ID );
				}
			}
		}
	}

	new AF_R_F_Q_Main();
}
