<?php
/*
 * This file is part of https://github.com/josantonius/wp-geolocation-attendance-control repository.
 *
 * (c) Josantonius <hello@josantonius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GAC\Controller;

use Eliasis\Framework\Controller;

/**
 * Main method for cleaning and removal of components.
 */
class Uninstall extends Controller {

	/**
	 * Remove and uninstall the plugin components.
	 */
	public function remove_all() {

		$this->model->remove_all();
	}
}
