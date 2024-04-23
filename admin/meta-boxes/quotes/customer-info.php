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
global $post;
$user_id = get_post_meta( $post->ID, '_customer_user', true );

$user_name = 'Guest';
$id_user   = 'guest';

$user = get_user_by( 'id', intval( $user_id ) );

if ( ! empty( $user_id ) && is_object( $user ) ) {

	$id_user   = $user_id;
	$user_name = $user->display_name . '(' . $user->user_email . ')';
}

?>
<div class="afacr-metabox-fields">
	
	<?php wp_nonce_field( 'afrfq_fields_nonce_action', 'afrfq_field_nonce' ); ?>
	
	<table class="addify-table-optoin">

		<?php if ( ! empty( $post->ID ) ) : ?>
			<tr class="addify-option-field">
				<th>
					<div class="option-head">
						<h3>
							<?php echo esc_html__( 'Quote #:', 'addify_rfq' ); ?>
						</h3>
					</div>
				</th>
				<td>
					<p><?php echo esc_attr( $post->ID ); ?></p>
				</td>
			</tr>
			<tr class="addify-option-field">
				<th>
					<div class="option-head">
						<h3>
							<?php echo esc_html__( 'Quote user:', 'addify_rfq' ); ?>
						</h3>
					</div>
				</th>
				<td>
					<select name="_customer_user" class="users ajax_customer_search">
						<option value="<?php echo esc_html( $id_user ); ?>"><?php echo esc_html( $user_name ); ?></option>
					</select>
				</td>
			</tr>
		<?php endif; ?>

		<?php
		foreach ( $quote_fields as $key => $field ) :
			$field_id = $field->ID;

			$afrfq_field_name        = get_post_meta( $field_id, 'afrfq_field_name', true );
			$afrfq_field_type        = get_post_meta( $field_id, 'afrfq_field_type', true );
			$afrfq_field_label       = get_post_meta( $field_id, 'afrfq_field_label', true );
			$afrfq_field_value       = get_post_meta( $field_id, 'afrfq_field_value', true );
			$afrfq_field_title       = get_post_meta( $field_id, 'afrfq_field_title', true );
			$afrfq_field_placeholder = get_post_meta( $field_id, 'afrfq_field_placeholder', true );
			$afrfq_field_options     = (array) get_post_meta( $field_id, 'afrfq_field_options', true );
			$afrfq_file_types        = get_post_meta( $field_id, 'afrfq_file_types', true );
			$afrfq_file_size         = get_post_meta( $field_id, 'afrfq_file_size', true );
			$afrfq_field_enable      = get_post_meta( $field_id, 'afrfq_field_enable', true );
			$field_data              = get_post_meta( $post->ID, $afrfq_field_name, true );

			// Get default value if set.
			if ( empty( $field_data ) && ! empty( $afrfq_field_value ) ) {
				$field_data = $quote_fields_obj->get_field_default_value( $field_id, $user_id );
			}


			?>
			<tr class="addify-option-field">
				<th>
					<div class="option-head">
						<h3>
							<?php echo esc_html( $afrfq_field_label ); ?>
						</h3>
					</div>
				</th>
				<td>
					<?php
					switch ( $afrfq_field_type ) {
						case 'terms_cond':
							echo esc_html( ucfirst( $field_data ) );

							break;
						case 'text':
							?>
									<input type="text" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>">
								<?php
							break;
						case 'time':
							?>
									<input type="time" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>">
								<?php
							break;
						case 'date':
							?>
									<input type="date" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>">
								<?php
							break;
						case 'datetime':
							?>
									<input type="datetime-local" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>">
								<?php
							break;
						case 'email':
							?>
									<input type="email" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>">
								<?php
							break;
						case 'number':
							?>
									<input type="number" placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $field_data ); ?>">
								<?php
							break;
						case 'file':
							if ( ! empty( $field_data ) && file_exists( AFRFQ_UPLOAD_DIR . $field_data ) ) {
								?>
									<iframe allowfullscreen="true" src="<?php echo esc_url( AFRFQ_UPLOAD_URL . $field_data ); ?>"></iframe>
								<?php
							} else {
								?>
									<p><?php echo esc_html__( 'No file was uploaded by user.', 'addify_rfq' ); ?></p>
								<?php
							}
							break;
						case 'textarea':
							?>
								<textarea name="<?php echo esc_html( $afrfq_field_name ); ?>"><?php echo esc_html( $field_data ); ?></textarea>
								<?php
							break;
						case 'select':
							?>
									<select name="<?php echo esc_html( $afrfq_field_name ); ?>">
									<?php
									foreach ( (array) $afrfq_field_options as $option ) :
										$value = strtolower( trim( $option ) );
										?>
											<option value="<?php echo esc_html( $value ); ?>" <?php echo selected( $value, $field_data ); ?> ><?php echo esc_html( $option ); ?></option>
										<?php endforeach; ?>
									</select>
								<?php
							break;
						case 'multiselect':
							?>
									<select placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" class="multi-select" name="<?php echo esc_html( $afrfq_field_name ); ?>[]" multiple>
									<?php
									foreach ( (array) $afrfq_field_options as $option ) :
										$value = strtolower( trim( $option ) );
										?>
											<option value="<?php echo esc_html( $value ); ?>" <?php echo in_array( $value, (array) $field_data, true ) ? 'selected' : ''; ?> > <?php echo esc_html( $option ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								<?php
							break;
						case 'radio':
							foreach ( (array) $afrfq_field_options as $option ) :
								$value = strtolower( trim( $option ) );
								?>
									<input placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" type="radio" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="<?php echo esc_html( $value ); ?>" <?php echo checked( $value, $field_data ); ?> ><?php echo esc_html( $option ); ?>
									<br>
								<?php
								endforeach;
							break;
						case 'checkbox':
							?>
									<input placeholder="<?php echo esc_html( $afrfq_field_placeholder ); ?>" type="checkbox" name="<?php echo esc_html( $afrfq_field_name ); ?>" value="yes" <?php echo checked( 'yes', $field_data ); ?> >
								<?php
							break;
					}
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
