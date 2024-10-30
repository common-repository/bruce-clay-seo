<?php
/**
 * SEOToolSetAPI class.
 *
 * This file defines how the plugin interacts with the remote API.
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
 * Integrations with the SEOToolSet API.
 *
 * @category SEOToolSet
 * @package  SEOToolSet
 * @author   SEOToolSet <support@seotoolset.com>
 * @license  GNU General Public License, version 3
 * @link     http://www.seotoolset.com/
 */
class SEOToolSetAPI {

	/**
	 * Company ID from API.
	 *
	 * @var string
	 */
	private static $company_id = null;

	/**
	 * Session ID from API.
	 *
	 * @var string
	 */
	private static $session_id = null;

	/**
	 * Last HTTP request result.
	 *
	 * @var string
	 */
	private static $last_request = null;

	/**
	 * Last API error.
	 *
	 * @var string
	 */
	private static $last_error = null;

	/**
	 * Last HTTP response if JSON.
	 *
	 * @var bool
	 */
	private static $is_json_response = false;

	/**
	 * Count records in response.
	 *
	 * @param array $arr Records to count.
	 *
	 * @return int
	 */
	public static function response_count_records( &$arr ) {
		if ( ! is_array( $arr ) ) {
			return 0;
		}

		$count = 0;

		foreach ( $arr as $k => $v ) {
			if ( is_numeric( $k ) ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Check for prior errors.
	 *
	 * @param bool   $ignore_previous_errors True to ignore prior errors.
	 * @param string $path                 Path for deduping.
	 *
	 * @return bool
	 */
	public static function state_check_for_prior_errors( $ignore_previous_errors, $path = null ) {
		$settings = SEOToolSet::get_settings();
		if ( is_array( $settings ) && is_array( $settings['api'] ) && isset( $settings['api']['code'] ) ) {
			if ( $ignore_previous_errors ) {
				// Clear transient error.
				unset( $settings['api']['error'], $settings['api']['code'] );
				update_option( 'seotoolset_settings', $settings );
			} elseif ( $settings['api']['code'] > 0 ) {
				self::admin_notice( $settings['api']['error'], 'error', $path );
				return true;
			}
		}
		return false;
	}

	/**
	 * Add an admin notice.
	 *
	 * @param string $msg   Message to display.
	 * @param string $level Severity level.
	 * @param string $path  Path for deduping.
	 *
	 * @return mixed
	 */
	public static function admin_notice( $msg, $level, $path = null ) {
		if ( ! self::$is_json_response ) {
			return SEOToolSet::add_admin_notice_dedupe_path( $msg, $level, $path );
		}
	}

	/**
	 * Check response for errors.
	 *
	 * @param array  $arr  Response to check.
	 * @param string $path Path for deduping.
	 *
	 * @return bool
	 */
	public static function response_check_for_errors( &$arr, $path = null ) {
		static $dupes = array();

		if ( ! isset( $arr['code'] ) ) {
			return false;
		}

		// Show error message.
		$msg   = $arr['message'];
		$level = SEOToolSet::severity_to_level( $arr['severity'] );

		// Set transient error code.
		$settings = SEOToolSet::get_settings();
		if ( ! is_array( $settings ) ) {
			$settings = array();
		}
		if ( ! is_array( $settings['api'] ) ) {
			$settings['api'] = array();
		}

		try {
			switch ( $arr['code'] ) {
				case '10':
					SEOToolSet::require_user_project( true );
					// throw exception
					break;

				default:
				case '99999':
					SEOToolSet::require_user_logged_in();
					// throw exception
					self::admin_notice( "[code {$arr['code']}] " . $msg, $level, $path );
					return false;
			}
		} catch ( Exception $e ) {
			$msg = $e->getMessage();
		}

		self::admin_notice( $msg, $level, $path );
		$settings['api']['error'] = $msg;
		$settings['api']['code']  = $arr['code'];
		update_option( 'seotoolset_settings', $settings );
		return true;
	}

	/**
	 * Calculate total pages in the given response, for pagination purposes.
	 *
	 * @param array $arr      Response to check.
	 * @param int   $cur_page  Current page.
	 * @param int   $row_limit Row limit.
	 *
	 * @return int
	 */
	public static function response_total_pages( &$arr, $cur_page, $row_limit ) {
		$count = $arr['meta']['count'] ?: self::response_count_records( $arr );

		if ( 1 >= $cur_page && 0 === $count ) {
			return 0;
		}

		$total = min( 1, $cur_page ) + floor( $count / $row_limit );

		return $total;
	}

	/**
	 * Initialize ajax handler and company/session ids.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_seotoolset_apiRequest', [ __CLASS__, 'catch_ajax' ] );

		$auth = SEOToolSet::get_setting( 'api' );
		if ( $auth ) {
			self::$company_id = $auth['company_id'];
			self::$session_id = $auth['session_id'];
		}
	}

	/**
	 * Return true if authentication settings are set.
	 *
	 * @return bool
	 */
	private static function is_authed() {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		return ( self::$company_id && self::$session_id );
	}

	/**
	 * Check that the last request responded with the expected code.
	 *
	 * @param int $code HTTP code to check.
	 *
	 * @return void
	 *
	 * @@TODO The API should return an error response if something's wrong, so
	 * we could catch the errors in here.
	 */
	private static function assert_code( $code = 200 ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		return ( intval( self::$last_request['http_code'] ) === intval( $code ) );
	}

	// ----- API THINGS BELOW ----------------------------------------------- //

	/**
	 * Handle browser ajax requests.
	 *
	 * Called via `admin-ajax.php` (aka `ajaxurl` on the JS end). We catch all
	 * the calls here, parse out the correct path, and call the corresponding
	 * method to execute the relevant API call. Whew.
	 *
	 * @return void
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function catch_ajax() {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		self::$is_json_response = true;

		array_walk_recursive( $_POST, 'sanitize_text_field' );
		// @@TODO Ajax nonce.
		// Craft a function name to call.
		$method = strtolower( trim( $_POST['method'] ) );

		// Parse into snake_case.
		$path = str_replace( '/', ' ', trim( $_POST['path'] ) );
		$path = strtolower( ltrim( $path ) );
		$path = str_replace( ' ', '_', $path );

		// e.g. GET /projects -> get_projects
		$function = $method . '_' . $path;

		$remoteapi = isset($_POST["remoteapi"]);

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		try {
			if ( $remoteapi ) {
				// Perform direct call to API when $_GET['remoteapi'] is present.
				SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . " calling api_request('$method', '$path', ...)" );
				$path = $_POST['path'];
				$data = $_POST['data'];

				$result = self::api_request( $method, $path, $data );
			} elseif ( method_exists( __CLASS__, $function ) ) {
				// Call the method that matches (e.g. `self::delete_login()`).
				$args = array_filter(
					$_POST,
					function ( $i ) {
						return ( ! in_array( $i, [ 'method', 'path', 'action', 'remoteapi' ] ) );
					},
					ARRAY_FILTER_USE_KEY
				);

				SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . " function=$function _POST=" . json_encode( $_POST ) . ' args=' . json_encode( $args ) );

				if ( preg_match( ';^(get|post)_ajax_(.*)$;', $function, $regs ) ) {
					$key    = $regs[2];
					$key[0] = strtolower( $key[0] );
					unset( $args[ $key ] );
					SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . " key=$key args=" . json_encode( $args ) );
					$result = self::$function( $_POST[ $key ], $args );
				} else {
					SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );
					$result = self::$function( $args );
				}
			} else {
				throw new Exception( __( "Function doesn't exist", SEOTOOLSET_TEXTDOMAIN ) . " $function", 404 );
			}//end if
		} catch ( Exception $e ) {
			// Error
			$code = $e->getCode() ?: 200;

			SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' exception ' . $e->getMessage() );

			wp_send_json_error( "Error:\n" . $e->getMessage(), $code );
			wp_die();
		}//end try

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' Result is ' . json_encode( $result ) );

		// Add success message to json payload
		$code = self::$last_request['http_code'];
		if ( $code >= 200 ) {
			$code = 200;
		}
		wp_send_json_success( $result, $code );
		wp_die();
	}

	/**
	 * Make an API request to SEOT and return the result.
	 *
	 * @param string $type                 The HTTP request type; GET|POST|PUT|DELETE.
	 * @param string $path                 The API path to join with the base url.
	 * @param mixed  $data                 Post body data.
	 * @param array  $headers              Headers to add to the request.
	 * @param bool   $ignore_previous_errors Don't abandon request if there was a prior error.
	 *
	 * @return array On success: [meta: [http_code: int, count: int], ... ], On fail: [message: string, ... ]
	 */
	public static function api_request( $type, $path, $data = null, $headers = null, $ignore_previous_errors = false ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . "(type=$type, path=$path, data=" . wp_json_encode( $data ) . ', headers=' . wp_json_encode( $headers ) . ')' );

		if ( self::state_check_for_prior_errors( $ignore_previous_errors, $path ) ) {
			SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );
			return array();
		}

		if ( null !== $data ) {
			$qs   = is_object( $data ) || is_array( $data ) ? http_build_query( $data ) : '';
			$data = is_string( $data ) ? $data : json_encode( $data );
			if ( 'GET' === $type ) {
				$path = "{$path}?{$qs}";
			}
		}

		if ( null === $headers ) {
			$headers = self::header_defaults();
		}

		$headers = array_merge(
			[
				'Content-Type: application/json',
				'Content-Length: ' . strlen( $data ),
			],
			$headers
		);

		if ( self::is_authed() ) {
			$headers = array_merge(
				$headers,
				[
					'Authentication: ' . self::$session_id,
					'X-Company-Id: ' . self::$company_id,
				]
			);
		}

		$ch = curl_init( SEOTOOLSET_API_ENDPOINT . $path );
		curl_setopt( $ch, CURLOPT_POST, ( 'GET' === $type ? 0 : 1 ) );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $type );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'seotoolset-wp: ' . SEOTOOLSET_VERSION . '; php: ' . PHP_VERSION . '; curl: ' . implode(', ', curl_version()) );

		$result             = curl_exec( $ch );
		self::$last_request = curl_getinfo( $ch );
		$header_size        = self::$last_request['header_size'];
		$response_header    = substr( $result, 0, $header_size );
		$response_body      = substr( $result, $header_size );
		$http_code          = self::$last_request['http_code'];

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . " ('$type', '$path', " . $data . ', ' . json_encode( $headers ) . ') Response [http_code=' . $http_code . '] is ' . $response_body );

		if ( curl_errno( $ch ) ) {
			self::$last_error = curl_error( $ch );
			SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' Error is ' . self::$last_error );
		} else {
			self::$last_error = null;
		}

		$data = json_decode( $response_body, true );

		if ( ! is_array( $data ) && $http_code >= 400 ) {
			$message = __( 'Invalid response received from the API for ', SEOTOOLSET_TEXTDOMAIN ) . "$path";
			if ( null !== self::$last_error ) {
				$message = 'curl ' . __( 'error', SEOTOOLSET_TEXTDOMAIN ) . ': ' . self::$last_error;
			}
			$data = array(
				'code'     => '99999',
				'message'  => $message,
				'severity' => 'error',
			);
		}

		if ( self::response_check_for_errors( $data, $path ) ) {
			$data = array();
		}

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );

		if ( ! is_array( $data['meta'] ) ) {
			$data['meta'] = [];
		}
		$data['meta']['http_code'] = $http_code;
		if ( preg_match( ';^Item-Count: ([0-9]+);mi', $response_header, $regs ) ) {
			$data['meta']['count'] = (int) $regs[1];
		}

		return $data;
	}


	/**
	 * Perform post request.
	 *
	 * @param string $path                 Path to request.
	 * @param null   $data                 Data to pass.
	 * @param array  $headers              Headers to pass.
	 * @param bool   $ignore_previous_errors Ignore previous errors.
	 *
	 * @return array
	 */
	public static function post( $path, $data = null, $headers = [], $ignore_previous_errors = false ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$headers = array_merge( $headers, self::header_defaults() );

		return self::api_request( 'POST', "/{$path}", $data, $headers, $ignore_previous_errors );
	}

	/**
	 * Perform get request.
	 *
	 * @param string $path                 Path to request.
	 * @param null   $data                 Data to pass.
	 * @param array  $headers              Headers to pass.
	 * @param bool   $ignore_previous_errors Ignore previous errors.
	 *
	 * @return void
	 */
	public static function get( $path, $data = null, $headers = [], $ignore_previous_errors = false ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$headers = array_merge( $headers, self::header_defaults() );

		$call = self::api_request( 'GET', "/{$path}", $data, $headers, $ignore_previous_errors );

		header( 'Content-Type: application/json' );
		echo json_encode( $call );
	}

	/**
	 * Request delete method.
	 *
	 * @param string $path                 Path to request.
	 * @param null   $data                 Data to pass.
	 * @param array  $headers              Headers to pass.
	 * @param bool   $ignore_previous_errors Ignore previous errors.
	 *
	 * @return array
	 */
	public static function delete( $path, $data = null, $headers = [], $ignore_previous_errors = false ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$headers = array_merge( $headers, self::header_defaults() );

		return self::api_request( 'DELETE', "/{$path}", $data, $headers, $ignore_previous_errors );
	}

	// Below are the actual functionality of the API. All the params should
	// should already be sanitized by `catch_ajax()` since it loops over what
	// gets POST'd to it, sets up data, and then sends them off to these things.

	/**
	 * Request post login.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return bool
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function post_login( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' args=' . json_encode( $args ) );

		if ( self::is_authed() ) {
			return true;
		}

		$defaults = [
			'username' => null,
			'password' => null,
		];

		$data = wp_parse_args(
			[
				'username' => $args['username'],
				'password' => $args['password'],
			],
			$defaults
		);

		$headers = array( 'Content-Type: application/json' );

		$call = self::api_request( 'POST', '/login', $data, $headers, true );

		if ( self::assert_code( 201 ) ) {
			self::$company_id = $call['companyId'];
			self::$session_id = $call['sessionId'];

			SEOToolSet::update_setting(
				'login',
				[
					'username' => $data['username'],
				]
			);
			SEOToolSet::update_setting(
				'api',
				[
					'company_id' => self::$company_id,
					'session_id' => self::$session_id,
				]
			);
			SEOToolSet::user_is_subscribed( true );
		} elseif ( self::assert_code( 401 ) ) {
			self::$company_id = null;
			self::$session_id = null;

			SEOToolSet::remove_setting( 'api' );
			SEOToolSet::remove_setting( 'login' );
			SEOToolSet::remove_setting( 'project' );
			SEOToolSet::remove_setting( 'subscription' );

			throw new Exception( 'Invalid username or password.' );
		}//end if
	}

	/**
	 * Request post ajax setting.
	 *
	 * @param string $setting Setting to post.
	 * @param array  $args    Parameters to pass.
	 *
	 * @return array|bool|void
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function post_ajax_setting( $setting, $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . " setting=$setting args=" . json_encode( $args ) );

		return self::get_ajax_setting( $setting, $args );
	}

	/**
	 * Request get ajax setting.
	 *
	 * @param string $setting Setting to get.
	 * @param array  $args    Parameters to pass.
	 *
	 * @return array|bool|void
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function get_ajax_setting( $setting, $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . " setting=$setting args=" . json_encode( $args ) );

		switch ( $setting ) {
			case 'login':
				return self::post_login( $args );
			case 'logout':
				return self::delete_login();
			case 'signup':
				return;
			case 'project':
				if ( '-1' === $args['id'] ) {
					// Attempt to create project and update project id in postdata.
					$resp = self::post_projects(
						array(
							'name' => $args['new_name'],
							'url'  => $args['new_url'],
						)
					);
					if ( ! ( $resp['id'] > 0 ) ) {
						if ( '' !== $resp['message'] ) {
							throw new Exception( $resp['message'] );
						}
						throw new Exception( __( 'Error creating project!', SEOTOOLSET_TEXTDOMAIN ) );
					}

					$args['id']          = $resp['id'];
					$args['name']        = $args['new_name'];
					$args['url']         = $args['new_url'];
					$args['description'] = $args['new_description'];
				} else {
					$arr = SEOToolSet::get_setting( $setting );

					if ( $args['id'] !== $arr['id'] ) {
						$projects = self::get_projects();
						foreach ( $projects as $i => $project ) {
							$id = $project['id'];
							if ( $id === $args['id'] ) {
								$args['name']        = $project['name'];
								$args['url']         = $project['url'];
								$args['description'] = $project['description'];
								break;
							}
						}
					}
				}//end if
				unset( $args['new_url'], $args['new_name'], $args['new_description'] );
				break;

			default:
		}//end switch

		if ( $setting ) {
			$arr = SEOToolSet::get_setting( $setting );
			$arr = wp_parse_args( $args, $arr );
			SEOToolSet::update_setting( $setting, $arr );
		}
	}

	/**
	 * Nuke current login by removing state in settings.
	 *
	 * @return array
	 */
	public static function delete_login() {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		try {
			$call = self::delete( 'login', null, [], true );
		} catch ( Exception $e ) {
			// do nothing
			$call = array();
		}

		self::$company_id = null;
		self::$session_id = null;

		SEOToolSet::remove_setting( 'api' );
		SEOToolSet::remove_setting( 'login' );
		SEOToolSet::remove_setting( 'project' );
		SEOToolSet::remove_setting( 'subscription' );
		return $call;
	}

	/**
	 * Post oauth request to google.
	 *
	 * @param array $args Paramters to pass.
	 *
	 * @return array
	 */
	public static function post_oauth_google( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$args = wp_parse_args(
			$args,
			[
				'email'        => null,
				'accessToken'  => null,
				'refreshToken' => null,
				'analytics_id' => null,
				'project_id'   => null,
			]
		);

		$data = [
			'credentials' => [
				'email'        => $args['email'],
				'accessToken'  => $args['accessToken'],
				'refreshToken' => $args['refreshToken'],
				'analytics_id' => $args['analytics_id'],
				'project_id'   => $args['project_id'],
			],
		];

		$call = self::post( 'oauth/google', $data );

		if ( self::assert_code( 201 ) ) {
			// Save to settings? Or will that already have happened?
			console . log( 'success' );
		}
		return $call;
	}

	/**
	 * Update project.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return array
	 */
	public static function post_projects( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$args = wp_parse_args(
			$args,
			[
				'name' => null,
				'url'  => null,
			]
		);

		$data = [
			'project' => [
				'name' => $args['name'],
				'url'  => $args['url'],
			],
		];

		$call = self::post( 'projects', $data, [], true );

		if ( self::assert_code( 201 ) ) {
			// Anything to do?
		}

		return $call;
	}

	/**
	 * Removes a post.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return void
	 * @todo   not implemented
	 */
	public static function delete_projects( $args ) {
		// TODO
	}

	/**
	 * Update post.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return array
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function post_posts( $args, $is_post = false ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' args=' . print_r( $args, true ) );

		if ( ! $args['postId'] ) {
			throw new Exception( __( 'Missing', SEOTOOLSET_TEXTDOMAIN ) . ' postId' . print_r( $args, true ) );
		}

		if ( $is_post ) {
			$data = [
				'post' => [
					'url'       => get_permalink( $args['postId'] ),
					'post_id'   => $args['postId'],
					'author_id' => $args['authorId'],
				],
			];
		}
		else {
			$data = [
				'post' => [
					'title'        => $args['title'],
					'publishedUrl' => get_permalink( $args['postId'] ),
					'postId'       => $args['postId'],
					'contentBody'  => $args['contentBody'],
					'description'  => substr( $args['description'], 0, 2048 ),
					'authorId'     => $args['authorId'],
				],
			];
		}

		if ( 'custom' === $args['seotoolset_attributes'] ) {
			$directives_data['meta_robots'] = $args['seotoolset_attributes_custom'];
		} else {
			$directives_data['meta_robots'] = $args['seotoolset_attributes'];
		}
		$directives_data['canonical_url'] = $args['seotoolset_canonical_url'];

		$headers = self::header_defaults( $args['project_id'] );
		if ( $is_post ) {
			$return  = self::api_request( 'POST', '/posts/bulkadd/', $data, $headers );
		}
		else {
			$return  = self::api_request( 'POST', '/posts/', $data, $headers );
		}
		$call    = self::api_request( 'PUT', '/posts/' . $args['postId'] . '/directives/', $directives_data, $headers );

		if ( self::assert_code( 204 ) ) {
			// @@TODO Check code.
		}

		return $return;
	}

	/**
	 * Get default request headers.
	 *
	 * @param string $project_id Project identifier.
	 *
	 * @return array
	 */
	public static function header_defaults( $project_id = null ) {
		$headers = array();

		if ( null === $project_id ) {
			$project_id = SEOToolSet::get_setting( 'project.id' );
		}

		if ( $project_id > 0 ) {
			$headers[] = "X-Project-Id: {$project_id}";
		}

		return $headers;
	}

	/**
	 * Update keywords.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return mixed
	 */
	public static function post_keywords( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' entering' );

		$call   = self::api_request( 'POST', "/posts/{$args['post_id']}/keywords", $args );
		$return = self::return_template( 'ajax-post-keywords', $args );

		if ( self::assert_code( 204 ) ) {
			// @@TODO Check code.
		}

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );

		return $return;
	}

	/**
	 * Request meta keywords.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return mixed
	 */
	public static function get_metakeywords( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' entering' );
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' args=' . json_encode( $args ) );

		$return = self::return_template( 'ajax-post-meta-keywords', $args );

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );

		return $return;
	}

	/**
	 * Request summary statistics.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return mixed
	 */
	public static function get_summarystatistics( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' entering' );
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' args=' . json_encode( $args ) );

		$return = self::return_template( 'ajax-post-summary-statistics', $args );

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );

		return $return;
	}

	/**
	 * Update activity status.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return void
	 */
	public static function patch_activity( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' entering' );

		$alert_ids = explode( ',', $args['alerts'] );
		foreach ( $alert_ids as $alert_id ) {
			$args2 = array(
				'alert_id' => $alert_id,
				'status'   => $args['status'],
			);
			$call  = self::api_request( 'POST', '/dashboard/alerts', $args2 );
		}

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );
	}

	/**
	 * Patch keywords.
	 *
	 * @param array $args Parameters to pass.
	 *
	 * @return mixed
	 */
	public static function patch_keywords( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' entering' );

		$call   = self::api_request( 'PATCH', "/posts/{$args['post_id']}/keywords/{$args['keyword_id']}", $args );
		$return = self::return_template( 'ajax-post-keywords', $args );

		if ( self::assert_code( 204 ) ) {
			// @@TODO Check code.
		}

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );

		return $return;
	}

	/**
	 * Delete keywords.
	 *
	 * @param array $args Parameters.
	 *
	 * @return mixed
	 */
	public static function delete_keywords( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' entering' );

		$call   = self::api_request( 'DELETE', "/posts/{$args['post_id']}/keywords/{$args['keyword_id']}" );
		$return = self::return_template( 'ajax-post-keywords', $args );

		if ( self::assert_code( 204 ) ) {
			// @@TODO Check code.
		}

		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' leaving' );

		return $return;
	}

	/**
	 * Request traffic data.
	 *
	 * @param array $args Parameters.
	 *
	 * @return mixed
	 */
	public static function get_traffic( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$return = SEOToolSet::get_template( 'ajax-traffic', $args );
		if ( self::assert_code( 204 ) ) {
			// @@TODO Check code.
		}
		return $return;
	}

	/**
	 * Request post summary.
	 *
	 * @param array $args Parameters.
	 *
	 * @return mixed
	 */
	public static function get_posts_post_id_summary( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$args = wp_parse_args(
			$args,
			[
				'post_id'    => null,
				'project_id' => SEOToolSet::get_setting( 'project.id' ),
			]
		);

		$call = self::get( "posts/{$args['post_id']}/summary", null, self::header_defaults( $args['project_id'] ) );

		if ( self::assert_code( 200 ) ) {
		}

		return $call;
	}

	/**
	 * Request post keywords.
	 *
	 * @param array $args Parameters.
	 *
	 * @return mixed
	 */
	public static function get_posts_post_id_keywords( $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		$args = wp_parse_args(
			$args,
			[
				'post_id'    => null,
				'project_id' => SEOToolSet::get_setting( 'project.id' ),
			]
		);

		$call = self::get( "posts/{$args['post_id']}/keywords", null, self::header_defaults( $args['project_id'] ) );

		if ( self::assert_code( 200 ) ) {
		}

		return $call;
	}

	/**
	 * Request table data.
	 *
	 * @param string $table_sort Sort key.
	 * @param array  $args      Parameters.
	 *
	 * @return mixed
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function get_ajax_tablesort( $table_sort, $args ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . "(tableSort=$table_sort, args=" . wp_json_encode( $args ) . ')' );

		$name = $args['template'];
		if ( '' === $name ) {
			throw new Exception( __( 'Invalid template name.', SEOTOOLSET_TEXTDOMAIN ) );
		}
		$args = SEOToolSet::big_table_page_args( $name );
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . '(bigTablePageArgs=' . wp_json_encode( $args ) . ')' );
		$ignore_previous_errors = ( 'settings' === $name );
		$data                   = self::api_request( 'GET', "/dashboard/{$name}", $args, null, $ignore_previous_errors );

		return self::get_ajax_template(
			"$name-table-sort",
			[
				'args'  => $args,
				"$name" => $data,
				'limit' => $args['rowsPerPage'],
				'page'  => $args['page'],
			]
		);
	}

	/**
	 * Request project data.
	 *
	 * @return mixed
	 */
	public static function get_projects() {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		// Cache result.
		static $return = null;
		if ( null !== $return ) {
			return $return;
		}

		// Fetch data.
		$response = self::api_request( 'GET', '/projects', null, null, true );
		// array of id|name|url
		// Check response.
		if ( self::assert_code( 200 ) ) {
			// TODO
		}

		// Log response for debugging.
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' (): Response is ' . json_encode( $response ) );

		// Transform & cache data.
		$return = &$response;

		return $return;
	}

	/**
	 * Request the dashboard data.
	 *
	 * @param string $dash Name of dashboard.
	 * @param array  $args Pass through to template.
	 *
	 * @return mixed
	 */
	public static function get_dashboard( $dash, $args = null ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ );

		// Cache result.
		static $return = array();
		if ( isset( $return[ $dash ] ) ) {
			return $return[ $dash ];
		}

		// Fetch data.
		if ( SEOToolSet::user_is_logged_in_subscribed_with_project() ) {
			$response = self::api_request( 'GET', "/dashboard/{$dash}", $args );
		}

		// Check response.
		if ( self::assert_code( 200 ) ) {
			// TODO
		}

		// Log response for debugging.
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . "('$dash'): Args are " . json_encode( $args ) . ' Response is ' . json_encode( $response ) );

		// Transform & cache data.
		switch ( $dash ) {
			default:
			case 'activity':
				// array of post_id|severity|post_title|alerts|author|last_updated|keywords|page_views
			case 'alerts':
				// array of alert_id|post_id|date|message|severity|status
			case 'authors':
				// array of number_of_pages|ranking|last_published|page_views|page_views_per_page|average_time_per_page|contributions
			case 'content':
				// array of post_id|post_title|ranking|author|date_posted|average_time|views|shared
			case 'keywords':
				// array of keyword_id|keyword_name|number_of_pages_assigned|highest_rank|page_views|number_of_clicks|number_of_impressions|click_through_rate
			case 'traffic':
				// array of start_date|end_date|points
				$return[ $dash ] = $response;
				break;

			case 'seoscore':
				// array of primary_score|month_change|page_score|mobile_score
				$tmp                  = $response[0];
				$tmp['primary_score'] = floor( $tmp['primary_score'] ) . '%';
				// 94%
				$tmp['mobile_score'] = floor( $tmp['mobile_score'] );
				// 92
				$tmp['page_score'] = floor( $tmp['page_score'] );
				// 97
				$tmp['month_change'] = preg_replace( ';([0-9]+)[.][0-9]+;', '\1', $tmp['month_change'] );
				// +10% from last month
				$return[ $dash ] = $tmp;
				break;
		}//end switch

		return $return[ $dash ];
	}

	/**
	 * Returns contents of an ajax template.
	 *
	 * @param string $template (e.g. dashboard -> (gets interpreted as) ajax-dashboard).
	 * @param array  $args     Pass through to template.
	 *
	 * @return any (returns native object, not json)
	 */
	public static function post_ajax_template( $template, $args = null ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' (template=' . $template . ')' );
		return self::get_ajax_template( $template, $args );
	}

	/**
	 * Fetch an ajax template.
	 *
	 * @param string $template Template name.
	 * @param array  $args     Pass through to template.
	 *
	 * @return mixed
	 */
	public static function get_ajax_template( $template, $args = null ) {
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . ' (template=' . $template . ')' );

		// Cache result.
		static $return = array();
		// Fetch data.
		$fn = function ( $template ) use ( $args ) {
			$ob   = ob_start();
			$ret  = SEOToolSet::get_template( $template, $args );
			$resp = ob_get_contents();
			ob_end_clean();

			return $resp ?: $ret;
		};

		// Preserve content type header.
		$headers = headers_list();

		if ( is_array( $template ) ) {
			$template = $template['template'];
		}

		// Capture output.
		$response = $fn( "ajax-$template" );

		// Restore content type header, if changed.
		header_remove( 'Content-Type' );
		foreach ( $headers as $header ) {
			if ( preg_match( ';^Content-Type:;i', $header ) ) {
				header( $header );
			}
		}

		// Check response.
		if ( self::assert_code( 204 ) ) {
			// TODO
		}

		// Log response for debugging.
		SEOToolSet::log( __CLASS__ . ':' . __METHOD__ . ':' . __LINE__ . " ('$template'): Response is $response" );

		return $response;
	}

	/**
	 * Fetch a template.
	 *
	 * @param string $template Template name.
	 * @param bool   $args     Pass through to template.
	 *
	 * @return mixed
	 */
	public function return_template( $template, $args = false ) {
		$fn = function ( $template ) use ( $args ) {
			$ob   = ob_start();
			$ret  = SEOToolSet::get_template( $template, $args );
			$resp = ob_get_contents();
			ob_end_clean();

			return $resp ?: $ret;
		};

		return $fn( $template );
	}
}
