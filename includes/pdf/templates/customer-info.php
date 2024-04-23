<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/pdf/customer-info.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'addify_rfq_before_pdf_customer_details' );

?>
<h3><?php echo esc_html__( 'Customer Information', 'addify_rfq' ); ?></h3>
<table width="100%" cellspacing="0" cellpadding="10" border="0">
	<tbody>
		<?php foreach ( $customer_info as $key => $info ) : ?>
			<tr>
				<th width="30%">
					<?php /* translators: %s: Label */ ?>
					<?php printf( esc_html__( '%s:', 'addify_rfq' ), esc_html( $info['label'] ) ); ?>
				</th>
				<td>
					<?php /* translators: %s: Value */ ?>
					<?php echo esc_html( $info['value'] ); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

