<?php
/**
 * Mini-cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/my-account/quote-details-my-account.php.
 *
 * Table to display the old quotes items data (older versions before 1.6.0).
 *
 *  @package WooCommerce\Templates\Emails
 *  @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

$quotedataname = get_post_meta( $afrfq_id, 'quote_proname', true );
$quotedataid   = get_post_meta( $afrfq_id, 'quote_proid', true );
$quotedataqty  = get_post_meta( $afrfq_id, 'qty', true );
$woo_addons    = get_post_meta( $afrfq_id, 'woo_addons', true );
$woo_addons1   = get_post_meta( $afrfq_id, 'woo_addons1', true );
$variationall  = get_post_meta( $afrfq_id, 'variation', true );

?>
<h2 class="entry-title"><?php echo esc_html__( 'Quote ', 'addify_rfq' ); ?><?php echo esc_html__( '#', 'addify_rfq' ) . intval( $afrfq_id ); ?></h2>

<p><?php echo esc_html__( 'Quote ', 'addify_rfq' ); ?> <?php echo esc_html__( '#', 'addify_rfq' ); ?><mark class="order-number"><?php echo intval( $afrfq_id ); ?></mark> <?php echo esc_html__( ' was placed on ', 'addify_rfq' ); ?> <mark class="order-date"><time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $quote->post_date ) ) ); ?>" title="<?php echo esc_attr( strtotime( $quote->post_date ) ); ?>"><?php echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $quote->post_date ) ) ); ?></time></mark>.</p>

<h2><?php echo esc_html__( 'Quote Details', 'addify_rfq' ); ?></h2>

<table class="shop_table order_details">
	<thead>
	<tr>
		<th></th>
		<th class="product-name"><?php echo esc_html__( 'Product', 'addify_rfq' ); ?></th>
		<th class="product-sku"><?php echo esc_html__( 'SKU', 'addify_rfq' ); ?></th>
		<th class="product-total"><?php echo esc_html__( 'Quantity', 'addify_rfq' ); ?></th>
	</tr>
	</thead>
	<tbody>
		<?php
		$total_products = count( $quotedataid );
		for ( $i = 0; $i < $total_products; $i++ ) {

			$product       = wc_get_product( $quotedataid[ $i ] );
			$image         = get_the_post_thumbnail_url( $quotedataid[ $i ] );
			$product_title = $product->get_title();
			$product_url   = $product->get_permalink();
			$product_sku   = $product->get_sku();

			$p_title = $product_title;



			if ( ! empty( $woo_addons ) ) {

				$w_add = $woo_addons[ $i ];
			} else {
				$w_add = '';
			}

			if ( ! empty( $woo_addons1 ) ) {

				$w_add1 = $woo_addons1[ $i ];
			} else {
				$w_add1 = '';
			}


			if ( ! empty( $variationall ) ) {

				$variation = $variationall[ $i ];
			} else {
				$variation = array();
			}

			$postvalue = unserialize( base64_decode( $variation ) );



			$item_data = array();
			$var_data  = array();
			$str       = '?';
			if ( ! empty( $postvalue ) ) {

				foreach ( $postvalue as $name => $value ) {
					$taxonomy1           = str_replace( 'attribute_', '', $value[0] );
					$label               = wc_attribute_label( $taxonomy1, $product );
					$item_data[ $label ] = $value[1];
					if ( '?' != $str ) {
						$str .= '&';
					}
					$str       .= $value[0] . '=' . $value[1];
					$var_data[] = ucfirst( $value[1] );
					?>

					<?php
				}
			}


			?>

			<tr>
				<td><a href="<?php echo esc_url( $product_url ); ?>" target="_blank"><img src="<?php echo esc_url( $image ); ?>" width="50"></a></td>
				<td class="product-name"><a href="<?php echo esc_url( $product_url ); ?>" target="_blank"><b><?php echo esc_attr( $p_title ); ?></b></a>

					<div class="woo_options">

						<dl class="variation">
						<?php foreach ( $item_data as $key => $data ) : ?>
								<dt class="<?php echo sanitize_html_class( 'variation-' . $key ); ?>"><?php echo wp_kses_post( ucfirst( $key ) ); ?>:</dt>
								<dd class="<?php echo sanitize_html_class( 'variation-' . $key ); ?>"><?php echo wp_kses_post( wpautop( $data ) ); ?></dd>
						<?php endforeach; ?>
						</dl>
					</div>

				</td>
				<td class="product-sku"><b><?php echo esc_attr( $product_sku ); ?></b></td>
				<td class="product-total"><b><?php echo esc_attr( $quotedataqty[ $i ] ); ?></b></td>
			</tr>

		<?php } ?>
	</tbody>

</table>
