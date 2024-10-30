<?php
/**
 * Post AJAX pagespeed partial.
 *
 * This partial returns data used to render pagespeed data.
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

if ( ! $pagespeed ) {
	$pagespeed = SEOToolSetAPI::api_request( 'GET', "/posts/{$post_id}/pagespeed" );
}

if ( isset( $pagespeed['code'] ) ) {
	$alert = __( $pagespeed['message'], SEOTOOLSET_TEXTDOMAIN );
	echo "<div class='alert alert-warning'>{$alert}</div>\n";
}

if ( ! function_exists( 'priority_fixes_level_html' ) ) {
	/**
	 * Assigns styling to rule impact.
	 *
	 * @param array  $fixes Array of issues to fix.
	 * @param string $color Color to show.
	 * @param string $level Severity level.
	 * @param string $text  Text to output.
	 *
	 * @return void
	 */
	function priority_fixes_level_html( $fixes, $color, $level, $text ) {
		static $idx = 0;
		?>
				<li>
					<span class="circle <?php echo $color; ?>"></span>
					<p>
						<strong><?php _e( $level, SEOTOOLSET_TEXTDOMAIN ); ?></strong>:
						<?php
							_e( $text, SEOTOOLSET_TEXTDOMAIN );
						if ( count( $fixes ) === 0 ) {
							echo '<p>';
							_e( "No {$level} issues to fix.", SEOTOOLSET_TEXTDOMAIN );
							echo '</p>';
						} else {
							echo "<p>{$fixes[0]['name']}<br/>";
							echo __( 'Rule Impact', SEOTOOLSET_TEXTDOMAIN ) . ": <strong>{$fixes[0]['ruleImpact']}</strong><br/>";
							echo "{$fixes[0]['summary']}</p>";

							foreach ( $fixes['0']['blocks'] as $blocks ) {
								$i         = $idx++;
								$has_items = ( is_array( $blocks['values'] ) && count( $blocks['values'] ) > 0 );
								echo <<<HTML
<div class="wrap-collapsible">

HTML;
								if ( ! $has_items ) {
									echo <<<HTML
  <label class="lbl-notoggle">{$blocks['header']}</label>

HTML;
								} else {
									echo <<<HTML
  <input id="collapsible_pagespeed_{$i}" class="toggle" type="checkbox">
  <label for="collapsible_pagespeed_{$i}" class="lbl-toggle">{$blocks['header']}</label>
  <div class="collapsible-content">
    <div class="content-inner">

HTML;
									echo '<p>';
									echo '<ul>';
									foreach ( $blocks['values'] as $blocks_values ) {
										echo '<li>';
										echo $blocks_values['value'];
										echo '</li>';
									}
									echo '</ul>';
									echo '</p>';
									echo <<<HTML
    </div>
  </div>
HTML;
								}//end if
								echo <<<HTML
</div>
HTML;
							}//end foreach
						}//end if
						?>
					</p>
				</li>
		<?php
	}
}//end if

if ( ! function_exists( 'priority_fixes_html' ) ) {
	/**
	 * Assigns priority indications
	 *
	 * @param array  $data Array of priority fixes.
	 * @param string $text Text to display.
	 *
	 * @return void
	 */
	function priority_fixes_html( &$data, $text ) {
		$percent = $data['percent_value'];
		?>
	<div class="columns">

		<div class="left one-third">
			<div class="chart donut score-good" data-score="94">
				<img src="<?php echo SEOTOOLSET_DIR_URL; ?>img/donut-mask.png">
				<span class="needle" style="transform: rotate(<?php echo $percent / 100 * 180; ?>deg)"></span>
			</div>
			<h2 class="score"><strong><?php echo $percent; ?></strong><span>/</span>100</h2>
		</div>

		<div class="right two-thirds">
			<ul class="alerts with-ps">
			<?php
				priority_fixes_level_html( $data['high_priority'], 'red', 'High Priority', "Issues that could decrease {$text} significantly (per Google)." );
				priority_fixes_level_html( $data['moderate_priority'], 'yellow', 'Medium Priority', "Issues that may impact {$text} (per Google)." );
				priority_fixes_level_html( $data['low_priority'], 'green', 'Low Priority', "Items that match or exceed Google\'s recommendations and may benefit {$text}." );
			?>
			</ul>
		</div>

	</div>
		<?php
	}
}//end if

priority_fixes_html( $pagespeed, __( 'page load speed', SEOTOOLSET_TEXTDOMAIN ) );
?>
	<script>
	SEOToolSet.events.bind('post-widget');
	</script>
