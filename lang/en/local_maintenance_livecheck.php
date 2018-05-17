<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Local plugin "Maintenance mode (live check)" - Language pack
 *
 * @package   local_maintenance_livecheck
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Maintenance mode (live check)';
$string['privacy:metadata'] = 'The maintenance mode (live check) plugin provides extended functionality to Moodle users, but does not store any personal data.';
$string['setting_advancedsettingsheading'] = 'Advanced settings';
$string['setting_backoff'] = 'Live check back off time';
$string['setting_backoff_desc'] = 'With this setting, you can set a back off time (in seconds) in which the users\' browsers will not perform any check yet. It can be used to reduce the load on the server side and is best described with an example: If you always schedule the maintenance mode with 10 minutes lead time and have set the live check interval to 60 seconds, you could set the live check back off time to 120 seconds. The users\' browsers will then perform the first live check 180 seconds after page load. Thus, the users will be notified 7 minutes before maintenance mode is started in the worst case which should be enough for most scenarios.';
$string['setting_checkinterval'] = 'Live check interval';
$string['setting_checkinterval_desc'] = 'With this setting, you can set the interval (in seconds) in which the users\' browsers will check if maintenance mode will be enabled soon or has been enabled in the meantime. Even if this check is very lightweight on the server side, you should be careful with really short intervals because short intervals, together with a large amount of concurrent active users, might generate perceivable additional load on your server. For normal Moodle setups, the default setting should be fine.';
$string['setting_enable'] = 'Enable live check';
$string['setting_enable_desc'] = 'With this setting, you can enable the maintenance announcement live check. As long as the live check is not enabled, the maintenance announcements are only shown on a full page load.';
$string['setting_generalfunctionalityheading'] = 'General functionality';
$string['setting_livecheckend'] = 'Live check end time';
$string['setting_livecheckend_desc'] = 'With these two settings, you can control the daytime (relating to server time) when the live check should be performed. If you set both settings to 0:00, the live check will be performed during the whole day which is also the default. However, if you are sure that you will always enable the maintenance mode only during a defined inspection window, you can limit the live check to this inspection window to save the load on the server side during the rest of the day.';
$string['setting_livecheckstart'] = 'Live check start time';
$string['setting_livecheckweekdays'] = 'Live check weekdays';
$string['setting_livecheckweekdays_desc'] = 'With this setting, you can control the weekdays when the live check should be performed. By default, all weekdays are enabled. However, if you are sure that you will never enable the maintenance mode on weekends or certain working days, you can disable these weekdays to save the load on the server side on these days.';
