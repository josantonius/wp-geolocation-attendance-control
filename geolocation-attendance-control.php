<?php
/**
 * Geolocation Attendance Control.
 *
 * Plugin Name: Geolocation Attendance Control
 * Plugin URI:  https://github.com/josantonius/wp-geolocation-attendance-control.git
 * Description: Geolocation Attendance Control.
 * Version:     1.0.0
 * Author:      Josantonius
 * Author URI:  https://josantonius.dev/
 * License:     MIT
 * Text Domain: geolocation-attendance-control
 */

use Eliasis\Framework\App;

/**
 * Don't expose information if this file called directly.
 */
if ( ! function_exists( 'add_action' ) || ! defined( 'ABSPATH' ) ) {

	echo 'I can do when called directly.';
	die;
}

/**
 * Class loader.
 */
require 'vendor/autoload.php';

/**
 * Start application.
 */
App::run( __DIR__, 'wordpress-plugin', 'GAC' );

/**
 * Get main instance.
 */
$launcher = App::getControllerInstance( 'Launcher', 'controller' );

/**
 * Register hooks.
 */
register_activation_hook( __FILE__, [ $launcher, 'activation' ] );

register_deactivation_hook( __FILE__, [ $launcher, 'deactivation' ] );

/**
 * Launch application.
 */
$launcher->init();
