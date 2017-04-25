moodle-local_maintenance_livecheck
=====================================

Moodle plugin which shows the Moodle maintenance announcement even if there is no full page load


Requirements
------------

This plugin requires Moodle 3.2+


Installation
------------

Install the plugin like any other plugin to folder
/local/maintenance_livecheck

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage
-----

After installing local_maintenance_livecheck, the plugin does not do anything to Moodle yet.
To configure the plugin and its behaviour, please visit Site administration -> Server -> Maintenance mode (live check).

There, you find two sections:

### 1. General functionality

With the "Enable functionality" setting, you can enable the maintenance mode live check. As long as the live check is not enabled, the maintenance announcements are only shown on a full page load.

And with the "Check interval" setting, you can set the interval (in seconds) in which the users' browsers will check if maintenance mode will be enabled soon or has been enabled in the meantime. Even if this check is very lightweight on the server side, you should be careful with really short intervals because short intervals, together with a large amount of concurrent active users, might generate perceivable additional load on your server. For normal Moodle setups, the default setting should be fine.

### 2. Advanced settings

With the "Back off time" setting, you can set a back off time (in seconds) in which the users' browsers will not perform any check yet. It can be used to reduce the load on the server side and is best described with an example: If you always schedule the maintenance mode with 10 minutes lead time and have set the live check interval to 60 seconds, you could set the live check back off time to 120 seconds. The users' browsers will then perform the first live check 180 seconds after page load. Thus, the users will be notified 7 minutes before maintenance mode is started in the worst case which should be enough for most scenarios.';

And with the "Live check weekdays" and "Live check start/end time" settings, you can control the weekdays and the daytime when the live check should be performed. By default, the live check will be performed 24/7. However, if you are sure that you will never enable the maintenance mode on certain weekdays or daytimes, you can limit the live check weekdays and daytimes to save the load on the server side.


Themes
------

local_maintenance_livecheck should work with all Bootstrap based Moodle themes.


Motivation for this plugin
--------------------------

Moodle has a built-in maintenance mode feature which is described on https://docs.moodle.org/en/Maintenance_mode. With the maintenance mode, an admin is able to suspend the usage of a Moodle installation to perform updates and other maintenance tasks. The maintenance mode can be either basically controlled in the GUI on /admin/settings.php?section=maintenancemode (on / off only) or can be fully controlled via CLI on /admin/cli/maintenance.php (on / off / scheduled).

As soon as the maintenance mode is scheduled and as long as the maintenance mode is active, a user gets a warning message about the maintenance period. Unfortunately, this warning message is only output on a full page reload. If a user is working in Moodle without reloading the page, for example because he is typing a long forum post, he might not notice that there is a maintenance period scheduled or being activated. Following the forum post example, if the user sends the forum post form after he has finished typing and after maintenance mode has been activated, Moodle does not process the forum post anymore and the post is probably lost.

To prevent problems like these, this plugin adds a live check for a scheduled or activated maintenance mode and will show a maintenance announcement even if there is no full page load.

This is achieved by abusing the *_extend_navigation() hook which allows plugin developers to extend Moodle's global navigation tree at runtime, but can also be abused to insert HTML code to each Moodle page. Please note that due to the way this plugin is built, the maintenance announcement is shown on as much Moodle pages as possible, but not on really all Moodle pages - for example, a SCORM activity instance opened in a new window will never show the maintenance announcement.


Further information
-------------------

local_maintenance_livecheck is found in the Moodle Plugins repository: http://moodle.org/plugins/view/local_maintenance_livecheck

Report a bug or suggest an improvement: https://github.com/moodleuulm/moodle-local_maintenance_livecheck/issues


Moodle release support
----------------------

Due to limited resources, local_maintenance_livecheck is only maintained for the most recent major release of Moodle. However, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that local_maintenance_livecheck still works with a new major relase - please let us know on https://github.com/moodleuulm/moodle-local_maintenance_livecheck/issues


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on
github with modifications.


PHP7 Support
------------

Since Moodle 3.0, Moodle core basically supports PHP7.
Please note that PHP7 support is on our roadmap for this plugin, but it has not yet been thoroughly tested for PHP7 support and we are still running it in production on PHP5.
If you encounter any success or failure with this plugin and PHP7, please let us know.


Copyright
---------

Ulm University
kiz - Media Department
Team Web & Teaching Support
Alexander Bias


Credits
-------

This plugin is a rebuilt and enhanced version of local_maintenance_announcement which was built by Soon Systems GmbH.
