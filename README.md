moodle-local_maintenance_livecheck
=====================================

[![Build Status](https://travis-ci.com/moodleuulm/moodle-local_maintenance_livecheck.svg?branch=master)](https://travis-ci.com/moodleuulm/moodle-local_maintenance_livecheck)

Moodle plugin which shows the Moodle maintenance announcement even if there is no full page load


Requirements
------------

This plugin requires Moodle 3.10+


Motivation for this plugin
--------------------------

Moodle has a built-in maintenance mode feature which is described on https://docs.moodle.org/en/Maintenance_mode. With the maintenance mode, an admin is able to suspend the usage of a Moodle installation to perform updates and other maintenance tasks. The maintenance mode can be either basically controlled in the GUI on /admin/settings.php?section=maintenancemode (on / off only) or can be fully controlled via CLI on /admin/cli/maintenance.php (on / off / scheduled).

As soon as the maintenance mode is scheduled and as long as the maintenance mode is active, a user gets a warning message about the maintenance period. Unfortunately, this warning message is only output on a full page reload. If a user is working in Moodle without reloading the page, for example because he is typing a long forum post, he might not notice that there is a maintenance period scheduled or being activated. Following the forum post example, if the user sends the forum post form after he has finished typing and after maintenance mode has been activated, Moodle does not process the forum post anymore and the post is probably lost.

To prevent problems like these, this plugin adds a live check for a scheduled or activated maintenance mode and will show a maintenance announcement even if there is no full page load.


Installation
------------

Install the plugin like any other plugin to folder
/local/maintenance_livecheck

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it does not do anything to Moodle yet.

To configure the plugin and its behaviour, please visit:
Site administration -> Server -> Maintenance mode (live check).

There, you find two sections:

### 1. General functionality

With the "Enable functionality" setting, you can enable the maintenance mode live check. As long as the live check is not enabled, the maintenance announcements are only shown on a full page load.

And with the "Check interval" setting, you can set the interval (in seconds) in which the users' browsers will check if maintenance mode will be enabled soon or has been enabled in the meantime. Even if this check is very lightweight on the server side, you should be careful with really short intervals because short intervals, together with a large amount of concurrent active users, might generate perceivable additional load on your server. For normal Moodle setups, the default setting should be fine.

### 2. Advanced settings

With the "Back off time" setting, you can set a back off time (in seconds) in which the users' browsers will not perform any check yet. It can be used to reduce the load on the server side and is best described with an example: If you always schedule the maintenance mode with 10 minutes lead time and have set the live check interval to 60 seconds, you could set the live check back off time to 120 seconds. The users' browsers will then perform the first live check 180 seconds after page load. Thus, the users will be notified 7 minutes before maintenance mode is started in the worst case which should be enough for most scenarios.';

And with the "Live check weekdays" and "Live check start/end time" settings, you can control the weekdays and the daytime when the live check should be performed. By default, the live check will be performed 24/7. However, if you are sure that you will never enable the maintenance mode on certain weekdays or daytimes, you can limit the live check weekdays and daytimes to save the load on the server side.


How this plugin works
---------------------

The functionality of this plugin is achieved by abusing the *_extend_navigation() hook which allows plugin developers to extend Moodle's global navigation tree at runtime, but can also be abused to insert HTML code to each Moodle page. Please note that due to the way this plugin is built, the maintenance announcement is shown on as much Moodle pages as possible, but not on really all Moodle pages - for example, a SCORM activity instance opened in a new window will never show the maintenance announcement.


Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/local_maintenance_livecheck

The latest development version can be found on Github:
https://github.com/moodleuulm/moodle-local_maintenance_livecheck


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/moodleuulm/moodle-local_maintenance_livecheck/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/moodleuulm/moodle-local_maintenance_livecheck/issues

Please create pull requests on Github:
https://github.com/moodleuulm/moodle-local_maintenance_livecheck/pulls

We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature _proposals_ and not as feature _requests_.


Moodle release support
----------------------

Due to limited resources, this plugin is only maintained for the most recent major release of Moodle as well as the most recent LTS release of Moodle. Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.

Apart from these maintained releases, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major release - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is an undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This Moodle plugin is shipped with an english language pack only. All translations into other languages must be managed through AMOS (https://lang.moodle.org) by what they will become part of Moodle's official language pack.

As the plugin creator, we manage the translation into german for our own local needs on AMOS. Please contribute your translation into all other languages in AMOS where they will be reviewed by the official language pack maintainers for Moodle.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


PHP7 Support
------------

Since Moodle 3.4 core, PHP7 is mandatory. We are developing and testing this plugin for PHP7 only.


Copyright
---------

Ulm University
Communication and Information Centre (kiz)
Alexander Bias


Credits
-------

This plugin is a rebuilt and enhanced version of local_maintenance_announcement which was built by Soon Systems GmbH.
