<?php
/**
 * Post AJAX keywords partial.
 *
 * This partial returns data used to render keyword data.
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
	<table class="unanalyzed keywords-table">
		<tr class="unanalyzed">
			<th><?php _e( 'Keyword', SEOTOOLSET_TEXTDOMAIN ); ?></th>
			<th><?php _e( 'Title', SEOTOOLSET_TEXTDOMAIN ); ?></th>
			<th><?php _e( 'Meta Description', SEOTOOLSET_TEXTDOMAIN ); ?></th>
			<th><?php _e( 'Content', SEOTOOLSET_TEXTDOMAIN ); ?></th>
			<th><?php _e( 'Highlight Color', SEOTOOLSET_TEXTDOMAIN ); ?></th>
			<th>&nbsp;</th>
		</tr>
<?php
$keyword_count = 0;
foreach ( $keywords as $k => $keyword ) {
	if ( ! is_numeric( $k ) ) {
		continue;
	}
	$keyword_count++;

	$keyword['title_target']['goalMet']            = $keyword['title_target']['target'] >= $keyword['title_target']['range']['minimum'] && $keyword['title_target']['target'] <= $keyword['title_target']['range']['maximum'];
	$keyword['meta_description_target']['goalMet'] = $keyword['meta_description_target']['target'] >= $keyword['meta_description_target']['range']['minimum'] && $keyword['meta_description_target']['target'] <= $keyword['meta_description_target']['range']['maximum'];
	$keyword['content_target']['goalMet']          = $keyword['content_target']['target'] >= $keyword['content_target']['range']['minimum'] && $keyword['content_target']['target'] <= $keyword['content_target']['range']['maximum'];
	?>
		<tr class="unanalyzed keyword_row <?php echo $keyword['keyword_id']; ?>">
			<td class="keyword"><?php echo $keyword['keyword']; ?></td>
			<td class="keyword_title"><?php SEOToolSet::keyword_pill( $keyword['title_target'] ); ?></td>
			<td class="keyword_meta_description"><?php SEOToolSet::keyword_pill( $keyword['meta_description_target'] ); ?></td>
			<td class="keyword_content"><?php SEOToolSet::keyword_pill( $keyword['content_target'] ); ?></td>
			<td class="highlight_color">
				<span id="highlight_swatch_<?php echo $keyword['keyword_id']; ?>" style="float:left;position:relative;top:6px;margin-right:4px;border-radius:8px;display:block;height:16px;width:16px;background-color:<?php echo $keyword['highlight_color']; ?>;"></span>
				<input type="text" class="keywordhighlight" style="max-width: 80px;" data-word="<?php echo htmlspecialchars( $keyword['keyword'] ); ?>" name="<?php echo $keyword['keyword_id']; ?>" value="<?php echo $keyword['highlight_color']; ?>">

			</td>
			<td>
				<a href="#" id="<?php echo $keyword['keyword_id']; ?>" title="delete" class="keyword-delete">x</a>            
			</td>
		</tr>
	<?php
}//end foreach

?>
		<tr class="unanalyzed">
			<td colspan="6">
<?php if ( ! ( $keyword_count > 0 ) ) { ?>
				<p><?php _e( 'Have SEOToolSet <a class="analyze" href="@@">analyze your content</a> in order to find keywords and keyword phrases that will help your content perform better. Add your own keywords and keyword phrases, and SEOToolSet will analyze them to help you optimize your content.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
<?php } else { ?>
				<ul class="test-highlight flat-list">
					<li><?php _e( 'Highlight', SEOTOOLSET_TEXTDOMAIN ); ?>:</li>
	<?php
	foreach ( $keywords as $k => $keyword ) {
		if ( ! is_numeric( $k ) ) {
			continue;
		}
		?>
					<li><a href="#" id="highlight_test_<?php echo $keyword['keyword_id']; ?>" data-word="<?php echo htmlspecialchars( $keyword['keyword'] ); ?>" data-color="<?php echo $keyword['highlight_color']; ?>"><?php echo htmlspecialchars( $keyword['keyword'] ); ?></a></li>
	<?php } ?>
					<li><a class="clear-highlights" href="#"><button class="btn"><?php _e( 'Clear', SEOTOOLSET_TEXTDOMAIN ); ?></button></a></li>
				</ul>
<?php } ?>
			</td>
		</tr>
	</table>
	<script>
	SEOToolSet.events.bind('post-widget');
	SEOToolSet.posts.updateKeywordCounts();
	</script>
