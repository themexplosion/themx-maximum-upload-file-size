<?php
if ( isset( $_GET['max-size-updated'] ) ) { ?>
	<div class="notice-success notice is-dismissible">
		<p><?php esc_html_e( 'Congratulations, Maximum Upload File Size Changed Successfully!', 'tmufs' ); ?></p>
	</div>
	<?php
}

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
			<!-- System Status Table -->
			<table class="tmufs-system-info">
				<tr>
					<th><?php esc_html_e( 'Title', 'tmufs' ); ?></th>
					<th><?php esc_html_e( 'Status', 'tmufs' ); ?></th>
					<th><?php esc_html_e( 'Message', 'tmufs' ); ?></th>
				</tr>

				<?php foreach ( $system_status as $value ) : ?>
				<tr>
					<td><?php printf( '%s', esc_html( $value['title'] ) ); ?></td>

					<td>
						<?php if ( 1 == $value['status'] ) : ?>
							<span class="dashicons dashicons-yes"></span>
						<?php else : ?>
							<span class="dashicons dashicons-warning"></span>
						<?php endif; ?>
					</td>
					<td>
						<?php if ( 1 == $value['status'] ) { ?>
							<p class="tmufs-status-messsage"> 
								<?php
								printf( '%s', esc_html( $value['size'] ) ); ?> <?php echo $value['success_message']; //phpcs:ignore ?>
								</p>
						<?php } else { ?>
							<?php printf( '%s', esc_html( $value['size'] ) ); ?>
							<p class="tmufs-status-messsage"><?php echo $value['error_message']; //phpcs:ignore ?></p>

						<?php } ?>

					</td>
				</tr>
				<?php endforeach; ?>
			</table>
				<!-- System Status Ends -->

			<!-- Start Content Area -->
			<div class="tmufs-admin-form">
				<form method="post">
					<table class="form-table">
						<tbody>
						<tr>
							<th scope="row"><label for="upload_max_file_size_field">Choose Maximum Upload File Size</label></th>
							<td>
								<select id="upload_max_file_size_field" name="upload_max_file_size_field"> 
								<?php
								foreach ( $upload_sizes as $size ) {
									echo '<option value="' . esc_attr( $size ) . '" ' . ( $size == $current_max_size ? 'selected' : '' ) . '>' . ( $size . 'MB' ) . '<option>';
								}
								?>
								</select>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="upload_max_file_size_field">Maximum Execution Time</label></th>
							<td>
								<input name="tmufs_maximum_execution_time" type="number" value="<?php echo esc_html( $tmufs_max_execution_time ); ?>">
								<br><small>Example: 300, 600, 1800, 3600</small>
							</td>
						</tr>

						</tbody>
					</table>
					<?php wp_nonce_field( 'upload_max_file_size_action', 'upload_max_file_size_nonce' ); ?>
					<?php submit_button(); ?>
				</form>
			</div>
			<!-- End Content Area -->
		</div> <!-- End Row--->
	</div>
</div> <!-- End Wrapper -->

