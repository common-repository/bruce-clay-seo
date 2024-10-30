<?php
/**
 * Authors AJAX table sort.
 *
 * This partial returns data used to render author tables.
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
			SEOToolSet::big_table_header_col( $args, 'Ranking', 'ranking', 'asc' );
			SEOToolSet::big_table_header_col( $args, 'Author', 'author', 'asc' );
			SEOToolSet::big_table_header_col( $args, 'Pages', 'number_of_pages', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Last Updated', 'last_published', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Page Views', 'page_views', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Avg. Views / page', 'page_views_per_page', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Avg. Time', 'average_time_per_page', 'desc' );
			?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $authors as $i => $author ) {
				if ( ! is_numeric( $i ) ) {
					continue;
				}
				echo '<tr>';
				echo '<td>' . $author['ranking'] . '</td>';
				echo '<td>' . get_the_author_meta( 'display_name', $author['author']['author_id'] ) . '</td>';
				echo '<td>' . $author['number_of_pages'] . '</td>';
				echo '<td>' . $author['last_published'] . '</td>';
				echo '<td>' . $author['page_views'] . '</td>';
				echo '<td>' . round( $author['page_views_per_page'], 2 ) . '</td>';
				echo '<td>' . $author['average_time_per_page'] . '</td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
