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
 * Local plugin "Maintenance mode (live check)" - Settings
 *
 * @package   local_maintenance_livecheck
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Create new settings page.
    $page = new admin_settingpage('local_maintenance_livecheck',
            get_string('pluginname', 'local_maintenance_livecheck', null, true));

    if ($ADMIN->fulltree) {
        // Add general functionality heading.
        $page->add(new admin_setting_heading('local_maintenance_livecheck/generalfunctionalityheading',
                get_string('setting_generalfunctionalityheading', 'local_maintenance_livecheck', null, true),
                ''));

        // Create enable control widget.
        $page->add(new admin_setting_configcheckbox('local_maintenance_livecheck/enable',
                get_string('setting_enable', 'local_maintenance_livecheck', null, true),
                get_string('setting_enable_desc', 'local_maintenance_livecheck', null, true),
                0));

        // Create check interval control widget.
        $choices = [10 => 10, 30 => 30, 60 => 60, 90 => 90, 120 => 120, 180 => 180, 240 => 240, 300 => 300];
        $page->add(new admin_setting_configselect('local_maintenance_livecheck/checkinterval',
                get_string('setting_checkinterval', 'local_maintenance_livecheck', null, true),
                get_string('setting_checkinterval_desc', 'local_maintenance_livecheck', null, true),
                60,
                $choices));
        unset($choices);

        // Add advanced settings heading.
        $page->add(new admin_setting_heading('local_maintenance_livecheck/advancedsettingsheading',
                get_string('setting_advancedsettingsheading', 'local_maintenance_livecheck', null, true),
                ''));

        // Create back off control widget.
        $choices = [0 => 0, 10 => 10, 30 => 30, 60 => 60, 90 => 90, 120 => 120, 180 => 180, 240 => 240, 300 => 300];
        $page->add(new admin_setting_configselect('local_maintenance_livecheck/backoff',
                get_string('setting_backoff', 'local_maintenance_livecheck', null, true),
                get_string('setting_backoff_desc', 'local_maintenance_livecheck', null, true),
                0,
                $choices));
        unset($choices);

        // Create live check time control widgets.
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        foreach ($days as $day) {
            $choices[$day] = get_string($day, 'calendar', null, false);
                    // Don't use string lazy loading here because the string will be directly used and
                    // would produce a PHP warning otherwise.
        }
        $page->add(new admin_setting_configmulticheckbox2('local_maintenance_livecheck/livecheckweekdays',
                get_string('setting_livecheckweekdays', 'local_maintenance_livecheck', null, true),
                get_string('setting_livecheckweekdays_desc', 'local_maintenance_livecheck', null, true),
                $choices,
                $choices));
        unset($choices);
        $page->add(new admin_setting_configtime('local_maintenance_livecheck/livecheckstart',
                'livecheckstartmin',
                get_string('setting_livecheckstart', 'local_maintenance_livecheck', null, true),
                '',
                ['h' => 0, 'm' => 0]));
        $page->add(new admin_setting_configtime('local_maintenance_livecheck/livecheckend',
                'livecheckendmin',
                get_string('setting_livecheckend', 'local_maintenance_livecheck', null, true),
                get_string('setting_livecheckend_desc', 'local_maintenance_livecheck', null, true),
                ['h' => 0, 'm' => 0]));
    }

    // Add settings page to navigation tree.
    $ADMIN->add('server', $page);
}
