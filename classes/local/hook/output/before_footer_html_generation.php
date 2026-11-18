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
 * Local plugin "Maintenance mode (live check)" - Hook: Allows plugins to add any elements to the page footer.
 *
 * @package   local_maintenance_livecheck
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_maintenance_livecheck\local\hook\output;

/**
 * Hook to allow plugins to add any elements to the page footer.
 *
 * @package   local_maintenance_livecheck
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_footer_html_generation {
    /**
     * Callback to insert the necessary JS code to each page.
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function callback(\core\hook\output\before_footer_html_generation $hook): void {
        global $CFG;

        // Fetch local_maintenance_livecheck config.
        $config = get_config('local_maintenance_livecheck');

        // Check if the plugin's functionality is enabled.
        // We have to check explicitely if the configurations are set because this function will already be
        // called at installation time and would then throw PHP notices otherwise.
        if (
            isset($config->enable) && $config->enable == true &&
                isset($config->checkinterval) && $config->checkinterval > 0
        ) {
            // Do only if maintenance mode has not been scheduled yet, because otherwise Moodle core will
            // output the announcement itself on page load and we then don't need to do any live check anymore.
            if (!(isset($CFG->maintenance_later) && $CFG->maintenance_later > time())) {
                // Do only if the live check is configured to be run on any weekday and if the live check times are configured.
                if (
                    isset($config->livecheckweekdays) && strpos($config->livecheckweekdays, "1") !== false &&
                        isset($config->livecheckstart) && isset($config->livecheckstartmin) &&
                        isset($config->livecheckend) && isset($config->livecheckendmin)
                ) {
                    // Get the time according to the server timezone.
                    // The timezone has to be passed explicitly as usergetdate() and make_timestamp() would use
                    // the timezone of the current user otherwise.
                    $servertz = \core_date::get_server_timezone();
                    $now = time();
                    $date = usergetdate($now, $servertz);

                    // Check if the current server day and the previous server day are configured live check days.
                    $livechecktoday = substr($config->livecheckweekdays, $date['wday'], 1) == 1;
                    $livecheckyesterday = substr($config->livecheckweekdays, ($date['wday'] + 6) % 7, 1) == 1;

                    // Get the live check start time and the live check end time on the current server day.
                    $livecheckstart = make_timestamp(
                        $date['year'],
                        $date['mon'],
                        $date['mday'],
                        $config->livecheckstart,
                        $config->livecheckstartmin,
                        0,
                        $servertz
                    );
                    $livecheckend = make_timestamp(
                        $date['year'],
                        $date['mon'],
                        $date['mday'],
                        $config->livecheckend,
                        $config->livecheckendmin,
                        0,
                        $servertz
                    );

                    // If live check start time == live check end time (which is the meaning for the whole day).
                    if ($livecheckstart == $livecheckend) {
                        // The live check is active if the current server day is a live check day.
                        $livecheckactive = $livechecktoday;

                        // Otherwise, if the live check start time is before the live check end time.
                    } else if ($livecheckstart < $livecheckend) {
                        // The live check is active if the current server day is a live check day and
                        // if the current server time is within the configured live check times.
                        $livecheckactive = $livechecktoday && $now >= $livecheckstart && $now <= $livecheckend;

                        // Otherwise, if the live check start time is after the live check end time
                        // (which means that the live check time slot spans midnight).
                    } else {
                        // The live check time slot belongs to the day on which it starts.
                        // Thus, the live check is active if the current server day is a live check day and the time slot
                        // has started today, or if the previous server day is a live check day and the time slot which
                        // started yesterday has not ended yet.
                        $livecheckactive = ($livechecktoday && $now >= $livecheckstart) ||
                                ($livecheckyesterday && $now <= $livecheckend);
                    }

                    // Do only if the live check is active now.
                    if ($livecheckactive) {
                        // If the back off time is not set yet (for any reason), fall back to the default (no back off time).
                        if (!isset($config->backoff)) {
                            $config->backoff = 0;
                        }

                        // Ship the strings which are needed for the maintenance announcement box with the page.
                        // This is needed because the JS code won't be able to fetch the strings from the server
                        // anymore as soon as CLI maintenance mode has started.
                        $hook->renderer->get_page()->requires->strings_for_js(
                            ['sitemaintenance', 'maintenancemodeisscheduled', 'maintenancemodeisscheduledlong'],
                            'admin'
                        );

                        // Insert the necessary JS code to the page.
                        $jsoptions = ['checkinterval' => $config->checkinterval,
                                'backoff' => $config->backoff, ];
                        $hook->renderer->get_page()->requires->js_call_amd(
                            'local_maintenance_livecheck/livecheck',
                            'init',
                            [$jsoptions]
                        );
                    }
                }
            }
        }
    }
}
