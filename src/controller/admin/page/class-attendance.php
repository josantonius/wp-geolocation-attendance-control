<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GAC\Controller\Admin\Page;

use Eliasis\Framework\App;
use Josantonius\Hook\Hook;
use Josantonius\WP_Menu\WP_Menu;
use Eliasis\Framework\Controller;
use Josantonius\WP_Register\WP_Register;

class Attendance extends Controller {

	public $slug = 'geolocation-attendance-control-attendance';

	public $data;

	public function set_menu() {

		WP_Menu::add(
			'menu',
			App::GAC()->getOption( 'menu', 'top-level' ),
			[ $this, 'render' ],
			[ $this, 'add_scripts' ],
			[ $this, 'add_styles' ]
		);
	}

	public function set_submenu() {

		WP_Menu::add(
			'submenu',
			App::GAC()->getOption( 'submenu', 'attendance' ),
			[ $this, 'render' ],
			[ $this, 'add_scripts' ],
			[ $this, 'add_styles' ]
		);
	}

	public function add_scripts() {

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'vuetify' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'moment' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'geolocationAttendanceControlAttendance' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'vueGoogleMaps' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'axios' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'vueGoogleAutocomplete' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'vueGoogleMapsPlacesAggregator' )
		);
	}

	public function add_styles() {

		WP_Register::add(
			'style',
			App::GAC()->getOption( 'assets', 'css', 'googleIcons' )
		);

		WP_Register::add(
			'style',
			App::GAC()->getOption( 'assets', 'css', 'vuetify' )
		);

		WP_Register::add(
			'style',
			App::GAC()->getOption( 'assets', 'css', 'geolocationAttendanceControlAttendance' )
		);
	}

	public function select_attendances() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlAttendance' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->select_attendances(
			date( 'Y-m-d', strtotime( $_POST['start_date'] ) ),
			date( 'Y-m-d', strtotime( $_POST['end_date'] . '+1 days' ) )
		);

		foreach ( $response as $key => $item ) {
			$user_info = get_userdata( (int) $item->user_id );

			$item->user_fullname   = $user_info->first_name . ' ' . $user_info->last_name;
			$item->user_dni        = get_the_author_meta( 'user_dni', (int) $item->user_id );
			$item->user_first_name = $user_info->first_name;
			$item->user_last_name  = $user_info->last_name;
		}

		echo json_encode( $response );

		die();
	}

	public function insert_attendance( $user_id, $activity_id, $action, $latitude, $longitude, $address, $place_id, $hour_status, $action_status, $meters_apart, $user_ip ) {

		return $this->model->insert_attendance( $user_id, $activity_id, $action, $latitude, $longitude, $address, $place_id, $hour_status, $action_status, $meters_apart, $user_ip );
	}

	public function delete_attendance() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlAttendance' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->delete_attendance( $_POST['id'] );

		echo json_encode( $response );

		die();
	}

	public function delete_attendances_by_activity_id( $id ) {
		return $this->model->delete_attendances_by_activity_id( $id );
	}

	public function render() {
		Hook::getInstance( App::getCurrentID() );

		$page   = App::GAC()->getOption( 'path', 'page' );
		$layout = App::GAC()->getOption( 'path', 'layout' );

		$this->view->renderizate( $layout, 'header' );
		$this->view->renderizate( $page, 'attendance' );
		$this->view->renderizate( $layout, 'footer' );
	}
}
