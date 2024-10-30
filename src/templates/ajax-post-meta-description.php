<?php
/**
 * Post AJAX meta description partial.
 *
 * This partial returns data used for meta descriptions.
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
	$alert = __( $keywords['message'], SEOTOOLSET_TEXTDOMAIN );
	echo "<div class='alert alert-warning'>{$alert}</div>\n";
}
?>
<?php

$post_title = SEOToolSet::get_post_meta( 'meta_title', $post_id ) ?: get_the_title( $post_id );
$post_url   = rtrim( get_permalink( $post_id ), '/' );
$meta_desc  = SEOToolSet::get_post_meta( 'meta_description', $post_id );
$meta_desc  = $meta_desc ?: get_the_excerpt();
$meta_desc  = $meta_desc ?: wp_trim_words( $GLOBALS['post']->post_content, 20 );

/**
 * Truncate a string, with ellipses.
 *
 * @param string $str   String to truncate.
 * @param int    $max   Max length.
 * @param bool   $ellip Show ellipses.
 *
 * @return string
 */
function word_trunc( $str, $max, $ellip = false ) {
	if ( strlen( $str ) <= $max ) {
		return $str;
	}

	// Trim to nearest word border. \S\s
	preg_match( ';^(.{1,' . ( $max - 1 ) . '}\S)(\s|$);smi', $str, $regs );

	return $regs[1] . ( $ellip ? '...' : '' );
}

$disp_title = word_trunc( $post_title, 60, true );
$disp_url   = substr( preg_replace( ';^http://;i', '', $post_url ), 0, 72 );
$disp_desc  = word_trunc( $meta_desc, 300, true );


?>
	<div class="columns">

	<div class="left one-third">
		<h4><?php _e( 'Meta Title', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
		<input type="text" id="seotoolset_meta_title" name="seotoolset_meta_title" value="<?php echo htmlspecialchars( $post_title ); ?>">

		<h4><?php _e( 'Meta Description', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
		<textarea rows="5" id="seotoolset_meta_description" name="seotoolset_meta_description"><?php echo htmlspecialchars( $meta_desc ); ?></textarea>

		<div id="ajax-post-meta-keywords">
			<?php
			SEOToolSet::get_template(
				'ajax-post-meta-keywords',
				array(
					'post_id'  => $post_id,
					'keywords' => $keywords,
				)
			);
			?>
		</div>
	</div>

	<div class="snippets right two-thirds">
		<h4><?php _e( 'Snippet Preview', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
		<p class="snippet">
			<a href="@@" class="seotoolset_meta_title_label"><?php echo $disp_title; ?></a>
			<cite><?php echo $disp_url; ?></cite><br>
			<span class="seotoolset_meta_description_label"><?php echo $disp_desc; ?></span>
		</p>
		<div class="seo-meta-messages"></div>
	</div>

	</div>
	<script>
	SEOToolSet.events.bind('post-widget');
	SEOToolSet.posts.updateKeywordCounts();
	</script>
