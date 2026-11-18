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

namespace local_maintenance_livecheck\local\hook\output;

/**
 * Local plugin "Maintenance mode (live check)" - Tests for the before_footer_html_generation hook callback.
 *
 * @package   local_maintenance_livecheck
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_maintenance_livecheck\local\hook\output\before_footer_html_generation
 */
final class before_footer_html_generation_test extends \advanced_testcase {
    /**
     * Test that the live check weekdays and the live check time slot are evaluated correctly, i.e.
     * in the server timezone and not in the timezone of the current user, and
     * with a time slot which spans midnight belonging to the weekday on which it starts.
     *
     * @dataProvider livecheck_times_provider
     * @param bool $differentweekdays True if the server and the user should be on different weekdays (25 hours apart),
     *                                false if it should be noon in server time and midnight in user time.
     * @param string $weekdays The live check weekdays relative to the current server weekday:
     *                         'all', 'todayonly', 'allbuttoday' or 'yesterdayonly'.
     * @param int $starthour The hour of the live check start time.
     * @param int $endhour The hour of the live check end time.
     * @param bool $expected True if the live check is expected to be active.
     */
    public function test_livecheck_times(
        bool $differentweekdays,
        string $weekdays,
        int $starthour,
        int $endhour,
        bool $expected
    ): void {
        global $PAGE;

        $this->resetAfterTest();

        // Pick the server timezone and the user timezone, both as offset to UTC in hours.
        // As the callback uses the real current time, the timezones are picked relative to the current time.
        // This way, the test is deterministic regardless of the time when it is run.
        if ($differentweekdays) {
            // These two timezones (UTC-12 and UTC+13) are 25 hours apart,
            // so the server weekday and the user weekday always differ.
            $serveroffset = -12;
            $useroffset = 13;
        } else {
            // It is currently between 12:00 and 12:59 in server time and between 0:00 and 0:59 in user time.
            $serveroffset = 12 - (int) gmdate('G');
            $useroffset = ($serveroffset > 0) ? $serveroffset - 12 : $serveroffset + 12;
        }

        // Set the server timezone.
        // Please note that the sign of the Etc/GMT timezones is inverted, i.e. Etc/GMT-2 is UTC+2.
        if ($serveroffset > 0) {
            $servertimezone = 'Etc/GMT-' . $serveroffset;
        } else if ($serveroffset < 0) {
            $servertimezone = 'Etc/GMT+' . abs($serveroffset);
        } else {
            $servertimezone = 'Etc/GMT';
        }
        $this->setTimezone($servertimezone, $servertimezone);

        // Log in as a user with the user timezone.
        // The timezone is set as numeric offset as the Etc/GMT timezone names are not accepted as user timezone.
        $user = $this->getDataGenerator()->create_user(['timezone' => (string) $useroffset]);
        $this->setUser($user);

        // Build the live check weekdays as string of seven 0/1 flags, starting with sunday.
        // The current server weekday is only known at runtime, so the flags can't be part of the data provider.
        $today = (int) (new \DateTime('now', \core_date::get_server_timezone_object()))->format('w');
        $yesterday = ($today + 6) % 7;
        $weekdays = match ($weekdays) {
            'all' => '1111111',
            'todayonly' => substr_replace('0000000', '1', $today, 1),
            'allbuttoday' => substr_replace('1111111', '0', $today, 1),
            'yesterdayonly' => substr_replace('0000000', '1', $yesterday, 1),
        };

        // Enable the plugin with the given live check weekdays and live check time slot.
        set_config('enable', 1, 'local_maintenance_livecheck');
        set_config('checkinterval', 10, 'local_maintenance_livecheck');
        set_config('livecheckweekdays', $weekdays, 'local_maintenance_livecheck');
        set_config('livecheckstart', $starthour, 'local_maintenance_livecheck');
        set_config('livecheckstartmin', 0, 'local_maintenance_livecheck');
        set_config('livecheckend', $endhour, 'local_maintenance_livecheck');
        set_config('livecheckendmin', 0, 'local_maintenance_livecheck');

        // Run the hook callback.
        $PAGE->set_url('/');
        $PAGE->set_context(\context_system::instance());
        $hook = new \core\hook\output\before_footer_html_generation($PAGE->get_renderer('core'));
        before_footer_html_generation::callback($hook);

        // Check if the live check JS code was added to the page.
        $livecheckadded = str_contains($PAGE->requires->get_end_code(), 'local_maintenance_livecheck/livecheck');
        $this->assertSame($expected, $livecheckadded);
    }

    /**
     * Data provider for test_livecheck_times().
     *
     * @return array
     */
    public static function livecheck_times_provider(): array {
        return [
            'Time slot matches server time only' => [
                'differentweekdays' => false,
                'weekdays' => 'all',
                'starthour' => 10,
                'endhour' => 15,
                'expected' => true,
            ],
            'Time slot matches user time only' => [
                'differentweekdays' => false,
                'weekdays' => 'all',
                'starthour' => 0,
                'endhour' => 2,
                'expected' => false,
            ],
            'Weekday matches server time only' => [
                'differentweekdays' => true,
                'weekdays' => 'todayonly',
                'starthour' => 0,
                'endhour' => 0,
                'expected' => true,
            ],
            'Weekday matches user time only' => [
                'differentweekdays' => true,
                'weekdays' => 'allbuttoday',
                'starthour' => 0,
                'endhour' => 0,
                'expected' => false,
            ],
            'Time slot spanning midnight, server time outside of it' => [
                'differentweekdays' => false,
                'weekdays' => 'all',
                'starthour' => 14,
                'endhour' => 11,
                'expected' => false,
            ],
            'Time slot spanning midnight, started today, today enabled' => [
                'differentweekdays' => false,
                'weekdays' => 'todayonly',
                'starthour' => 11,
                'endhour' => 9,
                'expected' => true,
            ],
            'Time slot spanning midnight, started today, yesterday enabled' => [
                'differentweekdays' => false,
                'weekdays' => 'yesterdayonly',
                'starthour' => 11,
                'endhour' => 9,
                'expected' => false,
            ],
            'Time slot spanning midnight, started yesterday, yesterday enabled' => [
                'differentweekdays' => false,
                'weekdays' => 'yesterdayonly',
                'starthour' => 16,
                'endhour' => 14,
                'expected' => true,
            ],
            'Time slot spanning midnight, started yesterday, today enabled' => [
                'differentweekdays' => false,
                'weekdays' => 'todayonly',
                'starthour' => 16,
                'endhour' => 14,
                'expected' => false,
            ],
        ];
    }
}
