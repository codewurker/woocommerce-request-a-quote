<?php
/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/pdf/pdf-header.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div>

<table width="100%" cellspacing="0" cellpadding="0" border="0" style="text-align: left; vertical-align: bottom; ">
	<tbody>
		<tr>
			<td width="40%">
				<img src="<?php echo esc_url( $image_file ); ?>" width="50">
			</td>
			<td style="text-align: left; vertical-align: bottom; ">
			</td>
		</tr>
		<tr>
			<td width="40%">
				<h4><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h4>
				<p><?php echo esc_html( get_bloginfo( 'description' ) ); ?> </p>
			</td>
			<td style="text-align: left; vertical-align: bottom; ">
				<h2>
				<?php
				echo esc_html__( 'Quotation #:', 'addify_rfq' );
				echo esc_html( $quote_id );
				?>
				</h2>
			</td>
		</tr>

	</tbody>
</table>
<br>
<hr>
</div>
