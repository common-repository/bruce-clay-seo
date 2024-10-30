<?php
/**
 * Post AJAX meta keywords partial.
 *
 * This partial returns data used for meta keywords.
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

if ( ! $keywords ) {
	$keywords = SEOToolSetAPI::api_request( 'GET', "/posts/{$post_id}/keywords" );
}

if ( isset( $keywords['code'] ) ) {
	$alert = __( ( 12 === $keywords['code'] ? 'Post not analyzed.' : $keywords['message'] ), SEOTOOLSET_TEXTDOMAIN );
	echo "<div class='alert alert-warning'>{$alert}</div>\n";
}
?>
<?php if ( SEOToolSetAPI::response_count_records( $keywords ) > 0 ) { ?>
		<table class="meta-keywords-table">
			<tr>
				<th><?php _e( 'Keyword', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				<th><?php _e( 'Title', SEOTOOLSET_TEXTDOMAIN ); ?></th>
				<th><?php _e( 'Meta Description', SEOTOOLSET_TEXTDOMAIN ); ?></th>
			</tr>
			<?php
			foreach ( $keywords as $i => $keyword ) {
				if ( ! is_numeric( $i ) ) {
					continue;
				}
				?>
			<tr class="keyword_row">
				<td class="keyword"><?php echo $keyword['keyword']; ?></td>
				<td class="keyword_title"><?php SEOToolSet::keyword_pill( $keyword['title_target'] ); ?></td>
				<td class="keyword_meta_description"><?php SEOToolSet::keyword_pill( $keyword['meta_description_target'] ); ?></td>
			</tr>
			<?php } ?>
			<tr class="legend">
				<th colspan="3">
					<?php SEOToolSet::get_template( 'keyword-legend' ); ?>
				</th>
			</tr>
		</table>
	<?php
}//end if
?>
	<script>
	SEOToolSet.events.bind('post-widget');
	</script>
