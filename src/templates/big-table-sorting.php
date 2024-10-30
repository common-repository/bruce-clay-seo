<?php
/**
 * Big table sorting partial.
 *
 * This partial returns data used to render table data.
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

preg_match( ';^seotoolset-(.*)$;', $_GET['page'], $regs );
$page_name = $regs[1];
$inputs    = SEOToolSet::big_table_page_inputs( $page_name );

?>

<div class="seotoolset table-sorting">
<?php
foreach ( $inputs as $input ) {
	switch ( $input ) {
		case 'query':
			switch ( $page_name ) {
				case 'activity':
					$placeholder = 'Search by Title or Reason';
					break;
				case 'content':
					$placeholder = 'Search by Title';
					break;
				case 'keywords':
					$placeholder = 'Search by Keyword';
					break;
				case 'authors':
				default:
					$placeholder = '';
					break;
			}
			?>
		<span class="field search">
			<input class="search" type="text" placeholder="<?php _e( $placeholder, SEOTOOLSET_TEXTDOMAIN ); ?>">
			<input class="button-primary" type="submit" value="<?php _e( 'Search', SEOTOOLSET_TEXTDOMAIN ); ?>">
		</span>
			<?php
			break;

		case 'severity':
			$active = array( SEOToolSet::severity_to_color( $args['severity'] ) => 'active' );
			echo <<<HTML
        <span class="field status">
HTML;
			_e( 'Status:', SEOTOOLSET_TEXTDOMAIN );
			echo <<<HTML
            &nbsp;
            <a class="severity" href="#red"><span class="circle red {$active['red']}" data-severity="Error"></span></a>
            &nbsp;
            <a class="severity" href="#yellow"><span class="circle yellow {$active['yellow']}" data-severity="Warning"></span></a>
            &nbsp;
            <a class="severity" href="#green"><span class="circle green {$active['green']}" data-severity="Info"></span></a>
        </span>

HTML;
			break;

		case 'status':
			echo <<<HTML
        <span class="field archive" data-current="{$args['status']}">

HTML;
			if ( 'seen' !== $args['status'] ) {
				?>
			<a href="?page=seotoolset-activity&status=seen">
				<button><span class="dashicons dashicons-archive"></span> <?php _e( 'View Archive', SEOTOOLSET_TEXTDOMAIN ); ?></button>
			</a>
				<?php
			} else {
				?>
			<a href="?page=seotoolset-activity&status=unseen">
				<button><span class="dashicons dashicons-yes"></span> <?php _e( 'View New', SEOTOOLSET_TEXTDOMAIN ); ?></button>
			</a>
				<?php
			}
			?>
	</span>
			<?php
			break;

		case 'range':
			list($start, $end, $range, $desc) = SEOToolSet::get_date_range( $args );
			?>
	<span class="field range">
		<input type="hidden" id="caleran-table-sort-target" class="range" name="range" value="<?php echo $range; ?>"/>
		<button id="caleran-table-sort"><span class="dashicons dashicons-calendar-alt"></span></button>
			<?php _e( 'Date Range', SEOTOOLSET_TEXTDOMAIN ); ?>: <span class="target_date"><?php echo $desc; ?></span>
	</span>
			<?php
			break;
	}//end switch
}//end foreach
if ( count( $inputs ) > 0 ) {
	?>
		<span class="field search">
			<input class="button-primary" type="submit" value="<?php _e( 'Apply', SEOTOOLSET_TEXTDOMAIN ); ?>">
		</span>
	<?php
}
?>
</div>
