<?php
/**
 * Post AJAX summary status_alerts partial.
 *
 * This partial returns data used to render summary status data.
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

$alerts = $summary['status_alerts'];

if ( ! is_array( $alerts ) ) {
	$alerts = array();
}

if ( 12 === $summary['code'] ) {
	$alerts[] = array(
		'color' => 'yellow',
		'items' => __( 'Post not analyzed.', SEOTOOLSET_TEXTDOMAIN ),
	);
} elseif ( $summary['code'] > 0 ) {
	$alerts[] = array(
		'color' => 'red',
		'items' => __( $summary['message'], SEOTOOLSET_TEXTDOMAIN ),
	);
}

if ( count( $alerts ) === 0 ) {
	$alerts[] = array(
		'color' => 'green',
		'items' => __( 'No alerts found.', SEOTOOLSET_TEXTDOMAIN ),
	);
}

?>
			<ul class="alerts">
<?php
foreach ( $alerts as $k => $alert ) {
	if ( ! is_array( $alert ) ) {
		$alert = array( 'items' => $alert );
	}

	$color = $alert['color'] ?: 'orange';
	?>
				<li>
					<span class="circle <?php echo $color; ?>"></span>
					<?php _e( $alert['items'], SEOTOOLSET_TEXTDOMAIN ); ?>
				</li>
	<?php
}
?>
			</ul>
