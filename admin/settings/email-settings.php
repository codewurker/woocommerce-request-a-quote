<?php
/**
 * Addify Add to Quote
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

$emails = array(
	'af_admin'      => __( 'Admin (New Quote)', 'addify_rfq' ),
	'af_pending'    => __( 'Pending/New Quote', 'addify_rfq' ),
	'af_in_process' => __( 'In Process', 'addify_rfq' ),
	'af_accepted'   => __( 'Accepted', 'addify_rfq' ),
	'af_admin_conv' => __( 'Converted to Order(Admin)', 'addify_rfq' ),
	'af_converted'  => __( 'Converted to Order(Customer)', 'addify_rfq' ),
	'af_declined'   => __( 'Declined/Products not available', 'addify_rfq' ),
	'af_cancelled'  => __( 'Cancelled/Rejected', 'addify_rfq' ),
);

$fields = array(
	'enable'  => __( 'Enable/Disable Email', 'addify_rfq' ),
	'subject' => __( 'Subject of Email', 'addify_rfq' ),
	'heading' => __( 'Heading of Email', 'addify_rfq' ),
	'message' => __( 'Additional Message', 'addify_rfq' ),
);

if ( ! is_array( get_option( 'afrfq_emails' ) ) ) {
	$email_values = (array) json_decode( get_option( 'afrfq_emails' ), true );
} else {
	$email_values = (array) get_option( 'afrfq_emails' );
}

?>
<div class="addify-singletab" id="tabs-4">
	<p><?php echo esc_html__( 'Email Settings for each status of quote. Messages will be display before quote table in emails.', 'addify_rfq' ); ?></p>
	<div class="afrfq_accordian">
		<?php

		foreach ( $emails as $key => $value ) :
			?>
			<div class="accordion">
				<h3><?php echo esc_html( $value ); ?></h3>
				<div class="content">
					<p>
						<table class="addify-table-optoin">
							<tbody>
								<?php
								foreach ( $fields as $name => $label ) :

									$field_value = isset( $email_values[ $key ][ $name ] ) ? $email_values[ $key ][ $name ] : '';

									?>
									<tr class="addify-option-field">
										<th>
											<div class="option-head">
												<h4><?php echo esc_html( $label ); ?></h4>
											</div>
										</th>
										<td>
											<?php if ( 'message' === $name ) : ?>
												<div class="afrfq_textarea">
													<?php
													$content     = wpautop( wptexturize( $field_value ) );
													$editor_name = 'afrfq_emails[' . $key . '][' . $name . ']';
													$editor_id   = 'afrfq_emails-' . $key . '-' . $name;
													$settings    = array(
														// Disable autop if the current post has blocks in it.
														'wpautop'             => false,
														'media_buttons'       => false,
														'default_editor'      => '',
														'drag_drop_upload'    => false,
														'textarea_name'       => $editor_name,
														'textarea_rows'       => 5,
														'tabindex'            => '',
														'tabfocus_elements'   => ':prev,:next',
														'editor_css'          => '',
														'editor_class'        => '',
														'teeny'               => false,
														'_content_editor_dfw' => false,
														'tinymce'             => true,
														'quicktags'           => true,
													);

													wp_editor( $content, $editor_id, $settings );
													?>
												</div>
											<?php endif; ?>

											<?php if ( 'enable' === $name ) : ?>
												<input value="yes" class="afrfq_input_class" type="checkbox" name="afrfq_emails[<?php echo esc_html( $key ); ?>][<?php echo esc_html( $name ); ?>]" <?php echo checked( 'yes', $field_value ); ?> />
											<?php endif; ?>

											<?php if ( 'heading' === $name || 'subject' === $name ) : ?>
												<input value="<?php echo esc_attr( $field_value ); ?>" class="afrfq_input_class" type="text" name="afrfq_emails[<?php echo esc_html( $key ); ?>][<?php echo esc_html( $name ); ?>]" id="" />
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
