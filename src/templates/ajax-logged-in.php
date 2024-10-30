<?php
/**
 * Logged in AJAX partial.
 *
 * This partial returns data used to check authentications.
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

// Stored in the form: ID|Project Name|https%3A%2F%projecturl.com
?>

<?php $project = explode( '|', SEOToolSet::get_setting( 'login.project' ) ); ?>

<p>
	<?php _e( 'You are currently signed in as', SEOTOOLSET_TEXTDOMAIN ); ?>
	<?php echo SEOToolSet::get_setting( 'login.username' ); ?> to SEOToolSet.
</p>
<p>
	<strong><?php _e( 'Current Project', SEOTOOLSET_TEXTDOMAIN ); ?>:</strong><br>

	<?php if ( SEOToolSet::user_has_project() ) : ?>
		<?php echo SEOToolSet::get_setting( 'project.name' ); ?> &bull;
		<?php echo htmlspecialchars( urldecode( SEOToolSet::get_setting( 'project.url' ) ) ); ?>
	<?php else : ?>
		<?php _e( 'No project selected!', SEOTOOLSET_TEXTDOMAIN ); ?>
	<?php endif; ?>

	<a class="icon edit" href="?page=seotoolset-settings" data-ajax="true" data-ajax-action="choose-project"><span class="dashicons dashicons-edit"></span></a>
</p>

<input id="project-id" type="hidden" name="project[id]" value="<?php echo SEOToolSet::get_setting( 'project.id' ); ?>">
<input id="project-name" type="hidden" name="project[name]" value="<?php echo SEOToolSet::get_setting( 'project.name' ); ?>">
<input id="project-url" type="hidden" name="project[url]" value="<?php echo SEOToolSet::get_setting( 'project.url' ); ?>">

<ul class="help-links">
	<li><a class="whats-this" href="#help" data-popup-target="project-selection"><?php _e( 'What\'s this?', SEOTOOLSET_TEXTDOMAIN ); ?></a></li>
	<li><a href="@@" data-ajax="true" data-ajax-action="signup-step2"><?php _e( 'Change or setup subscription.', SEOTOOLSET_TEXTDOMAIN ); ?></a></li>
</ul>
<div class="pop-up project-selection">
	<h3><?php _e( 'Account and Project Selection', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
	<p><?php _e( 'The Bruce Clay SEO plugin requires a connection to the SEOToolSet to power many of its features. Your plugin subscription includes access to the SEOToolSet.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
	<p><?php _e( 'When setting up Bruce Clay SEO for WordPress, you must log in to the SEOToolSet and specify the appropriate project here in this SEOToolSet Authentication pane. A project is how the SEOToolSet keeps the data related to your website together, such as the site\'s keywords, preferences, and so forth.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
	<p><?php _e( 'Under "Current Project," click the edit icon (pencil) to choose the SEOToolSet project that corresponds with your current website.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
	<p><?php _e( 'Note: You are already logged in to the SEOToolSet if you see this message: "You are currently signed in as [your username] in SEOToolSet." If not, you can enter your username and password to log in.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
</div>

<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="logout" data-ajax-success="login-form" value="<?php _e( 'Log Out', SEOTOOLSET_TEXTDOMAIN ); ?>">
