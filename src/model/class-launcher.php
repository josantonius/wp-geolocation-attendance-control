<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GAC\Model;

use Eliasis\Framework\App;
use Eliasis\Framework\Model;

/**
 * Main plugin launcher model.
 */
class Launcher extends Model {


	/**
	 * Create database tables.
	 */
	public function create_tables() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . 'g_a_c_centres';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
			$sql = "CREATE TABLE $table_name (
	          id mediumint(9) NOT NULL AUTO_INCREMENT,
	          centre varchar(255) NOT NULL,
	          latitude varchar(255) NOT NULL,
	          longitude varchar(255) NOT NULL,
	          address  varchar(255) NOT NULL,
	          place_id varchar(255)  NOT NULL,
	          state mediumint(1) NOT NULL,
	          updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
	          PRIMARY KEY  (id)
	        ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );
		}

		$table_name = $wpdb->prefix . 'g_a_c_activities';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
			$sql = "CREATE TABLE $table_name (
	          id mediumint(9) NOT NULL AUTO_INCREMENT,
	          centre_id mediumint(9) NOT NULL,
	          activity varchar(255) NOT NULL,
	          start_hour varchar(255) NOT NULL,
	          end_hour varchar(255) NOT NULL,
	          days varchar(30) NOT NULL,
	          months varchar(30) NOT NULL,
	          state mediumint(1) NOT NULL,
	          updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
	          PRIMARY KEY  (id)
	        ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );
		}

		$table_name = $wpdb->prefix . 'g_a_c_attendances';

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
			$sql = "CREATE TABLE $table_name (
	          id mediumint(9) NOT NULL AUTO_INCREMENT,
	          user_id mediumint(9) NOT NULL,
	          activity_id mediumint(9) NOT NULL,
	          action varchar(15) NOT NULL,
	          latitude varchar(255) NOT NULL,
	          longitude varchar(255) NOT NULL,
	          address  varchar(255) NOT NULL,
	          place_id varchar(255) NOT NULL,
	          hour_status mediumint(1) NOT NULL,
	          action_status mediumint(1) NOT NULL,
	          meters_apart int(255) NOT NULL,
              user_ip varchar(255) NOT NULL,
	          date TIMESTAMP DEFAULT CURRENT_TIMESTAMP  NOT NULL,
	          PRIMARY KEY  (id)
	        ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );
		}
	}

	/**
	 * Remove database tables.
	 */
	public function remove_tables() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'efg_custom_rating';
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}

	/**
	 * Set plugin options.
	 *
	 * @uses get_option()    → option value based on an option name.
	 * @uses add_option()    → add a new option to WordPress options.
	 * @uses update_option() → update a named option/value.
	 */
	public function set_options() {

		$slug = App::GAC()->getOption( 'slug' );

		$actual_version    = App::GAC()->getOption( 'version' );
		$installed_version = get_option( $slug ) . '-version';

		if ( ! $installed_version ) {
			add_option( $slug . '-version', $actual_version );
			add_option( $slug . '-geolocation-attendance-control', [ 4 ] );
		} else {
			if ( $installed_version < $actual_version ) {
				update_option( $slug . '-version', $actual_version );
			}
		}
	}
}
