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
 * Local plugin "Maintenance mode (live check)" - AJAX processing
 *
 * @package   local_maintenance_livecheck
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

// @codingStandardsIgnoreStart
// Let codechecker ignore the next line because otherwise it would complain about a missing login check
// after requiring config.php which is really not needed.
require(__DIR__ . '/../../config.php');
// @codingStandardsIgnoreEnd

global $CFG;

// Prepare result.
$result = new stdClass();
$result->timeleftinsec = null;

// Check if CLI maintenance mode is scheduled.
if (isset($CFG->maintenance_later) and $CFG->maintenance_later > time()) {
    $timeleftinsec = $CFG->maintenance_later - time();
    $result->timeleftinsec = $timeleftinsec;

    // Otherwise check if legacy maintenance mode is active.
} else if (isset($CFG->maintenance_enabled) and $CFG->maintenance_enabled == true) {
    $result->timeleftinsec = 0;
}

// Return result.
// List of possible return values:
// No maintenance mode active or scheduled: HTTP Status 200, return value null.
// CLI maintenance mode scheduled:          HTTP Status 200, return value int > 0.
// CLI maintenance mode active:             HTTP Status 503.
// Legacy maintenance mode active:          HTTP Status 200, return value 0.
echo json_encode($result);

// Don't do anything else.
die();
