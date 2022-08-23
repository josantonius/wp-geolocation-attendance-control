<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GAC\Model;

use Eliasis\Framework\App,
	Eliasis\Framework\Model;

/**
 * Main method for cleaning and removal of components.
 */
class Uninstall extends Model {

	/**
	 * Remove and uninstall the plugin components.
	 *
	 * @uses delete_option()      → removes option by name
	 * @uses delete_site_option() → removes a option by name
	 */
	public function remove_all() {

		$slug = App::GAC()->getOption( 'slug' );

		delete_option( $slug . '-version' );

		delete_site_option( $slug . '-version' );
	}
}
