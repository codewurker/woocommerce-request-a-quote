<?php

defined( 'ABSPATH' ) || exit;

class AF_R_F_Q_Rule_Controller {

	protected $ID;

	public $post;

	public function __construct( $id = 0 ) {
		$this->ID = $id;

		$this->post = get_post( $this->ID );
	}

	public function get_id() {
		return $this->ID;
	}

	public function save() {
	}
}
