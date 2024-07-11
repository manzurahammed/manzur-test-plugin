<?php
namespace MANZUR\TestPlugin\App;

use MANZUR\TestPlugin\Core\Singleton;

/**
 * Class Ajax
 *
 * Handles the AJAX requests for fetching API data.
 *
 * @package Manzur\TestPlugin\App
 */
class Ajax extends Singleton {

	/**
	 * Initialize hooks for AJAX actions.
	 */
	public function init() {
		add_action( 'wp_ajax_manzur_test_plugin_api_data', array( $this, 'get_data' ) );
		add_action( 'wp_ajax_nopriv_manzur_test_plugin_api_data', array( $this, 'get_data' ) );
	}

	/**
	 * Fetch data and return as JSON response.
	 */
	public function get_data() {
		check_ajax_referer('manzur_ajax_nonce', 'security');

		$data = Data_Manager::get_data();

		if ( empty( $data ) ) {
			wp_send_json_error( array( 'message' => __( 'No data found.', 'manzur-test-plugin' ) ) );
		} else {
			wp_send_json_success( $data );
		}
	}
}
