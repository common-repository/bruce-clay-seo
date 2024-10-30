<?php
/**
 * Analytics AJAX form partial.
 *
 * This file renders the Google Analytics authorization form.
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

SEOToolSet::update_setting( 'google', null );

?>

<button class="google"><?php _e( 'Sign in with Google', SEOTOOLSET_TEXTDOMAIN ); ?></button>
<p class="description"><?php _e( 'Clicking the button above will launch a pop-up window. Follow the directions, copy-paste the Google Authorization code it presents into the box below, and then close the pop-up.', SEOTOOLSET_TEXTDOMAIN ); ?></p>

<div id="google-auth">
	<label for="auth-code"><?php _e( 'Google Authentication Code', SEOTOOLSET_TEXTDOMAIN ); ?></label>
	<input id="auth-code" type="text" name="google[auth]" value="">
	<input id="access-token" type="hidden" name="google[access_token]">
	<input id="refresh-token" type="hidden" name="google[refresh_token]">
	<input id="username" type="hidden" name="google[username]">
	<input id="analytics_id" type="hidden" name="google[analytics_id]">

	<div class="sites">
		<br><p class="description"><?php _e( 'Login successful! Now, select an account for which Analytics will be included on this site.' ); ?></p>
		<ul class="site-list"></ul>
		<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="google" data-ajax-success="has-analytics" value="<?php _e( 'Save Changes', SEOTOOLSET_TEXTDOMAIN ); ?>">
	</div>
</div>
