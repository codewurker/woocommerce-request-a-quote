<?php
/**
 * Quote details in my Account.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/my-account/quote-details-my-account.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

$quote_contents = get_post_meta( $afrfq_id, 'quote_contents', true );
$quote_status   = get_post_meta( $afrfq_id, 'quote_status', true );
$quote_coverter = get_post_meta( $afrfq_id, 'converted_by', true );

$quote_user = get_post_meta( $afrfq_id, '_customer_user', true );

if ( is_user_logged_in() ) {
	$customer = get_current_user_id();
}

if ( $customer != $quote_user ) {

	$func = '<div class="woocommerce-MyAccount-content">
	<div class="woocommerce-notices-wrapper"></div><div class="woocommerce-error">' . esc_html__( 'Invalid quote.', 'addify_rfq' ) . ' <a href="' . esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) . '" class="wc-forward">My account</a></div></div>';

	echo wp_kses_post( $func );
	return;
}

$conv_enable = 'yes' === get_option( 'afrfq_enable_converted_by' ) ? true : false;
$statuses    = array(
	'af_pending'    => __( 'Pending', 'addify_rfq' ),
	'af_in_process' => __( 'In Process', 'addify_rfq' ),
	'af_accepted'   => __( 'Accepted', 'addify_rfq' ),
	'af_converted'  => __( 'Converted to Order', 'addify_rfq' ),
	'af_declined'   => __( 'Declined', 'addify_rfq' ),
	'af_cancelled'  => __( 'Cancelled', 'addify_rfq' ),
);


if ( ! isset( $af_quote ) ) {
	$af_quote = new AF_R_F_Q_Quote();
}

$quote_totals = $af_quote->get_calculated_totals( $quote_contents, $afrfq_id );

$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) ? true : false;
$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) ? true : false;
$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) ? true : false;

?>
<section class="woocommerce-order-details addify-quote-details">
	<?php do_action( 'addify_before_quote_table' ); ?>

	<table class="shop_table order_details quote_details" cellspacing="0">
		<tr>
			<th class="quote-number"><?php esc_html_e( 'Quote #', 'addify_rfq' ); ?></th>
			<td class="quote-number"><?php echo esc_html( $afrfq_id ); ?> </td>
		</tr>
		<tr>
			<th class="quote-date"><?php esc_html_e( 'Quote Date', 'addify_rfq' ); ?></th>
			<td class="quote-date"><?php echo esc_attr( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $quote->post_date ) ) ); ?> </td>
		</tr>
		<tr>
			<th class="quote-status"><?php esc_html_e( 'Current Status', 'addify_rfq' ); ?></th>
			<td class="quote-status"><?php echo isset( $statuses[ $quote_status ] ) ? esc_html( $statuses[ $quote_status ] ) : 'Pending'; ?> </td>
		</tr>
		<?php if ( $conv_enable && 'af_converted' === $quote_status ) : ?>
			<tr>
				<th class="quote-converter"><?php esc_html_e( 'Converted by', 'addify_rfq' ); ?></th>
				<td class="quote-converter"><?php echo esc_html( $quote_coverter ); ?> </td>
			</tr>
		<?php endif; ?>
	</table>
	<h2><?php echo esc_html__( 'Quote Details', 'addify_rfq' ); ?></h2>
	<table class="shop_table shop_table_responsive cart order_details quote_details" cellspacing="0">
		<thead>
			<tr>
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
			if ( ! empty( $quote_contents ) ) {


				foreach ( $quote_contents as $quote_item_key => $quote_item ) {

					if ( ! isset( $quote_item['data'] ) || ! is_object( $quote_item['data'] ) ) {
						continue;
					}

					$_product      = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
					$product_id    = apply_filters( 'addify_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key );
					$price         = empty( $quote_item['addons_price'] ) ? $_product->get_price() : $quote_item['addons_price'];
					$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

					if ( $_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
						$product_permalink = apply_filters( 'addify_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink( $quote_item ) : '', $quote_item, $quote_item_key );
						?>
					<tr class="addify__quote-item <?php echo esc_attr( apply_filters( 'addify_quote_item_class', 'cart_item', $quote_item, $quote_item_key ) ); ?>">

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
						?>
						<br>
						<?php
							echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'woocommerce' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';

						do_action( 'addify_after_quote_item_name', $quote_item, $quote_item_key );

						// Meta data.
						echo wp_kses_post( wc_get_formatted_cart_item_data( $quote_item ) ); // phpcs:ignore WordPress.Security.EscapeOutput

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $quote_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'addify_quote_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'addify_rfq' ) . '</p>', $product_id ) );
						}
						?>
						</td>

						<?php if ( $price_display ) : ?>
							<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'addify_rfq' ); ?>">
								<?php echo wp_kses_post( wc_price( $price ) ); ?>
							</td>
						<?php endif; ?>
						
						<?php if ( $of_price_display ) : ?>
							<td class="product-price" data-title="<?php esc_attr_e( 'Offered Price', 'addify_rfq' ); ?>">
								<?php echo wp_kses_post( wc_price( $offered_price ) ); ?>
							</td>
						<?php endif; ?>
						
						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'addify_rfq' ); ?>">
							<?php
							$qty_display = $quote_item['quantity'];
							// phpcs:ignore WordPress.Security.EscapeOutput
							echo wp_kses_post( apply_filters( 'addify_rfq_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&nbsp;%s', $qty_display ) . '</strong>', $quote_item ) );
							?>
						</td>

							<?php if ( $price_display ) : ?>
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'addify_rfq' ); ?>">
								<?php echo wp_kses_post( wc_price( $price * $qty_display ) ); ?>
							</td>
						<?php endif; ?>
							<?php if ( $of_price_display ) : ?>	
							<td class="product-subtotal" data-title="<?php esc_attr_e( 'Offered Subtotal', 'addify_rfq' ); ?>">
								<?php echo wp_kses_post( wc_price( $offered_price * $qty_display ) ); ?>
							</td>
						<?php endif; ?>
					</tr>
						<?php
					}
				}
			}
			?>
			</tbody>
		</table>
			<?php do_action( 'addify_after_quote_contents' ); ?>

	<?php do_action( 'addify_after_quote_table' ); ?>


	<?php do_action( 'addify_before_quote_collaterals' ); ?>
	
	<div class="cart-collaterals">
		<?php
			/**
			 * Cart collaterals hook.
			 *
			 * @hooked addify_cross_sell_display
			 * @hooked addify_quote_totals - 10
			 */
			do_action( 'addify_quote_collaterals' );
		?>
		<?php if ( $price_display || $of_price_display ) : ?>
			<div class="cart_totals">

				<?php do_action( 'woocommerce_before_cart_totals' ); ?>

				<h2><?php esc_html_e( 'Quote totals', 'addify_rfq' ); ?></h2>

				<table cellspacing="0" class="shop_table shop_table_responsive">

				<?php if ( $price_display ) : ?>
						<tr class="cart-subtotal">
							<th><?php esc_html_e( 'Subtotal(standard)', 'addify_rfq' ); ?></th>
							<td data-title="<?php esc_attr_e( 'Subtotal', 'addify_rfq' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_subtotal'] ) ); ?></td>
						</tr>
						<?php
					endif;

				if ( isset( $quote_totals['_offered_total'] ) && $of_price_display ) {
					?>
						<tr class="cart-subtotal">
							<th><?php esc_html_e( 'Offered Price Subtotal', 'addify_rfq' ); ?></th>
							<td data-title="<?php esc_attr_e( 'Offered Price Subtotal', 'addify_rfq' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_offered_total'] ) ); ?></td>
						</tr>
						<?php
				}

				if ( wc_tax_enabled() && $tax_display ) {
					?>
						<tr class="tax-rate">
							<th><?php echo esc_html__( 'Vat(standard)', 'addify_rfq' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td data-title="<?php echo esc_html__( 'Vat', 'addify_rfq' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_tax_total'] ) ); ?></td>
						</tr>
						<?php
				}
				?>

					<?php if ( $quote_totals['_shipping_total'] ) : ?>
						<tr class="shipping-cost">
							<th><?php echo esc_html__( 'Shipping Cost', 'addify_rfq' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td data-title="<?php echo esc_html__( 'Shipping', 'addify_rfq' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_shipping_total'] ) ); ?></td>
						</tr>
					<?php endif; ?>
					
					<?php if ( $price_display ) : ?>
						<tr class="order-total">
							<th><?php esc_html_e( 'Total(standard)', 'addify_rfq' ); ?></th>
							<td data-title="<?php esc_attr_e( 'Total', 'addify_rfq' ); ?>"><?php echo wp_kses_post( wc_price( $quote_totals['_total'] ) ); ?></td>
						</tr>
					<?php endif; ?>
				
				
				</table>				
			</div>
		<?php endif; ?>
		<?php
		$afrfq_enable = 'yes' === get_option( 'afrfq_enable_convert_order' ) ? true : false;

		if ( in_array( $quote_status, array( 'af_accepted', 'af_in_process' ), true ) && $afrfq_enable ) :
			?>
			<form method="post">
				<?php wp_nonce_field( '_afrfq__wpnonce', '_afrfq__wpnonce' ); ?>
				<div class="addify_converty_to_order_button">
					<button type="submit" value="<?php echo intval( $afrfq_id ); ?>" name="addify_convert_to_order_customer" id="addify_convert_to_order_customer" class="button button-primary button-large" >
						<?php echo esc_html__( 'Convert to Order', 'addify_rfq' ); ?>
					</button>
				</div>
			</form>
		<?php endif; ?>
	</div>
</section>
