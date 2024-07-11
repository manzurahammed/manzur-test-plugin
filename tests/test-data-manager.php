<?php

namespace MANZUR\TestPlugin\Tests;

use MANZUR\TestPlugin\App\Data_Manager;
use WP_UnitTestCase;

class Test_Data_Manager extends WP_UnitTestCase {

	/**
	 * Set up the test case.
	 */
	public function setUp(): void {
		parent::setUp();
		Data_Manager::refresh_data(); // Ensure the transient is cleared before each test.
	}

	/**
	 * Test retrieving data when the transient is empty.
	 */
	public function test_get_data_from_api() {
		// Mock the API response.
		$mock_response = array( 'title' => 'This amazing table', 'data' => [
			'headers' => [ 'ID', 'First Name' ],
			'rows'    => [
				[
					'id'    => '55',
					'fname' => 'Joe'
				]
			]
		] );

		add_filter( 'pre_http_request', function () use ( $mock_response ) {
			return array(
				'response'    => [ 'code' => 200, 'message' => 'OK' ],
				'status_code' => 200,
				'success'     => 1,
				'body' => json_encode( $mock_response, true ),
			);
		}, 10, 3 );


		// Test the data retrieval.
		$data = Data_Manager::get_data();
		$this->assertEquals( $mock_response, $data );

		$cached_data = get_transient( Data_Manager::TRANSIENT_KEY );
		$this->assertEquals( $mock_response,  json_decode( $cached_data, true ));
	}

	/**
	 * Test handling an API error.
	 */
	public function test_get_data_api_error() {
		add_filter( 'pre_http_request', function () {
			return array(
				'response'    => [ 'code' => 400, 'message' => 'OK' ],
				'status_code' => 400,
				'success'     => 1,
				'body' => new \WP_Error( 'error', 'An error occurred' ),
			);
		}, 10, 3 );

		// Test the data retrieval.
		$data = Data_Manager::get_data();
		$this->assertEmpty( $data );
	}

	/**
	 * Test refreshing the data.
	 */
	public function test_refresh_data() {
		$cached_data = json_encode( array( 'key' => 'value' ) );
		set_transient( Data_Manager::TRANSIENT_KEY, $cached_data, HOUR_IN_SECONDS );

		// Ensure the transient is set.
		$this->assertEquals( $cached_data, get_transient( Data_Manager::TRANSIENT_KEY ) );

		// Refresh the data.
		Data_Manager::refresh_data();

		// Verify that the transient is deleted.
		$this->assertFalse( get_transient( Data_Manager::TRANSIENT_KEY ) );
	}
}
