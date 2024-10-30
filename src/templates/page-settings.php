<?php
/**
 * Page settings partial.
 *
 * This partial returns data used to render settings page.
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

$only_show_auth = false;
try {
	SEOToolSet::require_user_project_subscribed( '/dashboard/settings' );
} catch ( Exception $e ) {
	$only_show_auth = true;
}

$permissions_map = [
	'Administrator' => 'manage_options',
	'Editor'        => 'edit_pages',
	'Author'        => 'publish_posts',
	'Contributor'   => 'edit_posts',
	'Subscriber'    => 'read',
];

$permissions = [
	'show_dashboard' => __( 'Which users can see the dashboard?', SEOTOOLSET_TEXTDOMAIN ),
	'show_panels'    => __( 'Which users can see the post/page widget?', SEOTOOLSET_TEXTDOMAIN ),
	'edit_panels'    => __( 'Which users can edit data on the post/page widget?', SEOTOOLSET_TEXTDOMAIN ),
	'edit_settings'  => __( 'Which users can edit Bruce Clay SEO settings?', SEOTOOLSET_TEXTDOMAIN ),
];

?>

<form action="" method="post">
	<div class="seotoolset wrap page-settings">
		<h1><?php _e( 'Bruce Clay SEO', SEOTOOLSET_TEXTDOMAIN ); ?></h1>
		<?php SEOToolSet::get_template( 'tabs', [ 'pages' => $pages ] ); ?>

			<div class="columns">
				<div class="left">

					<div class="seotoolset postbox pseudo with-descriptor settings authentication">
						<h3><?php _e( 'SEOToolSet Authentication', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<h4><?php _e( 'Manage your SEOToolSet® integration.', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

						<div class="inside" style="clear: left;">
						<?php if ( ! SEOToolSet::user_is_logged_in() ) : ?>
							<?php SEOToolSet::get_template( 'ajax-login-form' ); ?>
						<?php else : ?>
							<?php SEOToolSet::get_template( 'ajax-logged-in' ); ?>
						<?php endif; ?>
						</div>
					</div>

					<div class="seotoolset postbox pseudo with-descriptor settings sync">
						<h3><?php _e( 'Synchronize Content', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<h4><?php _e( 'Submit your content for analysis.  You need to do this only when you first set up Bruce Clay SEO on a site, or if the post count ever gets out of sync.', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

						<div class="inside">
						<?php SEOToolSet::get_template( 'ajax-content-sync' ); ?>
						</div>
					</div>

					<?php if ( ! SEOToolSet::is_duplicate_feature( 'site__google_analytics' ) ) { ?>
					<div class="seotoolset postbox pseudo with-descriptor settings google">
						<h3><?php _e( 'Google Analytics', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<h4><?php _e( 'Connect to your Google Analytics account.', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

						<div class="inside">
						<?php if ( ! SEOToolSet::get_setting( 'google.analytics_id' ) ) : ?>
							<?php SEOToolSet::get_template( 'ajax-analytics-form' ); ?>
						<?php else : ?>
							<?php SEOToolSet::get_template( 'ajax-has-analytics' ); ?>
						<?php endif; ?>

						</div>
					</div>
					<?php } ?>

					<?php if ( ! SEOToolSet::is_duplicate_feature( 'site__webmaster_verification_codes' ) ) { ?>
					<div class="seotoolset postbox pseudo with-descriptor settings webmaster">
						<h3><?php _e( 'Webmaster Verification Codes', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<h4><?php _e( 'Enter the verification codes for your Google Search Console and Bing Webmaster Tools.', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

						<div class="inside">
							<label for="verification-google"><?php _e( 'Google Verification Code', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							<input id="verification-google" type="text" name="verification[google]" value="<?php echo SEOToolSet::get_setting( 'verification.google' ); ?>">
							<label for="verification-bing"><?php _e( 'Bing Verification Code', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							<input id="verification-bing" type="text" name="verification[bing]" value="<?php echo SEOToolSet::get_setting( 'verification.bing' ); ?>">

							<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="verification" value="<?php _e( 'Save Changes', SEOTOOLSET_TEXTDOMAIN ); ?>">
						</div>
					</div>
					<?php } ?>

				</div>
				<div class="right">

					<?php if ( ! SEOToolSet::is_duplicate_feature( 'front_page__title' ) || ! SeoToolSet::isDuplicateFeature( 'front_page__meta_description' ) ) { ?>
					<div class="seotoolset postbox pseudo with-descriptor settings defaults">
						<h3><?php _e( 'Front Page Title and Meta Description', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<h4><?php _e( 'When you\'re NOT using a specific page as your front page, these will apply.', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

						<div class="inside">
							<label for="defaults-title"><?php _e( 'Front Page Title', SEOTOOLSET_TEXTDOMAIN ); ?></label><br>
							<input id="defaults-title" type="text" name="defaults[title]" value="<?php echo SEOToolSet::is_duplicate_feature( 'front_page__title' ) ? '" readonly="readonly' : SEOToolSet::get_Setting( 'defaults.title' ); ?>">
							<label for="defaults-description"><?php _e( 'Front Page Meta Description', SEOTOOLSET_TEXTDOMAIN ); ?></label>
							<textarea id="defaults-description" rows="3" name="defaults[description]"<?php echo SEOToolSet::is_duplicate_feature( 'front_page__meta_description' ) ? ' readonly="readonly">' : '>' . SEOToolSet::get_setting( 'defaults.description' ); ?></textarea>

							<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="defaults" value="<?php _e( 'Save Changes', SEOTOOLSET_TEXTDOMAIN ); ?>">
						</div>
					</div>
					<?php } ?>

					<div class="seotoolset postbox pseudo with-descriptor settings permissions">
						<h3><?php _e( 'User Permissions', SEOTOOLSET_TEXTDOMAIN ); ?></h3>
						<h4><?php _e( 'Control which Bruce Clay SEO features your users have access to.', SEOTOOLSET_TEXTDOMAIN ); ?></h4>

						<div class="inside">
							<?php foreach ( $permissions as $key => $words ) : ?>
							<p><strong><?php echo $words; ?></strong></p>
							<p>
								<?php foreach ( $permissions_map as $seo_role => $cap ) : ?>
								<input id="permissions-<?php echo $key; ?>-<?php echo $cap; ?>" type="radio" name="permissions[<?php echo $key; ?>]" value="<?php echo $cap; ?>" <?php checked( SEOToolSet::get_setting( "permissions.{$key}" ), $cap ); ?>>
								<label for="permissions-<?php echo $key; ?>-<?php echo $cap; ?>"><?php _e( $seo_role, SEOTOOLSET_TEXTDOMAIN ); ?>
									<?php
									if ( 'Administrator' !== $seo_role ) {
										_e( ' (and higher)', SEOTOOLSET_TEXTDOMAIN );
									}
									?>
								</label><br>
								<?php endforeach; ?>
							</p>
							<?php endforeach; ?>
							<input type="submit" class="button-primary savechanges" data-ajax="true" data-ajax-action="permissions" value="<?php _e( 'Save Changes', SEOTOOLSET_TEXTDOMAIN ); ?>">
						</div>
					</div>

				</div>
				<button id="kill-em-all" class="button" style="border: 1px solid #c00"><?php _e( 'Restore Defaults', SEOTOOLSET_TEXTDOMAIN ); ?></button>
			</div>
			<input type="hidden" name="seotoolset_update_settings" value="1">
			<?php wp_nonce_field( 'seotoolset_settings', 'seotoolset_settings_nonce' ); ?>
	</div>
</form>

<script>
SEOToolSet.events.bind('page-settings');
</script>
