<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require 'vendor/autoload.php';

use Eliasis\Framework\App;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

App::run( __DIR__, 'wordpress-plugin', 'GAC' );

App::GAC()->getControllerInstance( 'class-uninstall', 'controller' )->remove_all();
