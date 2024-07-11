<?php

namespace MANZUR\TestPlugin\Tests;

use WP_UnitTestCase;
use MANZUR\TestPlugin\App\Admin_Page;

class Test_Admin_Page extends WP_UnitTestCase {

	protected $admin_page;

	public function setUp(): void {
		parent::setUp();
		$this->admin_page = Admin_Page::instance();
	}

	public function test_init() {
		$this->admin_page->init();
		$this->assertEquals( 10, has_action( 'admin_menu', [ $this->admin_page, 'register_admin_page' ] ) );
		$this->assertEquals( 10, has_action( 'admin_post_manzur_refresh_api_data', [ $this->admin_page, 'refresh_api_data' ] ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->admin_page, 'enqueue_styles' ] ) );
	}

	public function test_enqueue_styles() {
		// Set up the global function to intercept the enqueue call
		global $wp_styles;
		$wp_styles = new \WP_Styles();

		$this->admin_page->enqueue_styles();
		$this->assertTrue( wp_style_is( 'manzur-test-plugin-admin-css', 'enqueued' ) );
	}
}

