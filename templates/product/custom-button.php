<?php
/**
 * Simple product add to quote
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/product/custom-button.php.
 *
 * @package WooCommerce\Templates\Emails
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

if ( ! isset( $afrfq_custom_button_text ) || empty( $afrfq_custom_button_text ) ) {
	$afrfq_custom_button_text = __( 'Add to Quote', 'addify_rfq' );
}

$pageurl = get_page_link( get_option( 'addify_atq_page_id', true ) );

if ( ! $product->is_in_stock() && 'yes' !== get_option( 'enable_o_o_s_products' ) ) {

	echo wp_kses_post( wc_get_stock_html( $product ) ); // phpcs:ignore WordPress.Security.EscapeOutput.

} else {

	do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );

		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);

		do_action( 'woocommerce_after_add_to_cart_quantity' );

		$pageurl = get_page_link( get_option( 'addify_atq_page_id', true ) );

		echo '<a href="' . esc_url( $afrfq_custom_button_link ) . '" rel="nofollow" class="button product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
		do_action( 'addify_after_add_to_quote_custom_button' );
		do_action( 'woocommerce_after_add_to_cart_button' );

		?>
		
	</form>

	<?php
	do_action( 'woocommerce_after_add_to_cart_form' );
}
