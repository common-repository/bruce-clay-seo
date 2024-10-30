<?php
/**
 * Post AJAX summary partial.
 *
 * This partial returns data used to render summary data.
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

if ( isset( $summary['code'] ) ) {
	$alert = __( $summary['message'], SEOTOOLSET_TEXTDOMAIN );
	echo "<div class='alert alert-warning'>{$alert}</div>\n";
}
?>
	<div class="columns three">

		<div class="left">
			<h4><?php _e( 'Status Alerts', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
			<div id="ajax-post-summary-status-alerts">
				<?php
				SEOToolSet::get_template(
					'ajax-post-summary-status-alerts',
					array(
						'post_id' => $post_id,
						'summary' => $summary,
					)
				);
				?>
			</div>
		</div>

		<div class="middle">
			<h4><?php _e( 'Statistics', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
			<div id="ajax-post-summary-statistics">
				<?php
				SEOToolSet::get_template(
					'ajax-post-summary-statistics',
					array(
						'post_id' => $post_id,
						'summary' => $summary,
					)
				);
				?>
			</div>
		</div>

		<div class="right">
			<h4><?php _e( 'Performance', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
			<div id="ajax-post-summary-performance">
				<?php
				SEOToolSet::get_template(
					'ajax-post-summary-performance',
					array(
						'post_id' => $post_id,
						'summary' => $summary,
					)
				);
				?>
			</div>
		</div>

	</div>
	<script>
	SEOToolSet.events.bind('post-widget');
	</script>
