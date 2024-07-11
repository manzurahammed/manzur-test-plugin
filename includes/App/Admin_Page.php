<?php

namespace MANZUR\TestPlugin\App;

use MANZUR\TestPlugin\Core\Singleton;

/**
 * Class Admin_Page
 *
 * Manages the admin page for displaying and refreshing API data.
 *
 * @package Manzur\TestPlugin\App
 */
class Admin_Page extends Singleton {

	/**
	 * Initialize hooks for the admin page.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
		add_action( 'admin_post_manzur_refresh_api_data', array( $this, 'refresh_api_data' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueue admin styles.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'manzur-test-plugin-admin-css', MANZUR_PLUGINTEST_ASSETS_URL . '/css/admin.css' );
	}

	/**
	 * Handle the API data refresh request.
	 *
	 * Validates the nonce and refreshes the API data. Redirects with a status message.
	 */
	public function refresh_api_data() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'manzur_post_nonce' ) ) {
			$this->redirect_with_message( 'failed' );
		}

		Data_Manager::refresh_data();
		$this->redirect_with_message( 'success' );
	}

	/**
	 * Redirects with a refresh status message.
	 *
	 * @param string $status The status message to append to the URL.
	 */
	private function redirect_with_message( $status ) {
		$redirect_url = add_query_arg( 'refresh', $status, wp_get_referer() );
		wp_redirect( esc_url_raw( $redirect_url ) );
		exit;
	}

	/**
	 * Register the admin page in the WordPress dashboard.
	 */
	public function register_admin_page() {
		add_menu_page(
			__( 'API Data', 'manzur-test-plugin' ),
			__( 'API Data', 'manzur-test-plugin' ),
			'manage_options',
			'manzur-test-plugin-data-table',
			array( $this, 'display_api_data' ),
			'dashicons-admin-generic'
		);
	}

	/**
	 * Display the API data on the admin page.
	 */
	public function display_api_data() {
		$data = Data_Manager::get_data();
		$url  = admin_url( 'admin-post.php' );

		if ( empty( $data['data'] ) ) {
			$this->display_notice( 'error', __( 'No data found.', 'manzur-test-plugin' ) );
			return;
		}

		?>
		<div class="wrap manzur-test-plugin">
			<div class="manzur-test-plugin-table-title">
				<h1><?php echo esc_html( $data['title'] ); ?></h1>
				<?php $this->display_refresh_message(); ?>
				<form method="post" action="<?php echo esc_url( $url ); ?>" class="refresh-form">
					<input type="hidden" name="action" value="manzur_refresh_api_data">
					<input type="hidden" name="nonce"
							value="<?php echo esc_attr( wp_create_nonce( 'manzur_post_nonce' ) ); ?>">
					<?php submit_button( __( 'Refresh Data', 'manzur-test-plugin' ), 'primary', 'refresh_data', false, array( 'id' => 'my-button' ) ); ?>
				</form>
			</div>
			<?php if ( empty( $data['data']['rows'] ) ) : ?>
				<div class="notice notice-error">
					<p><?php _e( 'No data found.', 'manzur-test-plugin' ); ?></p>
				</div>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
					<tr>
						<?php foreach ( $data['data']['headers'] as $header_name ) : ?>
							<th><?php echo esc_html( $header_name ); ?></th>
						<?php endforeach; ?>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $data['data']['rows'] as $item ) : ?>
						<tr>
							<?php foreach ( $item as $value ) : ?>
								<td><?php echo esc_html( $value ); ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Display refresh status message.
	 */
	private function display_refresh_message() {
		if ( isset( $_GET['refresh'] ) ) {
			$refresh_status = sanitize_text_field( $_GET['refresh'] );
			if ( 'success' === $refresh_status ) {
				$this->display_notice( 'success', __( 'Data refreshed successfully.', 'manzur-test-plugin' ) );
			} elseif ( 'failed' === $refresh_status ) {
				$this->display_notice( 'error', __( 'Nonce verification failed. Please try again.', 'manzur-test-plugin' ) );
			}
		}
	}

	/**
	 * Display a notice message.
	 *
	 * @param string $type The type of notice (success, error).
	 * @param string $message The message to display.
	 */
	private function display_notice( $type, $message ) {
		?>
		<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible">
			<p><?php echo esc_html( $message ); ?></p>
		</div>
		<?php
	}
}
