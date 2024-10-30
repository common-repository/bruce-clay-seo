<?php
/**
 * Table pagination partial.
 *
 * This partial returns data used to render pagination.
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

$total = SEOToolSetAPI::response_total_pages( $response, $args['page'], $args['rowsPerPage'] );

if ( $total > 1 || $args['page'] > 1 ) {
	if ( $total < $args['page'] ) {
		$total = $args['page'];
	}
	// $seo_paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$seo_paged = ( intval( $args['page'] ) ) ? intval( $args['page'] ) : 1;
	$base      = remove_query_arg( array_merge( wp_removable_query_args(), [ 'paged' ] ) );
	// echo "paged=$seo_paged;base=$base;";
	// This function is *technically* meant to be used on post archives, but with
	// sufficient futzing, you can make it work for any URL schema.
	$paging = paginate_links(
		[
			// 'type'      => 'array',
			'base'      => $base . '%_%',
			'format'    => '&paged=%#%',
			'current'   => $seo_paged,
			'total'     => $total,
			'end_size'  => 0,
			'mid_size'  => 2,
			// 'show_all'  => true,
			'prev_text' => '&lsaquo;',
			'next_text' => '&rsaquo;',
		]
	);

	$first = sprintf(
		'<%s class="first page-numbers" href="%s">&laquo;</%s>',
		( $seo_paged <= 1 ) ? 'span' : 'a',
		$base,
		( $seo_paged <= 1 ) ? 'span' : 'a'
	);

	$first = ( $seo_paged <= 1 ) ? '' : $first;

	$last = sprintf(
		'<%s class="last page-numbers" href="%s">&raquo;</%s>',
		( $seo_paged >= $total ) ? 'span' : 'a',
		$base . '&paged=' . $total,
		( $seo_paged >= $total ) ? 'span' : 'a'
	);

	$last = ( $seo_paged >= $total ) ? '' : $last;

	?>
<div class="seotoolset table-pagination">
	<?php echo $first . $paging . $last; ?>
</div>

	<?php
}//end if
