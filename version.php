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
 * Local plugin "Maintenance mode (live check)" - Version file
 *
 * @package   local_maintenance_livecheck
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_maintenance_livecheck';
$plugin->version = 2025041400;
$plugin->release = 'v5.0-r1';
$plugin->requires = 2025041400;
$plugin->supported = [500, 500];
$plugin->maturity = MATURITY_STABLE;
