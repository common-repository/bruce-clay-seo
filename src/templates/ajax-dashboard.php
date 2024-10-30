<?php
/**
 * Dashboard AJAX partial.
 *
 * This partial outputs dashboard components.
 * Templates starting with "ajax" are involved in Ajax calls. They aren't
 * always loaded via Ajax; sometimes they're loaded up as an initial state in
 * PHP. But they can be loaded via Ajax by including a `template` field when
 * posting a call.

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

try {
	SEOToolSet::require_user_project_subscribed( '/dashboard' );

	list($start, $end, $range, $desc) = SEOToolSet::get_date_range( $_REQUEST );

	$qs = 'firstrun=true';
	if ( '' !== $range ) {
		$qs .= '&range=' . urlencode( $range );
	}
	if ( '' !== $start ) {
		$qs .= '&DateRangeStart=' . urlencode( $start );
	}
	if ( '' !== $end ) {
		$qs .= '&DateRangeEnd=' . urlencode( $end );
	}
	?>
	<span class="field range">
		<input type="hidden" id="dashboard-content-sort-target" class="range" name="range" value="<?php echo $range; ?>"/>
		<?php _e( 'Date Range', SEOTOOLSET_TEXTDOMAIN ); ?>: <span class="target_date"><?php _e( $desc, SEOTOOLSET_TEXTDOMAIN ); ?></span>
		<button id="dashboard-content-sort"><span class="dashicons dashicons-calendar-alt"></span></button>
	</span>
	<br/>
	<div class="columns">
		<div class="left">
			<div class="seotoolset postbox pseudo activity" data-ajax-load="dashboard-activity" data-ajax-data="<?php echo $qs; ?>">
				<h3><?php _e( 'Activity', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
				<div class="inside"></div>
			</div>
			<div class="seotoolset postbox pseudo content-ranking" data-ajax-load="dashboard-content" data-ajax-data="<?php echo $qs; ?>">
				<h3><?php _e( 'Content Ranking', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
				<div class="inside"></div>
			</div>
			<div class="seotoolset postbox pseudo keywords" data-ajax-load="dashboard-keywords" data-ajax-data="<?php echo $qs; ?>">
				<h3><?php _e( 'Keywords', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
				<div class="inside"></div>
			</div>
		</div>
		<div class="right">
			<div class="seotoolset postbox insidemargin pseudo seo-score" data-ajax-load="dashboard-seoscore" data-ajax-data="<?php echo $qs; ?>">
				<h3><?php _e( 'SEO Score', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
				<div class="inside"></div>
			</div>
			<div class="columns">
				<div class="left">
					<div class="seotoolset postbox pseudo top-post" data-ajax-load="dashboard-toppost" data-ajax-data="<?php echo $qs; ?>">
						<h3><?php _e( 'Top Post by Traffic', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<div class="inside"></div>
					</div>
				</div>
				<div class="right">
					<div class="seotoolset postbox pseudo top-author" data-ajax-load="dashboard-topauthor" data-ajax-data="<?php echo $qs; ?>">
						<h3><?php _e( 'Top Author by Traffic', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<div class="inside"></div>
					</div>
				</div>
			</div>
			<div class="seotoolset postbox insidemargin pseudo traffic" data-ajax-load="dashboard-traffic" data-ajax-data="<?php echo $qs; ?>">
				<h3><?php _e( 'Traffic', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
				<div class="inside"></div>
			</div>
		</div>
	</div>
	<script>
	SEOToolSet.events.bind('page-dashboard');
	</script>
	<?php
} catch ( Exception $e ) {
	SEOToolSet::log( $e->getMessage() );
}//end try
