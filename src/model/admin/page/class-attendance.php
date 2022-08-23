<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GAC\Model\Admin\Page;

use Eliasis\Framework\App;
use Josantonius\Json\Json;
use Eliasis\Framework\Model;

/**
 * Model class.
 */
class Attendance extends Model {


	/**
	 * Model constructor.
	 */
	protected function __construct() {

		$json_path = App::GAC()->getOption( 'path', 'json' );
		$file      = App::GAC()->getOption( 'file', 'settings' );

		$this->filepath = $json_path . $file;
	}

	public function insert_attendance( $user_id, $activity_id, $action, $latitude, $longitude, $address, $place_id, $hour_status, $action_status, $meters_apart, $user_ip ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_attendances';

		$response = $wpdb->insert(
			$table_name,
			[
				'user_id' => $user_id,
				'activity_id' => $activity_id,
				'action' => $action,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'address' => $address,
				'place_id' => $place_id,
				'hour_status' => $hour_status,
				'action_status' => $action_status,
				'meters_apart' => $meters_apart,
				'user_ip' => $user_ip,
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s' ]
		);

		return $response ? $wpdb->insert_id : false;
	}

	public function delete_attendance( $id ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_attendances';

		return $wpdb->delete(
			$table_name,
			[
				'id' => $id,
			],
			[ '%s' ]
		);
	}

	public function delete_attendances_by_activity_id( $id ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_attendances';

		return $wpdb->delete(
			$table_name,
			[
				'activity_id' => $id,
			],
			[ '%d' ]
		);
	}

	public function select_attendances( $from, $to ) {

		global $wpdb;

		$attendances_table = $wpdb->prefix . 'g_a_c_attendances';
		$centres_table     = $wpdb->prefix . 'g_a_c_centres';
		$activities_table  = $wpdb->prefix . 'g_a_c_activities';

		$result = $wpdb->get_results(
			"
            SELECT attendances.id,
            	   attendances.user_id,
            	   attendances.activity_id,
            	   attendances.action,
            	   attendances.latitude,
            	   attendances.longitude,
            	   attendances.address,
            	   attendances.place_id,
            	   attendances.hour_status,
            	   attendances.action_status,
            	   attendances.meters_apart,
                   attendances.user_ip,
            	   attendances.date,
            	   centres.centre AS centre_name,
            	   centres.address AS centre_address,
            	   activities.activity AS activity_name

            FROM $attendances_table attendances
            INNER JOIN $activities_table activities
            ON attendances.activity_id = activities.id
            INNER JOIN $centres_table centres
            ON activities.centre_id = centres.id
            WHERE attendances.date BETWEEN '" . $from . "' AND  '" . $to . "'
        "
		);

		return $result;
	}

	/**
	 * Get settings.
	 *
	 * @return array → settings
	 */
	public function get_settings() {

		return Json::fileToArray( $this->filepath );
	}

	/**
	 * Set settings.
	 *
	 * @param array $options → options.
	 *
	 * @return boolean
	 */
	public function set_settings( $options ) {

		return Json::arrayToFile( $options, $this->filepath );
	}
}
