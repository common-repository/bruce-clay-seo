<?php
/**
 * Directives AJAX partial.
 *
 * This partial returns options used for directives.
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

?>

<div class="tab <?php echo $tab_class; ?> search-directives">
<?php

$directives_data        = array( 'post_id' => $args['post_id'] );
$directives_headers[]   = 'X-Project-Id: ' . SEOToolSet::get_setting( $args['project.id'] );
$directives_posts_array = SEOToolSetAPI::api_request( 'GET', '/posts/' . $post_id . '/directives', $directives_data, $directives_headers );
$directives_posts       = $directives_posts_array['response'];

?>
	<?php $custom = ! ( checked( SEOToolSet::get_post_meta( 'attributes', $post_id ), 'index,follow', false ) || checked( SEOToolSet::get_post_meta( 'attributes', $post_id ), 'noindex,nofollow', false ) ); ?>
	<?php $custom = ( ! SEOToolSet::get_post_meta( 'attributes', $post_id ) ) ? false : $custom; ?>

	<div class="columns">

		<div class="left">
			<h4><?php _e( 'Meta Robots', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

			<?php if ( ! SEOToolSet::get_post_meta( 'attributes', $post_id ) ) : ?>
				<input id="attributes-index" type="radio" name="seotoolset_attributes" value="index,follow" checked="checked">
			<?php else : ?>
				<input id="attributes-index" type="radio" name="seotoolset_attributes" value="index,follow" <?php checked( SEOToolSet::get_post_meta( 'attributes', $post_id ), 'index,follow' ); ?>>
			<?php endif; ?>

			<label for="attributes-index"><?php _e( 'Index this Page (index,follow)', SEOTOOLSET_TEXTDOMAIN ); ?></label><br><br>
			<input id="attributes-noindex" type="radio" name="seotoolset_attributes" value="noindex,nofollow" <?php checked( SEOToolSet::get_post_meta( 'attributes', $post_id ), 'noindex,nofollow' ); ?>>
			<label for="attributes-noindex"><?php _e( 'Don\'t Index this Page (noindex,nofollow)', SEOTOOLSET_TEXTDOMAIN ); ?></label><br><br>
			<input id="attributes-custom" type="radio" name="seotoolset_attributes" value="custom" <?php checked( $custom, true ); ?>>
			<label for="attributes-custom"><?php _e( 'Custom Attributes', SEOTOOLSET_TEXTDOMAIN ); ?></label><br>
			<input type="text" name="seotoolset_attributes_custom" value="
			<?php
			if ( $custom ) {
				echo SEOToolSet::get_post_meta( 'attributes', $post_id );
			}
			?>
			" placeholder="<?php _e( 'Comma-separated values without spaces', SEOTOOLSET_TEXTDOMAIN ); ?>">
			<a class="whats-this" href="#help" data-popup-target="custom-attributes">?</a>
		</div>

		<div class="right">
			<h4><?php _e( 'Canonical URL', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
			<input type="text" name="seotoolset_canonical_url" value="<?php echo SEOToolSet::get_post_meta( 'canonical_url', $post_id ); ?>" placeholder="http://example.com/canonical">
		</div>

	</div>
	<div class="pop-up custom-attributes">
		<h3><?php _e( 'Custom Meta Robot Tags', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
		<dl>
			<dt>index</dt><dd><?php _e( 'Index this page. This is by default and implied.', SEOTOOLSET_TEXTDOMAIN ); ?></dd>
			<dt>archive</dt><dd><?php _e( 'Keep a copy of this page in the search engine cache independent of the indexing or link following.', SEOTOOLSET_TEXTDOMAIN ); ?></dd>
			<dt>follow</dt><dd><?php _e( 'Follow links on this page. This is default and implied.', SEOTOOLSET_TEXTDOMAIN ); ?></dd>
			<dt>noindex</dt><dd><?php _e( 'Do not index this page, but follow the links on this page.', SEOTOOLSET_TEXTDOMAIN ); ?></dd>
			<dt>nofollow</dt><dd><?php _e( 'Do not follow the links on this page, but index this page for content.', SEOTOOLSET_TEXTDOMAIN ); ?></dd>
			<dt>noarchive</dt><dd><?php _e( 'Index and follow the links on this page, but do not keep a copy of the page in the search engine cache.', SEOTOOLSET_TEXTDOMAIN ); ?></dd>
			<dt>nosnippet</dt><dd><?php _e( 'Do not display a snippet (search engine result) for this page.', SEOTOOLSET_TEXTDOMAIN ); ?></dd>
		</dl>
		<p><em><?php _e( 'Use comma-separated values without spaces. For example, to have the search engine not index the page or keep it in the cache but still follow the links, use', SEOTOOLSET_TEXTDOMAIN ); ?> <code>noindex,follow,noarchive</code>.</em></p>
	</div>
</div>
