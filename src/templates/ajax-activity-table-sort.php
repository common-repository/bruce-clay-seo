<?php
/**
 * Activity AJAX table sort partial.
 *
 * This partial returns data used to render summary data.
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

	<table class="stripes widefat big-boy">
		<thead>
			<tr>
				<?php
				SEOToolSet::big_table_header_col( $args, 'Status', 'severity', 'desc' );
				SEOToolSet::big_table_header_col( $args, 'Post Title', 'post_title', 'asc' );
				SEOToolSet::big_table_header_col( $args, 'Reason', 'alerts', 'desc' );
				SEOToolSet::big_table_header_col( $args, 'Author', 'author', 'asc' );
				SEOToolSet::big_table_header_col( $args, 'Last Updated', 'last_updated', 'desc' );
				SEOToolSet::big_table_header_col( $args, 'Keywords', 'keywords', 'asc' );
				SEOToolSet::big_table_header_col( $args, 'Page Views', 'page_views', 'desc' );
				if ( 'seen' !== $args['status'] ) {
					SEOToolSet::big_table_header_col( $args, 'Mark as Done' );
				} else {
					SEOToolSet::big_table_header_col( $args, 'Actions' );
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $activity as $color => $act ) {
				if ( ! is_numeric( $color ) ) {
					continue;
				}

				$alerts      = &$act['alerts'];
				$author_id   = $act['author']['id'];
				$seo_post_id = $act['post_id'];

				$alert_ids = '';
				foreach ( $alerts as $alert ) {
					if ( '' !== $alert_ids ) {
						$alert_ids .= ',';
					}
					$alert_ids .= $alert['alert_id'];
				}

				$alert_counts = SEOToolSet::activity_alert_counts( $act );

				if ( 0 === $alert_counts[ $args['status'] ] ) {
					continue;
				}

				if ( 0 < $seo_post_id && ( '' === $author_id || '' === $act['post_title'] || '' === $act['author']['name'] ) ) {
					$seo_post = get_post( $seo_post_id );
					if ( '' === $act['post_title'] ) {
						$act['post_title'] = $seo_post->post_title;
					}
					if ( '' === $act['author']['name'] ) {
						$act['author']['name'] = get_the_author_meta( 'display_name', $seo_post->post_author );
					}
					if ( '' === $author_id ) {
						$author_id = $seo_post->post_author;
					}
				}

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

				$edit_post_url   = get_edit_post_link( $seo_post_id );
				$edit_author_url = get_edit_user_link( $author_id );

				?>
					<tr>
						<td class="alert-status">
							<span class="circle <?php echo $color; ?>"></span>
						</td>
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
						<td>
							<a href="<?php echo $edit_author_url; ?>"><?php echo $act['author']['name']; ?></a>
						</td>
						<td><?php echo $act['last_updated']; ?></td>
						<td><?php echo htmlspecialchars( $act['keywords'] ); ?></td>
						<td><?php echo $act['page_views']; ?></td>
						<td>
							<?php if ( 'seen' !== $args['status'] ) { ?>
								<a class="remove-item" href="#" data-alerts="<?php echo $alert_ids; ?>"><span class="dashicons dashicons-yes"></span></a>
							<?php } else { ?>
								<a class="unsee-item" href="#" data-alerts="<?php echo $alert_ids; ?>"><span class="dashicons dashicons-image-rotate"></span></a>
								<a class="delete-item" href="#" data-alerts="<?php echo $alert_ids; ?>"><span class="dashicons dashicons-trash"></span></a>
							<?php } ?>
						</td>
					</tr>
				<?php
			}//end foreach
			?>
		</tbody>
	</table>
