<?php
/**
 * Dashboard AJAX activity partial.
 *
 * This partial renders activity data used in the dashboard.
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

$activities = SEOToolSetAPI::api_request( 'GET', '/dashboard/activity', $args, $headers );
$http_code  = $activities['meta']['http_code'];
$arr        = array(
	'red'    => [],
	'yellow' => [],
	'green'  => [],
);
foreach ( $activities as $key => $activity ) {
	if ( ! is_numeric( $key ) ) {
		continue;
	}

	$color = SEOToolSet::severity_to_color( $activity['severity'] );
	if ( ! isset( $arr[ $color ] ) ) {
		$color = 'yellow';
	}

	$arr[ $color ][] = $activity;
}

foreach ( $arr as $color => $color_activities ) {
	$count            = count( $color_activities );
	$current_severity = false;
	switch ( $color ) {
		case 'red':
			$current_severity = 'Error';
			break;
		case 'yellow':
			$current_severity = 'Warning';
			break;
		case 'green':
			$current_severity = 'Info';
			break;
		default:
			$current_severity = 'Error';
	}
	?>
				<div class="collapsible<?php echo 'red' === $color ? '' : ' closed'; ?>">
					<div class="title">
						<span class="circle <?php echo $color; ?>"></span>
						<h4>
							<?php echo $count; ?>
							<?php _e( 'posts require attention', SEOTOOLSET_TEXTDOMAIN ); ?>
						</h4>
						<?php if ( $count > 0 ) { ?>
							<a class="reasons-view button"
								href="?page=seotoolset-activity<?php echo esc_html_e( ( $current_severity ) ? '&severity=' . $current_severity : '' ); ?>">
								<?php esc_html_e( 'View All', SEOTOOLSET_TEXTDOMAIN ); ?>
							</a>
						<?php } ?>
					</div>
					<div>
						<table class="stripes">
							<?php $activities_count = 0; ?>
							<?php foreach ( $color_activities as $act ) { ?>
								<?php
								$activities_count++;
								if ( $activities_count > 10 ) {
									break;
								}
								$alerts      = &$act['alerts'];
								$seo_post_id = $act['post_id'];
								$alert_ids   = '';
								foreach ( $alerts as $alert ) {
									if ( '' !== $alert_ids ) {
										$alert_ids .= ',';
									}
									$alert_ids .= $alert['alert_id'];
								}

								$alert_counts = SEOToolSet::activity_alert_counts( $act );

								// if ($alert_counts[$args['status']] == 0) continue;
								$color       = SEOToolSet::severity_to_color( $act['severity'] );
								$alert_count = count( $alerts );
								$alert_color = $alert_count > 0 ? 'green' : '';

								// use most 'severe' alert color for count display color.
								if ( is_array( $alerts ) ) {
									foreach ( $alerts as $k => $v ) {
										$tmp = SEOToolSet::severity_to_color( $v['severity'] );
										switch ( $tmp ) {
											case 'red':
												$alert_color = $tmp;
												break 2;
											case 'yellow':
												$alert_color = $tmp;
												break;
											default:
												if ( 'yellow' !== $alert_color ) {
													$alert_color = $tmp;
												}
												break;
										}
									}
								}
								$edit_post_url = get_edit_post_link( $seo_post_id );
								?>
								<tr>
									<td>
										<a href="<?php echo $edit_post_url; ?>"><?php echo $act['post_title']; ?></a>
									</td>
									<td class="alert-status <?php echo $alert_color; ?>">
										<?php
										echo '<div class="alert-reasons" onclick="jQuery(this).toggleClass(\'open\')">' . $alert_count . ' ' . __( 'Reason' . ( 1 === $alert_count ? '' : 's' ), SEOTOOLSET_TEXTDOMAIN ) . '</div>' . "\n";
										echo '<div><b>' . __( 'Bruce Clay SEO Status Alerts', SEOTOOLSET_TEXTDOMAIN ) . "</b><br/>\n";
										$alert_limit = 5;
										$alert_count = count( $alerts );
										for ( $i = 0; $i < $alert_limit && $i < $alert_count; $i++ ) {
											$alert = $alerts[ $i ];
											echo '&bull; <span>' . $alert['message'] . "</span><br/>\n";
										}
										if ( $alert_count > $alert_limit ) {
											$sub = $alert_count - $alert_limit;
											echo "<a href=\"$edit_post_url\"><button>" . __( 'View All', SEOTOOLSET_TEXTDOMAIN ) . " ($sub " . __( 'More', SEOTOOLSET_TEXTDOMAIN ) . ')</button></a>';
										}
										echo '</div>';

										?>

									</td>
									<td align="right">
									<?php if ( 'seen' !== $args['status'] && 'red' === $color ) { ?>
										<a class="remove-item" href="#" data-alerts="<?php echo $alert_ids; ?>"><span class="dashicons dashicons-yes"></span></a>
									<?php } ?>
									</td>
								</tr>
								<?php
							}//end foreach
							?>
						</table>
					</div>
				</div>
	<?php
}//end foreach

