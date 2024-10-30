<?php
/**
 * Page widget partial.
 *
 * This partial returns data used to render section tabs.
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

try {
	SEOToolSet::require_user_project_subscribed( '/posts' );
} catch ( Exception $e ) {
	echo '<p>' . $e->getMessage() . "</p>\n";
	return;
}

/**
 * Each of these tabs could be covered by their own template partial.
 */

$seo_post_id = get_the_ID();

$tab_class = ( ! SEOToolSet::check_permissions( 'edit_panels' ) ) ? 'look-but-dont-touch' : '';

?>

<ul class="tabs">
	<li class="active">
		<a href="#summary" data-tab-target="summary"><?php _e( 'Summary', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<li>
		<a href="#keywords" data-tab-target="keywords"><?php _e( 'Keywords', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<?php if ( ! SEOToolSet::is_duplicate_feature( 'post__meta_description' ) ) { ?>
	<li>
		<a href="#meta-description" data-tab-target="meta-description"><?php _e( 'Meta Description', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<?php } ?>
	<li>
		<a href="#traffic" data-tab-target="traffic"><?php _e( 'Traffic', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<li>
		<a href="#queries" data-tab-target="queries"><?php _e( 'Queries', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<li>
		<a href="#pagespeed" data-tab-target="pagespeed"><?php _e( 'Pagespeed', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<li>
		<a href="#mobile" data-tab-target="mobile"><?php _e( 'Mobile', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<?php if ( ! SEOToolSet::is_duplicate_feature( 'post__search_directives' ) ) { ?>
	<li>
		<a href="#search-directives" data-tab-target="search-directives"><?php _e( 'Search Directives', SEOTOOLSET_TEXTDOMAIN ); ?></a>
	</li>
	<?php } ?>
</ul>
<div class="tab <?php echo $tab_class; ?> summary">
	<div id="ajax-post-summary" data-ajax-load="post-summary" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>

<div class="tab <?php echo $tab_class; ?> keywords">
	<p class="tmce-disabled warning"><?php _e( 'Keyword highlighting only works in the Visual Editor. Please switch modes using the tabs above the post content.', SEOTOOLSET_TEXTDOMAIN ); ?></p>

	<div class="form">
		<span class="field phrases">
			<input type="text" id="keywordsinput" placeholder="<?php _e( 'Keyword Phrases (comma-separated)', SEOTOOLSET_TEXTDOMAIN ); ?>">
			<button class="button addkeywords"><?php _e( 'Add', SEOTOOLSET_TEXTDOMAIN ); ?></button>
		</span>

		<strong><?php _e( 'Or', SEOTOOLSET_TEXTDOMAIN ); ?></strong>

		<span class="field group">
			<select class="add-keyword-select">
				<option value=""><?php _e( 'Add Keyword Group', SEOTOOLSET_TEXTDOMAIN ); ?></option>
				<option value="@@"></option>
				<option value="@@"></option>
				<option value="@@"></option>
			</select>
		</span>

		<button class="button analyze"><?php _e( 'Analyze Content', SEOTOOLSET_TEXTDOMAIN ); ?></button>
		<button class="button" disabled="disabled"><?php _e( 'Refresh Goals', SEOTOOLSET_TEXTDOMAIN ); ?></button>
	</div>

	<div id="ajax-post-keywords" data-ajax-load="post-keywords" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>

<?php if ( ! SEOToolSet::is_duplicate_feature( 'post__meta_description' ) ) { ?>
<div class="tab <?php echo $tab_class; ?> meta-description">
	<div id="ajax-post-meta-description" data-ajax-load="post-meta-description" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>
<?php } ?>
<div class="tab <?php echo $tab_class; ?> traffic">
	<div id="ajax-post-traffic" data-ajax-load="post-traffic" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>
<div class="tab <?php echo $tab_class; ?> queries">
	<div id="ajax-post-queries" data-ajax-load="post-queries" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>
<div class="tab <?php echo $tab_class; ?> pagespeed">
	<div id="ajax-post-pagespeed" data-ajax-load="post-pagespeed" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>

<div class="tab <?php echo $tab_class; ?> mobile">
	<div id="ajax-post-mobile" data-ajax-load="post-mobile" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>

<?php if ( ! SEOToolSet::is_duplicate_feature( 'post__search_directives' ) ) { ?>
<div class="tab <?php echo $tab_class; ?> search-directives">
	<div id="ajax-post-search-directives" data-ajax-load="post-search-directives" data-ajax-data="post_id=<?php echo $seo_post_id; ?>"></div>
</div>
<?php } ?>

<script>
(function(b, c, s, e, o){
	SEOToolSet.events.bind('post-widget');

	c('#ajax-post-meta-description[data-ajax-load]').each(SEOToolSet.events.elementAjaxLoad);

	function run() {
	if (c('[data-ajax-load]:visible').length < 1) {
		setTimeout(function(){ run(); }, 500);
	} else {
		SEOToolSet.events.bind('post-widget');
	}
	}
	run();
})(document, jQuery);
</script>
