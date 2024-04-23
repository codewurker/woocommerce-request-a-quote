<?php
/**
 * Customer information table for email.
 *
 * The WooCommerce quote class stores quote data and maintain session of quotes.
 * The quote class also has a price calculation function which calls upon other classes to calculate totals.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$data          = $post;
$post_meta     = get_post_meta( $data->ID, 'products', true );
$quote         = json_decode( $post_meta, true );
$quotedataname = get_post_meta( $data->ID, 'quote_proname', true );
$quotedataid   = get_post_meta( $data->ID, 'quote_proid', true );
$quotedataqty  = get_post_meta( $data->ID, 'qty', true );
$woo_addons    = get_post_meta( $data->ID, 'woo_addons', true );
$woo_addons1   = get_post_meta( $data->ID, 'woo_addons1', true );
$variationall  = get_post_meta( $data->ID, 'variation', true );

if ( ! empty( $data ) ) {

	if ( ! empty( $quotedataid ) ) {
		$quote_tab  = '<div class="afrfq-metabox-fields">';
		$quote_tab .= '<table border="1" width="1100" class="quotetable">';
		$quote_tab .= '<tr>';
		$quote_tab .= '<th></th>';
		$quote_tab .= '<th><h2>' . esc_html__( 'Product', 'addify_rfq' ) . '</h2></th>';
		$quote_tab .= '<th><h2>' . esc_html__( 'Product SKU', 'addify_rfq' ) . '</h2></th>';
		$quote_tab .= '<th><h2>' . esc_html__( 'Quantity', 'addify_rfq' ) . '</h2></th>';
		$quote_tab .= '</tr>';

		$count_products = count( $quotedataid );

		for ( $i = 0; $i < $count_products; $i++ ) {

			$product = wc_get_product( $quotedataid[ $i ] );

			if ( ! is_object( $product ) ) {
				continue;
			}

			$image         = get_the_post_thumbnail_url( $quotedataid[ $i ] );
			$product_title = $product->get_title();
			$product_url   = $product->get_permalink();

			$product_sku = $product->get_sku();

			if ( ! empty( $woo_addons ) ) {

				$w_add = $woo_addons[ $i ];
			} else {
				$w_add = '';
			}

			if ( ! empty( $woo_addons1 ) ) {

				$w_add1 = $woo_addons1[ $i ];
			} else {
				$w_add1 = '';
			}

			if ( ! empty( $variationall ) ) {

				$variation = $variationall[ $i ];
			} else {
				$variation = array();
			}

			$postvalue = unserialize( base64_decode( $variation ) );

			$item_data = array();
			$var_data  = array();
			$str       = '?';
			if ( ! empty( $postvalue ) ) {

				foreach ( $postvalue as $name => $value ) {
					$taxonomy1           = str_replace( 'attribute_', '', $value[0] );
					$label               = wc_attribute_label( $taxonomy1, $product );
					$item_data[ $label ] = $value[1];
					if ( '?' !== $str ) {
						$str .= '&';
					}
					$str       .= $value[0] . '=' . $value[1];
					$var_data[] = ucfirst( $value[1] );
					?>

					<?php
				}
			}

			$p_title = $product->get_title();

			$quote_tab .= '<tr>';
			$quote_tab .= '<th><a href="' . esc_url( $product_url ) . '" target="_blank"><img src="' . esc_url( $image ) . '" width="80"></a></th>';
			$quote_tab .= '<th><a href="' . esc_url( $product_url ) . '" target="_blank"><b>' . esc_attr( $p_title ) . '</b></a><div class="woo_options"><dl class="variation">';

			foreach ( $item_data as $key => $datas ) {

				$quote_tab .= '<dt class="' . sanitize_html_class( 'variation-' . $key ) . '">' . wp_kses_post( ucfirst( $key ) ) . ':</dt>';
				$quote_tab .= '<dd class="' . sanitize_html_class( 'variation-' . $key ) . '">' . wp_kses_post( wpautop( $datas ) ) . '</dd>';
			}

			$quote_tab .= '</dl>';

			if ( '' !== $w_add ) {
				$wooaddons = explode( '-_-', $w_add );

				if ( '' !== $wooaddons ) {
					$count_wooaddons = count( $wooaddons );
					for ( $a = 1; $a < $count_wooaddons; $a++ ) {

						$quote_tab .= $wooaddons[ $a ] . '<br />';
					}
				}
			}

			if ( '' !== $w_add1 ) {
				$wooaddons1 = explode( '-_-', $w_add1 );

				if ( '' !== $wooaddons1 ) {
					$count_wooaddons1 = count( $wooaddons1 );
					for ( $b = 0; $b < $count_wooaddons1 - 1; $b++ ) {

						$new_a = explode( '_-_', $wooaddons1[ $b ] );
						$new_b = explode( ' (', $new_a[0] );

						if ( ! empty( $new_b ) && ! empty( $new_a ) ) {
							$quote_tab .= $new_b[0] . ' - ' . $new_a[1] . '<br />';
						}
					}
				}
			}

			$quote_tab .= '</th>';
			$quote_tab .= '<th><b>' . esc_attr( $product_sku ) . '</b></th>';
			$quote_tab .= '<th><b>' . esc_attr( $quotedataqty[ $i ] ) . '</b></th>';
			$quote_tab .= '</tr>';
		}


		$quote_tab .= '</table></div>';
		echo wp_kses_post( $quote_tab, '' );
	}
	?>

	</div>

	<?php
}
?>
</div>
