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
	'afrfq-general-sec',         // ID used to identify this section and with which to register options.
	esc_html__( 'General Settings', 'addify_rfq' ),   // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'afrfq_general_setting_section'                           // Page on which to add this section of options.
);

add_settings_field(
	'enable_o_o_s_products',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable (Out of stock)', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_o_o_s_products_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable/Disable request a quote button for out of stock products. (Note: It is compatible with simple and variable products only', 'addify_rfq' ) )
);

register_setting(
	'afrfq_general_setting_fields',
	'enable_o_o_s_products'
);

add_settings_field(
	'quote_menu',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Quote Basket Menu(s)', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_quote_menu_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array(
		esc_html__( 'Select Menu where you want to show Mini quote Basket. If there is no menu then you have to create menu in WordPress menus otherwise mini quote basket will not show.', 'addify_rfq' ),
		esc_html__( 'Alternatively, You can add quote basket by placing the short code [addify-mini-quote].', 'addify_rfq' ),
	)
);

register_setting(
	'afrfq_general_setting_fields',
	'quote_menu'
);

add_settings_field(
	'afrfq_customer_roles',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Disable quote basket for user roles', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_customer_roles_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Disable quote basket for user roles. Enable for all user roles by default.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_general_setting_fields',
	'afrfq_customer_roles'
);

add_settings_field(
	'afrfq_basket_option',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Quote Basket Style', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_basket_option_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array(
		esc_html__( 'Select to show drop down or icon with number of items in Basket.', 'addify_rfq' ),
		esc_html__( 'The mini quote design may not work properly for some page builders/themes. Alternatively, you can use the icon only layout or override the mini quote template by copying it to yourtheme/woocommerce/addify/rfq/quote/mini-quote-dropdown.php', 'addify_rfq' ),
	)
);

register_setting(
	'afrfq_general_setting_fields',
	'afrfq_basket_option'
);

add_settings_field(
	'enable_ajax_product',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Ajax add to Quote (Product Page)', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_ajax_product_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable/Disable Ajax add to quote on product page.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_general_setting_fields',
	'enable_ajax_product'
);

add_settings_field(
	'enable_ajax_shop',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Enable Ajax add to Quote (Shop Page)', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_enable_ajax_shop_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Enable/Disable Ajax add to quote on shop page.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_general_setting_fields',
	'enable_ajax_shop'
);

add_settings_field(
	'afrfq_redirect_to_quote',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Redirect to Quote Page', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_redirect_to_quote_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Redirect to quote page after a product added to quote successfully.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_general_setting_fields',
	'afrfq_redirect_to_quote'
);

add_settings_field(
	'afrfq_redirect_after_submission',                      // ID used to identify the field throughout the theme.
	esc_html__( 'Redirect after Quote Submission', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_redirect_after_submission_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'Redirect to any page after quote is submitted successfully.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_general_setting_fields',
	'afrfq_redirect_after_submission'
);

add_settings_field(
	'afrfq_redirect_url',                      // ID used to identify the field throughout the theme.
	esc_html__( 'URL to Redirect', 'addify_rfq' ),    // The label to the left of the option interface element.
	'afrfq_redirect_url_callback',   // The name of the function responsible for rendering the option interface.
	'afrfq_general_setting_section',                          // The page on which this option will be displayed.
	'afrfq-general-sec',         // The name of the section to which this field belongs.
	array( esc_html__( 'URL to redirect after quote is submitted successfully.', 'addify_rfq' ) )
);

register_setting(
	'afrfq_general_setting_fields',
	'afrfq_redirect_url'
);

if ( ! function_exists( 'afrfq_customer_roles_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_customer_roles_callback( $args ) {
		$values    = get_option( 'afrfq_customer_roles' );
		$sel_roles = is_array( $values ) ? $values : array();
		?>
		<ul>
		<?php
		global $wp_roles;
		$roles = $wp_roles->get_names();
		foreach ( $roles as $key => $value ) {
			?>
			<li class="par_cat">
				<input type="checkbox" class="parent" name="afrfq_customer_roles[]" id="" value="<?php echo esc_attr( $key ); ?>" 
				<?php echo in_array( (string) $key, $sel_roles, true ) ? 'checked' : ''; ?>
				/>
				<?php echo esc_attr( $value ); ?>
			</li>
		<?php } ?>
			<li class="par_cat">
				<input type="checkbox" class="parent" name="afrfq_customer_roles[]" id="" value="guest" 
				<?php echo in_array( (string) 'guest', $sel_roles, true ) ? 'checked' : ''; ?>
				/>
				<?php echo esc_html__( 'Guest', 'addify_rfq' ); ?>
			</li>
		</ul>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}


if ( ! function_exists( 'afrfq_redirect_url_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_redirect_url_callback( $args = array() ) {
		$value = get_option( 'afrfq_redirect_url' );
		?>
		<input value="<?php echo esc_url( $value ); ?>" class="afrfq_input_class" type="url" name="afrfq_redirect_url" id="afrfq_redirect_url" />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_redirect_after_submission_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_redirect_after_submission_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_redirect_after_submission" id="afrfq_redirect_after_submission" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_redirect_after_submission' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_redirect_to_quote_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_redirect_to_quote_callback( $args = array() ) {
		?>
		<input type="checkbox" name="afrfq_redirect_to_quote" id="afrfq_redirect_to_quote" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'afrfq_redirect_to_quote' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_ajax_shop_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_ajax_shop_callback( $args = array() ) {
		?>
		<input type="checkbox" name="enable_ajax_shop" id="enable_ajax_shop" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'enable_ajax_shop' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_ajax_product_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_ajax_product_callback( $args = array() ) {
		?>
		<input type="checkbox" name="enable_ajax_product" id="enable_ajax_product" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'enable_ajax_product' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_basket_option_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_basket_option_callback( $args = array() ) {
		?>
		<input  value="dropdown" class="" id="" type="radio" name="afrfq_basket_option" <?php echo checked( get_option( 'afrfq_basket_option' ), 'dropdown' ); ?> >
			<?php echo esc_html__( 'Quote Basket with Dropdown', 'addify_rfq' ); ?>
			<br>
		<input value="icon" class="" id="" type="radio" name="afrfq_basket_option" <?php echo checked( get_option( 'afrfq_basket_option' ), 'icon' ); ?> >
			<?php echo esc_html__( 'Icon and Number of items', 'addify_rfq' ); ?>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[1] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_quote_menu_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_quote_menu_callback( $args = array() ) {
		$nav_menus  = (array) get_terms( 'nav_menu' );
		$quote_menu = get_option( 'quote_menu' );
		foreach ( $nav_menus as $nav_menu ) :
			?>
			<input type="checkbox" name="quote_menu[]" id="quote_menu" value="<?php echo intval( $nav_menu->term_id ); ?>" <?php echo in_array( $nav_menu->term_id, (array) $quote_menu ) ? 'checked' : ''; ?> />
			<?php echo esc_html( $nav_menu->name ); ?>
			<br>
		<?php endforeach; ?>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[1] ); ?> </p>
		<?php
	}
}

if ( ! function_exists( 'afrfq_enable_o_o_s_products_callback' ) ) {
		/**
		 *  AF_R_F_Q_Admin.
		 *
		 * @param array $args returns array.
		 */
	function afrfq_enable_o_o_s_products_callback( $args = array() ) {
		?>
		<input type="checkbox" name="enable_o_o_s_products" id="enable_o_o_s_products" value="yes" <?php echo checked( 'yes', esc_attr( get_option( 'enable_o_o_s_products' ) ) ); ?> />
		<p class="description afreg_additional_fields_section_title"> <?php echo wp_kses_post( $args[0] ); ?> </p>
		<?php
	}
}


