<?php
	$config_file_checker = new SalterMatrix();
	$is_config           = $config_file_checker->config_file_path();
	$salts_file_name     = apply_filters( 'salt_dice_salts_file', 'wp-config' );
	if ( ! $is_config ) {
		printf(
		/* translators: 1: wp-config.php 2: https://codex.wordpress.org/Changing_File_Permissions */
			__( 'The file <code>%1$s</code> which contains your salt keys is not writable. First, make sure it exists then read how to setup the correct permissions on <a href="%2$s">WordPress codex</a>.', 'salt-dice' ),
			$salts_file_name . '.php',
			'https://wordpress.org/support/article/changing-file-permissions/'
		);
	} else {
		?>
		<div class="salt_dice_inner_settings">
			<div>
				<div>
					<p style="color:red; font-weight: bold"><?php esc_html_e( 'Changing WP Keys and Salts will force all logged-in users to login again.', 'salt-dice' ) ?></p>
					<h3><?php esc_html_e( 'Scheduled Change:', 'salt-dice' ) ?></h3>
					<?php if ( get_option( "salt_dice_autoupdate_enabled" ) == "true" ) {
						$next_schedule = date_i18n( get_option( 'date_format' ), wp_next_scheduled( 'salt_dice_change_salts' ) );
						?>
						<p style="color:green; font-weight: bold">
							<?php printf( __( 'The salt keys will be automatically changed on %s', 'salt-dice' ), $next_schedule ); ?>
						</p>
						<?php
					}
					?>
					<p> <?php esc_html_e( 'Set scheduled job for automated Salt changing:', 'salt-dice' ) ?></p>
					<input type="checkbox"
					       id="schedualed_salt_changer" <?php echo( get_option( "salt_dice_autoupdate_enabled" ) == "true" ? "checked" : "" ); ?> />
					<label><?php esc_html_e( 'Change WP Keys and Salts on', 'salt-dice' ) ?></label>
					<?php wp_nonce_field( 'salt-dice_save-salt-schd', '_ssnonce_scheduled' ); ?>
					<select id="schedualed_salt_value">
						<option value="daily" <?php echo( get_option( "salt_dice_update_interval" ) == "daily" ? "selected" : "" ); ?>><?php esc_html_e( 'Daily', 'salt-dice' ) ?></option>
						<option value="weekly" <?php echo( get_option( "salt_dice_update_interval" ) == "weekly" ? "selected" : "" ); ?>><?php esc_html_e( 'Weekly', 'salt-dice' ) ?></option>
						<option value="monthly" <?php echo( get_option( "salt_dice_update_interval" ) == "monthly" ? "selected" : "" ); ?>><?php esc_html_e( 'Monthly', 'salt-dice' ) ?></option>
						<option value="quarterly" <?php echo( get_option( "salt_dice_update_interval" ) == "quarterly" ? "selected" : "" ); ?>><?php esc_html_e( 'Quarterly', 'salt-dice' ) ?></option>
						<option value="biannually" <?php echo( get_option( "salt_dice_update_interval" ) == "biannually" ? "selected" : "" ); ?>><?php esc_html_e( 'Biannually', 'salt-dice' ) ?></option>
					</select>
					<?php esc_html_e( 'Basis.', 'salt-dice' ) ?>
				</div>
				<div>
					<h3><?php esc_html_e( 'Immediate Change:', 'salt-dice' ) ?></h3>
					<p class="keys_updated_message" style="display: none; color:green; font-weight: bold">
						<?php esc_html_e( "Keys have been updated, you'll be redirected to the login page in a few seconds.", 'salt-dice' ) ?>
					</p>
					<p><?php esc_html_e( 'When you click the following button, WP keys and salts will change immediately. And you will need to login again.', 'salt-dice' ) ?></p>

					<input type="button" id="change_salts_now" name="change_salts_now" class="button button-primary"
					       value="<?php esc_attr_e( 'Change Now', 'salt-dice' ) ?>"/>
					<?php wp_nonce_field( 'salt-dice_change-salts-now', '_ssnonce_now' ); ?>
					<div class="spinner" id="saving_spinner"></div>
				</div>
			</div>
		</div>
	<?php } ?>
