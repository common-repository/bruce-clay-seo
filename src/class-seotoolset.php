<?php
/**
 * SEOToolSet class.
 *
 * This file supports the usage of the page templates and helper functions.
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
 * This class supports the usage of the page templates, along with helper functions.
 *
 * @category SEOToolSet
 * @package  SEOToolSet
 * @author   SEOToolSet <support@seotoolset.com>
 * @license  GNU General Public License, version 3
 * @link     http://www.seotoolset.com/
 */
class SEOToolSet {

	/**
	 * Plugin settings array.
	 *
	 * @var array
	 */
	private static $_settings;

	/**
	 *  Fetch settings and trigger setup hooks.
	 */
	public function __construct() {
		self::get_settings();
		$this->setup_hooks();
		$GLOBALS['seotoolset'] = &$this;
	}

	/**
	 * Translate severity to level.
	 *
	 * @param string $severity Severity input.
	 *
	 * @return string
	 */
	public static function severity_to_level( $severity ) {
		$color = self::severity_to_color( $severity );

		$map = array(
			'red'    => 'error',
			'yellow' => 'warning',
			'green'  => 'info',
		);

		$name = isset( $map[ $color ] ) ? $map[ $color ] : 'warning';

		return $name;
	}

	/**
	 * Translate severity to color.
	 *
	 * @param string $severity Severity input.
	 *
	 * @return string
	 */
	public static function severity_to_color( $severity ) {
		if ( preg_match( ';^([0-3]|low|log|info|debug|green)$;i', $severity ) ) {
			$color = 'green';
		} elseif ( preg_match( ';^([4-7]|medium|warn|warning|yellow)$;i', $severity ) ) {
			$color = 'yellow';
		} elseif ( preg_match( ';^([7-9]|10|high|err|error|red)$;i', $severity ) ) {
			$color = 'red';
		} else {
			$color = 'blue';
		}

		return $color;
	}

	/**
	 * Return a tally of activity alerts.
	 *
	 * @param array $activity Activity to process.
	 *
	 * @return array
	 */
	public static function activity_alert_counts( &$activity ) {
		$alert_counts = array(
			'seen'    => 0,
			'unseen'  => 0,
			'deleted' => 0,
		);
		$alerts       = &$activity['alerts'];
		foreach ( $alerts as $alert ) {
			$alert_counts[ $alert['status'] ]++;
		}
		return $alert_counts;
	}

	/**
	 * Return true if Yoast installed and feature is redundant.
	 *
	 * @param string $feature Feature to check.
	 *
	 * @return bool
	 */
	public static function is_duplicate_feature( $feature ) {
		static $is_active = null;

		try {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$is_active = is_plugin_active( 'wordpress-seo/wp-seo.php' );
			// Yoast
		} catch ( Exception $e ) {
			// ignore
			$is_active = false;
		}

		// Play nicely with Yoast.
		if ( $is_active ) {
			switch ( $feature ) {
				case 'post__search_directives':
				case 'post__meta_description':
				case 'front_page__title':
				case 'front_page__meta_description':
				case 'site__webmaster_verification_codes':
				case 'site__robots_txt':
					return true;

				default:
				case 'site__google_analytics':
					break;
			}
		}

		return false;
	}


	/**
	 * Pass-through to add_admin_notice function.
	 *
	 * @param string $message     Message text.
	 * @param string $class       CSS class.
	 * @param bool   $dismissible Allow dismissing.
	 *
	 * @return string
	 */
	private function priv_add_admin_notice( $message, $class = 'notice-error', $dismissible = true ) {
		return self::add_admin_notice( $message, $class, $dismissible );
	}

	/**
	 * Output admin notice.
	 *
	 * @param string $message     Message text.
	 * @param string $class       CSS class.
	 * @param bool   $dismissible Allow dismissing.
	 *
	 * @return void
	 */
	public static function add_admin_notice( $message, $class = 'notice-error', $dismissible = true ) {
		$output  = '';
		$output .= '<div class="notice ' . $class;
		if ( $dismissible ) {
			$output .= ' is-dismissible';
		}
		$output .= '">';
		$output .= '<p>' . nl2br( $message ) . '</p></div>';
		print( $output );
	}

	/**
	 * Output admin notice if it's unique.
	 *
	 * @param string $message Message to display.
	 * @param string $level   Severity level.
	 * @param string $path    Path for deduping.
	 *
	 * @return void
	 */
	public static function add_admin_notice_dedupe_path( $message, $level = 'warning', $path = null ) {
		static $dupes = array();

		if ( isset( $dupes[ $message ] ) ) {
			return;
		}
		$dupes[ $message ] = true;

		$prepend_plugin_name = $path ? ! preg_match( ';^/(dashboard)(|/.*|[#?&].*)$;', $path ) : false;
		$notice              = $prepend_plugin_name ? __( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ) . ": $message" : $message;
		self::add_admin_notice( $notice, "notice-$level", true );
	}

	/**
	 * Set up WordPress hooks.
	 *
	 * @return void
	 */
	private function setup_hooks() {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'wp_head', [ $this, 'add_head_things' ] );
		add_action( 'wp_footer', [ $this, 'source_attribution' ] );

		add_filter( 'wp_title', [ $this, 'post_title' ] );
		add_filter( 'document_title_parts', [ $this, 'post_title' ], 99, 3 );
		add_filter( 'get_canonical_url', [ $this, 'canonical_url' ], 99, 2 );

		add_action( 'save_post', [ $this, 'api_posts_post' ] );
		add_action( 'wp_ajax_save_meta_description', [ $this, 'save_meta_description' ] );

		if ( is_admin() ) {
			// Backend setup.
			add_action( 'admin_init', [ $this, 'admin_init' ] );
			add_action( 'admin_body_class', [ $this, 'admin_body_class' ] );
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

			// Real and pseudo meta boxes.
			add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
			add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

			// Deal with post saving.
			add_action( 'save_post', [ $this, 'update_all_post_meta' ], 10, 3 );
			add_action( 'save_post', [ $this, 'remove_highlights' ], 10, 3 );

			// Miscellanea.
			add_action( 'mce_css', [ $this, 'add_editor_styles' ] );
		}
	}


	/**
	 * Store updated settings from _POST.
	 *
	 * @return void
	 */
	private function update_settings() {
		if ( empty( $_POST ) || ! isset( $_POST['seotoolset_update_settings'] ) ) {
			return;
		}

		if ( '-1' === $_POST['seotoolset_update_settings'] ) {
			$this->remove_all_settings();
			$this->priv_add_admin_notice( __( 'Killed all the settings!', SEOTOOLSET_TEXTDOMAIN ) );
			return;
		}

		$seotoolset_settings_nonce = '';
		if ( isset( $_POST['seotoolset_settings_nonce'] ) ) {
			$seotoolset_settings_nonce = $_POST['seotoolset_settings_nonce'];
		}
		if ( ! wp_verify_nonce( $seotoolset_settings_nonce, 'seotoolset_settings' ) ) {
			$this->priv_add_admin_notice( __( 'Nonce could not be verified! Your SEOToolSet settings were not updated.', SEOTOOLSET_TEXTDOMAIN ) );
			return;
		}

		$postdata = $_POST;
		// Don't want to overwrite it, probably.
		array_walk_recursive( $postdata, 'sanitize_text_field' );

		if ( '-1' === $postdata['project']['id'] ) {
			// Attempt to create project and update project id in postdata.
			$resp = SEOToolSetAPI::post_projects(
				array(
					'name' => $postdata['project']['new_name'],
					'url'  => $postdata['project']['new_url'],
				)
			);
			if ( $resp['id'] > 0 ) {
				$postdata['project']['id'] = $resp['id'];
			} else {
				unset( $postdata['project'] );
				$this->priv_add_admin_notice( __( 'Error creating project!', SEOTOOLSET_TEXTDOMAIN ) );
			}
		}

		if ( isset( $postdata['project'] ) ) {
			unset( $postdata['project']['new_name'], $postdata['project']['new_url'], $postdata['project']['new_description'] );
		}

		unset( $postdata['seotoolset_update_settings'], $postdata['seotoolset_settings_nonce'], $postdata['_wp_http_referer'] );

		// `update_option()` *seems* to sanitize input via `sanitize_option()`
		// but since I'm not entirely sure, I've preemptively done it above.
		$settings = self::get_settings();

		$no_changes = true;

		foreach ( $postdata as $key => $value ) {
			if ( isset( $settings[ $key ] ) ) {
				if ( wp_json_encode( $settings[ $key ] ) === wp_json_encode( $value ) ) {
					continue;
				}
			}
			$no_changes = false;
			break;
		}

		if ( $no_changes ) {
			$this->priv_add_admin_notice( __( 'Your SEOToolSet settings have not changed.', SEOTOOLSET_TEXTDOMAIN ), 'notice-info' );
			return;
		}

		$success = update_option( 'seotoolset_settings', array_merge( $settings, $postdata ) );

		if ( $success ) {
			$this->priv_add_admin_notice( __( 'Your SEOToolSet settings have been updated!', SEOTOOLSET_TEXTDOMAIN ), 'notice-success' );
			return;
		}

		$this->priv_add_admin_notice( __( 'Something went wrong! Your SEOToolSet settings were not updated.', SEOTOOLSET_TEXTDOMAIN ) );
	}

	/**
	 * Update a top-level setting.
	 *
	 * @param string $setting Setting to update.
	 * @param string $value   Value to set.
	 *
	 * @return void
	 */
	public function update_setting( $setting, $value ) {
		self::log( __CLASS__ . ':' . __FUNCTION__ . ':' . __LINE__ . ' setting=' . $setting . ' value=' . wp_json_encode( $value ) . '' );

		if ( is_array( $value ) ) {
			array_walk_recursive( $value, 'sanitize_text_field' );
		} else {
			$value = sanitize_text_field( $value );
		}

		$settings             = self::get_settings();
		$settings[ $setting ] = $value;
		update_option( 'seotoolset_settings', $settings );
		$settings = self::get_settings();
	}

	/**
	 * Remove a top-level setting.
	 *
	 * @param string $setting Setting to remove.
	 *
	 * @return void
	 */
	public function remove_setting( $setting ) {
		$settings = self::get_settings();
		unset( $settings[ $setting ] );
		update_option( 'seotoolset_settings', $settings );
		$settings = self::get_settings();
	}

	/**
	 * Kill all the settings. Probably only gonna be for debugging and plugin
	 * uninstall.
	 *
	 * @return void
	 */
	public function remove_all_settings() {
		delete_option( 'seotoolset_settings' );
		self::$_settings = [];
	}

	/**
	 * Update post meta after sanitizing fields.
	 *
	 * @param int   $post_id Related post.
	 * @param mixed $post    not used.
	 * @param mixed $update  not used.
	 *
	 * @return void
	 */
	public function update_all_post_meta( $post_id, $post = null, $update = null ) {
		$regulars = [
			'meta_title',
			'meta_description',
			'attributes',
			'canonical_url',
		];

		foreach ( $regulars as $field ) {
			if ( isset( $_POST[ 'seotoolset_' . $field ] ) ) {
				update_post_meta( $post_id, '_seotoolset_' . $field, sanitize_text_field( $_POST[ 'seotoolset_' . $field ] ) );
			}
		}

		if ( isset( $_POST['seotoolset_attributes'] ) && 'custom' === $_POST['seotoolset_attributes'] ) {
			$seotoolset_attributes_custom = '';
			if ( isset( $_POST['seotoolset_attributes_custom'] ) ) {
				$seotoolset_attributes_custom = $_POST['seotoolset_attributes_custom'];
			}
			$custom = sanitize_text_field( $seotoolset_attributes_custom );
			update_post_meta( $post_id, '_seotoolset_attributes', str_replace( ' ', '', $custom ) );
		}
	}

	/**
	 * Remove temporary highlight html from post.
	 *
	 * When you highlight keywords, actual markup is inserted into the post
	 * content. If it's saved after this, the highlights will stay and become
	 * part of the post, showing up on the frontend!
	 *
	 * @param int    $post_id Related post id.
	 * @param object $post    Related post.
	 * @param mixed  $update  not used.
	 *
	 * @return void
	 */
	public function remove_highlights( $post_id, $post, $update = null ) {
		// If we don't check, we may end up in an infinite loop of revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Similarly, if we don't remove and readd this hook, we will end up in
		// an infinite loop of `save_post` calls. This could be avoided if WP
		// offered an `update_post_field()` function but I didn't find one.
		remove_action( 'save_post', [ $this, 'remove_highlights' ] );

		$post->post_content = preg_replace( '|<mark class="seotoolset"[^>]+?[>](.*?)</mark>|i', '$1', $post->post_content );
		wp_update_post( $post );

		add_action( 'save_post', [ $this, 'remove_highlights' ], 10, 3 );
	}

	// ----- STATIC HELPERS ------------------------------------------------- //

	/**
	 * Navigate an array with dot notation.
	 *
	 * Don't pass the main array via reference; we're blowing it up at each step down into a subarray.
	 *
	 * @param array  $array Input array.
	 * @param string $path  Path to fetch.
	 *
	 * @return array|bool
	 */
	public static function get_array_field( $array, $path ) {
		if ( ! is_array( $array ) ) {
			return false;
		}

		$keys = explode( '.', $path );

		if ( ! is_array( $keys ) ) {
			return false;
		}

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, (array) $array ) ) {
				$array = $array[ $key ];
			} else {
				return false;
			}
		}

		return $array;
	}

	/**
	 * Retrieve plugin settings from WordPress.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option( 'seotoolset_settings' );

		self::$_settings = ( $settings ) ? $settings : [];

		return self::$_settings;
	}

	/**
	 * Fetch specific plugin setting from WordPress.
	 *
	 * @param string $setting Setting to get.
	 *
	 * @return mixed
	 */
	public static function get_setting( $setting ) {
		if ( ! $setting ) {
			return self::$_settings;
		}

		$setting = self::get_array_field( self::$_settings, $setting );

		if ( '0' === $setting ) {
			return false;
		}

		if ( '1' === $setting ) {
			return true;
		}

		return $setting;
	}

	/**
	 * Fetch post meta data.
	 *
	 * @param string $field   Field to fetch.
	 * @param int    $post_id Related post id.
	 * @param bool   $single  True if single.
	 *
	 * @return mixed
	 */
	public static function get_post_meta( $field, $post_id = 0, $single = true ) {
		$post_id = ( $post_id ) ? $post_id : $GLOBALS['post']->ID;

		return get_post_meta( $post_id, '_seotoolset_' . $field, $single );
	}

	/**
	 * Update post meta data.
	 *
	 * @param string $field   Field to update.
	 * @param mixed  $value   Value to set.
	 * @param int    $post_id Related post.
	 *
	 * @return mixed
	 */
	public static function update_post_meta( $field, $value, $post_id = 0 ) {
		$post_id = ( $post_id ) ? $post_id : $GLOBALS['post']->ID;

		return update_post_meta( $post_id, '_seotoolset_' . $field, $value );
	}

	/**
	 * Output link to plugin page.
	 *
	 * @param string $page Page slug to return.
	 *
	 * @return void
	 */
	public static function page_link( $page ) {
		echo self::get_page_link( $page );
	}

	/**
	 * Return link to plugin page.
	 *
	 * @param string $page Page slug to return.
	 *
	 * @return string
	 */
	public static function get_page_link( $page ) {
		$page = str_replace( 'seotoolset-', '', $page );
		return admin_url( "admin.php?page=seotoolset-{$page}" );
	}

	/**
	 * Output pill html for keyword.
	 *
	 * @param string $keyword Keyword to display.
	 *
	 * @return void
	 */
	public static function keyword_pill( $keyword ) {
		return self::pill( $keyword['target'], $keyword['range']['minimum'], $keyword['range']['maximum'] );
	}

	/**
	 * Output pill html.
	 *
	 * This has been mostly reworked on the front end with javascript.
	 *
	 * @param int $have Current value.
	 * @param int $min  Min value.
	 * @param int $max  Max value.
	 *
	 * @return void
	 */
	public static function pill( $have, $min, $max ) {
		echo '<span class="pill">';
		printf( '<span class="have">-</span>', $have );
		printf( '<span class="goal%s">%d-%d</span>', '', $min, $max );
		echo '</span>';
	}

	/**
	 * Return message/prompt to set up plugin.
	 *
	 * @return string
	 */
	public static function plugin_setup_not_complete() {
		$settings_url = self::get_page_link( 'settings' );
		/* translators: %s: seotoolset domain */
		$message = sprintf( __( 'Plugin setup is not complete. Go to <a href="%s">Bruce Clay SEO -- Settings</a> to authorize data from the SEOToolSet® and Google.', SEOTOOLSET_TEXTDOMAIN ), $settings_url );
		return $message;
	}

	/**
	 * Output a template and return an array of returned values.
	 *
	 * Include a template partial similar to `get_template_part` but with the
	 * ability to pass variables to that partial.
	 *
	 * @param string     $template Name of the template to include.
	 * @param array|bool $args     Variables to extract for this template's use.
	 *
	 * @return string
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function get_template( $template, $args = false ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		// Sanity check inputs.
		if ( ! preg_match( ';^[a-z0-9_-]+$;', $template ) ) {
			throw new Exception( __( 'Invalid template name.', SEOTOOLSET_TEXT_DOMAIN . '' ) . ' (' . $template . ')' );
		}

		if ( file_exists( SEOTOOLSET_DIR_PATH . "src/templates/{$template}.php" ) ) {
			include SEOTOOLSET_DIR_PATH . "src/templates/{$template}.php";
		}
	}

	/**
	 * Return true if login settings are present.
	 *
	 * @return bool
	 */
	public static function user_is_logged_in() {
		return (
			self::get_setting( 'api.session_id' ) &&
			self::get_setting( 'api.company_id' )
		);
	}

	/**
	 * Return true if subscription is valid according to API call.
	 *
	 * @param bool $force Set to true to force a remote check.
	 *
	 * @return bool|null
	 */
	public static function user_is_subscribed( $force = false ) {
		static $is_subscribed = null;

		if ( ! $force && null !== $is_subscribed ) {
			return $is_subscribed;
		}

		$subscription = get_transient( 'bcseo_subscription' );
		if ( $force || false === $subscription ) {
			// this code runs when there is no valid transient set
			$subscription = SEOToolSetAPI::api_request( 'GET', '/subscription', null, null, true );
			set_transient( 'bcseo_subscription', $subscription, 24 * HOUR_IN_SECONDS );
		}

		$is_subscribed = ( '404' !== $subscription['meta']['http_code'] );

		// username, email, plan, first_name, last_name, postal_code, account_code
		if ( $is_subscribed ) {
			self::update_setting( 'subscription', $subscription );
		} else {
			self::remove_setting( 'subscription' );
		}

		return $is_subscribed;
	}

	/**
	 * Return true if project setting appears valid.
	 *
	 * @return bool
	 */
	public static function user_has_project() {
		return (
			self::get_setting( 'project.id' ) > 0
		);
	}

	/**
	 * Return true if user is logged in, subscribed with an active project.
	 *
	 * @return bool
	 */
	public static function user_is_logged_in_subscribed_with_project() {
		return self::user_is_logged_in() && self::user_is_subscribed() && self::user_has_project();
	}

	/**
	 * Throw exception if user is not logged in.
	 *
	 * @param bool $force_fail Set to true to force exception.
	 *
	 * @return bool
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function require_user_logged_in( $force_fail = false ) {
		if ( ! $force_fail ) {
			if ( self::user_is_logged_in() ) {
				return true;
			}
		}

		throw new Exception( self::getplugin_setup_not_complete() );
	}

	/**
	 * Throw exception if user not subscribed.
	 *
	 * @param bool $force_fail Set to true to force exception.
	 *
	 * @return bool
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function require_user_subscribed( $force_fail = false ) {
		if ( ! $force_fail ) {
			self::require_user_logged_in();

			if ( self::user_is_subscribed() ) {
				return true;
			}
		}

		throw new Exception( self::getplugin_setup_not_complete() );
	}

	/**
	 * Throw exception if project not set up.
	 *
	 * @param bool $force_fail Set to true to force exception.
	 *
	 * @return bool
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function require_user_project( $force_fail = false ) {
		if ( ! $force_fail ) {
			self::require_user_logged_in();

			if ( self::user_has_project() ) {
				return true;
			}
		}

		throw new Exception( self::getplugin_setup_not_complete() );
	}

	/**
	 * Return incomplete setup message.
	 *
	 * @return string
	 */
	public static function getplugin_setup_not_complete() {
		return __( 'Setup is incomplete. Make sure you are logged in, subscribed and have an active project selected.', SEOTOOLSET_TEXTDOMAIN );
	}

	/**
	 * Throw exception if user not logged in, subscribed w/active project.
	 *
	 * @param string $path Default path.
	 *
	 * @return bool
	 * @throws Exception In case of failure, an exception is thrown.
	 */
	public static function require_user_project_subscribed( $path = null ) {
		try {
			self::require_user_logged_in();
			self::require_user_subscribed();
			self::require_user_project();
		} catch ( Exception $e ) {
			self::add_admin_notice_dedupe_path( $e->getMessage(), 'warning', $path );
			throw $e;
		}

		return true;
	}


	/**
	 * Store a setting in user's meta.
	 *
	 * @param string $key   Setting key.
	 * @param mixed  $value Setting value.
	 *
	 * @return void
	 */
	public static function set_session_var( $key, $value ) {
		$user_id = get_current_user_id();
		$expires = '30 minutes';
		if ( is_array( $value ) ) {
			$value = json_encode( $value );
		}
		update_user_meta( $user_id, "bcseo_{$key}", strtotime( $expires ) . '&' . $value );
	}

	/**
	 * Return a setting in user's meta.
	 *
	 * @param string $key Setting key.
	 *
	 * @return mixed|null
	 */
	public static function get_session_var( $key ) {
		$value          = null;
		$user_id        = get_current_user_id();
		$db_value       = get_user_meta( $user_id, "bcseo_{$key}", true );
		$splitted_value = explode( '&', $db_value );
		if ( 2 === count( $splitted_value ) && $splitted_value[0] > time() ) {
			$value = json_decode( $splitted_value[1], true );
			// Value is not JSON
			if ( JSON_ERROR_NONE !== json_last_error() ) {
				$value = $splitted_value[1];
			}
			return $value;
		}
		return null;
	}


	/**
	 * Return true if user has permission.
	 *
	 * @param string $ability Capability identifier.
	 *
	 * @return bool
	 */
	public static function check_permissions( $ability = 'null' ) {
		$capability = self::get_setting( "permissions.{$ability}" );

		if ( ! $capability ) {
			$capability = 'edit_pages';
		}

		return ( current_user_can( $capability ) );
	}

	/**
	 * Log message to debug log.
	 *
	 * @param string $message Debug message.
	 *
	 * @return void
	 */
	public static function log( $message ) {
		static $uniq = null;

		if ( null === $uniq ) {
			$uniq = substr( md5( getmypid() . microtime( true ) ), 0, 12 );
		}

		if ( ! defined( 'SEOTOOLSET_DEBUG_LOGGING' ) || ! SEOTOOLSET_DEBUG_LOGGING ) {
			return;
		}

		$fh   = fopen( SEOTOOLSET_DIR_PATH . '/log.txt', 'a' );
		$line = date( 'c' ) . " [$uniq] {$message}\n";
		fwrite( $fh, $line );
		fclose( $fh );
	}

	// ----- PUBLIC HOOKS --------------------------------------------------- //

	/**
	 * Initialize by fetching settings from WordPress.
	 *
	 * @return void
	 */
	public function init() {
		self::$_settings = self::get_settings();
	}

	/**
	 * `wp_title()` is "deprecated" in WP v4.4+, so we'll handle both through
	 * the newer `wp_get_document_title()`. The no-good former method sends a
	 * string while the latter, sanely, provides an array.
	 *
	 * @param array $title Page title.
	 *
	 * @return array
	 */
	public function post_title( $title ) {
		// On singles, replace with metadata if it's there.
		if ( is_singular() ) {
			if ( is_array( $title ) ) {
				$title['title'] = self::get_post_meta( 'meta_title' );
			} else {
				$title = wp_get_document_title();
			}
		}

		// On the front page, if they're not using a page, use the default.
		if ( is_front_page() && ! get_option( 'page_on_front' ) && ! self::is_duplicate_feature( 'front_page__title' ) ) {
			$default = self::get_setting( 'defaults.title' );
			if ( $default ) {
				$title['title']   = $default;
				$title['tagline'] = false;
			}
		}

		return $title;
	}

	/**
	 * Return canonical url for current post.
	 *
	 * @param string $url  URL to default.
	 * @param string $post Current post.
	 *
	 * @return string
	 */
	public function canonical_url( $url, $post ) {
		$replace = self::get_post_meta( 'canonical_url', is_object( $post ) ? $post->ID : 0 );

		return ( $replace ) ? user_trailingslashit( $replace ) : $url;
	}

	/**
	 * Output header assets.
	 *
	 * @return void
	 */
	public function add_head_things() {
		if ( ! self::is_duplicate_feature( 'site__webmaster_verification_codes' ) ) {
			// FIXME:yoast
			$google = self::get_setting( 'verification.google' );
			if ( $google ) {
				echo '<meta name="google-site-verification" content="' . $google . '">' . "\n";
			}

			$bing = self::get_setting( 'verification.bing' );
			if ( $bing ) {
				echo '<meta name="msvalidate.01" content="' . $bing . '">' . "\n";
			}
		}

		if ( ! self::is_duplicate_feature( 'site__google_analytics' ) ) {
			$tracking_disabled = self::get_setting( 'google.disable_tracking' );
			$analytics         = self::get_setting( 'google.analytics_id' );
			if ( $analytics && ! $tracking_disabled ) {
				$uaid = trim( explode( '|', $analytics )[0] );
				self::get_template( 'analytics', [ 'uaid' => $uaid ] );
			}
		}

		if ( is_singular() && ! self::is_duplicate_feature( 'post__meta_description' ) ) {
			echo '<meta name="description" content="' . self::get_post_meta( 'meta_description' ) . '">' . "\n";
			echo '<meta name="robots" content="' . self::get_post_meta( 'attributes' ) . '">' . "\n";
		}

		// On the front page, if they're not using a page, use the default.
		if ( is_front_page() && ! get_option( 'page_on_front' ) && ! self::is_duplicate_feature( 'front_page__meta_description' ) ) {
			$default = self::get_setting( 'defaults.description' );
			if ( $default ) {
				echo '<meta name="description" content="' . $default . '">' . "\n";
			}
		}
	}

	/**
	 * Output attribution html comment.
	 *
	 * @return void
	 */
	public function source_attribution() {
		echo '<!-- ' . __( 'This site is managed with Bruce Clay SEO.', SEOTOOLSET_TEXTDOMAIN ) . " -->\n";
	}

	// ----- ADMIN HOOKS ---------------------------------------------------- //

	/**
	 * Init admin settings.
	 *
	 * @return void
	 */
	public function admin_init() {
		// Set default permissions if necessary.
		if ( ! self::get_setting( 'permissions' ) ) {
			$permissions = [
				'show_dashboard' => 'edit_posts',
				'show_panels'    => 'edit_pages',
				'edit_panels'    => 'edit_pages',
				'edit_settings'  => 'edit_pages',
			];

			$this->update_setting( 'permissions', $permissions );
		}

		$this->update_settings();

		self::$_settings = self::get_settings();

		if ( self::get_setting( 'suspended' ) ) {
			$this->priv_add_admin_notice( '<a href="' . self::get_page_link( 'status' ) . '">' . __( 'SEOToolSet operations have been suspended!', SEOTOOLSET_TEXTDOMAIN ) . '</a>', 'notice-warning', false );
		}
	}

	/**
	 * Get admin body class.
	 *
	 * @param string $classes CSS classes to include.
	 *
	 * @return string
	 */
	public function admin_body_class( $classes = '' ) {
		return $classes . ( $GLOBALS['is_chrome'] ) ? 'chrome' : '';
	}

	/**
	 * Create admin menu class.
	 *
	 * @return void
	 */
	public function admin_menu() {
		new SEOToolSet_Admin_Menu();
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'seotoolset-admin-style', SEOTOOLSET_DIR_URL . 'css/admin-style.css', [], SEOTOOLSET_VERSION );
		wp_enqueue_style( 'seotoolset-admin-daterangepicker', SEOTOOLSET_DIR_URL . 'css/daterangepicker.css', [], SEOTOOLSET_VERSION );
		wp_enqueue_script( 'seotoolset-scripts', SEOTOOLSET_DIR_URL . 'js/script.js', [ 'jquery' ], SEOTOOLSET_VERSION );
		wp_enqueue_script( 'seotoolset-scripts-momentjs', SEOTOOLSET_DIR_URL . 'js/moment.min.js', [ 'jquery' ], SEOTOOLSET_VERSION );
		wp_enqueue_script( 'seotoolset-scripts-daterangepicker', SEOTOOLSET_DIR_URL . 'js/daterangepicker.js', [ 'jquery' ], SEOTOOLSET_VERSION );
		$this->localize_save_meta_description_script();
	}

	/**
	 * Function to localize script and register variables to save Meta Description
	 */
	public function localize_save_meta_description_script() {
		global $post;
		if ($post != null && property_exists($post, "ID")) 
		{
			wp_localize_script(
				'seotoolset-scripts',
				'seotoolset_vars',
				[
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'post_id'  => $post->ID,
				]
			);
		}
	}

	/**
	 * Function to save Meta Description via AJAX
	 */
	public function save_meta_description() {
		global $wpdb;
		$response = [];
		if ( ! isset( $_POST['seotoolset_post_id'] ) ||
			! isset( $_POST['seotoolset_meta_title'] ) ||
			! isset( $_POST['seotoolset_meta_description'] ) ||
			'' === $_POST['seotoolset_post_id'] ||
			'' === $_POST['seotoolset_meta_title'] ||
			'' === $_POST['seotoolset_meta_description'] ) {
			$response = [
				'status'  => 400,
				'message' => 'All fields are mandatory to save Meta description data.',
			];
			echo json_encode( $response );
			wp_die();
		}
		$post_id          = sanitize_text_field( trim( $_POST['seotoolset_post_id'] ) );
		$meta_title       = sanitize_text_field( trim( $_POST['seotoolset_meta_title'] ) );
		$meta_description = sanitize_text_field( trim( $_POST['seotoolset_meta_description'] ) );
		if ( ! is_numeric( $post_id ) ) {
			$response = [
				'status'  => 400,
				'message' => 'There was an error getting post id.',
			];
			echo json_encode( $response );
			wp_die();
		}
		update_post_meta( $post_id, '_seotoolset_meta_title', $meta_title );
		update_post_meta( $post_id, '_seotoolset_meta_description', $meta_description );
		$response = [
			'status'  => 200,
			'message' => 'Meta description data was saved successfully.',
		];
		echo json_encode( $response );
		wp_die();
	}

	/**
	 * Creates new meta boxes class.
	 *
	 * @return void
	 */
	public function add_dashboard_widget() {
		new SEOToolSet_Admin_Meta_Boxes();
	}

	/**
	 * This doesn't run on our new screens -- just edit-related ones!
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		new SEOToolSet_Admin_Meta_Boxes();
	}

	/**
	 * Add our editor CSS file. `add_editor_style()` only looks in the theme
	 * directory, so we're manually adding ours on the `mce_css` filter.
	 *
	 * @param string $stylesheets Base path.
	 *
	 * @return string
	 */
	public function add_editor_styles( $stylesheets ) {
		return $stylesheets .= ',' . SEOTOOLSET_DIR_URL . 'css/editor-style.css';
	}

	/**
	 * Runs on the action save_post. Check if it's a new post first, because
	 * it will contain no data. This only ignores auto-draft status.
	 *
	 * @param array $args Query parameters.
	 *
	 * @return void
	 */
	public function api_posts_post( $args ) {
		if ( get_post_status( $args ) === 'auto-draft' ) {
			return;
		}

		// Ignore WordPress 5 autosaves.
		if ( ! is_array( $_POST ) || count( $_POST ) === 0 || ! isset( $_POST['post_ID'] ) || ! $_POST['post_ID'] ) {
			return;
		}

		if ( ! array_key_exists( 'post_excerpt', $_POST ) ) {
			$exceptcontent         = $_POST['content'];
			$_POST['post_excerpt'] = wp_trim_excerpt( $exceptcontent );
		}

		$regulars = [
			'meta_title',
			'meta_description',
			'attributes',
			'canonical_url',
		];

		$attrs = $_POST;
		foreach ( $regulars as $field ) {
			$key = "seotoolset_$field";
			if ( ! isset( $attrs[ $key ] ) ) {
				$attrs[ $key ] = get_post_meta( $_POST['post_ID'], "_$key" );
			}
			$value = $attrs[ $key ];
			if ( 'attributes' === $field ) {
				$custom = ! ( checked( $value, 'index,follow', false ) || checked( $value, 'noindex,nofollow', false ) );
				$custom = ( ! $value ) ? false : $custom;

				if ( 'custom' === $value && ! isset( $attrs['seotoolset_attributes_custom'] ) ) {
					$attrs[ "{$key}_custom" ] = get_post_meta( $_POST['post_ID'], "_$key" );
				} elseif ( $custom ) {
					$attrs[ "{$key}_custom" ] = $value;
					$attrs[ $key ]            = 'custom';
				}
			}
		}

		$args = wp_parse_args(
			$args,
			[
				'title'                        => isset( $_POST['post_title'] ) ? $_POST['post_title'] : get_the_title( $_POST['post_ID'] ),
				'postId'                       => $_POST['post_ID'],
				'contentBody'                  => isset( $_POST['content'] ) ? $_POST['content'] : apply_filters( 'the_content', get_post_field( 'post_content', $_POST['post_ID'] ) ),
				'description'                  => $attrs['seotoolset_meta_description'],
				'authorId'                     => get_post_field( 'post_author', $_POST['post_ID'] ),
				'project_id'                   => self::get_setting( 'project.id' ),
				'seotoolset_attributes_custom' => $attrs['seotoolset_attributes_custom'],
				'seotoolset_attributes'        => $attrs['seotoolset_attributes'],
				'seotoolset_canonical_url'     => $attrs['seotoolset_canonical_url'],
			]
		);

		SEOToolSetAPI::post_posts( $args, true );
	}

	/**
	 * Output header columns for big table page.
	 *
	 * @param array  $args        Query parameters.
	 * @param string $text        Table caption.
	 * @param string $key         Sort key.
	 * @param string $default_dir Sort direction.
	 *
	 * @return void
	 */
	public static function big_table_header_col( $args, $text, $key = null, $default_dir = 'asc' ) {
		if ( null === $key ) {
			?>
		<th scope="col" class=""><span><?php _e( $text, SEOTOOLSET_TEXTDOMAIN ); ?></span></th>
			<?php
			return;
		}
		$args2         = array();
		$args2['page'] = preg_replace( ';^seotoolset-;', '', $_REQUEST['page'] ?: $_REQUEST['template'] ?: $args['template'] );

		// populate $args2
		$inputs = self::big_table_page_inputs( $args2['page'] );
		foreach ( $inputs as $input ) {
			if ( 'page' === $input ) {
				continue;
			}
			$args2[ $input ] = $args[ $input ] ?: $_REQUEST[ $input ];
			if ( '' === $args2[ $input ] ) {
				unset( $args2[ $input ] );
			}
		}

		$sort = $_REQUEST['sortColumn'] ?: $args['sortColumn'];
		$dir  = $_REQUEST['sortDirection'] ?: $args['sortDirection'];

		if ( $sort === $key ) {
			$sorted = 'sorted';
			switch ( $dir ) {
				case 'asc':
					$dir     = 'desc';
					$sorted .= ' asc';
					break;
				case 'desc':
					$dir     = 'asc';
					$sorted .= ' desc';
					break;
				default:
					$dir = $default_dir;
					break;
			}
		} else {
			$sorted = '';
			$dir    = $default_dir;
		}

		$args2['page']          = 'seotoolset-' . $args2['page'];
		$args2['sortColumn']    = $key;
		$args2['sortDirection'] = $dir;

		$q = http_build_query( $args2 );

		?>
		<th scope="col" class="manage-column column-title sortable <?php echo $sorted; ?>"><a href="?<?php echo $q; ?>"><span><?php _e( $text, SEOTOOLSET_TEXTDOMAIN ); ?></span><span class="sorting-indicator"></span></a></th>
		<?php
	}

	/**
	 * Return list of inputs for big table page.
	 *
	 * @param string $name Table name.
	 *
	 * @return array
	 */
	public static function big_table_page_inputs( $name ) {
		switch ( $name ) {
			case 'activity':
				$inputs = array( 'query', 'recent', 'severity', 'status' );
				break;

			case 'content':
				$inputs = array( 'query', 'range' );
				break;

			case 'keywords':
				$inputs = array( 'query', 'range' );
				break;

			case 'authors':
				$inputs = array( 'range' );
				break;

			default:
				$inputs = array();
				break;
		}//end switch

		return $inputs;
	}

	/**
	 * Fetch, format and return desired date range.
	 *
	 * @param array $args Query parameters.
	 *
	 * @return array
	 */
	public static function get_date_range( $args ) {
		$now = time();

		// Defaults.
		$default_start = strftime( '%Y-%m-%d', strtotime( '-29 days' ) );
		$default_end   = strftime( '%Y-%m-%d', strtotime( 'today' ) );

		// Current.
		$cur_start = self::get_session_var( 'DateRangeStart' );
		$cur_end   = self::get_session_var( 'DateRangeEnd' );

		// Range input.
		if ( isset( $args['range'] ) && preg_match( ';^([0-9-]+):([0-9-]+)$;', $args['range'], $regs ) ) {
			if ( empty( $args['DateRangeStart'] ) ) {
				$args['DateRangeStart'] = $regs[1];
			}
			if ( empty( $args['DateRangeEnd'] ) ) {
				$args['DateRangeEnd'] = $regs[2];
			}
		}

		// Set start/end from inputs, session, or use default.
		$start = isset($args['DateRangeStart']) ? $cur_start : $default_start;
		$end   = isset($args['DateRangeEnd']) ? $cur_end : $default_end;

		// Adjust start versus end date.
		if ( strtotime( $start ) > strtotime( $end ) ) {
			$_start = $start;
			$_end   = $end;
			// swap
			$start = $_end;
			$end   = $_start;
		}

		// Save new start/end dates.
		self::set_session_var( 'DateRangeStart', $start );
		self::set_session_var( 'DateRangeEnd', $end );

		// Set range format.
		$range = "$start:$end";

		// Set description.
		$desc = "$start - $end";

		// Improve time range description.
		$today = strftime( '%Y-%m-%d', $now );
		$days  = date_diff( date_create( $start ), date_create( $end ) )->format( '%a' ) + 1;
		if ( $end === $today ) {
			$desc = $start === $today ? __( 'Today', SEOTOOLSET_TEXTDOMAIN ) : __( 'Last', SEOTOOLSET_TEXTDOMAIN ) . ' ' . $days . ' ' . __( 'days', SEOTOOLSET_TEXTDOMAIN );
		}

		#echo "/* start=$start end=$end range=$range desc=$desc defaultStart=$default_start defaultEnd=$default_end curStart=$cur_start curEnd=$cur_end argStart={$args['DateRangeStart']} argEnd={$args['DateRangeEnd']} argRange={$args['range']} */";
		return array( $start, $end, $range, $desc );
	}

	/**
	 * Collect and return parameters for big table page.
	 *
	 * @param string $name Table name.
	 *
	 * @return array
	 */
	public static function big_table_page_args( $name ) {
		$page           = $_GET['paged'] ?: 1;
		$limit          = $_GET['limit'] ?: 20;
		$sort_column    = $_GET['sortColumn'];
		$sort_direction = $_GET['sortDirection'];

		$args = array(
			'page'        => $page,
			'rowsPerPage' => $limit,
		);

		if ( '' !== $sort_column ) {
			$args['sortColumn'] = $sort_column;
		}
		if ( '' !== $sort_direction ) {
			$args['sortDirection'] = $sort_direction;
		}

		$inputs = self::big_table_page_inputs( $name );

		foreach ( $inputs as $input ) {
			switch ( $input ) {
				case 'range':
					list($date_range_start, $date_range_end, $range, $desc) = self::get_date_range( $_REQUEST );
					$args['DateRangeStart']                                 = $date_range_start;
					$args['DateRangeEnd']                                   = $date_range_end;
					break;

				case 'status':
					$args['status'] = $_REQUEST['status'] ?: 'unseen';
					// seen | unseen
					break;

				case 'severity':
					$args['severity'] = $_REQUEST['severity'] ?: '';
					// Error | Warning | ?
					break;

				case 'query':
				case 'recent':
					$args[ $input ] = $_REQUEST[ $input ];
					break;

				default:
					break;
			}//end switch
		}//end foreach

		return $args;
	}
}
