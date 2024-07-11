<?php

namespace MANZUR\TestPlugin\Tests;

use MANZUR\TestPlugin\App\Ajax;
use WP_Ajax_UnitTestCase;
use WPAjaxDieContinueException;

class Test_Ajax extends WP_Ajax_UnitTestCase {

	protected $ajax;

	/**
	 * Set up the test case.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->ajax = Ajax::instance();
		$this->ajax->init();
		$this->set_api_mock_data();
	}

	/**
	 * Test AJAX request with valid nonce and data.
	 */
	public function test_ajax_plugin_api_data() {
		global $_POST;
		$_POST[ 'security' ] = wp_create_nonce( 'manzur_ajax_nonce' );

		try {
			$this->_handleAjax( 'manzur_test_plugin_api_data' );
		} catch ( WPAjaxDieContinueException $e ) {
			$this->caught_deprecated = [];
		}

		$response = json_decode( $this->_last_response );
		$this->assertTrue( $response->success );
		$this->assertNotEmpty( $response->data );
	}

	/**
	 * Mock API data for successful response.
	 */
	public function set_api_mock_data() {
		$mock_response = array(
			'title' => 'This amazing table',
			'data'  => [
				'headers' => [ 'ID', 'First Name' ],
				'rows'    => [
					[
						'id'    => '55',
						'fname' => 'Joe'
					]
				]
			]
		);

		add_filter( 'pre_http_request', function () use ( $mock_response ) {
			return array(
				'response'    => [ 'code' => 200, 'message' => 'OK' ],
				'status_code' => 200,
				'success'     => 1,
				'body'        => json_encode( $mock_response ),
			);
		}, 10, 3 );
	}
}
