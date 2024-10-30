<?php
/**
 * Content sync AJAX partial.
 *
 * This partial syncs data.
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

/**
 * Grab post type data
 *
 * @param string $type Post type.
 * @param array  $idx  Array of posts.
 *
 * @return array
 */
function seotoolset_ajax_content_sync_query_post_type( $type, &$idx ) {
	$args   = array(
		'post_status'    => 'publish',
		'post_type'      => $type,
		'posts_per_page' => -1,
	);
	$result = new WP_Query( $args );
	$data   = array();
	$count  = 0;
	foreach ( $result->posts as $post ) {
		$data[]           = array(
			'url'       => get_permalink( $post->ID ),
			'post_id'   => $post->ID,
			'author_id' => $post->post_author,
		);
		$idx[ $post->ID ] = array(
			'url'       => get_permalink( $post->ID ),
			'post_id'   => $post->ID,
			'author_id' => $post->post_author,
		);

		$count++;
	}

	return array( $data, $count );
}

$idx   = [];
$count = 0;
$data  = [];
$types = array_merge(
	[
		'post' => 'post',
		'page' => 'page',
	],
	get_post_types(
		[
			'public'   => true,
			'_builtin' => false,
		],
		'names',
		'and'
	)
);
foreach ( $types as $seo_type ) {
	list($d, $c) = seotoolset_ajax_content_sync_query_post_type( $seo_type, $idx );
	$data        = array_merge( $data, $d );
	$count      += $c;
}

/* translators: %s: count */
echo '<span>' . sprintf( __( 'There are %d page(s)/post(s) on your site.' ), $count ) . "</span><br/>\n";
foreach ( $data as $d ) {
	$a[] = $d['post_id'];
}

$call = SEOToolSetAPI::api_request( 'GET', '/posts/bulkadd', null, SEOToolSetAPI::header_defaults() );
foreach ( $call as $c ) {
	$b[] = $c['post_id'];
}

$diff         = array_diff( $a, $b );
$cd           = count( $diff );
$sync_missing = ( $cd > 0 );

if ( isset( $_POST ) && in_array( $_POST['template'], [ 'content-sync', 'content-sync-missing' ] ) ) {
	echo '<span>';
	if ( 'content-sync-missing' === $_POST['template'] ) {
		$arr = array();
		foreach ( $diff as $k => $aux_id ) {
			if ( isset( $idx[ $aux_id ] ) ) {
				$arr[] = $idx[ $aux_id ];
			}
		}
	} else {
		$arr = $data;
	}
	$call = SEOToolSetAPI::api_request( 'POST', '/posts/bulkadd', json_encode( $arr ), SEOToolSetAPI::header_defaults() );
	if ( $call['meta']['http_code'] < 200 || $call['meta']['http_code'] >= 300 ) {
		_e( 'An error occurred.', SEOTOOLSET_TEXTDOMAIN );
	} else {
		/* translators: %s: seotoolset domain */
		echo sprintf( __( 'Sent %d page(s)/post(s) for indexing!', SEOTOOLSET_TEXTDOMAIN ), count( $arr ) );
	}
	echo "</span>\n";
} elseif ( $sync_missing ) {
	/* translators: %s: cd */
	echo '<span>' . sprintf( __( 'There are %d page(s)/post(s) that need synchronized.' ), $cd ) . "</span>\n";
} else {
	echo '<span>' . __( 'All content has been synchronized.' ) . "</span>\n";
}//end if
?>
<br/>
<input type="submit" class="button-primary" data-ajax="true" data-ajax-action="content-sync" value="<?php _e( 'Sync All Content', SEOTOOLSET_TEXTDOMAIN ); ?>"/>
<?php if ( $sync_missing ) { ?>
<input type="submit" class="button-primary" data-ajax="true" data-ajax-action="content-sync-missing" style="float: left" value="<?php _e( 'Sync Missing Content', SEOTOOLSET_TEXTDOMAIN ); ?>"/>
<?php } ?>
