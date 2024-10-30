<?php
/**
 * Dashboard AJAX keywords partial.
 *
 * This partial returns data used to render keyword data.
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

list($start, $end, $range, $desc) = SEOToolSet::get_date_range( $_REQUEST );

// Keywords Section
$args2                   = [];
$args2['DateRangeStart'] = $start;
$args2['DateRangeEnd']   = $end;
$args2['page']           = $args['page'] ?: 1;
$args2['rowsPerPage']    = $args['rowsPerPage'] ?: 10;
$args2['sortColumn']     = $args['sortColumn'] ?: 'number_of_pages_assigned';
$keywords                = SEOToolSetAPI::api_request( 'GET', '/dashboard/keywords', $args2, $headers );
$arr                     = array( 'http_code' => $keywords['meta']['http_code'] );
foreach ( $keywords as $key => $keyword ) {
	if ( ! is_numeric( $key ) ) {
		continue;
	}
	$arr[ $key ]['keyword_id']               = $keyword['keyword_id'];
	$arr[ $key ]['keyword_name']             = $keyword['keyword_name'];
	$arr[ $key ]['number_of_pages_assigned'] = $keyword['number_of_pages_assigned'];
	$arr[ $key ]['highest_rank']             = $keyword['highest_rank'];
	$arr[ $key ]['page_views']               = $keyword['page_views'];
	$arr[ $key ]['number_of_clicks']         = $keyword['number_of_clicks'];
	$arr[ $key ]['number_of_impressions']    = $keyword['number_of_impressions'];
	$arr[ $key ]['click_through_rate']       = $keyword['click_through_rate'];
}

$total = $page + ( count( $arr ) >= 10 ? 1 : 0 );

// click_through_rate,highest_rank,keyword_id,keyword_name,number_of_clicks,number_of_impressions,number_of_pages_assigned,page_views
?>
					<a class="button view-all" href="?page=seotoolset-keywords"><?php _e( 'View All', SEOTOOLSET_TEXTDOMAIN ); ?></a>

					<ul class="tabs">
						<li class="active">
							<a href="#keyword_rankings" data-tab-target="rankings"><?php _e( 'Rankings', SEOTOOLSET_TEXTDOMAIN ); ?></a>
						</li>
					</ul>

					<div class="tab rankings">
						<table class="right-2 right-3 right-4">
							<tr>
								<th><?php _e( 'Keyword Phrases', SEOTOOLSET_TEXTDOMAIN ); ?></th>
								<th><?php _e( '# of Pg Assigned', SEOTOOLSET_TEXTDOMAIN ); ?></th>
								<th><?php _e( 'Best Average Position', SEOTOOLSET_TEXTDOMAIN ); ?></th>
								<th><?php _e( 'Pg. Views', SEOTOOLSET_TEXTDOMAIN ); ?></th>
							</tr>
							<tbody class="dashboard-keywords">
<?php
if ( is_array( $arr ) ) {
	foreach ( $arr as $keyword ) {
		$keyword_safe = htmlspecialchars( $keyword['keyword_name'] );
		echo <<<HTML
                            <tr>
                                <td>{$keyword_safe}</td>
                                <td>{$keyword['number_of_pages_assigned']}</td>
                                <td>{$keyword['highest_rank']}</td>
                                <td>{$keyword['page_views']}</td>
                            </tr>
HTML;
	}
}
?>
							</tbody>
						</table>
						<?php SEOToolSet::get_template( 'table-pagination', [ 'total' => $total ] ); ?>
					</div>

					<div class="tab unassigned-keywords">
						<p>Insert other tab content here.</p>
					</div>
