Upgrading this plugin
=====================

This is an internal documentation for plugin developers with some notes what has to be considered when updating this plugin to a new Moodle major version.

General
-------

* Generally, this is a quite simple plugin with just one purpose.
* It does not rely on any fluctuating library functions and should remain quite stable between Moodle major versions.
* Thus, the upgrading effort is low.


Upstream changes
----------------

* This plugin does not inherit any code from upstream sources.
* However, the maintenance announcement box which is built in amd/src/livecheck.js mimics the markup, CSS classes and strings of the maintenance announcement box which is rendered by Moodle core in core_renderer::maintenance_warning(). Thus, it should be checked if this core function has been changed in the new Moodle major version and, if yes, the changes should be adopted in amd/src/livecheck.js.


Automated tests
---------------

* The plugin has a good coverage with Behat tests which test all of the plugin's user stories.


Manual tests
------------

* There aren't any manual tests needed to upgrade this plugin.


Visual checks
-------------

* It might be advisable to have a quick look at the maintenance banner in the Moodle GUI as Moodle themes might influence the look of the banner.
