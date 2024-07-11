<?php
/**
 * Plugin Name:       Manzur Test plugin
 * Description:       A plugin to fetch and display data.
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Version:           1.0
 * Author:            Manzur
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       manzur-test-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

// Plugin version.
if ( ! defined( 'MANZUR_PLUGINTEST_VERSION' ) ) {
	define( 'MANZUR_PLUGINTEST_VERSION', '1.0.0' );
}

// Define PLUGIN_FILE.
if ( ! defined( 'MANZUR_PLUGINTEST_PLUGIN_FILE' ) ) {
	define( 'MANZUR_PLUGINTEST_PLUGIN_FILE', __FILE__ );
}

// Plugin directory.
if ( ! defined( 'MANZUR_PLUGINTEST_DIR' ) ) {
	define( 'MANZUR_PLUGINTEST_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin url.
if ( ! defined( 'MANZUR_PLUGINTEST_URL' ) ) {
	define( 'MANZUR_PLUGINTEST_URL', plugin_dir_url( __FILE__ ) );
}

// Assets url.
if ( ! defined( 'MANZUR_PLUGINTEST_ASSETS_URL' ) ) {
	define( 'MANZUR_PLUGINTEST_ASSETS_URL', MANZUR_PLUGINTEST_URL . '/assets' );
}



class MANZUR_TestPlugin {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load() {
		load_plugin_textdomain(
			'manzur-test-plugin',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);

		\MANZUR\TestPlugin\Core\Loader::instance();
	}
}

// Init the plugin and load the plugin instance for the first time.
add_action(
	'plugins_loaded',
	function () {
		MANZUR_TestPlugin::get_instance()->load();
	}
);
