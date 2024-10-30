<?php
/**
 * Signup tabs partial.
 *
 * This partial returns data used to render signup tabs.
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

$is_new_signup = ! SEOToolSet::user_is_logged_in();
if ( $is_new_signup ) {
	$seo_tabs = array( 'Account', 'Billing Plan', 'Payment Info' );
} else {
	$seo_tabs = array( 'Billing Plan', 'Payment Info' );
	$active--;
}
?>

<ul class="tabs">
<?php
foreach ( $seo_tabs as $key => $value ) {
	$n = $key + 1;

	echo '<li';
	if ( $n === $active ) {
		echo ' class="active"';
	}
	echo '><a';
	if ( $n !== $active ) {
		echo ' style="color: #333"';
	}
	echo ">$n. ";
	_e( $value, SEOTOOLSET_TEXTDOMAIN );
	echo "</a></li>\n";
}
?>
</ul>

<div id="signup-errors"></div>
