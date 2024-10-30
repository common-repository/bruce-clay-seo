<?php
/**
 * Keyword legend partial.
 *
 * This partial returns data used to render the keyword legend.
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
<div class="keyword-legend">
	<?php _e( 'Legend', SEOTOOLSET_TEXTDOMAIN ); ?>:
	<span class="pill">
		<span class="have"><?php _e( 'Count', SEOTOOLSET_TEXTDOMAIN ); ?></span>
		<span class="goal"><?php _e( 'Goal', SEOTOOLSET_TEXTDOMAIN ); ?></span>
	</span>
	<span class="pill">
		<span class="goal met"><?php _e( 'Goal Met', SEOTOOLSET_TEXTDOMAIN ); ?></span>
	</span>
</div>
