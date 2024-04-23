<?php
/**
 * Initialize this version of the REST API.
 *
 * @package RFQ\RestApi
 */

namespace Addify\Request_a_Quote\RestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Class responsible for loading the REST API and all REST API namespaces.
 */

use Automattic\WooCommerce\RestApi\Utilities\SingletonTrait;

class Server {

	use SingletonTrait;

	/**
	 * REST API namespaces and endpoints.
	 *
	 * @var array
	 */
	protected $controllers = array();

	/**
	 * Hook into WordPress ready to init the REST API as needed.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_rest_routes() {
		require AFRFQ_PLUGIN_DIR . 'includes/rest-api/controllers/class-af-r-f-q-rest-rules-controller.php';
		require AFRFQ_PLUGIN_DIR . 'includes/rest-api/controllers/class-af-r-f-q-rest-quotes-controller.php';

		foreach ( $this->get_rest_namespaces() as $namespace => $controllers ) {
			foreach ( $controllers as $controller_name => $controller_class ) {
				$this->controllers[ $namespace ][ $controller_name ] = new $controller_class();
				$this->controllers[ $namespace ][ $controller_name ]->register_routes();
			}
		}
	}

	/**
	 * Get API namespaces - new namespaces should be registered here.
	 *
	 * @return array List of Namespaces and Main controller classes.
	 */
	protected function get_rest_namespaces() {
		return apply_filters(
			'addify_rfq_rest_api_get_rest_namespaces',
			array(
				'afrfq' => $this->get_controllers(),
			)
		);
	}

	/**
	 * List of controllers in the namespace.
	 *
	 * @return array
	 */
	protected function get_controllers() {
		return array(
			'rfq_rules'  => 'AF_R_F_Q_Rest_Rules_Controller',
			'rfq_quotes' => 'AF_R_F_Q_Rest_Quotes_Controller',
		);
	}
}
