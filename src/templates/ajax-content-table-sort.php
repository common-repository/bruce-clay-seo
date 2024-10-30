<?php
/**
 * Content AJAX table sort partial.
 *
 * This partial renders a table used to sort post data.
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
			SEOToolSet::big_table_header_col( $args, 'Title', 'post_title', 'asc' );
			SEOToolSet::big_table_header_col( $args, 'Author', 'author', 'asc' );
			SEOToolSet::big_table_header_col( $args, 'Published', 'date_posted', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Views', 'views', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Avg. Time', 'average_time', 'desc' );
			SEOToolSet::big_table_header_col( $args, 'Actions' );
			?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $content as $i => $item ) {
				if ( ! is_numeric( $i ) ) {
					continue;
				}
				echo '<tr>';
				echo '<td>' . $item['ranking'] . '</td>';
				echo '<td><a href="' . get_permalink( $item['post_id'] ) . '">' . $item['post_title'] . '</a></td>';
				echo '<td>' . ( get_the_author_meta( 'display_name', $item['author']['author_id'] ) ?: __( '(none)', SEOTOOLSET_TEXTDOMAIN ) ) . '</td>';
				echo '<td>' . $item['date_posted'] . '</td>';
				echo '<td>' . $item['views'] . '</td>';
				echo '<td>' . $item['average_time'] . '</td>';
				echo '<td><a class="button" href="' . get_edit_post_link( $item['post_id'] ) . '">' . __( 'Edit Post', SEOTOOLSET_TEXTDOMAIN ) . '</a></td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>
