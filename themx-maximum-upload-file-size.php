<?php
/**
 * Plugin Name:       Themx Maximum Upload File Size
 * Plugin URI:        https://wordpress.org/plugins/themx-maximum-upload-file-size/
 * Description:       Increase maximum upload file size easily.
 * Version:           1.0.0
 * Author:            Themexplosion
 * Author URI:        https://profiles.wordpress.org/themexplosion/
 * License:           GPL v2 or later
 * Text Domain:       tmufs
 * Requires at least: 4.0
 * Tested up to: 6.0
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
		$this->time_limit();

		add_action( 'plugin_loaded', [ $this, 'load_plugin_textdomain' ] );
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
		define( 'TMUFS_PLUGIN_VERSION', get_file_data( TMUFS_PLUGIN_FILE, [ 'version' => 'Version' ], 'plugin' )['version'] );
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
	 * Increase maximum execution time limit, default 600
	 *
	 * @return void
	 */
	public function time_limit() {
		$tmufs_get_max_execution_time = get_option( 'tmufs_maximum_execution_time' ) != '' ? get_option( 'tmufs_maximum_execution_time' ) : ini_get( 'max_execution_time' );
		set_time_limit( $tmufs_get_max_execution_time );
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
