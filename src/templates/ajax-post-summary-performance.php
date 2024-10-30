<?php
/**
 * Post AJAX summary performance partial.
 *
 * This partial returns data used to render summary performance data.
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

if ( ! $summary ) {
	$summary = SEOToolSetAPI::api_request( 'GET', "/posts/{$post_id}/summary" );
}

$performance = $summary['performance'];

if ( null === $performance ) {
	echo '<span>' . __( 'No data', SEOTOOLSET_TEXTDOMAIN ) . '</span>';
} else {
	$values = array();
	foreach ( $performance as $key => $value ) {
		$dec = preg_match( ';[.];', $value );
		if ( 'avg_time_on_page' === $key ) {
			$values[ $key ] = $value;
		} else {
			$values[ $key ] = number_format( $value, $dec ? 1 : 0 );
		}
	}
	?>
			<table>
				<tr>
					<th><?php _e( 'Item', SEOTOOLSET_TEXTDOMAIN ); ?></th>
					<th style="text-align: right"><?php _e( 'Current', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				</tr>
				<tr>
					<td><?php _e( 'Total Page Views', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td style="text-align: right"><?php echo $values['page_views']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Average Time on Page', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td style="text-align: right"><?php echo $values['avg_time_on_page']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Views Today', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td style="text-align: right"><?php echo $values['views_today']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Views this Week', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td style="text-align: right"><?php echo $values['views_this_week']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Views this Month', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td style="text-align: right"><?php echo $values['views_this_month']; ?></td>
				</tr>
			</table>
	<?php
}//end if
