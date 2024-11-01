=== Plugin Name ===
Contributors: Ryan Huff, The CodeTree
Donate link: http://mycodetree.com/donations/
Tags: mysql, database, backup, cron, codetree, restore, database, backup database, codetree backup
Requires at least: 2.9
Tested up to: 3.2.1
Stable tag: 3.6.2

A Wordpress backup utility that can be used by itself or with an active subscription from http://mycodetree.com.

== Description ==

The CodeTree Backup plugin is a simple utility that will backup your themes, plugins and uploads folder as well as a complete copy of the database. The plugin works
in a manual 'standalone' mode and stores the backup on your server or works with an active subscription from <a href='http://mycodetree.com'>http://mycodetree.com</a>.

Restoring the backup is simple; reinstall the Wordpress blog engine and then restore your themes, plugins and uploads folders from the backup and then import the '.sql' database
file into your database (using PhpMyAdmin or some other similar tool) and your done! You'll have to reactivate all your plugins however.

If you have an active subscription from <a href='http://mycodetree.com'>http://mycodetree.com</a> you may not be responsible for restoring your backups as it may be
included with your subscriptions.


== Installation ==

1. Upload the plugin archive file into Wordpress using the 'Add New' option under the plugin menu in the Administration area.
1. Alternatively, you can unzip the plugin archive and FTP the contents to the wp-content/plugin/ folder of Wordpress
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Navigate to 'CodeTree Backup' under the settings menu. If using the plugin with an active subscription then place your 
API key in the 'API Key' box.

== Frequently Asked Questions ==

= Please supply a valid API key found in the subscription area of your account .. =

This indicates that you have not supplied an API key to the plugin. You do not need an API key or
an active CodeTree subscription in order to use the Manual Backup feature of the plugin.

= This is not a valid API key =

This indicates that the API key supplied to the plugin is not a valid API key issued by The CodeTree.
Login into your account at http://mycodetree.com and verify your API key.  Please email support@mycodetree.com
if you have an difficulties.

= The API key is not valid for the domain =

This indicates that the API key supplied to the plugin may be valid but is not issued for the domain that
it is being used with. Login into your account at http://mycodetree.com and verify your API key.  
Please email support@mycodetree.com if you have an difficulties.

= The backup file seems to be empty or missing =

Please select the corresponding option to the current *path* setting in the plugin settings menu.
If *Absolute Paths* is selected, switch to *Relative Paths* and Vice Versa. *Most Windows(R)/IIS web servers
will need to use Relative Paths*.

= Parse error: syntax error, unexpected ')', expecting '(' in .../wp-content/plugins/codetree-backup.php on line 34X =

This indicates that the web server is using an unsupported version of PHP. The CodeTree Backup requires PHP version 5.0 or higher.
Please upgrade the version of PHP that your web server is using or talk to your web host to see if they can upgrade for you.
If you are considering moving your web site to a different web host, MyCodeTree recommends
<a href='http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=rthcon' target='_blank'>http://hostgator.com</a> as the official web host of
choice for all CodeTree plugins.

== Screenshots ==

1. Example of the plugin's settings menu.
2. After you click the "Manual Backup" button you'll be given a download link for the backup and an option to remove the backup. You will not be able
to make another manual backup until you remove the existing one.
3. You'll see a green 'success' message if you choose to supply a valid API key from your CodeTree
subscription account and the API key is correctly submitted.
4. If you supply an API key that is not valid, you'll see a message indicating the problem.


== Changelog ==

= 1.3.1 =
* Launch Version
= 1.3.2 =
* Fixed DROP TABLE typo in SQL compiler
= 1.3.3 =
* Fixed FAQ section for Wordpress.org
= 1.4 =
* Cumulative Update
= 1.5 =
* Fixed path statement
= 1.5.1 =
* First cumlative update release
= 2.0 =
* Added support for custom list of backup folders in addition to the themes, plugins and media folders already backed up. *Must have CodeTree Subscription and valid API Key*
* Added description to help locate the database dump file in the backup archive file when made manually.
= 2.1 =
* Added a helper link to make it easier to find out how to get an API key
= 2.5 =
* Added Donation link
* Fixed several typographical errors
* Increased usability of UI
= 2.5.1 =
* Added link to easily find all CodeTree plugins in the Wordpress directory
= 3.0 = 
* Fixed issue reported by Alain Hogue - plugin path statements incorrect when running on MS Windows platforms
= 3.5 =
* Added a PHP version check. Plugin does not work below PHP version 5.
* Added GUI option to use Absolute paths OR relative paths when making backups for Windows(R) users and unique server setups.
= 3.5.1 = 
* Changed the default setting from *Absolute Paths* to *Relative Paths*
= 3.6 =
* Fixed logic error with path condition
= 3.6.1 =
* Fixed a typo, most WIN/IIS users need to use Relative paths NOT absolute paths
* Added an alternate trigger method for server that do not use url rewriting
* Added check for outbound port 80 availability and for the allow_url_fopen flag to be on
* Turned the manual backup into a button instead of a check box
= 3.6.2 =
* Correct path variable

== Upgrade Notice ==

= 1.5.1 =
* The 1.5 version is the first cumlative update since release.  All previous patch are included in version 1.5
= 2.0 =
* Should upgrade to version 2.0 if you have an active CodeTree subscription.
= 2.1 =
* Current stable release
= 2.5 =
* Milestone Release; several fixes and enhancements
= 2.5.1 =
* Fixed some typos and added a usability link
= 3.0 =
* Urgent upgrade if running on MS Windows web server - Fixed path statements
= 3.5 =
* Added GUI option for path usage and added PHP version check, Upgrade recommended. Make sure you have the correct path setting set once you have upgraded!
= 3.5.1 = 
* Changed the default setting from *Absolute Paths* to *Relative Paths*
= 3.6 =
* Fixed logic error with path condition
= 3.6.1 =
* Fixed typo, added 3 nex features
= 3.6.2 =
* Fixed path variable, please update.