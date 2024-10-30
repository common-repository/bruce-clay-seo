<?php
/**
 * Dashboard AJAX topauthor partial.
 *
 * This partial returns data used to render author rank widgets.
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

// Top Author section
$args2                   = [];
$args2['DateRangeStart'] = $start;
$args2['DateRangeEnd']   = $end;
$args2['rowsPerPage']    = 1;
$args2['sortColumn']     = 'ranking';
$authors                 = SEOToolSetAPI::get_dashboard( 'authors', $args2 );
$http_code               = $authors['meta']['http_code'];
$arr                     = [];
foreach ( $authors as $key => $author ) {
	if ( ! is_numeric( $key ) ) {
		continue;
	}
	$arr[ $key ]['author_thumbnail'] = get_avatar_url( get_the_author_meta( 'user_email', $author['author']['author_id'] ), [ 'size' => 310 ] );
	// ?: $tmp = SEOTOOLSET_DIR_URL . 'img/topauthordefault.png';
	$arr[ $key ]['author_name'] = get_the_author_meta( 'display_name', $author['author']['author_id'] );
	$arr[ $key ]['author_url']  = get_author_posts_url( $author['author']['author_id'] );
	break;
	// limit to 1
}
$arr = $arr[0] ?: array();
?>
						<?php if ( null === $arr || ! isset( $arr['author_thumbnail'] ) ) { ?>
						<img src="<?php echo SEOTOOLSET_DIR_URL; ?>img/topauthordefault.png" alt="">
						<?php } else { ?>
						<img src="<?php echo $arr['author_thumbnail']; ?>" alt="">
						<?php } ?>
						<?php if ( null !== $arr ) { ?>
						<div class="inside">
							<h3><?php echo $arr['author_name'] ?: __( '(none)', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
							<a class="button view-all" href="<?php echo $arr['author_url']; ?>"><?php _e( 'View All', SEOTOOLSET_TEXTDOMAIN ); ?></a>
						</div>
						<?php } ?>
