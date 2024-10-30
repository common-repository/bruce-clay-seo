<?php
/**
 * Bruce Clay SEO WordPress plugin.
 *
 * This is the main entrypoint for the plugin.
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
 * Plugin Name: Bruce Clay SEO
 * Plugin URI:  https://www.bruceclay.com/seo/tools/bruceclayseo/
 * Description: Bruce Clay SEO gives blog writers on-page SEO guidance based on real-time competitor analysis. Analytics is integrated to show post performance, right in WordPress. Keyword tracking, reports and more powered by SEOToolSet.
 * Version:     0.8.0
 * Author:      Bruce Clay, Inc.
 * Author URI:  https://www.bruceclay.com/?utm_source=bc-seo&utm_medium=plugin&utm_campaign=wordpress-general
 * License:     GPL-3.0-or-later
 * License URI: http://www.gnu.org/licenses/gpl-3.0-standalone.html
 * Text Domain: bruce-clay-seo
 * Domain Path: /languages
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

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'SEOTOOLSET_VERSION' ) ) {
	/**
	 * Current version of the plugin.
	 */
	define( 'SEOTOOLSET_VERSION', '0.8.0' );
}

if ( ! defined( 'SEOTOOLSET_DIR_PATH' ) ) {
	/**
	 * The current plugin directory.
	 */
	define( 'SEOTOOLSET_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SEOTOOLSET_DIR_URL' ) ) {
	/**
	 * The URL path for the plugin.
	 */
	define( 'SEOTOOLSET_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SEOTOOLSET_TEXTDOMAIN' ) ) {
	/**
	 * The translations text domain to use.
	 */
	define( 'SEOTOOLSET_TEXTDOMAIN', 'bruce-clay-seo' );
}

if ( ! defined( 'SEOTOOLSET_API_ENDPOINT' ) ) {
	/**
	 * The API endpoint for production.  Override in wp-config.php for development.
	 */
	define( 'SEOTOOLSET_API_ENDPOINT', 'https://toolsv6.seotoolset.com/api/v1' );
}

if ( ! defined( 'SEOTOOLSET_DEBUG_LOGGING' ) ) {
	/**
	 * To enable debug logging to log.txt, set this to true in wp-config.php.
	 */
	define( 'SEOTOOLSET_DEBUG_LOGGING', false );
}

if ( ! defined( 'SEOTOOLSET_RECURLY_PUBLIC_KEY' ) ) {
	/**
	 * The public key value to use the Recurly API.
	 */
	define( 'SEOTOOLSET_RECURLY_PUBLIC_KEY', 'ewr1-NiDn3HAkDUnr512ZRdva59' );
}

load_plugin_textdomain( SEOTOOLSET_TEXTDOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );

// Abort if PHP is < v5.6.0. We can push this down to v5.3.0 if we revert to the
// traditional array syntax (`array()` vs. `[]`).
if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {
	add_action(
		/**
		 * Callback function to raise error on old versions of PHP.
		 */
		'admin_notices',
		function () {
			?>
		<div class="error notice-error is-dismissible">
			<p><?php esc_html_e( 'SEOToolSet cannot run on PHP versions prior to 5.6.0. Please contact your hosting provider to update your site.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
		</div>
			<?php
		}
	);

	return false;
}

// Abort if missing curl.
if ( ! function_exists( 'curl_version' ) ) {
	add_action(
		/**
		 * Callback function to raise error on missing/old curl extension.
		 */
		'admin_notices',
		function () {
			?>
		<div class="error notice-error is-dismissible">
			<p><?php esc_html_e( 'SEOToolSet requires missing library: curl. Please contact your hosting provider to update your site.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
		</div>
			<?php
		}
	);

	return false;
}

// Load Recurly.js
if ( ! function_exists( 'load_js_admin_recurly' ) ) {
	/**
	 * Function to load recurly.js in settings page.
	 *
	 * @param string $hook_suffix The suffix of the page accessed.
	 */
	function load_js_admin_recurly( $hook_suffix ) {
		if ( SEOTOOLSET_TEXTDOMAIN . '_page_seotoolset-settings' === $hook_suffix ) {
			wp_enqueue_script( 'recurly', 'https://js.recurly.com/v4/recurly.js', [], 'v4', false );
		}
	}
	add_action( 'admin_enqueue_scripts', 'load_js_admin_recurly' );
}

require_once SEOTOOLSET_DIR_PATH . 'src/autoload.php';
$seotoolset = new SEOToolSet();
SEOToolSetAPI::init();
