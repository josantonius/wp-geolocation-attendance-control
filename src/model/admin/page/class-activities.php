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
class Activities extends Model {


	/**
	 * Model constructor.
	 */
	protected function __construct() {

		$json_path = App::GAC()->getOption( 'path', 'json' );
		$file      = App::GAC()->getOption( 'file', 'settings' );

		$this->filepath = $json_path . $file;
	}

	public function insert_activity( $centre_id, $activity, $start_hour, $end_hour, $days, $months, $state ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_activities';

		$response = $wpdb->insert(
			$table_name,
			[
				'centre_id' => (int) $centre_id,
				'activity' => $activity,
				'start_hour' => $start_hour,
				'end_hour' => $end_hour,
				'days' => $days,
				'months' => $months,
				'state' => (int) $state,
			],
			[ '%d', '%s', '%s', '%s', '%s', '%s', '%d' ]
		);

		return $response ? $wpdb->insert_id : false;
	}

	public function update_activity( $id, $centre_id, $activity, $start_hour, $end_hour, $days, $months, $state ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_activities';

		$response = $wpdb->update(
			$table_name,
			[
				'centre_id' => (int) $centre_id,
				'activity' => $activity,
				'start_hour' => $start_hour,
				'end_hour' => $end_hour,
				'days' => $days,
				'months' => $months,
				'state' => (int) $state,
			],
			[ 'id' => $id ],
			[ '%d', '%s', '%s', '%s', '%s', '%s', '%d' ],
			[ '%d' ]
		);

		return $response;
	}

	public function delete_activity( $id ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_activities';

		return $wpdb->delete(
			$table_name,
			[
				'id' => (int) $id,
			],
			[ '%d' ]
		);
	}

	public function delete_activities_by_centre_id( $id ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_activities';

		return $wpdb->delete(
			$table_name,
			[
				'centre_id' => $id,
			],
			[ '%d' ]
		);
	}

	public function select_centre( $id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_centres';

		$result = $wpdb->get_results(
			"
            SELECT * 
            FROM  $table_name
            WHERE id = $id
        "
		);

		return $result;
	}

	public function center_has_activities( $id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_activities';

		$result = $wpdb->get_results(
			"
            SELECT * 
            FROM  $table_name
            WHERE centre_id = $id
            LIMIT 1
        "
		);

		return [] !== $result;
	}

	public function select_all_centres() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_centres';

		$result = $wpdb->get_results(
			"
            SELECT id, centre
            FROM   $table_name
        "
		);

		return $result;
	}

	public function select_activities( $only_actives = false ) {

		global $wpdb;

		$activities_table = $wpdb->prefix . 'g_a_c_activities';
		$centres_table    = $wpdb->prefix . 'g_a_c_centres';

		$query = "
            SELECT activities.id,
            	   activities.centre_id,
            	   activities.activity,
            	   activities.start_hour,
            	   activities.end_hour,
            	   activities.days,
            	   activities.months,
            	   activities.state,
            	   centres.centre

            FROM $activities_table activities
            INNER JOIN $centres_table centres
            ON centres.id = activities.centre_id
        ";

		$query .= $only_actives ? ' WHERE centres.state = 1 AND activities.state = 1' : '';

		$result = $wpdb->get_results( $query );

		return $result;
	}

	public function select_activity( $id ) {

		global $wpdb;

		$activities_table = $wpdb->prefix . 'g_a_c_activities';
		$centres_table    = $wpdb->prefix . 'g_a_c_centres';

		$result = $wpdb->get_results(
			"
            SELECT activities.id,
            	   activities.centre_id,
            	   activities.activity,
            	   activities.start_hour,
            	   activities.end_hour,
            	   activities.days,
            	   activities.months,
            	   activities.state,
            	   centres.place_id AS centre_place_id,
            	   centres.centre AS centre_name,
            	   centres.address AS centre_address

            FROM $activities_table activities
            INNER JOIN $centres_table centres
            ON centres.id = activities.centre_id
            WHERE activities.id = $id
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
