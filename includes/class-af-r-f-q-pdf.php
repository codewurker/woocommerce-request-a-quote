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
require AFRFQ_PLUGIN_DIR . '/tcpdf-main/tcpdf.php';

/**
 * AF_R_F_Q_PDF_Controller class.
 */
class AF_R_F_Q_PDF extends TCPDF {
	/**
	 * Header of pdf page.
	 */
	public function Header() {
		// Logo.
		$this->SetFont( 'helvetica', 'I', 8 );
		$this->Cell( 0, 10, get_bloginfo( 'url' ), 0, 1, 'R', 0, '', 0 );
	}

	/**
	 * Footer of pdf page.
	 */
	public function Footer() {
		// Position at 15 mm from bottom.
		$this->SetY( -10 );
		// Set font.
		$this->SetFont( 'helvetica', 'I', 8 );
		// Page number.
		$this->MultiCell( 100, 5, get_bloginfo( 'url' ), 0, 'L', false, 0, '', '', true );
		$this->MultiCell( 100, 5, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 'R', false, 1, '', '', true );
	}
}
