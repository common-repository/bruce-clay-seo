<?php
/**
 * Page tabs partial.
 *
 * This partial returns data used to render navigation tabs.
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

$current = sanitize_title( $_GET['page'] );

?>

<ul class="seotoolset nav-tabs">
	<?php foreach ( $pages as $seo_title => $slug ) : ?>
	<li class="<?php echo $slug; ?> <?php
	if ( $slug === $current ) {
		echo 'active';
	}
	?>
	">
		<a href="<?php SEOToolSet::page_link( $slug ); ?>"><?php echo $seo_title; ?></a>
		<?php
		if ( 'seotoolset-activity' === $slug ) {
			try {
				if ( /*!is_array($activities) && */ SEOToolSet::user_is_logged_in_subscribed_with_project() ) {
					if ( isset( $_GET['severity'] ) ) {
						$custom_args             = [];
						$custom_args['severity'] = sanitize_text_field( $_GET['severity'] );
					}
					$activities = SEOToolSetAPI::api_request( 'GET', '/dashboard/activity', $custom_args );
				}
				if ( ! is_array( $activities ) ) {
					$activities = array();
				}
				// Only count activities with unseen alerts.
				$activity_count = 0;
				foreach ( $activities as $k => $activity ) {
					if ( ! is_numeric( $k ) ) {
						continue;
					}
					$alert_counts = SEOToolSet::activity_alert_counts( $activity );
					if ( $alert_counts['unseen'] > 0 ) {
						$activity_count++;
					}
				}

				if ( $activity_count > 0 ) {
					echo "<span class=\"badge\">$activity_count</span>";
				}
			} catch ( Exception $e ) {
				SEOToolSet::log( $e->getMessage() );
			}//end try
		}//end if
		?>
	</li>
	<?php endforeach; ?>
	<?php if ( SEOToolSet::check_permissions( 'edit_settings' ) ) : ?>
	<li class="settings 
		<?php
		if ( 'seotoolset-settings' === $current ) {
			echo 'active';
		}
		?>
	">
		<a href="<?php SEOToolSet::page_link( 'settings' ); ?>" title="Settings"><?php _e( 'Settings', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<?php endif; ?>
</ul>


