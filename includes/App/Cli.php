<?php

namespace MANZUR\TestPlugin\App;

use MANZUR\TestPlugin\Core\Singleton;

/**
 * Class Cli
 *
 * Handles the registration of WP-CLI commands for the plugin.
 *
 * @package Manzur\TestPlugin\App
 */
class Cli extends Singleton {

	/**
	 * Register the WP-CLI command.
	 *
	 * Checks if WP_CLI exists and registers the custom command for refreshing API data.
	 */
	public function register_cli_command() {
		if ( class_exists( 'WP_CLI' ) ) {
			\WP_CLI::add_command( 'manzur-refresh-api-data', '\MANZUR\TestPlugin\App\Commands\Refresh_Data' );
		}
	}

	/**
	 * Initialize hooks for registering the WP-CLI command.
	 */
	public function init() {
		add_action( 'cli_init', array( $this, 'register_cli_command' ), 100 );
	}
}
