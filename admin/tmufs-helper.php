<?php
// Read plugin header data.
$tmufs_plugin_data = get_plugin_data( TMUFS_PLUGIN_URL );

/**
 * Check minimum upload size set by WordPress
 */
function tmufs_wp_minimum_upload_file_size() {
	$wp_size = wp_max_upload_size();
	if ( ! $wp_size ) {
		$wp_size = 'unknown';
	} else {
		$wp_size = round( $wp_size / 1024 / 1024 ) . 'MB';
	}

	return $wp_size;
}

/**
 * Check minimum upload size set by Hosting Provider
 */
function tmufs_wp_upload_size_by_from_hosting() {
	$ini_size = ini_get( 'upload_max_filesize' );
	if ( ! $ini_size ) {
		$ini_size = 'unknown';
	} elseif ( is_numeric( $ini_size ) ) {
		$ini_size .= ' bytes';
	} else {
		$ini_size .= 'B';
	}

	return $ini_size;
}

/**
 * Convert to bytes from different units.
 *
 * @param string $from KB,MB etc.
 */
function tmufs_convert_to_bytes( string $from ): ?int {
	$units  = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB' ];
	$number = substr( $from, 0, - 2 );
	$suffix = strtoupper( substr( $from, - 2 ) );

	// B or no suffix.
	if ( is_numeric( substr( $suffix, 0, 1 ) ) ) {
		return preg_replace( '/[^\d]/', '', $from );
	}

	$exponent = array_flip( $units )[ $suffix ] ?? null;
	if ( null === $exponent ) {
		return null;
	}

	return $number * ( 1024 ** $exponent );
}

// WordPress minimum upload size .
$tmufs_wp_minimum_upload_file_size = '40MB';

// Minimum WordPress upload size.
$tmufs_wp_upload_size_status = tmufs_convert_to_bytes( tmufs_wp_minimum_upload_file_size() ) < tmufs_convert_to_bytes( $tmufs_wp_minimum_upload_file_size ) ? 0 : 1;

// Minimum upload file size from hosting provider.
$tmufs_wp_upload_size_status_from_hosting = tmufs_convert_to_bytes( tmufs_wp_upload_size_by_from_hosting() ) < tmufs_convert_to_bytes( $tmufs_wp_minimum_upload_file_size ) ? 0 : 1;

// PHP Limit Time.
$tmufs_php_minimum_limit_time = '120';
$tmufs_php_current_limit_time = ini_get( 'max_execution_time' );
$tmufs_php_limit_time_status  = $tmufs_php_minimum_limit_time <= $tmufs_php_current_limit_time ? 1 : 0;

$system_status = [
	[
		'title'         => __( 'Maximum Upload Limit set by WordPress', 'tmufs' ),
		'size'          => tmufs_wp_minimum_upload_file_size(),
		'status'        => $tmufs_wp_upload_size_status,
		'error_message' => __( 'Recommended : ', 'tmufs' ) . $tmufs_wp_minimum_upload_file_size,
	],

	[
		'title'         => __( 'Maximum Upload Limit Set By Hosting Provider', 'tmufs' ),
		'size'          => tmufs_wp_upload_size_by_from_hosting(),
		'status'        => $tmufs_wp_upload_size_status_from_hosting,
		'error_message' => __( 'Recommended :  ', 'tmufs' ) . $tmufs_wp_minimum_upload_file_size,
	],

	[
		'title'         => __( 'PHP Maximum Execution time', 'tmufs' ),
		'size'          => $tmufs_php_current_limit_time . __( ' seconds', 'tmufs' ),
		'status'        => $tmufs_php_limit_time_status,
		'error_message' => __( 'Recommended : ', 'tmufs' ) . $tmufs_php_minimum_limit_time . __( ' seconds', 'tmufs' ),
	],
];
