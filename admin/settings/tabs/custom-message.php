<?php
/**
 * Custom message tab fields.
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-message-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Custom Messages', 'addify_rfq' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_messages_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_success_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Quote Submitted successfully Message', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_success_message_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_messages_section',                          // The page on which this option will be displayed.
	'afrfq-message-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'This message will appear on quote submission page, when user submit quote.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_messages_fields',
	'afrfq_success_message'
);

add_settings_field(
	'afrfq_view_button_message',                      // ID used to identify the field throughout the theme.
	esc_html__( 'View Quote Basket Button Text', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_view_button_message_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_messages_section',                          // The page on which this option will be displayed.
	'afrfq-message-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'This text will be shown for view quote basket button.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_messages_fields',
	'afrfq_view_button_message'
);

if ( ! function_exists( 'afrfq_view_button_message_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_view_button_message_callback( $args = array() ) {
		?>
		<input type="text" name="afrfq_view_button_message" id="afrfq_view_button_message" value="<?php echo esc_attr( get_option( 'afrfq_view_button_message' ) ); ?>" />
		<p class="description afreg_additional_fields_section_title"><?php echo wp_kses_post( $args[0] ); ?></p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_success_message_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_success_message_callback( $args = array() ) {
		?>
		<textarea type="text" name="afrfq_success_message" id="afrfq_success_message"><?php echo esc_attr( get_option( 'afrfq_success_message' ) ); ?></textarea>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}
