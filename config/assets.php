<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eliasis\Framework\App;

$icons  = App::GAC()->getOption( 'url', 'icons' );
$json   = App::GAC()->getOption( 'url', 'json' );
$css    = App::GAC()->getOption( 'url', 'css' );
$js     = App::GAC()->getOption( 'url', 'js' );
$images = App::GAC()->getOption( 'url', 'images' );

return [

	'assets' => [

		'js' => [
			'vuetify' => [
				'name'      => 'vuetify',
				'url'       => $js . 'vuetify.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => false,
				'params'    => [
					'icons_url' => $icons,
				],
			],
			'vuetifyFront' => [
				'name'      => 'vuetifyFront',
				'url'       => $js . 'vuetify.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => false,
				'params'    => [],
			],
			'geolocationAttendanceControl' => [
				'name'      => 'geolocationAttendanceControl',
				'url'       => $js . 'geolocation-attendance-control.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => true,
				'params'    => [
					'google_api_key' => App::GAC()->getOption( 'google_api_key' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'loader_gif' => $images . 'loader.gif',
					'location_loader_gif' => $images . 'location-loader.gif',
					'location_error_gif' => $images . 'error.gif',
				],
			],
			'geolocationAttendanceControlPlaces' => [
				'name'      => 'geolocationAttendanceControlPlaces',
				'url'       => $js . 'geolocation-attendance-control-places.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => true,
				'params'    => [
					'google_api_key' => App::GAC()->getOption( 'google_api_key' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				],
			],
			'geolocationAttendanceControlActivities' => [
				'name'      => 'geolocationAttendanceControlActivities',
				'url'       => $js . 'geolocation-attendance-control-activities.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => true,
				'params'    => [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				],
			],
			'geolocationAttendanceControlAttendance' => [
				'name'      => 'geolocationAttendanceControlAttendance',
				'url'       => $js . 'geolocation-attendance-control-attendance.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'footer'    => true,
				'params'    => [
					'google_api_key' => App::GAC()->getOption( 'google_api_key' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				],
			],
			'axios' => [
				'name'      => 'axios',
				'url'       => 'https://unpkg.com/axios/dist/axios.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'axiosFront' => [
				'name'      => 'axiosFront',
				'url'       => 'https://unpkg.com/axios/dist/axios.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'vueGoogleMaps' => [
				'name'      => 'vueGoogleMaps',
				'url'       => $js . 'vue-google-maps.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'vueGoogleMapsFront' => [
				'name'      => 'vueGoogleMapsFront',
				'url'       => $js . 'vue-google-maps.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'vueGoogleAutocomplete' => [
				'name'      => 'vueGoogleAutocomplete',
				'url'       => $js . 'vue-google-autocomplete.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'vueGoogleMapsPlacesAggregator' => [
				'name'      => 'vueGoogleMapsPlacesAggregator',
				'url'       => $js . 'vue-google-maps-places-aggregator.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'vueGoogleMapsPlacesAggregatorFront' => [
				'name'      => 'vueGoogleMapsPlacesAggregatorFront',
				'url'       => $js . 'vue-google-maps-places-aggregator.min.js',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
			'moment' => [
				'name'      => 'moment',
				'url'       => $js . 'moment-with-locales.min.js',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.0.0',
				'footer'    => true,
				'params'    => [],
			],
		],

		'css' => [
			'vuetify' => [
				'name'      => 'vuetify',
				'url'       => $css . 'vuetify.min.css',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'vuetifyFront' => [
				'name'      => 'vuetifyFront',
				'url'       => $css . 'vuetify.min.css',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'googleIcons' => [
				'name'      => 'googleIcons',
				'url'       => 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'googleIconsFront' => [
				'name'      => 'googleIconsFront',
				'url'       => 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.3.9',
				'media'     => '',
			],
			'geolocationAttendanceControl' => [
				'name'      => 'geolocationAttendanceControl',
				'url'       => $css . 'geolocation-attendance-control.min.css',
				'place'     => 'front',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
			'geolocationAttendanceControlPlaces' => [
				'name'      => 'geolocationAttendanceControlPlaces',
				'url'       => $css . 'geolocation-attendance-control-places.min.css',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
			'geolocationAttendanceControlActivities' => [
				'name'      => 'geolocationAttendanceControlActivities',
				'url'       => $css . 'geolocation-attendance-control-activities.min.css',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
			'geolocationAttendanceControlAttendance' => [
				'name'      => 'geolocationAttendanceControlAttendance',
				'url'       => $css . 'geolocation-attendance-control-attendance.min.css',
				'place'     => 'admin',
				'deps'      => [],
				'version'   => '1.2.1',
				'media'     => '',
			],
		],
	],
];
