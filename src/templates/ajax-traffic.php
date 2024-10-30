<?php
/**
 * Traffic AJAX partial.
 *
 * This partial returns data used to render traffic content.
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

$headers   = SEOToolSetAPI::header_defaults();
$headers[] = 'metric: ' . $args['trafficMetric'];
$headers[] = 'time: ' . $args['trafficView'];

$traffic = SEOToolSetAPI::api_request( 'POST', '/posts/' . $post_id . '/traffic', json_encode( $args ), $headers );
foreach ( $traffic as $a => $b ) {
	if ( ! is_numeric( $a ) ) {
		continue;
	}
	foreach ( $b as $c => $d ) {
		if ( isset( $d['date'] ) ) {
			$traffic[ $a ][ $c ]['date'] = preg_replace( ';T00:00:00;', '', $d['date'] );
		}
	}
}

// time: today, yesterday, past week, past month, past year, all time
// metrics:  pageviews, visits, conversions
$checks = json_decode( stripslashes( $args['trafficCheck'] ), true );

// Types of traffic data: Desktop/Paid/Social/Organic/Mobile/Direct
// Default returns 10 values per type
// each element includes datetime and views
?>
	<div class="columns">

		<div class="left two-thirds">
			<div class="chart-wrapper area">
				<div id="chart-traffic" class="chart area half"></div>
			</div>
			<div class="columns">
				<div class="one-third">
					<h4><?php _e( 'Metric Type', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
					<select id="traffic-metric">
						<option value="pageviews" 
						<?php
						if ( 'pageviews' === $args['trafficMetric'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'Page Views', SEOTOOLSET_TEXTDOMAIN ); ?></option>
						<option value="visits" 
						<?php
						if ( 'visits' === $args['trafficMetric'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'Visits', SEOTOOLSET_TEXTDOMAIN ); ?></option>
						<option value="conversions" 
						<?php
						if ( 'conversions' === $args['trafficMetric'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'Conversions', SEOTOOLSET_TEXTDOMAIN ); ?></option>
					</select>
				</div>
				<div class="one-third">
					<h4><?php _e( 'Time', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
					<select id="traffic-view">
						<option value="past year" 
						<?php
						if ( 'past year' === $args['trafficView'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'This Past Year', SEOTOOLSET_TEXTDOMAIN ); ?></option>
						<option value="past month" 
						<?php
						if ( 'past month' === $args['trafficView'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'This Past Month', SEOTOOLSET_TEXTDOMAIN ); ?></option>
						<option value="past week" 
						<?php
						if ( 'past week' === $args['trafficView'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'This Past Week', SEOTOOLSET_TEXTDOMAIN ); ?></option>
						<option value="yesterday" 
						<?php
						if ( 'yesterday' === $args['trafficView'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'Yesterday', SEOTOOLSET_TEXTDOMAIN ); ?></option>
						<option value="today" 
						<?php
						if ( 'today' === $args['trafficView'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'Today', SEOTOOLSET_TEXTDOMAIN ); ?></option>
						<option value="all time" 
						<?php
						if ( 'all time' === $args['trafficView'] ) {
							echo 'selected';
						}
						?>
						><?php _e( 'All Time', SEOTOOLSET_TEXTDOMAIN ); ?></option>
					</select>
				</div>
				<div class="one-third">
					<h4><?php _e( 'Show', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
					<span class="field">
						<input id="check-desktop" type="checkbox" class="traffic-check  blue" value="1" 
						<?php
						if ( $checks['desktop'] ) {
							echo 'checked="checked"';
						}
						?>
						> <label for="show-desktop"><?php _e( 'Desktop', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					</span>
					<span class="field">
						<input id="check-mobile" type="checkbox" class="traffic-check  green" value="2" 
						<?php
						if ( $checks['mobile'] ) {
							echo 'checked="checked"';
						}
						?>
						> <label for="show-mobile"><?php _e( 'Mobile', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					</span>
					<span class="field">
						<input id="check-organic" type="checkbox" class="traffic-check yellow" value="3" 
						<?php
						if ( $checks['organic'] ) {
							echo 'checked="checked"';
						}
						?>
						> <label for="show-organic"><?php _e( 'Organic', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					</span>
					<span class="field">
						<input id="check-paid" type="checkbox" class="traffic-check orange" value="4" 
						<?php
						if ( $checks['paid'] ) {
							echo 'checked="checked"';
						}
						?>
						> <label for="show-paid"><?php _e( 'Paid', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					</span>
					<span class="field">
						<input id="check-direct" type="checkbox" class="traffic-check red" value="5" 
						<?php
						if ( $checks['direct'] ) {
							echo 'checked="checked"';
						}
						?>
						> <label for="show-direct"><?php _e( 'Direct', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					</span>
					<span class="field">
						<input id="check-social" type="checkbox" class="traffic-check purple"  value="6" 
						<?php
						if ( $checks['social'] ) {
							echo 'checked="checked"';
						}
						?>
						> <label for="show-social"><?php _e( 'Social', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					</span>
				</div>
			</div>
		</div>

		<div class="right one-third">
			<h4><?php _e( 'Views', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
			<?php
			$overalltraffic = $traffic['overall_views'];
			?>
			<table class="metric">
				<tr>
					<td><?php _e( 'Today', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['today']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Yesterday', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['yesterday']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Past Week', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['past_week']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Past Month', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['past_month']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Past Year', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['past_year']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'All Time', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['all_time']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Monthly Change', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['monthly_change']; ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Page Ranking', SEOTOOLSET_TEXTDOMAIN ); ?></td>
					<td><?php echo $overalltraffic['page_ranking']; ?></td>
				</tr>
			</table>
		</div>

	</div>
	<script>
	<?php if ( isset( $firstrun ) && true === $firstrun ) { ?>
	jQuery(document).ready(function(){
		var newData = <?php echo json_encode( $traffic ); ?>;
		SEOToolSet.draw.drawTrafficChart(newData);
	});
	<?php } else { ?>
	/*
	DO NOT CHANGE - parsed by script.js
	NEWDATASTART <?php echo json_encode( $traffic ); ?> NEWDATAEND
	*/
	<?php } ?>
	</script>
