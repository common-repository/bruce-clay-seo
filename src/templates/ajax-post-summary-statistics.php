<?php
/**
 * Post AJAX summary statistics partial.
 *
 * This partial returns data used to render summary statistic data.
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

$statistics = $summary['statistics'];

if ( null === $statistics || ! isset( $statistics[0] ) ) {
	echo '<span>' . __( 'No data.', SEOTOOLSET_TEXTDOMAIN ) . '</span>';
} else {
	?>
			<table>
				<tr>
					<th><?php _e( 'Item', SEOTOOLSET_TEXTDOMAIN ); ?></th>
					<th style="text-align: right"><?php _e( 'Current', SEOTOOLSET_TEXTDOMAIN ); ?></th>
					<th style="text-align: center"><?php _e( 'Goal', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				</tr>
	<?php
	foreach ( $statistics as $k => $statistic ) {
		$value = $statistic['current_value'];
		$dec   = preg_match( ';[.];', $value );
		$value = number_format( $value, $dec ? 2 : 0 );
		?>
				<tr>
					<td><?php _e( $statistic['item'], SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td style="text-align: right"><?php echo $value; ?></td>
					<td style="text-align: center"><?php echo $statistic['goal']['minimum'] . ' - ' . $statistic['goal']['maximum']; ?></td>
				</tr>
		<?php
	}
	?>
			</table>
	<?php
}//end if
