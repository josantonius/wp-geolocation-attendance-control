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

use Eliasis\Framework\App;
use Eliasis\Framework\Controller;
use Josantonius\WP_Register\WP_Register;

class Launcher extends Controller {

	public $places_page;

	public $attendances_page;

	public $activities_page;

	public function init() {

		add_action( 'init', [ $this, 'set_language' ] );

		App::GAC()->getControllerInstance( 'Shortcode', 'controller' )->registerAjaxMethods();

		add_action( 'show_user_profile', [ $this, 'add_extra_profile_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'add_extra_profile_fields' ] );
		add_action( 'user_profile_update_errors', [ $this, 'user_profile_update_errors' ], 10, 3 );
		add_action( 'personal_options_update', [ $this, 'update_profile_fields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'update_profile_fields' ] );

		add_filter( 'login_redirect', [ $this, 'admin_default_page' ] );

		$this->places_page      = App::GAC()->getControllerInstance( 'Places', 'admin-page' );
		$this->attendances_page = App::GAC()->getControllerInstance( 'Attendance', 'admin-page' );
		$this->activities_page  = App::GAC()->getControllerInstance( 'Activities', 'admin-page' );
		$this->run_ajax();
		if ( is_admin() ) {
			return $this->admin();
		}

		$this->front();
	}

	public function admin_default_page( $prev_url ) {
		$user_id   = get_current_user_id();
		$user_info = get_userdata( $user_id );
		$rol       = implode( ', ', $user_info->roles );
		if ( 'administrator' !== $rol ) {
			return get_permalink( 4 );
		}
		return admin_url();
	}

	public function add_front_end_actions() {
		add_action( 'template_redirect', [ $this, 'template_redirect_action' ] );
	}

	public function add_extra_profile_fields( $user ) {
		$user_dni = get_the_author_meta( 'user_dni', $user->ID );
		?>
		<h3><?php esc_html_e( 'Documento de identidad', 'crf' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="user_dni"><?php esc_html_e( 'DNI', 'crf' ); ?></label></th>
				<td>
					<input type="text" min="1900" id="user_dni" name="user_dni" value="<?php echo esc_attr( $user_dni ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>
		<?php
	}

	public function update_profile_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'user_dni', $_POST['user_dni'] );
	}

	public function user_profile_update_errors( $errors, $update, $user ) {
		if ( ! $update ) {
			return;
		}

		if ( empty( $_POST['user_dni'] ) ) {
			$errors->add( 'user_dni_error', __( '<strong>ERROR</strong>: Introduce un DNI válido.', 'crf' ) );
		}
	}

	public function run_ajax() {

		$methods = [ 'select_centres', 'insert_centre', 'delete_centre', 'update_centre' ];

		foreach ( $methods as $method ) {
			add_action( 'wp_ajax_' . $method, [ $this->places_page, $method ] );
			add_action( 'wp_ajax_nopriv_' . $method, [ $this->places_page, $method ] );
		}

		$methods = [ 'insert_attendance', 'select_attendances', 'delete_attendance' ];

		foreach ( $methods as $method ) {
			add_action( 'wp_ajax_' . $method, [ $this->attendances_page, $method ] );
			add_action( 'wp_ajax_nopriv_' . $method, [ $this->attendances_page, $method ] );
		}

		$methods = [ 'center_has_activities', 'select_all_centres', 'select_activities', 'insert_activity', 'delete_activity', 'update_activity' ];

		foreach ( $methods as $method ) {
			add_action( 'wp_ajax_' . $method, [ $this->activities_page, $method ] );
			add_action( 'wp_ajax_nopriv_' . $method, [ $this->activities_page, $method ] );
		}
	}

	/**
	 * Hook plugin activation | Executed only when activating the plugin.
	 *
	 * @uses check_admin_referer() → user was referred from admin page
	 * @uses flush_rewrite_rules() → remove rewrite rules and recreate
	 */
	public function activation() {

		$plugin = isset( $_REQUEST['plugin'] ) ? filter_var( wp_unslash( $_REQUEST['plugin'] ), FILTER_SANITIZE_STRING ) : null;

		check_admin_referer( "activate-plugin_$plugin" );

		$this->model->set_options();
		$this->model->create_tables();

		flush_rewrite_rules();
	}

	/**
	 * Hook plugin deactivation. Executed when deactivating the plugin.
	 *
	 * @uses check_admin_referer() → tests if the current request is valid
	 * @uses flush_rewrite_rules() → remove rewrite rules and recreate
	 */
	public function deactivation() {

		$plugin = isset( $_REQUEST['plugin'] ) ? filter_var( wp_unslash( $_REQUEST['plugin'] ), FILTER_SANITIZE_STRING ) : null;

		check_admin_referer( "deactivate-plugin_$plugin" );

		flush_rewrite_rules();
	}

	/**
	 * Admin initializer method.
	 *
	 * @uses add_action() → hooks a function on to a specific action
	 */
	public function admin() {

		$this->set_menus(
			App::GAC()->getOption( 'pages' ),
			App::GAC()->getOption( 'namespaces', 'admin-page' )
		);
	}

	/**
	 * Set plugin texdomain for translations.
	 */
	public function set_language() {

		$slug = App::GAC()->getOption( 'slug' );

		load_plugin_textdomain(
			$slug,
			false,
			$slug . '/languages/'
		);
	}

	/**
	 * Add shortcode.
	 *
	 * @return string → html div tag
	 */
	public function add_shortcode() {

		return '<div id="geolocation-attendance-control">dddddddd</div>';
	}

	/**
	 * Get current page and load submenu.
	 *
	 * @param array  $pages → class pages.
	 * @param string $namespace → namespace.
	 */
	public function set_menus( $pages = [], $namespace = '' ) {

		$set_menu = false;

		foreach ( $pages as $page ) {
			$page = $namespace . $page;

			if ( ! class_exists( $page ) ) {
				continue;
			}

			$instance = call_user_func( $page . '::getInstance' );

			if ( method_exists( $instance, 'init' ) ) {
				call_user_func( [ $instance, 'init' ] );
			}

			if ( ! $set_menu && method_exists( $instance, 'set_menu' ) ) {
				$set_menu = true;
				call_user_func( [ $instance, 'set_menu' ] );
			}

			if ( method_exists( $instance, 'set_submenu' ) ) {
				call_user_func( [ $instance, 'set_submenu' ] );
			}
		}
	}

	/**
	 * Front initializer method.
	 */
	public function front() {

		$this->add_front_end_actions();
		// $this->add_styles();
		// $this->add_scripts();
	}

	public function template_redirect_action() {
		if ( ! is_admin() && is_page() ) {
			App::GAC()->getControllerInstance( 'Shortcode', 'controller' )->load();
		}
	}

	protected function add_scripts() {

		WP_Register::add(
			'script',
			App::GAC()->getOption( 'assets', 'js', 'geolocationAttendanceControl' )
		);
	}

	protected function add_styles() {

		WP_Register::add(
			'style',
			App::GAC()->getOption( 'assets', 'css', 'geolocationAttendanceControl' )
		);
	}
}
