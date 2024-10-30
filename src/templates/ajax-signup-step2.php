<?php
/**
 * Signup AJAX step 2 partial.
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

$is_new_signup = ! SEOToolSet::user_is_logged_in();
SEOToolSet::user_is_subscribed( true );
$signup = SEOToolSet::get_session_var( 'signup' );
if ( ! is_array( $signup ) ) {
	$signup = array();
}

$cur_plan      = SEOToolSet::get_setting( 'subscription.plan' );
$selected_plan = $_POST['plan'] ?: $signup['plan'] ?: $cur_plan;


if ( 'signup' === $_POST['setting'] ) {
	if ( '' === $_POST['plan'] ) {
		throw new Exception( __( 'No plan selected.', SEOTOOLSET_TEXTDOMAIN ) );
	}

	$signup['plan'] = $_POST['plan'];

	SEOToolSet::set_session_var( 'signup', $signup );
	return true;
}
?>
<?php SEOToolSet::get_template( 'signup-tabs', [ 'active' => 2 ] ); ?>
<?php
$plans = SEOToolSetAPI::api_request( 'OPTIONS', '/subscription', null, null, true );
if ( ! $is_new_signup ) {
	$plans[] = array(
		'plan'        => 'cancel',
		'description' => __( 'Cancel Subscription', SEOTOOLSET_TEXTDOMAIN ),
	);
}
$show_next = true;
if ( 200 !== $plans['meta']['http_code'] ) {
	echo '<p>';
	_e( 'An error occurred attempting to fetch available billing plans. Please go back and try again.', SEOTOOLSET_TEXTDOMAIN );
	echo '</p>';
	$show_next = false;
} else {
	foreach ( $plans as $i => $row ) {
		if ( ! is_numeric( $i ) ) {
			continue;
		}
		$checked = '' !== $selected_plan && $selected_plan === $row['plan'] ? ' checked="checked"' : '';
		echo <<<HTML
    <br/>
    <input id="signup-plan-{$row['plan']}" class="required" type="radio" name="signup[plan]" value="{$row['plan']}"{$checked}/>
    <label for="signup-plan-{$row['plan']}">
HTML;

		_e( $row['description'], SEOTOOLSET_TEXTDOMAIN );
		if ( '' !== $cur_plan && $row['plan'] === $cur_plan ) {
			echo ' (' . __( 'current', SEOTOOLSET_TEXTDOMAIN ) . ')';
		}

		echo <<<HTML
    </label>
    <br/>
HTML;
	}//end foreach
}//end if

?>

<br/>
<?php if ( $is_new_signup ) { ?>
<input type="button" class="button signup-btn signup-btn-back" style="position: relative; top: 13px" data-ajax="true" data-ajax-action="signup-step1" value="<?php _e( 'Back', SEOTOOLSET_TEXTDOMAIN ); ?>">
<?php } ?>
<a href="@@" class="signup-btn signup-btn-cancel" data-ajax="true" data-ajax-action="<?php echo $is_new_signup ? 'login-form' : 'logged-in'; ?>"><?php _e( 'Cancel', SEOTOOLSET_TEXTDOMAIN ); ?></a>

<?php if ( $show_next ) { ?>
<input type="button" class="button-primary signup-btn signup-btn-next" data-ajax="true" data-ajax-action="signup-step2" data-ajax-success="signup-step3" value="<?php _e( 'Next', SEOTOOLSET_TEXTDOMAIN ); ?>">
<?php } ?>
