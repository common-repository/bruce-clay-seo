<?php
/**
 * SEOToolSet_Admin_Meta_Boxes class.
 *
 * This file defines the plugin's meta/edit boxes on the edit screens.
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
 * Actual WP meta boxes only apply on edit-related screens.
 *
 * @category SEOToolSet
 * @package  SEOToolSet
 * @author   SEOToolSet <support@seotoolset.com>
 * @license  GNU General Public License, version 3
 * @link     http://www.seotoolset.com/
 */
class SEOToolSet_Admin_Meta_Boxes {

	/**
	 * Holds a copy of the SEOToolSet singleton instance.
	 *
	 * @var mixed
	 */
	private $_toolset;

	/**
	 * The current WP screen from get_current_screen().
	 *
	 * @var WP_Screen
	 */
	private $_screen;

	/**
	 * Add widgets for current screen.
	 */
	public function __construct() {
		$this->_toolset = $GLOBALS['seotoolset'];
		$this->_screen  = get_current_screen();

		switch ( $this->_screen->id ) {
			case 'dashboard':
				if ( SEOToolSet::check_permissions( 'show_dashboard' ) ) {
					$this->add_dashboard_widget();
				}
				return;

			case 'page':
				if ( SEOToolSet::check_permissions( 'show_panels' ) ) {
					$this->add_page_widget();
				}
				return;

			default:
				break;
		}

		switch ( $this->_screen->base ) {
			case 'post':
				if ( SEOToolSet::check_permissions( 'show_panels' ) ) {
					$this->add_post_widget( $this->_screen->id );
				}
				return;

			default:
				break;
		}
	}

	/**
	 * Add our widget to the dashboard. We're using `add_meta_box()` instead of
	 * `wp_add_dashboard_widget()` simply so we can say it has a high priority.
	 * If the user has reordered their widgets then I don't think it'll matter,
	 * but if not then we go to the top(ish).
	 *
	 * @return void
	 */
	public function add_dashboard_widget() {
		// Quite the hook name, eh?
		add_filter(
			'postbox_classes_dashboard_seotoolset_dashboard_widget',
			function ( $classes ) {
				return array_merge( $classes, [ 'seotoolset', 'actual' ] );
			}
		);

		add_meta_box(
			'seotoolset_dashboard_widget',
			__( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ),
			[ $this, 'get_dashboard_widget' ],
			'dashboard',
			'normal',
			'high'
		);
	}

	/**
	 * Output dashboard-widget template.
	 *
	 * @return void
	 */
	public function get_dashboard_widget() {
		SEOToolSet::get_template( 'dashboard-widget' );
	}

	/**
	 * Add the post widget to a screen.
	 *
	 * @param string $screen_id Passthrough to add_meta_box.
	 *
	 * @return void
	 */
	public function add_post_widget( $screen_id = 'post' ) {
		// Quite the hook name, eh?
		add_filter(
			'postbox_classes_' . $screen_id . '_seotoolset_post_widget',
			function ( $classes ) {
				return array_merge( $classes, [ 'seotoolset', 'actual' ] );
			}
		);

		add_meta_box(
			'seotoolset_post_widget',
			__( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ),
			[ $this, 'post_widget' ],
			$screen_id,
			'normal',
			// The default is "advanced".
			'high'
		);
	}

	/**
	 * Output post-widget template.
	 *
	 * @return void
	 */
	public function post_widget() {
		SEOToolSet::get_template( 'post-widget' );
	}

	/**
	 * Add the post widget to a page screen.
	 *
	 * @return void
	 */
	public function add_page_widget() {
		// Quite the hook name, eh?
		add_filter(
			'postbox_classes_page_seotoolset_post_widget',
			function ( $classes ) {
				return array_merge( $classes, [ 'seotoolset', 'actual' ] );
			}
		);

		add_meta_box(
			'seotoolset_post_widget',
			__( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ),
			[ $this, 'post_widget' ],
			'page',
			'normal',
			// The default is "advanced".
			'high'
		);
	}

	/**
	 * Output the page-widget template.
	 *
	 * @return void
	 */
	public function page_widget() {
		SEOToolSet::get_template( 'page-widget' );
	}
}
