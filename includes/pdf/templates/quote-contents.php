<?php
/**
 * Addify quote-contents-table for pdf print.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/pdf/quote-contents.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'addify_rfq_before_pdf_quote_contents' );

$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) ? true : false;
$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) ? true : false;
$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) ? true : false;

$colspan  = 2;
$colspan += $price_display ? 1 : 0;
$colspan += $of_price_display ? 1 : 0;

$total_quanity = 0;
?>
<h3><?php echo esc_html__( 'Quote Contents', 'addify_rfq' ); ?></h3>
<table style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif" cellspacing="0" cellpadding="6" border="1">
	<thead>
		<tr>
			<th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
				<strong><?php echo esc_html__( 'Product', 'addify_rfq' ); ?></strong>
			</th>
			<th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
				<strong><?php echo esc_html__( 'SKU', 'addify_rfq' ); ?></strong>
			</th>
			<th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
				<strong><?php echo esc_html__( 'Quantity', 'addify_rfq' ); ?></strong>
			</th>
			<?php if ( $price_display ) : ?>
				<th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
					<strong><?php echo esc_html__( 'Price', 'addify_rfq' ); ?></strong>
				</th>
			<?php endif; ?>
			<?php if ( $of_price_display ) : ?>
				<th scope="col" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left">
					<strong><?php echo esc_html__( 'Offered Price', 'addify_rfq' ); ?></strong>
				</th>
			<?php endif; ?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( (array) $quote_contents as $key => $item ) :

			if ( ! isset( $item['data'] ) || ! is_object( $item['data'] ) ) {
				continue;
			}

			$product        = isset( $item['data'] ) ? $item['data'] : '';
			$total_quanity += $item['quantity'];
			$price          = empty( $item['addons_price'] ) ? $product->get_price() : $item['addons_price'];
			$offered_price  = isset( $item['offered_price'] ) ? floatval( $item['offered_price'] ) : $price;
			?>
			<tr>
				<td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">
					<?php
					echo esc_html( $product->get_name() );
					echo wp_kses_post( wc_get_formatted_cart_item_data( $item ) );
					?>
										
				</td>
				<td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word">
					<?php echo esc_html( $product->get_sku() ); ?>
				</td>
				<td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
				<?php echo esc_attr( $item['quantity'] ); ?>
				</td>
				<?php if ( $price_display ) : ?>
					<td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
						<?php echo wp_kses_post( wc_price( $price ) ); ?>
					</td>
				<?php endif; ?>
				<?php if ( $of_price_display ) : ?>
					<td style="color:#000000;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
						<?php echo wp_kses_post( wc_price( $offered_price ) ); ?>
					</td>
				<?php endif; ?>
				
			</tr>
			<?php endforeach; ?>
	</tbody>
	<tfoot>
		<?php if ( $price_display ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<strong><?php echo esc_html__( 'Subtotal(standard)', 'addify_rfq' ); ?>:</strong>
				</th>
				<td style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo wp_kses_post( wc_price( $quote_subtotal ) ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( $of_price_display ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<strong><?php echo esc_html__( 'Subtotal(offered)', 'addify_rfq' ); ?>:</strong>
				</th>
				<td style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo wp_kses_post( wc_price( $offered_subtotal ) ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( wc_tax_enabled() && $tax_display ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<strong><?php echo esc_html__( 'Vat(standard)', 'addify_rfq' ); ?>:</strong>
				</th>
				<td style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo wp_kses_post( wc_price( $vat_total ) ); ?>
				</td>
			</tr>
		<?php endif; ?>

		<?php if ( $price_display ) : ?>
			<tr>
				<th scope="row" colspan="<?php echo intval( $colspan ); ?>" style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<strong><?php echo esc_html__( 'Total(standard)', 'addify_rfq' ); ?>:</strong>
				</th>
				<td style="color:#000000;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left;border-top-width:4px">
					<?php echo wp_kses_post( wc_price( $quote_total ) ); ?>
				</td>
			</tr>
		<?php endif; ?>
	</tfoot>
</table>
