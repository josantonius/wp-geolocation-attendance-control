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
class Places extends Model {


	/**
	 * Model constructor.
	 */
	protected function __construct() {

		$json_path = App::GAC()->getOption( 'path', 'json' );
		$file      = App::GAC()->getOption( 'file', 'settings' );

		$this->filepath = $json_path . $file;
	}

	/**
	 * Add or update vote and associate to an IP address.
	 *
	 * @since 1.0.1
	 *
	 * @param string $post_id → post id.
	 * @param array  $votes  → votes.
	 * @param array  $vote   → vote.
	 * @param array  $ip     → ip.
	 *
	 * @return array → movie votes
	 */
	public function insert_centre( $centre, $latitude, $longitude, $address, $place_id, $state ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_centres';

		$response = $wpdb->insert(
			$table_name,
			[
				'centre' => $centre,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'address' => $address,
				'place_id' => $place_id,
				'state' => $state,
			],
			[ '%s', '%s', '%s', '%s', '%s', '%d' ]
		);

		return $response ? $wpdb->insert_id : false;
	}

	/**
	 * Add or update vote and associate to an IP address.
	 *
	 * @since 1.0.1
	 *
	 * @param string $post_id → post id.
	 * @param array  $votes  → votes.
	 * @param array  $vote   → vote.
	 * @param array  $ip     → ip.
	 *
	 * @return array → movie votes
	 */
	public function update_centre( $id, $centre, $latitude, $longitude, $address, $place_id, $state ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_centres';

		$response = $wpdb->update(
			$table_name,
			[
				'centre' => $centre,
				'latitude' => $latitude,
				'longitude' => $longitude,
				'address' => $address,
				'place_id' => $place_id,
				'state' => $state,
			],
			[ 'id' => $id ],
			[ '%s', '%s', '%s', '%s', '%s', '%d' ],
			[ '%d' ]
		);

		return $response;
	}

	/**
	 * Add or update vote and associate to an IP address.
	 *
	 * @since 1.0.1
	 *
	 * @param string $post_id → post id.
	 * @param array  $votes  → votes.
	 * @param array  $vote   → vote.
	 * @param array  $ip     → ip.
	 *
	 * @return array → movie votes
	 */
	public function delete_centre( $id ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_centres';

		return $wpdb->delete(
			$table_name,
			[
				'id' => $id,
			],
			[ '%s' ]
		);
	}

	/**
	 * Add or update vote and associate to an IP address.
	 *
	 * @since 1.0.1
	 *
	 * @param string $post_id → post id.
	 * @param array  $votes  → votes.
	 * @param array  $vote   → vote.
	 * @param array  $ip     → ip.
	 *
	 * @return array → movie votes
	 */
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

	/**
	 * Add or update vote and associate to an IP address.
	 *
	 * @since 1.0.1
	 *
	 * @param string $post_id → post id.
	 * @param array  $votes  → votes.
	 * @param array  $vote   → vote.
	 * @param array  $ip     → ip.
	 *
	 * @return array → movie votes
	 */
	public function select_centres() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'g_a_c_centres';

		$result = $wpdb->get_results(
			"
            SELECT * 
            FROM   $table_name
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
