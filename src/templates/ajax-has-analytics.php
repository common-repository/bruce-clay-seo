<?php
/**
 * Has analytics AJAX partial.
 *
 * This partial returns data used to render analytics charts.
 * Templates starting with "ajax" are involved in Ajax calls. They aren't
 * always loaded via Ajax; sometimes they're loaded up as an initial state in
 * PHP. But they can be loaded via Ajax by including a `template` field when
 * posting a call.
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

// Stored in the form: UA-XXXXX-Y|Site Name|http%3A%2F%2Furl.com
$analytics = explode( '|', SEOToolSet::get_setting( 'google.analytics_id' ) );

$tracking_disabled = SEOToolSet::get_setting( 'google.disable_tracking' );

?>

<p>
	<?php echo __( 'You are currently logging Analytics data for ', SEOTOOLSET_TEXTDOMAIN ) . $analytics[1]; ?>
</p>
<p>
	<strong><?php _e( 'Current Account', SEOTOOLSET_TEXTDOMAIN ); ?></strong><br>
	<?php echo $analytics[0] . '&lt;' . urldecode( $analytics[2] ) . '&gt;'; ?>
	<a class="icon edit" href="#"><span class="dashicons dashicons-edit"></span></a>
</p>
<p>
	<strong><?php _e( 'Disable Tracking Script', SEOTOOLSET_TEXTDOMAIN ); ?></strong><br>
	<?php _e( 'Select <b>Yes</b> if you already have tracking in place for this account.', SEOTOOLSET_TEXTDOMAIN ); ?><br>
	<br>
	No <input type="radio" name="google[disable_tracking]" value="0" <?php echo ! $tracking_disabled ? 'checked="checked"' : ''; ?>/>
	Yes <input type="radio" name="google[disable_tracking]" value="1" <?php echo $tracking_disabled ? 'checked="checked"' : ''; ?>/>
	<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="google" data-ajax-success="has-analytics" value="<?php _e( 'Save Changes', SEOTOOLSET_TEXTDOMAIN ); ?>">
</p>
	<br>

<input id="auth-code" type="hidden" name="google[auth]" value="<?php echo SEOToolSet::get_setting( 'google.auth' ); ?>">
<input id="access-token" type="hidden" name="google[access_token]" value="<?php echo SEOToolSet::get_setting( 'google.access_token' ); ?>">
<input id="refresh-token" type="hidden" name="google[refresh_token]" value="<?php echo SEOToolSet::get_setting( 'google.refresh_token' ); ?>">
<input id="username" type="hidden" name="google[username]" value="<?php echo SEOToolSet::get_setting( 'google.username' ); ?>">
<input id="analytics_id" type="hidden" name="google[analytics_id]" value="<?php echo SEOToolSet::get_setting( 'google.analytics_id' ); ?>">

<div class="sites">
	<p class="description"><?php _e( 'Account refresh successful! Select an account for which Analytics will be included on this site.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
	<ul class="site-list"></ul>
	<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="google" data-ajax-success="has-analytics" value="<?php _e( 'Save Changes', SEOTOOLSET_TEXTDOMAIN ); ?>">
</div>

<a class="whats-this" href="#help" data-popup-target="analytics"><?php _e( 'What\'s this?', SEOTOOLSET_TEXTDOMAIN ); ?></a>
<div class="pop-up analytics">
	<h3><?php _e( 'Google Analytics', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
	<p><?php _e( 'The Bruce Clay SEO plugin requires a connection to Google Analytics and Google Search Console to use all of its features.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
	<p><?php _e( 'When setting up the Bruce Clay SEO for WordPress plugin, you can login to Google using the same Google account you use to access Google Analytics and Google Search Console. The login will happen via Google servers, and once you have allowed the plugin access to your Analytics and Search Console data, you will be given a code to paste into the plugin.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
	<p><?php _e( 'Once the code is pasted in, you will be presented with a list of accounts which are active inside your Google Analytics Account. Once selected, the Google Analytics project will be used to provide pageview and visit data to the plugin. You may change the selected Google Analytics account at any time by selecting the pencil icon under "Current Account."', SEOTOOLSET_TEXTDOMAIN ); ?></p>
	<p><?php _e( 'Note: you are already logged in to Google Analytics if you see this message: "You are current logging Analytics data for [your analytics account]." If you do not see that message, you will need to login to Google Analytics again.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
</div>
