<?php
/**
 * Captcha settings tab fields.
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-captcha-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Google Captcha Settings', 'addify_rfq' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_captcha_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_enable_captcha',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Captcha', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_captcha_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_captcha_section',                          // The page on which this option will be displayed.
	'afrfq-captcha-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable Google reCaptcha field on the Request a Quote Form.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_captcha_fields',
	'afrfq_enable_captcha'
);

add_settings_field(
	'afrfq_site_key',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Site Key', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_site_key_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_captcha_section',                          // The page on which this option will be displayed.
	'afrfq-captcha-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'This is Google reCaptcha site key, you can get this from Google. Without this key Google reCaptcha will not work.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_captcha_fields',
	'afrfq_site_key'
);

add_settings_field(
	'afrfq_secret_key',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Secret Key', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_secret_key_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_captcha_section',                          // The page on which this option will be displayed.
	'afrfq-captcha-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'This is Google reCaptcha secret key, you can get this from google. Without this key google reCaptcha will not work.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_captcha_fields',
	'afrfq_secret_key'
);

if ( ! function_exists( 'afrfq_secret_key_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_secret_key_callback( $args = array() ) {
		$value = get_option( 'afrfq_secret_key' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="text" name="afrfq_secret_key" id="afrfq_secret_key" />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_site_key_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_site_key_callback( $args = array() ) {
		$value = get_option( 'afrfq_site_key' );
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_input_class" type="text" name="afrfq_site_key" id="afrfq_site_key" />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_captcha_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_captcha_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_captcha" id="afrfq_enable_captcha" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_captcha' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}
