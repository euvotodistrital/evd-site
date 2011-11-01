=== Simple Google Analytics ===
Author: JeromeMeyer62
Contributors: JeromeMeyer62
Tags: google analytics, wordpress statistics, tracking
Plugin link: http://www.arobase62.fr/2011/03/23/simple-google-analytics/
Requires at least: 2.6
Tested up to: 3.1
Version: 1.0.3
Stable tag: 1.0.3

== Description ==
Simple Google Analytics allows you to easilly add your Google Analytics code on all your pages.
Just add your ID, choose if you are on a sub-domain (setting in Google Analytics code), and enter the domain.
That's all, you're ready to go.

Simple Google Analytics will not track admin users logged-in.

This plugin is largely inspired by the Google Analytics Input plugin from Roy Duff ( http://wpable.com ).

If you have any question, you can find the plugin page here : http://www.arobase62.fr/2011/03/23/simple-google-analytics/

== Installation ==
This plugin follows the [standard WordPress installation method][]:

1. Upload the 'simple-google-analytics.zip' file to the `/wp-content/plugins/` directory using wget, curl of ftp.
2. 'unzip' the 'simple-google-analyticsz.zip' which will create the folder to the directory `/wp-content/plugins/simple_google_analytics`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin through 'Simple Google Analytics' submenu in the the 'Settings' section of the Wordpress admin menu
5. Add your google analytics ID there. An example of Google Analytics ID --> UA-0000000-0.
6. Choose if your blog is on a sub-domain or not. This option is defined in your Google Analytics settings page. Do not change if you don't know.
7. Enter the domain where your Wordpress is.
8. Save it & your done.

[standard WordPress installation method]: http://codex.wordpress.org/Managing_Plugins#Installing_Plugins

== Frequently Asked Questions ==
= Where can I find the google analytics ID? =
Login to google analytics, go to the website profile which you want, edit and go to check status and there you see your google analytics code. You just want to copy your google analytics ID which will look like this UA-0000000-0 (but will be your own unique id.)

= Does it track logged in users? =
No, you wouldn't want to track logged in users because this would give you invalid results, everything else is tracked.

= What versions does it work on? =
It should work from 2.6 upwards.. Has been tested on all current versions and is working.

== Screenshots ==
1. Screenshot Simple Google Analytics Admin Page

== Changelog ==
= version 1.0.3 =
* Clean Readme file and clean code
= version 1.0.2 =
* Added French Translation
= version 1.0 =
* Add multi-subdomain option