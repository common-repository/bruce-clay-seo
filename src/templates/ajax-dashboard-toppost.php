<?php
/**
 * Dashboard AJAX toppost partial.
 *
 * This partial returns data used to render the top post widget.
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

list($start, $end, $range, $desc) = SEOToolSet::get_date_range( $_REQUEST );

// Top Post section
$args2                   = [];
$args2['DateRangeStart'] = $start;
$args2['DateRangeEnd']   = $end;
$args2['page']           = '1';
$args2['rowsPerPage']    = '1';
$args2['sortColumn']     = 'ranking';
$seo_posts               = SEOToolSetAPI::api_request( 'GET', '/dashboard/content', $args2, $headers );
$http_code               = $seo_posts['meta']['http_code'];
$arr                     = [];
foreach ( $seo_posts as $key => $seo_post ) {
	if ( ! is_numeric( $key ) ) {
		continue;
	}
	$tmp                           = get_the_post_thumbnail( $seo_post['post_id'], array( 429, 286 ) );
	$arr[ $key ]['post_thumbnail'] = $tmp;
	if ( '' === $tmp || preg_match( ';width="1" height="1";', $tmp ) ) {
		$arr[ $key ]['post_thumbnail'] = ( '<img src="' . SEOTOOLSET_DIR_URL . 'img/toppostdefault.png" alt = ""/>' );
	}
	$arr[ $key ]['edit_url']    = get_edit_post_link( $seo_post['post_id'] );
	$arr[ $key ]['post_title']  = $seo_post['post_title'];
	$arr[ $key ]['author_url']  = get_author_posts_url( $seo_post['author']['author_id'] );
	$arr[ $key ]['author_name'] = get_the_author_meta( 'display_name', $seo_post['author']['author_id'] );
	break;
	// limit to 1
}
$arr = $arr[0];
?>

<?php if ( null === $arr || ! isset( $arr['post_thumbnail'] ) ) { ?>
<img src="<?php echo SEOTOOLSET_DIR_URL; ?>img/toppostdefault.png" alt="">
<?php } if ( null !== $arr ) { ?>
	<?php echo $arr['post_thumbnail']; ?>
<div class="inside">
	<h3><a href="<?php echo $arr['edit_url']; ?>"><?php echo $arr['post_title']; ?></a></h3>
	by 
	<?php
	if ( $arr['author_name'] ) {
		?>
		<a href="<?php echo $arr['author_url']; ?>"><?php echo $arr['author_name']; ?></a>
		<?php
	} else {
		?>
		<span class="author-none"><?php _e( '(none)', SEOTOOLSET_TEXTDOMAIN ); ?></span>
		<?php
	}
	?>
</div>
<?php } ?>
