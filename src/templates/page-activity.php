<?php
/**
 * Page activity partial.
 *
 * This partial returns data used to render page activity.
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
<div class="seotoolset wrap page-activity">
	<h1><?php _e( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ); ?></h1>
<?php
SEOToolSet::get_template(
	'tabs',
	[
		'pages'      => $pages,
		'activities' => $activity,
	]
);

try {
	SEOToolSet::require_user_project_subscribed( '/dashboard/activity' );

	SEOToolSet::get_template( 'big-table-sorting', [ 'args' => $args ] );

	SEOToolSet::get_template(
		'ajax-activity-table-sort',
		[
			'args'     => $args,
			'activity' => $activity,
		]
	);

	SEOToolSet::get_template(
		'table-pagination',
		[
			'response' => $activity,
			'args'     => $args,
		]
	);
} catch ( Exception $e ) {
	SEOToolSet::log( $e->getMessage() );
}//end try
?>
	<script>
	SEOToolSet.events.bind('page-activity');
	</script>
</div>
