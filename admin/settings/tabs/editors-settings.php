<?php
/**
 * Editors settings tab fields
 *
 * @package  woocommerce-request-a-yes
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-editors-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'Editors & Builders Settings', 'addify_rfq' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_editors_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_enable_elementor_compt',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Elementor Compatibility', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_elementor_compt_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_editors_section',                          // The page on which this option will be displayed.
	'afrfq-editors-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable Elementor compatibility', 'addify_rfq' ) )
);

register_setting(
	'afrfq_editors_fields',
	'afrfq_enable_elementor_compt'
);

add_settings_field(
	'afrfq_enable_divi_compt',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Divi Builder Compatibility', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_divi_compt_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_editors_section',                          // The page on which this option will be displayed.
	'afrfq-editors-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable Divi Builder Compatibility.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_editors_fields',
	'afrfq_enable_divi_compt'
);

add_settings_field(
	'afrfq_enable_solution2',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Solution 2', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_solution2_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_editors_section',                          // The page on which this option will be displayed.
	'afrfq-editors-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable another solution if your add to cart is not replaced by plugin Button.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_editors_fields',
	'afrfq_enable_solution2'
);

if ( ! function_exists( 'afrfq_enable_solution2_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_solution2_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_solution2" id="afrfq_enable_solution2" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_solution2' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_divi_compt_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_divi_compt_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_divi_compt" id="afrfq_enable_divi_compt" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_divi_compt' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_elementor_compt_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_elementor_compt_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_enable_elementor_compt" id="afrfq_enable_elementor_compt" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_elementor_compt' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}
