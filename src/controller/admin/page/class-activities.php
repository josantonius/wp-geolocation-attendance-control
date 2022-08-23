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

class Activities extends Controller {


	public $slug = 'geolocation-attendance-control-activities';

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
			App::GAC()->getOption( 'submenu', 'activities' ),
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
			App::GAC()->getOption( 'assets', 'js', 'geolocationAttendanceControlActivities' )
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
			App::GAC()->getOption( 'assets', 'css', 'geolocationAttendanceControlActivities' )
		);
	}

	public function center_has_activities( $id ) {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlActivities' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->center_has_activities( $_POST['id'] ?? 0 );

		echo json_encode( $response );

		die();
	}

	public function delete_activities_by_centre_id( $id ) {
		return $this->model->delete_activities_by_centre_id( $id );
	}

	public function select_all_centres() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlActivities' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->select_all_centres();

		echo json_encode( $response );

		die();
	}

	public function select_activity( $id ) {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControl' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->select_activity( $id );

		echo json_encode( $response );

		die();
	}

	public function select_activities() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlActivities' ) && ! wp_verify_nonce( $nonce, 'geolocationAttendanceControl' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->select_activities( wp_verify_nonce( $nonce, 'geolocationAttendanceControl' ) );

		echo json_encode( $response );

		die();
	}

	public function insert_activity() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlActivities' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->insert_activity(
			$_POST['centre_id'],
			$_POST['activity'],
			$_POST['start_hour'],
			$_POST['end_hour'],
			$_POST['days'],
			$_POST['months'],
			$_POST['state']
		);

		echo json_encode( $response );

		die();
	}

	public function update_activity() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlActivities' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->update_activity(
			(int) $_POST['id'],
			$_POST['centre_id'],
			$_POST['activity'],
			$_POST['start_hour'],
			$_POST['end_hour'],
			$_POST['days'],
			$_POST['months'],
			$_POST['state']
		);

		echo json_encode( $response );

		die();
	}

	public function delete_activity() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlActivities' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->delete_activity( $_POST['id'] );

		$attendances_page = App::GAC()->getControllerInstance( 'Attendance', 'admin-page' );

		if ( $response ) {
			$attendances_page->delete_attendances_by_activity_id( $_POST['id'] );
		}

		echo json_encode( $response );

		die();
	}

	public function render() {

		Hook::getInstance( App::getCurrentID() );

		$page   = App::GAC()->getOption( 'path', 'page' );
		$layout = App::GAC()->getOption( 'path', 'layout' );

		$this->view->renderizate( $layout, 'header' );
		$this->view->renderizate( $page, 'activities' );
		$this->view->renderizate( $layout, 'footer' );
	}
}
