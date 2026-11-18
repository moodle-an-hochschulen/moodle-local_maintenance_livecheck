moodle-local_maintenance_livecheck
==================================

[![Moodle Plugin CI](https://github.com/moodle-an-hochschulen/moodle-local_maintenance_livecheck/actions/workflows/moodle-plugin-ci.yml/badge.svg?branch=MOODLE_502_STABLE)](https://github.com/moodle-an-hochschulen/moodle-local_maintenance_livecheck/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3AMOODLE_502_STABLE)

Moodle plugin which shows the Moodle maintenance announcement even if there is no full page load


Requirements
------------

This plugin requires Moodle 5.2+


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

And with the "Live check weekdays" and "Live check start/end time" settings, you can control the weekdays and the daytime when the live check should be performed. By default, the live check will be performed 24/7. However, if you are sure that you will never enable the maintenance mode on certain weekdays or daytimes, you can limit the live check weekdays and daytimes to save the load on the server side. If the end time is earlier than the start time, the time slot spans midnight and belongs to the weekday on which it starts.


Capabilities
------------

This plugin does not add any additional capabilities.


Scheduled Tasks
---------------

This plugin does not add any additional scheduled tasks.


How this plugin works
---------------------

The functionality of this plugin is achieved by using Moodle core's before_footer_html_generation output hook which allows plugin developers to add JavaScript code to each Moodle page which is rendered with Moodle's standard page footer. Please note that due to the way this plugin is built, the maintenance announcement is shown on all Moodle pages which are rendered with Moodle's standard page footer (including pages with a minimal layout like a SCORM activity instance opened in a new window or the login page), but not on pages which are rendered without it.

If a Moodle page is embedded into another Moodle page within an iframe (for example H5P content within an activity page), the maintenance announcement is only shown on the outer page.


HTTP protocol versions
----------------------

A scheduled maintenance mode and the legacy maintenance mode (which is enabled in the GUI or with `--enableold`) are reported to the browser by this plugin itself. This works regardless of the HTTP protocol version.

However, as soon as the CLI maintenance mode is active, Moodle core answers all requests, including the live check requests of this plugin, with a HTTP 503 response. The live check recognizes this response as active CLI maintenance mode by one of these two indicators which are sent by Moodle core:

* The HTTP reason phrase `Moodle under maintenance`. This indicator only exists in HTTP/1.1 as HTTP/2 and HTTP/3 do not transmit reason phrases anymore.
* The HTTP header `Retry-After: 300`. This indicator exists in all HTTP protocol versions. The value of 300 seconds is hardcoded in the Moodle core code and does not depend on any Moodle setting.

The following table shows if the maintenance announcement works for the particular ways to enable the CLI maintenance mode with admin/cli/maintenance.php, depending on the HTTP protocol version which is used between the browser and the web server or reverse proxy:

| Setup | `--enable` | `--enablelater` | `--enablelater`, followed by `--enable` before the countdown has ended |
|-------|------------|-----------------|------------------------------------------------------------------------|
| HTTP/1.1 | Works | Works | Works |
| HTTP/2 | Works | Works | Works |
| HTTP/3 | Works | Works | Works |
| HTTP/1.1, with a reverse proxy which removes or overwrites the `Retry-After` header | Works | Works | Works |
| HTTP/1.1, with a reverse proxy which replaces the reason phrase | Works | Works | Works |
| HTTP/1.1, with a reverse proxy which removes or overwrites the `Retry-After` header and replaces the reason phrase | Does not work | Works | Works with delay |
| HTTP/2 or HTTP/3, with a reverse proxy which removes or overwrites the `Retry-After` header | Does not work | Works | Works with delay |

The table cells have these meanings:

* Works: The maintenance announcement is shown and switches to the maintenance message as soon as the CLI maintenance mode is active. With `--enablelater`, the countdown is run by the browser and switches to the maintenance message on its own when it ends, without depending on one of the indicators.
* Works with delay: The maintenance announcement keeps counting down to the originally scheduled time although the site is already in maintenance mode, and switches to the maintenance message only then.
* Does not work: The maintenance announcement is not shown until the next full page load.

Please note that the live check will also show the maintenance message if a component in front of Moodle answers a live check request with a HTTP 503 response and a `Retry-After: 300` header on its own, for example during an outage of the Moodle backend. The message will be removed again as soon as a live check request is answered by Moodle normally.


Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/local_maintenance_livecheck

The latest development version can be found on Github:
https://github.com/moodle-an-hochschulen/moodle-local_maintenance_livecheck


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/moodle-an-hochschulen/moodle-local_maintenance_livecheck/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/moodle-an-hochschulen/moodle-local_maintenance_livecheck/issues

Please create pull requests on Github:
https://github.com/moodle-an-hochschulen/moodle-local_maintenance_livecheck/pulls

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


Maintainers
-----------

The plugin is maintained by\
Moodle an Hochschulen e.V.


Copyright
---------

The copyright of this plugin is held by\
Moodle an Hochschulen e.V.


Initial copyright
-----------------

This plugin was initially built, maintained and published by\
Ulm University\
Communication and Information Centre (kiz)\
Alexander Bias

It was contributed to the Moodle an Hochschulen e.V. plugin catalogue in 2022.


Credits
-------

This plugin is a rebuilt and enhanced version of local_maintenance_announcement which was built by Soon Systems GmbH.
