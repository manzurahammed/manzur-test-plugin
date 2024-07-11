<?php
namespace MANZUR\TestPlugin\Core;

use MANZUR\TestPlugin\App;

/**
 * Class Loader
 *
 * Responsible for initializing components of the plugin based on PHP and WordPress version checks.
 *
 * @package Manzur\TestPlugin\Core
 */
final class Loader extends Singleton {
	/**
	 * Settings helper class instance.
	 *
	 * @var object
	 */
	public $settings;

	/**
	 * Minimum supported PHP version.
	 *
	 * @var string
	 */
	public $php_version = '7.4';

	/**
	 * Minimum WordPress version.
	 *
	 * @var string
	 */
	public $wp_version = '6.1';

	/**
	 * Constructs the Loader instance.
	 */
	protected function __construct() {
		if ( ! $this->can_boot() ) {
			return;
		}

		$this->init();
	}

	/**
	 * Checks if the current PHP and WordPress versions meet the minimum requirements.
	 *
	 * @return bool Whether the plugin can be initialized.
	 */
	private function can_boot() {
		global $wp_version;

		return (
			version_compare( PHP_VERSION, $this->php_version, '>' ) &&
			version_compare( $wp_version, $this->wp_version, '>' )
		);
	}

	/**
	 * Initializes the plugin components.
	 */
	private function init() {
		App\Admin_Page::instance()->init();
		App\Ajax::instance()->init();
		App\Cli::instance()->init();+
		App\Blocks::instance()->init();
	}
}
