<?php
/**
 * Login AJAX form partial.
 *
 * This partial returns data used to render a login form.
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

?>

<label for="login-username"><?php _e( 'Username', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="login-username" class="required" type="text" name="login[username]" value="<?php echo SEOToolSet::get_setting( 'login.username' ); ?>">
<label for="login-password"><?php _e( 'Password', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="login-password" class="required" type="password" name="login[password]" value="<?php echo SEOToolSet::get_setting( 'login.password' ); ?>">

<input id="project-id" type="hidden" name="project[id]" value="<?php echo SEOToolSet::get_setting( 'project.id' ); ?>">
<input id="project-name" type="hidden" name="project[name]" value="<?php echo SEOToolSet::get_setting( 'project.name' ); ?>">
<input id="project-url" type="hidden" name="project[url]" value="<?php echo SEOToolSet::get_setting( 'project.url' ); ?>">

<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="login" data-ajax-success="logged-in" value="<?php _e( 'Log In', SEOTOOLSET_TEXTDOMAIN ); ?>">

<ul class="help-links">
	<li><a href="https://toolsv6.seotoolset.com/accounts/register/password_reset/"><?php _e( 'I forgot my password.', SEOTOOLSET_TEXTDOMAIN ); ?></a></li>
	<li><a href="@@" data-ajax="true" data-ajax-action="signup-step1"><?php _e( 'I don\'t have an account.', SEOTOOLSET_TEXTDOMAIN ); ?></a></li>
</ul>
