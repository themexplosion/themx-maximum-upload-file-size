<?php
/**
 * Class to implement admin functions of the plugin
 *
 * @package tmufs
 */
class TMUFS_Admin {

	/**
	 * Function to initialize from this class in init hook
	 *
	 * @return void
	 */
	public static function init() {

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'tmufs_style_and_script' ] );
			add_action( 'admin_menu', [ __CLASS__, 'tmufs_add_pages' ] );
			add_filter( 'plugin_action_links_' . TMUFS_PLUGIN_BASENAME, [ __CLASS__, 'plugin_action_links' ] );
			add_filter( 'plugin_row_meta', [ __CLASS__, 'plugin_meta_links' ], 10, 2 );
			add_filter( 'admin_footer_text', [ __CLASS__, 'admin_footer_text' ] );

			if ( isset( $_POST['tmufs_max_file_size_field'] ) ) {
				$retrieved_nonce = isset( $_POST['upload_max_file_size_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['upload_max_file_size_nonce'] ) ) : '';

				if ( ! wp_verify_nonce( $retrieved_nonce, 'upload_max_file_size_action' ) ) {
					die( 'Are you cheating?' );
				}

				$max_size           = (int) $_POST['tmufs_max_file_size_field'] * 1024 * 1024;
				$max_execution_time = isset( $_POST['tmufs_maximum_execution_time'] ) ? sanitize_text_field( wp_unslash( (int) $_POST['tmufs_maximum_execution_time'] ) ) : '';
				update_option( 'tmufs_maximum_execution_time', $max_execution_time );
				update_option( 'max_file_size', $max_size );
				wp_safe_redirect( admin_url( 'upload.php?page=themx_maximum_upload_file_size&max-size-updated=true' ) );
			}
		}

		add_filter( 'upload_size_limit', [ __CLASS__, 'tmufs_increase_max_upload_size' ] );
	}

	/**
	 * Load Plugin Style and Scripts.
	 *
	 * @return void
	 */
	public static function tmufs_style_and_script() {
		wp_enqueue_style( 'tmufs-admin-style', TMUFS_PLUGIN_URL . 'assets/css/tmufs.min.css', null, TMUFS_PLUGIN_VERSION );

		wp_enqueue_script( 'tmufs-admin', TMUFS_PLUGIN_URL . 'assets/js/admin.js', [ 'jquery' ], TMUFS_PLUGIN_VERSION, true );

		// Ajax admin localization.
		$admin_notice_nonce = wp_create_nonce( 'tmufs_notice_status' );
		wp_localize_script(
			'tmufs-admin',
			'tmufs_admin_notice_ajax_object',
			[
				'tmufs_admin_notice_ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'                       => $admin_notice_nonce,
			]
		);
	}

	/**
	 * Check if it is being loaded on the plugin page.
	 *
	 * @return boolean
	 */
	public static function is_plugin_page() {
		$current_screen = get_current_screen();

		if ( 'media_page_themx_maximum_upload_file_size' == $current_screen->id ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Add settings link under plugin description
	 *
	 * @param array $links Links under plugin description.
	 * @return array New list of links.
	 */
	public static function plugin_action_links( $links ) {
		$settings_link = '<a href="' . admin_url( 'upload.php?page=themx_maximum_upload_file_size' ) . '" title="Adjust Max File Upload Size Settings">Settings</a>';

		array_unshift( $links, $settings_link );

		return $links;
	}


	/**
	 * Add a link in the plugin row meta
	 *
	 * @param array  $links Array of links in plugin row meta.
	 * @param string $file Name of the plugin __FILE__.
	 *
	 * @return string
	 */
	public static function plugin_meta_links( $links, $file ) {
		$support_link = '<a target="_blank" href="https://wordpress.org/support/plugin/themx-maximum-upload-file-size/" title="Get help">Support</a>';

		if ( plugin_basename( TMUFS_PLUGIN_FILE ) == $file ) {
			$links[] = $support_link;
		}

		return $links;
	}


	/**
	 * Add additional admin footer text.
	 *
	 * @param string $text Footer text string.
	 * @return string
	 */
	public static function admin_footer_text( $text ) {
		if ( ! self::is_plugin_page() ) {
			return $text;
		}

		$text = '<span id="footer-thankyou">If you like <strong><ins>Themx Maximum Upload File Size</ins></strong> please leave us a <a target="_blank" style="color:#f9b918" href="https://wordpress.org/support/view/plugin-reviews/themx-maximum-upload-file-size?rate=5#postform">★★★★★</a> rating. A huge thank you in advance!</span>';
		return $text;
	}


	/**
	 * Add submenu page under Media.
	 *
	 * @return void
	 */
	public static function tmufs_add_pages() {
		add_submenu_page(
			'upload.php',
			'Themx Maximum Upload File Size',
			'Increase Maximum Upload Limit',
			'manage_options',
			'themx_maximum_upload_file_size',
			[ __CLASS__, 'tmufs_dashboard' ]
		);
	}

	/**
	 * Get closest output value for maximum file size uploads.
	 *
	 * @param float $search Maximum size value.
	 * @param array $arr Upload sizes.
	 * @return null|int
	 */
	public static function get_closest( $search, $arr ) {
		$closest = null;
		foreach ( $arr as $item ) {
			if ( null === $closest || abs( $search - $closest ) > abs( $item - $search ) ) {
				$closest = $item;
			}
		}
		return $closest;
	}


	/**
	 * Dashboard Page
	 *
	 * @return mixed
	 */
	public static function tmufs_dashboard() {
		include_once TMUFS_PLUGIN_PATH . 'admin/tmufs-helper.php';
		include_once TMUFS_PLUGIN_PATH . 'admin/templates/class-tmufs-template.php';

		add_action( 'admin_head', [ __CLASS__, 'tmufs_remove_admin_action' ] );
	}


	/**
	 * Remove admin notices in admin page.
	 *
	 * @return void
	 */
	public static function tmufs_remove_admin_action() {
		remove_all_actions( 'user_admin_notices' );
		remove_all_actions( 'admin_notices' );
	}

	/**
	 * Filter to increase max_file_size
	 *
	 * @return int max_size in bytes
	 */
	public static function tmufs_increase_max_upload_size() {
		$max_size = (int) get_option( 'max_file_size' );
		if ( ! $max_size ) {
			$max_size = 64 * 1024 * 1024;
		}

		return $max_size;
	}
}

// Instance of class TMUFS_admin.
add_action( 'init', [ 'TMUFS_Admin', 'init' ] );
