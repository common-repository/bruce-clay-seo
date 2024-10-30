<?php
/**
 * SEOToolSet_Admin_Menu class.
 *
 * This file defines the plugin's admin menu and inserts the dashboard pages.
 *
 * php version 7.2
 *
 * @category  SEOToolSet
 * @package   SEOToolSet
 * @author    SEOToolSet <support@seotoolset.com>
 * @copyright 2018-2019  Bruce Clay, Inc.
 * @license   GNU General Public License, version 3
 * @link      http://www.seotoolset.com/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * Handles the menu and pulling in the relevant templates.
 *
 * @category SEOToolSet
 * @package  SEOToolSet
 * @author   SEOToolSet <support@seotoolset.com>
 * @license  GNU General Public License, version 3
 * @link     http://www.seotoolset.com/
 */
class SEOToolSet_Admin_Menu {

	/**
	 * Holds a copy of the SEOToolSet singleton instance.
	 *
	 * @var mixed
	 */
	private $toolset;

	/**
	 * Available dashboard tabs.
	 *
	 * @var array
	 */
	private $pages;

	/**
	 * Check admin permissions and define available dashboard tabs.
	 */
	public function __construct() {
		$this->toolset = $GLOBALS['seotoolset'];

		$this->pages = [
			'Dashboard' => 'seotoolset-dashboard',
			'Activity'  => 'seotoolset-activity',
			'Content'   => 'seotoolset-content',
			'Keywords'  => 'seotoolset-keywords',
			'Authors'   => 'seotoolset-authors',
		];

		if ( SEOToolSet::check_permissions( 'edit_settings' ) ) {
			$this->pages['Settings'] = 'seotoolset-settings';
		}

		$this->setup_menu();
	}

	/**
	 * Calls WordPress function hooks to display plugin menu.
	 *
	 * @return void
	 */
	private function setup_menu() {
		// @@ We may need fine control over each menu item.
		$capability = SEOToolSet::get_setting( 'permissions.show_dashboard' );

		if ( ! $capability ) {
			$capability = 'edit_posts';
		}

		// The top-level menu item.
		add_menu_page(
			__( 'Bruce Clay SEO Dashboard', SEOTOOLSET_TEXTDOMAIN ),
			__( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ),
			$capability,
			'seotoolset',
			null,
			'dashicons-video-alt',
			// o.O
			90
		);

		// And its submenu items.
		foreach ( $this->pages as $title => $slug ) {
			add_submenu_page(
				'seotoolset',
				__( "SEOToolSet {$title}", SEOTOOLSET_TEXTDOMAIN ),
				__( $title, SEOTOOLSET_TEXTDOMAIN ),
				$capability,
				$slug,
				[ $this, str_replace( ' ', '_', strtolower( $title ) ) ]
			);
		}

		// Remove the first submenu item that WP adds automatically.
		unset( $GLOBALS['submenu']['seotoolset'][0] );
	}

	/**
	 * Outputs the dashboard tabs section.
	 *
	 * @return void
	 */
	public function tabs() {
		SEOToolSet::get_template( 'tabs' );
	}

	/**
	 * Outputs dashboard summary page.
	 *
	 * @return void
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public function dashboard() {
		SEOToolSet::get_template( 'page-dashboard', [ 'pages' => $this->pages ] );
	}

	/**
	 * Outputs dashboard activities page.
	 *
	 * @return void
	 */
	public function activity() {
		$this->big_table_page( 'activity' );
	}

	/**
	 * Outputs dashboard content page.
	 *
	 * @return void
	 */
	public function content() {
		$this->big_table_page( 'content' );
	}

	/**
	 * Outputs dashboard keywords page.
	 *
	 * @return void
	 */
	public function keywords() {
		$this->big_table_page( 'keywords' );
	}

	/**
	 * Outputs dashboard authors page.
	 *
	 * @return void
	 */
	public function authors() {
		$this->big_table_page( 'authors' );
	}

	/**
	 * Outputs dashboard status page.
	 *
	 * @return void
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public function status() {
		SEOToolSet::get_template( 'page-status', [ 'pages' => $this->pages ] );
	}

	/**
	 * Outputs dashboard settings page.
	 *
	 * @return void
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public function settings() {
		SEOToolSet::get_template( 'page-settings', [ 'pages' => $this->pages ] );
	}

	/**
	 * Outputs dashboard page in the big table format.
	 *
	 * @param string $name Table name.
	 *
	 * @return void
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public function big_table_page( $name ) {
		$args = SEOToolSet::big_table_page_args( $name );

		if ( SEOToolSet::user_is_logged_in_subscribed_with_project() ) {
			$data = SEOToolSetAPI::api_request( 'GET', "/dashboard/{$name}", $args );
		}

		// Get current page from query string, explode and expose the corresponding template name as a global JS variable
		$template = preg_match( ';^[a-z]+-([a-z]+);', $_GET['page'], $regs ) ? $regs[1] : 'default';
		echo '<script> var current_page_template = "' . $template . '";</script>';

		SEOToolSet::get_template(
			"page-$name",
			[
				'pages'   => $this->pages,
				'args'    => $args,
				'headers' => $headers,
				"$name"   => $data,
				'limit'   => $args['rowsPerPage'],
				'page'    => $args['page'],
			]
		);
	}
}
