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

class Places extends Controller {

	public $slug = 'geolocation-attendance-control-places';

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
			App::GAC()->getOption( 'submenu', 'places' ),
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
			App::GAC()->getOption( 'assets', 'js', 'geolocationAttendanceControlPlaces' )
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
			App::GAC()->getOption( 'assets', 'css', 'geolocationAttendanceControlPlaces' )
		);
	}

	public function select_centre( $id ) {

		return $this->model->select_centre( $id );
	}

	/**
	 * Renderizate admin page.
	 */
	public function select_centres() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlPlaces' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->select_centres();

		echo json_encode( $response );

		die();
	}

	public function insert_centre() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlPlaces' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->insert_centre(
			$_POST['centre'],
			$_POST['latitude'],
			$_POST['longitude'],
			$_POST['address'],
			$_POST['place_id'],
			$_POST['state']
		);

		echo json_encode( $response );

		die();
	}

	public function update_centre() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlPlaces' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->update_centre(
			(int) $_POST['id'],
			$_POST['centre'],
			$_POST['latitude'],
			$_POST['longitude'],
			$_POST['address'],
			$_POST['place_id'],
			$_POST['state']
		);

		echo json_encode( $response );

		die();
	}

	public function delete_centre() {

		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControlPlaces' ) ) {
			die( 'Busted!' );
		}

		$response = $this->model->delete_centre( $_POST['id'] );

		$activities_page = App::GAC()->getControllerInstance( 'Activities', 'admin-page' );

		if ( $response ) {
			$activities_page->delete_activities_by_centre_id( $_POST['id'] );
		}

		echo json_encode( $response );

		die();
	}

	public function render() {

		Hook::getInstance( App::getCurrentID() );

		$page   = App::GAC()->getOption( 'path', 'page' );
		$layout = App::GAC()->getOption( 'path', 'layout' );

		$this->view->renderizate( $layout, 'header' );
		$this->view->renderizate( $page, 'places' );
		$this->view->renderizate( $layout, 'footer' );
	}
}
