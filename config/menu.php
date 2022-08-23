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

$icons_url = App::GAC()->getOption( 'url', 'icons' );

return [

	'menu' => [
		'top-level' => [
			'title'      => __( 'Asistencias', 'geolocation-attendance-control' ),
			'name'       => __( 'Asistencias', 'geolocation-attendance-control' ),
			'capability' => 'manage_options',
			'slug'       => 'geolocation-attendance-control-attendance',
			'function'   => '',
			'icon_url'   => $icons_url . 'geolocation-attendance-control-menu-admin.png',
			'position'   => 25,
		],
	],
	'submenu' => [
		'attendance' => [
			'parent'     => 'geolocation-attendance-control-attendance',
			'title'      => __( 'Asistencias', 'geolocation-attendance-control' ),
			'name'       => __( 'Asistencias', 'geolocation-attendance-control' ),
			'capability' => 'manage_options',
			'slug'       => 'geolocation-attendance-control-attendance',
			'function'   => '',
		],
		'places' => [
			'parent'     => 'geolocation-attendance-control-attendance',
			'title'      => __( 'Centros', 'geolocation-attendance-control' ),
			'name'       => __( 'Centros', 'geolocation-attendance-control' ),
			'capability' => 'manage_options',
			'slug'       => 'geolocation-attendance-control-places',
			'function'   => '',
		],
		'activities' => [
			'parent'     => 'geolocation-attendance-control-attendance',
			'title'      => __( 'Actividades', 'geolocation-attendance-control' ),
			'name'       => __( 'Actividades', 'geolocation-attendance-control' ),
			'capability' => 'manage_options',
			'slug'       => 'geolocation-attendance-control-activities',
			'function'   => '',
		],
	],
];
