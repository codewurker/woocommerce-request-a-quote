<?php
/**
 * Quote details in my Account.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/my-account/quote-list-table.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $customer_quotes ) ) {


	?>
	<table class="shop_table shop_table_responsive cart my_account_orders my_account_quotes">
		<thead>
			<tr>
				<th data-title=""><?php echo esc_html__( 'Quote', 'addify_rfq' ); ?></th>
				<th><?php echo esc_html__( 'Status', 'addify_rfq' ); ?></th>
				<th><?php echo esc_html__( 'Date', 'addify_rfq' ); ?></th>
				<th><?php echo esc_html__( 'Action', 'addify_rfq' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php

			foreach ( $customer_quotes as $quote ) {

				$quote_status = get_post_meta( $quote->ID, 'quote_status', true );
				?>
				<tr>
					<td data-title="ID">
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'request-quote', $quote->ID ) ); ?>">
							<?php echo esc_html__( '#', 'addify_rfq' ) . intval( $quote->ID ); ?>
						</a>
					</td>
					<td data-title="Status">
						<?php echo isset( $statuses[ $quote_status ] ) ? esc_html( $statuses[ $quote_status ] ) : 'Pending'; ?>
					</td>
					<td data-title="Date">
						<time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', strtotime( $quote->post_date ) ) ); ?>" title="<?php echo esc_attr( strtotime( $quote->post_date ) ); ?>"><?php echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $quote->post_date ) ) ); ?></time>
					</td>							
					<td data-title="Action">
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'request-quote', $quote->ID ) ); ?>" class="woocommerce-button button view">
							<?php echo esc_html__( 'View', 'addify_rfq' ); ?>
						</a>

					<?php
					if ('yes' !== get_option( 'afrfq_disable_for_customers' )) {
						?>
   
					<button type="button" id="af_rfq_get_qoute_by_my_account_pdf"  name="af_rfq_get_qoute_by_my_account_pdf" data-qoutepdfid="<?php echo esc_attr( $quote->ID ); ?>" value="<?php echo esc_attr( $quote->ID ); ?>" class="woocommerce-button fas fa-file-pdf af_rfq_get_qoute_by_my_account_pdf"></button>

				<?php } ?>

					</button>
						<?php
						$afrfq_enable = 'yes' === get_option( 'afrfq_enable_convert_order' ) ? true : false;

						if ( in_array( $quote_status, array( 'af_accepted', 'af_in_process' ), true ) && $afrfq_enable ) :
							?>
							<form class="quote-convert-to-order" method="post">
								<?php wp_nonce_field( '_afrfq__wpnonce', '_afrfq__wpnonce' ); ?>
								<button type="submit" value="<?php echo intval( $quote->ID ); ?>" name="addify_convert_to_order_customer" id="addify_convert_to_order_customer" class="button button-primary button-large"><?php echo esc_html__( 'Convert to Order', 'addify_rfq' ); ?></button>
							</form>
						<?php endif; ?>
						
					</td>
				</tr>
						<?php } ?>
		</tbody>
	</table>

<?php } else { ?>

	<div class="woocommerce-MyAccount-content">
		<div class="woocommerce-notices-wrapper"></div>
		<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
			<a class="woocommerce-Button button" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php echo esc_html__( 'Go to shop', 'addify_rfq' ); ?></a><?php echo esc_html__( 'No quote has been made yet.', 'addify_rfq' ); ?></div>
	</div>
	<?php
}

