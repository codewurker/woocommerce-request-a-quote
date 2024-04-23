<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/quote-table.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $af_quote ) ) {
	$af_quote = new AF_R_F_Q_Quote();
}

$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) ? true : false;
$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) ? true : false;
$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) ? true : false;
$colspan          = 4;
$colspan          = $price_display ? $colspan + 2 : $colspan;
$colspan          = $of_price_display ? $colspan + 2 : $colspan;

do_action( 'addify_before_quote_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents  addify-quote-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'addify_rfq' ); ?></th>
				<?php if ( $price_display ) : ?>
					<th class="product-price"><?php esc_html_e( 'Price', 'addify_rfq' ); ?></th>
				<?php endif; ?>
				<?php if ( $of_price_display ) : ?>
					<th class="product-price"><?php esc_html_e( 'Offered Price', 'addify_rfq' ); ?></th>
				<?php endif; ?>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'addify_rfq' ); ?></th>
				<?php if ( $price_display ) : ?>
					<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'addify_rfq' ); ?></th>
				<?php endif; ?>
				<?php if ( $of_price_display ) : ?>
					<th class="product-subtotal"><?php esc_html_e( 'Offered Subtotal', 'addify_rfq' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'addify_before_quote_contents' ); ?>

			<?php
			foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

				if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
					continue;
				}

				$_product      = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
				$product_id    = apply_filters( 'addify_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key );
				$price         = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
				$price         = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];
				$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

				if ( $_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
					$product_permalink = apply_filters( 'addify_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink( $quote_item ) : '', $quote_item, $quote_item_key );
					?>
					<tr class=" <?php echo esc_attr( apply_filters( 'addify_quote_item_class', 'cart_item', $quote_item, $quote_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo wp_kses_post(
									apply_filters(
										'addify_quote_item_remove_link',
										sprintf(
											'<a href="%s" class="remove remove-cart-item remove-quote-item" aria-label="%s" data-cart_item_key="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
											esc_attr( $quote_item_key ),
											esc_html__( 'Remove this item', 'addify_rfq' ),
											esc_attr( $quote_item_key ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$quote_item_key
									)
								);
							?>
						</td>

						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'addify_quote_item_thumbnail', $_product->get_image(), $quote_item, $quote_item_key );

						if ( ! $product_permalink ) {
							echo wp_kses_post( $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) ); // phpcs:ignore WordPress.Security.EscapeOutput
						}
						?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'addify_rfq' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'addify_quote_item_name', $_product->get_name(), $quote_item, $quote_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'addify_quote_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $quote_item, $quote_item_key ) );
						}

						do_action( 'addify_after_quote_item_name', $quote_item, $quote_item_key );

						// Meta data.
						echo wp_kses_post( wc_get_formatted_cart_item_data( $quote_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $quote_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'addify_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'addify_rfq' ) . '</p>', $product_id ) );
						}

						echo wp_kses_post( sprintf( '<p><small>SKU:%s</small></p>', esc_attr( $_product->get_sku() ) ) );
						?>
						</td>

						<?php if ( $price_display ) : ?>
							<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'addify_rfq' ); ?>">
								<?php
									$args['qty']   = 1;
									$args['price'] = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
									$args['price'] = empty( $quote_item['role_base_price'] ) ? $_product->get_price() : $quote_item['role_base_price'];

									echo wp_kses_post( apply_filters( 'addify_quote_item_price', $af_quote->get_product_price( $_product, $args ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>
						
						<?php if ( $of_price_display ) : ?>
							<td class="product-price offered-price" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
								<input type="number" class="input-text offered-price-input text" step="any" name="offered_price[<?php echo esc_attr( $quote_item_key ); ?>]" value="<?php echo esc_attr( $offered_price ); ?>">
							</td>
						<?php endif; ?>	

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_rfq' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '<input type="hidden" name="quote_qty[%s]" value="1" />', $quote_item_key );
						} else {
							woocommerce_quantity_input(
								array(
									'input_name'   => "quote_qty[{$quote_item_key}]",
									'input_value'  => $quote_item['quantity'],
									'max_value'    => $_product->get_max_purchase_quantity(),
									'min_value'    => '0',
									'product_name' => $_product->get_name(),
								),
								$_product,
								true
							);
						}
						?>
						</td>

						<?php if ( $price_display ) : ?>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'addify_rfq' ); ?>">
								<?php
									$args['qty']   = $quote_item['quantity'];
									$args['price'] = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
									$args['price'] = empty( $quote_item['role_base_price'] ) ? $args['price'] : $quote_item['role_base_price'];

									echo wp_kses_post( apply_filters( 'addify_quote_item_subtotal', $af_quote->get_product_subtotal( $_product, $quote_item['quantity'], $args ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>	

						<?php if ( $of_price_display ) : ?>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'addify_rfq' ); ?>">
								<?php
									echo wp_kses_post( apply_filters( 'addify_quote_item_subtotal', wc_price( $offered_price * $quote_item['quantity'] ), $quote_item, $quote_item_key ) ); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							</td>
						<?php endif; ?>
						
					</tr>
					<?php
				}
			}
			?>
			<td colspan="<?php echo esc_attr( $colspan ); ?>" class="actions">

					<?php
					$afrfq_update_button_text     = get_option( 'afrfq_update_button_text' );
					$afrfq_update_button_bg_color = get_option( 'afrfq_update_button_bg_color' );
					$afrfq_update_button_fg_color = get_option( 'afrfq_update_button_fg_color' );
					$afrfq_update_button_text     = empty( $afrfq_update_button_text ) ? __( 'Update Quote', 'addify_rfq' ) : $afrfq_update_button_text;
					?>

					<style type="text/css">
						.afrfq_update_quote_btn{
							color: <?php echo esc_html( $afrfq_update_button_fg_color ); ?> !important;
							background-color: <?php echo esc_html( $afrfq_update_button_bg_color ); ?> !important;
						}
					</style>

					<button type="button" type="submit" id="afrfq_update_quote_btn" class="button afrfq_update_quote_btn" name="update_quote" value="<?php esc_html( $afrfq_update_button_text ); ?>"><?php echo esc_html( $afrfq_update_button_text ); ?></button> 

					<?php do_action( 'addify_quote_actions' ); ?>

					<?php wp_nonce_field( 'addify-cart', 'addify-cart-nonce' ); ?>
				</td>
			</tbody>
			<?php do_action( 'addify_quote_contents' ); ?>
			</tbody>
		</table>
			<?php do_action( 'addify_after_quote_contents' ); ?>

	<?php do_action( 'addify_after_quote_table' ); ?>
