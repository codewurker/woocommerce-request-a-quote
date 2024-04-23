<?php
/**
 * PDF Layout tab fields
 *
 * @package  woocommerce-request-a-quote
 * @version  1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_settings_section(
	'afrfq-pdflayout-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'PDF Settings', 'addify_rfq' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_pdflayout_setting_section' // Page on which to add this section of options.
);

add_settings_field(
	'afrfq_company_logo',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Company Logo', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_company_logos_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                          // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Upload Company Logo.', 'addify_rfq' ) )
);
register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_company_logo' // This should match the name attribute of the input field.
);

add_settings_field(
	'afrfq_company_name_text',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Company Name', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_company_name_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                          // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enter Company Name.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_company_name_text'
);

add_settings_field(
	'afrfq_company_address',                                     // ID used to identify the field throughout the theme.
	esc_html__( 'Company Address', 'addify_rfq' ),               // The label to the left of the option interface element.
	'afrfq_afrfq_company_address_callback',                      // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                           // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',                                       // The name of the section to which this field belongs.
	array( esc_html__( 'Enter company address info.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_company_address'
);

add_settings_field(
	'afrfq_pdf_select_layout',                          // ID used to identify the field throughout the theme.
	esc_html__( 'Select Layout', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_pdf_select_layout_callback',                 // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                        // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',                                    // The name of the section to which this field belongs.
	array( esc_html__( 'Select the Layout for pdf.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_pdf_select_layout'
);

add_settings_field(
	'afrfq_backrgound_color',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Background Color', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_backrgound_color_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                          // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Select Background Color for Layout .', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_backrgound_color'
);


add_settings_field(
	'afrfq_text_color_for_background',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Text color for background', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_text_color_for_background_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                          // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Text color is applied on text with background color .', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_text_color_for_background'
);

add_settings_field(
	'afrfq_enable_term_and_condition',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Term and Condition', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_term_and_condition_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                          // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable Term & Conditions in pdf.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_enable_term_and_condition'
);

add_settings_field(
	'afrfq_term_and_condition_text',                          // ID used to identify the field throughout the theme.
	esc_html__( 'Term and Condition Text', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_term_and_condition_text_callback',                 // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                        // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',                                    // The name of the section to which this field belongs.
	array( esc_html__( 'Enter text for Term & Conditions in pdf.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_term_and_condition_text'
);



add_settings_field(
	'afrfq_disable_for_customers',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Disable for Customers', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_disable_for_customers_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_pdflayout_setting_section',                          // The page on which this option will be displayed.
	'afrfq-pdflayout-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Disable PDF download for customers in my account quotes grid.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_pdflayout_setting_fields',
	'afrfq_disable_for_customers'
);


if ( ! function_exists( 'afrfq_company_logos_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_company_logos_callback( $args = array() ) {
		?>
		<div id="afrfq_company_logo_preview">
			<img src="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" alt="Company Logo" style="max-width: 150px; border-radius:10px" />
		</div>
		<input type="hidden" class="afrfq_company_logo" id="afrfq_company_logo" name="afrfq_company_logo" value="<?php echo esc_url( get_option( 'afrfq_company_logo' ) ); ?>" />
		<input type="button" class="button-secondary afrfq_upload_button" value="Upload Image" />
		<p class="description"><?php echo wp_kses_post( $args[0] ); ?></p>
			<?php
	}
}

if ( ! function_exists( 'afrfq_afrfq_company_address_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_afrfq_company_address_callback( $args = array() ) {

		$af_rfq_get_site_address_1 = get_option('woocommerce_store_address');
		$af_rfq_get_site_address_2 = get_option('woocommerce_store_address_2');
		$value                     = get_option( 'afrfq_company_address' );
		if (empty($value)) {

			if (!empty($af_rfq_get_site_address_1)) {

				$value =$af_rfq_get_site_address_1;

			} elseif (( !empty($af_rfq_get_site_address_2) )&&( empty($af_rfq_get_site_address_1) )) {
				
				$value =$af_rfq_get_site_address_2;
		
			}

		}
		
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_company_address" type="text" name="afrfq_company_address" id="afrfq_company_address" />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<?php
	}
}

if ( ! function_exists( 'afrfq_company_name_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_company_name_callback( $args = array() ) {

		$value = !empty( get_option( 'afrfq_company_name_text' ) ) ? get_option( 'afrfq_company_name_text' ) : get_bloginfo('name') ;
	
		?>
		<input value="<?php echo esc_html( $value ); ?>" class="afrfq_company_name_text" type="text" name="afrfq_company_name_text" id="afrfq_company_name_text" />
		<p class="description"> <?php echo wp_kses_post( current( $args ) ); ?> </p>
			<?php
	}
}

if ( ! function_exists( 'afrfq_enable_term_and_condition_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_term_and_condition_callback( $args = array() ) {

		?>
		<input type="checkbox" name="afrfq_enable_term_and_condition" id="afrfq_enable_term_and_condition" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_enable_term_and_condition' ) ) ); ?> />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<?php
	}
}

if ( ! function_exists( 'afrfq_term_and_condition_text_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_term_and_condition_text_callback( $args = array() ) {

		$af_rfq_privicy_and_term_conditions =get_option( 'afrfq_term_and_condition_text' );
		
		if (!empty($af_rfq_privicy_and_term_conditions)) {
			$af_rfq_privicy_and_term_conditions =get_option( 'afrfq_term_and_condition_text' );    
		} else {
			$af_rfq_privicy_and_term_conditions ='Privacy & Terms and Conditions';
		}


		?>
		<textarea class="afrfq_term_and_condition_text" type="text" name="afrfq_term_and_condition_text" id="afrfq_term_and_condition_text" ><?php echo esc_html( $af_rfq_privicy_and_term_conditions ); ?></textarea>
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<?php
	}
}




if ( ! function_exists( 'afrfq_disable_for_customers_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_disable_for_customers_callback( $args = array() ) {

		?>
		<input type="checkbox" name="afrfq_disable_for_customers" id="afrfq_disable_for_customers" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_disable_for_customers' ) ) ); ?> />
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<?php
	}
}


if ( ! function_exists( 'afrfq_backrgound_color_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_backrgound_color_callback( $args = array() ) {

		?>
	<input type="color" value="<?php echo esc_attr( get_option( 'afrfq_backrgound_color' ) ); ?>" name="afrfq_backrgound_color" class="afrfq_backrgound_color" id="afrfq_backrgound_color">
	<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}}

if ( ! function_exists( 'afrfq_text_color_for_background_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_text_color_for_background_callback( $args = array() ) {

		?>
		<input type="color" value="<?php echo esc_attr( get_option( 'afrfq_text_color_for_background' ) ); ?>" name="afrfq_text_color_for_background" class="afrfq_text_color_for_background" id="afrfq_text_color_for_background">
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<?php
	}}

if ( ! function_exists( 'afrfq_pdf_select_layout_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_pdf_select_layout_callback( $args = array() ) {

		$afrfq_template_img1_path = AFRFQ_URL . 'assets/images/PDFLayout1.png';
		$afrfq_template_img2_path = AFRFQ_URL . 'assets/images/PDFLayout2.png';
		$afrfq_template_img3_path = AFRFQ_URL . 'assets/images/PDFLayout3.png';

		?>
		<select name="afrfq_pdf_select_layout" id="afrfq_pdf_select_layout">
			<option value="afrfq_template1" <?php selected( get_option( 'afrfq_pdf_select_layout' ), 'afrfq_template1' ); ?>><?php echo esc_html__( 'PDF Layout 1', 'addify_wsccr' ); ?></option>
			<option value="afrfq_template2" <?php selected( get_option( 'afrfq_pdf_select_layout' ), 'afrfq_template2' ); ?>><?php echo esc_html__( 'PDF Layout 2', 'addify_wsccr' ); ?></option>
			<option value="afrfq_template3" <?php selected( get_option( 'afrfq_pdf_select_layout' ), 'afrfq_template3' ); ?>><?php echo esc_html__( 'PDF Layout 3', 'addify_wsccr' ); ?></option>
		</select>
		<p class="description"> <?php echo wp_kses_post( $args[0] ); ?> </p>
			<img id="afrfq_template1" src="<?php echo esc_url( $afrfq_template_img1_path ); ?>" style="max-width: 400px;">
			<img id="afrfq_template2" src="<?php echo esc_url( $afrfq_template_img2_path ); ?>" style="max-width: 400px;">
			<img id="afrfq_template3" src="<?php echo esc_url( $afrfq_template_img3_path ); ?>" style="max-width: 400px;">
			<?php
	}
}


