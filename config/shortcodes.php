<?php
/**
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

return [
	'shortcodes' => [
		[
			'id'                => 'geolocation-attendance-control',
			'class'             => 'Geolocation',
			'namespace'         => 'front-section',
			'scripts'           => true,
			'styles'            => true,
			'get-credits'       => false,
			'only-users-logged' => true,
			'ajax-methods'      => [
				'set_attendance',
			],
		],
	],
];
