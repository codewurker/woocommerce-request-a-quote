<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/emails/customer-info.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'addify_rfq_before_email_customer_details' );

?>

<table width="100%" cellspacing="0" cellpadding="10" border="0">
	<tbody>
		<?php
		foreach ( $customer_info as $key => $info ) :

			?>
			<tr>
				<th width="30%">
					<?php /* translators: %s: Label */ ?>
					<?php printf( esc_html__( '%s:', 'addify_rfq' ), esc_html( $info['label'] ) ); ?>
				</th>
				<td>
					<?php /* translators: %s: Value */ ?>
					<?php echo wp_kses_post( $info['value'] ); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
