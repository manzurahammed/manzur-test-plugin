<?php

namespace MANZUR\TestPlugin\App;

use MANZUR\TestPlugin\Core\Singleton;

/**
 * Class Data_Manager
 *
 * Manages the retrieval and caching of API data.
 *
 * @package Manzur\TestPlugin\App
 */
class Data_Manager extends Singleton {
	const TRANSIENT_KEY = 'manzur_test_plugin_api_data';
	const API_ENDPOINT  = 'https://miusage.com/v1/challenge/1/';

	/**
	 * Retrieve the data from the API or cache.
	 *
	 * @return array The API data or an empty array if there's an error.
	 */
	public static function get_data() {
		$data = get_transient( self::TRANSIENT_KEY );

		if ( $data === false ) {
			$data = self::fetch_data_from_api();
			if ( !empty( $data ) ) {
				set_transient( self::TRANSIENT_KEY, $data, HOUR_IN_SECONDS );
			}
		}

		return self::decode_data( $data );
	}

	/**
	 * Fetch data from the API.
	 *
	 * @return string|false The API response body or false if there's an error.
	 */
	private static function fetch_data_from_api() {
		$response = wp_remote_get( self::API_ENDPOINT );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( $http_code !== 200 ) {
			set_transient( self::TRANSIENT_KEY, json_encode( [] ), 15 * MINUTE_IN_SECONDS );
			return false;
		}

		$data = wp_remote_retrieve_body( $response );
		if ( empty( $data ) ) {
			return false;
		}

		return $data;
	}

	/**
	 * Decode JSON data.
	 *
	 * @param string $data The JSON data to decode.
	 * @return array The decoded data or an empty array if there's an error.
	 */
	private static function decode_data( $data ) {
		$decoded_data = json_decode( $data, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return array();
		}

		return $decoded_data;
	}

	/**
	 * Refresh the cached data by deleting the transient.
	 */
	public static function refresh_data() {
		delete_transient( self::TRANSIENT_KEY );
	}
}
