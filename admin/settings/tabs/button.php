<?php
/**
 * General settings tab fields
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-button-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Place Quote Button Settings', 'addify_rfq' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_button_setting_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_submit_button_text',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Place quote button text', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_submit_button_text_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-button-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote button text.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_submit_button_text'
);

add_settings_field(
	'afrfq_submit_button_bg_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Place quote button background color', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_submit_button_bg_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-button-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote background color.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_submit_button_bg_color'
);

add_settings_field(
	'afrfq_submit_button_fg_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Place quote button color', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_submit_button_fg_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq-button-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote text color.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_submit_button_fg_color'
);

if ( ! function_exists( 'afrfq_submit_button_text_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_submit_button_text_callback( $args = array() ) {
		$value = get_option( 'afrfq_submit_button_text' );
		$value = empty( $value ) ? __( 'Place Quote', 'addify_rfq' ) : $value;
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="text" name="afrfq_submit_button_text" id="afrfq_submit_button_text" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_submit_button_bg_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_submit_button_bg_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_submit_button_bg_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="color" name="afrfq_submit_button_bg_color" id="afrfq_submit_button_bg_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_submit_button_fg_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_submit_button_fg_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_submit_button_fg_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="color" name="afrfq_submit_button_fg_color" id="afrfq_submit_button_fg_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

add_settings_section(
	'afrfq_update_button_sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Update Quote Button Settings', 'addify_rfq' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_button_setting_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_submit_button_text',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Update quote button text', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_update_button_text_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq_update_button_sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote button text.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_update_button_text'
);

add_settings_field(
	'afrfq_update_button_bg_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Update quote button background color', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_update_button_bg_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq_update_button_sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote background color.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_button_setting_fields',
	'afrfq_update_button_bg_color'
);

add_settings_field(
	'afrfq_update_button_fg_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Update quote button color', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_update_button_fg_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_button_setting_section',                          // The page on which this option will be displayed.
	'afrfq_update_button_sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Customize place quote text color.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdf_layout_fields',
	'afrfq_update_button_fg_color'
);

if ( ! function_exists( 'afrfq_update_button_text_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_update_button_text_callback( $args = array() ) {
		$value = get_option( 'afrfq_update_button_text' );
		$value = empty( $value ) ? __( 'Update Quote', 'addify_rfq' ) : $value;
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="text" name="afrfq_update_button_text" id="afrfq_update_button_text" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_update_button_bg_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_update_button_bg_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_update_button_bg_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="color" name="afrfq_update_button_bg_color" id="afrfq_update_button_bg_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_update_button_fg_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_update_button_fg_color_callback( $args = array() ) {
		$value = get_option( 'afrfq_update_button_fg_color' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="color" name="afrfq_update_button_fg_color" id="afrfq_update_button_fg_color" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}
