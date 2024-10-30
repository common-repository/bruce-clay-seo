<?php
/**
 * Dashboard AJAX seoscore partial.
 *
 * This partial returns data used to render SEO score data
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

// SEO Score section
$args2                   = [];
$args2['DateRangeStart'] = $start;
$args2['DateRangeEnd']   = $end;
$arr                     = SEOToolSetAPI::get_dashboard( 'seoscore', $args2 );
?>
					<div class="columns">
						<div class="left seoscore left-border">

							<h1 class="seo-score"><?php echo round( $arr['primary_score'], 2 ); ?>%</h1>
							<h3 class="seo-score-delta"><?php echo $arr['month_change']; ?></h3>
							<a class="whats-this boldlink" href="#help" data-popup-target="seo-score"><?php _e( "What's this?", SEOTOOLSET_TEXTDOMAIN ); ?></a>

						</div>
						<div class="right seoscore">
							<table>
								<tr>
									<td>
										<h4><?php _e( 'Overall Average Page Speed', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
									</td>
									<td>
										<a class="whats-this boldlink" href="#help" data-popup-target="pagespeed-help"><?php _e( "What's this?", SEOTOOLSET_TEXTDOMAIN ); ?></a>
									</td>
								</tr>
								<tr>
									<td col-span="2">
										<h2 class="page-speed"><strong><?php echo round( $arr['page_score'], 0 ); ?></strong><span>/</span>100</h2>
									</td>
								</tr>
								<tr>
									<td>
										<h4><?php _e( 'Overall Average Mobile Score', SEOTOOLSET_TEXTDOMAIN ); ?></h4>
									</td>
									<td>
										<a class="whats-this boldlink" href="#help" data-popup-target="mobile-score-help"><?php _e( "What's this?", SEOTOOLSET_TEXTDOMAIN ); ?></a></span>
									</td>
								</tr>
								<tr>
									<td>
										<h2 class="mobile-score"><strong><?php echo round( $arr['mobile_score'], 0 ); ?></strong><span>/</span>100</h2>
									</td>

								</tr>
							</table>
						</div>
					</div>
					<div class="pop-up seo-score">
						<h3><?php _e( 'SEO Score', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<p><?php _e( 'The SEO Score is recalculated monthly and based on the site as a whole.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
					</div>
					<div class="pop-up left pagespeed-help">
						<h3><?php _e( 'Page Speed', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<p><?php _e( 'The Page Speed is the average score for all pages on the site on a scale from 0 - 100, with 100 being the best possible score. Page Speed scores are representative of how fast your pages are loading..', SEOTOOLSET_TEXTDOMAIN ); ?></p>
					</div>
					<div class="pop-up left mobile-score-help">
						<h3><?php _e( 'Mobile Score', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<p><?php _e( 'The Mobile Score is the average score for all pages on the site on a scale from 0 - 100, with 100 being the best possible score. Mobile scores reflect mobile usability, speed and design.', SEOTOOLSET_TEXTDOMAIN ); ?></p>
					</div>
