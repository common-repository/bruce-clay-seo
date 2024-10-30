<?php
/**
 * Dashboard AJAX traffic partial.
 *
 * This partial returns data used to render traffic data.
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

$firstrun                         = 'true' === $_REQUEST['firstrun'];
list($start, $end, $range, $desc) = SEOToolSet::get_date_range( $_REQUEST );

// Traffic Section
$args2                   = [];
$args2['DateRangeStart'] = $start;
$args2['DateRangeEnd']   = $end;
$traffic                 = SEOToolSetAPI::api_request( 'GET', '/dashboard/traffic', $args2, $headers );
$http_code               = $traffic['meta']['http_code'];
$arr                     = [];
foreach ( $traffic as $key => $value ) {
	if ( ! is_numeric( $key ) ) {
		continue;
	}
	if ( ! is_array( $traffic[ $key ] ) ) {
		break;
	}
	foreach ( $traffic[ $key ] as $key => $traf ) {
		foreach ( $traf as $i => $values ) {
			$arr[ $key ][ $i ]['date']  = strftime( '%m/%d', strtotime( substr( $values['date'], 0, 10 ) ) );
			$arr[ $key ][ $i ]['views'] = $values['views'];
		}
	}
	break;
}
?>
					<div class="chart-wrapper area">
						<div id="chart-controls" class="right">
							<h4><?php _e( 'Show', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
							<span class="field">
								<input id="check-desktop" type="checkbox" class="traffic-check blue" value="1" checked="checked"> <label for="show-desktop"><?php _e( 'Desktop', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							</span>
							<span class="field">
								<input id="check-mobile" type="checkbox" class="traffic-check green" value="2" checked="checked"> <label for="show-mobile"><?php _e( 'Mobile', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							</span>
							<span class="field">
								<input id="check-organic" type="checkbox" class="traffic-check yellow" value="3" checked="checked"> <label for="show-organic"><?php _e( 'Organic', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							</span>
							<span class="field">
								<input id="check-paid" type="checkbox" class="traffic-check orange" value="4" checked="checked"> <label for="show-paid"><?php _e( 'Paid', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							</span>
							<span class="field">
								<input id="check-direct" type="checkbox" class="traffic-check red" value="5" checked="checked"> <label for="show-direct"><?php _e( 'Direct', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							</span>
							<span class="field">
								<input id="check-social" type="checkbox" class="traffic-check purple" value="6" checked="checked"> <label for="show-social"><?php _e( 'Social', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							</span>
						</div>
						<div id="chart-traffic" class="chart area half"></div>
						<div class="one-third">
							<script>
							<?php if ( isset( $firstrun ) && true === $firstrun ) { ?>
							jQuery(document).ready(function(){
								var newData = <?php echo json_encode( $arr ); ?>;
								SEOToolSet.draw.drawTrafficChart(newData);
							});
							<?php } else { ?>
							/*
							DO NOT CHANGE - parsed by script.js
							NEWDATASTART <?php echo json_encode( $arr ); ?> NEWDATAEND
							*/
							<?php } ?>
							</script>
						</div>
					</div>
