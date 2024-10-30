<?php
/**
 * Choose project AJAX partial.
 *
 * This partial handles choosing and selection a SEOTools Project.
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

if ( ! function_exists( 'hs' ) ) {
	/**
	 * HTMLSpecialChars
	 *
	 * @param string $str String to escape.
	 *
	 * @return void
	 */
	function hs( $str ) {
		echo htmlspecialchars( $str );
	}
}

if ( ! function_exists( 'config' ) ) {
	/**
	 * Configuration
	 *
	 * @param string $setting Setting to fetch.
	 *
	 * @return bool|mixed
	 */
	function config( $setting ) {
		return SEOToolSet::get_setting( $setting );
	}
}

$seo_error = false;

if ( isset( $_POST ) ) {
	$postdata = $_POST;
	// Don't want to overwrite it, probably.
	array_walk_recursive( $postdata, 'sanitize_text_field' );

	$seo_id = (int) $postdata['project']['id'];
	if ( -1 === $seo_id ) {
		// Attempt to create project and update project id in postdata.
		$resp = SEOToolSetAPI::post_projects(
			array(
				'name' => $postdata['project']['new_name'],
				'url'  => $postdata['project']['new_url'],
			)
		);
		// echo "<pre>resp=". print_r($resp, true) . "</pre>";
		if ( $resp['id'] > 0 ) {
			$postdata['project']['id'] = $resp['id'];
			$project                   = SEOToolSet::get_setting( 'project' );
			SEOToolSet::update_setting( 'project', $project );
		} else {
			unset( $postdata['project'] );
			// $this->addAdminNotice(__('Error creating project!', SEOTOOLSET_TEXTDOMAIN));
			$seo_error = __( 'Error creating project!', SEOTOOLSET_TEXTDOMAIN );
		}
	}

	// require_once('ajax-logged-in.php');
}//end if

$projects = SEOToolSetAPI::get_projects();
?>

<p class="logged_in_as">
	<?php _e( 'You are currently signed in as', SEOTOOLSET_TEXTDOMAIN ); ?>
	<span class="username"><?php hs( config( 'login.username' ) ); ?></span> to SEOToolSet.
	<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="logout" data-ajax-success="login-form" value="<?php _e( 'Log Out', SEOTOOLSET_TEXTDOMAIN ); ?>">
</p>

<h4><?php _e( 'Select a Project to use with the SEOToolSet WP Plugin', SEOTOOLSET_TEXTDOMAIN ); ?>:</h4>
<ul class="project-list">
	<?php foreach ( $projects as $i => $project ) : ?>
		<?php
		if ( ! is_numeric( $i ) ) {
			continue;
		}
		?>
	<li>
		<?php $seo_id = $project['id']; ?>
		<input id="project-id-<?php hs( $seo_id ); ?>" type="radio" name="project[id]" value="<?php hs( $seo_id ); ?>" <?php checked( $seo_id, config( 'project.id' ) ); ?>/>
		<label for="project-id-<?php hs( $seo_id ); ?>"><?php hs( $project['name'] ); ?> &bull; <?php hs( $project['url'] ); ?></label>
	</li>
	<?php endforeach; ?>

	<input id="project-name" type="hidden" name="project[name]" value="<?php echo SEOToolSet::get_setting( 'project.name' ); ?>">
	<input id="project-url" type="hidden" name="project[url]" value="<?php echo SEOToolSet::get_setting( 'project.url' ); ?>">

	<h4><?php _e( 'OR create a new Project', SEOTOOLSET_TEXTDOMAIN ); ?>:</h4>

	<li class="new-project">
		<div style="float: left; padding-top: 25px;">
			<input id="new-project" type="radio" name="project[id]" value="-1" <?php checked( -1, config( 'project.id' ) ); ?>>
		</div>
		<div style="float: right; width: calc(100% - 25px)">
			<div class="columns">
				<div class="left">
					<label for="new-project-name"><?php _e( 'Project Name', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					<input id="new-project-name" type="text" name="project[new_name]" value="<?php hs( config( 'project.new_name' ) ); ?>">
				</div>
				<div class="right">
					<label for="new-project-url"><?php _e( 'URL', SEOTOOLSET_TEXTDOMAIN ); ?></label>
					<input id="new-project-url" type="text" name="project[new_url]" value="<?php hs( config( 'project.new_url' ) ); ?>">
				</div>
			</div>
			<div class="columns">
				<label for="new-project-description"><?php _e( 'Description', SEOTOOLSET_TEXTDOMAIN ); ?></label>
				<input id="new-project-description" type="text" name="project[new_description]" value="<?php hs( config( 'project.new_description' ) ); ?>">
			</div>
		</div>
	</li>
</ul>

<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="project" data-ajax-success="logged-in" value="<?php _e( 'Save Changes', SEOTOOLSET_TEXTDOMAIN ); ?>">
