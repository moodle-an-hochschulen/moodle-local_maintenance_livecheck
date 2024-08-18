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
 * Local plugin "Maintenance mode (live check)" - Library
 *
 * @package   local_maintenance_livecheck
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Insert the necessary JS code to each page by abusing Moodle's *_extend_navigation() hook.
 *
 * @param global_navigation $navigation
 */
function local_maintenance_livecheck_extend_navigation(global_navigation $navigation) {
    global $CFG, $PAGE;

    // Fetch local_maintenance_livecheck config.
    $config = get_config('local_maintenance_livecheck');

    // Check if the plugin's functionality is enabled.
    // We have to check explicitely if the configurations are set because this function will already be
    // called at installation time and would then throw PHP notices otherwise.
    if (isset($config->enable) && $config->enable == true &&
            isset($config->checkinterval) && $config->checkinterval > 0) {

        // Do only if maintenance mode has not been scheduled yet, because otherwise Moodle core will
        // output the announcement itself on page load and we then don't need to do any live check anymore.
        if (!(isset($CFG->maintenance_later) && $CFG->maintenance_later > time())) {

            // Do only if the live check is configured to be run on any weekday and if the live check times are configured.
            if (isset($config->livecheckweekdays) && strpos($config->livecheckweekdays, "1") !== false &&
                    isset($config->livecheckstart) && isset($config->livecheckstartmin) &&
                    isset($config->livecheckend) && isset($config->livecheckendmin)) {

                // Get the time according to the server timezone.
                $now = time();
                $date = usergetdate($now);

                // Do only if the current server day is a configured live check day.
                $livechecktoday = substr($config->livecheckweekdays, $date['wday'], 1);
                if ($livechecktoday == 1) {

                    // Do only if live check start time == live check end time (which is the meaning for the whole day) or
                    // if the current server time is within the configured live check times.
                    $livecheckstart = make_timestamp($date['year'], $date['mon'], $date['mday'],
                            $config->livecheckstart, $config->livecheckstartmin);
                    $livecheckend = make_timestamp($date['year'], $date['mon'], $date['mday'],
                            $config->livecheckend, $config->livecheckendmin);
                    if (($livecheckstart == $livecheckend) || ($now >= $livecheckstart && $now <= $livecheckend)) {

                        // Insert the necessary JS code to the page.
                        $jsoptions = ['checkinterval' => $config->checkinterval,
                                'backoff' => $config->backoff, ];
                        $PAGE->requires->js_call_amd('local_maintenance_livecheck/livecheck', 'init', [$jsoptions]);
                    }
                }
            }
        }
    }
}
