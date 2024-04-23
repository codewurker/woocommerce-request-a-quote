<?php
/**
 * Template for email to customer.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/emails/quote-email-to-customer.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked AF_R_F_Q_Email_Controller::email_header() Output the email header
 */
do_action( 'addify_rfq_email_header', $email_heading, $email ); ?>

<p>
	<?php
		echo wp_kses_post(
			str_replace(
				array( '{user_name}', '{quote_id}' ),
				array( $user_name, $quote_id ),
				wpautop( wptexturize( apply_filters( 'addify_rfq_email_text', $email_message ) ) )
			)
		);
		?>
</p>

<?php

/*
 * @hooked AF_R_F_Q_Email_Controller::customer_details() Shows customer details
 * @hooked AF_R_F_Q_Email_Controller::email_address() Shows email address
 */
do_action( 'addify_rfq_email_customer_details', $quote_id, $email );


do_action( 'addify_rfq_email_after_customer_details', $quote_id, $email );

/*
 * @hooked AF_R_F_Q_Email_Controller::quote_details() Shows the quote details table.
 *
 */

do_action( 'addify_rfq_email_quote_details', $quote_id, $email );

/*
 * @hooked AF_R_F_Q_Email_Controller::quote_meta() Shows quote meta data.
 */
do_action( 'addify_rfq_email_quote_meta', $quote_id, $email );


/*
 * @hooked AF_R_F_Q_Email_Controller::email_footer() Output the email footer
 */
do_action( 'addify_rfq_email_footer', $email );
