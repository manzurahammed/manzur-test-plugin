<?php

namespace MANZUR\TestPlugin\App;

use MANZUR\TestPlugin\Core\Singleton;

/**
 * Class Blocks
 *
 * Handles the registration of Gutenberg blocks for the plugin.
 *
 * @package Manzur\TestPlugin\App
 */
class Blocks extends Singleton {

	/**
	 * Initialize hooks for registering blocks.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Register the Gutenberg blocks.
	 *
	 * Registers the blocks using the built files from the specified directory.
	 */
	public function register_blocks() {
		register_block_type( MANZUR_PLUGINTEST_DIR . 'build' );
	}

	/**
	 * Enqueue block editor assets and localize script with nonce.
	 */
	public function enqueue_block_editor_assets() {
		$nonce = wp_create_nonce('manzur_ajax_nonce');
		wp_localize_script('manzur-api-data-table-editor-script', 'manzurSettings', array(
			'nonce' => $nonce,
		));
	}
}
