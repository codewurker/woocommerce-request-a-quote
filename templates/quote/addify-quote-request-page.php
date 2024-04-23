<?php
/**
 * Quote Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/addify-quote-request-page.php.
 *
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

$af_quote = new AF_R_F_Q_Quote( WC()->session->get( 'quotes' ) );

$quotes = WC()->session->get( 'quotes' );

if ( ! empty( $quotes ) ) {

	foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

		if ( isset( $quote_item['quantity'] ) && empty( $quote_item['quantity'] ) ) {

			unset( $quotes[ $quote_item_key ] );
		}

		if ( ! isset( $quote_item['data'] ) ) {
			unset( $quotes[ $quote_item_key ] );
		}
	}

	WC()->session->set( 'quotes', $quotes );
}

if ( ! empty( WC()->session->get( 'quotes' ) ) ) {

	$quotes         = WC()->session->get( 'quotes' );
	$total          = 0;
	$user           = null;
	$user_name      = '';
	$user_email_add = '';

	if ( is_user_logged_in() ) {
		$user = wp_get_current_user(); // object.
		if ( '' == $user->user_firstname && '' == $user->user_lastname ) {
			$user_name = $user->nickname; // probably admin user.
		} elseif ( '' == $user->user_firstname || '' == $user->user_lastname ) {
			$user_name = trim( $user->user_firstname . ' ' . $user->user_lastname );
		} else {
			$user_name = trim( $user->user_firstname . ' ' . $user->user_lastname );
		}

		$user_email_add = $user->user_email;
	}

	do_action( 'addify_before_quote' );

	$price_display    = 'yes' === get_option( 'afrfq_enable_pro_price' ) ? true : false;
	$of_price_display = 'yes' === get_option( 'afrfq_enable_off_price' ) ? true : false;
	$tax_display      = 'yes' === get_option( 'afrfq_enable_tax' ) ? true : false;

	?>
	<div class="woocommerce">
		<div class="woocommerce-notices-wrapper">
		</div>
		<form class="woocommerce-cart-form addify-quote-form" method="post" enctype="multipart/form-data">
			
			<?php

			if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-table.php' ) ) {

				include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-table.php';

			} else {

				wc_get_template(
					'quote/quote-table.php',
					array(),
					'/woocommerce/addify/rfq/',
					AFRFQ_PLUGIN_DIR . 'templates/'
				);
			}

			?>

			<?php do_action( 'addify_before_quote_collaterals' ); ?>
			<?php if ( $price_display || $of_price_display ) : ?>
				<div class="cart-collaterals">
					<?php
						/**
						 * Quote collateral's hook.
						 */
						do_action( 'addify_quote_collaterals' );
					?>
					<div class="cart_totals">

						<?php do_action( 'addify_rfq_before_quote_totals' ); ?>

						<h2><?php esc_html_e( 'Quote totals', 'addify_rfq' ); ?></h2>

						<?php
						if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php' ) ) {

							include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-totals-table.php';

						} else {

							wc_get_template(
								'quote/quote-totals-table.php',
								array(),
								'/woocommerce/addify/rfq/',
								AFRFQ_PLUGIN_DIR . 'templates/'
							);
						}
						?>
					</div>
				</div>
			<?php endif; ?>

			<?php do_action( 'addify_after_quote' ); ?>

			<div class="af_quote_fields">

				<?php
				if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-fields.php' ) ) {

					include get_stylesheet_directory() . '/woocommerce/addify/rfq/front/quote-fields.php';

				} else {

					wc_get_template(
						'quote/quote-fields.php',
						array(),
						'/woocommerce/addify/rfq/',
						AFRFQ_PLUGIN_DIR . 'templates/'
					);
				}
				?>

				<?php do_action( 'addify_after_quote_fields' ); ?>

				<?php if ( 'yes' == get_option( 'afrfq_enable_captcha' ) ) { ?>

					<?php if ( ! empty( get_option( 'afrfq_site_key' ) ) ) { ?>

						<div class="form_row">
							<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( get_option( 'afrfq_site_key' ) ); ?>"></div>
						</div>
					<?php } ?>
				<?php } ?>

				<div class="form_row">
					<input name="afrfq_action" type="hidden" value="save_afrfq"/>

					<?php
					$afrfq_submit_button_text     = get_option( 'afrfq_submit_button_text' );
					$afrfq_submit_button_bg_color = get_option( 'afrfq_submit_button_bg_color' );
					$afrfq_submit_button_fg_color = get_option( 'afrfq_submit_button_fg_color' );
					$afrfq_submit_button_text     = empty( $afrfq_submit_button_text ) ? __( 'Place Quote', 'addify_rfq' ) : $afrfq_submit_button_text;
					?>

					<style type="text/css">
						.addify_checkout_place_quote{
							color: <?php echo esc_html( $afrfq_submit_button_fg_color ); ?> !important;
							background-color: <?php echo esc_html( $afrfq_submit_button_bg_color ); ?> !important;
						}
					</style>
					<?php wp_nonce_field( 'save_afrfq', 'afrfq_nonce' ); ?>
					
					<button type="submit" name="addify_checkout_place_quote" class="button alt addify_checkout_place_quote"><?php echo esc_html( $afrfq_submit_button_text ); ?></button>
				</div>

			</div>
		</form>
	</div>

<?php } else { ?>

	<div class="addify">
		<p class="cart-empty"><?php echo esc_html__( 'Your quote is currently empty.', 'addify_rfq' ); ?></p>
				<p class="return-to-shop"><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="button wc-backward"><?php echo esc_html__( 'Return To Shop', 'addify_rfq' ); ?></a>
		</p>
	</div>

	<?php
}
