<div class="afrfq_admin_main" id="">
	<?php wp_nonce_field( 'afrfq_fields_nonce_action', 'afrfq_field_nonce' ); ?>
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Quote for User Roles', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<div class="all_cats">
			<ul>
			<?php
			global $wp_roles;
			$roles = $wp_roles->get_names();
			foreach ( $roles as $key => $value ) {
				?>
				<li class="par_cat">
					<input type="checkbox" class="parent" name="afrfq_hide_user_role[]" id="" value="<?php echo esc_attr( $key ); ?>" 
				<?php
				if ( ! empty( $afrfq_hide_user_role ) && in_array( (string) $key, $afrfq_hide_user_role, true ) ) {
					echo 'checked';
				}
				?>
					/>
				<?php
				echo esc_attr( $value );
				?>
				</li>

			<?php } ?>
				<li class="par_cat">
					<input type="checkbox" class="parent" name="afrfq_hide_user_role[]" id="" value="guest" 
					<?php
					if ( ! empty( $afrfq_hide_user_role ) && in_array( 'guest', $afrfq_hide_user_role, true ) ) {
						echo 'checked';
					}
					?>
						/>
					<?php echo esc_html__( 'Guest', 'addify_rfq' ); ?>
				</li>
			</ul>
		</div>

	</div>

</div>

<div class="afrfq_admin_main">
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Apply on All Products', 'addify_rfq' ); ?></strong></label></div>
	<div class="afrfq_admin_main_right">
		<?php
			$applied_on_all_products = get_post_meta( $post->ID, 'afrfq_apply_on_all_products', true );
		?>
		<input type="checkbox" name="afrfq_apply_on_all_products" id="afrfq_apply_on_all_products" value="yes" <?php echo checked( 'yes', $applied_on_all_products ); ?>>
		<p class="description"><?php echo esc_html__( 'Check this if you want to apply this rule on all products.', 'addify_rfq' ); ?></p>
	</div>
</div>

<div class="afrfq_admin_main">
	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Apply on out of stock products only.', 'addify_rfq' ); ?></strong></label></div>
	<div class="afrfq_admin_main_right">
		<?php
			$afrfq_apply_on_oos_products = get_post_meta( $post->ID, 'afrfq_apply_on_oos_products', true );
		?>
		<input type="checkbox" name="afrfq_apply_on_oos_products" id="afrfq_apply_on_oos_products" value="yes" <?php echo checked( 'yes', $afrfq_apply_on_oos_products ); ?>>
		<p class="description"><?php echo esc_html__( 'Check this if you want to apply this rule for "out of stock products" only.', 'addify_rfq' ); ?></p>
		<p class="description"><?php echo esc_html__( 'Select replace quote button with add to cart in order to activate it.', 'addify_rfq' ); ?></p>
	</div>
</div>

<div class="afrfq_admin_main hide_all_pro">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Quote Rule for Selected Products', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">
		<select class="select_box wc-enhanced-select afrfq_hide_products" name="afrfq_hide_products[]" id="afrfq_hide_products"  multiple='multiple'>
			<?php

			if ( ! empty( $afrfq_hide_products ) ) {

				foreach ( $afrfq_hide_products as $pro ) {

					$prod_post = get_post( $pro );

					?>

						<option value="<?php echo intval( $pro ); ?>" selected="selected"><?php echo esc_attr( $prod_post->post_title ); ?></option>

					<?php
				}
			}
			?>
		</select>
	</div>

</div>

<div class="afrfq_admin_main hide_all_pro">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Quote Rule for Selected Categories', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">


		<div class="all_cats">
			<ul>
				<?php

				$pre_vals = $afrfq_hide_categories;

				$args = array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
					'parent'     => 0,
				);

				$product_cat = get_terms( $args );
				foreach ( $product_cat as $parent_product_cat ) {
					?>
					<li class="par_cat">
						<input type="checkbox" class="parent" name="afrfq_hide_categories[]" id="afrfq_hide_categories" value="<?php echo intval( $parent_product_cat->term_id ); ?>" 
						<?php
						if ( ! empty( $pre_vals ) && in_array( (string) $parent_product_cat->term_id, $pre_vals, true ) ) {
							echo 'checked';
						}
						?>
						/>
						<?php echo esc_attr( $parent_product_cat->name ); ?>

						<?php
						$child_args         = array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => false,
							'parent'     => intval( $parent_product_cat->term_id ),
						);
						$child_product_cats = get_terms( $child_args );
						if ( ! empty( $child_product_cats ) ) {
							?>
							<ul>
								<?php foreach ( $child_product_cats as $child_product_cat ) { ?>
									<li class="child_cat">
										<input type="checkbox" class="child parent" name="afrfq_hide_categories[]" id="afrfq_hide_categories" value="<?php echo intval( $child_product_cat->term_id ); ?>" 
										<?php
										if ( ! empty( $pre_vals ) && in_array( (string) $child_product_cat->term_id, $pre_vals, true ) ) {
											echo 'checked';
										}
										?>
										/>
										<?php echo esc_attr( $child_product_cat->name ); ?>

										<?php
										// 2nd level
										$child_args2 = array(
											'taxonomy'   => 'product_cat',
											'hide_empty' => false,
											'parent'     => intval( $child_product_cat->term_id ),
										);

										$child_product_cats2 = get_terms( $child_args2 );
										if ( ! empty( $child_product_cats2 ) ) {
											?>

											<ul>
												<?php foreach ( $child_product_cats2 as $child_product_cat2 ) { ?>

													<li class="child_cat">
														<input type="checkbox" class="child parent" name="afrfq_hide_categories[]" id="afrfq_hide_categories" value="<?php echo intval( $child_product_cat2->term_id ); ?>" 
														<?php
														if ( ! empty( $pre_vals ) && in_array( (string) $child_product_cat2->term_id, $pre_vals, true ) ) {
															echo 'checked';
														}
														?>
														/>
														<?php echo esc_attr( $child_product_cat2->name ); ?>


														<?php
														// 3rd level
														$child_args3 = array(
															'taxonomy' => 'product_cat',
															'hide_empty' => false,
															'parent'   => intval( $child_product_cat2->term_id ),
														);

														$child_product_cats3 = get_terms( $child_args3 );
														if ( ! empty( $child_product_cats3 ) ) {
															?>

															<ul>
																<?php foreach ( $child_product_cats3 as $child_product_cat3 ) { ?>

																	<li class="child_cat">
																		<input type="checkbox" class="child parent" name="afrfq_hide_categories[]" id="afrfq_hide_categories" value="<?php echo intval( $child_product_cat3->term_id ); ?>" 
																		<?php
																		if ( ! empty( $pre_vals ) && in_array( (string) $child_product_cat3->term_id, $pre_vals, true ) ) {
																			echo 'checked';
																		}
																		?>
																		/>
																		<?php echo esc_attr( $child_product_cat3->name ); ?>


																		<?php
																		// 4th level
																		$child_args4 = array(
																			'taxonomy' => 'product_cat',
																			'hide_empty' => false,
																			'parent'   => intval( $child_product_cat3->term_id ),
																		);

																		$child_product_cats4 = get_terms( $child_args4 );
																		if ( ! empty( $child_product_cats4 ) ) {
																			?>

																			<ul>
																				<?php foreach ( $child_product_cats4 as $child_product_cat4 ) { ?>

																					<li class="child_cat">
																						<input type="checkbox" class="child parent" name="afrfq_hide_categories[]" id="afrfq_hide_categories" value="<?php echo intval( $child_product_cat4->term_id ); ?>"
																						<?php
																						if ( ! empty( $pre_vals ) && in_array( (string) $child_product_cat4->term_id, $pre_vals, true ) ) {
																							echo 'checked';
																						}
																						?>
																						/>
																						<?php echo esc_attr( $child_product_cat4->name ); ?>


																						<?php
																						// 5th level
																						$child_args5 = array(
																							'taxonomy' => 'product_cat',
																							'hide_empty' => false,
																							'parent'   => intval( $child_product_cat4->term_id ),
																						);

																						$child_product_cats5 = get_terms( $child_args5 );
																						if ( ! empty( $child_product_cats5 ) ) {
																							?>

																							<ul>
																								<?php foreach ( $child_product_cats5 as $child_product_cat5 ) { ?>

																									<li class="child_cat">
																										<input type="checkbox" class="child parent" name="afrfq_hide_categories[]" id="afrfq_hide_categories" value="<?php echo intval( $child_product_cat5->term_id ); ?>" 
																										<?php
																										if ( ! empty( $pre_vals ) && in_array( (string) $child_product_cat5->term_id, $pre_vals, true ) ) {
																											echo 'checked';
																										}
																										?>
																										/>
																										<?php echo esc_attr( $child_product_cat5->name ); ?>


																										<?php
																										// 6th level
																										$child_args6 = array(
																											'taxonomy' => 'product_cat',
																											'hide_empty' => false,
																											'parent'   => intval( $child_product_cat5->term_id ),
																										);

																										$child_product_cats6 = get_terms( $child_args6 );
																										if ( ! empty( $child_product_cats6 ) ) {
																											?>

																											<ul>
																												<?php foreach ( $child_product_cats6 as $child_product_cat6 ) { ?>

																													<li class="child_cat">
																														<input type="checkbox" class="child" name="afrfq_hide_categories[]" id="afrfq_hide_categories" value="<?php echo intval( $child_product_cat6->term_id ); ?>" 
																														<?php
																														if ( ! empty( $pre_vals ) && in_array( (string) $child_product_cat6->term_id, $pre_vals, true ) ) {
																															echo 'checked';
																														}
																														?>
																														/>
																														<?php echo esc_attr( $child_product_cat6->name ); ?>
																													</li>

																												<?php } ?>
																											</ul>

																										<?php } ?>

																									</li>

																								<?php } ?>
																							</ul>

																						<?php } ?>


																					</li>

																				<?php } ?>
																			</ul>

																		<?php } ?>


																	</li>

																<?php } ?>
															</ul>

														<?php } ?>

													</li>

												<?php } ?>
											</ul>

										<?php } ?>

									</li>
								<?php } ?>
							</ul>
						<?php } ?>

					</li>
					<?php
				}
				?>
			</ul>
		</div>


	</div>

</div>


<div class="afrfq_admin_main">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Hide Price', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<select name="afrfq_is_hide_price" class="select_box_small" id="afrfq_is_hide_price" onchange="afrfq_HidePrice(this.value)">
			<option value="no" <?php echo selected( 'no', esc_attr( $afrfq_is_hide_price ) ); ?>><?php echo esc_html__( 'No', 'addify_rfq' ); ?></option>
			<option value="yes" <?php echo selected( 'yes', esc_attr( $afrfq_is_hide_price ) ); ?>><?php echo esc_html__( 'Yes', 'addify_rfq' ); ?></option>
		</select>

	</div>

</div>

<div class="afrfq_admin_main" id="hpircetext">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Hide Price Text', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<?php
		if ( ! empty( $afrfq_hide_price_text ) ) {
			$afpricetext = $afrfq_hide_price_text;
		} else {
			$afpricetext = '';
		}
		?>
		<textarea cols="50" rows="5" name="afrfq_hide_price_text" id="afrfq_hide_price_text" /><?php echo esc_textarea( $afpricetext ); ?></textarea>
		<br><i><?php echo esc_html__( 'Display the above text when price is hidden, e.g "Price is hidden"', 'addify_rfq' ); ?></i>

	</div>

</div>

<div class="afrfq_admin_main">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Hide Add to Cart Button', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<select name="afrfq_is_hide_addtocart" class="select_box_small" id="afrfq_is_hide_addtocart" onchange="getCustomURL(this.value)">
			<option value="replace" <?php echo selected( 'replace', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Replace Add to Cart button with a Quote Button', 'addify_rfq' ); ?>
			</option>
			<option value="addnewbutton" <?php echo selected( 'addnewbutton', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Keep Add to Cart button and add a new Quote Button', 'addify_rfq' ); ?>
			</option>
			<option value="replace_custom" <?php echo selected( 'replace_custom', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Replace Add to Cart with custom button', 'addify_rfq' ); ?>
			</option>
			<option value="addnewbutton_custom" <?php echo selected( 'addnewbutton_custom', esc_attr( $afrfq_is_hide_addtocart ) ); ?>>
				<?php echo esc_html__( 'Keep Add to Cart and add a new custom button', 'addify_rfq' ); ?>
			</option>
		</select>

	</div>

</div>

<div class="afrfq_admin_main" id="afcustom_link">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Custom Button Link', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<?php
		if ( ! empty( $afrfq_custom_button_link ) ) {
			$afrfq_custom_button_link = $afrfq_custom_button_link;
		} else {
			$afrfq_custom_button_link = '';
		}
		?>
		<input type="text" class="afrfq_input_class" name="afrfq_custom_button_link" id="afrfq_custom_button_link" value="<?php echo esc_attr( $afrfq_custom_button_link ); ?>">
		<br><i><?php echo esc_html__( 'Link for custom button e.g "http://www.example.com"', 'addify_rfq' ); ?></i>

	</div>

</div>

<div class="afrfq_admin_main">

	<div class="afrfq_admin_main_left"><label><strong><?php echo esc_html__( 'Custom Button Label', 'addify_rfq' ); ?></strong></label></div>

	<div class="afrfq_admin_main_right">

		<?php
		if ( ! empty( $afrfq_custom_button_text ) ) {
			$afcustombuttontext = $afrfq_custom_button_text;
		} else {
			$afcustombuttontext = __( 'Add to Quote', 'addify_rfq' );
		}
		?>
		<input type="text" name="afrfq_custom_button_text" style="width:60%" id="afrfq_custom_button_text" value="<?php echo esc_html( $afcustombuttontext ); ?>" >
		<br><i><?php echo esc_html__( 'Display the above label on custom button, e.g "Request a Quote"', 'addify_rfq' ); ?></i>

	</div>

</div>
