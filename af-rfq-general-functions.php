<?php
require AFRFQ_PLUGIN_DIR . 'vendor/autoload.php';
require_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-quote-fields.php';
use Dompdf\Dompdf;
use Dompdf\Options;

function af_rfq_gernate_pdf_of_order(
	$af_rfq_is_email_send_to_admin,
	$af_rfq_is_email_send_to_user,
	$is_ajax_applied,
	$afrfq_is_multiple_quotes_downloaded,
	$admin_add_new_qoute,
	$customer_add_new_qoute,
	$is_qoute_status_update,
	$qoute_id_arr = array()
) {
		$quote_fields_obj         = new AF_R_F_Q_Quote_Fields();
		$quote_fields             = (array) $quote_fields_obj->quote_fields;
		$afrfq_admin_defaut_email = get_option( 'admin_email' );
		
		
		
	if (!empty(get_option( 'afrfq_backrgound_color' ))) {
		$afrfq_backrgound_color = get_option( 'afrfq_backrgound_color' );
	} else {
		$afrfq_backrgound_color ='#244F70';
	}
	if (!empty(get_option( 'afrfq_text_color_for_background' ))) {

		$afrfq_text_color_for_background = get_option( 'afrfq_text_color_for_background' );
		
	} else {

		$afrfq_text_color_for_background ='#060809';
			
	}
	$af_rfq_privicy_and_term_conditions =get_option( 'afrfq_term_and_condition_text' );
		
	if (!empty($af_rfq_privicy_and_term_conditions)) {
		$af_rfq_privicy_and_term_conditions =get_option( 'afrfq_term_and_condition_text' );    
	} else {
		$af_rfq_privicy_and_term_conditions ='Privacy & Terms and Conditions';
	}

		$size                       = 'woocommerce_thumbnail';
		$afrfq_default_image        = wc_placeholder_img_src( $size );
		$af_rfq_pdf_layout_selected = get_option( 'afrfq_pdf_select_layout' );
		$af_rfq_get_site_title      = get_bloginfo('name');
		$af_rfq_get_site_address_1  = get_option('woocommerce_store_address');
		$af_rfq_get_site_address_2  = get_option('woocommerce_store_address_2');


	switch ( $af_rfq_pdf_layout_selected ) {

		case 'afrfq_template3':
			$options = new Options();
			$options->set( 'isRemoteEnabled', true );
			$dompdf = new Dompdf( $options );

			$email_values = (array) get_option( 'afrfq_emails' );
			$user_id      = get_post_meta( current( $qoute_id_arr ), '_customer_user', true );
			$user_name    = 'Guest';
			$id_user      = 'guest';
			$user         = get_user_by( 'id', intval( $user_id ) );
			if ( ! empty( $user_id ) && is_object( $user ) ) {

				$user_name         = $user->display_name;
				$af_rfq_user_email = $user->user_email;

			}

			ob_start();
			?>
				<!DOCTYPE html>
				<html>
				<head>
					<meta charset="UTF-8">
				</head>
				<body>
				
				<div id="addify_quote_items_container" style="font-family: sans-serif!important;">
				
			<?php

			foreach ( $qoute_id_arr as $qoute_id ) {

				$quote_contents = get_post_meta( $qoute_id, 'quote_contents', true );

				$afrfq_get_qoute_by_id = get_post( $qoute_id );

				if ( ! isset( $afrfq_quote_details ) ) {
					$afrfq_quote_details = new AF_R_F_Q_Quote();
				}

				if ( ! empty( get_option( 'afrfq_admin_email' ) ) ) {
					$admin_email = get_option( 'afrfq_admin_email' );
				} else {
					$admin_email = $afrfq_admin_defaut_email;
				}
				$afrfq_admin_email_pdf = get_option( 'afrfq_admin_email_pdf' );

				$quote_status       = get_post_meta( $qoute_id, 'quote_status', true );
				$afrfq_quote_totals = $afrfq_quote_details->get_calculated_totals( $quote_contents, $qoute_id );

				$afrfq_company_name             = '';
				$afrfq_company_name             = get_option( 'afrfq_company_name_text' );
				$afrfq_user_email_pdf           = get_option( 'afrfq_user_email_pdf' );
				$afrfq_get_qoute_status_for_pdf = '';

				if ( 'af_pending' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Pending';
				} elseif ( 'af_in_process' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'In Progress';
				} elseif ( 'af_accepted' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Accepted';
				} elseif ( 'af_converted' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Converted to Order';
				} elseif ( 'af_declined' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Declined';
				} elseif ( 'af_cancelled' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Cancelled';
				}

				?>
					<div class="afrfq_info_div" style="display:flex;">
					   
					<div class="af_rfq_company_information " style="display:inline-block; width:68%; vertical-align:top;">
						<div class="afrfq_company_logo_preview">
						<?php if ( get_option( 'afrfq_company_logo' ) ) : ?>
								<img src="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" alt="Company Logo" style="width: 60px; margin-right: 10px; display: inline-block; vertical-align:middle;" />
							<?php endif; ?>
						</div>
						<div class="afrfq_company_info_sub_details" style="font-size:13px;width:73%; display:inline-block; vertical-align:top;">

						<?php if (!empty(get_option( 'afrfq_company_address' ))) { ?>
							<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( get_option( 'afrfq_company_address' ) ); ?></p>
						<?php } elseif (!empty($af_rfq_get_site_address_1)) { ?>
							<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_1 ); ?></p>
						<?php } elseif (( !empty($af_rfq_get_site_address_2) )&&( empty($af_rfq_get_site_address_1) )) { ?>
							<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_2 ); ?></p>
						<?php } ?>
						<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Email:', 'addify_rfq' ); ?></strong>
						<?php
							$af_rfq_email_array_pdf = explode( ',', $admin_email );
							$iteration              = 1; // Initialize an iteration counter.

						foreach ( $af_rfq_email_array_pdf as $email ) {
							if ( $iteration > 1 ) {
															// Apply margin-left to the email address starting from the second iteration.
															echo '<span style="margin-top:31px; margin-left: 54px;">' . esc_attr( $email ) . '</span><br>';
							} else {
															echo esc_attr( $email ) . '<br>'; // No margin-left for the first iteration.
							}

														++$iteration; // Increment the iteration counter.
						}
						?>
															</p>
						</div>
					</div>
			
					<div class="afrfq-quote-detail" style="display:inline-block;  vertical-align:top; width:30%;">
					<div style="background:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;padding: 5px;">
					<h1 style="font-size:16px; margin-bottom:5px; font-size:20px; line-height:10px; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>;"><?php echo esc_html( 'Quote', 'addify_rfq' ); ?></h1>
					</div>
					
							<p style="font-size:13px; "><strong style="margin-right: 12px;"><?php echo esc_html( 'Quote ID:', 'addify_rfq' ); ?></strong> <span style="display:inline-block; width:64%; text-align: right; "><?php echo esc_attr( $qoute_id ); ?></span></p>
							<p style="font-size:13px;"><strong style="margin-right: 12px;"><?php echo esc_html( 'Quote Status:', 'addify_rfq' ); ?></strong> <span style="display:inline-block; width:50%; text-align: right; "> <?php echo esc_attr( $afrfq_get_qoute_status_for_pdf ); ?></span></p>
							<p style="font-size:13px;"><strong style="margin-right: 12px;"><?php echo esc_html( 'Quote Date:', 'addify_rfq' ); ?></strong> <span style="display:inline-block; width:57%; text-align: right; "> <?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $afrfq_get_qoute_by_id->post_date ) ) ); ?></span></p>
					</div>
					<div class="afrfq_client_info_details" style=" vertical-align:top;width:50% ">
					<h1 class="cust_info_text" style="margin-bottom:20px;padding:7px 10px;  background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px;  font-weight: 700;" ><?php echo esc_html( 'Customer Information', 'addify_rfq' ); ?></h1>
					<?php
									$afrfq_increment_j = 0;
					foreach ( $quote_fields as $key => $field ) {
						$field_id          = $field->ID;
						$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
						$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );
						$afrfq_field_value = get_post_meta( $field_id, 'afrfq_field_value', true );
						$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
						$field_data        = get_post_meta( $qoute_id, $afrfq_field_name, true );

						if ( empty( $field_data ) && ! empty( $afrfq_field_value ) ) {
							$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
						}

						if ( ( ! empty( $field_data ) ) && ( 'terms_cond' != $afrfq_field_type ) ) {
							++$afrfq_increment_j;

							if ( is_array( $field_data ) ) {

								$field_data = implode( ',', $field_data );
							}

							if ( empty( $afrfq_field_label ) ) {
								if ( 'user_login' == $afrfq_field_value ) {

									$afrfq_field_value = 'Username';
									?>
											<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																					
												<?php

								} else {

									$afrfq_field_value = ucwords( str_replace( '_', ' ', $afrfq_field_value ) );
									?>
											<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																					
												<?php

								}
							} else {

								?>
									<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_label . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																			
												<?php

							}
						}
					}
					if ( 0 == $afrfq_increment_j ) {

						?>
										<style>
											.cust_info_text{
												display:none;
											}
										</style>
						<?php
					}

					?>
					
					
					</div>
				   
					</div>
					<table cellpadding="0" cellspacing="0" style="margin-top:10px; border-collapse: collapse; width:100%; border:1px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
						<thead style="background:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
				
						<tr style="border-bottom:1px solid #d3d3d3a3;">
						<th style="padding:10px 20px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width: 20%;" class="thumb sortable" data-sort="string-ins"><?php esc_html_e( 'Image', 'addify_rfq' ); ?></th>
								<th style="padding:10px 20px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; text-align: left; width:35%;" class="item sortable" data-sort="string-ins"><?php esc_html_e( 'Item', 'addify_rfq' ); ?></th>
								<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
										<th style="padding:10px 20px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width: 20%;" class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Item Price', 'addify_rfq' ); ?></th>
									<?php } ?>
								<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<th style="padding:10px 20px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered price', 'addify_rfq' ); ?></th>
									<?php } ?>
									<th style="padding:10px 20px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Quantity', 'addify_rfq' ); ?></th>
								<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
										<th style="padding:10px 20px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:15%;" class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Subtotal', 'addify_rfq' ); ?></th>
									<?php } ?>
								<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<th style="padding:10px 20px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered Subtotal', 'addify_rfq' ); ?></th>
									<?php } ?>
								</tr>
							<tr>                              
						</tr>
						</thead>
						<tbody>
						<?php

						$offered_price_subtotal = 0;

						foreach ( (array) $quote_contents as $item_id => $item ) {
							if ( isset( $item['data'] ) ) {
								$product = $item['data'];
							} else {
								continue;
							}

							if ( ! is_object( $product ) ) {
								continue;
							}

							$price                   = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
							$price                   = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
							$qty_display             = $item['quantity'];
							$offered_price           = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
							$product_link            = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
							$thumbnail               = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							$offered_price_subtotal += floatval( $offered_price ) * intval( $qty_display );
							$image                   = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
							$image_url               = isset( $image[0] ) ? $image[0] : '';
							?>
								<tr class="item" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
									<td class="thumb" style="padding: 15px 20x; text-align:center; width: 20%;">
									<?php if ( ! empty( $image_url ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $image_url ); ?>" alt="Product Image"/>
											<?php } elseif ( ( empty( $image_url ) ) && ( extension_loaded( 'gd' ) ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $afrfq_default_image ); ?>" alt="Product Image"/>				
											<?php } ?>
									</td>
									<td class="woocommerce-table__product-name product-name" style=" padding:15px 20px!important; text-align:left; font-size: 13px; color:#000; width:35%;overflow-wrap: anywhere; word-break: break-word; ">
									<?php
									$product_permalink = get_edit_post_link( $product->get_id() );

									echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="color:#000; text-decoration:none;">%s</a>', $product_permalink, $product->get_name() ) : $product->get_name(), $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped									


									?>
										<br>
									<?php
										echo wp_kses_post( '<div class="wc-quote-item-sku" style="font-size:12x; margin-top:10px;"><strong style="font-size:12px;">' . esc_html__( 'SKU:', 'addify_rfq' ) . '</strong> ' . esc_attr( $product->get_sku() ) . '</div>' );
									?>
									</td>
									<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
									<td class="woocommerce-table__product-total product-total" style="padding: 15px 20x; text-align:center; color:#000; width: 20%; font-size: 13px;">
										<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $price, wc_get_price_decimals() ) ); ?>
									</td>
									<?php } ?>
									<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td style="padding: 15px 20x; text-align:center; color:#000; font-size: 13px; width: 15%;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
										<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $offered_price, wc_get_price_decimals() ) ); ?>
									</td>
									<?php } ?>                
									<td style="padding: 15px 20x; text-align:center; font-size: 13px; color:#000; width: 10%;">
										<?php echo esc_attr( $item['quantity'] ); ?>
									</td>
									<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
									<td class="woocommerce-table__product-total product-total" style="padding: 15px 20x; text-align:center; color:#000; font-size: 13px; width: 15%;">
										<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $price * $qty_display, wc_get_price_decimals() ) ); ?>
									</td>
									<?php } ?>
									<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td style="padding: 15px 20x; text-align:center; color:#000; font-size: 13px; width: 15%;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
										<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $offered_price * $qty_display, wc_get_price_decimals() ) ); ?>
								</td>
								<?php } ?>
									
								</tr>
								<?php
								if ( in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

									if (( isset($item['addons']) )&&( !empty($item['addons']) )) {
										// echo '<br>';
										foreach ($item['addons'] as $addon) {
											?>
							<tr>
							<td style="text-align:left; padding: 15px 20px; width: 20%;"></td>
							<td style="font-family: sans-serif; padding:4px 20px!important; text-align:left; font-family:sans-serif; color:#000; width:35%; overflow-wrap: anywhere; word-break: break-word;">
												<?php
												if (0!=$addon['price']) {
							
													$character_limit = 50;
							
													if (strlen($addon['name']) > $character_limit) {
														$trimmed_text = substr($addon['name'], 0, $character_limit) . '...';
													} else {
														$trimmed_text = $addon['name'];
													}
							
													echo '<b style="font-family: sans-serif;font-size: 13px; text-align: left; width:35%; overflow-wrap: anywhere; word-break: break-word;">' . esc_attr("{$trimmed_text} (" . get_woocommerce_currency_symbol() . " {$addon['price']}):" . PHP_EOL) . '</b>';
												} else {
							
							
													$character_limit = 50;
							
													if (strlen($addon['name']) > $character_limit) {
														$trimmed_text = substr($addon['name'], 0, $character_limit) . '...';
													} else {
														$trimmed_text = $addon['name'];
													}
												
													echo '<b style="font-family: sans-serif;font-size: 13px; text-align: left; width:35%; overflow-wrap: anywhere; word-break: break-word;">' . esc_attr($trimmed_text) . '</b>';
												}
							
												echo '<br>';
							
												if (( isset($addon['value'])&&( !empty($addon['value']) ) )) {
							
										
										
													$character_limit = 50;
							
													if (strlen($addon['value']) > $character_limit) {
														$trimmed_text = substr($addon['value'], 0, $character_limit) . '...';
													} else {
														$trimmed_text = $addon['value'];
													}
												
							
													echo '<p style="font-family: sans-serif;font-size: 13px;">' . esc_attr($trimmed_text) . '</p>';
										
										
												}  
												?>
							</td>
											<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
							<td></td>
							<?php } ?>
																<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
							<td></td>
										<?php } ?>
							<td></td>
																<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
							<td></td>
										<?php } ?>
																<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
							<td></td>
										<?php } ?>
							</tr>
																<?php
							
										}
									}                               
							
								}

								
								?>
								<?php } ?>
							</tbody>
							</table>
					<div>
					<?php if ( 'yes' == get_option( 'afrfq_enable_term_and_condition' ) ) { ?>
					<div class="afrfq_client_info_details" style="width:64%;display:inline-block;margin-top:15px; vertical-align:top;">
					<h1 style="margin-bottom:20px;padding:7px 10px;  background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px;  font-weight: 700;" ><?php echo esc_html( 'Terms & Conditions', 'addify_rfq' ); ?></h1>
					<ul  style="padding-left:13px; font-size:13px;  line-height: 10px;" ><li><?php echo esc_attr( $af_rfq_privicy_and_term_conditions ); ?></li></ul>
					</div>
		
					<div style="margin-left:20px; margin-right:0px; width:32%;display:inline-block; vertical-align:top;margin-top:8px; float:right;">
					<?php } else { ?>
					<div style="margin-left:470px; margin-right:0px; width:32%;display:inline-block; vertical-align:top;margin-top:8px;">
					<?php } ?>
			<table style="">
			<tbody>
		
					<?php

					if ( isset( $afrfq_quote_totals['_subtotal'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
						?>
			<tr style="border-top:1px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>; margin-top:15px; font-family: sans-serif;">
			<td style="padding-top:10px; padding-bottom:10px; padding-left:7px; padding-right:20px;  color:#000; font-size: 14px; text-align:left; font-weight:bold; font-family:sans-serif;" ><?php echo esc_html( 'Subtotal', 'addify_rfq' ); ?></td>
			<td style="padding-top:10px; padding-bottom:10px;padding-left:10px; padding-right:20px; color:#000; font-size: 14px; text-align:left;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_subtotal'], wc_get_price_decimals() ) ); ?></td> 
			</tr>
						<?php
					}

					if ( isset( $afrfq_quote_totals['_offered_total'] ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) {
						?>
			<tr>
			<td style="padding-top:10px; padding-bottom:10px; padding-left:7px; padding-right:20px; color:#000; font-size: 14px; text-align:left; font-weight:bold; font-family:sans-serif;"><?php echo esc_html( 'Offered Total', 'addify_rfq' ); ?></td>
			<td style="padding-top:10px; padding-bottom:10px;padding-left:10px; padding-right:20px;  color:#000; font-size: 14px; text-align:left;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_offered_total'], wc_get_price_decimals() ) ); ?></td> 
			</tr>
		
						<?php
					}

					if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_tax_total'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
						?>
			<tr>
			<td style="padding-top:10px; padding-bottom:10px;padding-left:7px; padding-right:20px;  color:#000; font-size: 14px; text-align:left; font-weight:bold; font-family:sans-serif;" ><?php echo esc_html( 'Tax Total', 'addify_rfq' ); ?></td>
			<td style="padding-top:10px; padding-bottom:10px;padding-left:10px; padding-right:20px; color:#000; font-size: 14px; text-align:left;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_tax_total'], wc_get_price_decimals() ) ); ?></td> 
			</tr>
						<?php
					}


					if ( isset( $afrfq_quote_totals['_total'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
						?>
			<tr style="background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
			<td style="padding-top:10px; padding-bottom:10px; padding-left:7px; padding-right:20px; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; text-align:left;  font-weight:bold; font-family:sans-serif;"><?php echo esc_html( 'Quote Total', 'addify_rfq' ); ?></td>
			<td style="padding-top:10px;   padding-bottom:10px;padding-left:10px; padding-right:20px; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px;  margin:0;"><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_total'], wc_get_price_decimals() ) ); ?></td> 
			</tr>
						<?php
					}

					?>
					</tbody>
				</table>
				</div>
				</div>
			
				<?php } ?>
				</div>
				</body>
				</html>
				<?php

				$html = ob_get_clean();
				//  echo wp_kses_post( $html );
				// exit;
				$dompdf->loadHtml( $html );
				$dompdf->setPaper( 'A4', 'portrait' );
				$dompdf->render();
				$pdf_content   = $dompdf->output();
				$pdf_file_name = 'Quote_' . current( $qoute_id_arr ) . '.pdf';

				$file_to_save = AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf';
				file_put_contents( $file_to_save, $pdf_content );

				if ( ( true == $afrfq_is_multiple_quotes_downloaded )
				&& ( ( 'yes' == $afrfq_admin_email_pdf ) ) ) {

					$to          = $admin_email;
					$subject     = 'Your PDF';
					$message     = 'Here is the PDF you requested.';
					$headers     = array(
						'Content-Type: text/html; charset=UTF-8',
					);
					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
					wp_mail( $to, $subject, $message, $headers, $attachments );

				} elseif ( ( true == $is_ajax_applied ) && ( true == $is_qoute_status_update ) ) {

					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );

					return $attachments;

				} elseif ( ( true == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) {

					if ( isset( $email_values['af_admin']['enable'] ) && ( 'yes' === $email_values['af_admin']['enable'] ) && ( ! empty( $email_values['af_admin']['subject'] ) ) ) {

						$subject            = $email_values['af_admin']['subject'] ? $email_values['af_admin']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin']['heading'] ? $email_values['af_admin']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin']['message'] ? $email_values['af_admin']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_pending' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_pending']['enable'] ) && ( 'yes' === $email_values['af_pending']['enable'] ) && ( ! empty( $email_values['af_pending']['subject'] ) ) ) {

						$subject            = $email_values['af_pending']['subject'] ? $email_values['af_pending']['subject'] : 'Your PDF';
						$heading            = $email_values['af_pending']['heading'] ? $email_values['af_pending']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_pending']['message'] ? $email_values['af_pending']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_in_process' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_in_process']['enable'] ) && ( 'yes' === $email_values['af_in_process']['enable'] ) && ( ! empty( $email_values['af_in_process']['subject'] ) ) ) {

						$subject            = $email_values['af_in_process']['subject'] ? $email_values['af_in_process']['subject'] : 'Your PDF';
						$heading            = $email_values['af_in_process']['heading'] ? $email_values['af_in_process']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_in_process']['message'] ? $email_values['af_in_process']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_accepted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_accepted']['enable'] ) && ( 'yes' === $email_values['af_accepted']['enable'] ) && ( ! empty( $email_values['af_accepted']['subject'] ) ) ) {

						$subject            = $email_values['af_accepted']['subject'] ? $email_values['af_accepted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_accepted']['heading'] ? $email_values['af_accepted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_accepted']['message'] ? $email_values['af_accepted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_converted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_converted']['enable'] ) && ( 'yes' === $email_values['af_converted']['enable'] ) && ( ! empty( $email_values['af_converted']['subject'] ) ) ) {

						$subject            = $email_values['af_converted']['subject'] ? $email_values['af_converted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_converted']['heading'] ? $email_values['af_converted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_converted']['message'] ? $email_values['af_converted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}

					if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

						$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_declined' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_declined']['enable'] ) && ( 'yes' === $email_values['af_declined']['enable'] ) && ( ! empty( $email_values['af_declined']['subject'] ) ) ) {

						$subject            = $email_values['af_declined']['subject'] ? $email_values['af_declined']['subject'] : 'Your PDF';
						$heading            = $email_values['af_declined']['heading'] ? $email_values['af_declined']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_declined']['message'] ? $email_values['af_declined']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_cancelled' === $quote_status ) && ( false == $admin_add_new_qoute ) ) {

					if ( isset( $email_values['af_cancelled']['enable'] ) && ( 'yes' === $email_values['af_cancelled']['enable'] ) && ( ! empty( $email_values['af_cancelled']['subject'] ) ) ) {
						$subject            = $email_values['af_cancelled']['subject'] ? $email_values['af_cancelled']['subject'] : 'Your PDF';
						$heading            = $email_values['af_cancelled']['heading'] ? $email_values['af_cancelled']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_cancelled']['message'] ? $email_values['af_cancelled']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				}

				if ( true == $is_ajax_applied ) {

					$file_to_save_via_url = AFRFQ_URL . 'includes/pdf/pdf-files/Quotes.pdf';
					return $file_to_save_via_url;

				} else {

					$dompdf->stream();
					exit( 0 );
				}

			break;

		case 'afrfq_template2':
			$options = new Options();
			$options->set( 'isRemoteEnabled', true );
			$dompdf = new Dompdf( $options );

			$email_values = (array) get_option( 'afrfq_emails' );
			$user_id      = get_post_meta( current( $qoute_id_arr ), '_customer_user', true );
			$user_name    = 'Guest';
			$id_user      = 'guest';
			$user         = get_user_by( 'id', intval( $user_id ) );
			if ( ! empty( $user_id ) && is_object( $user ) ) {

				$user_name         = $user->display_name;
				$af_rfq_user_email = $user->user_email;

			}

			ob_start();
			?>
					<!DOCTYPE html>
					<html>
					<head>
						<meta charset="UTF-8">
					</head>
					<body>
					
					<div id="addify_quote_items_container" style="font-family: sans-serif!important;">
					
				<?php

				foreach ( $qoute_id_arr as $qoute_id ) {

					$quote_contents = get_post_meta( $qoute_id, 'quote_contents', true );

					$afrfq_get_qoute_by_id = get_post( $qoute_id );

					if ( ! isset( $afrfq_quote_details ) ) {
						$afrfq_quote_details = new AF_R_F_Q_Quote();
					}

					if ( ! empty( get_option( 'afrfq_admin_email' ) ) ) {
						$admin_email = get_option( 'afrfq_admin_email' );
					} else {
						$admin_email = $afrfq_admin_defaut_email;
					}
					$afrfq_admin_email_pdf = get_option( 'afrfq_admin_email_pdf' );

					$quote_status       = get_post_meta( $qoute_id, 'quote_status', true );
					$afrfq_quote_totals = $afrfq_quote_details->get_calculated_totals( $quote_contents, $qoute_id );

					$afrfq_company_name             = '';
					$afrfq_company_name             = get_option( 'afrfq_company_name_text' );
					$afrfq_user_email_pdf           = get_option( 'afrfq_user_email_pdf' );
					$afrfq_get_qoute_status_for_pdf = '';

					if ( 'af_pending' === $quote_status ) {
						$afrfq_get_qoute_status_for_pdf = 'Pending';
					} elseif ( 'af_in_process' === $quote_status ) {
						$afrfq_get_qoute_status_for_pdf = 'In Progress';
					} elseif ( 'af_accepted' === $quote_status ) {
						$afrfq_get_qoute_status_for_pdf = 'Accepted';
					} elseif ( 'af_converted' === $quote_status ) {
						$afrfq_get_qoute_status_for_pdf = 'Converted to Order';
					} elseif ( 'af_declined' === $quote_status ) {
						$afrfq_get_qoute_status_for_pdf = 'Declined';
					} elseif ( 'af_cancelled' === $quote_status ) {
						$afrfq_get_qoute_status_for_pdf = 'Cancelled';
					}

					?>
						<div class="afrfq_info_div" style="display:flex;">
						   
						<div class="af_rfq_company_information " style="display:block; vertical-align:top;">
							<div class="afrfq_company_logo_preview">
							<?php if ( get_option( 'afrfq_company_logo' ) ) : ?>
									<img src="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" alt="Company Logo" style="width: 60px; margin-right: 10px; display: inline-block; vertical-align:middle;" />
								<?php endif; ?>
							</div>
							<div class="afrfq_company_info_sub_details" style="font-size:13px;width:73%; display:inline-block; vertical-align:top;">
							<?php if (!empty(get_option( 'afrfq_company_address' ))) { ?>
							<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( get_option( 'afrfq_company_address' ) ); ?></p>
						<?php } elseif (!empty($af_rfq_get_site_address_1)) { ?>
							<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_1 ); ?></p>
						<?php } elseif (( !empty($af_rfq_get_site_address_2) )&&( empty($af_rfq_get_site_address_1) )) { ?>
							<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_2 ); ?></p>
						<?php } ?>
								<p><strong style="margin-right: 12px;"><?php echo esc_html( 'Email:', 'addify_rfq' ); ?></strong>
							<?php
															$af_rfq_email_array_pdf = explode( ',', $admin_email );
															$iteration              = 1; // Initialize an iteration counter.

							foreach ( $af_rfq_email_array_pdf as $email ) {
								if ( $iteration > 1 ) {
																// Apply margin-left to the email address starting from the second iteration.
																echo '<span style="margin-top:58px; margin-left: 55px;">' . esc_attr( $email ) . '</span><br>';
								} else {
																									echo esc_attr( $email ) . '<br>'; // No margin-left for the first iteration.
								}

																						++$iteration; // Increment the iteration counter.
							}
							?>
								</p>
							</div>
							<div class="afrfq-quote-detail" style="line-height: 10px;font-size:13px; width:25%; display:inline-block; vertical-align:top;">
								<h1 style="margin-bottom:20px;line-height:20px; padding:7px 10px;  background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px;  font-weight: 700;" ><?php echo esc_html( 'Quote', 'addify_rfq' ); ?></h1>
								<p style="font-size:13px; "><strong style="margin-right: 12px;"><?php echo esc_html( 'Quote ID:', 'addify_rfq' ); ?></strong> <span style="display:inline-block; width:56%; text-align: right; "><?php echo esc_attr( $qoute_id ); ?></span></p>
								<p style="font-size:13px;"><strong style="margin-right: 12px;"><?php echo esc_html( 'Quote Status:', 'addify_rfq' ); ?></strong> <span style="display:inline-block; width:39%; text-align: right; "> <?php echo esc_attr( $afrfq_get_qoute_status_for_pdf ); ?></span></p>
								<p style="font-size:13px;"><strong style="margin-right: 12px;"><?php echo esc_html( 'Quote Date:', 'addify_rfq' ); ?></strong> <span style="display:inline-block; width:46%; text-align: right; "> <?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $afrfq_get_qoute_by_id->post_date ) ) ); ?></span></p>
							</div>
						</div>
						
						<div class="afrfq_client_info_details" style=" vertical-align:top;">
						<h1 style="margin-bottom:20px;padding:7px 10px;  background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px;  font-weight: 700;" ><?php echo esc_html( 'Customer Information', 'addify_rfq' ); ?></h1>
						<?php
									$afrfq_increment_j = 0;
						foreach ( $quote_fields as $key => $field ) {
							$field_id          = $field->ID;
							$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
							$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );
							$afrfq_field_value = get_post_meta( $field_id, 'afrfq_field_value', true );
							$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
							$field_data        = get_post_meta( $qoute_id, $afrfq_field_name, true );

							if ( empty( $field_data ) && ! empty( $afrfq_field_value ) ) {
								$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
							}

							if ( ( ! empty( $field_data ) ) && ( 'terms_cond' != $afrfq_field_type ) ) {
								++$afrfq_increment_j;

								if ( is_array( $field_data ) ) {

									$field_data = implode( ',', $field_data );
								}

								if ( empty( $afrfq_field_label ) ) {
									if ( 'user_login' == $afrfq_field_value ) {

										$afrfq_field_value = 'Username';
										?>
											<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																					
												<?php

									} else {

										$afrfq_field_value = ucwords( str_replace( '_', ' ', $afrfq_field_value ) );
										?>
											<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																					
												<?php

									}
								} else {

									?>
									<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_label . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																			
										<?php

								}
							}
						}
						if ( 0 == $afrfq_increment_j ) {

							?>
										<style>
											.cust_info_text{
												display:none;
											}
										</style>
							<?php
						}
						?>
								
						</div>
					   
						</div>
						<table cellpadding="0" cellspacing="0" style="margin-top:10px; border-collapse: collapse; width:100%; border:1px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
							<thead style="background:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
								<tr>
									<th style="padding:10px 20px; font-size:14px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width: 20%;" class="thumb sortable" data-sort="string-ins"><?php esc_html_e( 'Image', 'addify_rfq' ); ?></th>
									<th style="padding:10px 20px; font-size:14px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; text-align: left; width:35%;" class="item sortable" data-sort="string-ins"><?php esc_html_e( 'Item', 'addify_rfq' ); ?></th>
								<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
									<th style="padding:10px 20px; font-size:14px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width: 20%;" class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Item Price', 'addify_rfq' ); ?></th>
									<?php } ?>
								<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<th style="padding:10px 20px; font-size:14px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered price', 'addify_rfq' ); ?></th>
									<?php } ?>
								<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
									<th style="padding:10px 20px; font-size:14px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Quantity', 'addify_rfq' ); ?></th>
									<?php } ?>
									<th style="padding:10px 20px; font-size:14px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:15%;" class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Subtotal', 'addify_rfq' ); ?></th>
								<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<th style="padding:10px 20px; font-size:14px; font-family:sans-serif; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered Subtotal', 'addify_rfq' ); ?></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
							<?php

							$offered_price_subtotal = 0;

							foreach ( (array) $quote_contents as $item_id => $item ) {
								if ( isset( $item['data'] ) ) {
									$product = $item['data'];
								} else {
									continue;
								}

								if ( ! is_object( $product ) ) {
									continue;
								}

								$price                   = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
								$price                   = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
								$qty_display             = $item['quantity'];
								$offered_price           = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
								$product_link            = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
								$thumbnail               = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
								$offered_price_subtotal += floatval( $offered_price ) * intval( $qty_display );
								$image                   = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
								$image_url               = isset( $image[0] ) ? $image[0] : '';
								?>
									<tr class="item" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
										<td class="thumb" style="padding: 15px 20x; text-align:center; width: 20%;">
										<?php if ( ! empty( $image_url ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $image_url ); ?>" alt="Product Image"/>
											<?php } elseif ( ( empty( $image_url ) ) && ( extension_loaded( 'gd' ) ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $afrfq_default_image ); ?>" alt="Product Image"/>				
											<?php } ?>
										</td>
										
										<td class="woocommerce-table__product-name product-name" style=" padding:15px 20px!important; text-align:left; font-size: 13px; color:#000; width:35%; overflow-wrap: anywhere; word-break: break-word;">
										<?php
										$product_permalink = get_edit_post_link( $product->get_id() );

										echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="color:#000; text-decoration:none;">%s</a>', $product_permalink, $product->get_name() ) : $product->get_name(), $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					
										?>
											<br>
										<?php
											echo wp_kses_post( '<div class="wc-quote-item-sku" style="font-size:12x; margin-top:10px;"><strong style="font-size:12px;">' . esc_html__( 'SKU:', 'addify_rfq' ) . '</strong> ' . esc_attr( $product->get_sku() ) . '</div>' );
										?>
										</td>
										<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
										<td class="woocommerce-table__product-total product-total" style="padding: 15px 20x; text-align:center; color:#000; width: 20%; font-size: 13px;">
											<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $price, wc_get_price_decimals() ) ); ?>
										</td>
										<?php } ?>
										<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td style="padding: 15px 20x; text-align:center; font-size: 13px; color:#000; width: 10%;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
											<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $offered_price, wc_get_price_decimals() ) ); ?>
								</td>
								<?php } ?> 
										<td style="padding: 15px 20x; text-align:center; font-size: 13px; color:#000; width: 10%;">
											<?php echo esc_attr( $item['quantity'] ); ?>
										</td>
										<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
										<td class="woocommerce-table__product-total product-total" style="padding: 15px 20x; text-align:center; color:#000; font-size: 13px; width: 15%;">
											<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $price * $qty_display, wc_get_price_decimals() ) ); ?>
										</td>
										<?php } ?>
									<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td style="padding: 15px 20x; text-align:center; font-size: 13px; color:#000; width: 10%;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
										<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $offered_price * $qty_display, wc_get_price_decimals() ) ); ?>
								</td>
								<?php } ?> 
									</tr>
									<?php
									if ( in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

										if (( isset($item['addons']) )&&( !empty($item['addons']) )) {
											// echo '<br>';
											foreach ($item['addons'] as $addon) {
												?>
<tr>
<td style="text-align:left; padding: 15px 20px; width: 20%;"></td>
<td style="font-family: sans-serif; padding:4px 20px!important; text-align:left; font-family:sans-serif; color:#000; width:35%; overflow-wrap: anywhere; word-break: break-word;">
													<?php
													if (0!=$addon['price']) {

														$character_limit = 50;

														if (strlen($addon['name']) > $character_limit) {
															$trimmed_text = substr($addon['name'], 0, $character_limit) . '...';
														} else {
															$trimmed_text = $addon['name'];
														}

														echo '<b style="font-family: sans-serif;font-size: 13px; text-align: left; width:35%; overflow-wrap: anywhere; word-break: break-word;">' . esc_attr("{$trimmed_text} (" . get_woocommerce_currency_symbol() . " {$addon['price']}):" . PHP_EOL) . '</b>';
													} else {


														$character_limit = 50;

														if (strlen($addon['name']) > $character_limit) {
															$trimmed_text = substr($addon['name'], 0, $character_limit) . '...';
														} else {
															$trimmed_text = $addon['name'];
														}
					
														echo '<b style="font-family: sans-serif;font-size: 13px; text-align: left; width:35%; overflow-wrap: anywhere; word-break: break-word;">' . esc_attr($trimmed_text) . '</b>';
													}

													echo '<br>';

													if (( isset($addon['value'])&&( !empty($addon['value']) ) )) {

			
			
														$character_limit = 50;

														if (strlen($addon['value']) > $character_limit) {
															$trimmed_text = substr($addon['value'], 0, $character_limit) . '...';
														} else {
															$trimmed_text = $addon['value'];
														}
					

														echo '<p style="font-family: sans-serif;font-size: 13px;">' . esc_attr($trimmed_text) . '</p>';
			
			
													}  
													?>
</td>
												<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
<td></td>
<?php } ?>
									<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
<td></td>
			<?php } ?>
<td></td>
									<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
<td></td>
			<?php } ?>
									<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
<td></td>
			<?php } ?>
</tr>
									<?php

											}
										}                               

									}

							}
							?>
									</tbody>
								</table>
								<table cellpadding="0" cellspacing="0" style=" border-collapse: collapse; width:100%; font-family: sans-serif!important; border:1px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
								<tbody>
					<?php
					if ( isset( $afrfq_quote_totals['_subtotal'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
						?>
									<tr style=" margin-top:15px; font-family: sans-serif!important;">
									<td style="padding: 15px 25px; color:#000; font-size: 14px; text-align:left; font-weight:bold; font-family:sans-serif; " ><?php echo esc_html( 'Shipping Total', 'addify_rfq' ); ?></td>
							<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td></td>
									<td></td>
									<td></td>
									<td style="padding: 15px 20px; color:#000; font-size: 14px; text-align:right;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_subtotal'], wc_get_price_decimals() ) ); ?></td> 
									</tr>
							<?php
					}

					if ( isset( $afrfq_quote_totals['_offered_total'] ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) {
						?>
									<tr>
									<td style="padding: 15px 25px; color:#000; font-size: 14px; text-align:left;  font-weight:bold; font-family:sans-serif;"><?php echo esc_html( 'Offered Total', 'addify_rfq' ); ?></td>
							<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td style="padding: 15px 20px; color:#000;  font-size: 14px; text-align:right; sans-serif!important" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_offered_total'], wc_get_price_decimals() ) ); ?></td> 
									</tr>
				
							<?php
					}

					if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_tax_total'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
						?>
									<tr>
									<td style="padding: 15px 25px; color:#000; font-size: 14px; text-align:left;  font-weight:bold; font-family:sans-serif;" ><?php echo esc_html( 'Tax Total', 'addify_rfq' ); ?></td>
							<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td style="padding: 15px 20px; color:#000; font-size: 14px; text-align:right;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_tax_total'], wc_get_price_decimals() ) ); ?></td> 
									</tr>
							<?php
					}

		

					if ( isset( $afrfq_quote_totals['_total'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
						?>
									<tr style="background:<?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
									<td style="padding: 10px 25px; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; text-align:left;  font-weight:600; font-family: sans-serif; border: none!important; font-weight:bold; font-family:sans-serif;"><?php echo esc_html( 'Quote Total', 'addify_rfq' ); ?></td>
							<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td ></td>
									<td style="border:none;"></td>
									<td style="border:none;"></td>
									<td style="padding: 10px 20px; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size: 14px; text-align:right; border: none!important;">
																			<?php
																			echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_total'], wc_get_price_decimals() ) );

																			?>
									</td> 
									</tr>
									<?php
					}

					?>
								</tbody>
								</table>
							<?php if ( 'yes' == get_option( 'afrfq_enable_term_and_condition' ) ) { ?>
								<div class="afrfq_client_info_details" style=" margin-top:15px; vertical-align:top;  font-family:sans-serif;">
						<h1 style="margin-bottom:20px;padding:7px 10px; font-family:sans-serif; background-color:<?php echo esc_attr( $afrfq_backrgound_color ); ?>; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>; font-size:16px;  font-weight: 700;" ><?php echo esc_html( 'Terms & Conditions', 'addify_rfq' ); ?></h1>
						<ul  style="padding-left:13px; font-size:13px;  font-family:sans-serif; line-height: 10px;" ><li><?php echo esc_attr( $af_rfq_privicy_and_term_conditions ); ?></li></ul>
						</div>
				
				
								<?php
							}
				}
				?>
					</div>
					</body>
					</html>
				<?php

				$html = ob_get_clean();
				$dompdf->loadHtml( $html );
				$dompdf->setPaper( 'A4', 'portrait' );
				$dompdf->render();
				$pdf_content   = $dompdf->output();
				$pdf_file_name = 'Quote_' . current( $qoute_id_arr ) . '.pdf';

				$file_to_save = AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf';
				file_put_contents( $file_to_save, $pdf_content );

				if ( ( true == $afrfq_is_multiple_quotes_downloaded )
				&& ( ( 'yes' == $afrfq_admin_email_pdf ) ) ) {

					$to          = $admin_email;
					$subject     = 'Your PDF';
					$message     = 'Here is the PDF you requested.';
					$headers     = array(
						'Content-Type: text/html; charset=UTF-8',
					);
					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
					wp_mail( $to, $subject, $message, $headers, $attachments );

				} elseif ( ( true == $is_ajax_applied ) && ( true == $is_qoute_status_update ) ) {

					$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );

					return $attachments;

				} elseif ( ( true == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) {

					if ( isset( $email_values['af_admin']['enable'] ) && ( 'yes' === $email_values['af_admin']['enable'] ) && ( ! empty( $email_values['af_admin']['subject'] ) ) ) {

						$subject            = $email_values['af_admin']['subject'] ? $email_values['af_admin']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin']['heading'] ? $email_values['af_admin']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin']['message'] ? $email_values['af_admin']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_pending' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_pending']['enable'] ) && ( 'yes' === $email_values['af_pending']['enable'] ) && ( ! empty( $email_values['af_pending']['subject'] ) ) ) {

						$subject            = $email_values['af_pending']['subject'] ? $email_values['af_pending']['subject'] : 'Your PDF';
						$heading            = $email_values['af_pending']['heading'] ? $email_values['af_pending']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_pending']['message'] ? $email_values['af_pending']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_in_process' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_in_process']['enable'] ) && ( 'yes' === $email_values['af_in_process']['enable'] ) && ( ! empty( $email_values['af_in_process']['subject'] ) ) ) {

						$subject            = $email_values['af_in_process']['subject'] ? $email_values['af_in_process']['subject'] : 'Your PDF';
						$heading            = $email_values['af_in_process']['heading'] ? $email_values['af_in_process']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_in_process']['message'] ? $email_values['af_in_process']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_accepted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_accepted']['enable'] ) && ( 'yes' === $email_values['af_accepted']['enable'] ) && ( ! empty( $email_values['af_accepted']['subject'] ) ) ) {

						$subject            = $email_values['af_accepted']['subject'] ? $email_values['af_accepted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_accepted']['heading'] ? $email_values['af_accepted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_accepted']['message'] ? $email_values['af_accepted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_converted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_converted']['enable'] ) && ( 'yes' === $email_values['af_converted']['enable'] ) && ( ! empty( $email_values['af_converted']['subject'] ) ) ) {

						$subject            = $email_values['af_converted']['subject'] ? $email_values['af_converted']['subject'] : 'Your PDF';
						$heading            = $email_values['af_converted']['heading'] ? $email_values['af_converted']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_converted']['message'] ? $email_values['af_converted']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}

					if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

						$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
						$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_declined' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

					if ( isset( $email_values['af_declined']['enable'] ) && ( 'yes' === $email_values['af_declined']['enable'] ) && ( ! empty( $email_values['af_declined']['subject'] ) ) ) {

						$subject            = $email_values['af_declined']['subject'] ? $email_values['af_declined']['subject'] : 'Your PDF';
						$heading            = $email_values['af_declined']['heading'] ? $email_values['af_declined']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_declined']['message'] ? $email_values['af_declined']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				} elseif ( ( 'af_cancelled' === $quote_status ) && ( false == $admin_add_new_qoute ) ) {

					if ( isset( $email_values['af_cancelled']['enable'] ) && ( 'yes' === $email_values['af_cancelled']['enable'] ) && ( ! empty( $email_values['af_cancelled']['subject'] ) ) ) {
						$subject            = $email_values['af_cancelled']['subject'] ? $email_values['af_cancelled']['subject'] : 'Your PDF';
						$heading            = $email_values['af_cancelled']['heading'] ? $email_values['af_cancelled']['heading'] : 'Here is the PDF you requested.';
						$additional_message = $email_values['af_cancelled']['message'] ? $email_values['af_cancelled']['message'] : '';
						$message            = $heading . $additional_message;

						if ( ( ! empty( $af_rfq_user_email )
						&& ( 'yes' == $afrfq_user_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_user ) )
						&& ( 'Guest' != $user_name ) ) {

							$to = $af_rfq_user_email;

							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}

						if ( ( ! empty( $admin_email ) )
						&& ( 'yes' == $afrfq_admin_email_pdf )
						&& ( true == $af_rfq_is_email_send_to_admin ) ) {

							$to          = $admin_email;
							$headers     = array(
								'Content-Type: text/html; charset=UTF-8',
							);
							$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
							wp_mail( $to, $subject, $message, $headers, $attachments );

						}
					}
				}

				if ( true == $is_ajax_applied ) {

					$file_to_save_via_url = AFRFQ_URL . 'includes/pdf/pdf-files/Quotes.pdf';
					return $file_to_save_via_url;

				} else {

					$dompdf->stream();
					exit( 0 );
				}

			break;
		default:
			$options = new Options();
			$options->set( 'isRemoteEnabled', true );
			$dompdf = new Dompdf( $options );

			$email_values = (array) get_option( 'afrfq_emails' );
			$user_id      = get_post_meta( current( $qoute_id_arr ), '_customer_user', true );
			$user_name    = 'Guest';
			$id_user      = 'guest';
			$user         = get_user_by( 'id', intval( $user_id ) );
			if ( ! empty( $user_id ) && is_object( $user ) ) {

				$user_name         = $user->display_name;
				$af_rfq_user_email = $user->user_email;

			}

			ob_start();
			?>
					<!DOCTYPE html>
					<html>
					<head>
						<meta charset="UTF-8">
					</head>
					<body>
					
					<div id="addify_quote_items_container" style="">
					
			<?php

			foreach ( $qoute_id_arr as $qoute_id ) {

				$quote_contents = get_post_meta( $qoute_id, 'quote_contents', true );

				$afrfq_get_qoute_by_id = get_post( $qoute_id );

				if ( ! isset( $afrfq_quote_details ) ) {
					$afrfq_quote_details = new AF_R_F_Q_Quote();
				}

				if ( ! empty( get_option( 'afrfq_admin_email' ) ) ) {
					$admin_email = get_option( 'afrfq_admin_email' );
				} else {
					$admin_email = $afrfq_admin_defaut_email;
				}
				$afrfq_admin_email_pdf = get_option( 'afrfq_admin_email_pdf' );

				$quote_status       = get_post_meta( $qoute_id, 'quote_status', true );
				$afrfq_quote_totals = $afrfq_quote_details->get_calculated_totals( $quote_contents, $qoute_id );

				$afrfq_company_name             = '';
				$afrfq_company_name             = get_option( 'afrfq_company_name_text' );
				$afrfq_user_email_pdf           = get_option( 'afrfq_user_email_pdf' );
				$afrfq_get_qoute_status_for_pdf = '';

				if ( 'af_pending' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Pending';
				} elseif ( 'af_in_process' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'In Progress';
				} elseif ( 'af_accepted' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Accepted';
				} elseif ( 'af_converted' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Converted to Order';
				} elseif ( 'af_declined' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Declined';
				} elseif ( 'af_cancelled' === $quote_status ) {
					$afrfq_get_qoute_status_for_pdf = 'Cancelled';
				}

				?>
										
						<div class="afrfq_info_div" style="font-family: sans-serif!important;">
							<div class="af_rfq_company_information" style="border-bottom:2px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
								<div class="Qoute" style="font-size:22px; display:inline-block;width:70%; line-height:32px; vertical-align:middle;" >
								<?php 
								if (!empty(get_option( 'afrfq_company_name_text' ))) {
									$afrfq_company_name = get_option( 'afrfq_company_name_text' ); 
									?>
									<h1 style="font-size:19px;"><?php echo esc_attr( $afrfq_company_name ); ?></h1>
								<?php } elseif (!empty($af_rfq_get_site_title)) { ?>
									<h1 style="font-size:19px;"><?php echo esc_attr($af_rfq_get_site_title); ?></h1>
								<?php } ?>
								</div>
								<div class="afrfq_company_logo_preview" style="text-align:right; display:inline-block;width:25%;vertical-align:middle;">
							<?php if ( get_option( 'afrfq_company_logo' ) ) : ?>
										<img src="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" alt="Company Logo" style="border-radius:100%; width: 80px;" />
									<?php endif; ?>
								</div>
							</div>   
							
							<div class="afrfq_company_info_sub_details" style="">
							<?php if (!empty(get_option( 'afrfq_company_address' ))) { ?>
							<p style="font-size:13px;" ><strong style="margin-right: 12px;font-size:13px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( get_option( 'afrfq_company_address' ) ); ?></p>
						<?php } elseif (!empty($af_rfq_get_site_address_1)) { ?>
							<p style="font-size:13px;" ><strong style="margin-right: 12px;font-size:13px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_1 ); ?></p>
						<?php } elseif (( !empty($af_rfq_get_site_address_2) )&&( empty($af_rfq_get_site_address_1) )) { ?>
							<p style="font-size:13px;" ><strong style="margin-right: 12px;font-size:13px;"><?php echo esc_html( 'Address:', 'addify_rfq' ); ?></strong><?php echo esc_attr( $af_rfq_get_site_address_2 ); ?></p>
						<?php } ?>
								<p style="font-size:13px;"><strong style="font-size:13px;"><?php echo esc_html( 'Email:', 'addify_rfq' ); ?></strong>
							<?php
								$af_rfq_email_array_pdf = explode( ',', $admin_email );
								$iteration              = 1; // Initialize an iteration counter.

							foreach ( $af_rfq_email_array_pdf as $email ) {
								if ( $iteration > 1 ) {
									// Apply margin-left to the email address starting from the second iteration.
									echo '<span style="margin-top:8px; margin-left: 43px;">' . esc_attr( $email ) . '</span><br>';
								} else {
									echo esc_attr( $email ) . '<br>'; // No margin-left for the first iteration.
								}
								++$iteration; // Increment the iteration counter.
							}
							?>
								</p>
							</div>
							<br>
							<br>
							<div class=""style="margin-top:35px; border-bottom:2px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
								<div class="afrfq_client_info_details" style="display:inline-block; width:69%">
									<h1 class="cust_info_text" style="font-size:19px;" ><?php echo esc_html( 'Customer Information', 'addify_rfq' ); ?></h1>
									<?php
									$afrfq_increment_j = 0;
									foreach ( $quote_fields as $key => $field ) {
										$field_id          = $field->ID;
										$afrfq_field_name  = get_post_meta( $field_id, 'afrfq_field_name', true );
										$afrfq_field_label = get_post_meta( $field_id, 'afrfq_field_label', true );
										$afrfq_field_value = get_post_meta( $field_id, 'afrfq_field_value', true );
										$afrfq_field_type  = get_post_meta( $field_id, 'afrfq_field_type', true );
										$field_data        = get_post_meta( $qoute_id, $afrfq_field_name, true );

										if ( empty( $field_data ) && ! empty( $afrfq_field_value ) ) {
											$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
										}

										if ( ( ! empty( $field_data ) ) && ( 'terms_cond' != $afrfq_field_type ) ) {
											++$afrfq_increment_j;

											if ( is_array( $field_data ) ) {

												$field_data = implode( ',', $field_data );
											}

											if ( empty( $afrfq_field_label ) ) {
												if ( 'user_login' == $afrfq_field_value ) {

													$afrfq_field_value = 'Username';
													?>
											<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																					
													<?php

												} else {

													$afrfq_field_value = ucwords( str_replace( '_', ' ', $afrfq_field_value ) );
													?>
											<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_value . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																					
													<?php

												}
											} else {

												?>
									<p  style="font-size:13px;" ><strong style="margin-right:8px; font-size:13px;"><?php echo esc_attr( $afrfq_field_label . ':' ); ?></strong><?php echo esc_attr( $field_data ); ?></p>  
																			
																							<?php

											}
										}
									}
									if ( 0 == $afrfq_increment_j ) {

										?>
										<style>
											.cust_info_text{
												display:none;
											}
										</style>
										<?php
									}

									?>
									
								</div>
								<div class="afrfq-quote-detail" style="display:inline-block; width:30%">
									<h1 style="font-size:19px;"><?php echo esc_html('Quote', 'addify_rfq'); ?></h1>
									<p style="font-size:13px;"><strong style="margin-right:8px; font-size:13px;"><?php echo esc_html( 'Quote ID:', 'addify_rfq' ); ?></strong> <span style=""><?php echo esc_attr( $qoute_id ); ?></span></p>
									<p style="font-size:13px;"><strong style="margin-right:8px; font-size:13px;"><?php echo esc_html( 'Quote Status:', 'addify_rfq' ); ?> </strong> <span style=""> <?php echo esc_attr( $afrfq_get_qoute_status_for_pdf ); ?></span></p>
									<p style="font-size:13px;"><strong style="margin-right:8px; font-size:13px;"><?php echo esc_html( 'Quote Date:', 'addify_rfq' ); ?></strong> <span style=" "> <?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $afrfq_get_qoute_by_id->post_date ) ) ); ?></span></p>
								</div>
							</div>
						</div>
				
						<table cellpadding="0" cellspacing="0" style="margin-top:10px; border-collapse: collapse; width:100%; font-family: sans-serif!important;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
							<thead style="">                    
								<tr style="border-bottom:1px solid #d3d3d3a3;">
									<th style="text-align:left; padding:7px 20px 15px; font-family:sans-serif;  font-size: 14px; width: 20%;" class="thumb sortable" data-sort="string-ins"><?php esc_html_e( 'Image', 'addify_rfq' ); ?></th>
									<th style="padding:7px 20px 15px;  font-size: 14px; font-family:sans-serif; text-align: left; width:35%;" class="item sortable" data-sort="string-ins"><?php esc_html_e( 'Item', 'addify_rfq' ); ?></th>
									<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
									<th style="padding:7px 20px 15px;  font-size: 14px; font-family:sans-serif; width: 20%;" class="item_cost sortable" data-sort="float"><?php esc_html_e( 'Item Price', 'addify_rfq' ); ?></th>
									<?php } ?>
									<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<th style="padding:7px 20px 15px; font-family:sans-serif;  font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered price', 'addify_rfq' ); ?></th>
									<?php } ?>
									<th style="padding:7px 20px 15px; font-family:sans-serif;  font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Quantity', 'addify_rfq' ); ?></th>
									<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
									<th style="padding:7px 20px 15px; font-family:sans-serif; font-size: 14px; width:15%;" class="line_cost sortable" data-sort="float"><?php esc_html_e( 'Subtotal', 'addify_rfq' ); ?></th>
									<?php } ?>
									<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<th style="padding:7px 20px 15px; font-family:sans-serif;  font-size: 14px; width:10%;" class="quantity sortable" data-sort="int"><?php esc_html_e( 'Offered Subtotal', 'addify_rfq' ); ?></th>
									<?php } ?>
								</tr>
							</thead>
							<tbody>
							<?php

							$offered_price_subtotal = 0;

							foreach ( (array) $quote_contents as $item_id => $item ) {
								if ( isset( $item['data'] ) ) {
									$product = $item['data'];
								} else {
									continue;
								}

								if ( ! is_object( $product ) ) {
									continue;
								}

								$price                   = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
								$price                   = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
								$qty_display             = $item['quantity'];
								$offered_price           = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
								$product_link            = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
								$thumbnail               = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
								$offered_price_subtotal += floatval( $offered_price ) * intval( $qty_display );
								$image                   = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
								$image_url               = isset( $image[0] ) ? $image[0] : '';
								?>
									<tr style="border-bottom:1px solid #d3d3d3a3;" class="item" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
										<td class="thumb" style="text-align:left; padding: 15px 20px; width: 20%;">
										<?php if ( ! empty( $image_url ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $image_url ); ?>" alt="Product Image"/>
											<?php } elseif ( ( empty( $image_url ) ) && ( extension_loaded( 'gd' ) ) ) { ?>
												<img width="40px; "  src="<?php echo esc_url( $afrfq_default_image ); ?>" alt="Product Image"/>				
											<?php } ?>
										</td>
										
										<td class="woocommerce-table__product-name product-name" style=" padding:15px 20px!important; text-align:left; font-size: 13px; font-family:sans-serif; color:#000; width:35%; overflow-wrap: anywhere; word-break: break-word;">
										<?php
										$product_permalink = get_edit_post_link( $product->get_id() );

										echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s" style="color:#000; text-decoration:none;">%s</a>', $product_permalink, $product->get_name() ) : $product->get_name(), $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
											<br>
										<?php
											echo wp_kses_post( '<div class="wc-quote-item-sku" style="font-size:12x; font-family:sans-serif; margin-top:10px;"><strong style="font-size:12px; font-family:sans-serif;">' . esc_html__( 'SKU:', 'addify_rfq' ) . '</strong> ' . esc_attr( $product->get_sku() ) . '</div>' );
										?>
										</td>
										<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
										<td class="woocommerce-table__product-total product-total" style="padding: 15px 20x; text-align:center;font-family:sans-serif; color:#000; width: 20%; font-size: 13px;">
											<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $price, wc_get_price_decimals() ) ); ?>
										</td>
									<?php } ?>
								<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td style="padding: 15px 20x; text-align:center; font-size: 13px; color:#000; width: 10%; font-family:sans-serif;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
									<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $offered_price, wc_get_price_decimals() ) ); ?>
								</td>
								<?php } ?>
										<td style="padding: 15px 20x; text-align:center; font-size: 13px; color:#000; width: 10%; font-family:sans-serif;">
											<?php echo esc_attr( $item['quantity'] ); ?>
										</td>
							
										<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
										<td class="woocommerce-table__product-total product-total" style="padding: 15px 20x; text-align:center; color:#000; font-size: 13px; width: 15%; font-family:sans-serif;">
											<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $price * $qty_display, wc_get_price_decimals() ) ); ?>
										</td>
										<?php } ?>
	
										<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td style="padding: 15px 20x; text-align:center; font-size: 13px; color:#000; width: 10%; font-family:sans-serif;" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
											<?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $offered_price * $qty_display, wc_get_price_decimals() ) ); ?>
								</td>
								<?php } ?> 
	
									</tr>
									<?php
									if ( in_array( 'woocommerce-product-addons/woocommerce-product-addons.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {

										if (( isset($item['addons']) )&&( !empty($item['addons']) )) {
											// echo '<br>';
											foreach ($item['addons'] as $addon) {
												?>
<tr>
<td style="text-align:left; padding: 15px 20px; width: 20%;"></td>
<td style="font-family: sans-serif; padding:4px 20px!important; text-align:left; font-family:sans-serif; color:#000; width:35%; overflow-wrap: anywhere; word-break: break-word;">
													<?php
													if (0!=$addon['price']) {

														$character_limit = 50;

														if (strlen($addon['name']) > $character_limit) {
															$trimmed_text = substr($addon['name'], 0, $character_limit) . '...';
														} else {
															$trimmed_text = $addon['name'];
														}

														echo '<b style="font-family: sans-serif;font-size: 13px; text-align: left; width:35%; overflow-wrap: anywhere; word-break: break-word;">' . esc_attr("{$trimmed_text} (" . get_woocommerce_currency_symbol() . " {$addon['price']}):" . PHP_EOL) . '</b>';
													} else {


														$character_limit = 50;

														if (strlen($addon['name']) > $character_limit) {
															$trimmed_text = substr($addon['name'], 0, $character_limit) . '...';
														} else {
															$trimmed_text = $addon['name'];
														}
						
														echo '<b style="font-family: sans-serif;font-size: 13px; text-align: left; width:35%; overflow-wrap: anywhere; word-break: break-word;">' . esc_attr($trimmed_text) . '</b>';
													}

													if (( isset($addon['value'])&&( !empty($addon['value']) ) )) {

				
				
														$character_limit = 50;

														if (strlen($addon['value']) > $character_limit) {
															$trimmed_text = substr($addon['value'], 0, $character_limit) . '...';
														} else {
															$trimmed_text = $addon['value'];
														}
						

														echo '<p style="font-family: sans-serif;font-size: 13px;">' . esc_attr($trimmed_text) . '</p>';
				
				
													}  
													?>
</td>
												<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
<td></td>
<?php } ?>
								<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
<td></td>
				<?php } ?>
<td></td>
								<?php if ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) { ?>
<td></td>
				<?php } ?>
								<?php if ( ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
<td></td>
				<?php } ?>
</tr>
								<?php

											}
										}                               

									}


							}
							?>
							</tbody>
							</table>
							<table cellpadding="0" cellspacing="0" style=" border-collapse: collapse; width:44%; font-family: sans-serif!important; margin:auto 0 auto auto	;" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">
							<tbody>
				<?php
				if ( isset( $afrfq_quote_totals['_subtotal'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
					?>
									<tr style="margin-top:15px; font-family: sans-serif;">
						<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td></td>
									<td></td>
									<?php } ?>
									<td></td>
									<td></td>
									<td></td>
									<td style="width:25%; padding: 15px 25px; color:#000; font-family:sans-serif; font-size: 14px; text-align:left; font-weight:bold; font-family:sans-serif; "  ><?php esc_html_e( 'Subtotal', 'addify_rfq' ); ?></td>
									<td style="font-family: sans-serif; padding: 5px; color:#000; font-size: 14px; text-align:right;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_subtotal'], wc_get_price_decimals() ) ); ?></td> 
									</tr>
						<?php
				}

				if ( isset( $afrfq_quote_totals['_offered_total'] ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) {
					?>
									<tr>
						<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td ></td>
									<td ></td>
									<?php } ?>
									<td ></td>
									<td></td>
									<td></td>
									<td style="width:25%;padding: 15px 25px; color:#000; font-size: 14px; text-align:left;  font-weight:bold; font-family:sans-serif;"><?php echo esc_html( 'Offered Total', 'addify_rfq' ); ?></td>
									<td style="padding: 5px; color:#000;  font-size: 14px; text-align:right; font-family:sans-serif;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_offered_total'], wc_get_price_decimals() ) ); ?></td> 
									</tr>
				
						<?php
				}

				if ( wc_tax_enabled() && isset( $afrfq_quote_totals['_tax_total'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
					?>
									<tr style="font-family:sans-serif;">
						<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td ></td>
									<td ></td>
									<?php } ?>
									<td ></td>
									<td></td>
									<td></td>
									<td style="width:25%; padding: 15px 25px; color:#000; font-size: 14px; text-align:left; font-weight:bold; font-family:sans-serif; " ><?php echo esc_html( 'Tax Total', 'addify_rfq' ); ?></td>
									<td style="padding: 5px; color:#000; font-size: 14px; text-align:right;" ><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_tax_total'], wc_get_price_decimals() ) ); ?></td> 
									</tr>
						<?php
				}


				if ( isset( $afrfq_quote_totals['_total'] ) && ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) ) {
					?>
									<tr style="font-family:sans-serif;">
						<?php if ( ( 'yes' === get_option( 'afrfq_enable_pro_price' ) ) && ( 'yes' === get_option( 'afrfq_enable_off_price' ) ) ) { ?>
									<td ></td>
									<td ></td>
									<?php } ?>
									<td ></td>
									<td ></td>
									<td ></td>
									<td style="padding: 10px 25px; padding-bottom:30px;    font-size: 14px; text-align:left;  font-weight:bold;  border: none!important; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>;"><?php echo esc_html( 'Quote Total', 'addify_rfq' ); ?></td>
									<td style="padding: 5px; padding-bottom:30px;   font-size: 14px;  text-align:right; border: none!important; color:<?php echo esc_attr( $afrfq_text_color_for_background ); ?>;"><?php echo wp_kses_post( get_woocommerce_currency_symbol() . ' ' . wc_format_decimal( $afrfq_quote_totals['_total'], wc_get_price_decimals() ) ); ?></td> 
									</tr>
						<?php } ?>
								</tbody>
								</table>
						<?php if ( 'yes' == get_option( 'afrfq_enable_term_and_condition' ) ) { ?>
							<div class="afrfq_client_info_details" style="font-family:sans-serif; border-top:2px solid <?php echo esc_attr( $afrfq_backrgound_color ); ?>;">
							<h1 style="font-family:sans-serif; margin-bottom:0px; font-size:18px; line-height:28px;" ><?php echo esc_html( 'Terms & Conditions', 'addify_rfq' ); ?></h1>
							<ul style="font-family:sans-serif; margin:0px; padding:15px;" ><li><?php echo esc_attr( $af_rfq_privicy_and_term_conditions ); ?></li></ul>
						</div>
						<?php } ?>
						</div>
					
					<?php } ?>
					</div>
					</body>
					</html>
					<?php

					$html = ob_get_clean();
					$dompdf->loadHtml( $html );
					$dompdf->setPaper( 'A4', 'portrait' );
					$dompdf->render();
					$pdf_content   = $dompdf->output();
					$pdf_file_name = 'Qoute_' . current( $qoute_id_arr ) . '.pdf';

					$file_to_save = AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf';
					file_put_contents( $file_to_save, $pdf_content );

					if ( ( true == $afrfq_is_multiple_quotes_downloaded )
					&& ( ( 'yes' == $afrfq_admin_email_pdf ) ) ) {

						$to          = $admin_email;
						$subject     = 'Your PDF';
						$message     = 'Here is the PDF you requested.';
						$headers     = array(
							'Content-Type: text/html; charset=UTF-8',
						);
						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
						wp_mail( $to, $subject, $message, $headers, $attachments );

					} elseif ( ( true == $is_ajax_applied ) && ( true == $is_qoute_status_update ) ) {

						$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );

						return $attachments;

					} elseif ( ( true == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) {

						if ( isset( $email_values['af_admin']['enable'] ) && ( 'yes' === $email_values['af_admin']['enable'] ) && ( ! empty( $email_values['af_admin']['subject'] ) ) ) {

							$subject            = $email_values['af_admin']['subject'] ? $email_values['af_admin']['subject'] : 'Your PDF';
							$heading            = $email_values['af_admin']['heading'] ? $email_values['af_admin']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_admin']['message'] ? $email_values['af_admin']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $admin_email ) )
							&& ( 'yes' == $afrfq_admin_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_admin ) ) {

								$to          = $admin_email;
								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}
					} elseif ( ( 'af_pending' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

						if ( isset( $email_values['af_pending']['enable'] ) && ( 'yes' === $email_values['af_pending']['enable'] ) && ( ! empty( $email_values['af_pending']['subject'] ) ) ) {

							$subject            = $email_values['af_pending']['subject'] ? $email_values['af_pending']['subject'] : 'Your PDF';
							$heading            = $email_values['af_pending']['heading'] ? $email_values['af_pending']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_pending']['message'] ? $email_values['af_pending']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $af_rfq_user_email )
							&& ( 'yes' == $afrfq_user_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_user ) )
							&& ( 'Guest' != $user_name ) ) {

								$to = $af_rfq_user_email;

								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}

							if ( ( ! empty( $admin_email ) )
							&& ( 'yes' == $afrfq_admin_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_admin ) ) {

								$to          = $admin_email;
								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}
					} elseif ( ( 'af_in_process' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

						if ( isset( $email_values['af_in_process']['enable'] ) && ( 'yes' === $email_values['af_in_process']['enable'] ) && ( ! empty( $email_values['af_in_process']['subject'] ) ) ) {

							$subject            = $email_values['af_in_process']['subject'] ? $email_values['af_in_process']['subject'] : 'Your PDF';
							$heading            = $email_values['af_in_process']['heading'] ? $email_values['af_in_process']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_in_process']['message'] ? $email_values['af_in_process']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $af_rfq_user_email )
							&& ( 'yes' == $afrfq_user_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_user ) )
							&& ( 'Guest' != $user_name ) ) {

								$to = $af_rfq_user_email;

								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}

							if ( ( ! empty( $admin_email ) )
							&& ( 'yes' == $afrfq_admin_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_admin ) ) {

								$to          = $admin_email;
								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}
					} elseif ( ( 'af_accepted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

						if ( isset( $email_values['af_accepted']['enable'] ) && ( 'yes' === $email_values['af_accepted']['enable'] ) && ( ! empty( $email_values['af_accepted']['subject'] ) ) ) {

							$subject            = $email_values['af_accepted']['subject'] ? $email_values['af_accepted']['subject'] : 'Your PDF';
							$heading            = $email_values['af_accepted']['heading'] ? $email_values['af_accepted']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_accepted']['message'] ? $email_values['af_accepted']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $af_rfq_user_email )
							&& ( 'yes' == $afrfq_user_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_user ) )
							&& ( 'Guest' != $user_name ) ) {

								$to = $af_rfq_user_email;

								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}

							if ( ( ! empty( $admin_email ) )
							&& ( 'yes' == $afrfq_admin_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_admin ) ) {

								$to          = $admin_email;
								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}
					} elseif ( ( 'af_converted' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

						if ( isset( $email_values['af_converted']['enable'] ) && ( 'yes' === $email_values['af_converted']['enable'] ) && ( ! empty( $email_values['af_converted']['subject'] ) ) ) {

							$subject            = $email_values['af_converted']['subject'] ? $email_values['af_converted']['subject'] : 'Your PDF';
							$heading            = $email_values['af_converted']['heading'] ? $email_values['af_converted']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_converted']['message'] ? $email_values['af_converted']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $af_rfq_user_email )
							&& ( 'yes' == $afrfq_user_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_user ) )
							&& ( 'Guest' != $user_name ) ) {

								$to = $af_rfq_user_email;

								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}

						if ( isset( $email_values['af_admin_conv']['enable'] ) && ( 'yes' === $email_values['af_admin_conv']['enable'] ) && ( ! empty( $email_values['af_admin_conv']['subject'] ) ) ) {

							$subject            = $email_values['af_admin_conv']['subject'] ? $email_values['af_admin_conv']['subject'] : 'Your PDF';
							$heading            = $email_values['af_admin_conv']['heading'] ? $email_values['af_admin_conv']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_admin_conv']['message'] ? $email_values['af_admin_conv']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $admin_email ) )
							&& ( 'yes' == $afrfq_admin_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_admin ) ) {

								$to          = $admin_email;
								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}
					} elseif ( ( 'af_declined' === $quote_status ) && ( ( false == $admin_add_new_qoute ) && ( false == $customer_add_new_qoute ) ) ) {

						if ( isset( $email_values['af_declined']['enable'] ) && ( 'yes' === $email_values['af_declined']['enable'] ) && ( ! empty( $email_values['af_declined']['subject'] ) ) ) {

							$subject            = $email_values['af_declined']['subject'] ? $email_values['af_declined']['subject'] : 'Your PDF';
							$heading            = $email_values['af_declined']['heading'] ? $email_values['af_declined']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_declined']['message'] ? $email_values['af_declined']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $af_rfq_user_email )
							&& ( 'yes' == $afrfq_user_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_user ) )
							&& ( 'Guest' != $user_name ) ) {

								$to = $af_rfq_user_email;

								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}

							if ( ( ! empty( $admin_email ) )
							&& ( 'yes' == $afrfq_admin_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_admin ) ) {

								$to          = $admin_email;
								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}
					} elseif ( ( 'af_cancelled' === $quote_status ) && ( false == $admin_add_new_qoute ) ) {

						if ( isset( $email_values['af_cancelled']['enable'] ) && ( 'yes' === $email_values['af_cancelled']['enable'] ) && ( ! empty( $email_values['af_cancelled']['subject'] ) ) ) {
							$subject            = $email_values['af_cancelled']['subject'] ? $email_values['af_cancelled']['subject'] : 'Your PDF';
							$heading            = $email_values['af_cancelled']['heading'] ? $email_values['af_cancelled']['heading'] : 'Here is the PDF you requested.';
							$additional_message = $email_values['af_cancelled']['message'] ? $email_values['af_cancelled']['message'] : '';
							$message            = $heading . $additional_message;

							if ( ( ! empty( $af_rfq_user_email )
							&& ( 'yes' == $afrfq_user_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_user ) )
							&& ( 'Guest' != $user_name ) ) {

								$to = $af_rfq_user_email;

								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}

							if ( ( ! empty( $admin_email ) )
							&& ( 'yes' == $afrfq_admin_email_pdf )
							&& ( true == $af_rfq_is_email_send_to_admin ) ) {

								$to          = $admin_email;
								$headers     = array(
									'Content-Type: text/html; charset=UTF-8',
								);
								$attachments = array( AFRFQ_PLUGIN_DIR . 'includes/pdf/pdf-files/Quotes.pdf' );
								wp_mail( $to, $subject, $message, $headers, $attachments );

							}
						}
					}

					if ( true == $is_ajax_applied ) {

						$file_to_save_via_url = AFRFQ_URL . 'includes/pdf/pdf-files/Quotes.pdf';
						return $file_to_save_via_url;

					} else {

						$dompdf->stream();
						exit( 0 );
					}

			break;
	}
}

