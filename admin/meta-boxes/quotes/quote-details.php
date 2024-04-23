<?php
/**
 * Quote details in Meta box.
 *
 * It shows the details of quotes items in meta box.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;

$quote_contents = get_post_meta( $post->ID, 'quote_contents', true );
$quote_status   = get_post_meta( $post->ID, 'quote_status', true );
$user_id        = get_post_meta( $post->ID, '_customer_user', true );

$order_id_for_this_quote = get_post_meta( $post->ID, 'order_for_this_quote', true );
$af_quote                = new AF_R_F_Q_Quote( $quote_contents );

$quote_totals = $af_quote->get_calculated_totals( (array) $quote_contents, $post->ID );

$quote_id = $post->ID;

?>
<div class="woocommerce_order_items_wrapper wc-order-items-editable addify_quote_items_wrapper">
	<?php
	do_action( 'addify_rfq_order_details_before_order_table', $post );

	require AFRFQ_PLUGIN_DIR . 'admin/meta-boxes/quotes/quote-details-table.php';

	do_action( 'addify_rfq_order_details_after_order_table', $post );
	?>
	<!-- The Modal -->
	<div id="af-backbone-add-product-modal" class="af-backbone-modal">
		<!-- Modal content -->
			<div class="af-backbone-modal-content">
			<section class="af-backbone-modal-main" role="main">
				<header class="af-backbone-modal-header">
					<h1><?php echo esc_html__( 'Add product', 'addify_rfq' ); ?></h1>
						<span class="af-backbone-close">&times;</span>
				</header>
				<article style="max-height: 316.5px;">
					<form action="" method="post">
						<table class="widefat">
							<thead>
								<tr>
									<th><?php echo esc_html__( 'Product', 'addify_rfq' ); ?></th>
									<th><?php echo esc_html__( 'Quantity', 'addify_rfq' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<td>
									<select class="af-single_select-product">	
									</select>
								</td>
								<td>
									<input type="number" min='1' name="afacr_product_quantity" value="1">
								</td>
							</tbody>
						</table>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" value="<?php echo intval( $post->ID ); ?>" class="button button-primary button-large">
							<?php echo esc_html__( 'Add to Quote', 'addify_rfq' ); ?>
						</button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="addify_converty_to_order_button">
		<div class="left-buttons">
			<button type="button" id="addify_add_item" name="addify_add_item" class="button add-product" >
				<?php echo esc_html__( 'Add product(s)', 'addify_rfq' ); ?>
			</button>
		</div>
		<?php if ( 'af_converted' !== $quote_status ) : ?>
			<div class="right-buttons">
				<button type="submit" name="addify_convert_to_order" class="button button-primary button-large" >
					<?php echo esc_html__( 'Convert to Order', 'addify_rfq' ); ?>
				</button>
			</div>
		<?php else : ?>
			<div class="right-buttons" style="text-align: right;">
				
					<?php if ( ! empty( $order_id_for_this_quote ) ) : ?>

						<b><?php echo esc_html__( 'This quote has been converted to ', 'addify_rfq' ) . '<a href="admin.php?page=wc-orders&action=edit&id=' . intval( $order_id_for_this_quote ) . '"> order # ' . intval( $order_id_for_this_quote ); ?></a></b>

					<?php endif; ?>
				
			</div>
		<?php endif; ?>
	</div>
</div>
