<?php

	class Saltpepper extends SalterMatrix {

		public function __construct() {
			define( 'SALT_dice_DOMAIN', 'salt-dice' );
			add_action( 'admin_menu', array( __CLASS__, 'add_menu_item' ) );
			add_action( 'admin_init', array( __CLASS__, 'add_settings_metabox' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );
			add_action( 'wp_ajax_change_salts_now', array( &$this, 'wp_ajax_change_salts_now' ) );
			add_action( 'wp_ajax_save_salt_schd', array( &$this, 'wp_ajax_save_salt_schd' ) );
			add_action( 'salt_dice_change_salts', array( &$this, 'shuffleSalts' ) );
			add_filter( 'cron_schedules', array( $this, 'cron_time_intervals' ) );  //Adjusting WP Cron
			add_action( 'admin_notices', array( $this, 'salt_dice_warning' ) );

	}

	public static function add_menu_item() {
			add_submenu_page( 'tools.php', __( 'Salt dice Settings', 'salt-dice' ), __( 'Salt dice', 'salt-dice' ), 'manage_options', 'salt_dice', array(
				__CLASS__,
				'admin_page_content',
			) );
		}

		public static function add_settings_metabox() {
			add_meta_box( 'salt_dice_settings_metabox', __( 'Salt Changing Behaviour', 'salt-dice' ), array(
				__CLASS__,
				'metabox_content'
			), 'saltdice', 'normal' );
		}
		
		public static function admin_page_content() {
			include_once( plugin_dir_path( __FILE__ ) . "gui/settings-template.php" );
		}
		
		public static function metabox_content() {
			include_once( plugin_dir_path( __FILE__ ) . "gui/inner-settings-template.php" );
		}
		
		public static function enqueue_admin_scripts() {
			wp_enqueue_script( 'salt_dice_admin', plugin_dir_url( __FILE__ ) . 'gui/js/salt_dice_admin.js', array( "jquery" ) );
		}
		
		public function wp_ajax_change_salts_now() {
			if ( ! $this->check_nonce( '_ssnonce_now', 'salt-dice_change-salts-now' )
			     || ! current_user_can( 'administrator' ) ) {
				wp_die( - 1 );
			}
			do_action( 'salt_dice_change_salts' );
			die( 0 );
		}
		
		public function wp_ajax_save_salt_schd() {
			if ( ! $this->check_nonce( '_ssnonce_scheduled', 'salt-dice_save-salt-schd' )
			     || ! current_user_can( 'administrator' ) ) {
				wp_die( - 1 );
			}
			update_option( "salt_dice_update_interval", $_POST["interval"] );
			update_option( "salt_dice_autoupdate_enabled", $_POST["enabled"] );
			if ( isset( $_POST["enabled"] ) && $_POST["enabled"] == "true" ) {
				// Make sure there's no current jobs before making a new one.
				wp_clear_scheduled_hook( 'salt_dice_change_salts' );
				// Now you can schedule the job.
				wp_schedule_event( time(), $_POST["interval"], "salt_dice_change_salts" );
			} else {
				wp_clear_scheduled_hook( 'salt_dice_change_salts' );
			}
			die( 0 );
		}
		
		/**
		 * Ensure that the request contains a valid nonce.
		 *
		 * This is used to prevent CSRF attacks and must be called
		 * *at the beginning* of any AJAX handler.
		 *
		 * @param string $param
		 * @param string $name
		 *
		 * @return boolean
		 */
		public static function check_nonce( $param = '_wpnonce', $name = 'salt_dice' ) {
			return ( isset( $_POST[ $param ] ) && wp_verify_nonce( $_POST[ $param ], $name ) );
		}
		
		/**
		 * Adding more intervals to WP cron.
		 *
		 * WordPress wp_schedule_event accepts only hourly, twicedaily or daily.
		 * We are adding more intervals.
		 *
		 * @param string $schedules
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		public function cron_time_intervals( $schedules ) {
			$schedules['weekly']     = array(
				'interval' => 604800,
				'display'  => __( 'Weekly' )
			);
			$schedules['monthly']    = array(
				'interval' => 2635200,
				'display'  => __( 'Monthly' )
			);
			$schedules['quarterly']  = array(
				'interval' => 3 * 2635200,
				'display'  => __( 'Every 3 Months' )
			);
			$schedules['biannually'] = array(
				'interval' => 6 * 2635200,
				'display'  => __( 'Every 6 Months' )
			);
			
			return $schedules;
		}
		
		
		/**
		 * A warning message to be shown if the file that contains the salts isn't writable
		 *
		 * @since 1.0.0
		 */
		public function salt_dice_warning() {
			
			$config_file = SalterMatrix::config_file_path();
			if ( ! $config_file && current_user_can( 'administrator' ) ) {
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php
							$url  = esc_url( admin_url( '/tools.php?page=salt_dice' ) );
							$link = sprintf( wp_kses( __( 'Salt dice is not working due to a configuration error. Please visit <a href="%s">the settings page</a> to resolve this error.', 'salt-dice' ),
								array(
									'a' => array(
										'href' => array()
									)
								)
							),
								esc_url( $url ) );
							echo $link;
						?>
					</p>
				</div>
				<?php
			}
		}
	}