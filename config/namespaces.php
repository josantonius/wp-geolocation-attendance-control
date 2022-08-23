<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$plugin_name = 'GAC';

return [
	'namespaces' => [
		'modules'         => $plugin_name . '\\Modules\\',
		'admin-page'      => $plugin_name . '\\Controller\\Admin\\Page\\',
		'controller'      => $plugin_name . '\\Controller\\',
		'front-section'     => $plugin_name . '\\Controller\\Front\\Section\\',
	],
];
