<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // restict for direct access.
}

if ( ! class_exists( 'AF_R_F_Q_Whole_Sale_Compatibility' ) ) {

	class AF_R_F_Q_Whole_Sale_Compatibility {

		private $allfetchedrules;

		public function __construct() {

			$this->allfetchedrules = $this->csp_load();
			add_action( 'addify_quote_session_changed', array( $this, 'af_wsp_recalculate_price' ), 20 );
		}

		public function csp_load() {

			// get Rules.
			$args = array(
				'post_type'        => 'af_wholesale_price',
				'post_status'      => 'publish',
				'orderby'          => 'menu_order',
				'order'            => 'ASC',
				'numberposts'      => -1,
				'suppress_filters' => false,
			);

			return get_posts( $args );
		}

		public function af_wsp_recalculate_price() {

			$quote_object = wc()->session->get( 'quotes' );

			$user               = wp_get_current_user();
			$role               = (array) $user->roles;
			$current_role       = current( $user->roles );
			$quantity           = 0;
			$customer_discount  = false;
			$role_discount      = false;
			$customer_discount1 = false;
			$role_discount1     = false;

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

					if ( ! empty( $this->addify_wsp_discount_price[ $current_role ] ) && 'sale' == $this->addify_wsp_discount_price[ $current_role ] && ! empty( $value['data']->get_sale_price() ) ) {

						$pro_price = $value['data']->get_sale_price();

					} elseif ( ! empty( $this->addify_wsp_discount_price[ $current_role ] ) && 'regular' == $this->addify_wsp_discount_price[ $current_role ] && ! empty( $value['data']->get_regular_price() ) ) {

						$pro_price = $value['data']->get_regular_price();

					} else {

						$pro_price = $value['data']->get_price();
					}

					// get customer specifc price.
					$cus_base_wsp_price = get_post_meta( $product_id, '_cus_base_wsp_price', true );

					// get role base price.
					$role_base_wsp_price = get_post_meta( $product_id, '_role_base_wsp_price', true );

					// Customer pricing.
					if ( ! empty( $cus_base_wsp_price ) ) {

						foreach ( $cus_base_wsp_price as $cus_price ) {

							if ( isset( $cus_price['customer_name'] ) && $user->ID == $cus_price['customer_name'] ) {

								if ( ( $value['quantity'] >= $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
									|| ( $value['quantity'] >= $cus_price['min_qty'] && '' == $cus_price['max_qty'] )
									|| ( $value['quantity'] >= $cus_price['min_qty'] && 0 == $cus_price['max_qty'] )
									|| ( '' == $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
									|| ( 0 == $cus_price['min_qty'] && $value['quantity'] <= $cus_price['max_qty'] )
								) {

									if ( 'fixed_price' == $cus_price['discount_type'] ) {

										$value['data']->set_price( $cus_price['discount_value'] );
										$value['role_base_price'] = $cus_price['discount_value'];
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$customer_discount = true;

									} elseif ( 'fixed_increase' == $cus_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
											$newprice = $newprice + $cus_price['discount_value'];
										} else {

											$newprice = $pro_price + $cus_price['discount_value'];
										}

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );

										$customer_discount = true;

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

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$customer_discount = true;

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

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$customer_discount = true;

									} elseif ( 'percentage_increase' == $cus_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
										} else {

											$percent_price = $pro_price * $cus_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$customer_discount = true;

									}
								}
							}
						}
					}

					// Role Based Pricing.
					// chcek if there is customer specific pricing then role base pricing will not work.
					if ( ! $customer_discount ) {

						if ( ! empty( $role_base_wsp_price ) ) {

							foreach ( $role_base_wsp_price as $role_price ) {

								if ( isset( $role_price['user_role'] ) && $role[0] == $role_price['user_role'] ) {

									if ( ( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
										|| ( $value['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
										|| ( $value['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] )
										|| ( '' == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
										|| ( 0 == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									) {

										if ( 'fixed_price' == $role_price['discount_type'] ) {

											$value['data']->set_price( $role_price['discount_value'] );
											$value['role_base_price'] = $role_price['discount_value'];
											$quote_object[ $key ]     = $value;
											wc()->session->set( 'quotes', $quote_object );
											$role_discount = true;

										} elseif ( 'fixed_increase' == $role_price['discount_type'] ) {

											if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

												$newprice = 0;
												$newprice = $newprice + $role_price['discount_value'];
											} else {

												$newprice = $pro_price + $role_price['discount_value'];
											}

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											wc()->session->set( 'quotes', $quote_object );
											$role_discount = true;

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

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
																wc()->session->set( 'quotes', $quote_object );
											$role_discount = true;

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

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											wc()->session->set( 'quotes', $quote_object );
											$role_discount = true;

										} elseif ( 'percentage_increase' == $role_price['discount_type'] ) {

											if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

												$newprice = 0;

											} else {

												$percent_price = $pro_price * $role_price['discount_value'] / 100;

												$newprice = $pro_price + $percent_price;
											}

											$value['data']->set_price( $newprice );
											$value['role_base_price'] = $newprice;
											$quote_object[ $key ]     = $value;
											wc()->session->set( 'quotes', $quote_object );
											$role_discount = true;

										}
									}
								}
							}
						}
					}

					// Rules.
					if ( false == $customer_discount && false == $role_discount ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {

							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'wsp_apply_on_all_products', true );
								$products                = get_post_meta( $rule->ID, 'wsp_applied_on_products', true );
								$categories              = get_post_meta( $rule->ID, 'wsp_applied_on_categories', true );

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

								if ( $istrue ) {

									// get rule customer based price.
									$rule_cus_base_wsp_price = get_post_meta( $rule->ID, 'rcus_base_wsp_price', true );

									// get rule role base price.
									$rule_role_base_wsp_price = get_post_meta( $rule->ID, 'rrole_base_wsp_price', true );

									if ( ! empty( $rule_cus_base_wsp_price ) ) {

										foreach ( $rule_cus_base_wsp_price as $rule_cus_price ) {

											if ( isset( $rule_cus_price['customer_name'] ) && $user->ID == $rule_cus_price['customer_name'] ) {

												if ( ( $value['quantity'] >= $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && '' == $rule_cus_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_cus_price['min_qty'] && 0 == $rule_cus_price['max_qty'] )
														|| ( '' == $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
														|| ( 0 == $rule_cus_price['min_qty'] && $value['quantity'] <= $rule_cus_price['max_qty'] )
													) {

													if ( 'fixed_price' == $rule_cus_price['discount_type'] ) {

														$value['data']->set_price( $rule_cus_price['discount_value'] );
														$value['role_base_price'] = $rule_cus_price['discount_value'];
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );
														$customer_discount1 = true;

													} elseif ( 'fixed_increase' == $rule_cus_price['discount_type'] ) {

														if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

															$newprice = 0;
															$newprice = $newprice + $rule_cus_price['discount_value'];

														} else {

															$newprice = $pro_price + $rule_cus_price['discount_value'];
														}

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );
														$customer_discount1 = true;

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

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
																wc()->session->set( 'quotes', $quote_object );
														$customer_discount1 = true;

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

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
																wc()->session->set( 'quotes', $quote_object );
														$customer_discount1 = true;

													} elseif ( 'percentage_increase' == $rule_cus_price['discount_type'] ) {

														if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_cus_price['discount_value'] / 100;

															$newprice = $pro_price + $percent_price;
														}

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );
														$customer_discount1 = true;
													}
												}
											}
										}
									}

									// Rule Role Based Pricing.
									// chcek if there is customer specific pricing then role base pricing will not work.
									if ( ! $customer_discount1 ) {

										if ( ! empty( $rule_role_base_wsp_price ) ) {

											foreach ( $rule_role_base_wsp_price as $rule_role_price ) {

												if ( isset( $rule_role_price['user_role'] ) && $role[0] == $rule_role_price['user_role'] ) {

													if ( ( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
															|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] )
															|| ( '' == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
															|| ( 0 == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														) {

														if ( 'fixed_price' == $rule_role_price['discount_type'] ) {

															$value['data']->set_price( $rule_role_price['discount_value'] );
															$value['role_base_price'] = $rule_role_price['discount_value'];
															$quote_object[ $key ]     = $value;
															wc()->session->set( 'quotes', $quote_object );

														} elseif ( 'fixed_increase' == $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;
																$newprice = $newprice + $rule_role_price['discount_value'];

															} else {

																$newprice = $pro_price + $rule_role_price['discount_value'];
															}

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															wc()->session->set( 'quotes', $quote_object );

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

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															wc()->session->set( 'quotes', $quote_object );

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

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															wc()->session->set( 'quotes', $quote_object );

														} elseif ( 'percentage_increase' == $rule_role_price['discount_type'] ) {

															if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

																$newprice = 0;

															} else {

																$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

																$newprice = $pro_price + $percent_price;
															}

															$value['data']->set_price( $newprice );
															$value['role_base_price'] = $newprice;
															$quote_object[ $key ]     = $value;
															wc()->session->set( 'quotes', $quote_object );

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

				foreach ( $quote_object as $key => $value ) {

					$quantity += $value['quantity'];

					if ( 0 != $value['variation_id'] ) {

						$product_id = $value['variation_id'];
						$parent_id  = $value['product_id'];

					} else {

						$product_id = $value['product_id'];
						$parent_id  = 0;

					}

					if ( ! empty( $this->addify_wsp_discount_price['guest'] ) && 'sale' == $this->addify_wsp_discount_price['guest'] && ! empty( $value['data']->get_sale_price() ) ) {

						$pro_price = $value['data']->get_sale_price();

					} elseif ( ! empty( $this->addify_wsp_discount_price['guest'] ) && 'regular' == $this->addify_wsp_discount_price['guest'] && ! empty( $value['data']->get_regular_price() ) ) {

						$pro_price = $value['data']->get_regular_price();

					} else {

						$pro_price = $value['data']->get_price();
					}

					// Role Based Pricing for guest.

					// get role base price.
					$role_base_wsp_price = get_post_meta( $product_id, '_role_base_wsp_price', true );

					if ( ! empty( $role_base_wsp_price ) ) {

						foreach ( $role_base_wsp_price as $role_price ) {

							if ( isset( $role_price['user_role'] ) && 'guest' == $role_price['user_role'] ) {

								if ( ( $value['quantity'] >= $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && '' == $role_price['max_qty'] )
									|| ( $value['quantity'] >= $role_price['min_qty'] && 0 == $role_price['max_qty'] )
									|| ( '' == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
									|| ( 0 == $role_price['min_qty'] && $value['quantity'] <= $role_price['max_qty'] )
								) {

									if ( 'fixed_price' == $role_price['discount_type'] ) {

										$value['data']->set_price( $role_price['discount_value'] );
										$value['role_base_price'] = $role_price['discount_value'];
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$role_discount = true;

									} elseif ( 'fixed_increase' == $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;
											$newprice = $newprice + $role_price['discount_value'];

										} else {

											$newprice = $pro_price + $role_price['discount_value'];
										}

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$role_discount = true;

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

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$role_discount = true;

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

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );
										$role_discount = true;

									} elseif ( 'percentage_increase' == $role_price['discount_type'] ) {

										if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

											$newprice = 0;

										} else {

											$percent_price = $pro_price * $role_price['discount_value'] / 100;

											$newprice = $pro_price + $percent_price;
										}

										$value['data']->set_price( $newprice );
										$value['role_base_price'] = $newprice;
										$quote_object[ $key ]     = $value;
										wc()->session->set( 'quotes', $quote_object );
										$role_discount = true;

									}
								}
							}
						}
					}

					// Rules - guest users.
					if ( false == $role_discount ) {

						if ( empty( $this->allfetchedrules ) ) {

							echo '';

						} else {

							$all_rules = $this->allfetchedrules;

						}

						if ( ! empty( $all_rules ) ) {

							foreach ( $all_rules as $rule ) {

								$istrue = false;

								$applied_on_all_products = get_post_meta( $rule->ID, 'wsp_apply_on_all_products', true );
								$products                = get_post_meta( $rule->ID, 'wsp_applied_on_products', true );
								$categories              = get_post_meta( $rule->ID, 'wsp_applied_on_categories', true );

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

								if ( $istrue ) {

									// get rule role base price for guest.
									$rule_role_base_wsp_price = get_post_meta( $rule->ID, 'rrole_base_wsp_price', true );

									if ( ! empty( $rule_role_base_wsp_price ) ) {

										foreach ( $rule_role_base_wsp_price as $rule_role_price ) {

											if ( isset( $rule_role_price['user_role'] ) && 'guest' == $rule_role_price['user_role'] ) {

												if ( ( $value['quantity'] >= $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_role_price['min_qty'] && '' == $rule_role_price['max_qty'] )
														|| ( $value['quantity'] >= $rule_role_price['min_qty'] && 0 == $rule_role_price['max_qty'] )
														|| ( '' == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
														|| ( 0 == $rule_role_price['min_qty'] && $value['quantity'] <= $rule_role_price['max_qty'] )
													) {

													if ( 'fixed_price' == $rule_role_price['discount_type'] ) {

														$value['data']->set_price( $rule_role_price['discount_value'] );
														$value['role_base_price'] = $rule_role_price['discount_value'];
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );

													} elseif ( 'fixed_increase' == $rule_role_price['discount_type'] ) {

														if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

															$newprice = 0;
															$newprice = $newprice + $rule_role_price['discount_value'];

														} else {

															$newprice = $pro_price + $rule_role_price['discount_value'];
														}

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );

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

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );

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

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );

													} elseif ( 'percentage_increase' == $rule_role_price['discount_type'] ) {

														if ( empty( $pro_price ) || ( ! empty( $pro_price ) && 0 == $pro_price ) ) {

															$newprice = 0;

														} else {

															$percent_price = $pro_price * $rule_role_price['discount_value'] / 100;

															$newprice = $pro_price + $percent_price;
														}

														$value['data']->set_price( $newprice );
														$value['role_base_price'] = $newprice;
														$quote_object[ $key ]     = $value;
														wc()->session->set( 'quotes', $quote_object );
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

	new AF_R_F_Q_Whole_Sale_Compatibility();
}
