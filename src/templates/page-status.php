<?php
/**
 * Page Status
 *
 * This partial returns data used to render plugin status information.
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
 * Display setting.
 *
 * @param mixed $setting setting to display.
 * @param mixed $notext  text if none set.
 *
 * @return void
 */
function display_setting( $setting, $notext = 'None set.' ) {
	$setting = SEOToolSet::get_setting( $setting );

	echo ( $setting )
		? '<span class="dashicons dashicons-yes green"></span>' . $setting
		: '<span class="dashicons dashicons-no red"></span>' . $notext;
}

?>

<div class="seotoolset wrap page-status">
	<style>pre { max-width: 800px; overflow: scroll; }</style>

	<h1><?php _e( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ); ?></h1>
	<?php SEOToolSet::get_template( 'tabs', [ 'pages' => $pages ] ); ?>

	<p><?php _e( 'This is a page for ensuring that things are set up correctly. See WooCommerce\'s for an extensive (perhaps overly so) example.', SEOTOOLSET_TEXTDOMAIN ); ?></p>

	<h3><?php _e( 'Hold Up!', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
	<table class="widefat">
		<tr class="<?php echo ( SEOToolSet::get_setting( 'suspended' ) ) ? 'suspended' : 'active'; ?>">
			<td><?php _e( 'Suspend operations?', SEOTOOLSET_TEXTDOMAIN ); ?></td>
			<td>
				<input id="suspend" name="suspend" type="checkbox" <?php checked( SEOToolSet::get_setting( 'suspended' ), '1' ); ?>>
				<label class="description" for="suspend"><?php _e( 'Check this to temporarily halt SEOToolSet operations.', SEOTOOLSET_TEXTDOMAIN ); ?></label>
			</td>
		</tr>
	</table>

	<h3><?php _e( 'Environment Variables', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
	<table class="widefat stripes">
		<tr>
			<td><?php _e( 'PHP Version', SEOTOOLSET_TEXTDOMAIN ); ?></td>
			<td>
				<?php echo PHP_VERSION; ?>

				<?php if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) : ?>
					<span class="dashicons dashicons-yes green"></span>
				<?php else : ?>
					<span class="dashicons dashicons-no red"></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td>SEOTOOLSET_VERSION</td>
			<td><?php echo SEOTOOLSET_VERSION; ?></td>
		</tr>
		<tr>
			<td>SEOTOOLSET_DIR_PATH</td>
			<td><?php echo SEOTOOLSET_DIR_PATH; ?></td>
		</tr>
		<tr>
			<td>SEOTOOLSET_DIR_URL</td>
			<td><?php echo SEOTOOLSET_DIR_URL; ?></td>
		</tr>
		<tr>
			<td>SEOTOOLSET_TEXTDOMAIN</td>
			<td><?php echo SEOTOOLSET_TEXTDOMAIN; ?></td>
		</tr>
	</table>

	<h3><?php _e( 'SEOToolSet Account Information', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
	<table class="widefat stripes">
		<tr>
			<td><?php _e( 'Username', SEOTOOLSET_TEXTDOMAIN ); ?></td>
			<td><?php display_setting( 'login.username' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Password', SEOTOOLSET_TEXTDOMAIN ); ?></td>
			<td><?php display_setting( 'login.password' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Real Name', SEOTOOLSET_TEXTDOMAIN ); ?></td>
			<td><?php display_setting( 'login.realname' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Email', SEOTOOLSET_TEXTDOMAIN ); ?></td>
			<td><?php display_setting( 'login.email' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'License Key', SEOTOOLSET_TEXTDOMAIN ); ?></td>
			<td><?php display_setting( 'login.api_key' ); ?></td>
		</tr>
	</table>

	<h3><?php _e( 'API Connectivity Test', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
	<p><?php _e( 'Endpoint', SEOTOOLSET_TEXTDOMAIN ); ?>: ...</p>
</div>
