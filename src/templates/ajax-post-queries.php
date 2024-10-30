<?php
/**
 * Post AJAX queries partial.
 *
 * This partial returns data used to render query data.
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

if ( SEOToolSet::get_setting( 'google.analytics_id' ) ) {
	if ( ! $queries ) {
		$queries = SEOToolSetAPI::api_request( 'GET', "/posts/{$post_id}/queries" );
	}

	if ( isset( $queries['code'] ) ) {
		$alert = __( $queries['message'], SEOTOOLSET_TEXTDOMAIN );
		echo "<div class='alert alert-warning'>{$alert}</div>\n";
	}
	$google_queries = $queries['google_queries'];
	$bing_queries   = $queries['bing_queries'];
	?>
	<div class="columns">

		<h4><?php _e( 'Google', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
		<table>
			<tr>
				<th><?php _e( 'Query', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				<th><?php _e( 'Best Average Position', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				<th><?php _e( 'Impressions', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				<th><?php _e( 'Clicks', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				<th><abbr title="<?php _e( 'Click-Through Rate', SEOTOOLSET_TEXTDOMAIN ); ?>"><?php _e( 'CTR', SEOTOOLSET_TEXTDOMAIN ); ?></abbr></th>
			</tr>
			<?php foreach ( $google_queries as $gkey ) { ?>
				<tr>
					<td><?php echo $gkey['query']; ?></td>
					<td><?php echo $gkey['rank']; ?></td>
					<td><?php echo $gkey['impressions']; ?></td>
					<td><?php echo $gkey['clicks']; ?></td>
					<td><?php echo round( $gkey['ctr'], 2 ); ?>%</td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php
} else {
	$str = __( 'Connect your Google Analytics account on the %a href% Settings %/a% page.' );
	$str = preg_replace( ';%a href%\s*(.*?)\s*%/a%;mi', '<a href="/wp-admin/admin.php?page=seotoolset-settings" title="$1">$1</a>', $str );
	?>
	<div>
		<p><?php echo $str; ?></p>
	</div>
	<?php
}//end if
?>
	<script>
	SEOToolSet.events.bind('post-widget');
	</script>
