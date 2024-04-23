<?php
/**
 * Quote items table row template
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

?>

<tr class="item" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
	<td class="thumb">
		<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>'; ?>
	</td>

	<td class="woocommerce-table__product-name product-name">
		<?php
		$is_visible        = $product && $product->is_visible();
		$product_permalink = apply_filters( 'addify_rfq_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $post );

		echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $product->get_name() ) : $product->get_name(), $item, $is_visible ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		do_action( 'addify_rfq_order_item_meta_start', $item_id, $item, $post, false );

		// Meta data.
		echo wp_kses_post( wc_get_formatted_cart_item_data( $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput

		do_action( 'addify_rfq_order_item_meta_end', $item_id, $item, $post, false );
		?>
		<br>
		<?php
			echo wp_kses_post( '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'addify_rfq' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>' );
		?>
	</td>
	<td class="woocommerce-table__product-total product-total">
		<?php echo wp_kses_post( wc_price( $product->get_Price() ) ); ?>
	</td>
	<td class="woocommerce-table__product-total product-total">
		<input type="number" class="input-text offered-price-input text" min="1" name="offered_price[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $offered_price ); ?>">
	</td>

	<td>
		<input type="number" class="input-text quote-qty-input text" min="1" name="quote_qty[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item['quantity'] ); ?>">
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo wp_kses_post( wc_price( $product->get_Price() * $qty_display ) ); ?>
	</td>

	<td class="woocommerce-table__product-total product-total">
		<?php echo wp_kses_post( wc_price( $offered_price * $qty_display ) ); ?>
	</td>

</tr>
