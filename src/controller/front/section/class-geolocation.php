<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GAC\Controller\Front\Section;

use Eliasis\Framework\App;
use Eliasis\Framework\Controller;
use Josantonius\WP_Register\WP_Register;

class Geolocation extends Controller {

	public $slug = 'geolocation';

	public function init( $params = [] ) {
		ob_start();
		$this->render();
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Load scripts.
	 */
	public function add_scripts() {

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'vuetifyFront' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'geolocationAttendanceControl' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'vueGoogleMapsFront' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'axiosFront' )
		);

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'vueGoogleMapsPlacesAggregatorFront' )
		);
	}


	public function set_attendance() {
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( ! wp_verify_nonce( $nonce, 'geolocationAttendanceControl' ) ) {
			die( 'Busted!' );
		}

		$action      = $_POST['attendance_action'];
		$latitude    = $_POST['latitude'];
		$longitude   = $_POST['longitude'];
		$activity_id = $_POST['activity_id'];

		date_default_timezone_set( 'Europe/Madrid' );
		$in_area               = 0;
		$is_valid_distance     = false;
		$is_valid_hour         = false;
		$destination_addresses = 'Desconocido';
		$origin_addresses      = 'Desconocido';
		$distance_value        = 999999999999;
		$distance_text         = '¿?';
		$max_area              = 1000; // 1 km
		$max_start_time        = 3600; // 1 hora
		$max_end_time          = 3600; // 1 hora
		$user_position         = "{$latitude},{$longitude}";
		$current_hour          = date( 'H:i', time() );
		$hour_value            = null;
		$meters_apart          = 0;
		$centre_id             = 0;
		$start_hour            = 0;
		$end_hour              = 0;
		$user_id               = $this->get_current_user_id();

		$google_key = App::GAC()->getOption( 'google_api_key' );

		$activities_instance = App::GAC()->getControllerInstance( 'Activities', 'admin-page' );

		$attendances_instance = App::GAC()->getControllerInstance( 'Attendance' );

		$item = $activities_instance->model->select_activity( (int) $activity_id );

		$item = $item[0] ?? null;

		$centre_place_id = $item->centre_place_id;

		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins={$user_position}&destinations=place_id:{$centre_place_id}&key={$google_key}&callback=?";

		$response = @file_get_contents( $url );

		if ( $item && $response ) {
			$response = $response ? json_decode( $response, true ) : false;

			if ( isset( $response['status'] ) && 'OK' === $response['status'] ) {
				$destination_addresses = $response['destination_addresses'][0] ?? $destination_addresses;
				$origin_addresses      = $response['origin_addresses'][0] ?? $origin_addresses;
				$distance_value        = $response['rows'][0]['elements'][0]['distance']['value'] ?? $distance_value;
				$distance_text         = $response['rows'][0]['elements'][0]['distance']['text'] ?? $distance_text;

				$is_valid_distance = ( $distance_value - $max_area ) <= 0;

				$start_hour = strtotime( $item->start_hour );
				$end_hour   = strtotime( $item->end_hour );
				$in_area++;
				$centre_id = $item->id;
				if ( 'Entrada' === $action ) {
					$hour_value    = $start_hour - strtotime( $current_hour );
					$is_valid_hour = $hour_value > 0 && $hour_value <= $max_start_time;
				} else {
					$hour_value    = strtotime( $current_hour ) - $end_hour;
					$is_valid_hour = $hour_value >= 0 && $hour_value <= $max_end_time;
				}
			}
		}

		$place_id = '';

		$valid_distance = ( $is_valid_distance || $in_area > 0 ) ? 1 : 0;
		$valid_hour     = $is_valid_hour ? 1 : 0;

		$attendances_instance->insert_attendance( $user_id, $centre_id, $action, $latitude, $longitude, $origin_addresses, $place_id, $valid_hour, $valid_distance, $distance_value, $_SERVER['REMOTE_ADDR'] );

		$error_message   = null;
		$success_message = null;

		if ( $distance_value >= 1000 ) {
			$distance = round( $distance_value / 1000, 0 ) . ' KM';
			$area     = round( $max_area / 1000, 0 ) . ' KM';
		} else {
			$distance = $distance_value . ' METROS';
			$area     = $max_area . ' METROS';
		}

		if ( ! $is_valid_distance ) {
			$error_message = 'TU UBICACIÓN NO ES CORRECTA. TE ENCUENTRAS A ' . $distance . ' DE ESTE CENTRO. UBÍCATE A UNA DISTANCIA INFERIOR A ' . $area . ' Y VUELVE A INTENTARLO.';
		} elseif ( $is_valid_distance && ! $is_valid_hour ) {
			if ( 'Entrada' === $action ) {
				$error_message = 'LA HORA DE ENTRADA NO ES CORRECTA. DEBERÍAS ENTRAR ENTRE LAS ' . date( 'H:i', strtotime( '-60 minutes', $start_hour ) ) . ' Y LAS ' . date( 'H:i', strtotime( '-1 minutes', $start_hour ) ) . '.';
				if ( $start_hour - strtotime( $current_hour ) > $max_start_time ) {
					$error_message .= ' VUELVE A INTENTARLO MÁS TARDE.';
				}
			} else {
				$error_message = 'LA HORA DE SALIDA NO ES CORRECTA. DEBERÍAS SALIR ENTRE LAS ' . date( 'H:i', strtotime( '+0 minutes', $end_hour ) ) . ' Y LAS ' . date( 'H:i', strtotime( '+60 minutes', $end_hour ) ) . '.';
				if ( strtotime( $current_hour ) - $end_hour < 0 ) {
					$error_message .= ' VUELVE A INTENTARLO MÁS TARDE.';
				}
			}
		} elseif ( $is_valid_distance && $is_valid_hour ) {
			$success_message = 'UBICACIÓN Y HORARIO CORRECTOS';
		}

		$response = [
			'address' => $origin_addresses,
			'hour' => $current_hour,
			'error_msg' => $error_message,
			'success_msg' => $success_message,
			'hour_value' => $hour_value,
		];

		echo json_encode( $response );

		die();
	}

	public function get_current_user_id() {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return 0;
		}
		$user = wp_get_current_user();
		return ( isset( $user->ID ) ? (int) $user->ID : 0 );
	}

	/**
	 * Load styles.
	 */
	public function add_styles() {

		WP_Register::add(
			'style',
			App::GAC()->getOption( 'assets', 'css', 'googleIconsFront' )
		);

		WP_Register::add(
			'style',
			App::GAC()->getOption( 'assets', 'css', 'vuetifyFront' )
		);

		WP_Register::add(
			'style',
			App::GAC()->getOption( 'assets', 'css', 'geolocationAttendanceControl' )
		);
	}

	public function render() {
		$data = [];
		$path = App::GAC()->getOption( 'path', 'front-section' );
		$this->view->renderizate( $path, $this->slug, $data );
	}
}
