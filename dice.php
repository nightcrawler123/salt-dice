<?php
/**
Plugin Name: Salt Dice
Plugin URI: https://abhijeet.dev/
Description: A plugin that changes WordPress Authentication Unique Keys and Salts to enhance and strengthen WordPress security.
Version: 1.0.0
Author: Abhijeet
Author URI: https://abhijeet.dev/
License: GPLv3 or later
Text Domain: salt-dice
Domain Path: /languages
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
Copyright 2021 Abhijeet Verma.
 */

include_once(plugin_dir_path(__FILE__) . "_inc/loader.php");
$salt_dice = new Saltpepper();

function salt_dice_load_plugin_textdomain() {
    load_plugin_textdomain( 'salt-dice', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'salt_dice_load_plugin_textdomain' );

/**
 * Add a link to the settings page on the plugins.php page.
 *
 * @param $actions
 * @param $plugin_file
 *
 * @return array         List of modified plugin action links.
 * @since 1.0.0
 *
 */

function salt_dice_settings_link( $actions, $plugin_file ) {
    static $plugin;

    if ( ! isset( $plugin ) ) {
        $plugin = plugin_basename( __FILE__ );
    }
    if ( $plugin == $plugin_file ) {

        $settings  = array( 'settings' => '<a href="' . esc_url( admin_url( '/tools.php?page=salt_dice' ) ) . '">' . __( 'Settings', 'salt-dice' ) . '</a>' );
        $site_link = array( 'support' => '<a href="' . esc_url( 'https://www.buymeacoffee.com/abhi78' ) . '" style="color:#0eb804;">' . __( 'Buy Me a Coffee!', 'salt-dice' ) . '</a>' );

        $actions = array_merge( $settings, $actions );
        $actions = array_merge( $site_link, $actions );

    }

    return $actions;
}
add_filter( 'plugin_action_links', 'salt_dice_settings_link', 10, 5 );