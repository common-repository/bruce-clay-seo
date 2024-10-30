<?php
/**
 * Keywords AJAX table sort partial.
 *
 * This partial returns data used to render keyword tables.
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
			SEOToolSet::big_table_header_col( $args, 'Keyword', 'keyword_name', 'asc' );
			SEOToolSet::big_table_header_col( $args, 'Assignments', 'number_of_pages_assigned', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Best Average Position', 'highest_rank', 'asc' );
			SEOToolSet::big_table_header_col( $args, 'Views', 'page_views', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Clicks', 'number_of_clicks', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Impressions', 'number_of_impressions', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'CTR', 'click_through_rate', 'desc' );
			?>
			</tr>
		</thead>
		<tbody>
<?php
if ( is_array( $keywords ) ) {
	foreach ( $keywords as $i => $keyword ) {
		if ( ! is_numeric( $i ) ) {
			continue;
		}
		$keyword_safe = htmlspecialchars( $keyword['keyword_name'] );
		$str_ctr      = sprintf( '%0.2f%%', $keyword['click_through_rate'] * 100 );
		echo <<<HTML
            <tr>
                <td>{$keyword_safe}</td>
                <td>{$keyword['number_of_pages_assigned']}</td>
                <td>{$keyword['highest_rank']}</td>
                <td>{$keyword['page_views']}</td>
                <td>{$keyword['number_of_clicks']}</td>
                <td>{$keyword['number_of_impressions']}</td>
                <td>{$str_ctr}</td>
            </tr>

HTML;
	}//end foreach
}//end if
?>
		</tbody>
	</table>
