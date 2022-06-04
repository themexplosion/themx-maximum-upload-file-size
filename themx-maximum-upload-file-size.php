<?php
/**
 * Plugin Name:       Themx Maximum Upload File Size
 * Plugin URI:        https://wordpress.org/plugins/themx-maximum-upload-file-size/
 * Description:       Increase maximum upload file size easily.
 * Version:           1.0.0
 * Author:            Themexplosion
 * Author URI:        https://wordpress.org/plugins/themexplosion/
 * License:           GPL v2 or later
 * Text Domain:       tmufs
 * Requires at least: 4.0
 * Tested up to: 5.9
 * Requires PHP: 5.6
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit();

/**
* Plugin Main Class
*/
final class Tmufs {
	private function __construct() {
		$this->define_constants();
		$this->include_files();

		add_action( 'plugin_loaded', [ $this, 'load_plugin_textdomain' ] );
		add_action( 'wp_ajax_tmufs_admin_notice_ajax_object_save', 'tmufs_admin_notice_ajax_object_callback' );
	}

	/**
	 * Define the necessary constant for this plugin
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'TMUFS_PLUGIN_FILE', __FILE__ );
		define( 'TMUFS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'TMUFS_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'TMUFS_PLUGIN_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
		define( 'TMUFS_PLUGIN_VERSION', '1.0.0' );
	}

	/**
	 * Load necessary files to run this plugin
	 *
	 * @return void
	 */
	public function include_files() {
		include_once TMUFS_PLUGIN_PATH . 'admin/class-tmufs-admin.php';
	}

	/**
	 * Load plugin textdomain for translation
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'tmufs', false, TMUFS_PLUGIN_PATH . '/languages/' );
	}


	/**
	 * Click to hide success message via AJAX
	 *
	 * @return mixed
	 */
	public function tmufs_admin_notice_ajax_object_callback() {

		$data = isset( $_POST['data'] ) ? sanitize_text_field( wp_unslash( $_POST['data'] ) ) : [];

		if ( $data ) {
			// Check valid request form user.
			check_ajax_referer( 'tmufs_notice_status' );

			update_option( 'tmufs_notice_disable_time', strtotime( '+6 Months' ) );

			$response['message'] = 'sucess';
			wp_send_json_success( $response );
		}

		wp_die();
	}


	/**
	 * Initialize the plugin class
	 *
	 * @return \Tmufs
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
}

/**
 * Function to initialize Tmufs plugin
 *
 * @return \TMUFS
 */
function tmufs() {
	return Tmufs::init();
}

// Kick off the plugin.
tmufs();

/**
 * Increase maximum execution time.
 * Default 600.
 */
$tmufs_get_max_execution_time = get_option( 'tmufs_maximum_execution_time' ) != '' ? get_option( 'tmufs_maximum_execution_time' ) : ini_get( 'max_execution_time' );
set_time_limit( $tmufs_get_max_execution_time );
