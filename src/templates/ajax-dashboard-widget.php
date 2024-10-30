<?php
/**
 * Dashboard AJAX widget partial.
 *
 * This partial renders widget data.
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

$data = SEOToolSetAPI::get_ajax_template( 'dashboard-widget-data' );

?>
<div class="columns">
	<?php if ( ! SEOToolSet::user_is_logged_in_subscribed_with_project() ) : ?>
		<p><?php echo SEOToolSet::get_plugin_setup_not_complete(); ?></p>
	<?php else : ?>
		<div class="left">

			<h4><?php _e( 'SEO Score', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

			<h1 class="seo-score"><?php echo $data['seoscore']['primary_score']; ?></h1>
			<h3 class="seo-score-delta"><?php echo $data['seoscore']['month_change']; ?></h3>

		</div>
		<div class="right">

			<h4><?php _e( 'Alerts', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

			<ul class="alerts">
				<?php
				$dash_url = SEOToolSet::get_page_link( 'dashboard' );

				$low  = 0;
				$med  = 0;
				$high = 0;
				foreach ( $data['alerts'] as $i => $alert ) {
					if ( ! is_numeric( $i ) ) {
						continue;
					}
					if ( $alert['severity'] >= 8 ) {
						$high++;
					} elseif ( $alert['severity'] >= 5 ) {
						$med++;
					} else {
						$low++;
					}
				}
				?>
				<li>
					<span class="circle red"></span>
					<a href="<?php echo $dash_url; ?>"><?php echo $high; ?> <?php _e( 'high alerts', SEOTOOLSET_TEXTDOMAIN ); ?></a>
				</li>
				<li>
					<span class="circle yellow"></span>
					<a href="<?php echo $dash_url; ?>"><?php echo $med; ?> <?php _e( 'medium alerts', SEOTOOLSET_TEXTDOMAIN ); ?></a>
				</li>
				<li>
					<span class="circle green"></span>
					<a href="<?php echo $dash_url; ?>"><?php echo $low; ?> <?php _e( 'low alerts', SEOTOOLSET_TEXTDOMAIN ); ?></a>
				</li>
			</ul>

			<a class="button-primary" href="<?php echo $dash_url; ?>"><?php _e( 'View Dashboard', SEOTOOLSET_TEXTDOMAIN ); ?></a>

		</div>
	<?php endif; ?>

</div>

<?php if ( SEOToolSet::user_is_logged_in() ) : ?>
<hr>

<h4><?php _e( 'Top 5 Posts', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
<table class="center-1 right-4 right-5">
	<tr>
		<th><?php _e( 'Rank', SEOTOOLSET_TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Title', SEOTOOLSET_TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Author', SEOTOOLSET_TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Views', SEOTOOLSET_TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Avg. Time', SEOTOOLSET_TEXTDOMAIN ); ?></th>
	</tr>
		<?php foreach ( $data['topposts'] as $i => $seo_post ) : ?>
			<?php
			if ( ! is_numeric( $i ) ) {
				continue;
			}
			?>
	<tr>
		<td><?php echo $seo_post['ranking']; ?></td>
		<td>
			<a href="<?php echo get_edit_post_link( $seo_post['post_id'] ); ?>"><?php echo htmlspecialchars( $seo_post['post_title'] ); ?></a>
		</td>
		<td>
			<a href="<?php echo htmlspecialchars( $seo_post['author']['url'] ); ?>"><?php echo htmlspecialchars( $seo_post['author']['name'] ); ?></a>
		</td>
		<td><?php echo number_format( $seo_post['views'] ); ?></td>
		<td><?php echo $seo_post['average_time']; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>
<script>
SEOToolSet.events.bind('dashboard-widget');
</script>
