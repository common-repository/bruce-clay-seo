<?php
/**
 * Dashboard AJAX content partial.
 *
 * This partial returns data used for the content section.
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

$args2                   = [];
$args2['DateRangeStart'] = $start;
$args2['DateRangeEnd']   = $end;
$args2['limit']          = '10';
$contents                = SEOToolSetAPI::api_request( 'GET', '/dashboard/content', $args2, $headers );
$http_code               = $contents['meta']['http_code'];
$arr                     = [];
foreach ( $contents as $key => $content ) {
	if ( ! is_numeric( $key ) ) {
		continue;
	}
	$arr[ $key ]['ranking']        = $content['ranking'];
	$arr[ $key ]['post_url']       = get_permalink( $content['post_id'] );
	$arr[ $key ]['post_title']     = $content['post_title'];
	$arr[ $key ]['post_thumbnail'] = get_the_post_thumbnail( $content['post_id'], array( 429, 286 ) );
	if ( preg_match( ';width="1" height="1";', $arr[ $key ]['post_thumbnail'] ) ) {
		$arr[ $key ]['post_thumbnail'] = ( '<img src="' . SEOTOOLSET_DIR_URL . 'img/toppostdefault.png" alt = ""/>' );
	}
	$arr[ $key ]['author_url']   = get_author_posts_url( $content['author']['author_id'] );
	$arr[ $key ]['author_name']  = get_the_author_meta( 'display_name', $content['author']['author_id'] );
	$arr[ $key ]['date_posted']  = $content['date_posted'];
	$arr[ $key ]['views']        = $content['views'];
	$arr[ $key ]['average_time'] = $content['average_time'];
	$arr[ $key ]['shares']       = $content['shares'];
	$arr[ $key ]['edit_button']  = '<a class="button" href="' . get_edit_post_link( $content['post_id'] ) . '">' . __( 'Edit Post', SEOTOOLSET_TEXTDOMAIN ) . '</a>';
	$arr[ $key ]['edit_url']     = get_edit_post_link( $content['post_id'] );
	$arr[ $key ]['edit_text']    = __( 'Edit Post', SEOTOOLSET_TEXTDOMAIN );
}//end foreach
?>
					<a class="button view-all" href="?page=seotoolset-content"><?php _e( 'View All', SEOTOOLSET_TEXTDOMAIN ); ?></a>
					<ul class="tabs">
						<li class="active">
							<a href="#content_rankings" data-tab-target="rankings"><?php _e( 'Rankings', SEOTOOLSET_TEXTDOMAIN ); ?></a>
						</li>
					</ul>

					<div class="tab rankings">
						<table class="center-1 right-4 right-5">
							<tr>
								<th><?php _e( 'Rank', SEOTOOLSET_TEXTDOMAIN ); ?></th>
								<th><?php _e( 'Title', SEOTOOLSET_TEXTDOMAIN ); ?></th>
								<th><?php _e( 'Author', SEOTOOLSET_TEXTDOMAIN ); ?></th>
								<th><?php _e( 'Views', SEOTOOLSET_TEXTDOMAIN ); ?></th>
								<th><?php _e( 'Avg. Time', SEOTOOLSET_TEXTDOMAIN ); ?></th>
							</tr>
							<tbody class="dashboard-content">
							<?php foreach ( $arr as $key => $content ) { ?>
							<tr>
								<td><?php echo $content['ranking']; ?></td>
								<td>
									<a href="<?php echo $content['edit_url']; ?>"><?php echo $content['post_title']; ?></a>
								</td>
								<td>
									<?php
									if ( $content['author_name'] ) {
										?>
										<a href="<?php echo $content['author_url']; ?>"><?php echo $content['author_name']; ?></a>
										<?php
									} else {
										?>
										<span class="author-none"><?php _e( '(none)', SEOTOOLSET_TEXTDOMAIN ); ?></span>
										<?php
									}
									?>
								</td>
								<td><?php echo $content['views']; ?></td>
								<td><?php echo $content['average_time']; ?></td>
							</tr>
								<?php
							}//end foreach
							?>
							</tbody>
						</table>
					</div>
