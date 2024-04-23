<?php
/**
 * Simple product (out of stock) add to quote
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/product/simple-out-of-stock.php.
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

if ( $product->is_in_stock() || 'yes' === get_option( 'enable_o_o_s_products' ) ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );

		woocommerce_quantity_input(
			array(
				'min_value'   => 0,
				'max_value'   => '',
				'input_value' => 1, // WPCS: CSRF ok, input var ok.
				'inputmode'   => 'numeric',
			),
			$product,
			true
		);

		do_action( 'woocommerce_after_add_to_cart_quantity' );

		$pageurl = get_page_link( get_option( 'addify_atq_page_id', true ) );

		echo '<a href="javascript:void(0)" rel="nofollow" data-product_id="' . intval( $product->get_ID() ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" class="afrfqbt_single_page button alt single_add_to_cart_button product_type_' . esc_attr( $product->get_type() ) . '">' . esc_attr( $afrfq_custom_button_text ) . '</a>';
		?>
		
	</form>

	<?php
	do_action( 'addify_after_add_to_quote_button' );
	do_action( 'woocommerce_after_add_to_cart_form' );
	?>

<?php endif; ?>
