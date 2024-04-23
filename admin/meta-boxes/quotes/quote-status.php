<?php
/**
 * Addify Add to Quote.
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
$quote_status   = get_post_meta( $post->ID, 'quote_status', true );
$quote_statuses = array(
	'af_pending'    => __( 'Pending', 'addify_rfq' ),
	'af_in_process' => __( 'In Process', 'addify_rfq' ),
	'af_accepted'   => __( 'Accepted', 'addify_rfq' ),
	'af_converted'  => __( 'Converted to Order', 'addify_rfq' ),
	'af_declined'   => __( 'Declined', 'addify_rfq' ),
	'af_cancelled'  => __( 'Cancelled', 'addify_rfq' ),
);

?>

<p class="post-attributes-label-wrapper quote-status-label-wrapper">
	<label class="post-attributes-label" for="quote_status">
		<?php echo esc_html__( 'Current Status', 'addify_rfq' ); ?>
	</label>
</p>
<select name="quote_status" id="quote_status">

	<?php foreach ( $quote_statuses as $value => $label ) : ?>
		<option value="<?php echo esc_html( $value ); ?>" <?php echo selected( $value, $quote_status ); ?> >
			<?php echo esc_html( $label ); ?>
		</option>
	<?php endforeach; ?>

</select> 

<p class="post-attributes-label-wrapper afrfq-label-wrapper">
	<label class="post-attributes-label" for="quote_status">
		<?php esc_html_e( 'Notify Customer', 'addify_rfq' ); ?>
	</label>
</p>
	<select name="afrfq_notify_customer" id="afrfq_notify_customer" >
		<option value="no"><?php esc_html_e( 'No', 'addify_rfq' ); ?></option>
		<option value="yes"><?php esc_html_e( 'Yes', 'addify_rfq' ); ?></option>
	</select>
<p class="desciption">
	<?php esc_html_e( 'Select "Yes" to notify customer via email.', 'addify_rfq' ); ?>
</p>
<button type="button" id="af_rfq_download_pdf_with_qoute_id_admin_qoute_attributes"  name="af_rfq_download_pdf_with_qoute_id_admin_qoute_attributes" value="<?php echo esc_attr( $post->ID ); ?>" class="woocommerce-button fas fa-file-pdf af_rfq_download_pdf_with_qoute_id_admin_qoute_attributes"><?php esc_html_e( 'download pdf', 'addify_rfq' ); ?></button>