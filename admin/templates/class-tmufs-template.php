<?php
if ( isset( $_GET['max-size-updated'] ) ) : ?>
	<div class="notice-success notice is-dismissible">
		<p><?php esc_html_e( 'Congratulations, Maximum Upload File Size Changed Successfully!', 'tmufs' ); ?></p>
	</div>
	<?php
endif;

$max_size = get_option( 'max_file_size' );
if ( ! $max_size ) {
	$max_size = 64 * 1024 * 1024;
}
$max_size                 = $max_size / 1024 / 1024;
$upload_sizes             = [ 16, 32, 64, 128, 256, 512, 1024, 2048 ];
$current_max_size         = self::get_closest( $max_size, $upload_sizes );
$tmufs_max_execution_time = get_option( 'tmufs_maximum_execution_time' ) != '' ? get_option( 'tmufs_maximum_execution_time' ) : ini_get( 'max_execution_time' );
?>

<div class="tmufs-wrapper">
	<h1><?php echo esc_html_e( 'Themx Maximum Upload File Size', 'tmufs' ); ?></h1><br>
	<div class="tmufs-dashboard">
		<div class="tmufs-row" id="poststuff">
			<div class="tmufs-status-cards">
			<?php foreach ( $system_status as $value ) : ?>
			<div class="tmufs-card">
				<h3><?php printf( '%s', esc_html( $value['title'] ) ); ?></h3>
				<?php if ( 1 == $value['status'] ) : ?>
					<div class="tmufs-success">
						<p class="tmufs-size"> 
							<span class="dashicons dashicons-yes"></span>
							<?php printf( '%s', esc_html( $value['size'] ) ); ?>
						</p>
					</div>
				<?php else : ?>
					<div class="tmufs-warning">
						<p class="tmufs-size">
							<span class="dashicons dashicons-warning"></span>
							<?php printf( '%s', esc_html( $value['size'] ) ); ?>
						</p>

						<span class="recommendation"><?php echo esc_html( $value['error_message'] ); ?></span>
					</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
			</div>

			<div class="tmufs-admin-form">
				<form method="post">
					<div class="tmufs-form-table">
						<div class="tmufs-form-fields tmufs-max-file-size">
							<div class="tmufs-label">
								<label for="tmufs_max_file_size_field"><?php esc_html_e( 'Choose Maximum Upload File Size', 'tmufs' ); ?></label>
							</div>
							<div class="tmufs-field">
								<select id="tmufs_max_file_size_field" name="tmufs_max_file_size_field"> 
								<?php
								foreach ( $upload_sizes as $size ) :
									echo '<option value="' . esc_attr( $size ) . '" ' . ( $size == $current_max_size ? 'selected' : '' ) . '>' . ( esc_html( $size ) . 'MB' ) . '</option>';
								endforeach;
								?>
								</select>
							</div>
						</div>

						<div class="tmufs-form-fields tmufs-max-execution-time">
							<div class="tmufs-label">
								<label for="tmufs_max_execution_time_field"><?php esc_html_e( 'Maximum Execution Time', 'tmufs' ); ?></label>
							</div>
							<div class="tmufs-field">
								<input name="tmufs_maximum_execution_time" id="" type="number" value="<?php echo esc_html( $tmufs_max_execution_time ); ?>">
								<br><small><?php esc_html_e( 'Example: 300, 600, 1800, 3600', 'tmufs' ); ?></small>
							</div>
						</div>
					</div>
					<?php wp_nonce_field( 'upload_max_file_size_action', 'upload_max_file_size_nonce' ); ?>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
	</div>
</div>

