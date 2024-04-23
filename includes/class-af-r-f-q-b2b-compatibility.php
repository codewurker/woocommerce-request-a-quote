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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // restict for direct access.
}

if ( ! class_exists( 'AF_R_F_Q_B2B_Compatibility' ) ) {

	/**
	 * AF_R_F_Q_B2B_Compatibility
	 */
	class AF_R_F_Q_B2B_Compatibility {

			/**
			 * Errors.
			 *
			 * @var $allfetchedrules.
			 */
		private $allfetchedrules;

		/**
		 * Construct.
		 */
		public function __construct() {

			$this->allfetchedrules = $this->csp_load();
			add_action( 'addify_quote_session_changed', array( $this, 'af_csp_recalculate_price' ), 20 );
		}

		/**
		 * AF_R_F_Q_B2B_Compatibility.
		 */
		public function csp_load() {

			// get Rules.
			$args = array(
				'post_type'        => 'csp_rules',
				'post_status'      => 'publish',
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'numberposts'      => -1,
				'suppress_filters' => false,
			);

			return get_posts( $args );
		}

		/**
		 * AF_R_F_Q_B2B_Compatibility
		 */
		public function af_csp_recalculate_price() {

			$quote_object = wc()->session->get( 'quotes' );

			$user               = wp_get_current_user();
			$role               = (array) $user->roles;
			$quantity           = 0;
			$customer_discount  = false;
			$role_discount      = false;
			$customer_discount1 = false;
			$role_discount1     = false;
			$rule_cus_price     = '';

			$price_for_discount = get_option( 'afb2b_discount_price' );
			$active_currency    = get_woocommerce_currency();
			$base_currency      = get_option( 'woocommerce_currency' );

			if ( is_user_logged_in() ) {

				foreach ( $quote_object as $key => $value ) {

					$quantity += $value['quantity'];

					if ( 0 != $value['variation_id'] ) {

						$product_id = $value['variation_id'];
						$parent_id  = $value['product_id'];

					} else {

						$product_id = $value['product_id'];
						$parent_id  = 0;

					}

					$first_role = current( $user->roles );

					if ( ! empty( $price_for_discount[ $first_role ] ) && 'sale' == $price_for_discount[ $first_role ] && ! empty( $value['data']->get_sale_price() ) ) {

						$pro_price = $value['data']->get_sale_price();

					} elseif ( ! empty( $price_for_discount[ $first_role ] ) && 'regular' === $price_for_discount[ $first_role ] && ! empty( $value['data']->get_regular_price() ) ) {

						$pro_price = $value['data']->get_regular_price();

					} else {

						$pro_price = $value['data']->get_price();
					}

					// get customer specifc price.
					$cus_base_price = get_post_meta( $product_id, '_cus_base_price', true );

					// get role base price.
					$role_base_price = get_post_meta( $product_id, '_role_base_price', true );

					// Customer pricing.
					if ( ! empty( $cus_base_price ) ) {

						foreach ( $cus_base_price as $cus_price ) {

							if ( isset( $cus_price['customer_name'] ) && $user->ID == $cus_price['customer_name'] ) {

								if ( ( $value['quantity'] >= $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
									|| ( $value['quantity'] >= $cus_price['min_qty'] && '' == $cus_price['max_qty'] )
									|| ( $value['quantity'] >= $cus_price['min_qty'] && 0 == $cus_price['max_qty'] )
									|| ( '' == $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
									|| ( 0 == $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
								) {

									if ( 'fixed_price' == $cus_price['discount_type'] ) {

										$converted_amount = apply_filters( 'wc_aelia_cs_convert', $cus_price['discount_value'], $base_currency, $active_currency );

										$value['data']->set_price( $converted_amount );
										$value['role_base_price'] = $converted_amount;
											$quote_object[ $key ] = $value;
										$customer_discount        = true;

									} elseif ( 'fixed_increase' == $cus_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
											$newprice = $newprice + $cus_price['discount_value'];
										} else {

											$newprice = $pro_price + $cus_price['discount_value'];
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
											$quote_object[ $key ] = $value;
										$customer_discount        = true;

									} elseif ( 'fixed_decrease' == $cus_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
										} else {

											$newprice = $pro_price - $cus_price['discount_value'];

											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
											$quote_object[ $key ] = $value;
										$customer_discount        = true;

									} elseif ( 'percentage_decrease' == $cus_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;

											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;
											}
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
											$quote_object[ $key ] = $value;
										$customer_discount        = true;

									} elseif ( 'percentage_increase' == $cus_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
											$quote_object[ $key ] = $value;
										$customer_discount        = true;

									} else {

										$customer_discount = false;
									}
								}
							}
						}
					} else {

						$customer_discount = false;
					}

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( ! $customer_discount ) {

						if ( ! empty( $role_base_price ) ) {

							foreach ( $role_base_price as $role_price ) {

								if ( isset( $role_price['user_role'] ) && $role[0] == $role_price['user_role'] ) {

									if ( ( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
										|| ( $value['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
										|| ( $value['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] )
										|| ( '' == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
										|| ( 0 == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									) {

										if ( 'fixed_price' == $role_price['discount_type'] ) {

											$newprice = apply_filters( 'wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency );

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											$role_discount            = true;

										} elseif ( 'fixed_increase' == $role_price['discount_type'] ) {

											if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}

											$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											$role_discount            = true;

										} elseif ( 'fixed_decrease' == $role_price['discount_type'] ) {

											if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

												$newprice = 0;

											} else {

												$newprice = $pro_price - $role_price['discount_value'];
												if ( 0 > $newprice ) {

													$newprice = 0;

												} else {

													$newprice = $newprice;

												}
											}

											$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											$role_discount            = true;

										} elseif ( 'percentage_decrease' == $role_price['discount_type'] ) {

											if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

												$newprice = 0;

											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price - $percent_price;

												if ( 0 > $newprice ) {

													$newprice = 0;

												} else {

													$newprice = $newprice;

												}
											}

											$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

											$value['data']->set_price( $newprice );

											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											$role_discount            = true;

										} elseif ( 'percentage_increase' == $role_price['discount_type'] ) {

											if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

												$newprice = 0;

											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;
											}

											$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											$role_discount            = true;

										} else {

											$role_discount = false;
										}
									}
								}
							}
						} else {

							$role_discount = false;
						}
					}

					if ( false == $customer_discount && false == $role_discount ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {

							$rule_check = false;

							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta( $rule->ID, 'csp_applied_on_products', true );
								$categories              = get_post_meta( $rule->ID, 'csp_applied_on_categories', true );
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if ( ! $rule_check ) {

									if ( 'yes' == $applied_on_all_products ) {
										$istrue = true;

									} elseif ( ! empty( $products ) && ( in_array( $product_id, $products ) || in_array( $parent_id, $products ) ) ) {
										$istrue = true;

									}

									if ( ! empty( $categories ) ) {
										foreach ( $categories as $cat ) {

											if ( ! empty( $cat ) && ( has_term( $cat, 'product_cat', $product_id ) ) || ( has_term( $cat, 'product_cat', $parent_id ) ) ) {

												$istrue = true;

											}
										}
									}

									if ( ! empty( $rbp_slected_brands ) ) {
										foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

											if ( ! empty( $rbp_brand_slect ) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id ) ) || ( has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

												$istrue = true;

											}
										}
									}

									if ( $istrue ) {

										$rule_cus_base_price = get_post_meta( $rule->ID, 'rcus_base_price', true );

										$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price', true );

										if ( ! empty( $rule_cus_base_price ) ) {

											foreach ( $rule_cus_base_price as $rule_cus_price ) {

												if ( isset( $rule_cus_price['customer_name'] ) && $user->ID == $rule_cus_price['customer_name'] ) {

													if ( ( $value['quantity'] >= $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && '' == $rule_cus_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && 0 == $rule_cus_price['max_qty'] )
														|| ( '' == $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
														|| ( 0 == $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
													) {

														$rule_check = true;

														if ( 'fixed_price' == $rule_cus_price['discount_type'] ) {

															$newprice = apply_filters( 'wc_aelia_cs_convert', $rule_cus_price['discount_value'], $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															$customer_discount1       = true;

														} elseif ( 'fixed_increase' == $rule_cus_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;
																$newprice = $newprice + $rule_cus_price['discount_value'];

															} else {

																$newprice = $pro_price + $rule_cus_price['discount_value'];
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															$customer_discount1       = true;

														} elseif ( 'fixed_decrease' == $rule_cus_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;

															} else {

																$newprice = $pro_price - $rule_cus_price['discount_value'];
																if ( 0 > $newprice ) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															$customer_discount1       = true;

														} elseif ( 'percentage_decrease' == $rule_cus_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

																$newprice = $pro_price - $percent_price;

																if ( 0 > $newprice ) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															$customer_discount1       = true;

														} elseif ( 'percentage_increase' == $rule_cus_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															$customer_discount1       = true;

														} else {

															$customer_discount1 = false;
														}
													}
												}
											}
										} else {

											$customer_discount1 = false;
										}

										if ( ! $customer_discount1 ) {

											if ( ! empty( $rule_role_base_price ) ) {

												foreach ( $rule_role_base_price as $rule_role_price ) {

													if ( isset( $rule_role_price['user_role'] ) && $role[0] == $rule_role_price['user_role'] ) {

														if ( ( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] )
															|| ( '' == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
															|| ( 0 == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														) {

															$rule_check = true;

															if ( 'fixed_price' == $rule_role_price['discount_type'] ) {

																$newprice = apply_filters( 'wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency );

																$value['data']->set_price( $newprice );
																$value['role_base_price'] = $newprice;
																$quote_object[ $key ]     = $value;

															} elseif ( 'fixed_increase' == $rule_role_price['discount_type'] ) {

																if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																	$newprice = 0;
																	$newprice = $newprice + $rule_role_price['discount_value'];

																} else {

																	$newprice = $pro_price + $rule_role_price['discount_value'];
																}

																$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );
																$value['data']->set_price( $newprice );
																$value['role_base_price'] = $newprice;
																$quote_object[ $key ]     = $value;

															} elseif ( 'fixed_decrease' == $rule_role_price['discount_type'] ) {

																if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																	$newprice = 0;

																} else {

																	$newprice = $pro_price - $rule_role_price['discount_value'];

																	if ( 0 > $newprice ) {

																		$newprice = 0;

																	} else {

																		$newprice = $newprice;

																	}
																}

																$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );
																$value['data']->set_price( $newprice );
																$value['role_base_price'] = $newprice;
																$quote_object[ $key ]     = $value;

															} elseif ( 'percentage_decrease' == $rule_role_price['discount_type'] ) {

																if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																	$newprice = 0;

																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = $pro_price - $percent_price;

																	if ( 0 > $newprice ) {

																		$newprice = 0;

																	} else {

																		$newprice = $newprice;

																	}
																}

																$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

																$value['data']->set_price( $newprice );
																$value['role_base_price'] = $newprice;
																$quote_object[ $key ]     = $value;

															} elseif ( 'percentage_increase' == $rule_role_price['discount_type'] ) {

																if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																	$newprice = 0;

																} else {

																	$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																	$newprice = $pro_price + $percent_price;
																}

																$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

																$value['data']->set_price( $newprice );
																$value['role_base_price'] = $newprice;
																$quote_object[ $key ]     = $value;

															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			} elseif ( ! is_user_logged_in() ) {

				foreach ( $quote_object->get_cart() as $key => $value ) {

					$quantity += $value['quantity'];

					if ( 0 != $value['variation_id'] ) {

						$product_id = $value['variation_id'];
						$parent_id  = $value['product_id'];

					} else {

						$product_id = $value['product_id'];
						$parent_id  = 0;

					}

					if ( ! empty( $price_for_discount['guest'] ) && 'sale' == $price_for_discount['guest'] && ! empty( $value['data']->get_sale_price() ) ) {

						$pro_price = $value['data']->get_sale_price();

					} elseif ( ! empty( $price_for_discount['guest'] ) && 'regular' === $price_for_discount['guest'] && ! empty( $value['data']->get_regular_price() ) ) {

						$pro_price = $value['data']->get_regular_price();

					} else {

						$pro_price = $value['data']->get_price();
					}

					$role_base_price = get_post_meta( $product_id, '_role_base_price', true );

					if ( ! empty( $role_base_price ) ) {

						foreach ( $role_base_price as $role_price ) {

							if ( isset( $role_price['user_role'] ) && 'guest' == $role_price['user_role'] ) {

								if ( ( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] )
									|| ( '' == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( 0 == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
								) {

									if ( 'fixed_price' == $role_price['discount_type'] ) {

										$newprice = apply_filters( 'wc_aelia_cs_convert', $role_price['discount_value'], $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										$role_discount            = true;

									} elseif ( 'fixed_increase' == $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
											$newprice = $newprice + $role_price['discount_value'];

										} else {

											$newprice = $pro_price + $role_price['discount_value'];
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										$role_discount            = true;

									} elseif ( 'fixed_decrease' == $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;

										} else {

											$newprice = $pro_price - $role_price['discount_value'];

											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										$role_discount            = true;

									} elseif ( 'percentage_decrease' == $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price - $percent_price;

											if ( 0 > $newprice ) {

												$newprice = 0;

											} else {

												$newprice = $newprice;

											}
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										$role_discount            = true;

									} elseif ( 'percentage_increase' == $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										$role_discount            = true;

									} else {

										$role_discount = false;
									}
								}
							}
						}
					} else {

						$role_discount = false;
					}

					if ( false == $role_discount ) {

						if ( empty( $this->allfetchedrules ) ) {

								echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {

							$rule_check = false;

							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'csp_apply_on_all_products', true );
								$products                = get_post_meta( $rule->ID, 'csp_applied_on_products', true );
								$categories              = get_post_meta( $rule->ID, 'csp_applied_on_categories', true );
								$rbp_slected_brands      = json_decode( get_post_meta( $rule->ID, 'rbp_multi_brands', true ) );

								if ( ! $rule_check ) {

									if ( 'yes' == $applied_on_all_products ) {
										$istrue = true;

									} elseif ( ! empty( $products ) && ( in_array( $product_id, $products ) || in_array( $parent_id, $products ) ) ) {
										$istrue = true;

									}

									if ( ! empty( $categories ) ) {
										foreach ( $categories as $cat ) {

											if ( ! empty( $cat ) && ( has_term( $cat, 'product_cat', $product_id ) ) || ( has_term( $cat, 'product_cat', $parent_id ) ) ) {

												$istrue = true;

											}
										}
									}

									if ( ! empty( $rbp_slected_brands ) ) {
										foreach ( $rbp_slected_brands as $rbp_brand_slect ) {

											if ( ! empty( $rbp_brand_slect ) && ( has_term( $rbp_brand_slect, 'product_brand', $product_id ) ) || ( has_term( $rbp_brand_slect, 'product_brand', $parent_id ) ) ) {

												$istrue = true;

											}
										}
									}

									if ( $istrue ) {

										$rule_role_base_price = get_post_meta( $rule->ID, 'rrole_base_price', true );

										if ( ! empty( $rule_role_base_price ) ) {

											foreach ( $rule_role_base_price as $rule_role_price ) {

												if ( isset( $rule_role_price['user_role'] ) && 'guest' == $rule_role_price['user_role'] ) {

													if ( ( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] )
														|| ( '' == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														|| ( 0 == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
													) {

														$rule_check = true;

														if ( 'fixed_price' == $rule_role_price['discount_type'] ) {

															$newprice = apply_filters( 'wc_aelia_cs_convert', $rule_role_price['discount_value'], $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;

														} elseif ( 'fixed_increase' == $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;
																$newprice = $newprice + $rule_role_price['discount_value'];

															} else {

																$newprice = $pro_price + $rule_role_price['discount_value'];
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;

														} elseif ( 'fixed_decrease' == $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;

															} else {

																$newprice = $pro_price - $rule_role_price['discount_value'];

																if ( 0 > $newprice ) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;

														} elseif ( 'percentage_decrease' == $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price - $percent_price;

																if ( 0 > $newprice ) {

																	$newprice = 0;

																} else {

																	$newprice = $newprice;

																}
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;

														} elseif ( 'percentage_increase' == $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

															$newprice = apply_filters( 'wc_aelia_cs_convert', $newprice, $base_currency, $active_currency );

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;

														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			wc()->session->set( 'quotes', $quote_object );
		}
	}

	new AF_R_F_Q_B2B_Compatibility();
}
