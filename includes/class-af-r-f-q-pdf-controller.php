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


// Include the main TCPDF library (search for installation path).
//require_once AFRFQ_PLUGIN_DIR . '/includes/class-af-r-f-q-pdf.php';

/**
 * AF_R_F_Q_PDF_Controller class.
 */
class AF_R_F_Q_PDF_Controller {
	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $quote_contents = array();

	/**
	 * Contains an array of quote items.
	 *
	 * @var array
	 */
	public $af_rfq_pdf;

	/**
	 * Constructor for the AF_R_F_Q_PDF_Controller class. Loads quote contents.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function __construct( $quote_id = 0 ) {
		$this->af_rfq_pdf = new AF_R_F_Q_PDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
	}

	/**
	 * Get_pdf_print.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function get_pdf_print( $quote_id ) {
		// Close and output PDF document.
		$site_name = str_replace( ' ', '_', get_bloginfo( 'name' ) );
		$file_name = $site_name . '_quote_' . substr( md5( $quote_id . time() ), 5 ) . '.pdf';
		$path      = AFRFQ_PLUGIN_DIR . '/includes/pdf/pdf-files/';

		$output_file = $path . $file_name;
		ob_start();
		$this->af_rfq_pdf->Output( $output_file, 'FI' );
		$file            = ob_get_clean();
		$output_file_url = AFRFQ_URL . 'includes/pdf/pdf-files/' . $file_name;
		return $output_file_url;
	}

	/**
	 * Get_header.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function get_header( $quote_id ) {

		$custom_logo_id = get_theme_mod( 'custom_logo' );

		$image_file = wp_get_attachment_image_url( $custom_logo_id, 'full' );

		$this->af_rfq_pdf->SetFont( 'helvetica', '', 8 );

		ob_start();

		if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/pdf/pdf-header.php' ) ) {

			include get_stylesheet_directory() . '/woocommerce/addify/pdf/pdf-header.php';

		} else {

			include AFRFQ_PLUGIN_DIR . 'includes/pdf/templates/pdf-header.php';
		}

		$html = ob_get_clean();

		$this->af_rfq_pdf->writeHTML( $html, false, false, false, false, '' );
	}

	/**
	 * Set_properties.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function set_properties( $quote_id ) {

		// set default monospaced font.
		$this->af_rfq_pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

		// set margins.
		$this->af_rfq_pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );

		// set auto page breaks.
		$this->af_rfq_pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );

		// set image scale factor.
		$this->af_rfq_pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );

		// set some language-dependent strings (optional).
		if ( file_exists( __DIR__ . '/lang/eng.php' ) ) {
			require_once __DIR__ . '/lang/eng.php';
			$this->af_rfq_pdf->setLanguageArray( $l );
		}

		// add a page.
		$this->af_rfq_pdf->AddPage();

		$this->af_rfq_pdf->SetFont( 'helvetica', '', 8 );
	}

	/**
	 * Set_properties.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function process_pdf_print( $quote_id ) {

		$this->set_properties( $quote_id );
		$this->get_header( $quote_id );
		$this->add_quote_contents_table( $quote_id );
		$this->add_customer_info_table( $quote_id );

		return $this->get_pdf_print( $quote_id );
	}

	/**
	 * Set_properties.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function add_customer_info_table( $quote_id ) {

		$customer_info = $this->get_quote_user_info( $quote_id );

		if ( empty( $customer_info ) ) {
			return;
		}

		ob_start();

		if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/pdf/customer-info.php' ) ) {

			include get_stylesheet_directory() . '/woocommerce/addify/pdf/customer-info.php';

		} else {

			include AFRFQ_PLUGIN_DIR . 'includes/pdf/templates/customer-info.php';
		}

		$table_html = ob_get_clean();

		$this->af_rfq_pdf->writeHTML( $table_html, true, false, false, false, '' );
	}

	/**
	 * Set_properties.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function add_quote_contents_table( $quote_id ) {

		$quote_contents = get_post_meta( $quote_id, 'quote_contents', true );

		$af_quote = new AF_R_F_Q_Quote();

		$totals = $af_quote->get_calculated_totals( $quote_contents, $quote_id );

		$quote_subtotal   = isset( $totals['_subtotal'] ) ? $totals['_subtotal'] : 0;
		$offered_subtotal = isset( $totals['_offered_total'] ) ? $totals['_offered_total'] : 0;
		$vat_total        = isset( $totals['_tax_total'] ) ? $totals['_tax_total'] : 0;
		$quote_total      = isset( $totals['_total'] ) ? $totals['_total'] : 0;

		if ( empty( $quote_contents ) || ! is_array( $quote_contents ) ) {
			return;
		}

		ob_start();

		if ( file_exists( get_stylesheet_directory() . '/woocommerce/addify/pdf/quote-contents.php' ) ) {

			include get_stylesheet_directory() . '/woocommerce/addify/pdf/quote-contents.php';

		} else {

			include AFRFQ_PLUGIN_DIR . 'includes/pdf/templates/quote-contents.php';
		}

		$table_html = ob_get_clean();

		$this->af_rfq_pdf->writeHTML( $table_html, true, false, false, false, '' );
	}

	/**
	 * Set_properties.
	 *
	 * @param object $quote_id returns qoute id.
	 */
	public function get_quote_user_info( $quote_id ) {

		$customer_info = array();
		$quote_date    = gmdate( 'M d, y', get_post_time( 'U', false, $quote_id, true ) );

		$customer_info['quote_id']   = array(
			'label' => __( 'Quote Number', 'addify_rfq' ),
			'value' => $quote_id,
		);
		$customer_info['quote_date'] = array(
			'label' => __( 'Quote Date', 'addify_rfq' ),
			'value' => $quote_date,
		);

		$quote_fiels_obj = new AF_R_F_Q_Quote_Fields();
		$quote_fields    = (array) $quote_fiels_obj->afrfq_get_fields_enabled();

		if ( empty( $quote_fields ) ) {
			return $customer_info;
		}

		foreach ( $quote_fields as $key => $field ) {

			if ( ! is_a( $field, 'WP_Post' ) ) {
				continue;
			}

			$post_id = $field->ID;

			$afrfq_field_name  = get_post_meta( $post_id, 'afrfq_field_name', true );
			$afrfq_field_type  = get_post_meta( $post_id, 'afrfq_field_type', true );
			$afrfq_field_label = get_post_meta( $post_id, 'afrfq_field_label', true );
			$field_data        = get_post_meta( $quote_id, $afrfq_field_name, true );

			if ( is_array( $field_data ) ) {
				$field_data = implode( ', ', $field_data );
			}

			if ( in_array( $afrfq_field_type, array( 'select', 'radio', 'mutliselect' ), true ) ) {
				$field_data = ucwords( $field_data );
			}

			$customer_info[ $afrfq_field_name ] = array(
				'label' => $afrfq_field_label,
				'value' => $field_data,
			);
		}

		return $customer_info;
	}
}
