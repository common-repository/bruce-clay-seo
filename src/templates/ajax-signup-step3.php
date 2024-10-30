<?php
/**
 * Signup AJAX step 3 partial.
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
$signup        = SEOToolSet::get_session_var( 'signup' );
if ( ! is_array( $signup ) ) {
	$signup = array();
}
$subscription = SEOToolSet::get_setting( 'subscription' );
if ( ! is_array( $subscription ) ) {
	$subscription = array();
}

$account_code = $signup[ $key ];
if ( ! $is_new_signup && '' === $account_code ) {
	$account_code = $subscription['account_code'];
}

if ( 'signup' === $_POST['setting'] ) {
	if ( 'cancel' === $signup['plan'] ) {
		$result = SEOToolSetAPI::api_request( 'DELETE', '/subscription', null, null, true );
	} else {
		$_POST['first_name'] = trim( $_POST['first_name'] );

		if ( ! preg_match( ';^[a-z].+;i', $_POST['first_name'] ) ) {
			throw new Exception( __( 'Invalid or missing first name.' ) );
		}

		$_POST['last_name'] = trim( $_POST['last_name'] );

		if ( ! preg_match( ';^[a-z].+;i', $_POST['last_name'] ) ) {
			throw new Exception( __( 'Invalid or missing last name.' ) );
		}

		if ( ! preg_match( ';^[0-9]{5}$;i', ltrim( $_POST['postal_code'] ) ) ) {
			throw new Exception( __( 'Invalid or missing postal code.' ) );
		}

		if ( ! preg_match( ';^\S+;i', ltrim( $_POST['recurly-token'] ) ) ) {
			throw new Exception( __( 'Invalid or missing credit card token.' ) );
		}

		if ( 'true' !== $_POST['terms_accept'] ) {
			throw new Exception( __( 'You must accept the terms and conditions.' ) );
		}

		foreach ( array( 'first_name', 'last_name', 'postal_code' ) as $key ) {
			$signup[ $key ] = $_POST[ $key ];
		}

		SEOToolSet::set_session_var( 'signup', $signup );

		// perform signup
		$keys = array( 'plan', 'first_name', 'last_name', 'postal_code', 'recurly-token', 'account_code' );
		if ( $is_new_signup ) {
			$keys = array_merge( array( 'username', 'password', 'email' ), $keys );
		}
		$args = array();

		foreach ( $keys as $key ) {
			switch ( $key ) {
				case 'recurly-token':
					$args[ $key ] = $_POST[ $key ];
					break;

				case 'account_code':
					if ( '' !== $account_code ) {
						$args[ $key ] = $account_code;
					}
					break;

				default:
					$args[ $key ] = $signup[ $key ];
					break;
			}
		}

		$result = SEOToolSetAPI::api_request( $is_new_signup ? 'POST' : 'PUT', '/subscription', $args, null, true );
	}//end if

	if ( isset( $result['account_code'] ) ) {
		$signup['account_code'] = $result['account_code'];
	}

	if ( isset( $result[0]['account_code'] ) ) {
		$signup['account_code'] = $result[0]['account_code'];
	}

	if ( isset( $result['error_message'] ) ) {
		throw new Exception( $result['error_message'] );
	}

	if ( isset( $result[0]['message'] ) ) {
		throw new Exception( $result[0]['message'] );
	}

	$success = false;
	switch ( $result['meta']['http_code'] ) {
		case '201':
			// create success
			$success = true;
			SEOToolSet::set_session_var( 'signup-result', __( 'Account and subscription successfully created.', SEOTOOLSET_TEXTDOMAIN ) );
			break;

		case '202':
			// update success
			$success = true;
			SEOToolSet::set_session_var( 'signup-result', __( 'Subscription successfully updated.', SEOTOOLSET_TEXTDOMAIN ) );
			break;

		case '204':
			// delete success
			$success = true;
			SEOToolSet::set_session_var( 'signup-result', __( 'Subscription successfully deleted.', SEOTOOLSET_TEXTDOMAIN ) );
			break;

		case '400':
			// bad request
		case '404':
			// no subscription found to update/delete
		case '422':
			// bad payment
		default:
			SEOToolSet::set_session_var( 'signup-result', null );
			break;
	}//end switch

	if ( 'cancel' === $signup['plan'] ) {
		if ( ! $success ) {
			throw new Exception( 'Invalid cancel response.' );
		}
	} elseif ( $is_new_signup ) {
		if ( ! $success ) {
			throw new Exception( 'Invalid signup response.' );
		}

		if ( '' === $result['username'] ) {
			throw new Exception( 'Invalid username in signup response.' );
		}

		if ( '' === $result['companyId'] ) {
			throw new Exception( 'Invalid companyId in signup response.' );
		}

		if ( '' === $result['sessionId'] ) {
			throw new Exception( 'Invalid sessionId in signup response.' );
		}

		SEOToolSet::update_setting(
			'login',
			[
				'username' => $result['username'],
			]
		);

		SEOToolSet::update_setting(
			'api',
			[
				'company_id' => $result['companyId'],
				'session_id' => $result['sessionId'],
			]
		);
	} else {
		if ( ! $success ) {
			throw new Exception( 'Invalid update response.' );
		}
	}//end if

	SEOToolSet::user_is_subscribed( true );

	SEOToolSet::set_session_var( 'signup', null );
	return true;
}//end if

$values = $signup;
if ( ! $is_new_signup ) {
	foreach ( $subscription as $key => $value ) {
		if ( '' === $values[ $key ] && '' !== $value ) {
			$values[ $key ] = $value;
		}
	}
}
?>
<?php SEOToolSet::get_template( 'signup-tabs', [ 'active' => 3 ] ); ?>

<?php if ( 'cancel' === $signup['plan'] ) { ?>
<p><?php _e( 'Once you click Next, your subscription will be cancelled.  Your current subscription will continue until the next billing cycle and will then terminate.', SEOTOOLSET_TEXTDOMAIN ); ?></p> 
<?php } else { ?>
	<?php if ( '' !== $account_code ) { ?>
<input id="signup-account_code" type="hidden" name="signup[account_code]" value="<?php echo htmlspecialchars( $account_code ); ?>" data-recurly="account_code"/>
	<?php } ?>

<label for="signup-first_name"><?php _e( 'First Name', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="signup-first_name" class="required" type="text" name="signup[first_name]" value="<?php echo htmlspecialchars( $values['first_name'] ); ?>" data-recurly="first_name"/>

<label for="signup-last_name"><?php _e( 'Last Name', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="signup-last_name" class="required" type="text" name="signup[last_name]" value="<?php echo htmlspecialchars( $values['last_name'] ); ?>" data-recurly="last_name"/>

<label for="signup-card"><?php _e( 'Credit Card Information', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<div id="signup-card" data-recurly="card"></div>

<label for="signup-postal_code"><?php _e( 'Postal Code', SEOTOOLSET_TEXTDOMAIN ); ?></label>
<input id="signup-postal_code" class="required" type="text" name="signup[postal_code]" value="<?php echo htmlspecialchars( $values['postal_code'] ); ?>" data-recurly="postal_code"/>

<input type="hidden" name="signup[recurly-token]" data-recurly="token"/>

<br/>
<input id="signup-terms_accept" type="checkbox" name="signup[terms_accept]" value="true"/>
<label for="signup-terms_accept"><?php _e( 'I agree to', SEOTOOLSET_TEXTDOMAIN ); ?> <a href="http://www.seotoolset.com/legal/" target="_blank"><?php _e( 'terms and conditions', SEOTOOLSET_TEXTDOMAIN ); ?></a>.</label>

	<?php
}//end if
?>

<br/>
<br/>
<input type="button" class="button signup-btn signup-btn-back" style="position: relative; top: 13px" data-ajax="true" data-ajax-action="signup-step2" value="<?php _e( 'Back', SEOTOOLSET_TEXTDOMAIN ); ?>">
<a href="@@" class="signup-btn signup-btn-cancel" data-ajax="true" data-ajax-action="<?php echo $is_new_signup ? 'login-form' : 'logged-in'; ?>"><?php _e( 'Cancel', SEOTOOLSET_TEXTDOMAIN ); ?></a>
<input type="button" class="button-primary signup-btn signup-btn-finish 
<?php
if ( 'cancel' === $signup['plan'] ) {
	echo 'signup-btn-delete';
}
?>
" data-ajax="true" data-ajax-action="signup-step3" data-ajax-success="signup-step4" value="<?php _e( 'Next', SEOTOOLSET_TEXTDOMAIN ); ?>">

<?php if ( 'cancel' !== $signup['plan'] ) { ?>
<script type="text/javascript">
var _recurly = _recurly || null;
(function(){
	var retries = 100;
	var configure = function() {
	if (typeof recurly == 'undefined') {
		if (retries > 0) {
			retries--;
			setTimeout(function() { configure(); }, 200);
		}
		return;
	}
	_recurly = _recurly || recurly;
	_recurly.configure({
		publicKey: '<?php echo SEOTOOLSET_RECURLY_PUBLIC_KEY; ?>',
		required: [
		'cvv'
		]
	});
	};

	configure();
})();
</script>
<?php }//end if
?>
