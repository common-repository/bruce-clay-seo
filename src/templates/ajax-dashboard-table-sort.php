<?php
/**
 * Dashboard AJAX table sort partial.
 *
 * This partial returns data from the keywords endpoint within a range.
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

$range = explode( ':', $_REQUEST['range'] );
list($date_range_start, $date_range_end, $range, $desc) = SEOToolSet::get_date_range(
	array(
		'DateRangeStart' => $range[0],
		'DateRangeEnd'   => $range[1],
	)
);
switch ( $_REQUEST['keyword'] ) {
	case 'keyword':
		$sort_column = 'keyword';
		break;
	case 'views':
		$sort_column = 'views';
		break;
	case 'shares':
		$sort_column = 'shares';
		break;
	default:
		$sort_column = '';
		break;
}

$keyword_args      = json_encode(
	array(
		'page'           => 1,
		'DateRangeStart' => $date_range_start,
		'DateRangeEnd'   => $date_range_end,
	)
);
$keyword_headers[] = 'X-Project-Id: ' . SEOToolSet::get_setting( 'project.id' );
$keywords          = SEOToolSetAPI::api_request( 'GET', '/dashboard/keywords', $keyword_args, $keyword_headers );


$ret = array();
foreach ( $keywords as $i => $keyword ) {
	if ( ! is_numeric( $i ) ) {
		continue;
	}
	$ret[ $i ]['keyword_id']               = $keyword['keyword_id'];
	$ret[ $i ]['keyword_name']             = $keyword['keyword_name'];
	$ret[ $i ]['number_of_pages_assigned'] = $keyword['number_of_pages_assigned'];
	$ret[ $i ]['highest_rank']             = $keyword['highest_rank'];
	$ret[ $i ]['page_views']               = $keyword['page_views'];
	$ret[ $i ]['number_of_clicks']         = $keyword['number_of_clicks'];
	$ret[ $i ]['number_of_impressions']    = $keyword['number_of_impressions'];
}

$ret = json_encode( $ret );
