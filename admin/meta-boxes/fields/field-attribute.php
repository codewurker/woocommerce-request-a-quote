<?php
/**
 * Field Attributes.
 *
 * Deal field attributes in metabox .
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
$field_id = $post->ID;

$field_types = array(
	'text'        => __( 'Text', 'addify_rfq' ),
	'email'       => __( 'Email', 'addify_rfq' ),
	'number'      => __( 'Number', 'addify_rfq' ),
	'file'        => __( 'File', 'addify_rfq' ),
	'time'        => __( 'Time', 'addify_rfq' ),
	'date'        => __( 'Date', 'addify_rfq' ),
	'datetime'    => __( 'DateTime', 'addify_rfq' ),
	'textarea'    => __( 'Textarea', 'addify_rfq' ),
	'select'      => __( 'Select (Dropdown)', 'addify_rfq' ),
	'multiselect' => __( 'Multi Select', 'addify_rfq' ),
	'radio'       => __( 'Radio', 'addify_rfq' ),
	'checkbox'    => __( 'Checkbox', 'addify_rfq' ),
	'terms_cond'  => __( 'Terms & Conditions', 'addify_rfq' ),
);

$default_values = array(
	''                    => 'Select a default value',
	'user_login'          => __( 'Username', 'addify_rfq' ),
	'first_name'          => __( 'First Name', 'addify_rfq' ),
	'last_name'           => __( 'Last Name', 'addify_rfq' ),
	'nickname'            => __( 'Nickname', 'addify_rfq' ),
	'display_name'        => __( 'Display Name', 'addify_rfq' ),
	'email'               => __( 'Email', 'addify_rfq' ),
	'billing_first_name'  => __( 'Billing First Name', 'addify_rfq' ),
	'billing_last_name'   => __( 'Billing Last Name', 'addify_rfq' ),
	'billing_company'     => __( 'Billing Company', 'addify_rfq' ),
	'billing_address_1'   => __( 'Billing Address 1', 'addify_rfq' ),
	'billing_address_2'   => __( 'Billing Address 2', 'addify_rfq' ),
	'billing_city'        => __( 'Billing City', 'addify_rfq' ),
	'billing_postcode'    => __( 'Billing Postcode', 'addify_rfq' ),
	'billing_phone'       => __( 'Billing Phone', 'addify_rfq' ),
	'billing_email'       => __( 'Billing Email', 'addify_rfq' ),
	'shipping_first_name' => __( 'Shipping First Name', 'addify_rfq' ),
	'shipping_last_name'  => __( 'Shipping Last Name', 'addify_rfq' ),
	'shipping_company'    => __( 'Shipping Company', 'addify_rfq' ),
	'shipping_address_1'  => __( 'Shipping Address 1', 'addify_rfq' ),
	'shipping_address_2'  => __( 'Shipping Address 2', 'addify_rfq' ),
	'shipping_city'       => __( 'Shipping City', 'addify_rfq' ),
	'shipping_postcode'   => __( 'Shipping Postcode', 'addify_rfq' ),
	'shipping_phone'      => __( 'Shipping Phone', 'addify_rfq' ),
	'shipping_email'      => __( 'Shipping Email', 'addify_rfq' ),
);

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
$afrfq_field_terms       = get_post_meta( $field_id, 'afrfq_field_terms', true );


if ( empty( $afrfq_field_name ) ) {
	$afrfq_field_name = 'afrfq_field_' . $field_id;
	$readonly         = '';
} else {
	$readonly = 'readonly';
}

?>
<div class="afrfq-metabox-fields">
	<?php wp_nonce_field( 'afrfq_fields_nonce_action', 'afrfq_field_nonce' ); ?>
	<table class="addify-table-optoin">
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Field Name', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<input type="text" name="afrfq_field_name" value="<?php echo esc_html( $afrfq_field_name ); ?>" required <?php echo esc_html( $readonly ); ?> >
				<p class="description"><?php echo esc_html__( 'Add a unique name for each quote field. It is also used as meta_key to store values in database. Once publish, you will not be able to modify it.', 'addify_rfq' ); ?></p>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Field Type', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<select id="afrfq_field_type" name="afrfq_field_type" required>
					<?php foreach ( $field_types as $value => $label ) : ?>
						<option value="<?php echo esc_html( $value ); ?>" <?php echo selected( $value, $afrfq_field_type ); ?> > <?php echo esc_html( $label ); ?> </option>
					<?php endforeach; ?>
				</select>
				<p class="description"></p>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Field label', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<input type="text" name="afrfq_field_label" value="<?php echo esc_html( $afrfq_field_label ); ?>">
				<p class="description"></p>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Terms & Conditions', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<textarea rows="5" id="afrfq_field_terms" name="afrfq_field_terms"><?php echo esc_html( $afrfq_field_terms ); ?></textarea>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Field Default Value', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<select name="afrfq_field_value" >
					<?php foreach ( $default_values as $value => $label ) : ?>
						<option value="<?php echo esc_html( $value ); ?>" <?php echo selected( $value, $afrfq_field_value ); ?> > <?php echo esc_html( $label ); ?> </option>
					<?php endforeach; ?>
				</select>
				<p class="description"></p>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Field Placeholder', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<input type="text" name="afrfq_field_placeholder" value="<?php echo esc_html( $afrfq_field_placeholder ); ?>" >
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Allowed File Types', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<input type="text" name="afrfq_file_types" value="<?php echo esc_html( $afrfq_file_types ); ?>" >
				<p class="description"><?php echo esc_html__( 'Add Comma separated file extensions. Ex. pdf,txt,jpg.', 'addify_rfq' ); ?></p>
			</td>
		</tr>
		<tr class="addify-option-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Allowed File Size', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<input type="number" name="afrfq_file_size" value="<?php echo esc_html( $afrfq_file_size ); ?>" >
				<p class="description"><?php echo esc_html__( 'File size in bytes 1KB = 1000 bytes and 1MB = 1000000 bytes', 'addify_rfq' ); ?></p>
			</td>
		</tr>
		<tr class="addify-option-field options-field">
			<th>
				<div class="option-head">
					<h3>
						<?php echo esc_html__( 'Field Options', 'addify_rfq' ); ?>
					</h3>
				</div>
			</th>
			<td>
				<?php
				if ( 0 === count( $afrfq_field_options ) ) {
					$afrfq_field_options = array( '' );
				}
				foreach ( $afrfq_field_options as $value ) :
					?>
					<div class="option_row">
						<input type="text" name="afrfq_field_options[]" value="<?php echo esc_html( $value ); ?>" >
						<span type="button" title="Add Option" class="dashicons dashicons-plus-alt2 add_option_button" value="add_more"></span>
						<span type="button" title="Remove Option" class="dashicons dashicons-no-alt remove_option_button" value="add_more"></span>
					</div>
				<?php endforeach; ?>
				<p class="description"><?php echo esc_html__( 'Add Option(s) for fields types ( Select, Multi-Select and Radio ).', 'addify_rfq' ); ?></p>
			</td>
		</tr>
	</table>
</div>
