<?php
/**
 * Signup AJAX step 4 partial.
 *
 * This partial returns data used to render signup form.
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

$result = SEOToolSet::get_session_var( 'signup-result' ); ?>

<p><?php _e( 'Success!', SEOTOOLSET_TEXTDOMAIN ); ?></p>

<p><?php echo $result; ?></p>

<a href="@@" class="signup-btn signup-btn-done" data-ajax="true" data-ajax-action="<?php echo SEOToolSet::user_is_logged_in() ? 'logged-in' : 'login-form'; ?>"><?php _e( 'Done', SEOTOOLSET_TEXTDOMAIN ); ?></a>

<?php SEOToolSet::set_session_var( 'signup-result', null ); ?>
