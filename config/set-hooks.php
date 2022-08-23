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

$namespace = App::GAC()->getOption( 'namespaces', 'admin-page' );

return [
	'hooks' => [
		[ 'select-options-one', [ $namespace . 'Options', 'select_options_one' ] ],
		[ 'select-options-two', [ $namespace . 'Options', 'select_options_two' ] ],
	],
];
