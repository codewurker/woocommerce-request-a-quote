<?php
/**
 * Order Item Details
 *
 * This template is used to view quote items details in admin.
 *
 * @package woocommerce-request-a-quote
 * @version 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'addify_rfq_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'addify_rfq' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'addify_rfq' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'addify_rfq' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'addify_rfq_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'addify_rfq_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">

					<td class="woocommerce-table__product-name product-name">
						<?php
						$is_visible        = $product && $product->is_visible();
						$product_permalink = apply_filters( 'addify_rfq_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );

						echo wp_kses_post( apply_filters( 'addify_rfq_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						$qty_display = $item->get_quantity();

						echo wp_kses_post( apply_filters( 'addify_rfq_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $qty_display ) . '</strong>', $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						do_action( 'addify_rfq_order_item_meta_start', $item_id, $item, $order, false );

						wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						do_action( 'addify_rfq_order_item_meta_end', $item_id, $item, $order, false );
						?>
					</td>

					<td class="woocommerce-table__product-total product-total">
						<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>

				</tr>
				<?php
			}

			do_action( 'addify_rfq_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo 'payment_method' === $key ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'addify_rfq' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action( 'addify_rfq_order_details_after_order_table', $order ); ?>
</section>
