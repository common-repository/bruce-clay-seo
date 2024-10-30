=== Bruce Clay SEO WP ===
Contributors: alanderkin
Plugin URI: https://www.bruceclay.com/seo/tools/bruceclayseo/
Donate link: 
Tags: SEO, Readability, Content Analysis, Google Search Console, Google Analytics, SaaS, Competitive Analysis, Page Analysis, SERP Analysis, Search Engine Optimization, Bruce Clay
Requires at least: 4.5
Tested up to: 5.5.1
Requires PHP: 5.6
Stable tag: v0.8.0
License: GNU General Public License v3
License URI: https://www.gnu.org/licenses/gpl.html

Next-level SEO plugin! Get on-page guidance per keyword based on analysis of top competitors. See analytics in the WP dashboard. Powered by SEOToolSet.

== Description ==
Bruce Clay SEO WP™ puts powerful needed features right into the hands of the writers creating content in WordPress. It is fully compatible with the Yoast SEO plugin, as well.

**What makes this plugin unique:** It enriches your publishing workspace with SEO insights based on real-time search results and analytics data. In other words, you can see real-world insights while you work in WordPress. Integrations with SEOToolSet®, Google Search Console and Google Analytics make this possible.

The Bruce Clay SEO plugin works like software as a service (SaaS). Rather than a hard-coded checklist approach to optimizing a post or page, this plugin has a live connection with the SEOToolSet software. It analyzes your keywords and competition in real time.

You see on-page recommendations that are *customized per keyword*. So your page can better compete for visibility in the search engines.

**In the WordPress editor, you can:**

* Enter more than one focus keyword per page.
* Analyze the top-ranked competitors without leaving WordPress.
* See customized recommendations for keyword usage and content length.
* Highlight keywords to easily see how well they are distributed through the content.
* Check the readability level of a page and compare it to keyword competitors.

**In the Bruce Clay SEO dashboard, you can:**

* See which are the top-performing pages and authors on the site.
* View analytics data such as number of search impressions and click-throughs.
* Identify problems with mobile performance.
* Be alerted to duplicate content on the site.
* Know how much content the site has for each keyword.
* See how each page is performing, using integrated Google Analytics data.
* View top-performing posts per author, measured by visitors.

**Compatibility with Yoast:** Bruce Clay SEO is fully compatible with the Yoast SEO plugins. If Yoast is active on the site, then the plugin uses the title tags, meta descriptions, and canonical and meta-robots directives from Yoast and disables those features within Bruce Clay SEO in order to avoid any conflict.

If Yoast is not being used, additional tabs let you enter meta tags and search directives for the page right in the WordPress editor.

**Subscription Cost:** Monthly subscription is $24.95/month per domain and includes all the plugin features plus use of the SEOToolSet for additional tools and reports. Try the plugin free for the first 7 days. You can cancel anytime.

**To view a demo video, visit our [Bruce Clay SEO for WordPress](http://bit.ly/2G8rb6Y) page**

== Installation ==

**First, add the plugin in WordPress:**

1. Download the zip file: [https://downloads.wordpress.org/plugin/bruce-clay-seo.zip](https://downloads.wordpress.org/plugin/bruce-clay-seo.zip).
2. Open your WordPress site and choose *Plugins > Add New*.
3. Click *Upload Plugin* and browse to select the zip file.
4. Click *Install Now* and proceed to *Activate Plugin*.
          Note: You might be required to enter an FTP password, which you can obtain from your hosting provider.

**Next, connect the plugin to the SEOToolSet®:**
*This is a one-time setup required to power the plugin features.*

1. Choose *Bruce Clay SEO* > *Settings* from the WordPress menu.
2. In the SEOToolSet Authentication pane, click *I do not have an account*.
3. Enter your email address, username (can be your email), and a password of your choice (twice); then click *Next*.
4. Select your preferred billing plan and click *Next*.
          Note: You will not be charged until the end of the trial period. You may cancel anytime.
5. Enter your billing details and click *Next* to complete your signup.
6. To connect your account to the plugin, click *Log in to the SEOToolSet* and enter your newly created username and password.
7. Create the website project by entering:
          Project name – such as ExampleSite.com
          Homepage URL – such as https://www.examplesite.com
          Description – brief description of the project (optional)
8. Click *Save Changes*.

**Last, authorize the plugin to pull data securely from Google:**
*This is a one-time, secure setup to authorize a Google API. Your Google username and password will not be stored. This step allows the SEOToolSet to pull Google Analytics and Search Console data for your site and display it within WordPress.*

1. Within *Bruce Clay SEO* > *Settings*, locate the Google Analytics pane.
2. Log in to Google (if you are not already logged in).
3. Click the Edit icon (pencil) and select the appropriate *Current Account*.
4. Click *Save Changes*.

== Frequently Asked Questions ==

Q: Is the Bruce Clay SEO plugin compatible with Yoast SEO?
A: Yes. The plugin is fully compatible with Yoast. The Bruce Clay SEO plugin detects automatically when a Yoast plugin (free or premium) is active. If Yoast is installed and active, then the Bruce Clay SEO plugin will use the page titles, meta descriptions, meta robots and canonical directives set up in Yoast.

Q: Is the Bruce Clay SEO plugin compatible with other WordPress SEO plugins?
A: The plugin is fully compatible with Yoast SEO. It may not be compatible with other WordPress SEO plugins.

Q: Does it support Gutenberg?
A: Yes, the Bruce Clay SEO plugin supports both the classic WordPress editor and the Gutenberg interface.

Q: Is there a free version of Bruce Clay SEO WP?
A: There is no free version, but new subscribers get the first seven days free. When you download the plugin and set up your SEOToolSet account, your monthly fees will begin at the end of the free trial period.

Q: Can I cancel my subscription?
A: Yes, you may cancel your subscription at any time. If you cancel during your free trial period, you will not be charged. If you cancel after billing has begun, your subscription will terminate at the end of the current billing period, and no future subscription fees will be charged.

Q: Do plugin subscriptions include use of the SEOToolSet® software?
A: Yes, your subscription includes the SEOToolSet as well as the Bruce Clay SEO plugin for WordPress. If desired, plugin users may log in directly to SEOToolSet.com and use the tools and reports there. Note that an additional fee may be required to activate several advanced features of the SEOToolSet (such as Link Reports).

== Screenshots ==

1. A convenient widget on the WordPress dashboard shows an overall SEO score for the website, alert links if any, and the top 5 posts based on pageviews.
2. In the page editor, writers can assign multiple keywords per page. Bruce Clay SEO analyzes the top-ranking pages for each keyword to customize keyword usage recommendations per page. Green indicates when the usage targets are met. Keywords are analyzed and stored in the SEOToolSet®.
3. For published pages, you can see how the page is performing (based on analytics data from Google) right in the WordPress editor!

== Changelog ==

= 0.8.0 =
Release Date: September 9, 2020

Bugfixes:
* Fixes keyword usage issue in the post/page editor.
* Fixes issue when sending page via the API on publishing and updating.
* Fixes several PHP warnings.
* Fixes Dashboard Widgets issues.
* Fixes an unnecessary error that was happening on render.

Enhancements:
* Refactor more code to meet Coding Standards
* Ensure WordPress compatibility for the 5.5 release

= 0.7.1 =
Release Date: May 18, 2020

Bugfixes:
* Fixes functionality to save Meta Description data and have that data reflect in the page statistics.
* Fixes session arrays to enable saving of data between different screens.
* Fixes dashboard UI issues like sorting and items per page for affected dashboard views.

= 0.7.0 -
Release Date: February 6, 2020

Bugfixes:
* Fixes UI bugs related to form controls, buttons and colors. 
* Fixes a bug where Yoast data was not being read correctly.
* Fixes a bug where settings may not be saved correctly.
* Fixes a JS bug related to using Google Charts.

Enahancements:
* Refactored the file structure and source code for automated testing compatibility.
* Improved code documentation.

= 0.6.3 =
Release Date: September 5, 2019

Bugfixes:
* Fixes a bug where a separate Title or Meta Description may appear in the Wordpress output.

Enhancements:
* Improved Translations for Japanese.
* Code cleanup and documentation additions.

= 0.6.2 =
Release Date: July 17, 2019

Bugfixes:

* Fixes a bug in pagination on dashboard data screens.
* Fixes a bug where the plugin was incorrectly reporting 5xx errors from our API for AJAX requests which were cancelled by a user.
* Fixes a bug where a function that displays an error message was not being called properly. This resulted in a fatal PHP error.

= 0.6.1 =
Release Date: June 24, 2019

Enhancements:

* Ensured support for WordPress 5.2.2.
* Improved Translations for Japanese.

Bugfixes:

* Fixes a session cache bug which would prevent the plugin dashboard screens working in some environments.

= 0.6.0 =
Release Date: June 13, 2019

Enhancements:

* Support for custom post types.
* Better formatting for Mobile and Pagespeed Reporting.

Bugfixes:

* Fixes a bug when highlighting certain keywords would also display HTML.
* Fixes a bug where the post_id may be empty when analyzing content with the SEOToolSet.
* Fixes a bug in our signup process where our billing system would not receive the correct data.
* Fixes a bug where a non-published post would cause a PHP error during auto-save.

= 0.5.1 =
Release Date: May 29, 2019

Enhancements:

* Ensure WordPress 5.2 compatibility.
* Filter the sync so only published pages are synced.
* Improvements to AJAX loading and error handling.

Bugfixes:

* Fixes pagination issues in the Dashboard data tables.
* Fixes sorting issues in the Dashboard data tables.
* Fixes a bug where posts would not get published.
* Fixes a bug where the wrong author may be reported for a post.
* Fixes a bug where the displayed Alert count may be incorrect.

= 0.5.0 =
Release Date: April 23, 2019

Enhancements:

* Makes all data calls in parallel, making the UI much faster.
* Improves Yoast Integration.
* Adds multilingual support.
* Improves word goal detection.

Bugfixes:

* Fixes several UI and data type issues.
* Fixes a bug where Low Priority Alerts were showing even if there were none to show.

= 0.4.2 =
Release Date: March 26, 2019

Enhancements:

* Adds a content sync feature to sync your pages and posts with the SEOToolSet rather than publishing or updating one by one.
* Improves Yoast integration.
* Uses gravatar for Featured Author Image.

Bugfixes:

* Fixes a date snapping bug by adding a default of 30 days to the Dashboard dates.
* Fixes tabular data display for CTR and Average Views per Page in the Dashboard. 

= 0.4.1 =
Release Date: February 28, 2019

Enhancements: 

* Improves live updating of keyword goals and counts
* Improves mobile analysis display

Bugfixes:

* Fixes JavaScript and other Plugin collisions by namespacing jQuery calls.
* Fixes SEO Score data display by pulling data from SEOToolSet.
* Fixes compatibility with the Classic Editor.

== Upgrade Notice ==

= 0.8.0 =
Fixes bugs regarding sending information to the SEOToolSet, PHP Warnings, and keyword usage. Upgrade today if your pages were having trouble staying in sync with the SEOToolSet or you saw a lot of PHP Warnings when activating the plugin.

= 0.7.1 =
Fixes bugs related to table sorting and filtering. Also fixes Title and Meta Description analysis in the page editor. Upgrade now if Title or Meta Description information was incorrect.

= 0.7.0 =
Fixes bugs in saving Meta Description tags, signing up, and dashboard data. Upgrade now if you are unable to subscribe or are having trouble saving meta information for a post.

= 0.7.0 =
Fixes bugs in the UI, Yoast Compatibility, Settings and Chart components. Refactors code in order to do some more automated testing and documenting. Upgrade now if you have been seeing zeroes for data in Yoast fields, blank screens or charts or errors when saving settings.

= 0.6.3 =
Fixes a bug where separate meta titles or descriptions may appear in the Wordpress output. Upgrade now if you are getting weird output for titles or meta descriptions when the plugin is enabled.

= 0.6.2 =
Fixes critical bugs where incorrect PHP Errors and 500 errors were being reported. Upgrade now if you are getting these types of errors while trying to use the plugin.

= 0.6.1 =
Fixes a bug with session caching by eliminating the use of sessions altogehter. Upgrade now if you are getting PHP warnings or PHP errors when trying to use the plugin.

= 0.6.0 =
Adds support for custom post types. Also addresses issues with signing up, keyword highlighting and page analysis. Upgrade if you are having issues signing up or seeing the correct analysis data.

= 0.5.1 =
WordPress 5.2 compatibility and several fixes to the Dashboard data tables to improve the UI including sorting and pagination. Upgrade now if you are having issues viewing data or publishing posts.

= 0.5.0 =
Makes the UI much, much faster and adds multilingual support. Upgrade immediately if you are having issues with the plugin slowing down the WP admin.

= 0.4.2 =
This version adds the ability for all of your pages and posts to be analyzed at once. Upgrade immediately to get more accurate analysis.

= 0.4.1 =
v0.4.1 fixes several bugs while working inside the editor. Upgrade if you are having problems with the plugin not working in certain views.
