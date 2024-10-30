<?php
/**
 * Signup AJAX step 1 partial.
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

$signup = SEOToolSet::get_session_var( 'signup' );
if ( ! is_array( $signup ) ) {
	$signup = array();
}

if ( 'signup' === $_POST['setting'] ) {
	$_POST['email'] = trim( $_POST['email'] );
	if ( ! preg_match( ';^[^@]+@[^@]+$;', $_POST['email'] ) ) {
		throw new Exception( __( 'Invalid email address.' ) );
	}

	$_POST['username'] = trim( $_POST['username'] );
	if ( ! preg_match( ';^[a-z].*;i', $_POST['username'] ) ) {
		throw new Exception( __( 'Invalid username.' ) );
	}

	if ( ! preg_match( ';^.{8,};i', ltrim( $_POST['password'] ) ) ) {
		throw new Exception( __( 'Bad password.' ) );
	}
	if ( $_POST['password'] !== $_POST['password2'] ) {
		throw new Exception( __( 'Passwords don\'t match.' ) );
	}

	foreach ( array( 'username', 'email', 'password', 'password2' ) as $key ) {
		$signup[ $key ] = $_POST[ $key ];
	}

	SEOToolSet::set_session_var( 'signup', $signup );
	return true;
}//end if

?>
<?php SEOToolSet::get_template( 'signup-tabs', [ 'active' => 1 ] ); ?>

<label for="signup-email"><?php _e( 'Email Address', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="signup-email" class="required" type="text" name="signup[email]" value="<?php echo htmlspecialchars( $signup['email'] ); ?>"/>
<label for="signup-username"><?php _e( 'Username', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="signup-username" class="required" type="text" name="signup[username]" value="<?php echo htmlspecialchars( $signup['username'] ); ?>"/>
<label for="signup-password"><?php _e( 'Password', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="signup-password" class="required signup" type="password" name="signup[password]" value="<?php htmlspecialchars( $signup['password'] ); ?>"/>
<label for="signup-password2"><?php _e( 'Confirm Password', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="signup-password2" class="required signup" type="password" name="signup[password2]" value="<?php htmlspecialchars( $signup['password2'] ); ?>"/>

<br/>
<input type="button" class="button signup-btn signup-btn-cancel" style="position: relative; top: 13px" data-ajax="true" data-ajax-action="login-form" value="<?php _e( 'Cancel', SEOTOOLSET_TEXTDOMAIN ); ?>">
<input type="button" class="button signup-btn signup-btn-next" data-ajax="true" data-ajax-action="signup-step1" data-ajax-success="signup-step2" value="<?php _e( 'Next', SEOTOOLSET_TEXTDOMAIN ); ?>">

