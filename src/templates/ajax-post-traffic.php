<?php
/**
 * Post AJAX traffic partial.
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

SEOToolSet::get_template(
	'ajax-traffic',
	[
		'firstrun' => true,
		'post_id'  => $post_id,
		'args'     => array(
			'trafficMetric' => 'pageviews',
			'trafficView'   => 'past week',
			'trafficCheck'  => json_encode(
				[
					'desktop' => true,
					'mobile'  => true,
					'organic' => true,
					'paid'    => true,
					'direct'  => true,
					'social'  => true,
				]
			),
		),
	]
);
?>
	<script>
	SEOToolSet.events.bind('post-widget');
	</script>
