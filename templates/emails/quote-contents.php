<?php
/**
 * Email Quote Contents.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/emails/quote-contents.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'addify_rfq_before_email_quote_contents' );

?>
<div style="margin-top: 10px; margin-bottom: 10px;">
	<table style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif" cellspacing="0" cellpadding="6" border="1">
		<thead>
			<tr>
				<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
				</th>
				<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
					<?php echo esc_html__( 'Product', 'addify_rfq' ); ?>
				</th>
				<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
					<?php echo esc_html__( 'Quantity', 'addify_rfq' ); ?>
				</th>
				<?php if ( $price_display ) : ?>
					<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
						<?php echo esc_html__( 'Price', 'addify_rfq' ); ?>
					</th>
				<?php endif; ?>
				<?php if ( $of_price_display ) : ?>
					<th scope="col" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
						<?php echo esc_html__( 'Offered Price', 'addify_rfq' ); ?>
					</th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( (array) $quote_contents as $key => $item ) :

				$product = isset( $item['data'] ) ? $item['data'] : '';

				if ( ! is_object( $product ) ) {
					continue;
				}

				$price         = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
				$price         = empty( $item['role_base_price'] ) ? $price : $item['role_base_price'];
				$offered_price = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;

				?>
				<tr>
					<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">
						<?php echo wp_kses_post( $product->get_image( 'woocommerce_gallery_thumbnail' ) ); ?>
					</td>
					<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">
						<?php
						echo esc_html( $product->get_name() );
						echo wp_kses_post( wc_get_formatted_cart_item_data( $item ) );
						?>
						<br>
						<small>
							<b><?php echo esc_html__( 'SKU:', 'addify_rfq' ); ?></b><?php echo esc_html( $product->get_sku() ); ?>
						</small>
					</td>

					<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
					<?php echo esc_attr( $item['quantity'] ); ?>
					</td>

					<?php if ( $price_display ) : ?>
						<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
						<?php echo wp_kses_post( wc_price( $price ) ); ?>
						</td>
					<?php endif; ?>

					<?php if ( $of_price_display ) : ?>
						<td style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
						<?php echo wp_kses_post( wc_price( $offered_price ) ); ?>
						</td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
		</tbody>
		<tfoot>

			<?php if ( $price_display ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo esc_html__( 'Subtotal( Standard)', 'addify_rfq' ); ?>:</th>
				<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo wp_kses_post( wc_price( $quote_subtotal ) ); ?>
				</td>
			</tr>
			<?php endif; ?>

			<?php if ( $of_price_display ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo esc_html__( 'Subtotal (Offered)', 'addify_rfq' ); ?>:</th>
				<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo wp_kses_post( wc_price( $offered_total ) ); ?>
				</td>
			</tr>
			<?php endif; ?>

			<?php if ( wc_tax_enabled() && $tax_display ) : ?>
				<tr>
					<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
						<?php echo esc_html__( 'Vat (Standard)', 'addify_rfq' ); ?>:</th>
					<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
						<?php echo wp_kses_post( wc_price( $vat_total ) ); ?>
					</td>
				</tr>
			<?php endif; ?>

			<?php if ( $price_display ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo esc_html__( 'Total (Standard)', 'addify_rfq' ); ?>:</th>
				<td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo wp_kses_post( wc_price( $quote_total ) ); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tfoot>
	</table>
</div>
